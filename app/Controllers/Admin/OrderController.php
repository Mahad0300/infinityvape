<?php
namespace App\Controllers\Admin;

use App\Models\Order;
use App\Core\Database;
use PDO;

class OrderController extends AdminController
{
    public function index()
    {
        $orders = Order::all();
        $this->render('admin.orders.index', [
            'page_title' => 'Orders',
            'current_admin_page' => 'orders',
            'page_script' => 'orders.js',
            'orders' => $orders
        ]);
    }

    public function detail()
    {
        $id = trim($_GET['id'] ?? '');
        if (empty($id)) {
            set_flash('error', 'Order ID is required.');
            $this->redirect('admin/orders');
            return;
        }

        $order = Order::find($id);

        if (!$order) {
            set_flash('error', 'The requested order was not found.');
            $this->redirect('admin/orders');
            return;
        }

        $this->render('admin.orders.detail', [
            'page_title' => 'Order #' . ($order['order_number'] ?? $id),
            'current_admin_page' => 'orders',
            'page_script' => 'order-detail.js',
            'order' => $order
        ]);
    }

    /**
     * Process order status, tracking, and carrier updates
     */
    public function updateStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['success' => false, 'message' => 'Invalid request method.'], 405);
        }

        // Parse input from JSON or POST body
        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true);
        if (!is_array($input)) {
            $input = $_POST;
        }

        $orderId = trim($input['order_id'] ?? $input['order_number'] ?? $input['id'] ?? '');
        if (empty($orderId)) {
            return $this->json(['success' => false, 'message' => 'Order reference ID is required.'], 400);
        }

        $db = Database::getConnection();
        if (!$db) {
            return $this->json(['success' => false, 'message' => 'Database connection unavailable.'], 500);
        }

        try {
            $stmt = $db->prepare("SELECT * FROM orders WHERE id = :id OR order_number = :num LIMIT 1");
            $stmt->execute([':id' => $orderId, ':num' => $orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                return $this->json(['success' => false, 'message' => 'Order not found in database.'], 404);
            }

            $realId = (int)$order['id'];
            $orderNum = $order['order_number'];
            $oldShippingStatus = $order['shipping_status'] ?? 'pending';

            // New status values
            $newShippingStatus = strtolower(trim($input['shipping_status'] ?? $oldShippingStatus));
            $newPaymentStatus = strtolower(trim($input['payment_status'] ?? ($order['payment_status'] ?? 'paid')));
            $shippingCarrier = trim($input['shipping_carrier'] ?? ($order['shipping_carrier'] ?? ''));
            $trackingNumber = trim($input['tracking_number'] ?? ($order['tracking_number'] ?? ''));
            $adminNotes = trim($input['admin_notes'] ?? ($order['admin_notes'] ?? ''));

            // Allowed ENUM validations
            $allowedShipping = ['pending', 'confirmed', 'processing', 'shipped', 'in_transit', 'out_for_delivery', 'delivered', 'cancelled'];
            if (!in_array($newShippingStatus, $allowedShipping)) {
                $newShippingStatus = $oldShippingStatus;
            }

            $allowedPayment = ['pending', 'paid', 'failed', 'refunded'];
            if (!in_array($newPaymentStatus, $allowedPayment)) {
                $newPaymentStatus = $order['payment_status'] ?? 'paid';
            }

            // Derive Delivery Status
            $deliveryStatus = 'pending';
            if ($newShippingStatus === 'shipped') {
                $deliveryStatus = 'shipped';
            } elseif ($newShippingStatus === 'in_transit') {
                $deliveryStatus = 'in_transit';
            } elseif ($newShippingStatus === 'out_for_delivery') {
                $deliveryStatus = 'out_for_delivery';
            } elseif ($newShippingStatus === 'delivered') {
                $deliveryStatus = 'delivered';
            } elseif ($newShippingStatus === 'cancelled') {
                $deliveryStatus = 'cancelled';
            } elseif ($newShippingStatus === 'confirmed' || $newShippingStatus === 'processing') {
                $deliveryStatus = $newShippingStatus;
            }

            // Derive Fulfillment Status
            $fulfillmentStatus = $order['fulfillment_status'] ?? 'unfulfilled';
            if (in_array($newShippingStatus, ['shipped', 'in_transit', 'out_for_delivery', 'delivered'])) {
                $fulfillmentStatus = 'fulfilled';
            } elseif ($newShippingStatus === 'cancelled') {
                $fulfillmentStatus = 'unfulfilled';
            } elseif (in_array($newShippingStatus, ['pending', 'confirmed', 'processing'])) {
                $fulfillmentStatus = 'unfulfilled';
            }

            // Stock Restocking if transitioning to Cancelled
            if ($oldShippingStatus !== 'cancelled' && $newShippingStatus === 'cancelled') {
                $stmtItems = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
                $stmtItems->execute([$realId]);
                $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

                foreach ($items as $item) {
                    $qty = (int)$item['quantity'];
                    if ($qty <= 0) continue;

                    if (!empty($item['variant_id'])) {
                        // Restock variant
                        $upVar = $db->prepare("UPDATE product_variants SET stock_quantity = stock_quantity + ? WHERE id = ?");
                        $upVar->execute([$qty, $item['variant_id']]);

                        // Sync parent product stock
                        $syncProd = $db->prepare("UPDATE products SET stock_quantity = (SELECT COALESCE(SUM(stock_quantity), 0) FROM product_variants WHERE product_id = ?) WHERE id = ?");
                        $syncProd->execute([$item['product_id'], $item['product_id']]);

                        // Insert inventory audit log
                        $logStmt = $db->prepare("INSERT INTO inventory_logs (variant_id, order_id, change_amount, reason, created_at) VALUES (?, ?, ?, ?, NOW())");
                        $logStmt->execute([$item['variant_id'], $realId, $qty, "Order #{$orderNum} Cancelled - Inventory Restocked"]);
                    } elseif (!empty($item['product_id'])) {
                        $upProd = $db->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?");
                        $upProd->execute([$qty, $item['product_id']]);

                        $logStmt = $db->prepare("INSERT INTO inventory_logs (variant_id, order_id, change_amount, reason, created_at) VALUES (NULL, ?, ?, ?, NOW())");
                        $logStmt->execute([$realId, $qty, "Order #{$orderNum} Cancelled - Inventory Restocked"]);
                    }
                }
            }
            // Re-deduct if mistakenly cancelled and reopened
            elseif ($oldShippingStatus === 'cancelled' && $newShippingStatus !== 'cancelled') {
                $stmtItems = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
                $stmtItems->execute([$realId]);
                $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

                foreach ($items as $item) {
                    $qty = (int)$item['quantity'];
                    if ($qty <= 0) continue;

                    if (!empty($item['variant_id'])) {
                        $upVar = $db->prepare("UPDATE product_variants SET stock_quantity = GREATEST(0, stock_quantity - ?) WHERE id = ?");
                        $upVar->execute([$qty, $item['variant_id']]);

                        $syncProd = $db->prepare("UPDATE products SET stock_quantity = (SELECT COALESCE(SUM(stock_quantity), 0) FROM product_variants WHERE product_id = ?) WHERE id = ?");
                        $syncProd->execute([$item['product_id'], $item['product_id']]);

                        $logStmt = $db->prepare("INSERT INTO inventory_logs (variant_id, order_id, change_amount, reason, created_at) VALUES (?, ?, ?, ?, NOW())");
                        $logStmt->execute([$item['variant_id'], $realId, -$qty, "Order #{$orderNum} Reopened from Cancelled - Stock Deducted"]);
                    } elseif (!empty($item['product_id'])) {
                        $upProd = $db->prepare("UPDATE products SET stock_quantity = GREATEST(0, stock_quantity - ?) WHERE id = ?");
                        $upProd->execute([$qty, $item['product_id']]);

                        $logStmt = $db->prepare("INSERT INTO inventory_logs (variant_id, order_id, change_amount, reason, created_at) VALUES (NULL, ?, ?, ?, NOW())");
                        $logStmt->execute([$realId, -$qty, "Order #{$orderNum} Reopened from Cancelled - Stock Deducted"]);
                    }
                }
            }

            // Persist order update
            $updateStmt = $db->prepare("UPDATE orders SET 
                shipping_status = :shipping_status,
                delivery_status = :delivery_status,
                fulfillment_status = :fulfillment_status,
                payment_status = :payment_status,
                shipping_carrier = :shipping_carrier,
                tracking_number = :tracking_number,
                admin_notes = :admin_notes,
                updated_at = NOW()
                WHERE id = :id");

            $updateStmt->execute([
                ':shipping_status'    => $newShippingStatus,
                ':delivery_status'    => $deliveryStatus,
                ':fulfillment_status' => $fulfillmentStatus,
                ':payment_status'     => $newPaymentStatus,
                ':shipping_carrier'   => $shippingCarrier,
                ':tracking_number'    => $trackingNumber,
                ':admin_notes'        => $adminNotes,
                ':id'                 => $realId
            ]);

            return $this->json([
                'success' => true,
                'message' => 'Order status, tracking, and fulfillment successfully updated.',
                'data' => [
                    'order_id'              => $orderNum,
                    'shipping_status'       => $newShippingStatus,
                    'shipping_status_label' => ucwords(str_replace('_', ' ', $newShippingStatus)),
                    'delivery_status'       => $deliveryStatus,
                    'fulfillment_status'    => ucfirst($fulfillmentStatus),
                    'payment_status'        => $newPaymentStatus,
                    'payment_status_label'  => ucfirst($newPaymentStatus),
                    'shipping_carrier'      => $shippingCarrier,
                    'tracking_number'       => $trackingNumber,
                    'admin_notes'           => $adminNotes
                ]
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
    }
}
