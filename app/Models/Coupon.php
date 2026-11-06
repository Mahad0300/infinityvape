<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Coupon extends Model
{
    /**
     * Get all coupons with optional filtering
     */
    public static function all($search = '', $type = '', $status = '')
    {
        $db = self::getDB();
        if (!$db) return [];

        try {
            $sql = "SELECT * FROM coupons WHERE 1=1";
            $params = [];

            if (!empty($search)) {
                $sql .= " AND UPPER(code) LIKE :search";
                $params[':search'] = '%' . strtoupper(trim($search)) . '%';
            }

            if (!empty($type) && in_array($type, ['percentage', 'fixed_amount', 'free_shipping'])) {
                $sql .= " AND type = :type";
                $params[':type'] = $type;
            }

            if ($status === 'active') {
                $sql .= " AND is_active = 1 AND (end_date IS NULL OR end_date >= NOW()) AND (max_uses IS NULL OR uses_count < max_uses)";
            } elseif ($status === 'inactive') {
                $sql .= " AND is_active = 0";
            } elseif ($status === 'expired') {
                $sql .= " AND (end_date < NOW() OR (max_uses IS NOT NULL AND uses_count >= max_uses))";
            }

            $sql .= " ORDER BY id DESC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map([self::class, 'formatRow'], $results);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get summary metrics for admin dashboard cards
     */
    public static function metrics()
    {
        $db = self::getDB();
        if (!$db) {
            return [
                'total'       => 0,
                'active'      => 0,
                'total_uses'  => 0,
                'top_code'    => 'N/A'
            ];
        }

        try {
            $stmt = $db->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN is_active = 1 AND (end_date IS NULL OR end_date >= NOW()) AND (max_uses IS NULL OR uses_count < max_uses) THEN 1 ELSE 0 END) as active,
                    COALESCE(SUM(uses_count), 0) as total_uses
                FROM coupons
            ");
            $metrics = $stmt->fetch(PDO::FETCH_ASSOC);

            $topStmt = $db->query("SELECT code FROM coupons ORDER BY uses_count DESC LIMIT 1");
            $topCode = $topStmt->fetchColumn() ?: 'None';

            return [
                'total'      => (int)($metrics['total'] ?? 0),
                'active'     => (int)($metrics['active'] ?? 0),
                'total_uses' => (int)($metrics['total_uses'] ?? 0),
                'top_code'   => $topCode
            ];
        } catch (\Exception $e) {
            return [
                'total'      => 0,
                'active'     => 0,
                'total_uses' => 0,
                'top_code'   => 'N/A'
            ];
        }
    }

    /**
     * Find coupon by ID
     */
    public static function find($id)
    {
        $db = self::getDB();
        if (!$db) return null;

        try {
            $stmt = $db->prepare("SELECT * FROM coupons WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => (int)$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? self::formatRow($row) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Find coupon by uppercase code
     */
    public static function findByCode($code)
    {
        $db = self::getDB();
        if (!$db) return null;

        try {
            $stmt = $db->prepare("SELECT * FROM coupons WHERE UPPER(code) = :code LIMIT 1");
            $stmt->execute([':code' => strtoupper(trim($code))]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? self::formatRow($row) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Insert new coupon
     */
    public static function create($data)
    {
        $db = self::getDB();
        if (!$db) return false;

        $stmt = $db->prepare("
            INSERT INTO coupons (
                code, type, value, min_order_amount, max_uses, uses_count, 
                start_date, end_date, is_active
            ) VALUES (
                :code, :type, :val, :min_order, :max_uses, 0, 
                :start_date, :end_date, :is_active
            )
        ");

        return $stmt->execute([
            ':code'        => strtoupper(trim($data['code'])),
            ':type'        => $data['type'],
            ':val'         => (float)$data['value'],
            ':min_order'   => !empty($data['min_order_amount']) ? (float)$data['min_order_amount'] : 0.00,
            ':max_uses'    => !empty($data['max_uses']) ? (int)$data['max_uses'] : null,
            ':start_date'  => !empty($data['start_date']) ? $data['start_date'] : null,
            ':end_date'    => !empty($data['end_date']) ? $data['end_date'] : null,
            ':is_active'   => isset($data['is_active']) ? (int)$data['is_active'] : 1
        ]);
    }

    /**
     * Update existing coupon
     */
    public static function updateCoupon($id, $data)
    {
        $db = self::getDB();
        if (!$db) return false;

        $stmt = $db->prepare("
            UPDATE coupons SET 
                code = :code,
                type = :type,
                value = :val,
                min_order_amount = :min_order,
                max_uses = :max_uses,
                start_date = :start_date,
                end_date = :end_date,
                is_active = :is_active
            WHERE id = :id
        ");

        return $stmt->execute([
            ':code'        => strtoupper(trim($data['code'])),
            ':type'        => $data['type'],
            ':val'         => (float)$data['value'],
            ':min_order'   => !empty($data['min_order_amount']) ? (float)$data['min_order_amount'] : 0.00,
            ':max_uses'    => !empty($data['max_uses']) ? (int)$data['max_uses'] : null,
            ':start_date'  => !empty($data['start_date']) ? $data['start_date'] : null,
            ':end_date'    => !empty($data['end_date']) ? $data['end_date'] : null,
            ':is_active'   => isset($data['is_active']) ? (int)$data['is_active'] : 1,
            ':id'          => (int)$id
        ]);
    }

    /**
     * Toggle active status
     */
    public static function toggle($id)
    {
        $db = self::getDB();
        if (!$db) return false;

        $stmt = $db->prepare("UPDATE coupons SET is_active = IF(is_active = 1, 0, 1) WHERE id = :id");
        return $stmt->execute([':id' => (int)$id]);
    }

    /**
     * Delete coupon
     */
    public static function deleteCoupon($id)
    {
        $db = self::getDB();
        if (!$db) return false;

        $stmt = $db->prepare("DELETE FROM coupons WHERE id = :id");
        return $stmt->execute([':id' => (int)$id]);
    }

    /**
     * Format row with human labels and computed states
     */
    private static function formatRow($row)
    {
        $type = $row['type'];
        $val = (float)$row['value'];
        $min = (float)($row['min_order_amount'] ?? 0);
        $maxUses = !empty($row['max_uses']) ? (int)$row['max_uses'] : null;
        $uses = (int)($row['uses_count'] ?? 0);
        $isActive = (int)($row['is_active'] ?? 1) === 1;

        $now = time();
        $isExpired = false;
        if (!empty($row['end_date']) && strtotime($row['end_date']) < $now) {
            $isExpired = true;
        }

        $isLimitReached = ($maxUses !== null && $uses >= $maxUses);

        // Determine badge status
        if (!$isActive) {
            $statusLabel = 'Inactive';
            $badgeClass = 'badge-cancelled';
        } elseif ($isExpired) {
            $statusLabel = 'Expired';
            $badgeClass = 'badge-cancelled';
        } elseif ($isLimitReached) {
            $statusLabel = 'Limit Reached';
            $badgeClass = 'badge-pending';
        } else {
            $statusLabel = 'Active';
            $badgeClass = 'badge-completed';
        }

        // Discount label
        if ($type === 'percentage') {
            $discountLabel = number_format($val, 0) . '% OFF';
            $typeLabel = 'Percentage';
        } elseif ($type === 'fixed_amount') {
            $discountLabel = '$' . number_format($val, 2) . ' OFF';
            $typeLabel = 'Fixed Amount';
        } else {
            $discountLabel = 'Free Shipping';
            $typeLabel = 'Free Shipping';
        }

        $row['discount_label']  = $discountLabel;
        $row['type_label']      = $typeLabel;
        $row['status_label']    = $statusLabel;
        $row['badge_class']     = $badgeClass;
        $row['min_order_label'] = $min > 0 ? '$' . number_format($min, 2) : 'No Minimum';
        $row['usage_label']     = $maxUses !== null ? "{$uses} / {$maxUses}" : "{$uses} (Unlimited)";
        $row['usage_percent']   = $maxUses ? min(100, round(($uses / $maxUses) * 100)) : 0;
        
        $row['validity_label']  = 'No Expiration';
        if (!empty($row['start_date']) && !empty($row['end_date'])) {
            $row['validity_label'] = date('M d, Y', strtotime($row['start_date'])) . ' - ' . date('M d, Y', strtotime($row['end_date']));
        } elseif (!empty($row['end_date'])) {
            $row['validity_label'] = 'Expires ' . date('M d, Y', strtotime($row['end_date']));
        } elseif (!empty($row['start_date'])) {
            $row['validity_label'] = 'Starts ' . date('M d, Y', strtotime($row['start_date']));
        }

        return $row;
    }
}

