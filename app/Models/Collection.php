<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Collection extends Model
{
    /**
     * Get all collections with dynamic products count and parent name
     */
    public static function all()
    {
        $db = self::getDB();
        if (!$db) return [];

        try {
            self::ensureLongDescriptionColumn($db);
            $sql = "SELECT c.*, 
                           COUNT(pc.product_id) AS products_count, 
                           p.name AS parent_name,
                           p.slug AS parent_slug,
                           IF(c.is_active = 1, 'Active', 'Inactive') AS status_label
                    FROM collections c
                    LEFT JOIN product_collections pc ON c.id = pc.collection_id
                    LEFT JOIN collections p ON c.parent_id = p.id
                    GROUP BY c.id
                    ORDER BY c.sort_order ASC, c.id ASC";
            
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Collection::all() error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Find a single collection by ID with product count and parent info
     */
    public static function find($id)
    {
        $db = self::getDB();
        if (!$db) return null;

        try {
            $sql = "SELECT c.*, 
                           COUNT(pc.product_id) AS products_count, 
                           p.name AS parent_name,
                           p.slug AS parent_slug,
                           IF(c.is_active = 1, 'Active', 'Inactive') AS status_label
                    FROM collections c
                    LEFT JOIN product_collections pc ON c.id = pc.collection_id
                    LEFT JOIN collections p ON c.parent_id = p.id
                    WHERE c.id = ?
                    GROUP BY c.id
                    LIMIT 1";

            $stmt = $db->prepare($sql);
            $stmt->execute([(int)$id]);
            $collection = $stmt->fetch(PDO::FETCH_ASSOC);
            return $collection ?: null;
        } catch (\Exception $e) {
            error_log("Collection::find() error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Find a collection by slug with parent information
     */
    public static function findBySlug($slug)
    {
        $db = self::getDB();
        if (!$db) return null;

        try {
            self::ensureLongDescriptionColumn($db);
            $cleanSlug = trim($slug, '/');
            $stmt = $db->prepare("SELECT c.*, 
                                         p.name AS parent_name, 
                                         p.slug AS parent_slug 
                                  FROM collections c 
                                  LEFT JOIN collections p ON c.parent_id = p.id 
                                  WHERE c.slug = ? 
                                     OR c.custom_url_path = ? 
                                     OR c.custom_url_path = ? 
                                     OR c.custom_url_path = ? 
                                  LIMIT 1");
            $stmt->execute([
                $cleanSlug, 
                '/collections/' . $cleanSlug,
                '/' . $cleanSlug,
                $cleanSlug
            ]);
            $collection = $stmt->fetch(PDO::FETCH_ASSOC);
            return $collection ?: null;
        } catch (\Exception $e) {
            error_log("Collection::findBySlug() error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get the clean, canonical SEO URL for a collection
     * If collection has a parent: /{parent-slug}/{child-slug}
     * If collection is top-level: /{slug}
     */
    public static function getUrl($collectionOrSlug)
    {
        if (empty($collectionOrSlug)) return url('collections');

        if (is_string($collectionOrSlug)) {
            if ($collectionOrSlug === 'all' || $collectionOrSlug === 'shop') {
                return url('collections');
            }
            $collection = self::findBySlug($collectionOrSlug);
            if (!$collection && is_numeric($collectionOrSlug)) {
                $collection = self::find($collectionOrSlug);
            }
        } elseif (is_array($collectionOrSlug)) {
            $collection = $collectionOrSlug;
        } else {
            return url('collections');
        }

        if (!$collection || empty($collection['slug'])) {
            return url('collections');
        }

        if ($collection['slug'] === 'all' || $collection['slug'] === 'shop') {
            return url('collections');
        }

        // Check if collection has a parent
        $parentSlug = null;
        if (!empty($collection['parent_slug'])) {
            $parentSlug = $collection['parent_slug'];
        } elseif (!empty($collection['parent_id'])) {
            $parent = self::find($collection['parent_id']);
            if ($parent && !empty($parent['slug'])) {
                $parentSlug = $parent['slug'];
            }
        }

        if (!empty($parentSlug)) {
            return url(urlencode($parentSlug) . '/' . urlencode($collection['slug']));
        }

        return url(urlencode($collection['slug']));
    }

    /**
     * Get parent collections list for dropdown selector
     */
    public static function parents($excludeId = null)
    {
        $db = self::getDB();
        if (!$db) return [];

        try {
            if ($excludeId) {
                $stmt = $db->prepare("SELECT id, name, slug FROM collections WHERE id != ? AND (parent_id IS NULL OR parent_id = 0) ORDER BY name ASC");
                $stmt->execute([(int)$excludeId]);
            } else {
                $stmt = $db->query("SELECT id, name, slug FROM collections WHERE parent_id IS NULL OR parent_id = 0 ORDER BY name ASC");
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Collection::parents() error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Create a new collection in the database
     */
    public static function create(array $data)
    {
        $db = self::getDB();
        if (!$db) return false;

        $name = trim($data['name'] ?? $data['title'] ?? '');
        if (empty($name)) return false;

        $slug = trim($data['slug'] ?? '');
        if (empty($slug)) {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
            $slug = trim($slug, '-');
        }

        $parentId = !empty($data['parent_id']) ? (int)$data['parent_id'] : null;

        // Resolve parent slug to build clean hierarchical URL path
        $parentSlug = null;
        if ($parentId) {
            $pStmt = $db->prepare("SELECT slug FROM collections WHERE id = ? LIMIT 1");
            $pStmt->execute([(int)$parentId]);
            $pRow = $pStmt->fetch(PDO::FETCH_ASSOC);
            if ($pRow && !empty($pRow['slug'])) {
                $parentSlug = $pRow['slug'];
            }
        }

        $customUrlPath = trim($data['custom_url_path'] ?? '');
        if (empty($customUrlPath) || strpos($customUrlPath, '/collections/') === 0) {
            $customUrlPath = $parentSlug ? ('/' . $parentSlug . '/' . $slug) : ('/' . $slug);
        }
        $headerImageUrl = isset($data['header_image_url']) ? trim($data['header_image_url']) : (isset($data['image']) ? trim($data['image']) : '');
        $shortDescription = trim($data['short_description'] ?? $data['description'] ?? '');
        $longDescription = isset($data['long_description']) ? trim($data['long_description']) : null;
        $metaTitle = trim($data['meta_title'] ?? $data['seo_title'] ?? ($name . ' | Infinity Vape Shop'));
        $metaDesc = trim($data['meta_desc'] ?? $data['seo_description'] ?? $shortDescription);
        $sortOrder = isset($data['sort_order']) ? (int)$data['sort_order'] : 0;
        $isActive = isset($data['is_active']) ? (int)$data['is_active'] : 1;

        $relatedVal = null;
        if (isset($data['related_collection_ids'])) {
            $relatedVal = is_array($data['related_collection_ids']) 
                ? json_encode(array_values(array_filter(array_map('intval', $data['related_collection_ids'])))) 
                : $data['related_collection_ids'];
        }

        $headerVal = null;
        if (isset($data['header_collection_ids'])) {
            $headerVal = is_array($data['header_collection_ids']) 
                ? json_encode(array_values(array_filter(array_map('intval', $data['header_collection_ids'])))) 
                : $data['header_collection_ids'];
        }

        try {
            self::ensureLongDescriptionColumn($db);
            self::ensureRelatedCollectionsColumn($db);
            self::ensureHeaderCollectionsColumn($db);

            $sql = "INSERT INTO collections 
                    (parent_id, name, slug, custom_url_path, header_image_url, short_description, long_description, related_collection_ids, header_collection_ids, meta_title, meta_desc, sort_order, is_active)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $parentId,
                $name,
                $slug,
                $customUrlPath,
                $headerImageUrl,
                $shortDescription,
                $longDescription,
                $relatedVal,
                $headerVal,
                $metaTitle,
                $metaDesc,
                $sortOrder,
                $isActive
            ]);

            return (int)$db->lastInsertId();
        } catch (\Exception $e) {
            error_log("Collection::create() error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update an existing collection in the database
     */
    public static function update($id, array $data)
    {
        $db = self::getDB();
        if (!$db) return false;

        $name = trim($data['name'] ?? $data['title'] ?? '');
        if (empty($name)) return false;

        $slug = trim($data['slug'] ?? '');
        if (empty($slug)) {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
            $slug = trim($slug, '-');
        }

        $parentId = !empty($data['parent_id']) ? (int)$data['parent_id'] : null;

        // Resolve parent slug to build clean hierarchical URL path
        $parentSlug = null;
        if ($parentId) {
            $pStmt = $db->prepare("SELECT slug FROM collections WHERE id = ? LIMIT 1");
            $pStmt->execute([(int)$parentId]);
            $pRow = $pStmt->fetch(PDO::FETCH_ASSOC);
            if ($pRow && !empty($pRow['slug'])) {
                $parentSlug = $pRow['slug'];
            }
        }

        $customUrlPath = trim($data['custom_url_path'] ?? '');
        if (empty($customUrlPath) || strpos($customUrlPath, '/collections/') === 0) {
            $customUrlPath = $parentSlug ? ('/' . $parentSlug . '/' . $slug) : ('/' . $slug);
        }
        $hasImageKey = array_key_exists('header_image_url', $data) || array_key_exists('image', $data);
        $headerImageUrl = array_key_exists('header_image_url', $data) ? trim($data['header_image_url']) : (array_key_exists('image', $data) ? trim($data['image']) : null);
        $shortDescription = trim($data['short_description'] ?? $data['description'] ?? '');
        $hasLongDescKey = array_key_exists('long_description', $data);
        $longDescription = $hasLongDescKey ? trim($data['long_description'] ?? '') : null;
        $metaTitle = trim($data['meta_title'] ?? $data['seo_title'] ?? ($name . ' | Infinity Vape Shop'));
        $metaDesc = trim($data['meta_desc'] ?? $data['seo_description'] ?? $shortDescription);
        $sortOrder = isset($data['sort_order']) ? (int)$data['sort_order'] : 0;
        $isActive = isset($data['is_active']) ? (int)$data['is_active'] : 1;

        try {
            self::ensureLongDescriptionColumn($db);
            self::ensureRelatedCollectionsColumn($db);
            self::ensureHeaderCollectionsColumn($db);

            $fields = [
                'parent_id = ?' => $parentId,
                'name = ?' => $name,
                'slug = ?' => $slug,
                'custom_url_path = ?' => $customUrlPath,
                'short_description = ?' => $shortDescription,
                'meta_title = ?' => $metaTitle,
                'meta_desc = ?' => $metaDesc,
                'sort_order = ?' => $sortOrder,
                'is_active = ?' => $isActive
            ];

            if ($hasImageKey) {
                $fields['header_image_url = ?'] = $headerImageUrl;
            }
            if ($hasLongDescKey) {
                $fields['long_description = ?'] = $longDescription;
            }
            if (array_key_exists('related_collection_ids', $data)) {
                $relVal = $data['related_collection_ids'];
                if (is_array($relVal)) {
                    $relVal = json_encode(array_values(array_filter(array_map('intval', $relVal))));
                }
                $fields['related_collection_ids = ?'] = $relVal;
            }
            if (array_key_exists('header_collection_ids', $data)) {
                $headVal = $data['header_collection_ids'];
                if (is_array($headVal)) {
                    $headVal = json_encode(array_values(array_filter(array_map('intval', $headVal))));
                }
                $fields['header_collection_ids = ?'] = $headVal;
            }

            $setClause = implode(', ', array_keys($fields));
            $params = array_values($fields);
            $params[] = (int)$id;

            $sql = "UPDATE collections SET {$setClause} WHERE id = ?";
            $stmt = $db->prepare($sql);
            return $stmt->execute($params);
        } catch (\Exception $e) {
            error_log("Collection::update() error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ensure long_description column exists in collections table and seed default for Disposables
     */
    public static function ensureLongDescriptionColumn($db = null)
    {
        $db = $db ?: self::getDB();
        if (!$db) return;
        static $checked = false;
        if ($checked) return;

        try {
            $cols = $db->query("SHOW COLUMNS FROM collections LIKE 'long_description'")->fetchAll();
            if (empty($cols)) {
                $db->exec("ALTER TABLE collections ADD COLUMN long_description LONGTEXT DEFAULT NULL AFTER short_description");
            }

            // Seed sample long description for Disposable Vapes if empty
            $row = $db->query("SELECT id, long_description FROM collections WHERE slug = 'disposables' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            if (!empty($row) && empty($row['long_description'])) {
                $sampleLong = "<p>Disposable vapes represent the ultimate in convenience, consistency, and portability for adult vapers. Featuring pre-filled salt nicotine e-liquids, integrated high-efficiency lithium rechargeable batteries, and state-of-the-art dual mesh coils, today's smart disposables deliver anywhere from 5,000 up to 50,000 puffs with zero maintenance required.</p><p>Explore our carefully curated selection of certified authentic disposables from premier manufacturers including Lost Mary, Geek Bar, RAZ, HQD, and more. Whether you prefer icy fruit blends, classic Virginia tobaccos, or tart beverage profiles, New Vape guarantees genuine products verified with master distributor scratch-off security codes and fast same-day shipping on orders over \$150.</p>";
                $upd = $db->prepare("UPDATE collections SET long_description = ? WHERE id = ?");
                $upd->execute([$sampleLong, (int)$row['id']]);
            }

            $checked = true;
        } catch (\Exception $e) {
            error_log("ensureLongDescriptionColumn error: " . $e->getMessage());
        }
    }

    /**
     * Ensure related_collection_ids column exists in collections table
     */
    public static function ensureRelatedCollectionsColumn($db = null)
    {
        $db = $db ?: self::getDB();
        if (!$db) return;
        static $checked = false;
        if ($checked) return;

        try {
            $cols = $db->query("SHOW COLUMNS FROM collections LIKE 'related_collection_ids'")->fetchAll();
            if (empty($cols)) {
                $db->exec("ALTER TABLE collections ADD COLUMN related_collection_ids TEXT DEFAULT NULL AFTER long_description");
            }
            $checked = true;
        } catch (\Exception $e) {
            error_log("ensureRelatedCollectionsColumn error: " . $e->getMessage());
        }
    }

    /**
     * Get related collections for storefront cross-sell display
     */
    public static function getRelatedCollections($collection, $limit = 8)
    {
        $db = self::getDB();
        if (!$db) return [];

        self::ensureRelatedCollectionsColumn($db);

        // Check if manual related collection IDs are configured
        $relatedIds = [];
        if (is_array($collection) && !empty($collection['related_collection_ids'])) {
            $raw = $collection['related_collection_ids'];
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

        // If no related collections were marked by admin, return empty (do not show default fallback)
        if (empty($relatedIds)) {
            return [];
        }

        try {
            // Fetch only the checkmarked related collections in preserved order
            $placeholders = implode(',', array_fill(0, count($relatedIds), '?'));
            $sql = "SELECT c.*, 
                           COUNT(pc.product_id) AS products_count
                    FROM collections c
                    LEFT JOIN product_collections pc ON c.id = pc.collection_id
                    WHERE c.id IN ($placeholders) AND c.is_active = 1
                    GROUP BY c.id
                    ORDER BY FIELD(c.id, $placeholders)";
            
            // Duplicate parameters for FIELD()
            $params = array_merge($relatedIds, $relatedIds);
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Collection::getRelatedCollections error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Ensure header_collection_ids column exists in collections table
     */
    public static function ensureHeaderCollectionsColumn($db = null)
    {
        $db = $db ?: self::getDB();
        if (!$db) return;
        static $checked = false;
        if ($checked) return;

        try {
            $cols = $db->query("SHOW COLUMNS FROM collections LIKE 'header_collection_ids'")->fetchAll();
            if (empty($cols)) {
                $db->exec("ALTER TABLE collections ADD COLUMN header_collection_ids TEXT DEFAULT NULL AFTER related_collection_ids");
            }
            $checked = true;
        } catch (\Exception $e) {
            error_log("ensureHeaderCollectionsColumn error: " . $e->getMessage());
        }
    }

    /**
     * Get header banner navigation collections (checkmarked by admin)
     */
    public static function getHeaderCollections($collection, $limit = 20)
    {
        $db = self::getDB();
        if (!$db) return [];

        self::ensureHeaderCollectionsColumn($db);

        $headerIds = [];
        if (is_array($collection) && !empty($collection['header_collection_ids'])) {
            $raw = $collection['header_collection_ids'];
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

        // If no header collections were marked by admin, return empty
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
            error_log("Collection::getHeaderCollections error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Delete a collection from the database
     */
    public static function delete($id)
    {
        $db = self::getDB();
        if (!$db) return false;

        try {
            // Delete product associations first (handled by foreign key cascade, or explicit)
            $db->prepare("DELETE FROM product_collections WHERE collection_id = ?")->execute([(int)$id]);
            
            // Reassign any child collections to null parent
            $db->prepare("UPDATE collections SET parent_id = NULL WHERE parent_id = ?")->execute([(int)$id]);

            // Delete the collection
            $stmt = $db->prepare("DELETE FROM collections WHERE id = ?");
            return $stmt->execute([(int)$id]);
        } catch (\Exception $e) {
            error_log("Collection::delete() error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get main featured vape categories for homepage and storefront display
     */
    public static function getMainCategories($limit = 4)
    {
        $db = self::getDB();
        if (!$db) return [];

        try {
            $sql = "SELECT c.*, 
                           COUNT(pc.product_id) AS products_count 
                    FROM collections c
                    LEFT JOIN product_collections pc ON c.id = pc.collection_id
                    WHERE c.is_active = 1 AND (c.parent_id IS NULL OR c.parent_id = 0)
                    GROUP BY c.id
                    ORDER BY c.sort_order ASC, c.id ASC";
            
            if ($limit !== null && $limit > 0) {
                $sql .= " LIMIT " . (int)$limit;
            }

            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Collection::getMainCategories() error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get FAQs for a specific collection
     */
    public static function getFaqs($collectionId)
    {
        $db = self::getDB();
        if (!$db || empty($collectionId)) return [];

        try {
            $stmt = $db->prepare("SELECT id, question, answer, sort_order FROM collection_faqs WHERE collection_id = ? ORDER BY sort_order ASC, id ASC");
            $stmt->execute([(int)$collectionId]);
            $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($faqs)) {
                return $faqs;
            }
        } catch (\Exception $e) {
            error_log("Collection::getFaqs() error: " . $e->getMessage());
        }
        return [];
    }

    /**
     * Save/Replace FAQs for a collection
     */
    public static function saveFaqs($collectionId, array $faqs)
    {
        $db = self::getDB();
        if (!$db || empty($collectionId)) return false;

        try {
            $db->prepare("DELETE FROM collection_faqs WHERE collection_id = ?")->execute([(int)$collectionId]);

            if (!empty($faqs)) {
                $stmt = $db->prepare("INSERT INTO collection_faqs (collection_id, question, answer, sort_order) VALUES (?, ?, ?, ?)");
                $order = 1;
                foreach ($faqs as $faq) {
                    $question = trim($faq['question'] ?? '');
                    $answer = trim($faq['answer'] ?? '');
                    if (!empty($question) && !empty($answer)) {
                        $stmt->execute([(int)$collectionId, $question, $answer, $order++]);
                    }
                }
            }
            return true;
        } catch (\Exception $e) {
            error_log("Collection::saveFaqs() error: " . $e->getMessage());
            return false;
        }
    }
}
