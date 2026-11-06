<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Product extends Model
{
    /**
     * Safely clear and drop legacy dummy badge columns from database and ensure schema
     */
    public static function cleanUnwantedBadges()
    {
        $db = self::getDB();
        if ($db) {
            try {
                $check = $db->query("SHOW COLUMNS FROM products LIKE 'badge_text'");
                if ($check && $check->fetch()) {
                    $db->exec("ALTER TABLE products DROP COLUMN badge_text, DROP COLUMN badge_type");
                }
            } catch (\Exception $e) {
                try {
                    $db->exec("UPDATE products SET badge_text = NULL, badge_type = NULL WHERE badge_text IS NOT NULL OR badge_type IS NOT NULL");
                } catch (\Exception $e2) {}
            }

            // Ensure related_posts_content column exists in products table
            try {
                $checkCol = $db->query("SHOW COLUMNS FROM products LIKE 'related_posts_content'");
                if ($checkCol && !$checkCol->fetch()) {
                    $db->exec("ALTER TABLE products ADD COLUMN related_posts_content LONGTEXT NULL AFTER long_desc");
                }
            } catch (\Exception $e) {}

            // Clean up any old dummy test FAQs
            try {
                $db->exec("DELETE FROM product_faqs WHERE question LIKE '%deliver up to%' OR answer LIKE '%delivered by%' OR answer LIKE '%scratch-off%'");
            } catch (\Exception $e) {}

            // Automatically sync stock_quantity and stock_status for products with variants
            try {
                $db->exec("UPDATE products p
                           JOIN (
                               SELECT product_id, SUM(stock_quantity) AS total_variant_stock
                               FROM product_variants
                               GROUP BY product_id
                           ) pv ON p.id = pv.product_id
                           SET p.stock_quantity = pv.total_variant_stock,
                               p.stock_status = CASE 
                                   WHEN pv.total_variant_stock <= 0 THEN 'Out of Stock'
                                   WHEN pv.total_variant_stock < 15 THEN 'Low Stock'
                                   ELSE 'In Stock'
                               END");
            } catch (\Exception $e) {}

            // Automatically sync reviews_count and rating_avg with approved product_reviews
            try {
                $db->exec("UPDATE products p
                           LEFT JOIN (
                               SELECT product_id, COUNT(*) AS approved_count, AVG(rating) AS avg_rating
                               FROM product_reviews
                               WHERE status = 'approved'
                               GROUP BY product_id
                           ) pr ON p.id = pr.product_id
                           SET p.reviews_count = COALESCE(pr.approved_count, 0),
                               p.rating_avg = COALESCE(ROUND(pr.avg_rating, 2), 0.00)");
            } catch (\Exception $e) {}
        }
    }

    /**
     * Get all products with dynamic Brand & Collection taxonomy
     */
    public static function all()
    {
        self::cleanUnwantedBadges();
        $db = self::getDB();
        if ($db) {
            try {
                $sql = "SELECT p.*, 
                               b.name AS brand,
                               COALESCE(c.name, 'Disposable') AS category,
                               c.id AS collection_id,
                               COALESCE(NULLIF(NULLIF(p.featured_image, ''), 'assets/images/vape-image.webp'), 'assets/images/no-image.svg') AS img,
                               (SELECT COUNT(*) FROM product_variants pv WHERE pv.product_id = p.id) AS variants_count,
                               COALESCE((SELECT SUM(pv.stock_quantity) FROM product_variants pv WHERE pv.product_id = p.id), p.stock_quantity, 0) AS calculated_stock
                        FROM products p 
                        LEFT JOIN brands b ON p.brand_id = b.id
                        LEFT JOIN product_collections pc ON p.id = pc.product_id
                        LEFT JOIN collections c ON pc.collection_id = c.id
                        GROUP BY p.id
                        ORDER BY p.id DESC";
                $stmt = $db->query($sql);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($results)) {
                    foreach ($results as &$r) {
                        if ((int)($r['variants_count'] ?? 0) > 0) {
                            $r['stock_quantity'] = (int)$r['calculated_stock'];
                            $r['stock_status'] = ($r['stock_quantity'] <= 0) ? 'Out of Stock' : (($r['stock_quantity'] < 15) ? 'Low Stock' : 'In Stock');
                        }
                    }
                    unset($r);
                    return $results;
                }
            } catch (\Exception $e) {
                error_log("Product::all() error: " . $e->getMessage());
            }
        }
        return [];
    }

    /**
     * Find single product by ID with Brand & Collection relations
     */
    public static function find($id)
    {
        self::cleanUnwantedBadges();
        $db = self::getDB();
        if ($db) {
            try {
                $isNumeric = is_numeric($id);
                $sql = "SELECT p.*, 
                               b.name AS brand,
                               b.slug AS brand_slug,
                               COALESCE(c.name, 'Disposables') AS category,
                               c.slug AS category_slug,
                               c.id AS collection_id,
                               c.parent_id AS collection_parent_id,
                               CASE 
                                   WHEN c.parent_id IS NOT NULL AND c.parent_id > 0 AND p_col.id IS NOT NULL THEN p_col.name
                                   ELSE COALESCE(c.name, 'Disposables')
                               END AS parent_category_name,
                               CASE 
                                   WHEN c.parent_id IS NOT NULL AND c.parent_id > 0 AND p_col.id IS NOT NULL THEN p_col.slug
                                   ELSE COALESCE(c.slug, 'disposables')
                               END AS parent_category_slug,
                               COALESCE(NULLIF(NULLIF(p.featured_image, ''), 'assets/images/vape-image.webp'), 'assets/images/no-image.svg') AS img,
                               (SELECT COUNT(*) FROM product_variants pv WHERE pv.product_id = p.id) AS variants_count,
                               COALESCE((SELECT SUM(pv.stock_quantity) FROM product_variants pv WHERE pv.product_id = p.id), p.stock_quantity, 0) AS calculated_stock
                        FROM products p 
                        LEFT JOIN brands b ON p.brand_id = b.id 
                        LEFT JOIN product_collections pc ON p.id = pc.product_id
                        LEFT JOIN collections c ON pc.collection_id = c.id
                        LEFT JOIN collections p_col ON c.parent_id = p_col.id
                        WHERE " . ($isNumeric ? "p.id = ?" : "(p.slug = ? OR p.custom_url = ?)") . "
                        LIMIT 1";
                $stmt = $db->prepare($sql);
                if ($isNumeric) {
                    $stmt->execute([(int)$id]);
                } else {
                    $stmt->execute([(string)$id, (string)$id]);
                }
                $item = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($item) {
                    if ((int)($item['variants_count'] ?? 0) > 0) {
                        $item['stock_quantity'] = (int)$item['calculated_stock'];
                        $item['stock_status'] = ($item['stock_quantity'] <= 0) ? 'Out of Stock' : (($item['stock_quantity'] < 15) ? 'Low Stock' : 'In Stock');
                    }
                    return $item;
                }
            } catch (\Exception $e) {
                error_log("Product::find() error: " . $e->getMessage());
            }
        }
        return null;
    }

    /**
     * Get New Arrivals for storefront
     */
    public static function newArrivals($limit = 10)
    {
        self::cleanUnwantedBadges();
        $db = self::getDB();
        if ($db) {
            try {
                $sql = "SELECT p.*, 
                               b.name AS brand,
                               COALESCE(c.name, 'Disposable') AS category,
                               c.id AS collection_id,
                               COALESCE(NULLIF(p.featured_image, ''), 'assets/images/no-image.svg') AS img
                        FROM products p 
                        LEFT JOIN brands b ON p.brand_id = b.id
                        LEFT JOIN product_collections pc ON p.id = pc.product_id
                        LEFT JOIN collections c ON pc.collection_id = c.id
                        WHERE p.status = 'published' AND (p.is_new = 1 OR pc.collection_id IN (8, 19))
                        GROUP BY p.id
                        ORDER BY p.id DESC
                        LIMIT " . (int)$limit;
                $stmt = $db->query($sql);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($results)) return $results;
            } catch (\Exception $e) {
                error_log("Product::newArrivals() error: " . $e->getMessage());
            }
        }
        return [];
    }

    /**
     * Get Best Selling products for storefront
     */
    public static function bestSellers($limit = 10)
    {
        $db = self::getDB();
        if ($db) {
            try {
                $sql = "SELECT p.*, 
                               b.name AS brand,
                               COALESCE(c.name, 'Disposable') AS category,
                               c.id AS collection_id,
                               COALESCE(NULLIF(p.featured_image, ''), 'assets/images/no-image.svg') AS img
                        FROM products p 
                        LEFT JOIN brands b ON p.brand_id = b.id
                        LEFT JOIN product_collections pc ON p.id = pc.product_id
                        LEFT JOIN collections c ON pc.collection_id = c.id
                        WHERE p.status = 'published' AND (p.is_bestseller = 1 OR pc.collection_id IN (9, 20))
                        GROUP BY p.id
                        ORDER BY p.reviews_count DESC, p.id DESC
                        LIMIT " . (int)$limit;
                $stmt = $db->query($sql);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($results)) return $results;
            } catch (\Exception $e) {
                error_log("Product::bestSellers() error: " . $e->getMessage());
            }
        }
        return [];
    }

    /**
     * Get products by collection/category slug or ID
     */
    public static function byCollection($slugOrId, $limit = 16)
    {
        $db = self::getDB();
        if ($db) {
            try {
                $isNumeric = is_numeric($slugOrId);
                $sql = "SELECT p.*, 
                               b.name AS brand,
                               COALESCE(c.name, 'Disposable') AS category,
                               c.id AS collection_id,
                               COALESCE(NULLIF(p.featured_image, ''), 'assets/images/no-image.svg') AS img
                        FROM products p 
                        LEFT JOIN brands b ON p.brand_id = b.id
                        JOIN product_collections pc ON p.id = pc.product_id
                        JOIN collections c ON pc.collection_id = c.id
                        WHERE p.status = 'published' AND (" . ($isNumeric ? "c.id = ?" : "c.slug = ?") . ")
                        GROUP BY p.id
                        ORDER BY p.id DESC
                        LIMIT " . (int)$limit;
                $stmt = $db->prepare($sql);
                $stmt->execute([$slugOrId]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($results)) return $results;
            } catch (\Exception $e) {
                error_log("Product::byCollection() error: " . $e->getMessage());
            }
        }
        return [];
    }

    /**
     * Get products for a collection with comprehensive filtering & sorting
     */
    public static function getCollectionProducts($slugOrId = null, array $filters = [])
    {
        self::cleanUnwantedBadges();
        $db = self::getDB();
        if (!$db) return [];

        try {
            $params = [];
            $where = ["p.status = 'published'"];

            if (!empty($slugOrId) && $slugOrId !== 'all' && $slugOrId !== 'shop') {
                if (is_numeric($slugOrId)) {
                    $where[] = "pc.collection_id = ?";
                    $params[] = (int)$slugOrId;
                } else {
                    $where[] = "(c.slug = ? OR pc.collection_id = (SELECT id FROM collections WHERE slug = ? LIMIT 1))";
                    $params[] = $slugOrId;
                    $params[] = $slugOrId;
                }
            }

            // Brand filter (single or array)
            if (!empty($filters['brand'])) {
                $brand = $filters['brand'];
                if (is_array($brand)) {
                    $placeholders = implode(',', array_fill(0, count($brand), '?'));
                    $where[] = "(b.slug IN ($placeholders) OR b.name IN ($placeholders))";
                    $params = array_merge($params, $brand, $brand);
                } else {
                    $where[] = "(b.slug = ? OR b.name = ?)";
                    $params[] = $brand;
                    $params[] = $brand;
                }
            }

            // Min & Max Price
            if (isset($filters['min_price']) && is_numeric($filters['min_price'])) {
                $where[] = "p.base_price >= ?";
                $params[] = (float)$filters['min_price'];
            }
            if (isset($filters['max_price']) && is_numeric($filters['max_price'])) {
                $where[] = "p.base_price <= ?";
                $params[] = (float)$filters['max_price'];
            }

            // In Stock filter
            if (!empty($filters['in_stock'])) {
                $where[] = "p.stock_quantity > 0 AND p.stock_status != 'Out of Stock'";
            }

            // Search query
            if (!empty($filters['q'])) {
                $where[] = "(p.title LIKE ? OR p.name LIKE ? OR p.short_desc LIKE ? OR b.name LIKE ?)";
                $kw = '%' . trim($filters['q']) . '%';
                $params[] = $kw;
                $params[] = $kw;
                $params[] = $kw;
                $params[] = $kw;
            }

            // Sorting
            $orderBy = "p.id DESC";
            if (!empty($filters['sort'])) {
                switch ($filters['sort']) {
                    case 'price-asc':
                        $orderBy = "p.base_price ASC";
                        break;
                    case 'price-desc':
                        $orderBy = "p.base_price DESC";
                        break;
                    case 'name':
                    case 'name-asc':
                        $orderBy = "COALESCE(p.title, p.name) ASC";
                        break;
                    case 'newest':
                        $orderBy = "p.created_at DESC, p.id DESC";
                        break;
                    case 'bestseller':
                        $orderBy = "p.is_bestseller DESC, p.reviews_count DESC";
                        break;
                    case 'rating':
                        $orderBy = "p.rating_avg DESC, p.reviews_count DESC";
                        break;
                }
            }

            $whereClause = implode(" AND ", $where);
            $limitClause = "";
            if (!empty($filters['limit'])) {
                $limit = (int)$filters['limit'];
                $offset = !empty($filters['offset']) ? (int)$filters['offset'] : 0;
                $limitClause = "LIMIT $offset, $limit";
            }

            $sql = "SELECT p.*, 
                           COALESCE((SELECT COUNT(*) FROM product_reviews pr WHERE pr.product_id = p.id AND pr.status = 'approved'), 0) AS reviews_count,
                           COALESCE((SELECT ROUND(AVG(rating), 2) FROM product_reviews pr WHERE pr.product_id = p.id AND pr.status = 'approved'), 0.00) AS rating_avg,
                           b.name AS brand,
                           b.slug AS brand_slug,
                           COALESCE(c.name, 'Disposable') AS category,
                           c.id AS collection_id,
                           c.slug AS collection_slug,
                           COALESCE(NULLIF(NULLIF(p.featured_image, ''), 'assets/images/vape-image.webp'), 'assets/images/no-image.svg') AS img,
                           (SELECT spec_value FROM product_specifications WHERE product_id = p.id AND (spec_key = 'Puffs' OR spec_key = 'Puff Count') LIMIT 1) AS spec_puffs,
                           (SELECT spec_value FROM product_specifications WHERE product_id = p.id AND (spec_key = 'Nicotine' OR spec_key = 'Nicotine Strength') LIMIT 1) AS spec_nicotine,
                           (SELECT spec_value FROM product_specifications WHERE product_id = p.id AND (spec_key = 'Battery' OR spec_key = 'Battery Capacity') LIMIT 1) AS spec_battery
                    FROM products p
                    LEFT JOIN brands b ON p.brand_id = b.id
                    LEFT JOIN product_collections pc ON p.id = pc.product_id
                    LEFT JOIN collections c ON pc.collection_id = c.id
                    WHERE $whereClause
                    GROUP BY p.id
                    ORDER BY $orderBy
                    $limitClause";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Compute and attach detected puff counts
            foreach ($results as &$item) {
                $item['detected_puffs'] = self::extractPuffs($item['title'] ?: ($item['name'] ?? ''), $item['spec_puffs'] ?? null);
            }
            unset($item);

            return $results;
        } catch (\Exception $e) {
            error_log("Product::getCollectionProducts error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Extract puff count number from title or specifications
     */
    public static function extractPuffs($title, $spec = null)
    {
        if (!empty($spec)) {
            $clean = str_replace(',', '', $spec);
            if (preg_match('/(\d{4,6})/', $clean, $m)) {
                return (int)$m[1];
            }
        }
        if (preg_match('/(\d{1,3}(?:,\d{3})+)\s*puffs?/i', $title, $m)) {
            return (int)str_replace(',', '', $m[1]);
        }
        if (preg_match('/(?:MO|SW|N|CT|MT|RX|RYL)?\s*(\d{4,6})\b/i', $title, $m)) {
            return (int)$m[1];
        }
        if (preg_match('/(\d{1,3})\s*k\b/i', $title, $m)) {
            return (int)$m[1] * 1000;
        }
        return 0;
    }

    /**
     * Get brands with product counts for a collection (or all active products)
     */
    public static function getBrandsForCollection($slugOrId = null)
    {
        $db = self::getDB();
        if (!$db) return [];

        try {
            $params = [];
            $collectionJoin = "";
            $where = "p.status = 'published'";

            if (!empty($slugOrId) && $slugOrId !== 'all' && $slugOrId !== 'shop') {
                $collectionJoin = "JOIN product_collections pc ON p.id = pc.product_id 
                                   JOIN collections c ON pc.collection_id = c.id";
                if (is_numeric($slugOrId)) {
                    $where .= " AND pc.collection_id = ?";
                    $params[] = (int)$slugOrId;
                } else {
                    $where .= " AND (c.slug = ? OR pc.collection_id = (SELECT id FROM collections WHERE slug = ? LIMIT 1))";
                    $params[] = $slugOrId;
                    $params[] = $slugOrId;
                }
            }

            $sql = "SELECT b.id, b.name, b.slug, COUNT(DISTINCT p.id) AS products_count
                    FROM brands b
                    JOIN products p ON b.id = p.brand_id
                    $collectionJoin
                    WHERE $where
                    GROUP BY b.id
                    HAVING products_count > 0
                    ORDER BY products_count DESC, b.name ASC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Product::getBrandsForCollection error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get min and max price for products in a collection
     */
    public static function getPriceBounds($slugOrId = null)
    {
        $db = self::getDB();
        if (!$db) return ['min' => 0, 'max' => 100];

        try {
            $params = [];
            $collectionJoin = "";
            $where = "p.status = 'published'";

            if (!empty($slugOrId) && $slugOrId !== 'all' && $slugOrId !== 'shop') {
                $collectionJoin = "JOIN product_collections pc ON p.id = pc.product_id 
                                   JOIN collections c ON pc.collection_id = c.id";
                if (is_numeric($slugOrId)) {
                    $where .= " AND pc.collection_id = ?";
                    $params[] = (int)$slugOrId;
                } else {
                    $where .= " AND (c.slug = ? OR pc.collection_id = (SELECT id FROM collections WHERE slug = ? LIMIT 1))";
                    $params[] = $slugOrId;
                    $params[] = $slugOrId;
                }
            }

            $sql = "SELECT MIN(p.base_price) AS min_price, MAX(p.base_price) AS max_price
                    FROM products p
                    $collectionJoin
                    WHERE $where";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'min' => !empty($res['min_price']) ? floor($res['min_price']) : 0,
                'max' => !empty($res['max_price']) ? ceil($res['max_price']) : 100,
            ];
        } catch (\Exception $e) {
            error_log("Product::getPriceBounds error: " . $e->getMessage());
            return ['min' => 0, 'max' => 100];
        }
    }

    /**
     * Get Disposable Vapes for storefront
     */
    public static function disposables($limit = 16)
    {
        $results = self::byCollection('disposable', $limit);
        if (empty($results)) {
            $results = self::byCollection('disposable-vapes', $limit);
        }
        if (empty($results)) {
            $results = self::byCollection(16, $limit);
        }
        return $results;
    }

    /**
     * Get Vape Juices for storefront
     */
    public static function vapeJuices($limit = 4)
    {
        $results = self::byCollection('e-liquids', $limit);
        if (empty($results)) {
            $results = self::byCollection('eliquids', $limit);
        }
        if (empty($results)) {
            $results = self::byCollection(17, $limit);
        }
        return $results;
    }

    /**
     * Get Nicotine Pouches for storefront
     */
    public static function nicotinePouches($limit = 4)
    {
        $results = self::byCollection('nicotine-pouches', $limit);
        if (empty($results)) {
            $results = self::byCollection('pouches', $limit);
        }
        if (empty($results)) {
            $results = self::byCollection(18, $limit);
        }
        return $results;
    }

    /**
     * Get Hemp & THC products for storefront
     */
    public static function hempThc($limit = 4)
    {
        $results = self::byCollection('hemp-thc', $limit);
        if (empty($results)) {
            $results = self::byCollection(74, $limit); // Lifestyle fallback
        }
        if (empty($results)) {
            $results = self::byCollection('lifestyle', $limit);
        }
        return $results;
    }

    /**
     * Create a new product in the database
     */
    public static function create(array $data)
    {
        $db = self::getDB();
        if (!$db) return false;

        $title = trim($data['title'] ?? $data['name'] ?? '');
        if (empty($title)) return false;

        $name = $title;

        // Resolve Brand ID
        $brandId = !empty($data['brand_id']) ? (int)$data['brand_id'] : null;
        if (!$brandId && !empty($data['brand'])) {
            $brandName = trim($data['brand']);
            $bStmt = $db->prepare("SELECT id FROM brands WHERE LOWER(name) = ? OR LOWER(slug) = ? LIMIT 1");
            $bStmt->execute([strtolower($brandName), strtolower($brandName)]);
            $bRow = $bStmt->fetch(PDO::FETCH_ASSOC);
            if ($bRow) $brandId = (int)$bRow['id'];
        }

        // Generate or clean SKU
        $sku = trim($data['sku'] ?? '');
        if (empty($sku)) {
            $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $title), 0, 4) ?: 'IVS');
            $sku = $prefix . '-' . rand(1000, 9999) . '-' . strtoupper(bin2hex(random_bytes(2)));
        }

        // Generate or clean Slug
        $slug = trim($data['slug'] ?? '');
        if (empty($slug)) {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $title));
            $slug = trim($slug, '-');
        }

        // Unique slug check
        $baseSlug = $slug;
        $counter = 1;
        while (true) {
            $checkStmt = $db->prepare("SELECT id FROM products WHERE slug = ? LIMIT 1");
            $checkStmt->execute([$slug]);
            if (!$checkStmt->fetch()) break;
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $customUrl = $slug;
        $shortDesc = trim($data['short_desc'] ?? $data['summary'] ?? '');
        $longDesc = trim($data['long_desc'] ?? $data['description'] ?? '');
        $relatedPostsContent = !empty($data['related_posts_content']) ? trim($data['related_posts_content']) : null;
        $basePrice = (float)str_replace(['$', ','], '', $data['price'] ?? $data['base_price'] ?? 0);
        $comparePrice = !empty($data['compare_price']) ? (float)str_replace(['$', ','], '', $data['compare_price']) : null;
        $stockQuantity = isset($data['stock_quantity']) ? (int)$data['stock_quantity'] : (isset($data['stock']) ? (int)$data['stock'] : 0);
        
        $stockStatus = 'In Stock';
        if ($stockQuantity <= 0) {
            $stockStatus = 'Out of Stock';
        } elseif ($stockQuantity < 15) {
            $stockStatus = 'Low Stock';
        }

        $rawFeat = trim($data['featured_image'] ?? $data['image'] ?? $data['img'] ?? '');
        $featuredImage = (!empty($rawFeat) && $rawFeat !== 'assets/images/vape-image.webp' && $rawFeat !== 'assets/images/no-image.svg') ? $rawFeat : null;
        $status = in_array($data['status'] ?? '', ['published', 'draft', 'archived']) ? $data['status'] : ($data['status'] === 'active' ? 'published' : 'published');
        $tags = is_array($data['tags'] ?? null) ? implode(', ', $data['tags']) : trim($data['tags'] ?? '');
        $optionNames = array_key_exists('option_names', $data) ? (is_array($data['option_names']) ? json_encode($data['option_names']) : $data['option_names']) : '["Flavor"]';
        $seoTitle = !empty(trim($data['seo_title'] ?? '')) ? trim($data['seo_title']) : null;
        $seoDescription = !empty(trim($data['seo_description'] ?? '')) ? trim($data['seo_description']) : null;
        $bundleBannerActive = !empty($data['bundle_banner_active']) ? 1 : 0;
        $bundleBannerImg = !empty($data['bundle_banner_img']) ? trim($data['bundle_banner_img']) : null;
        $bundleBannerLink = !empty($data['bundle_banner_link']) ? trim($data['bundle_banner_link']) : null;

        $frequentlyBoughtTogether = null;
        if (!empty($data['frequently_bought_together'])) {
            if (is_array($data['frequently_bought_together'])) {
                $fbtClean = array_values(array_slice(array_unique(array_filter(array_map('intval', $data['frequently_bought_together']))), 0, 4));
                $frequentlyBoughtTogether = !empty($fbtClean) ? json_encode($fbtClean) : null;
            } elseif (is_string($data['frequently_bought_together'])) {
                $fbtDecoded = json_decode($data['frequently_bought_together'], true);
                if (is_array($fbtDecoded)) {
                    $fbtClean = array_values(array_slice(array_unique(array_filter(array_map('intval', $fbtDecoded))), 0, 4));
                    $frequentlyBoughtTogether = !empty($fbtClean) ? json_encode($fbtClean) : null;
                } else {
                    $frequentlyBoughtTogether = trim($data['frequently_bought_together']);
                }
            }
        }

        try {
            $sql = "INSERT INTO products 
                    (brand_id, name, title, sku, slug, custom_url, short_desc, long_desc, related_posts_content, base_price, compare_price, stock_quantity, stock_status, featured_image, status, tags, option_names, seo_title, seo_description, bundle_banner_active, bundle_banner_img, bundle_banner_link, frequently_bought_together)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $brandId,
                $name,
                $title,
                $sku,
                $slug,
                $customUrl,
                $shortDesc,
                $longDesc,
                $relatedPostsContent,
                $basePrice,
                $comparePrice,
                $stockQuantity,
                $stockStatus,
                $featuredImage,
                $status,
                $tags,
                $optionNames,
                $seoTitle,
                $seoDescription,
                $bundleBannerActive,
                $bundleBannerImg,
                $bundleBannerLink,
                $frequentlyBoughtTogether
            ]);

            $productId = (int)$db->lastInsertId();

            // Link Collection
            $colId = !empty($data['collection_id']) ? (int)$data['collection_id'] : null;
            if (!$colId && !empty($data['collection'])) {
                $colName = trim($data['collection']);
                $cStmt = $db->prepare("SELECT id FROM collections WHERE LOWER(name) = ? OR LOWER(slug) = ? LIMIT 1");
                $cStmt->execute([strtolower($colName), strtolower($colName)]);
                $cRow = $cStmt->fetch(PDO::FETCH_ASSOC);
                if ($cRow) $colId = (int)$cRow['id'];
            }

            if ($colId) {
                $pcStmt = $db->prepare("INSERT INTO product_collections (product_id, collection_id) VALUES (?, ?)");
                $pcStmt->execute([$productId, $colId]);
            }

            // Save Images to product_images table if provided
            $finalCreateImages = [];
            if (!empty($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $img) {
                    $img = trim($img);
                    if (!empty($img) && !in_array($img, $finalCreateImages)) {
                        $finalCreateImages[] = $img;
                    }
                }
            }
            if (!empty($data['existing_images']) && is_array($data['existing_images'])) {
                foreach ($data['existing_images'] as $img) {
                    $img = trim($img);
                    if (!empty($img) && !in_array($img, $finalCreateImages)) {
                        $finalCreateImages[] = $img;
                    }
                }
            }
            if (!empty($data['variants']) && is_array($data['variants'])) {
                foreach ($data['variants'] as $v) {
                    $vImg = is_array($v) ? trim($v['image_url'] ?? '') : '';
                    if (!empty($vImg) && !in_array($vImg, $finalCreateImages)) {
                        $finalCreateImages[] = $vImg;
                    }
                }
            }

            if (!empty($finalCreateImages)) {
                $imgStmt = $db->prepare("INSERT INTO product_images (product_id, image_url, alt_text, sort_order) VALUES (?, ?, ?, ?)");
                foreach ($finalCreateImages as $order => $imgUrl) {
                    $altText = $title . ' - ' . ($order === 0 ? 'Featured Image' : 'Gallery Image ' . ($order + 1));
                    $imgStmt->execute([$productId, $imgUrl, $altText, $order + 1]);
                }
            }

            // Save Variants if provided
            if (!empty($data['variants']) && is_array($data['variants'])) {
                $vStmt = $db->prepare("INSERT INTO product_variants (product_id, sku, variant_name, price, stock_quantity, is_default, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $seenSkus = [];
                $totalVariantStock = 0;
                foreach ($data['variants'] as $idx => $v) {
                    $vName = is_array($v) ? ($v['name'] ?? $v['variant_name'] ?? '') : (string)$v;
                    if (empty($vName)) continue;
                    $rawSku = trim(is_array($v) ? ($v['sku'] ?? '') : '');
                    $candidateSku = !empty($rawSku) ? $rawSku : ($sku . '-V' . ($idx + 1));

                    // Ensure SKU is unique within this product's variants batch
                    $vSku = $candidateSku;
                    $dupCount = 1;
                    while (in_array(strtoupper($vSku), $seenSkus)) {
                        $vSku = $candidateSku . '-' . ($dupCount++);
                    }

                    // Check if this SKU collides with another product in product_variants
                    $chk = $db->prepare("SELECT id FROM product_variants WHERE sku = ? AND product_id != ? LIMIT 1");
                    $chk->execute([$vSku, $productId]);
                    if ($chk->fetch()) {
                        $vSku = $vSku . '-' . $productId . '-' . ($idx + 1);
                    }

                    $seenSkus[] = strtoupper($vSku);

                    $vPrice = is_array($v) ? (float)($v['price'] ?? $basePrice) : $basePrice;
                    $vStock = is_array($v) ? (int)($v['stock'] ?? $v['stock_quantity'] ?? 0) : 0;
                    $totalVariantStock += $vStock;
                    $vImg = is_array($v) ? (!empty($v['image_url']) ? $v['image_url'] : null) : null;
                    $isDefault = ($idx === 0) ? 1 : 0;

                    try {
                        $vStmt->execute([$productId, $vSku, $vName, $vPrice, $vStock, $isDefault, $vImg]);
                    } catch (\Exception $vex) {
                        error_log("Variant insert retry in create for product {$productId}: " . $vex->getMessage());
                        $fallbackSku = $sku . '-V' . ($idx + 1) . '-' . bin2hex(random_bytes(2));
                        $vStmt->execute([$productId, $fallbackSku, $vName, $vPrice, $vStock, $isDefault, $vImg]);
                    }
                }

                // Automatically sync parent product stock_quantity & stock_status with variants
                $stStatus = ($totalVariantStock <= 0) ? 'Out of Stock' : (($totalVariantStock < 15) ? 'Low Stock' : 'In Stock');
                $db->prepare("UPDATE products SET stock_quantity = ?, stock_status = ? WHERE id = ?")->execute([$totalVariantStock, $stStatus, $productId]);
            }

            // Save Specifications if provided
            if (isset($data['highlights']) || isset($data['specs'])) {
                self::saveSpecifications($productId, $data['highlights'] ?? [], $data['specs'] ?? []);
            }

            // Save FAQs if provided
            if (isset($data['faqs']) && is_array($data['faqs'])) {
                self::saveFaqs($productId, $data['faqs']);
            }

            return $productId;
        } catch (\Exception $e) {
            error_log("Product::create() error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update an existing product in the database
     */
    public static function update($id, array $data)
    {
        $db = self::getDB();
        if (!$db) return false;

        $existing = self::find($id);
        if (!$existing) return false;

        $title = trim($data['title'] ?? $data['name'] ?? $existing['title'] ?? '');
        if (empty($title)) return false;

        $name = $title;

        // Resolve Brand ID
        $brandId = !empty($data['brand_id']) ? (int)$data['brand_id'] : ($existing['brand_id'] ?? null);
        if (!$brandId && !empty($data['brand'])) {
            $brandName = trim($data['brand']);
            $bStmt = $db->prepare("SELECT id FROM brands WHERE LOWER(name) = ? OR LOWER(slug) = ? LIMIT 1");
            $bStmt->execute([strtolower($brandName), strtolower($brandName)]);
            $bRow = $bStmt->fetch(PDO::FETCH_ASSOC);
            if ($bRow) $brandId = (int)$bRow['id'];
        }

        $sku = !empty($data['sku']) ? trim($data['sku']) : ($existing['sku'] ?? '');
        $slug = !empty($data['slug']) ? trim($data['slug']) : ($existing['slug'] ?? '');
        if (empty($slug)) {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $title));
            $slug = trim($slug, '-');
        }

        // Unique slug check excluding current record
        $baseSlug = $slug;
        $counter = 1;
        while (true) {
            $checkStmt = $db->prepare("SELECT id FROM products WHERE slug = ? AND id != ? LIMIT 1");
            $checkStmt->execute([$slug, (int)$id]);
            if (!$checkStmt->fetch()) break;
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $customUrl = $slug;
        $shortDesc = isset($data['short_desc']) ? trim($data['short_desc']) : ($existing['short_desc'] ?? '');
        $longDesc = isset($data['long_desc']) ? trim($data['long_desc']) : (isset($data['description']) ? trim($data['description']) : ($existing['long_desc'] ?? ''));
        $relatedPostsContent = array_key_exists('related_posts_content', $data) ? trim($data['related_posts_content']) : ($existing['related_posts_content'] ?? null);
        $basePrice = isset($data['price']) || isset($data['base_price']) ? (float)str_replace(['$', ','], '', $data['price'] ?? $data['base_price'] ?? 0) : (float)($existing['base_price'] ?? 0);
        $comparePrice = array_key_exists('compare_price', $data) ? (!empty($data['compare_price']) ? (float)str_replace(['$', ','], '', $data['compare_price']) : null) : ($existing['compare_price'] ?? null);
        $stockQuantity = isset($data['stock_quantity']) ? (int)$data['stock_quantity'] : (isset($data['stock']) ? (int)$data['stock'] : (int)($existing['stock_quantity'] ?? 0));
        
        $stockStatus = 'In Stock';
        if ($stockQuantity <= 0) {
            $stockStatus = 'Out of Stock';
        } elseif ($stockQuantity < 15) {
            $stockStatus = 'Low Stock';
        }

        $hasImageKey = array_key_exists('featured_image', $data) || array_key_exists('image', $data) || array_key_exists('img', $data);
        $rawFeatured = $data['featured_image'] ?? $data['image'] ?? $data['img'] ?? null;
        $featuredImage = (!empty($rawFeatured) && $rawFeatured !== 'assets/images/vape-image.webp' && $rawFeatured !== 'assets/images/no-image.svg') ? $rawFeatured : null;

        $statusVal = $data['status'] ?? $existing['status'] ?? 'published';
        $status = in_array($statusVal, ['published', 'draft', 'archived']) ? $statusVal : ($statusVal === 'active' ? 'published' : 'published');
        $tags = isset($data['tags']) ? (is_array($data['tags']) ? implode(', ', $data['tags']) : trim($data['tags'])) : ($existing['tags'] ?? '');
        $optionNames = array_key_exists('option_names', $data) ? (is_array($data['option_names']) ? json_encode($data['option_names']) : $data['option_names']) : ($existing['option_names'] ?? '["Flavor"]');
        $seoTitle = array_key_exists('seo_title', $data) 
            ? (!empty(trim($data['seo_title'] ?? '')) ? trim($data['seo_title']) : null) 
            : ($existing['seo_title'] ?? null);
        $seoDescription = array_key_exists('seo_description', $data) 
            ? (!empty(trim($data['seo_description'] ?? '')) ? trim($data['seo_description']) : null) 
            : ($existing['seo_description'] ?? null);

        $bundleBannerActive = isset($data['bundle_banner_active']) ? (int)$data['bundle_banner_active'] : 0;
        $bundleBannerImg = array_key_exists('bundle_banner_img', $data) ? $data['bundle_banner_img'] : null;
        $bundleBannerLink = array_key_exists('bundle_banner_link', $data) ? $data['bundle_banner_link'] : null;

        $frequentlyBoughtTogether = $existing['frequently_bought_together'] ?? null;
        if (array_key_exists('frequently_bought_together', $data)) {
            if (is_array($data['frequently_bought_together'])) {
                $fbtClean = array_values(array_slice(array_unique(array_filter(array_map('intval', $data['frequently_bought_together']))), 0, 4));
                $frequentlyBoughtTogether = !empty($fbtClean) ? json_encode($fbtClean) : null;
            } elseif (is_string($data['frequently_bought_together'])) {
                $fbtDecoded = json_decode($data['frequently_bought_together'], true);
                if (is_array($fbtDecoded)) {
                    $fbtClean = array_values(array_slice(array_unique(array_filter(array_map('intval', $fbtDecoded))), 0, 4));
                    $frequentlyBoughtTogether = !empty($fbtClean) ? json_encode($fbtClean) : null;
                } else {
                    $val = trim($data['frequently_bought_together']);
                    $frequentlyBoughtTogether = ($val !== '') ? $val : null;
                }
            } else {
                $frequentlyBoughtTogether = null;
            }
        }

        try {
            if ($hasImageKey) {
                $sql = "UPDATE products 
                        SET brand_id = ?, name = ?, title = ?, sku = ?, slug = ?, custom_url = ?, 
                            short_desc = ?, long_desc = ?, related_posts_content = ?, base_price = ?, compare_price = ?, 
                            stock_quantity = ?, stock_status = ?, featured_image = ?, status = ?, 
                            tags = ?, option_names = ?, seo_title = ?, seo_description = ?,
                            bundle_banner_active = ?, bundle_banner_img = ?, bundle_banner_link = ?,
                            frequently_bought_together = ?,
                            updated_at = CURRENT_TIMESTAMP
                        WHERE id = ?";
                $params = [
                    $brandId, $name, $title, $sku, $slug, $customUrl,
                    $shortDesc, $longDesc, $relatedPostsContent, $basePrice, $comparePrice,
                    $stockQuantity, $stockStatus, $featuredImage, $status,
                    $tags, $optionNames, $seoTitle, $seoDescription,
                    $bundleBannerActive, $bundleBannerImg, $bundleBannerLink,
                    $frequentlyBoughtTogether,
                    (int)$id
                ];
            } else {
                $sql = "UPDATE products 
                        SET brand_id = ?, name = ?, title = ?, sku = ?, slug = ?, custom_url = ?, 
                            short_desc = ?, long_desc = ?, related_posts_content = ?, base_price = ?, compare_price = ?, 
                            stock_quantity = ?, stock_status = ?, status = ?, 
                            tags = ?, option_names = ?, seo_title = ?, seo_description = ?,
                            bundle_banner_active = ?, bundle_banner_img = ?, bundle_banner_link = ?,
                            frequently_bought_together = ?,
                            updated_at = CURRENT_TIMESTAMP
                        WHERE id = ?";
                $params = [
                    $brandId, $name, $title, $sku, $slug, $customUrl,
                    $shortDesc, $longDesc, $relatedPostsContent, $basePrice, $comparePrice,
                    $stockQuantity, $stockStatus, $status,
                    $tags, $optionNames, $seoTitle, $seoDescription,
                    $bundleBannerActive, $bundleBannerImg, $bundleBannerLink,
                    $frequentlyBoughtTogether,
                    (int)$id
                ];
            }

            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            // Update primary collection link
            $colId = !empty($data['collection_id']) ? (int)$data['collection_id'] : null;
            if (!$colId && !empty($data['collection'])) {
                $colName = trim($data['collection']);
                $cStmt = $db->prepare("SELECT id FROM collections WHERE LOWER(name) = ? OR LOWER(slug) = ? LIMIT 1");
                $cStmt->execute([strtolower($colName), strtolower($colName)]);
                $cRow = $cStmt->fetch(PDO::FETCH_ASSOC);
                if ($cRow) $colId = (int)$cRow['id'];
            }

            if ($colId) {
                $delColStmt = $db->prepare("DELETE FROM product_collections WHERE product_id = ?");
                $delColStmt->execute([(int)$id]);

                $pcStmt = $db->prepare("INSERT INTO product_collections (product_id, collection_id) VALUES (?, ?)");
                $pcStmt->execute([(int)$id, $colId]);
            }

            // Sync images in product_images table
            if (isset($data['existing_images']) || isset($data['images']) || !empty($data['variants'])) {
                $finalImages = [];
                if (!empty($data['existing_images']) && is_array($data['existing_images'])) {
                    foreach ($data['existing_images'] as $eImg) {
                        $eImg = trim($eImg);
                        if (!empty($eImg) && !in_array($eImg, $finalImages)) {
                            $finalImages[] = $eImg;
                        }
                    }
                }
                if (!empty($data['images']) && is_array($data['images'])) {
                    foreach ($data['images'] as $nImg) {
                        $nImg = trim($nImg);
                        if (!empty($nImg) && !in_array($nImg, $finalImages)) {
                            $finalImages[] = $nImg;
                        }
                    }
                }
                if (!empty($data['variants']) && is_array($data['variants'])) {
                    foreach ($data['variants'] as $v) {
                        $vImg = is_array($v) ? trim($v['image_url'] ?? '') : '';
                        if (!empty($vImg) && !in_array($vImg, $finalImages)) {
                            $finalImages[] = $vImg;
                        }
                    }
                }

                $db->prepare("DELETE FROM product_images WHERE product_id = ?")->execute([(int)$id]);
                if (!empty($finalImages)) {
                    $imgStmt = $db->prepare("INSERT INTO product_images (product_id, image_url, alt_text, sort_order) VALUES (?, ?, ?, ?)");
                    foreach ($finalImages as $order => $imgUrl) {
                        $altText = $title . ' - ' . ($order === 0 ? 'Featured Image' : 'Gallery Image ' . ($order + 1));
                        $imgStmt->execute([(int)$id, $imgUrl, $altText, $order + 1]);
                    }
                }
            }

            // Save Variants if provided
            if (isset($data['variants']) && is_array($data['variants'])) {
                $db->prepare("DELETE FROM product_variants WHERE product_id = ?")->execute([(int)$id]);
                if (!empty($data['variants'])) {
                    $vStmt = $db->prepare("INSERT INTO product_variants (product_id, sku, variant_name, price, stock_quantity, is_default, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $seenSkus = [];
                    $totalVariantStock = 0;
                    foreach ($data['variants'] as $idx => $v) {
                        $vName = is_array($v) ? ($v['name'] ?? $v['variant_name'] ?? '') : (string)$v;
                        if (empty($vName)) continue;
                        $rawSku = trim(is_array($v) ? ($v['sku'] ?? '') : '');
                        $candidateSku = !empty($rawSku) ? $rawSku : ($sku . '-V' . ($idx + 1));

                        // Ensure SKU is unique within this product's variants batch
                        $vSku = $candidateSku;
                        $dupCount = 1;
                        while (in_array(strtoupper($vSku), $seenSkus)) {
                            $vSku = $candidateSku . '-' . ($dupCount++);
                        }

                        // Check if this SKU collides with another product in product_variants
                        $chk = $db->prepare("SELECT id FROM product_variants WHERE sku = ? AND product_id != ? LIMIT 1");
                        $chk->execute([$vSku, (int)$id]);
                        if ($chk->fetch()) {
                            $vSku = $vSku . '-' . (int)$id . '-' . ($idx + 1);
                        }

                        $seenSkus[] = strtoupper($vSku);

                        $vPrice = is_array($v) ? (float)($v['price'] ?? $basePrice) : $basePrice;
                        $vStock = is_array($v) ? (int)($v['stock'] ?? $v['stock_quantity'] ?? 0) : 0;
                        $totalVariantStock += $vStock;
                        $vImg = is_array($v) ? (!empty($v['image_url']) ? $v['image_url'] : null) : null;
                        $isDefault = ($idx === 0) ? 1 : 0;

                        try {
                            $vStmt->execute([(int)$id, $vSku, $vName, $vPrice, $vStock, $isDefault, $vImg]);
                        } catch (\Exception $vex) {
                            error_log("Variant insert retry in update for product {$id}: " . $vex->getMessage());
                            $fallbackSku = $sku . '-V' . ($idx + 1) . '-' . bin2hex(random_bytes(2));
                            $vStmt->execute([(int)$id, $fallbackSku, $vName, $vPrice, $vStock, $isDefault, $vImg]);
                        }
                    }

                    // Automatically sync parent product stock_quantity & stock_status with variants
                    $stStatus = ($totalVariantStock <= 0) ? 'Out of Stock' : (($totalVariantStock < 15) ? 'Low Stock' : 'In Stock');
                    $db->prepare("UPDATE products SET stock_quantity = ?, stock_status = ? WHERE id = ?")->execute([$totalVariantStock, $stStatus, (int)$id]);
                }
            }

            // Save Specifications if provided
            if (isset($data['highlights']) || isset($data['specs'])) {
                self::saveSpecifications($id, $data['highlights'] ?? [], $data['specs'] ?? []);
            }

            // Save FAQs if provided
            if (isset($data['faqs']) && is_array($data['faqs'])) {
                self::saveFaqs($id, $data['faqs']);
            }

            return true;
        } catch (\Exception $e) {
            error_log("Product::update() error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get only database-persisted images for a product (strictly for Admin Edit)
     */
    public static function getRawImages($productId)
    {
        $db = self::getDB();
        if (!$db || empty($productId)) return [];
        try {
            $stmt = $db->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order ASC, id ASC");
            $stmt->execute([(int)$productId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get all images for a product (from product_images table or featured image fallback)
     */
    public static function getImages($productId, $product = null)
    {
        $db = self::getDB();
        $images = [];
        if ($db) {
            try {
                $stmt = $db->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order ASC, id ASC");
                $stmt->execute([(int)$productId]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($rows)) {
                    $images = $rows;
                }
            } catch (\Exception $e) {}
        }

        if (empty($images)) {
            $featImg = !empty($product['featured_image']) && $product['featured_image'] !== 'assets/images/vape-image.webp'
                ? $product['featured_image'] 
                : (!empty($product['img']) && $product['img'] !== 'assets/images/vape-image.webp' ? $product['img'] : 'assets/images/no-image.svg');
            $pTitle = $product['title'] ?? ($product['name'] ?? 'Product');
            
            $images = [
                [
                    'image_url' => $featImg,
                    'alt_text'  => $pTitle . ' - Front View'
                ]
            ];
        }

        return $images;
    }

    /**
     * Delete product and all associated dependencies
     */
    public static function delete($id)
    {
        $db = self::getDB();
        if (!$db) return false;

        try {
            $intId = (int)$id;

            // Delete dependencies
            $db->prepare("DELETE FROM product_collections WHERE product_id = ?")->execute([$intId]);
            $db->prepare("DELETE FROM product_variants WHERE product_id = ?")->execute([$intId]);
            $db->prepare("DELETE FROM product_images WHERE product_id = ?")->execute([$intId]);
            $db->prepare("DELETE FROM product_specifications WHERE product_id = ?")->execute([$intId]);
            $db->prepare("DELETE FROM product_faqs WHERE product_id = ?")->execute([$intId]);
            $db->prepare("DELETE FROM product_reviews WHERE product_id = ?")->execute([$intId]);
            $db->prepare("DELETE FROM product_related_posts WHERE product_id = ?")->execute([$intId]);

            // Delete product
            $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
            return $stmt->execute([$intId]);
        } catch (\Exception $e) {
            error_log("Product::delete() error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Dynamic Top 4 Highlight Boxes (Puffs, Nicotine, Battery, Capacity)
     */
    public static function getHighlights($productId, $product = null)
    {
        $db = self::getDB();
        if ($db) {
            try {
                $stmt = $db->prepare("SELECT spec_key as label, spec_value as value, icon_type FROM product_specifications WHERE product_id = ? AND is_highlight = 1 ORDER BY sort_order ASC LIMIT 4");
                $stmt->execute([(int)$productId]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($rows)) return $rows;
            } catch (\Exception $e) {}
        }

        return [];
    }

    /**
     * Dynamic "More Information" Specifications Table
     */
    public static function getSpecifications($productId, $product = null)
    {
        $db = self::getDB();
        if ($db) {
            try {
                $stmt = $db->prepare("SELECT spec_key, spec_value FROM product_specifications WHERE product_id = ? AND is_highlight = 0 ORDER BY sort_order ASC");
                $stmt->execute([(int)$productId]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($rows)) {
                    $specs = [];
                    foreach ($rows as $r) {
                        $specs[$r['spec_key']] = $r['spec_value'];
                    }
                    return $specs;
                }
            } catch (\Exception $e) {}
        }

        return [];
    }

    /**
     * Get only database-persisted variants (strictly for Admin Edit)
     */
    public static function getRawVariants($productId)
    {
        $db = self::getDB();
        if (!$db || empty($productId)) return [];
        try {
            $stmt = $db->prepare("SELECT * FROM product_variants WHERE product_id = ? ORDER BY id ASC");
            $stmt->execute([(int)$productId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get raw highlights and specifications from DB for a product
     */
    public static function getRawSpecifications($productId)
    {
        $db = self::getDB();
        $result = ['highlights' => [], 'specs' => []];
        if (!$db || empty($productId)) return $result;
        try {
            $stmt = $db->prepare("SELECT * FROM product_specifications WHERE product_id = ? ORDER BY is_highlight DESC, sort_order ASC, id ASC");
            $stmt->execute([(int)$productId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $r) {
                if ((int)$r['is_highlight'] === 1) {
                    $result['highlights'][] = $r;
                } else {
                    $result['specs'][] = $r;
                }
            }
            return $result;
        } catch (\Exception $e) {
            return $result;
        }
    }

    /**
     * Save/Replace Specifications (both highlights and detailed table) for a product
     */
    public static function saveSpecifications($productId, array $highlights = [], array $specs = [])
    {
        $db = self::getDB();
        if (!$db || empty($productId)) return false;

        try {
            $db->prepare("DELETE FROM product_specifications WHERE product_id = ?")->execute([(int)$productId]);

            $stmt = $db->prepare("INSERT INTO product_specifications (product_id, spec_key, spec_value, is_highlight, icon_type, spec_group, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");

            // Save Highlights (is_highlight = 1)
            $hOrder = 1;
            foreach ($highlights as $h) {
                $key = trim($h['key'] ?? $h['spec_key'] ?? $h['label'] ?? '');
                $val = trim($h['value'] ?? $h['spec_value'] ?? '');
                $icon = trim($h['icon_type'] ?? 'general');
                if (!empty($key) && !empty($val)) {
                    $stmt->execute([(int)$productId, $key, $val, 1, $icon, 'Key Highlights', $hOrder++]);
                }
            }

            // Save Detailed Specs (is_highlight = 0)
            $sOrder = 1;
            foreach ($specs as $s) {
                $key = trim($s['key'] ?? $s['spec_key'] ?? '');
                $val = trim($s['value'] ?? $s['spec_value'] ?? '');
                $group = trim($s['spec_group'] ?? 'General Specs');
                if (!empty($key) && !empty($val)) {
                    $stmt->execute([(int)$productId, $key, $val, 0, 'general', $group, $sOrder++]);
                }
            }

            return true;
        } catch (\Exception $e) {
            error_log("Product::saveSpecifications() error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Dynamic Variants (Flavors, Strengths, Colors, etc.)
     */
    public static function getVariants($productId, $product = null)
    {
        $db = self::getDB();
        if ($db) {
            try {
                $stmt = $db->prepare("SELECT * FROM product_variants WHERE product_id = ? ORDER BY id ASC");
                $stmt->execute([(int)$productId]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($rows)) {
                    $prodCompare = !empty($product['compare_price']) ? (float)$product['compare_price'] : null;
                    foreach ($rows as &$r) {
                        $r['full_image_url'] = !empty($r['image_url']) ? asset($r['image_url']) : '';
                        if (!isset($r['compare_price']) || $r['compare_price'] === null) {
                            $r['compare_price'] = $prodCompare;
                        }
                    }
                    unset($r);
                    return $rows;
                }
            } catch (\Exception $e) {}
        }

        return [];
    }

    /**
     * Find a single variant by its ID
     */
    public static function findVariant($variantId)
    {
        $db = self::getDB();
        if (!$db || empty($variantId)) return null;

        try {
            $stmt = $db->prepare("SELECT * FROM product_variants WHERE id = ? LIMIT 1");
            $stmt->execute([(int)$variantId]);
            $variant = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($variant) {
                $variant['full_image_url'] = !empty($variant['image_url']) ? asset($variant['image_url']) : '';
                return $variant;
            }
        } catch (\Exception $e) {
            error_log("Product::findVariant error: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Dynamic Related Products ("You May Also Like")
     */
    public static function getRelatedProducts($productId, $brandId = null, $collectionId = null, $limit = 6)
    {
        $db = self::getDB();
        if ($db) {
            try {
                $sql = "SELECT p.*, 
                               b.name AS brand,
                               COALESCE(c.name, 'Disposables') AS category,
                               c.slug AS category_slug,
                               COALESCE(NULLIF(p.featured_image, ''), 'assets/images/vape-image.webp') AS img
                        FROM products p 
                        LEFT JOIN brands b ON p.brand_id = b.id
                        LEFT JOIN product_collections pc ON p.id = pc.product_id
                        LEFT JOIN collections c ON pc.collection_id = c.id
                        WHERE p.id != ? AND p.status = 'published' ";
                
                $params = [(int)$productId];
                if (!empty($collectionId)) {
                    $sql .= " AND (pc.collection_id = ? OR p.brand_id = ?) ";
                    $params[] = (int)$collectionId;
                    $params[] = (int)$brandId;
                } elseif (!empty($brandId)) {
                    $sql .= " AND p.brand_id = ? ";
                    $params[] = (int)$brandId;
                }

                $sql .= " GROUP BY p.id ORDER BY p.is_bestseller DESC, p.id DESC LIMIT " . (int)$limit;
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($results) < $limit) {
                    $fillSql = "SELECT p.*, b.name AS brand, COALESCE(c.name, 'Disposables') AS category, c.slug AS category_slug, COALESCE(NULLIF(p.featured_image, ''), 'assets/images/vape-image.webp') AS img
                                FROM products p
                                LEFT JOIN brands b ON p.brand_id = b.id
                                LEFT JOIN product_collections pc ON p.id = pc.product_id
                                LEFT JOIN collections c ON pc.collection_id = c.id
                                WHERE p.id != ? AND p.status = 'published'
                                GROUP BY p.id ORDER BY p.id DESC LIMIT " . (int)$limit;
                    $fillStmt = $db->prepare($fillSql);
                    $fillStmt->execute([(int)$productId]);
                    $results = $fillStmt->fetchAll(PDO::FETCH_ASSOC);
                }

                return $results;
            } catch (\Exception $e) {
                error_log("Product::getRelatedProducts error: " . $e->getMessage());
            }
        }
        return [];
    }

    /**
     * Dynamic Product FAQs (Tab 4)
     */
    public static function getFaqs($productId, $product = null)
    {
        $db = self::getDB();
        if ($db) {
            try {
                $stmt = $db->prepare("SELECT question, answer FROM product_faqs WHERE product_id = ? ORDER BY sort_order ASC");
                $stmt->execute([(int)$productId]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($rows)) return $rows;
            } catch (\Exception $e) {}
        }

        return [];
    }

    /**
     * Save/Replace FAQs for a product
     */
    public static function saveFaqs($productId, array $faqs)
    {
        $db = self::getDB();
        if (!$db || empty($productId)) return false;

        try {
            $db->prepare("DELETE FROM product_faqs WHERE product_id = ?")->execute([(int)$productId]);

            if (!empty($faqs)) {
                $stmt = $db->prepare("INSERT INTO product_faqs (product_id, question, answer, sort_order) VALUES (?, ?, ?, ?)");
                $order = 1;
                foreach ($faqs as $faq) {
                    $question = trim($faq['question'] ?? '');
                    $answer = trim($faq['answer'] ?? '');
                    if (!empty($question) && !empty($answer)) {
                        $stmt->execute([(int)$productId, $question, $answer, $order++]);
                    }
                }
            }
            return true;
        } catch (\Exception $e) {
            error_log("Product::saveFaqs() error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Dynamic Product Reviews (Tab 3)
     */
    public static function getReviews($productId)
    {
        $db = self::getDB();
        if ($db) {
            try {
                $stmt = $db->prepare("SELECT id, customer_name, rating, title, comment, created_at FROM product_reviews WHERE product_id = ? AND status = 'approved' ORDER BY id DESC");
                $stmt->execute([(int)$productId]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($rows)) {
                    foreach ($rows as &$r) {
                        if (!empty($r['created_at'])) {
                            $r['created_at'] = date('M d, Y', strtotime($r['created_at']));
                        }
                    }
                    return $rows;
                }
            } catch (\Exception $e) {}
        }

        return [];
    }

    /**
     * Dynamic Related Posts (Tab 5)
     */
    public static function getRelatedPosts($productId)
    {
        $db = self::getDB();
        if ($db) {
            try {
                $stmt = $db->prepare("SELECT bp.title, bp.slug FROM product_related_posts prp JOIN blog_posts bp ON prp.post_id = bp.id WHERE prp.product_id = ? ORDER BY prp.sort_order ASC");
                $stmt->execute([(int)$productId]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($rows)) {
                    $prodCompare = !empty($product['compare_price']) ? (float)$product['compare_price'] : null;
                    foreach ($rows as &$r) {
                        $r['full_image_url'] = !empty($r['image_url']) ? asset($r['image_url']) : '';
                        if (!isset($r['compare_price']) || $r['compare_price'] === null) {
                            $r['compare_price'] = $prodCompare;
                        }
                    }
                    unset($r);
                    return $rows;
                }
            } catch (\Exception $e) {}
        }

        return [];
    }

    /**
     * Dynamic Frequently Bought Together products
     */
    public static function getFrequentlyBoughtProducts($productOrId)
    {
        $db = self::getDB();
        if (!$db) return [];

        $fbtRaw = null;
        if (is_array($productOrId)) {
            $fbtRaw = $productOrId['frequently_bought_together'] ?? null;
            if ($fbtRaw === null && isset($productOrId['id'])) {
                $p = self::find($productOrId['id']);
                $fbtRaw = $p['frequently_bought_together'] ?? null;
            }
        } elseif (is_numeric($productOrId)) {
            $p = self::find((int)$productOrId);
            $fbtRaw = $p['frequently_bought_together'] ?? null;
        }

        if (empty($fbtRaw)) return [];

        $ids = [];
        if (is_array($fbtRaw)) {
            $ids = array_map('intval', $fbtRaw);
        } else {
            $decoded = json_decode($fbtRaw, true);
            if (is_array($decoded)) {
                $ids = array_map('intval', $decoded);
            } else {
                $ids = array_map('intval', explode(',', $fbtRaw));
            }
        }

        $ids = array_values(array_unique(array_filter($ids, function($id) { return $id > 0; })));
        $ids = array_slice($ids, 0, 4);

        if (empty($ids)) return [];

        try {
            $inPlaceholders = implode(',', array_fill(0, count($ids), '?'));
            $sql = "SELECT p.*, 
                           b.name AS brand,
                           b.slug AS brand_slug,
                           COALESCE(c.name, 'Disposable') AS category,
                           c.slug AS category_slug,
                           COALESCE(NULLIF(NULLIF(p.featured_image, ''), 'assets/images/vape-image.webp'), 'assets/images/no-image.svg') AS img
                    FROM products p 
                    LEFT JOIN brands b ON p.brand_id = b.id
                    LEFT JOIN product_collections pc ON p.id = pc.product_id
                    LEFT JOIN collections c ON pc.collection_id = c.id
                    WHERE p.id IN ($inPlaceholders) AND p.status = 'published'
                    GROUP BY p.id";

            $stmt = $db->prepare($sql);
            $stmt->execute($ids);
            $fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Index by ID
            $indexed = [];
            foreach ($fetched as $item) {
                $indexed[$item['id']] = $item;
            }

            $results = [];
            foreach ($ids as $fbtId) {
                if (isset($indexed[$fbtId])) {
                    $item = $indexed[$fbtId];
                    // Fetch variants
                    $variants = self::getVariants($item['id'], $item);
                    $item['variants'] = $variants;

                    // Parse option names
                    $optionNames = [];
                    if (!empty($item['option_names'])) {
                        $optDecoded = json_decode($item['option_names'], true);
                        if (is_array($optDecoded)) {
                            $optionNames = $optDecoded;
                        }
                    }
                    if (empty($optionNames)) {
                        $optionNames = ['Flavor'];
                    }
                    $item['parsed_option_names'] = $optionNames;

                    // Parse multi-option dimensions if variants exist
                    $parsedOptions = [];
                    if (!empty($variants)) {
                        $maxParts = 1;
                        foreach ($variants as $pv) {
                            $parts = array_map('trim', explode(' / ', $pv['variant_name']));
                            if (count($parts) > $maxParts) {
                                $maxParts = count($parts);
                            }
                        }

                        $optionLabels = [];
                        for ($i = 0; $i < $maxParts; $i++) {
                            if (!empty($optionNames[$i])) {
                                $optionLabels[$i] = $optionNames[$i];
                            } else {
                                $optionLabels[$i] = ($i === 0) ? 'Flavor' : (($i === 1) ? 'Strength' : (($i === 2) ? 'Size' : 'Option ' . ($i + 1)));
                            }
                        }

                        $optionValuesMap = array_fill(0, $maxParts, []);
                        foreach ($variants as $pv) {
                            $parts = array_map('trim', explode(' / ', $pv['variant_name']));
                            for ($i = 0; $i < $maxParts; $i++) {
                                $val = $parts[$i] ?? ($i === 0 ? $pv['variant_name'] : '');
                                if ($val !== '' && !in_array($val, $optionValuesMap[$i])) {
                                    $optionValuesMap[$i][] = $val;
                                }
                            }
                        }

                        for ($i = 0; $i < $maxParts; $i++) {
                            if (!empty($optionValuesMap[$i])) {
                                $parsedOptions[] = [
                                    'index' => $i,
                                    'name' => $optionLabels[$i],
                                    'values' => $optionValuesMap[$i]
                                ];
                            }
                        }
                    }
                    $item['parsed_options'] = $parsedOptions;

                    // Calculate price range
                    $prices = [];
                    if (!empty($variants)) {
                        foreach ($variants as $v) {
                            $vp = (float)($v['price'] ?? $item['base_price']);
                            if ($vp > 0) $prices[] = $vp;
                        }
                    }
                    if (empty($prices)) {
                        $prices[] = (float)$item['base_price'];
                    }

                    $minPrice = min($prices);
                    $maxPrice = max($prices);

                    $item['min_price'] = $minPrice;
                    $item['max_price'] = $maxPrice;
                    $item['price_display'] = ($minPrice === $maxPrice) 
                        ? '$' . number_format($minPrice, 2) 
                        : '$' . number_format($minPrice, 2) . ' - $' . number_format($maxPrice, 2);

                    // Calculate total stock & stock status
                    $totalStock = 0;
                    $firstInStockVariant = null;
                    if (!empty($variants)) {
                        foreach ($variants as $v) {
                            $vStock = (int)($v['stock_quantity'] ?? 0);
                            $totalStock += $vStock;
                            if ($vStock > 0 && $firstInStockVariant === null) {
                                $firstInStockVariant = $v;
                            }
                        }
                        // Product has variants: It is ONLY out of stock if ALL variants have 0 stock!
                        $isOutOfStock = ($totalStock <= 0);
                    } else {
                        $totalStock = (int)($item['stock_quantity'] ?? 0);
                        $isOutOfStock = ($totalStock <= 0) || (strtolower(str_replace(' ', '_', $item['stock_status'] ?? '')) === 'out_of_stock');
                    }

                    $item['calculated_stock'] = $totalStock;
                    $item['is_out_of_stock'] = $isOutOfStock;
                    $item['first_in_stock_variant'] = $firstInStockVariant ?? (!empty($variants) ? $variants[0] : null);

                    $results[] = $item;
                }
            }

            return $results;
        } catch (\Exception $e) {
            error_log("Product::getFrequentlyBoughtProducts error: " . $e->getMessage());
            return [];
        }
    }
}
