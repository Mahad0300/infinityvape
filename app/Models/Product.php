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
                               COALESCE(NULLIF(p.featured_image, ''), 'assets/images/no-image.svg') AS img,
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
                               COALESCE(NULLIF(p.featured_image, ''), 'assets/images/vape-image.webp') AS img,
                               (SELECT COUNT(*) FROM product_variants pv WHERE pv.product_id = p.id) AS variants_count,
                               COALESCE((SELECT SUM(pv.stock_quantity) FROM product_variants pv WHERE pv.product_id = p.id), p.stock_quantity, 0) AS calculated_stock
                        FROM products p 
                        LEFT JOIN brands b ON p.brand_id = b.id 
                        LEFT JOIN product_collections pc ON p.id = pc.product_id
                        LEFT JOIN collections c ON pc.collection_id = c.id
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
                           b.name AS brand,
                           b.slug AS brand_slug,
                           COALESCE(c.name, 'Disposable') AS category,
                           c.id AS collection_id,
                           c.slug AS collection_slug,
                           COALESCE(NULLIF(p.featured_image, ''), 'assets/images/no-image.svg') AS img,
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

        $featuredImage = trim($data['featured_image'] ?? $data['image'] ?? $data['img'] ?? 'assets/images/vape-image.webp');
        $status = in_array($data['status'] ?? '', ['published', 'draft', 'archived']) ? $data['status'] : ($data['status'] === 'active' ? 'published' : 'published');
        $tags = is_array($data['tags'] ?? null) ? implode(', ', $data['tags']) : trim($data['tags'] ?? '');
        $optionNames = array_key_exists('option_names', $data) ? (is_array($data['option_names']) ? json_encode($data['option_names']) : $data['option_names']) : '["Flavor"]';
        $seoTitle = trim($data['seo_title'] ?? ($title . ' | Infinity Vape Shop'));
        $seoDescription = trim($data['seo_description'] ?? strip_tags($shortDesc ?: $longDesc));
        $bundleBannerActive = !empty($data['bundle_banner_active']) ? 1 : 0;
        $bundleBannerImg = !empty($data['bundle_banner_img']) ? trim($data['bundle_banner_img']) : null;
        $bundleBannerLink = !empty($data['bundle_banner_link']) ? trim($data['bundle_banner_link']) : null;

        try {
            $sql = "INSERT INTO products 
                    (brand_id, name, title, sku, slug, custom_url, short_desc, long_desc, related_posts_content, base_price, compare_price, stock_quantity, stock_status, featured_image, status, tags, option_names, seo_title, seo_description, bundle_banner_active, bundle_banner_img, bundle_banner_link)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
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
                $bundleBannerLink
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
            if (!empty($data['images']) && is_array($data['images'])) {
                $imgStmt = $db->prepare("INSERT INTO product_images (product_id, image_url, sort_order) VALUES (?, ?, ?)");
                foreach ($data['images'] as $order => $imgUrl) {
                    if (!empty($imgUrl)) {
                        $imgStmt->execute([$productId, $imgUrl, $order + 1]);
                    }
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
        $featuredImage = $data['featured_image'] ?? $data['image'] ?? $data['img'] ?? null;

        $statusVal = $data['status'] ?? $existing['status'] ?? 'published';
        $status = in_array($statusVal, ['published', 'draft', 'archived']) ? $statusVal : ($statusVal === 'active' ? 'published' : 'published');
        $tags = isset($data['tags']) ? (is_array($data['tags']) ? implode(', ', $data['tags']) : trim($data['tags'])) : ($existing['tags'] ?? '');
        $optionNames = array_key_exists('option_names', $data) ? (is_array($data['option_names']) ? json_encode($data['option_names']) : $data['option_names']) : ($existing['option_names'] ?? '["Flavor"]');
        $seoTitle = trim($data['seo_title'] ?? $existing['seo_title'] ?? ($title . ' | Infinity Vape Shop'));
        $seoDescription = trim($data['seo_description'] ?? $existing['seo_description'] ?? strip_tags($shortDesc ?: $longDesc));

        $bundleBannerActive = isset($data['bundle_banner_active']) ? (int)$data['bundle_banner_active'] : 0;
        $bundleBannerImg = array_key_exists('bundle_banner_img', $data) ? $data['bundle_banner_img'] : null;
        $bundleBannerLink = array_key_exists('bundle_banner_link', $data) ? $data['bundle_banner_link'] : null;

        try {
            if ($hasImageKey && $featuredImage !== null) {
                $sql = "UPDATE products 
                        SET brand_id = ?, name = ?, title = ?, sku = ?, slug = ?, custom_url = ?, 
                            short_desc = ?, long_desc = ?, related_posts_content = ?, base_price = ?, compare_price = ?, 
                            stock_quantity = ?, stock_status = ?, featured_image = ?, status = ?, 
                            tags = ?, option_names = ?, seo_title = ?, seo_description = ?,
                            bundle_banner_active = ?, bundle_banner_img = ?, bundle_banner_link = ?,
                            updated_at = CURRENT_TIMESTAMP
                        WHERE id = ?";
                $params = [
                    $brandId, $name, $title, $sku, $slug, $customUrl,
                    $shortDesc, $longDesc, $relatedPostsContent, $basePrice, $comparePrice,
                    $stockQuantity, $stockStatus, $featuredImage, $status,
                    $tags, $optionNames, $seoTitle, $seoDescription,
                    $bundleBannerActive, $bundleBannerImg, $bundleBannerLink,
                    (int)$id
                ];
            } else {
                $sql = "UPDATE products 
                        SET brand_id = ?, name = ?, title = ?, sku = ?, slug = ?, custom_url = ?, 
                            short_desc = ?, long_desc = ?, related_posts_content = ?, base_price = ?, compare_price = ?, 
                            stock_quantity = ?, stock_status = ?, status = ?, 
                            tags = ?, option_names = ?, seo_title = ?, seo_description = ?,
                            bundle_banner_active = ?, bundle_banner_img = ?, bundle_banner_link = ?,
                            updated_at = CURRENT_TIMESTAMP
                        WHERE id = ?";
                $params = [
                    $brandId, $name, $title, $sku, $slug, $customUrl,
                    $shortDesc, $longDesc, $relatedPostsContent, $basePrice, $comparePrice,
                    $stockQuantity, $stockStatus, $status,
                    $tags, $optionNames, $seoTitle, $seoDescription,
                    $bundleBannerActive, $bundleBannerImg, $bundleBannerLink,
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
            if (isset($data['existing_images']) || isset($data['images'])) {
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

                $db->prepare("DELETE FROM product_images WHERE product_id = ?")->execute([(int)$id]);
                if (!empty($finalImages)) {
                    $imgStmt = $db->prepare("INSERT INTO product_images (product_id, image_url, sort_order) VALUES (?, ?, ?)");
                    foreach ($finalImages as $order => $imgUrl) {
                        $imgStmt->execute([(int)$id, $imgUrl, $order + 1]);
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

        if (count($images) < 2) {
            $featImg = !empty($images[0]['image_url']) ? $images[0]['image_url'] : (!empty($product['featured_image']) ? $product['featured_image'] : (!empty($product['img']) ? $product['img'] : 'assets/images/vape-image.webp'));
            $pTitle = $product['title'] ?? ($product['name'] ?? 'Product');
            
            $images = [
                [
                    'image_url' => $featImg,
                    'alt_text'  => $pTitle . ' - Front View'
                ]
            ];

            // Complementary multi-view thumbnails (box, angle, flavor profile)
            $boxIdx = (($productId % 6) + 1);
            $images[] = [
                'image_url' => "assets/images/product-box-{$boxIdx}.png",
                'alt_text'  => $pTitle . ' - Packaging View'
            ];
            $images[] = [
                'image_url' => 'assets/images/disposible.webp',
                'alt_text'  => $pTitle . ' - Handheld View'
            ];
            $images[] = [
                'image_url' => 'assets/images/e-liquid.webp',
                'alt_text'  => $pTitle . ' - Flavor & Specs'
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
                if (!empty($rows)) return $rows;
            } catch (\Exception $e) {}
        }

        return [];
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
                if (!empty($rows)) return $rows;
            } catch (\Exception $e) {}
        }

        return [];
    }
}
