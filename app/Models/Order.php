<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Order extends Model
{
    /**
     * Get all orders formatted for Admin display
     */
    public static function all()
    {
        $db = self::getDB();
        if ($db) {
            try {
                $stmt = $db->query("SELECT o.*, 
                    (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) as item_count 
                    FROM orders o 
                    ORDER BY o.id DESC");
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($results)) {
                    $formatted = [];
                    foreach ($results as $r) {
                        $count = (int)($r['item_count'] ?? 1);
                        $formatted[] = [
                            'id'                 => $r['order_number'] ?: (string)$r['id'],
                            'db_id'              => $r['id'],
                            'date'               => date('M d, Y', strtotime($r['created_at'])),
                            'customer'           => $r['customer_name'] ?: trim(($r['customer_first_name'] ?? '') . ' ' . ($r['customer_last_name'] ?? '')),
                            'email'              => $r['customer_email'],
                            'phone'              => $r['customer_phone'] ?? '',
                            'total'              => '$' . number_format((float)$r['total_amount'], 2),
                            'subtotal'           => '$' . number_format((float)$r['subtotal'], 2),
                            'shipping_cost'      => '$' . number_format((float)$r['shipping_cost'], 2),
                            'tax_amount'         => '$' . number_format((float)$r['tax_amount'], 2),
                            'discount_amount'    => '$' . number_format((float)$r['discount_amount'], 2),
                            'payment_status'     => ucfirst($r['payment_status'] ?? 'Pending'),
                            'payment_method'     => ucwords(str_replace('_', ' ', $r['payment_method'] ?? 'credit_card')),
                            'fulfillment_status' => ucfirst($r['fulfillment_status'] ?? 'Unfulfilled'),
                            'items'              => $count . ' ' . ($count === 1 ? 'item' : 'items'),
                            'delivery_status'    => ucwords(str_replace('_', ' ', $r['delivery_status'] ?: ($r['shipping_status'] ?: 'pending'))),
                            'delivery_method'    => $r['delivery_method'] ?: 'Standard Ground',
                            'shipping_address'   => $r['shipping_address'] ?? ''
                        ];
                    }
                    return $formatted;
                }
            } catch (\Exception $e) {}
        }

        return [];
    }

    /**
     * Find single order with items
     */
    public static function find($id)
    {
        $db = self::getDB();
        if ($db) {
            try {
                $stmt = $db->prepare("SELECT * FROM orders WHERE id = :id OR order_number = :num LIMIT 1");
                $stmt->execute([':id' => $id, ':num' => $id]);
                $order = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($order) {
                    $stmtItems = $db->prepare("SELECT oi.*, p.slug as product_slug, p.featured_image 
                                               FROM order_items oi 
                                               LEFT JOIN products p ON p.id = oi.product_id 
                                               WHERE oi.order_id = :oid");
                    $stmtItems->execute([':oid' => $order['id']]);
                    $order['items_list'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

                    // Formatted fields for display
                    $order['formatted_total'] = '$' . number_format((float)$order['total_amount'], 2);
                    $order['formatted_subtotal'] = '$' . number_format((float)$order['subtotal'], 2);
                    $order['formatted_shipping'] = '$' . number_format((float)$order['shipping_cost'], 2);
                    $order['formatted_tax'] = '$' . number_format((float)$order['tax_amount'], 2);
                    $order['formatted_discount'] = '$' . number_format((float)$order['discount_amount'], 2);
                    $order['date'] = date('M d, Y h:i A', strtotime($order['created_at']));

                    return $order;
                }
            } catch (\Exception $e) {}
        }

        return null;
    }
}
