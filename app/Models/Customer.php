<?php
namespace App\Models;

use App\Core\Model;

class Customer extends Model
{
    /**
     * Fetch all customers with their location, order count, and total spent
     */
    public static function all($search = '', $status = '')
    {
        $db = self::getDB();
        if ($db) {
            try {
                $sql = "SELECT u.id, 
                               CONCAT(TRIM(u.first_name), ' ', TRIM(u.last_name)) AS name, 
                               u.first_name, 
                               u.last_name, 
                               u.email, 
                               u.phone, 
                               u.status, 
                               u.age_verified, 
                               u.birth_date,
                               u.total_orders,
                               u.total_spent,
                               u.tags,
                               u.admin_notes,
                               u.created_at,
                               COALESCE(ua.city, '') AS city,
                               COALESCE(ua.state, '') AS state,
                               COALESCE(ua.street, '') AS street,
                               COALESCE(ua.zip, '') AS zip,
                               COALESCE(ua.country, 'United States') AS country,
                               (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.id) AS real_order_count,
                               (SELECT COALESCE(SUM(o.total_amount), 0) FROM orders o WHERE o.user_id = u.id) AS real_total_spent
                        FROM users u
                        LEFT JOIN user_addresses ua ON u.id = ua.user_id AND ua.address_type = 'shipping'
                        WHERE u.role = 'customer'";

                $params = [];
                if (!empty($search)) {
                    $sql .= " AND (CONCAT(u.first_name, ' ', u.last_name) LIKE ? OR u.email LIKE ? OR ua.city LIKE ? OR ua.state LIKE ?)";
                    $term = "%{$search}%";
                    $params = [$term, $term, $term, $term];
                }

                if (!empty($status)) {
                    $sql .= " AND u.status = ?";
                    $params[] = $status;
                }

                $sql .= " GROUP BY u.id ORDER BY u.id ASC";

                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                if (!empty($rows)) {
                    foreach ($rows as &$row) {
                        // Location formatting
                        if (!empty($row['city']) && !empty($row['state'])) {
                            $row['location'] = $row['city'] . ', ' . $row['state'];
                        } elseif (!empty($row['city'])) {
                            $row['location'] = $row['city'];
                        } elseif (!empty($row['state'])) {
                            $row['location'] = $row['state'];
                        } else {
                            $row['location'] = 'Not set yet';
                        }

                        // Orders count formatting
                        $ordersCount = max((int)$row['total_orders'], (int)$row['real_order_count']);
                        $row['orders_count'] = $ordersCount;
                        $row['orders'] = $ordersCount . ($ordersCount === 1 ? ' order' : ' orders');

                        // Total spent formatting
                        $spentVal = (float)$row['total_spent'] > 0 ? (float)$row['total_spent'] : (float)$row['real_total_spent'];
                        $row['total_spent_val'] = $spentVal;
                        $row['spent'] = '$' . number_format($spentVal, 2);
                    }
                    return $rows;
                }
            } catch (\Exception $e) {
                error_log("Customer::all error: " . $e->getMessage());
            }
        }
        return [];
    }

    /**
     * Find single customer by ID with full details, address, order history, and frequent items
     */
    public static function find($id)
    {
        $id = (int)$id;
        if ($id <= 0) {
            return null;
        }

        $db = self::getDB();
        if (!$db) {
            return null;
        }

        try {
            $stmt = $db->prepare("SELECT u.*, CONCAT(TRIM(u.first_name), ' ', TRIM(u.last_name)) AS name 
                                   FROM users u 
                                   WHERE u.id = ? AND u.role = 'customer' 
                                   LIMIT 1");
            $stmt->execute([$id]);
            $customer = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$customer) {
                return null;
            }

            // Joined date formatting
            $customer['joined'] = !empty($customer['created_at']) 
                ? date('F j, Y', strtotime($customer['created_at'])) 
                : 'Recent Member';

            // DOB and age
            if (!empty($customer['birth_date']) && $customer['birth_date'] !== '0000-00-00') {
                $dob = new \DateTime($customer['birth_date']);
                $now = new \DateTime();
                $age = $now->diff($dob)->y;
                $customer['dob'] = date('F j, Y', strtotime($customer['birth_date'])) . " (Age: {$age})";
                $customer['age'] = $age;
            } else {
                $customer['dob'] = !empty($customer['age_verified']) ? '21+ Confirmed' : 'Not recorded';
                $customer['age'] = !empty($customer['age_verified']) ? 21 : null;
            }

            $customer['verify_method'] = 'AgeChecker.Net (Instant Database Match)';

            // Fetch addresses
            $addrStmt = $db->prepare("SELECT * FROM user_addresses WHERE user_id = ? ORDER BY is_default DESC, id ASC");
            $addrStmt->execute([(int)$customer['id']]);
            $addresses = $addrStmt->fetchAll(\PDO::FETCH_ASSOC);

            $customer['shipping'] = null;
            $customer['billing'] = null;

            foreach ($addresses as $addr) {
                if ($addr['address_type'] === 'shipping' && !$customer['shipping']) {
                    $customer['shipping'] = $addr;
                } elseif ($addr['address_type'] === 'billing' && !$customer['billing']) {
                    $customer['billing'] = $addr;
                }
            }

            if ($customer['shipping']) {
                $customer['shipping']['name'] = trim(($customer['shipping']['first_name'] ?? '') . ' ' . ($customer['shipping']['last_name'] ?? '')) ?: $customer['name'];
            }

            if ($customer['billing']) {
                $customer['billing']['name'] = trim(($customer['billing']['first_name'] ?? '') . ' ' . ($customer['billing']['last_name'] ?? '')) ?: $customer['name'];
            }

            // Fetch order history
            $ordStmt = $db->prepare("SELECT o.*, 
                                            COUNT(oi.id) as items_count 
                                     FROM orders o 
                                     LEFT JOIN order_items oi ON o.id = oi.order_id 
                                     WHERE o.user_id = ? 
                                     GROUP BY o.id 
                                     ORDER BY o.created_at DESC");
            $ordStmt->execute([(int)$customer['id']]);
            $orders = $ordStmt->fetchAll(\PDO::FETCH_ASSOC);

            $customer['orders'] = [];
            $totalSpentCalculated = 0.00;

            foreach ($orders as $ord) {
                $statusClass = 'badge-completed';
                $sStatus = strtolower($ord['shipping_status'] ?? '');
                $pStatus = strtolower($ord['payment_status'] ?? '');

                if ($sStatus === 'delivered' || $pStatus === 'paid') {
                    $statusClass = 'badge-completed';
                } elseif (in_array($sStatus, ['pending', 'in_transit', 'out_for_delivery'])) {
                    $statusClass = 'badge-processing';
                } elseif ($sStatus === 'cancelled' || $pStatus === 'failed') {
                    $statusClass = 'badge-failed';
                }

                $itemsCount = (int)$ord['items_count'];
                $itemsText = $itemsCount === 1 ? '1 item' : ($itemsCount > 0 ? "{$itemsCount} items" : '1 item');

                $customer['orders'][] = [
                    'id' => !empty($ord['order_number']) ? $ord['order_number'] : $ord['id'],
                    'db_id' => $ord['id'],
                    'date' => date('M d, Y', strtotime($ord['created_at'])),
                    'items' => $itemsText,
                    'total' => '$' . number_format((float)$ord['total_amount'], 2),
                    'total_num' => (float)$ord['total_amount'],
                    'status' => ucfirst($ord['shipping_status'] ?: ($ord['payment_status'] ?: 'Processing')),
                    'status_class' => $statusClass
                ];

                $totalSpentCalculated += (float)$ord['total_amount'];
            }

            // If user has orders recorded, use that count, else use total_orders column
            $ordersCount = count($customer['orders']);
            if ($ordersCount === 0 && (int)$customer['total_orders'] > 0) {
                $ordersCount = (int)$customer['total_orders'];
            }
            $customer['orders_count'] = $ordersCount;

            $spentVal = (float)$customer['total_spent'] > 0 ? (float)$customer['total_spent'] : $totalSpentCalculated;
            $customer['total_spent'] = '$' . number_format($spentVal, 2);
            $customer['total_spent_val'] = $spentVal;

            // Avg order value
            $avgVal = $ordersCount > 0 ? ($spentVal / $ordersCount) : 0.00;
            $customer['avg_order'] = '$' . number_format($avgVal, 2);

            // Fetch frequent purchased items
            $favStmt = $db->prepare("SELECT oi.product_id, 
                                            oi.product_title, 
                                            oi.variant_name, 
                                            SUM(oi.quantity) AS total_qty, 
                                            MAX(o.created_at) AS last_purchased,
                                            p.featured_image
                                     FROM order_items oi
                                     JOIN orders o ON oi.order_id = o.id
                                     LEFT JOIN products p ON oi.product_id = p.id
                                     WHERE o.user_id = ?
                                     GROUP BY oi.product_id, oi.variant_name
                                     ORDER BY total_qty DESC
                                     LIMIT 5");
            $favStmt->execute([(int)$customer['id']]);
            $customer['frequent_items'] = $favStmt->fetchAll(\PDO::FETCH_ASSOC);

            return $customer;
        } catch (\Exception $e) {
            error_log("Customer::find error: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Update customer admin internal note
     */
    public static function updateNotes($id, $notes)
    {
        $db = self::getDB();
        if (!$db) return false;

        try {
            $stmt = $db->prepare("UPDATE users SET admin_notes = ?, updated_at = NOW() WHERE id = ?");
            return $stmt->execute([$notes, (int)$id]);
        } catch (\Exception $e) {
            error_log("Customer::updateNotes error: " . $e->getMessage());
            return false;
        }
    }
}
