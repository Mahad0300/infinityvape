<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Review extends Model
{
    /**
     * Get all reviews with optional filters (status, product_id, search)
     */
    public static function all(array $filters = [])
    {
        $db = self::getDB();
        if (!$db) return [];

        try {
            $sql = "SELECT pr.*, 
                           p.title AS product_title, 
                           p.slug AS product_slug,
                           COALESCE(NULLIF(p.featured_image, ''), 'assets/images/vape-image.webp') AS product_image,
                           b.name AS brand_name
                    FROM product_reviews pr
                    LEFT JOIN products p ON pr.product_id = p.id
                    LEFT JOIN brands b ON p.brand_id = b.id
                    WHERE 1=1 ";
            
            $params = [];

            // Filter by Status
            if (!empty($filters['status']) && $filters['status'] !== 'all') {
                $sql .= " AND pr.status = ? ";
                $params[] = $filters['status'];
            }

            // Filter by Product ID
            if (!empty($filters['product_id']) && $filters['product_id'] !== 'all') {
                $sql .= " AND pr.product_id = ? ";
                $params[] = (int)$filters['product_id'];
            }

            // Search query (customer, title, comment, product title)
            if (!empty($filters['search'])) {
                $search = '%' . trim($filters['search']) . '%';
                $sql .= " AND (pr.customer_name LIKE ? OR pr.title LIKE ? OR pr.comment LIKE ? OR p.title LIKE ?) ";
                $params[] = $search;
                $params[] = $search;
                $params[] = $search;
                $params[] = $search;
            }

            $sql .= " ORDER BY pr.id DESC ";

            if (!empty($filters['limit'])) {
                $sql .= " LIMIT " . (int)$filters['limit'];
                if (!empty($filters['offset'])) {
                    $sql .= " OFFSET " . (int)$filters['offset'];
                }
            }

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Review::all() error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Find single review by ID with product details
     */
    public static function find($id)
    {
        $db = self::getDB();
        if (!$db) return null;

        try {
            $stmt = $db->prepare("SELECT pr.*, 
                                         p.title AS product_title, 
                                         p.slug AS product_slug,
                                         COALESCE(NULLIF(p.featured_image, ''), 'assets/images/vape-image.webp') AS product_image,
                                         b.name AS brand_name
                                  FROM product_reviews pr
                                  LEFT JOIN products p ON pr.product_id = p.id
                                  LEFT JOIN brands b ON p.brand_id = b.id
                                  WHERE pr.id = ? 
                                  LIMIT 1");
            $stmt->execute([(int)$id]);
            $review = $stmt->fetch(PDO::FETCH_ASSOC);
            return $review ?: null;
        } catch (\Exception $e) {
            error_log("Review::find() error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a new review
     */
    public static function create(array $data)
    {
        $db = self::getDB();
        if (!$db) return false;

        $productId    = (int)($data['product_id'] ?? 0);
        $customerName = trim($data['customer_name'] ?? ($data['nickname'] ?? 'Verified Customer'));
        $rating       = max(1, min(5, (int)($data['rating'] ?? 5)));
        $title        = trim($data['title'] ?? 'Great Product');
        $comment      = trim($data['comment'] ?? ($data['body'] ?? ''));
        $status       = in_array($data['status'] ?? '', ['pending', 'approved', 'rejected']) ? $data['status'] : 'pending';
        $userId       = !empty($data['user_id']) ? (int)$data['user_id'] : null;

        if (empty($productId) || empty($comment)) {
            return false;
        }

        try {
            $stmt = $db->prepare("INSERT INTO product_reviews 
                                  (product_id, user_id, customer_name, rating, title, comment, status, created_at, updated_at) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([
                $productId,
                $userId,
                $customerName,
                $rating,
                $title,
                $comment,
                $status
            ]);

            $newId = (int)$db->lastInsertId();

            // Recalculate product rating & reviews cache
            self::recalculateProductRating($productId);

            return $newId;
        } catch (\Exception $e) {
            error_log("Review::create() error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update an existing review
     */
    public static function update($id, array $data)
    {
        $db = self::getDB();
        if (!$db) return false;

        $review = self::find($id);
        if (!$review) return false;

        $customerName = trim($data['customer_name'] ?? $review['customer_name']);
        $rating       = isset($data['rating']) ? max(1, min(5, (int)$data['rating'])) : (int)$review['rating'];
        $title        = trim($data['title'] ?? $review['title']);
        $comment      = trim($data['comment'] ?? $review['comment']);
        $status       = in_array($data['status'] ?? '', ['pending', 'approved', 'rejected']) ? $data['status'] : $review['status'];

        try {
            $stmt = $db->prepare("UPDATE product_reviews 
                                  SET customer_name = ?, rating = ?, title = ?, comment = ?, status = ?, updated_at = NOW() 
                                  WHERE id = ?");
            $stmt->execute([
                $customerName,
                $rating,
                $title,
                $comment,
                $status,
                (int)$id
            ]);

            // Recalculate product rating & reviews cache
            self::recalculateProductRating($review['product_id']);

            return true;
        } catch (\Exception $e) {
            error_log("Review::update() error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Quick status update
     */
    public static function updateStatus($id, $status)
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return false;
        }

        $review = self::find($id);
        if (!$review) return false;

        $db = self::getDB();
        if (!$db) return false;

        try {
            $stmt = $db->prepare("UPDATE product_reviews SET status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$status, (int)$id]);

            self::recalculateProductRating($review['product_id']);
            return true;
        } catch (\Exception $e) {
            error_log("Review::updateStatus() error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete review
     */
    public static function delete($id)
    {
        $review = self::find($id);
        if (!$review) return false;

        $db = self::getDB();
        if (!$db) return false;

        try {
            $stmt = $db->prepare("DELETE FROM product_reviews WHERE id = ?");
            $stmt->execute([(int)$id]);

            self::recalculateProductRating($review['product_id']);
            return true;
        } catch (\Exception $e) {
            error_log("Review::delete() error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Recalculate and synchronize rating_avg and reviews_count in products table
     */
    public static function recalculateProductRating($productId)
    {
        $db = self::getDB();
        if (!$db || empty($productId)) return;

        try {
            $stmt = $db->prepare("SELECT COUNT(*) AS total_reviews, COALESCE(AVG(rating), 5.0) AS avg_rating 
                                  FROM product_reviews 
                                  WHERE product_id = ? AND status = 'approved'");
            $stmt->execute([(int)$productId]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);

            $count = (int)($stats['total_reviews'] ?? 0);
            $avg   = round((float)($stats['avg_rating'] ?? 5.0), 2);

            $upd = $db->prepare("UPDATE products SET rating_avg = ?, reviews_count = ? WHERE id = ?");
            $upd->execute([$avg, $count, (int)$productId]);
        } catch (\Exception $e) {
            error_log("Review::recalculateProductRating() error: " . $e->getMessage());
        }
    }

    /**
     * Overall review statistics for admin dashboard
     */
    public static function getStats()
    {
        $db = self::getDB();
        if (!$db) {
            return ['total' => 0, 'approved' => 0, 'pending' => 0, 'rejected' => 0, 'avg_rating' => '5.0'];
        }

        try {
            $totalStmt = $db->query("SELECT COUNT(*) FROM product_reviews");
            $total = (int)$totalStmt->fetchColumn();

            $apprStmt = $db->query("SELECT COUNT(*) FROM product_reviews WHERE status = 'approved'");
            $approved = (int)$apprStmt->fetchColumn();

            $pendStmt = $db->query("SELECT COUNT(*) FROM product_reviews WHERE status = 'pending'");
            $pending = (int)$pendStmt->fetchColumn();

            $rejStmt = $db->query("SELECT COUNT(*) FROM product_reviews WHERE status = 'rejected'");
            $rejected = (int)$rejStmt->fetchColumn();

            $avgStmt = $db->query("SELECT COALESCE(AVG(rating), 5.0) FROM product_reviews WHERE status = 'approved'");
            $avg = round((float)$avgStmt->fetchColumn(), 1);

            return [
                'total'      => $total,
                'approved'   => $approved,
                'pending'    => $pending,
                'rejected'   => $rejected,
                'avg_rating' => number_format($avg, 1)
            ];
        } catch (\Exception $e) {
            return ['total' => 0, 'approved' => 0, 'pending' => 0, 'rejected' => 0, 'avg_rating' => '5.0'];
        }
    }
}
