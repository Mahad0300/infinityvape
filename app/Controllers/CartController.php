<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cart;

class CartController extends Controller
{
    /**
     * Parse incoming JSON or form-encoded request payload
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
     * GET /cart/items
     * Returns full verified cart and financial totals
     */
    public function items()
    {
        $cart = Cart::getCart();
        $totals = Cart::totals();

        return $this->json([
            'success'     => true,
            'cart'        => $cart,
            'totals'      => $totals,
            'total_count' => $totals['count'],
            'subtotal'    => $totals['subtotal']
        ]);
    }

    /**
     * POST /cart/add
     * Adds an item to the session cart after validating against the database
     */
    public function add()
    {
        $payload = $this->getPayload();

        $productId = (int)($payload['product_id'] ?? $payload['productId'] ?? 0);
        $variantId = !empty($payload['variant_id']) ? (int)$payload['variant_id'] : (!empty($payload['variantId']) ? (int)$payload['variantId'] : null);
        $quantity  = max(1, (int)($payload['quantity'] ?? $payload['qty'] ?? 1));

        if ($productId <= 0) {
            return $this->json([
                'success' => false,
                'message' => 'Invalid product identifier.'
            ], 400);
        }

        $result = Cart::add($productId, $variantId, $quantity);
        if (!empty($result['success']) && class_exists('\\App\\Models\\Analytics')) {
            \App\Models\Analytics::recordEvent('/cart', 'cart_add', 'Product #' . $productId, $quantity);
        }
        $status = $result['success'] ? 200 : 400;

        return $this->json($result, $status);
    }

    /**
     * POST /cart/add-multiple
     * Adds multiple items (e.g., FBT bundle) in a single request
     */
    public function addMultiple()
    {
        $payload = $this->getPayload();
        $items = $payload['items'] ?? [];

        if (!is_array($items) || empty($items)) {
            return $this->json([
                'success' => false,
                'message' => 'No items provided to add.'
            ], 400);
        }

        $addedCount = 0;
        $errors = [];

        foreach ($items as $item) {
            $pId = (int)($item['product_id'] ?? $item['productId'] ?? 0);
            $vId = !empty($item['variant_id']) ? (int)$item['variant_id'] : (!empty($item['variantId']) ? (int)$item['variantId'] : null);
            $qty = max(1, (int)($item['quantity'] ?? $item['qty'] ?? 1));

            if ($pId > 0) {
                $res = Cart::add($pId, $vId, $qty);
                if ($res['success']) {
                    $addedCount += $qty;
                } else {
                    $errors[] = $res['message'];
                }
            }
        }

        $cart = Cart::getCart();
        $totals = Cart::totals();

        if ($addedCount > 0) {
            return $this->json([
                'success'     => true,
                'message'     => "{$addedCount} item(s) added to cart.",
                'cart'        => $cart,
                'totals'      => $totals,
                'total_count' => $totals['count'],
                'subtotal'    => $totals['subtotal'],
                'warnings'    => $errors
            ]);
        }

        return $this->json([
            'success' => false,
            'message' => !empty($errors) ? implode(' ', $errors) : 'Failed to add items to cart.',
            'cart'    => $cart,
            'totals'  => $totals
        ], 400);
    }

    /**
     * POST /cart/update
     * Updates the quantity of a specific cart item
     */
    public function update()
    {
        $payload = $this->getPayload();

        $key = trim((string)($payload['id'] ?? $payload['key'] ?? ''));
        $quantity = (int)($payload['quantity'] ?? $payload['qty'] ?? 0);

        if ($key === '') {
            return $this->json([
                'success' => false,
                'message' => 'Item identifier is required.'
            ], 400);
        }

        $result = Cart::update($key, $quantity);
        $status = $result['success'] ? 200 : 400;

        return $this->json($result, $status);
    }

    /**
     * POST /cart/remove
     * Removes an item from the cart
     */
    public function remove()
    {
        $payload = $this->getPayload();

        $key = trim((string)($payload['id'] ?? $payload['key'] ?? ''));

        if ($key === '') {
            return $this->json([
                'success' => false,
                'message' => 'Item identifier is required.'
            ], 400);
        }

        $result = Cart::remove($key);

        return $this->json($result, 200);
    }

    /**
     * POST /cart/clear
     * Empties the shopping cart
     */
    public function clear()
    {
        $result = Cart::clear();

        return $this->json($result, 200);
    }
}

