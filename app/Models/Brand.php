<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Brand extends Model
{
    /**
     * Get all brands with attached products count
     */
    public static function all()
    {
        $db = self::getDB();
        if (!$db) return [];

        try {
            $sql = "SELECT b.*, 
                           COUNT(p.id) AS products_count,
                           IF(b.is_active = 1, 'Active', 'Inactive') AS status_label
                    FROM brands b
                    LEFT JOIN products p ON b.id = p.brand_id
                    GROUP BY b.id
                    ORDER BY b.sort_order ASC, b.id ASC";
            
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Brand::all() error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get only active brands (e.g. for storefront & product dropdowns)
     */
    public static function active($limit = null)
    {
        $db = self::getDB();
        if (!$db) return [];

        try {
            $sql = "SELECT b.*, COUNT(p.id) AS products_count
                    FROM brands b
                    LEFT JOIN products p ON b.id = p.brand_id
                    WHERE b.is_active = 1
                    GROUP BY b.id
                    ORDER BY b.sort_order ASC, b.name ASC";
            
            if ($limit !== null && $limit > 0) {
                $sql .= " LIMIT " . (int)$limit;
            }

            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Brand::active() error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Find single brand by ID
     */
    public static function find($id)
    {
        $db = self::getDB();
        if (!$db) return null;

        try {
            $sql = "SELECT b.*, 
                           COUNT(p.id) AS products_count,
                           IF(b.is_active = 1, 'Active', 'Inactive') AS status_label
                    FROM brands b
                    LEFT JOIN products p ON b.id = p.brand_id
                    WHERE b.id = ?
                    GROUP BY b.id
                    LIMIT 1";

            $stmt = $db->prepare($sql);
            $stmt->execute([(int)$id]);
            $brand = $stmt->fetch(PDO::FETCH_ASSOC);
            return $brand ?: null;
        } catch (\Exception $e) {
            error_log("Brand::find() error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Find brand by slug
     */
    public static function findBySlug(string $slug)
    {
        $db = self::getDB();
        if (!$db) return null;

        try {
            $sql = "SELECT b.*, COUNT(p.id) AS products_count
                    FROM brands b
                    LEFT JOIN products p ON b.id = p.brand_id
                    WHERE b.slug = ?
                    GROUP BY b.id
                    LIMIT 1";

            $stmt = $db->prepare($sql);
            $stmt->execute([trim($slug)]);
            $brand = $stmt->fetch(PDO::FETCH_ASSOC);
            return $brand ?: null;
        } catch (\Exception $e) {
            error_log("Brand::findBySlug() error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get the clean, canonical SEO URL for a brand: /brand/{slug}
     */
    public static function getUrl($brandOrSlug)
    {
        if (empty($brandOrSlug)) return url('brands');

        if (is_string($brandOrSlug)) {
            $brand = self::findBySlug($brandOrSlug);
            if (!$brand && is_numeric($brandOrSlug)) {
                $brand = self::find($brandOrSlug);
            }
            if ($brand && !empty($brand['slug'])) {
                return url('brand/' . rawurlencode($brand['slug']));
            }
            return url('brand/' . rawurlencode(trim($brandOrSlug, '/')));
        }

        if (is_array($brandOrSlug) && !empty($brandOrSlug['slug'])) {
            return url('brand/' . rawurlencode($brandOrSlug['slug']));
        }

        return url('brands');
    }

    /**
     * Get FAQs for a specific brand
     */
    public static function getFaqs($brandId)
    {
        $db = self::getDB();
        if (!$db || empty($brandId)) return [];

        try {
            $stmt = $db->prepare("SELECT id, question, answer, sort_order FROM brand_faqs WHERE brand_id = ? ORDER BY sort_order ASC, id ASC");
            $stmt->execute([(int)$brandId]);
            $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return !empty($faqs) ? $faqs : [];
        } catch (\Exception $e) {
            error_log("Brand::getFaqs() error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Save/Replace FAQs for a brand
     */
    public static function saveFaqs($brandId, array $faqs)
    {
        $db = self::getDB();
        if (!$db || empty($brandId)) return false;

        try {
            $db->prepare("DELETE FROM brand_faqs WHERE brand_id = ?")->execute([(int)$brandId]);

            if (!empty($faqs)) {
                $stmt = $db->prepare("INSERT INTO brand_faqs (brand_id, question, answer, sort_order) VALUES (?, ?, ?, ?)");
                $order = 1;
                foreach ($faqs as $faq) {
                    $question = trim($faq['question'] ?? '');
                    $answer = trim($faq['answer'] ?? '');
                    if (!empty($question) && !empty($answer)) {
                        $stmt->execute([(int)$brandId, $question, $answer, $order++]);
                    }
                }
            }
            return true;
        } catch (\Exception $e) {
            error_log("Brand::saveFaqs() error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get header banner navigation collections for this brand
     */
    public static function getHeaderCollections($brand, $limit = 20)
    {
        $db = self::getDB();
        if (!$db) return [];

        $headerIds = [];
        if (is_array($brand) && !empty($brand['header_collection_ids'])) {
            $raw = $brand['header_collection_ids'];
            if (is_array($raw)) {
                $headerIds = $raw;
            } else {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $headerIds = $decoded;
                } else {
                    $headerIds = explode(',', $raw);
                }
            }
            $headerIds = array_values(array_filter(array_map('intval', $headerIds)));
        }

        if (empty($headerIds)) {
            return [];
        }

        try {
            $placeholders = implode(',', array_fill(0, count($headerIds), '?'));
            $sql = "SELECT c.*, 
                           COUNT(pc.product_id) AS products_count
                    FROM collections c
                    LEFT JOIN product_collections pc ON c.id = pc.collection_id
                    WHERE c.id IN ($placeholders) AND c.is_active = 1
                    GROUP BY c.id
                    ORDER BY FIELD(c.id, $placeholders)";
            
            $params = array_merge($headerIds, $headerIds);
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Brand::getHeaderCollections error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get related collections for storefront cross-sell display
     */
    public static function getRelatedCollections($brand, $limit = 8)
    {
        $db = self::getDB();
        if (!$db) return [];

        $relatedIds = [];
        if (is_array($brand) && !empty($brand['related_collection_ids'])) {
            $raw = $brand['related_collection_ids'];
            if (is_array($raw)) {
                $relatedIds = $raw;
            } else {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $relatedIds = $decoded;
                } else {
                    $relatedIds = explode(',', $raw);
                }
            }
            $relatedIds = array_values(array_filter(array_map('intval', $relatedIds)));
        }

        if (empty($relatedIds)) {
            return [];
        }

        try {
            $placeholders = implode(',', array_fill(0, count($relatedIds), '?'));
            $sql = "SELECT c.*, 
                           COUNT(pc.product_id) AS products_count
                    FROM collections c
                    LEFT JOIN product_collections pc ON c.id = pc.collection_id
                    WHERE c.id IN ($placeholders) AND c.is_active = 1
                    GROUP BY c.id
                    ORDER BY FIELD(c.id, $placeholders)";
            
            $params = array_merge($relatedIds, $relatedIds);
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Brand::getRelatedCollections error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get products for a brand with live filters and sorting
     */
    public static function getProducts($brandIdOrSlug, array $filters = [])
    {
        $db = self::getDB();
        if (!$db) return [];

        try {
            $where = ["p.status = 'published'"];
            $params = [];

            if (is_numeric($brandIdOrSlug)) {
                $where[] = "p.brand_id = ?";
                $params[] = (int)$brandIdOrSlug;
            } else {
                $where[] = "(b.slug = ? OR b.name = ?)";
                $params[] = $brandIdOrSlug;
                $params[] = $brandIdOrSlug;
            }

            // Collection filter
            if (!empty($filters['collection'])) {
                $col = $filters['collection'];
                if (is_numeric($col)) {
                    $where[] = "pc.collection_id = ?";
                    $params[] = (int)$col;
                } else {
                    $where[] = "(c.slug = ? OR c.name = ?)";
                    $params[] = $col;
                    $params[] = $col;
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
                $where[] = "(p.title LIKE ? OR p.name LIKE ? OR p.short_desc LIKE ?)";
                $kw = '%' . trim($filters['q']) . '%';
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
                $item['detected_puffs'] = Product::extractPuffs($item['title'] ?: ($item['name'] ?? ''), $item['spec_puffs'] ?? null);
            }
            unset($item);

            // Filter by puffs if requested
            if (!empty($filters['puffs']) && $filters['puffs'] !== 'all') {
                $targetPuff = (int)$filters['puffs'];
                if ($targetPuff > 0) {
                    $results = array_values(array_filter($results, function($p) use ($targetPuff) {
                        $pCount = (int)($p['detected_puffs'] ?? 0);
                        if ($targetPuff === 5000) return ($pCount >= 5000 && $pCount < 10000);
                        if ($targetPuff === 10000) return ($pCount >= 10000 && $pCount < 15000);
                        if ($targetPuff === 15000) return ($pCount >= 15000 && $pCount < 20000);
                        if ($targetPuff === 20000) return ($pCount >= 20000 && $pCount < 30000);
                        if ($targetPuff === 30000) return ($pCount >= 30000 && $pCount < 50000);
                        if ($targetPuff === 50000) return ($pCount >= 50000);
                        return false;
                    }));
                }
            }

            return $results;
        } catch (\Exception $e) {
            error_log("Brand::getProducts error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get price bounds (min and max) for this brand's products
     */
    public static function getPriceBounds($brandIdOrSlug)
    {
        $db = self::getDB();
        if (!$db) return ['min' => 0, 'max' => 100];

        try {
            $where = "p.status = 'published'";
            $params = [];
            if (is_numeric($brandIdOrSlug)) {
                $where .= " AND p.brand_id = ?";
                $params[] = (int)$brandIdOrSlug;
            } else {
                $where .= " AND (b.slug = ? OR b.name = ?)";
                $params[] = $brandIdOrSlug;
                $params[] = $brandIdOrSlug;
            }

            $sql = "SELECT MIN(p.base_price) AS min_price, MAX(p.base_price) AS max_price
                    FROM products p
                    LEFT JOIN brands b ON p.brand_id = b.id
                    WHERE $where";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'min' => !empty($res['min_price']) ? floor($res['min_price']) : 0,
                'max' => !empty($res['max_price']) ? ceil($res['max_price']) : 100,
            ];
        } catch (\Exception $e) {
            error_log("Brand::getPriceBounds error: " . $e->getMessage());
            return ['min' => 0, 'max' => 100];
        }
    }

    /**
     * Get collections/categories associated with this brand's products
     */
    public static function getCollectionsForBrand($brandIdOrSlug)
    {
        $db = self::getDB();
        if (!$db) return [];

        try {
            $where = "p.status = 'published' AND c.id IS NOT NULL";
            $params = [];
            if (is_numeric($brandIdOrSlug)) {
                $where .= " AND p.brand_id = ?";
                $params[] = (int)$brandIdOrSlug;
            } else {
                $where .= " AND (b.slug = ? OR b.name = ?)";
                $params[] = $brandIdOrSlug;
                $params[] = $brandIdOrSlug;
            }

            $sql = "SELECT c.id, c.name, c.slug, COUNT(p.id) AS products_count
                    FROM collections c
                    JOIN product_collections pc ON c.id = pc.collection_id
                    JOIN products p ON pc.product_id = p.id
                    LEFT JOIN brands b ON p.brand_id = b.id
                    WHERE $where
                    GROUP BY c.id
                    ORDER BY c.sort_order ASC, c.name ASC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Brand::getCollectionsForBrand error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Create a new brand
     */
    public static function create(array $data)
    {
        $db = self::getDB();
        if (!$db) return false;

        $name = trim($data['name'] ?? '');
        if (empty($name)) return false;

        $slug = trim($data['slug'] ?? '');
        if (empty($slug)) {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
            $slug = trim($slug, '-');
        }

        // Check for unique slug
        $baseSlug = $slug;
        $counter = 1;
        while (true) {
            $checkStmt = $db->prepare("SELECT id FROM brands WHERE slug = ? LIMIT 1");
            $checkStmt->execute([$slug]);
            if (!$checkStmt->fetch()) break;
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $logoUrl = isset($data['logo_url']) ? trim($data['logo_url']) : (isset($data['logo']) ? trim($data['logo']) : '');
        $headerImageUrl = isset($data['header_image_url']) ? trim($data['header_image_url']) : null;
        $shortDescription = trim($data['short_description'] ?? '');
        $description = trim($data['description'] ?? '');
        $longDescription = trim($data['long_description'] ?? '');
        $websiteUrl = trim($data['website_url'] ?? '');
        $metaTitle = trim($data['meta_title'] ?? ($name . ' Official Store | New Vape'));
        $metaDesc = trim($data['meta_desc'] ?? ($shortDescription ?: $description));
        $headerCollectionIds = isset($data['header_collection_ids']) ? (is_array($data['header_collection_ids']) ? json_encode(array_values(array_filter(array_map('intval', $data['header_collection_ids'])))) : trim($data['header_collection_ids'])) : null;
        $relatedCollectionIds = isset($data['related_collection_ids']) ? (is_array($data['related_collection_ids']) ? json_encode(array_values(array_filter(array_map('intval', $data['related_collection_ids'])))) : trim($data['related_collection_ids'])) : null;
        $promoBannerImage = isset($data['promo_banner_image']) ? trim($data['promo_banner_image']) : null;
        $promoBannerLink = isset($data['promo_banner_link']) ? trim($data['promo_banner_link']) : null;
        $sortOrder = isset($data['sort_order']) ? (int)$data['sort_order'] : 0;
        $isActive = isset($data['is_active']) ? (int)$data['is_active'] : 1;

        try {
            $sql = "INSERT INTO brands 
                    (name, slug, logo_url, header_image_url, short_description, description, long_description, 
                     header_collection_ids, related_collection_ids, promo_banner_image, promo_banner_link, 
                     website_url, meta_title, meta_desc, sort_order, is_active)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $name,
                $slug,
                $logoUrl,
                $headerImageUrl,
                $shortDescription,
                $description,
                $longDescription,
                $headerCollectionIds,
                $relatedCollectionIds,
                $promoBannerImage,
                $promoBannerLink,
                $websiteUrl,
                $metaTitle,
                $metaDesc,
                $sortOrder,
                $isActive
            ]);

            return (int)$db->lastInsertId();
        } catch (\Exception $e) {
            error_log("Brand::create() error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update an existing brand
     */
    public static function update($id, array $data)
    {
        $db = self::getDB();
        if (!$db) return false;

        $name = trim($data['name'] ?? '');
        if (empty($name)) return false;

        $slug = trim($data['slug'] ?? '');
        if (empty($slug)) {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
            $slug = trim($slug, '-');
        }

        // Unique slug check excluding current record
        $baseSlug = $slug;
        $counter = 1;
        while (true) {
            $checkStmt = $db->prepare("SELECT id FROM brands WHERE slug = ? AND id != ? LIMIT 1");
            $checkStmt->execute([$slug, (int)$id]);
            if (!$checkStmt->fetch()) break;
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $fields = [
            'name = ?',
            'slug = ?'
        ];
        $params = [$name, $slug];

        if (array_key_exists('logo_url', $data) || array_key_exists('logo', $data)) {
            $fields[] = 'logo_url = ?';
            $params[] = array_key_exists('logo_url', $data) ? trim($data['logo_url']) : trim($data['logo']);
        }
        if (array_key_exists('header_image_url', $data)) {
            $fields[] = 'header_image_url = ?';
            $params[] = !empty($data['header_image_url']) ? trim($data['header_image_url']) : null;
        }
        if (array_key_exists('short_description', $data)) {
            $fields[] = 'short_description = ?';
            $params[] = trim($data['short_description']);
        }
        if (array_key_exists('description', $data)) {
            $fields[] = 'description = ?';
            $params[] = trim($data['description']);
        }
        if (array_key_exists('long_description', $data)) {
            $fields[] = 'long_description = ?';
            $params[] = trim($data['long_description']);
        }
        if (array_key_exists('header_collection_ids', $data)) {
            $fields[] = 'header_collection_ids = ?';
            $raw = $data['header_collection_ids'];
            $params[] = is_array($raw) ? json_encode(array_values(array_filter(array_map('intval', $raw)))) : $raw;
        }
        if (array_key_exists('related_collection_ids', $data)) {
            $fields[] = 'related_collection_ids = ?';
            $raw = $data['related_collection_ids'];
            $params[] = is_array($raw) ? json_encode(array_values(array_filter(array_map('intval', $raw)))) : $raw;
        }
        if (array_key_exists('promo_banner_image', $data)) {
            $fields[] = 'promo_banner_image = ?';
            $params[] = !empty($data['promo_banner_image']) ? trim($data['promo_banner_image']) : null;
        }
        if (array_key_exists('promo_banner_link', $data)) {
            $fields[] = 'promo_banner_link = ?';
            $params[] = !empty($data['promo_banner_link']) ? trim($data['promo_banner_link']) : null;
        }
        if (array_key_exists('website_url', $data)) {
            $fields[] = 'website_url = ?';
            $params[] = trim($data['website_url']);
        }
        if (array_key_exists('meta_title', $data)) {
            $fields[] = 'meta_title = ?';
            $params[] = trim($data['meta_title']);
        }
        if (array_key_exists('meta_desc', $data)) {
            $fields[] = 'meta_desc = ?';
            $params[] = trim($data['meta_desc']);
        }
        if (array_key_exists('sort_order', $data)) {
            $fields[] = 'sort_order = ?';
            $params[] = (int)$data['sort_order'];
        }
        if (array_key_exists('is_active', $data)) {
            $fields[] = 'is_active = ?';
            $params[] = (int)$data['is_active'];
        }

        $params[] = (int)$id;
        $sql = "UPDATE brands SET " . implode(', ', $fields) . " WHERE id = ?";

        try {
            $stmt = $db->prepare($sql);
            return $stmt->execute($params);
        } catch (\Exception $e) {
            error_log("Brand::update() error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete brand and unlink attached products
     */
    public static function delete($id)
    {
        $db = self::getDB();
        if (!$db) return false;

        try {
            // Unlink products associated with this brand
            $unlinkStmt = $db->prepare("UPDATE products SET brand_id = NULL WHERE brand_id = ?");
            $unlinkStmt->execute([(int)$id]);

            // Delete brand record
            $stmt = $db->prepare("DELETE FROM brands WHERE id = ?");
            return $stmt->execute([(int)$id]);
        } catch (\Exception $e) {
            error_log("Brand::delete() error: " . $e->getMessage());
            return false;
        }
    }
}
