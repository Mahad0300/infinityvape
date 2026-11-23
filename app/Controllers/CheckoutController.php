<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Cart;
use App\Models\Order;
use PDO;

class CheckoutController extends Controller
{
    /**
     * Helper to read JSON or POST input
     */
    protected function getPayload(): array
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        if (is_array($data)) {
            return $data;
        }

        return $_POST ?? [];
    }

    /**
     * Helper to load settings from DB
     */
    protected function getSettings(): array
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT `key`, `value` FROM settings");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $settings = [];
        foreach ($rows as $r) {
            $settings[$r['key']] = $r['value'];
        }

        return $settings;
    }

    /**
     * Load a coupon from the database and reject it when it is inactive,
     * outside its dates, under the minimum, or out of uses.
     */
    protected function findValidCoupon($codeOrId, float $subtotal, bool $byId = false): array
    {
        $db = Database::getInstance()->getConnection();
        if ($byId) {
            $stmt = $db->prepare("SELECT * FROM coupons WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => (int)$codeOrId]);
        } else {
            $stmt = $db->prepare("SELECT * FROM coupons WHERE UPPER(code) = :code LIMIT 1");
            $stmt->execute([':code' => strtoupper(trim((string)$codeOrId))]);
        }

        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$coupon || (int)($coupon['is_active'] ?? 0) !== 1) {
            return ['ok' => false, 'message' => 'Invalid or expired coupon code.'];
        }

        $now = time();
        $start = $this->couponTimestamp($coupon['start_date'] ?? null);
        if ($start !== null && $start > $now) {
            return ['ok' => false, 'message' => 'This coupon is not active yet.'];
        }
        $end = $this->couponTimestamp($coupon['end_date'] ?? null);
        if ($end !== null && $end < $now) {
            return ['ok' => false, 'message' => 'This coupon has expired.'];
        }

        $minOrder = (float)($coupon['min_order_amount'] ?? 0);
        if ($minOrder > 0 && $subtotal < $minOrder) {
            return [
                'ok' => false,
                'message' => 'This code requires a minimum subtotal of $' . number_format($minOrder, 2) . '.'
            ];
        }

        if (!empty($coupon['max_uses']) && (int)$coupon['uses_count'] >= (int)$coupon['max_uses']) {
            return ['ok' => false, 'message' => 'This coupon has reached its maximum usage limit.'];
        }

        $discount = 0.00;
        if ($coupon['type'] === 'percentage') {
            $discount = round(($subtotal * ((float)$coupon['value'] / 100)), 2);
        } elseif ($coupon['type'] === 'fixed_amount') {
            $discount = min($subtotal, (float)$coupon['value']);
        } elseif ($coupon['type'] === 'free_shipping') {
            $discount = (float)($coupon['value'] ?: 5.99);
        }

        return [
            'ok' => true,
            'coupon' => $coupon,
            'discount' => $discount,
            'min_order_amount' => $minOrder,
        ];
    }

    protected function couponTimestamp($value): ?int
    {
        if ($value === null || $value === '' || $value === '0000-00-00 00:00:00') {
            return null;
        }
        $ts = strtotime((string)$value);
        return $ts ? $ts : null;
    }

    protected function rememberCoupon(array $coupon, float $discount, float $minOrder): array
    {
        $saved = [
            'id'               => $coupon['id'],
            'code'             => $coupon['code'],
            'type'             => $coupon['type'],
            'value'            => (float)$coupon['value'],
            'discount_amount'  => $discount,
            'min_order_amount' => $minOrder,
        ];
        $_SESSION['checkout_coupon'] = $saved;
        return $saved;
    }

    /**
     * GET /checkout
     * Main checkout page rendering with live cart and settings
     */
    public function index()
    {
        $cart = Cart::getCart();
        $totals = Cart::totals();

        if (class_exists('\\App\\Models\\Analytics')) {
            \App\Models\Analytics::recordEvent('/checkout', 'checkout_start', 'Reached Checkout Page', 1);
        }

        $settings = $this->getSettings();

        $taxRate = isset($settings['tax_rate_percent']) ? (float)$settings['tax_rate_percent'] : 4.50;
        $standardShipping = isset($settings['standard_shipping_rate']) ? (float)$settings['standard_shipping_rate'] : 5.99;
        $expressShipping = isset($settings['express_shipping_rate']) ? (float)$settings['express_shipping_rate'] : 14.99;
        $freeShippingThreshold = isset($settings['free_shipping_threshold']) ? (float)$settings['free_shipping_threshold'] : 150.00;
        $testCheckoutEnabled = !empty($settings['test_checkout_enabled']) && $settings['test_checkout_enabled'] == '1';

        // Check if free shipping qualifies
        $initialShipping = ($totals['subtotal'] >= $freeShippingThreshold) ? 0.00 : $standardShipping;

        // Re-check the saved coupon against the database before showing the total
        $coupon = null;
        $discountAmount = 0.00;
        $savedCoupon = $_SESSION['checkout_coupon'] ?? null;
        if ($savedCoupon && !empty($savedCoupon['id'])) {
            $checked = $this->findValidCoupon($savedCoupon['id'], (float)$totals['subtotal'], true);
            if (!empty($checked['ok'])) {
                $coupon = $this->rememberCoupon($checked['coupon'], $checked['discount'], $checked['min_order_amount']);
                if ($coupon['type'] === 'percentage' || $coupon['type'] === 'fixed_amount') {
                    $discountAmount = $checked['discount'];
                } elseif ($coupon['type'] === 'free_shipping') {
                    $initialShipping = 0.00;
                }
            } else {
                unset($_SESSION['checkout_coupon']);
            }
        }

        $taxableAmount = max(0, $totals['subtotal'] - $discountAmount);
        $taxAmount = round(($taxableAmount * ($taxRate / 100)), 2);
        $grandTotal = round(($taxableAmount + $initialShipping + $taxAmount), 2);

        // Pre-fill logged-in customer data if available
        $currentUser = null;
        if (class_exists('\\App\\Core\\CustomerAuth') && \App\Core\CustomerAuth::check()) {
            $currentUser = \App\Core\CustomerAuth::user();
        }

        $this->render('front.checkout.index', [
            'page_title'             => 'Secure Checkout | Infinity Vape Shop',
            'page_js'                => 'checkout',
            'cart'                   => $cart,
            'totals'                 => $totals,
            'settings'               => $settings,
            'taxRate'                => $taxRate,
            'standardShipping'       => $standardShipping,
            'expressShipping'        => $expressShipping,
            'freeShippingThreshold'  => $freeShippingThreshold,
            'testCheckoutEnabled'    => $testCheckoutEnabled,
            'initialShipping'        => $initialShipping,
            'discountAmount'         => $discountAmount,
            'taxAmount'              => $taxAmount,
            'grandTotal'             => $grandTotal,
            'coupon'                 => $coupon,
            'currentUser'            => $currentUser
        ]);
    }

    /**
     * POST /checkout/apply-coupon
     * Validate and apply a promotional coupon code
     */
    public function applyCoupon()
    {
        $payload = $this->getPayload();
        $code = strtoupper(trim((string)($payload['code'] ?? '')));

        if ($code === '') {
            return $this->json(['success' => false, 'message' => 'Please enter a coupon code.'], 400);
        }

        $cart = Cart::getCart();
        $totals = Cart::totals();
        $subtotal = $totals['subtotal'];

        if ($subtotal <= 0) {
            return $this->json(['success' => false, 'message' => 'Your cart is empty.'], 400);
        }

        $checked = $this->findValidCoupon($code, $subtotal, false);
        if (empty($checked['ok'])) {
            return $this->json(['success' => false, 'message' => $checked['message']], 400);
        }

        $coupon = $checked['coupon'];
        $discount = $checked['discount'];
        $this->rememberCoupon($coupon, $discount, $checked['min_order_amount']);

        return $this->json([
            'success'            => true,
            'message'            => "Coupon '{$coupon['code']}' applied successfully!",
            'coupon_code'        => $coupon['code'],
            'discount'           => $discount,
            'formatted_discount' => '-$' . number_format($discount, 2)
        ]);
    }

    /**
     * POST /checkout/remove-coupon
     */
    public function removeCoupon()
    {
        unset($_SESSION['checkout_coupon']);
        return $this->json([
            'success' => true,
            'message' => 'Coupon removed.'
        ]);
    }

    /**
     * POST /checkout/process
     * Complete order validation, atomic database creation, stock deduction & order placement
     */
    public function process()
    {
        $payload = $this->getPayload();

        // 0. CSRF Token Verification
        $token = $payload['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!verify_csrf_token($token)) {
            return $this->json([
                'success' => false,
                'message' => 'Your security session has expired. Please refresh the page and try again.'
            ], 403);
        }

        // 1. Validate Form Fields
        $email = trim((string)($payload['email'] ?? ''));
        $firstName = trim((string)($payload['firstName'] ?? $payload['first_name'] ?? ''));
        $lastName = trim((string)($payload['lastName'] ?? $payload['last_name'] ?? ''));
        $phone = trim((string)($payload['phone'] ?? ''));
        $address1 = trim((string)($payload['address1'] ?? $payload['street'] ?? ''));
        $address2 = trim((string)($payload['address2'] ?? ''));
        $city = trim((string)($payload['city'] ?? ''));
        $state = strtoupper(trim((string)($payload['state'] ?? '')));
        $zip = trim((string)($payload['zip'] ?? ''));
        $shippingMethodCode = trim((string)($payload['shippingMethod'] ?? '5.99'));
        $shippingMethodName = trim((string)($payload['deliveryMethodName'] ?? 'Standard Ground Shipping'));
        $paymentType = trim((string)($payload['paymentType'] ?? 'credit_card'));
        $ageVerified = !empty($payload['ageVerification']);

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['success' => false, 'message' => 'Please provide a valid email address.'], 400);
        }

        if (!$firstName || !$lastName) {
            return $this->json(['success' => false, 'message' => 'Please provide your first and last name.'], 400);
        }

        if (!$address1 || !$city || !$state || !$zip) {
            return $this->json(['success' => false, 'message' => 'Please provide a complete shipping address (Street, City, State, ZIP).'], 400);
        }

        if (!$phone) {
            return $this->json(['success' => false, 'message' => 'Please provide a contact phone number.'], 400);
        }

        if (!$ageVerified) {
            return $this->json(['success' => false, 'message' => 'You must verify that you are at least 21 years old to order.'], 400);
        }

        // 2. Validate Cart and Live Stock in MariaDB
        $cart = Cart::getCart();
        if (empty($cart)) {
            return $this->json([
                'success' => false,
                'message' => 'Your shopping cart is currently empty. Please add products to proceed.'
            ], 400);
        }

        $db = Database::getInstance()->getConnection();

        // Re-verify stock in database for every cart line item
        foreach ($cart as $item) {
            $pId = (int)$item['product_id'];
            $vId = !empty($item['variant_id']) ? (int)$item['variant_id'] : null;
            $qty = (int)$item['quantity'];

            if ($vId) {
                $stmt = $db->prepare("SELECT stock_quantity, variant_name FROM product_variants WHERE id = :vid AND product_id = :pid");
                $stmt->execute([':vid' => $vId, ':pid' => $pId]);
                $varRow = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$varRow || (int)$varRow['stock_quantity'] < $qty) {
                    $available = $varRow ? (int)$varRow['stock_quantity'] : 0;
                    return $this->json([
                        'success' => false,
                        'message' => "Insufficient stock for '{$item['title']} - {$item['flavor']}'. Only {$available} left in stock."
                    ], 400);
                }
            } else {
                $stmt = $db->prepare("SELECT stock_quantity FROM products WHERE id = :pid");
                $stmt->execute([':pid' => $pId]);
                $prodRow = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$prodRow || (int)$prodRow['stock_quantity'] < $qty) {
                    $available = $prodRow ? (int)$prodRow['stock_quantity'] : 0;
                    return $this->json([
                        'success' => false,
                        'message' => "Insufficient stock for '{$item['title']}'. Only {$available} left in stock."
                    ], 400);
                }
            }
        }

        // 3. Authoritative Financial Calculations
        $settings = $this->getSettings();
        $taxRate = isset($settings['tax_rate_percent']) ? (float)$settings['tax_rate_percent'] : 4.50;
        $standardShipping = isset($settings['standard_shipping_rate']) ? (float)$settings['standard_shipping_rate'] : 5.99;
        $expressShipping = isset($settings['express_shipping_rate']) ? (float)$settings['express_shipping_rate'] : 14.99;
        $freeShippingThreshold = isset($settings['free_shipping_threshold']) ? (float)$settings['free_shipping_threshold'] : 150.00;
        $testCheckoutEnabled = !empty($settings['test_checkout_enabled']) && $settings['test_checkout_enabled'] == '1';

        $subtotal = 0.00;
        foreach ($cart as $it) {
            $subtotal += ((float)$it['price'] * (int)$it['quantity']);
        }
        $subtotal = round($subtotal, 2);

        // Resolve Shipping Rate
        $shippingCost = $standardShipping;
        if ($subtotal >= $freeShippingThreshold) {
            $shippingCost = 0.00;
            $shippingMethodName = 'Free Shipping (' . $shippingMethodName . ')';
        } elseif ((float)$shippingMethodCode === 0.00 || stripos($shippingMethodName, 'Pickup') !== false) {
            $shippingCost = 0.00;
            $shippingMethodName = 'In-Store Pickup (Oklahoma City)';
        } elseif ((float)$shippingMethodCode >= 10.00 || stripos($shippingMethodName, 'Express') !== false) {
            $shippingCost = $expressShipping;
            $shippingMethodName = 'Express Priority Delivery';
        }

        // Apply the coupon from a fresh database row, not the old session copy
        $discountAmount = 0.00;
        $couponId = null;
        $savedCoupon = $_SESSION['checkout_coupon'] ?? null;
        if ($savedCoupon && !empty($savedCoupon['id'])) {
            $checked = $this->findValidCoupon($savedCoupon['id'], $subtotal, true);
            if (empty($checked['ok'])) {
                unset($_SESSION['checkout_coupon']);
                return $this->json([
                    'success' => false,
                    'message' => $checked['message'] . ' Remove the code and place the order again.'
                ], 400);
            }
            $coupon = $checked['coupon'];
            $couponId = (int)$coupon['id'];
            $this->rememberCoupon($coupon, $checked['discount'], $checked['min_order_amount']);
            if ($coupon['type'] === 'percentage' || $coupon['type'] === 'fixed_amount') {
                $discountAmount = $checked['discount'];
            } elseif ($coupon['type'] === 'free_shipping') {
                $shippingCost = 0.00;
            }
        }

        $taxableAmount = max(0, $subtotal - $discountAmount);
        $taxAmount = round(($taxableAmount * ($taxRate / 100)), 2);
        $grandTotal = round(($taxableAmount + $shippingCost + $taxAmount), 2);

        // Map Payment Method to Enum ('credit_card','apple_pay','zelle')
        $cleanPaymentMethod = 'credit_card';
        if ($paymentType === 'apple_pay') {
            $cleanPaymentMethod = 'apple_pay';
        } elseif ($paymentType === 'zelle') {
            $cleanPaymentMethod = 'zelle';
        }

        // Format full shipping address
        $fullShippingAddress = "{$firstName} {$lastName}\n{$address1}" . ($address2 ? ", {$address2}" : "") . "\n{$city}, {$state} {$zip}\nUnited States\nPhone: {$phone}";

        // 4. ATOMIC DATABASE TRANSACTION
        try {
            $db->beginTransaction();

            // Generate Sequential / Unique Order Number
            $stmtMax = $db->query("SELECT MAX(id) FROM orders");
            $nextId = ((int)$stmtMax->fetchColumn()) + 1;
            $orderNumber = (string)(104820 + $nextId);

            // User ID if logged in
            $userId = null;
            if (class_exists('\\App\\Core\\CustomerAuth') && \App\Core\CustomerAuth::check()) {
                $userId = \App\Core\CustomerAuth::id();
            }

            // No card is charged in test checkout, so payment stays pending until a real payment is recorded.
            $paymentStatus = 'pending';
            $shippingStatus = 'confirmed';
            $fulfillmentStatus = 'unfulfilled';
            $deliveryStatus = 'pending';
            $adminNotes = $testCheckoutEnabled 
                ? 'Processed via Sandbox / Test Checkout Mode (No live card charged).' 
                : 'Order received via storefront checkout.';

            // Insert Order
            $stmtOrder = $db->prepare("
                INSERT INTO orders (
                    order_number, user_id, customer_first_name, customer_last_name, 
                    customer_name, customer_email, customer_phone, subtotal, tax_amount, 
                    shipping_cost, discount_amount, total_amount, coupon_id, payment_method, 
                    payment_status, shipping_status, fulfillment_status, delivery_status, 
                    delivery_method, shipping_address, admin_notes, age_verified, created_at, updated_at
                ) VALUES (
                    :order_number, :user_id, :first_name, :last_name, 
                    :customer_name, :email, :phone, :subtotal, :tax_amount, 
                    :shipping_cost, :discount_amount, :total_amount, :coupon_id, :payment_method, 
                    :payment_status, :shipping_status, :fulfillment_status, :delivery_status, 
                    :delivery_method, :shipping_address, :admin_notes, 1, NOW(), NOW()
                )
            ");

            $customerFullName = "{$firstName} {$lastName}";
            $stmtOrder->execute([
                ':order_number'       => $orderNumber,
                ':user_id'            => $userId,
                ':first_name'         => $firstName,
                ':last_name'          => $lastName,
                ':customer_name'      => $customerFullName,
                ':email'              => $email,
                ':phone'              => $phone,
                ':subtotal'           => $subtotal,
                ':tax_amount'         => $taxAmount,
                ':shipping_cost'      => $shippingCost,
                ':discount_amount'    => $discountAmount,
                ':total_amount'       => $grandTotal,
                ':coupon_id'          => $couponId,
                ':payment_method'     => $cleanPaymentMethod,
                ':payment_status'     => $paymentStatus,
                ':shipping_status'    => $shippingStatus,
                ':fulfillment_status' => $fulfillmentStatus,
                ':delivery_status'    => $deliveryStatus,
                ':delivery_method'    => $shippingMethodName,
                ':shipping_address'   => $fullShippingAddress,
                ':admin_notes'        => $adminNotes
            ]);

            $orderId = (int)$db->lastInsertId();

            // Insert Line Items & Decrement Stock
            $stmtItem = $db->prepare("
                INSERT INTO order_items (
                    order_id, product_id, variant_id, product_title, variant_name, quantity, price_at_purchase
                ) VALUES (
                    :order_id, :product_id, :variant_id, :product_title, :variant_name, :quantity, :price_at_purchase
                )
            ");

            $stmtDecVariant = $db->prepare("
                UPDATE product_variants 
                SET stock_quantity = GREATEST(0, stock_quantity - :qty) 
                WHERE id = :vid
            ");

            $stmtDecProduct = $db->prepare("
                UPDATE products 
                SET stock_quantity = GREATEST(0, stock_quantity - :qty) 
                WHERE id = :pid
            ");

            $stmtInvLog = $db->prepare("
                INSERT INTO inventory_logs (variant_id, order_id, change_amount, reason, created_at)
                VALUES (:vid, :oid, :amount, :reason, NOW())
            ");

            foreach ($cart as $it) {
                $pId = (int)$it['product_id'];
                $vId = !empty($it['variant_id']) ? (int)$it['variant_id'] : null;
                $qty = (int)$it['quantity'];
                $price = (float)$it['price'];
                $title = (string)$it['title'];
                $flavor = (string)($it['variant_name'] ?? $it['flavor'] ?? '');

                // Insert into order_items
                $stmtItem->execute([
                    ':order_id'          => $orderId,
                    ':product_id'        => $pId,
                    ':variant_id'        => $vId,
                    ':product_title'     => $title,
                    ':variant_name'      => $flavor,
                    ':quantity'          => $qty,
                    ':price_at_purchase' => $price
                ]);

                // Live Stock Decrement in MariaDB
                if ($vId) {
                    $stmtDecVariant->execute([':qty' => $qty, ':vid' => $vId]);
                    // Also log to inventory_logs
                    try {
                        $stmtInvLog->execute([
                            ':vid'    => $vId,
                            ':oid'    => $orderId,
                            ':amount' => -$qty,
                            ':reason' => "Customer Order #{$orderNumber}"
                        ]);
                    } catch (\Exception $e) {}
                } else {
                    $stmtDecProduct->execute([':qty' => $qty, ':pid' => $pId]);
                    try {
                        $stmtInvLog->execute([
                            ':vid'    => null,
                            ':oid'    => $orderId,
                            ':amount' => -$qty,
                            ':reason' => "Customer Order #{$orderNumber}"
                        ]);
                    } catch (\Exception $e) {}
                }
            }

            // Update Coupon Usage Count if the code is still available
            if ($couponId) {
                $lock = $db->prepare("SELECT is_active, max_uses, uses_count, start_date, end_date FROM coupons WHERE id = :cid FOR UPDATE");
                $lock->execute([':cid' => $couponId]);
                $freshCoupon = $lock->fetch(PDO::FETCH_ASSOC);
                $freshStart = $freshCoupon ? $this->couponTimestamp($freshCoupon['start_date'] ?? null) : null;
                $freshEnd = $freshCoupon ? $this->couponTimestamp($freshCoupon['end_date'] ?? null) : null;
                $couponStillOk = $freshCoupon
                    && (int)$freshCoupon['is_active'] === 1
                    && ($freshStart === null || $freshStart <= time())
                    && ($freshEnd === null || $freshEnd >= time())
                    && (empty($freshCoupon['max_uses']) || (int)$freshCoupon['uses_count'] < (int)$freshCoupon['max_uses']);
                if (!$couponStillOk) {
                    $db->rollBack();
                    unset($_SESSION['checkout_coupon']);
                    return $this->json([
                        'success' => false,
                        'message' => 'This coupon is no longer available. Remove the code and place the order again.'
                    ], 400);
                }
                $db->prepare("UPDATE coupons SET uses_count = uses_count + 1 WHERE id = :cid")->execute([':cid' => $couponId]);
            }

            // Insert User Address record
            try {
                $stmtAddr = $db->prepare("
                    INSERT INTO user_addresses (
                        user_id, order_id, first_name, last_name, address_type, 
                        street, city, state, zip, phone, is_default, created_at
                    ) VALUES (
                        :uid, :oid, :fn, :ln, 'shipping', 
                        :street, :city, :st, :zip, :phone, 1, NOW()
                    )
                ");
                $stmtAddr->execute([
                    ':uid'    => $userId,
                    ':oid'    => $orderId,
                    ':fn'     => $firstName,
                    ':ln'     => $lastName,
                    ':street' => $address1 . ($address2 ? ", {$address2}" : ""),
                    ':city'   => $city,
                    ':st'     => $state,
                    ':zip'    => $zip,
                    ':phone'  => $phone
                ]);
            } catch (\Exception $e) {}

            // Commit Transaction
            $db->commit();

            // Record Analytics Order Completed Funnel Event
            if (class_exists('\\App\\Models\\Analytics')) {
                \App\Models\Analytics::recordEvent('/checkout', 'order_complete', 'Order #' . $orderNumber, (int)$orderId);
            }

            // Clear Session Cart and Coupon
            Cart::clear();
            unset($_SESSION['checkout_coupon']);

            // Save order info into session for receipt confirmation page
            $_SESSION['last_order'] = [
                'order_id'          => $orderId,
                'order_number'      => $orderNumber,
                'customer_name'     => $customerFullName,
                'email'             => $email,
                'phone'             => $phone,
                'total'             => '$' . number_format($grandTotal, 2),
                'subtotal'          => '$' . number_format($subtotal, 2),
                'shipping'          => '$' . number_format($shippingCost, 2),
                'tax'               => '$' . number_format($taxAmount, 2),
                'discount'          => '$' . number_format($discountAmount, 2),
                'payment_method'    => ucwords(str_replace('_', ' ', $cleanPaymentMethod)),
                'delivery_method'   => $shippingMethodName,
                'shipping_address'  => $fullShippingAddress,
                'date'              => date('F d, Y h:i A')
            ];

            return $this->json([
                'success'           => true,
                'message'           => 'Thank you for your order! Your order has been placed successfully.',
                'order_number'      => '#' . $orderNumber,
                'order_id'          => $orderId,
                'total'             => '$' . number_format($grandTotal, 2),
                'payment_method'    => ucwords(str_replace('_', ' ', $cleanPaymentMethod)),
                'delivery_estimate' => $shippingMethodName,
                'test_mode'         => $testCheckoutEnabled
            ]);

        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            return $this->json([
                'success' => false,
                'message' => 'Failed to process order. ' . $e->getMessage()
            ], 500);
        }
    }
}
