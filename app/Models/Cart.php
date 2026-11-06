<?php
namespace App\Models;

use App\Core\Model;
use App\Models\Product;

class Cart extends Model
{
    const FREE_SHIPPING_THRESHOLD = 150.00;

    /**
     * Ensure session is active
     */
    protected static function initSession(): void
    {
        if (function_exists('session_init')) {
            session_init();
        } elseif (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @session_start();
        }

        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    /**
     * Get all verified cart items (synced with real-time database stock & prices)
     */
    public static function getCart(): array
    {
        self::initSession();

        $cart = $_SESSION['cart'];
        $verifiedCart = [];
        $hasChanges = false;

        foreach ($cart as $key => $item) {
            $productId = (int)($item['product_id'] ?? 0);
            $variantId = !empty($item['variant_id']) ? (int)$item['variant_id'] : null;

            if (!$productId) {
                $hasChanges = true;
                continue;
            }

            $product = Product::find($productId);
            if (!$product || ($product['status'] ?? '') !== 'published') {
                $hasChanges = true;
                continue;
            }

            $availableStock = 0;
            $price = (float)($product['base_price'] ?? 0);
            $flavor = '';
            $sku = $product['sku'] ?? '';
            $image = !empty($product['featured_image']) ? asset($product['featured_image']) : asset('assets/images/vape-image.webp');

            if ($variantId) {
                $variant = Product::findVariant($variantId);
                if (!$variant || (int)$variant['product_id'] !== $productId) {
                    $hasChanges = true;
                    continue;
                }
                $availableStock = (int)($variant['stock_quantity'] ?? 0);
                if (!empty($variant['price'])) {
                    $price = (float)$variant['price'];
                }
                $flavor = $variant['variant_name'] ?? '';
                if (!empty($variant['image_url'])) {
                    $image = asset($variant['image_url']);
                }
                if (!empty($variant['sku'])) {
                    $sku = $variant['sku'];
                }
            } else {
                $availableStock = (int)($product['stock_quantity'] ?? 0);
            }

            // If completely out of stock, remove from cart
            if ($availableStock <= 0) {
                $hasChanges = true;
                continue;
            }

            // Cap quantity if exceeding available stock
            $qty = max(1, (int)($item['quantity'] ?? 1));
            if ($qty > $availableStock) {
                $qty = $availableStock;
                $hasChanges = true;
            }

            $verifiedCart[$key] = [
                'id'           => $key,
                'product_id'   => $productId,
                'variant_id'   => $variantId,
                'title'        => $product['title'] ?? 'Product',
                'product_slug' => $product['slug'] ?? (string)$productId,
                'flavor'       => $flavor,
                'variant_name' => $flavor,
                'price'        => $price,
                'quantity'     => $qty,
                'image'        => $image,
                'sku'          => $sku,
                'max_stock'    => $availableStock
            ];
        }

        if ($hasChanges) {
            $_SESSION['cart'] = $verifiedCart;
        }

        return array_values($verifiedCart);
    }

    /**
     * Add product/variant to cart with database validation
     */
    public static function add(int $productId, ?int $variantId = null, int $quantity = 1): array
    {
        self::initSession();

        if ($productId <= 0) {
            return ['success' => false, 'message' => 'Invalid product identifier.'];
        }

        $quantity = max(1, $quantity);
        $product = Product::find($productId);

        if (!$product) {
            return ['success' => false, 'message' => 'Product not found.'];
        }

        if (($product['status'] ?? '') !== 'published') {
            return ['success' => false, 'message' => 'This product is currently not available for purchase.'];
        }

        $availableStock = 0;
        $price = (float)($product['base_price'] ?? 0);
        $flavor = '';
        $sku = $product['sku'] ?? '';
        $image = !empty($product['featured_image']) ? asset($product['featured_image']) : asset('assets/images/vape-image.webp');

        // Check if product has variants in DB
        $allVariants = Product::getVariants($productId, $product);

        if (!empty($allVariants)) {
            // If specific variant requested
            if ($variantId) {
                $variant = Product::findVariant($variantId);
                if (!$variant || (int)$variant['product_id'] !== $productId) {
                    return ['success' => false, 'message' => 'Selected option is invalid.'];
                }
            } else {
                // Auto-select first in-stock variant
                $variant = null;
                foreach ($allVariants as $v) {
                    if ((int)($v['stock_quantity'] ?? 0) > 0) {
                        $variant = $v;
                        break;
                    }
                }
                if (!$variant) {
                    $variant = $allVariants[0];
                }
                $variantId = (int)$variant['id'];
            }

            $availableStock = (int)($variant['stock_quantity'] ?? 0);
            if (!empty($variant['price'])) {
                $price = (float)$variant['price'];
            }
            $flavor = $variant['variant_name'] ?? '';
            if (!empty($variant['image_url'])) {
                $image = asset($variant['image_url']);
            }
            if (!empty($variant['sku'])) {
                $sku = $variant['sku'];
            }
        } else {
            $variantId = null;
            $availableStock = (int)($product['stock_quantity'] ?? 0);
        }

        if ($availableStock <= 0) {
            return [
                'success' => false,
                'message' => 'Sorry, this product is currently Out of Stock.'
            ];
        }

        // Unique item key
        $itemKey = $variantId ? "{$productId}-{$variantId}" : "{$productId}";
        $currentInCart = isset($_SESSION['cart'][$itemKey]['quantity']) ? (int)$_SESSION['cart'][$itemKey]['quantity'] : 0;
        $newTotalQty = $currentInCart + $quantity;

        if ($newTotalQty > $availableStock) {
            if ($currentInCart >= $availableStock) {
                return [
                    'success' => false,
                    'message' => "Maximum quantity reached. You already have all {$availableStock} available units in your cart."
                ];
            }
            // Cap to available stock
            $quantity = $availableStock - $currentInCart;
            $newTotalQty = $availableStock;
        }

        $cartItem = [
            'id'           => $itemKey,
            'product_id'   => $productId,
            'variant_id'   => $variantId,
            'title'        => $product['title'] ?? 'Product',
            'product_slug' => $product['slug'] ?? (string)$productId,
            'flavor'       => $flavor,
            'variant_name' => $flavor,
            'price'        => $price,
            'quantity'     => $newTotalQty,
            'image'        => $image,
            'sku'          => $sku,
            'max_stock'    => $availableStock
        ];

        $_SESSION['cart'][$itemKey] = $cartItem;

        $items = self::getCart();
        $totals = self::totals();

        return [
            'success'     => true,
            'message'     => 'Added to cart successfully!',
            'item'        => $cartItem,
            'cart'        => $items,
            'totals'      => $totals,
            'total_count' => $totals['count'],
            'subtotal'    => $totals['subtotal']
        ];
    }

    /**
     * Update quantity of a cart item
     */
    public static function update(string $itemKey, int $quantity): array
    {
        self::initSession();

        if (!isset($_SESSION['cart'][$itemKey])) {
            return ['success' => false, 'message' => 'Cart item not found.'];
        }

        if ($quantity <= 0) {
            return self::remove($itemKey);
        }

        $item = $_SESSION['cart'][$itemKey];
        $productId = (int)$item['product_id'];
        $variantId = !empty($item['variant_id']) ? (int)$item['variant_id'] : null;

        $availableStock = 0;
        if ($variantId) {
            $variant = Product::findVariant($variantId);
            $availableStock = $variant ? (int)$variant['stock_quantity'] : 0;
        } else {
            $product = Product::find($productId);
            $availableStock = $product ? (int)$product['stock_quantity'] : 0;
        }

        if ($availableStock <= 0) {
            unset($_SESSION['cart'][$itemKey]);
            return [
                'success' => false,
                'message' => 'This item is now out of stock and was removed from your cart.',
                'cart'    => self::getCart(),
                'totals'  => self::totals()
            ];
        }

        $capped = false;
        if ($quantity > $availableStock) {
            $quantity = $availableStock;
            $capped = true;
        }

        $_SESSION['cart'][$itemKey]['quantity'] = $quantity;
        $_SESSION['cart'][$itemKey]['max_stock'] = $availableStock;

        $totals = self::totals();

        return [
            'success'     => true,
            'message'     => $capped ? "Quantity adjusted to maximum available stock ({$availableStock})." : 'Cart updated.',
            'item'        => $_SESSION['cart'][$itemKey],
            'cart'        => self::getCart(),
            'totals'      => $totals,
            'total_count' => $totals['count'],
            'subtotal'    => $totals['subtotal']
        ];
    }

    /**
     * Remove item from cart
     */
    public static function remove(string $itemKey): array
    {
        self::initSession();

        if (isset($_SESSION['cart'][$itemKey])) {
            unset($_SESSION['cart'][$itemKey]);
        }

        $totals = self::totals();

        return [
            'success'     => true,
            'message'     => 'Item removed from cart.',
            'cart'        => self::getCart(),
            'totals'      => $totals,
            'total_count' => $totals['count'],
            'subtotal'    => $totals['subtotal']
        ];
    }

    /**
     * Clear all items from cart
     */
    public static function clear(): array
    {
        self::initSession();
        $_SESSION['cart'] = [];

        return [
            'success'     => true,
            'message'     => 'Cart cleared.',
            'cart'        => [],
            'totals'      => self::totals(),
            'total_count' => 0,
            'subtotal'    => 0.00
        ];
    }

    /**
     * Calculate financial totals & free shipping threshold progress
     */
    public static function totals(): array
    {
        self::initSession();
        $cart = self::getCart();

        $totalCount = 0;
        $subtotal = 0.00;

        foreach ($cart as $item) {
            $qty = (int)($item['quantity'] ?? 1);
            $price = (float)($item['price'] ?? 0);
            $totalCount += $qty;
            $subtotal += ($price * $qty);
        }

        $subtotal = round($subtotal, 2);
        $threshold = self::FREE_SHIPPING_THRESHOLD;
        $freeShipping = ($subtotal >= $threshold);
        $needed = max(0, round($threshold - $subtotal, 2));
        $progress = min(100, round(($subtotal / $threshold) * 100));

        return [
            'count'                => $totalCount,
            'subtotal'             => $subtotal,
            'formatted_subtotal'   => '$' . number_format($subtotal, 2),
            'free_shipping'        => $freeShipping,
            'free_shipping_needed' => $needed,
            'formatted_needed'     => '$' . number_format($needed, 2),
            'shipping_progress'    => $progress
        ];
    }
}

