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
                SELECT b.*, c.name as category_name, c.slug as category_slug
                FROM blog_posts b 
                LEFT JOIN blog_categories c ON b.category_id = c.id 
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
                SELECT b.*, c.name as category_name, c.slug as category_slug
                FROM blog_posts b 
                LEFT JOIN blog_categories c ON b.category_id = c.id 
                WHERE b.is_active = 1
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
                SELECT b.*, c.name as category_name, c.slug as category_slug
                FROM blog_posts b 
                LEFT JOIN blog_categories c ON b.category_id = c.id 
                WHERE b.is_active = 1 AND c.slug IN ($placeholders)
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
                    SELECT b.*, c.name as category_name, c.slug as category_slug
                    FROM blog_posts b 
                    LEFT JOIN blog_categories c ON b.category_id = c.id 
                    WHERE b.is_active = 1 $excludeSql
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
                SELECT b.*, c.name as category_name, c.slug as category_slug
                FROM blog_posts b 
                LEFT JOIN blog_categories c ON b.category_id = c.id 
                $whereSql
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
            $sql = "SELECT b.*, c.name as category_name, c.slug as category_slug
                    FROM blog_posts b
                    LEFT JOIN blog_categories c ON b.category_id = c.id
                    WHERE " . ($isNumeric ? "b.id = ?" : "(b.slug = ? OR b.custom_url_path = ?)") . "
                    LIMIT 1";
            $stmt = $db->prepare($sql);
            if ($isNumeric) {
                $stmt->execute([(int)$idOrSlug]);
            } else {
                $stmt->execute([(string)$idOrSlug, (string)$idOrSlug]);
            }
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
            return $post ?: null;
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
                $where[] = "b.category_id = ?";
                $params[] = (int)$categoryId;
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            // Total count
            $countSql = "SELECT COUNT(*) FROM blog_posts b $whereClause";
            $countStmt = $db->prepare($countSql);
            $countStmt->execute($params);
            $total = (int)$countStmt->fetchColumn();

            // Fetch data
            $sql = "SELECT b.*, c.name as category_name, c.slug as category_slug
                    FROM blog_posts b 
                    LEFT JOIN blog_categories c ON b.category_id = c.id 
                    $whereClause
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
                ':category_id'        => !empty($data['category_id']) ? (int)$data['category_id'] : null,
                ':author_id'          => !empty($data['author_id']) ? (int)$data['author_id'] : null,
                ':title'              => $title,
                ':slug'               => $slug,
                ':custom_url_path'    => $customUrl,
                ':featured_image_url' => !empty($data['featured_image_url']) ? $data['featured_image_url'] : 'assets/images/blog-1.jpg',
                ':author_name'        => 'New Vape',
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
                SELECT c.*, COUNT(b.id) as post_count 
                FROM blog_categories c 
                LEFT JOIN blog_posts b ON c.id = b.category_id 
                GROUP BY c.id
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
            // Nullify category_id in posts
            $db->prepare("UPDATE blog_posts SET category_id = NULL WHERE category_id = ?")->execute([(int)$id]);
            // Delete category
            $stmt = $db->prepare("DELETE FROM blog_categories WHERE id = ?");
            return $stmt->execute([(int)$id]);
        } catch (\Exception $e) {
            error_log("BlogPost::deleteCategory() error: " . $e->getMessage());
            return false;
        }
    }
}
