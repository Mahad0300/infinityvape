<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class BlogPost extends Model
{
    /**
     * Get all blog posts with category
     */
    public static function all($limit = 100)
    {
        $db = self::getDB();
        if (!$db) return [];

        try {
            // Ensure author name is Infinity Vape Shop across legacy records
            static $authorUpdated = false;
            if (!$authorUpdated) {
                @$db->exec("UPDATE blog_posts SET author_name = 'Infinity Vape Shop' WHERE author_name = 'New Vape' OR author_name IS NULL OR author_name = ''");
                $authorUpdated = true;
            }

            $stmt = $db->query("
                SELECT b.*, 
                    GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') as category_name,
                    GROUP_CONCAT(DISTINCT c.slug SEPARATOR ', ') as category_slug
                FROM blog_posts b 
                LEFT JOIN blog_categories c ON (FIND_IN_SET(c.id, b.category_id) > 0 OR b.category_id = c.id) 
                GROUP BY b.id
                ORDER BY b.published_at DESC
                LIMIT " . (int)$limit
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("BlogPost::all() error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get latest active blog posts for storefront
     */
    public static function latest($limit = 4)
    {
        $db = self::getDB();
        if (!$db) return [];

        try {
            // Ensure author name is Infinity Vape Shop across legacy records
            static $authorUpdated = false;
            if (!$authorUpdated) {
                @$db->exec("UPDATE blog_posts SET author_name = 'Infinity Vape Shop' WHERE author_name = 'New Vape' OR author_name IS NULL OR author_name = ''");
                $authorUpdated = true;
            }

            $stmt = $db->query("
                SELECT b.*, 
                    GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') as category_name,
                    GROUP_CONCAT(DISTINCT c.slug SEPARATOR ', ') as category_slug
                FROM blog_posts b 
                LEFT JOIN blog_categories c ON (FIND_IN_SET(c.id, b.category_id) > 0 OR b.category_id = c.id) 
                WHERE b.is_active = 1
                GROUP BY b.id
                ORDER BY b.published_at DESC, b.id DESC
                LIMIT " . (int)$limit
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("BlogPost::latest() error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get active blog posts by category slugs with optional fallback
     */
    public static function getByCategorySlugs(array $categorySlugs, $limit = 4, $fallbackToAny = true)
    {
        $db = self::getDB();
        if (!$db || empty($categorySlugs)) return [];

        try {
            $placeholders = implode(',', array_fill(0, count($categorySlugs), '?'));
            $sql = "
                SELECT b.*, 
                    GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') as category_name,
                    GROUP_CONCAT(DISTINCT c.slug SEPARATOR ', ') as category_slug
                FROM blog_posts b 
                INNER JOIN blog_categories c ON (FIND_IN_SET(c.id, b.category_id) > 0 OR b.category_id = c.id) 
                WHERE b.is_active = 1 AND c.slug IN ($placeholders)
                GROUP BY b.id
                ORDER BY b.published_at DESC, b.id DESC
                LIMIT " . (int)$limit;
            
            $stmt = $db->prepare($sql);
            $stmt->execute(array_values($categorySlugs));
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // If we have fewer than limit and fallback is enabled, fill with other active posts
            if ($fallbackToAny && count($posts) < $limit) {
                $existingIds = array_column($posts, 'id');
                $excludeSql = !empty($existingIds) ? " AND b.id NOT IN (" . implode(',', array_map('intval', $existingIds)) . ")" : "";
                $needed = (int)$limit - count($posts);

                $fallbackSql = "
                    SELECT b.*, 
                        GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') as category_name,
                        GROUP_CONCAT(DISTINCT c.slug SEPARATOR ', ') as category_slug
                    FROM blog_posts b 
                    LEFT JOIN blog_categories c ON (FIND_IN_SET(c.id, b.category_id) > 0 OR b.category_id = c.id) 
                    WHERE b.is_active = 1 $excludeSql
                    GROUP BY b.id
                    ORDER BY b.published_at DESC, b.id DESC
                    LIMIT $needed";
                $fallbackStmt = $db->query($fallbackSql);
                $fallbackPosts = $fallbackStmt ? $fallbackStmt->fetchAll(PDO::FETCH_ASSOC) : [];
                $posts = array_merge($posts, $fallbackPosts);
            }

            return $posts;
        } catch (\Exception $e) {
            error_log("BlogPost::getByCategorySlugs() error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get active blog posts by Tag with pagination
     */
    public static function byTag($tag, $page = 1, $perPage = 12)
    {
        $db = self::getDB();
        if (!$db) return ['data' => [], 'total' => 0, 'current_page' => 1, 'last_page' => 1];

        try {
            $page = max(1, (int)$page);
            $perPage = max(1, (int)$perPage);
            $offset = ($page - 1) * $perPage;

            $normalizedTag = trim(str_replace(['-', '_'], ' ', $tag));
            $tagWildcard = '%' . $normalizedTag . '%';
            $slugWildcard = '%' . trim($tag) . '%';

            $whereSql = "WHERE b.is_active = 1 AND (b.tags LIKE ? OR b.tags LIKE ?)";
            $params = [$tagWildcard, $slugWildcard];

            $countStmt = $db->prepare("SELECT COUNT(*) FROM blog_posts b $whereSql");
            $countStmt->execute($params);
            $total = (int)$countStmt->fetchColumn();

            $sql = "
                SELECT b.*, 
                    GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') as category_name, 
                    GROUP_CONCAT(DISTINCT c.slug SEPARATOR ', ') as category_slug
                FROM blog_posts b 
                LEFT JOIN blog_categories c ON (FIND_IN_SET(c.id, b.category_id) > 0 OR b.category_id = c.id) 
                $whereSql
                GROUP BY b.id
                ORDER BY b.published_at DESC, b.id DESC
                LIMIT $perPage OFFSET $offset";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'data' => $data,
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => (int)ceil($total / $perPage) ?: 1
            ];
        } catch (\Exception $e) {
            error_log("BlogPost::byTag() error: " . $e->getMessage());
            return ['data' => [], 'total' => 0, 'current_page' => 1, 'last_page' => 1];
        }
    }

    /**
     * Get paginated posts by category slug or ID
     */
    public static function byCategory($categorySlugOrId, $page = 1, $perPage = 12)
    {
        $db = self::getDB();
        if (!$db) return ['category' => null, 'data' => [], 'total' => 0, 'current_page' => 1, 'last_page' => 1];

        try {
            $cat = self::findCategory($categorySlugOrId);
            if (!$cat) {
                return ['category' => null, 'data' => [], 'total' => 0, 'current_page' => 1, 'last_page' => 1];
            }

            $page = max(1, (int)$page);
            $perPage = max(1, (int)$perPage);
            $offset = ($page - 1) * $perPage;

            $catId = (int)$cat['id'];

            $countStmt = $db->prepare("SELECT COUNT(*) FROM blog_posts WHERE (FIND_IN_SET(?, category_id) > 0 OR category_id = ?) AND is_active = 1");
            $countStmt->execute([$catId, $catId]);
            $total = (int)$countStmt->fetchColumn();

            $sql = "
                SELECT b.*, 
                    GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') as category_name, 
                    GROUP_CONCAT(DISTINCT c.slug SEPARATOR ', ') as category_slug
                FROM blog_posts b 
                LEFT JOIN blog_categories c ON (FIND_IN_SET(c.id, b.category_id) > 0 OR b.category_id = c.id) 
                WHERE (FIND_IN_SET(?, b.category_id) > 0 OR b.category_id = ?) AND b.is_active = 1
                GROUP BY b.id
                ORDER BY b.published_at DESC, b.id DESC
                LIMIT $perPage OFFSET $offset";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$catId, $catId]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'category' => $cat,
                'data' => $data,
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => (int)ceil($total / $perPage) ?: 1
            ];
        } catch (\Exception $e) {
            error_log("BlogPost::byCategory() error: " . $e->getMessage());
            return ['category' => null, 'data' => [], 'total' => 0, 'current_page' => 1, 'last_page' => 1];
        }
    }

    /**
     * Get archive months with post count
     */
    public static function getArchiveMonths()
    {
        $db = self::getDB();
        if (!$db) return [];

        try {
            // Ensure author name is Infinity Vape Shop across legacy records
            static $authorUpdated = false;
            if (!$authorUpdated) {
                @$db->exec("UPDATE blog_posts SET author_name = 'Infinity Vape Shop' WHERE author_name = 'New Vape' OR author_name IS NULL OR author_name = ''");
                $authorUpdated = true;
            }

            $stmt = $db->query("
                SELECT 
                    DATE_FORMAT(published_at, '%Y-%m') as ym,
                    DATE_FORMAT(published_at, '%M %Y') as label,
                    COUNT(id) as count
                FROM blog_posts
                WHERE is_active = 1
                GROUP BY ym, label
                ORDER BY ym DESC
                LIMIT 12
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("BlogPost::getArchiveMonths() error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all unique tags from active blog posts
     */
    public static function getAllTags()
    {
        $db = self::getDB();
        if (!$db) return [];

        try {
            // Ensure author name is Infinity Vape Shop across legacy records
            static $authorUpdated = false;
            if (!$authorUpdated) {
                @$db->exec("UPDATE blog_posts SET author_name = 'Infinity Vape Shop' WHERE author_name = 'New Vape' OR author_name IS NULL OR author_name = ''");
                $authorUpdated = true;
            }

            $stmt = $db->query("SELECT tags FROM blog_posts WHERE is_active = 1 AND tags IS NOT NULL AND tags != ''");
            $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $tagList = [];
            foreach ($rows as $row) {
                $parts = explode(',', $row);
                foreach ($parts as $p) {
                    $p = trim($p);
                    if ($p !== '' && !in_array($p, $tagList)) {
                        $tagList[] = $p;
                    }
                }
            }
            return $tagList;
        } catch (\Exception $e) {
            error_log("BlogPost::getAllTags() error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Find single blog post by ID or Slug
     */
    public static function find($idOrSlug)
    {
        $db = self::getDB();
        if (!$db) return null;

        try {
            $isNumeric = is_numeric($idOrSlug);
            $sql = "SELECT b.*
                    FROM blog_posts b
                    WHERE " . ($isNumeric ? "b.id = ?" : "(b.slug = ? OR b.custom_url_path = ?)") . "
                    LIMIT 1";
            $stmt = $db->prepare($sql);
            if ($isNumeric) {
                $stmt->execute([(int)$idOrSlug]);
            } else {
                $stmt->execute([(string)$idOrSlug, (string)$idOrSlug]);
            }
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$post) return null;

            // Fetch attached categories
            $post['categories'] = [];
            $post['category_ids'] = [];
            if (!empty($post['category_id'])) {
                $rawIds = array_filter(array_map('trim', explode(',', (string)$post['category_id'])));
                if (!empty($rawIds)) {
                    $post['category_ids'] = array_map('intval', $rawIds);
                    $inPlaceholders = implode(',', array_fill(0, count($rawIds), '?'));
                    $catStmt = $db->prepare("SELECT id, name, slug FROM blog_categories WHERE id IN ($inPlaceholders) ORDER BY name ASC");
                    $catStmt->execute(array_values($rawIds));
                    $post['categories'] = $catStmt->fetchAll(PDO::FETCH_ASSOC);
                }
            }

            if (!empty($post['categories'])) {
                $post['category_name'] = implode(', ', array_column($post['categories'], 'name'));
                $post['category_slug'] = $post['categories'][0]['slug'] ?? 'vaping-guides';
            } else {
                $post['category_name'] = 'Vaping Guides';
                $post['category_slug'] = 'vaping-guides';
            }

            return $post;
        } catch (\Exception $e) {
            error_log("BlogPost::find() error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Paginated list for Admin with Search and Status filter
     */
    public static function paginate($page = 1, $perPage = 12, $search = '', $status = 'all', $categoryId = null)
    {
        $db = self::getDB();
        if (!$db) {
            return [
                'data' => [],
                'total' => 0,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => 1
            ];
        }

        try {
            $page = max(1, (int)$page);
            $perPage = max(1, (int)$perPage);
            $offset = ($page - 1) * $perPage;

            $where = [];
            $params = [];

            if (!empty($search)) {
                $where[] = "(b.title LIKE ? OR b.excerpt LIKE ? OR b.tags LIKE ?)";
                $params[] = '%' . $search . '%';
                $params[] = '%' . $search . '%';
                $params[] = '%' . $search . '%';
            }

            if ($status === 'active' || $status === 'visible') {
                $where[] = "b.is_active = 1";
            } elseif ($status === 'hidden' || $status === 'inactive') {
                $where[] = "b.is_active = 0";
            }

            if (!empty($categoryId) && is_numeric($categoryId)) {
                $where[] = "(FIND_IN_SET(?, b.category_id) > 0 OR b.category_id = ?)";
                $params[] = (int)$categoryId;
                $params[] = (int)$categoryId;
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            // Total count
            $countSql = "SELECT COUNT(*) FROM blog_posts b $whereClause";
            $countStmt = $db->prepare($countSql);
            $countStmt->execute($params);
            $total = (int)$countStmt->fetchColumn();

            // Fetch data
            $sql = "SELECT b.*, 
                        GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') as category_name, 
                        GROUP_CONCAT(DISTINCT c.slug SEPARATOR ', ') as category_slug
                    FROM blog_posts b 
                    LEFT JOIN blog_categories c ON (FIND_IN_SET(c.id, b.category_id) > 0 OR b.category_id = c.id) 
                    $whereClause
                    GROUP BY b.id
                    ORDER BY b.published_at DESC, b.id DESC
                    LIMIT $perPage OFFSET $offset";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'data' => $items,
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => (int)ceil($total / $perPage) ?: 1
            ];
        } catch (\Exception $e) {
            error_log("BlogPost::paginate() error: " . $e->getMessage());
            return [
                'data' => [],
                'total' => 0,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => 1
            ];
        }
    }

    /**
     * Create new blog post
     */
    public static function create(array $data)
    {
        $db = self::getDB();
        if (!$db) return false;

        try {
            $title = trim($data['title'] ?? '');
            if (empty($title)) return false;

            $slug = !empty($data['slug']) ? trim($data['slug']) : strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $title));
            $slug = trim($slug, '-');

            $customUrl = !empty($data['custom_url_path']) ? trim($data['custom_url_path']) : '/blog/' . $slug;

            $catIdVal = null;
            if (isset($data['category_id']) && $data['category_id'] !== '' && $data['category_id'] !== null) {
                if (is_array($data['category_id'])) {
                    $cleaned = array_filter(array_map('intval', $data['category_id']));
                    $catIdVal = !empty($cleaned) ? implode(',', $cleaned) : null;
                } else {
                    $catIdVal = trim((string)$data['category_id']);
                }
            }

            $sql = "INSERT INTO blog_posts (
                category_id, author_id, title, slug, custom_url_path,
                featured_image_url, author_name, excerpt, content,
                tags, published_at, meta_title, meta_desc, is_active
            ) VALUES (
                :category_id, :author_id, :title, :slug, :custom_url_path,
                :featured_image_url, :author_name, :excerpt, :content,
                :tags, :published_at, :meta_title, :meta_desc, :is_active
            )";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':category_id'        => $catIdVal,
                ':author_id'          => !empty($data['author_id']) ? (int)$data['author_id'] : null,
                ':title'              => $title,
                ':slug'               => $slug,
                ':custom_url_path'    => $customUrl,
                ':featured_image_url' => !empty($data['featured_image_url']) ? $data['featured_image_url'] : 'assets/images/blog-1.jpg',
                ':author_name'        => !empty($data['author_name']) ? $data['author_name'] : 'Infinity Vape Shop',
                ':excerpt'            => $data['excerpt'] ?? '',
                ':content'            => $data['content'] ?? '',
                ':tags'               => $data['tags'] ?? '',
                ':published_at'       => !empty($data['published_at']) ? $data['published_at'] : date('Y-m-d H:i:s'),
                ':meta_title'         => $data['meta_title'] ?? null,
                ':meta_desc'          => $data['meta_desc'] ?? null,
                ':is_active'          => isset($data['is_active']) ? (int)$data['is_active'] : 1
            ]);

            return $db->lastInsertId();
        } catch (\Exception $e) {
            error_log("BlogPost::create() error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update existing blog post
     */
    public static function update($id, array $data)
    {
        $db = self::getDB();
        if (!$db) return false;

        try {
            if (array_key_exists('category_id', $data)) {
                if (is_array($data['category_id'])) {
                    $cleaned = array_filter(array_map('intval', $data['category_id']));
                    $data['category_id'] = !empty($cleaned) ? implode(',', $cleaned) : null;
                } elseif ($data['category_id'] !== null && $data['category_id'] !== '') {
                    $data['category_id'] = trim((string)$data['category_id']);
                } else {
                    $data['category_id'] = null;
                }
            }

            $fields = [];
            $params = [':id' => (int)$id];

            $allowed = [
                'category_id', 'author_id', 'title', 'slug', 'custom_url_path',
                'featured_image_url', 'author_name', 'excerpt', 'content',
                'tags', 'published_at', 'meta_title', 'meta_desc', 'is_active'
            ];

            foreach ($allowed as $field) {
                if (array_key_exists($field, $data)) {
                    $fields[] = "`$field` = :$field";
                    $params[":$field"] = $data[$field];
                }
            }

            if (empty($fields)) return false;

            $sql = "UPDATE blog_posts SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $db->prepare($sql);
            return $stmt->execute($params);
        } catch (\Exception $e) {
            error_log("BlogPost::update() error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete blog post
     */
    public static function delete($id)
    {
        $db = self::getDB();
        if (!$db) return false;

        try {
            $stmt = $db->prepare("DELETE FROM blog_posts WHERE id = ?");
            return $stmt->execute([(int)$id]);
        } catch (\Exception $e) {
            error_log("BlogPost::delete() error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get total count of unique active blog posts
     */
    public static function countActive()
    {
        $db = self::getDB();
        if (!$db) return 0;
        try {
            return (int)$db->query("SELECT COUNT(*) FROM blog_posts WHERE is_active = 1")->fetchColumn();
        } catch (\Exception $e) {
            error_log("BlogPost::countActive() error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get all categories with post counts
     */
    public static function getCategories()
    {
        $db = self::getDB();
        if (!$db) return [];

        try {
            // Ensure author name is Infinity Vape Shop across legacy records
            static $authorUpdated = false;
            if (!$authorUpdated) {
                @$db->exec("UPDATE blog_posts SET author_name = 'Infinity Vape Shop' WHERE author_name = 'New Vape' OR author_name IS NULL OR author_name = ''");
                $authorUpdated = true;
            }

            $stmt = $db->query("
                SELECT c.*, 
                    (SELECT COUNT(*) FROM blog_posts b WHERE (FIND_IN_SET(c.id, b.category_id) > 0 OR b.category_id = c.id) AND b.is_active = 1) as post_count 
                FROM blog_categories c 
                ORDER BY c.name ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("BlogPost::getCategories() error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Find category by ID or slug
     */
    public static function findCategory($idOrSlug)
    {
        $db = self::getDB();
        if (!$db) return null;

        try {
            $isNumeric = is_numeric($idOrSlug);
            $stmt = $db->prepare("SELECT * FROM blog_categories WHERE " . ($isNumeric ? "id = ?" : "slug = ?") . " LIMIT 1");
            $stmt->execute([$idOrSlug]);
            $cat = $stmt->fetch(PDO::FETCH_ASSOC);
            return $cat ?: null;
        } catch (\Exception $e) {
            error_log("BlogPost::findCategory() error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create category
     */
    public static function createCategory(array $data)
    {
        $db = self::getDB();
        if (!$db) return false;

        try {
            $name = trim($data['name'] ?? '');
            if (empty($name)) return false;

            $slug = !empty($data['slug']) ? trim($data['slug']) : strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
            $slug = trim($slug, '-');

            $stmt = $db->prepare("INSERT INTO blog_categories (name, slug, description) VALUES (?, ?, ?)");
            $stmt->execute([$name, $slug, $data['description'] ?? null]);
            return $db->lastInsertId();
        } catch (\Exception $e) {
            error_log("BlogPost::createCategory() error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update category
     */
    public static function updateCategory($id, array $data)
    {
        $db = self::getDB();
        if (!$db) return false;

        try {
            $name = trim($data['name'] ?? '');
            if (empty($name)) return false;

            $slug = !empty($data['slug']) ? trim($data['slug']) : strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
            $slug = trim($slug, '-');

            $stmt = $db->prepare("UPDATE blog_categories SET name = ?, slug = ?, description = ? WHERE id = ?");
            return $stmt->execute([$name, $slug, $data['description'] ?? null, (int)$id]);
        } catch (\Exception $e) {
            error_log("BlogPost::updateCategory() error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete category
     */
    public static function deleteCategory($id)
    {
        $db = self::getDB();
        if (!$db) return false;

        try {
            // Remove deleted category ID from comma-separated category_id in blog_posts
            $posts = $db->query("SELECT id, category_id FROM blog_posts WHERE (FIND_IN_SET(" . (int)$id . ", category_id) > 0 OR category_id = " . (int)$id . ")")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($posts as $p) {
                $ids = array_filter(array_map('trim', explode(',', (string)$p['category_id'])));
                $ids = array_diff($ids, [(string)$id, (int)$id]);
                $newCatId = !empty($ids) ? implode(',', $ids) : null;
                $upd = $db->prepare("UPDATE blog_posts SET category_id = ? WHERE id = ?");
                $upd->execute([$newCatId, $p['id']]);
            }
            // Delete category
            $stmt = $db->prepare("DELETE FROM blog_categories WHERE id = ?");
            return $stmt->execute([(int)$id]);
        } catch (\Exception $e) {
            error_log("BlogPost::deleteCategory() error: " . $e->getMessage());
            return false;
        }
    }
}
