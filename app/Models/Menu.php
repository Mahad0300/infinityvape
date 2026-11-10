<?php
namespace App\Models;

use App\Core\Model;

class Menu extends Model
{
    /**
     * Get all menus with their items count
     */
    public static function all()
    {
        $db = self::getDB();
        if (!$db) return [];

        try {
            $sql = "SELECT m.*, 
                           (SELECT COUNT(*) FROM menu_items mi WHERE mi.menu_id = m.id) AS items_count 
                    FROM menus m 
                    ORDER BY m.id ASC";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Menu::all error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Find menu by ID with its items
     */
    public static function find($id)
    {
        $db = self::getDB();
        if (!$db) return null;

        try {
            $stmt = $db->prepare("SELECT * FROM menus WHERE id = ? LIMIT 1");
            $stmt->execute([(int)$id]);
            $menu = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$menu) return null;

            // Fetch menu items ordered by sort_order
            $itemsStmt = $db->prepare("SELECT * FROM menu_items WHERE menu_id = ? ORDER BY sort_order ASC, id ASC");
            $itemsStmt->execute([(int)$id]);
            $menu['items'] = $itemsStmt->fetchAll(\PDO::FETCH_ASSOC);

            return $menu;
        } catch (\Exception $e) {
            error_log("Menu::find error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a new menu
     */
    public static function create($data)
    {
        $db = self::getDB();
        if (!$db) return false;

        try {
            $stmt = $db->prepare("INSERT INTO menus (name, subtitle, location) VALUES (?, ?, ?)");
            $stmt->execute([
                trim($data['name'] ?? 'New Menu'),
                trim($data['subtitle'] ?? 'Secondary Menu'),
                trim($data['location'] ?? 'custom_menu_' . time())
            ]);
            return $db->lastInsertId();
        } catch (\Exception $e) {
            error_log("Menu::create error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update menu settings
     */
    public static function update($id, $data)
    {
        $db = self::getDB();
        if (!$db) return false;

        try {
            $stmt = $db->prepare("UPDATE menus SET name = ?, location = ?, subtitle = ? WHERE id = ?");
            return $stmt->execute([
                trim($data['name'] ?? ''),
                trim($data['location'] ?? ''),
                trim($data['subtitle'] ?? 'Secondary Menu'),
                (int)$id
            ]);
        } catch (\Exception $e) {
            error_log("Menu::update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete menu and all its items
     */
    public static function delete($id)
    {
        $db = self::getDB();
        if (!$db) return false;

        try {
            $db->beginTransaction();

            // Sever self-referential parent_id links before deletion to prevent foreign key errors
            $db->prepare("UPDATE menu_items SET parent_id = NULL WHERE menu_id = ?")->execute([(int)$id]);

            $stmtItems = $db->prepare("DELETE FROM menu_items WHERE menu_id = ?");
            $stmtItems->execute([(int)$id]);

            $stmtMenu = $db->prepare("DELETE FROM menus WHERE id = ?");
            $stmtMenu->execute([(int)$id]);

            $db->commit();
            return true;
        } catch (\Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            error_log("Menu::delete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Save the entire menu settings and hierarchical items tree
     */
    public static function saveMenuAndTree($menuId, $name, $location, $subtitle, $items)
    {
        $db = self::getDB();
        if (!$db) return false;

        try {
            $db->beginTransaction();

            // 1. Update Menu details
            $stmt = $db->prepare("UPDATE menus SET name = ?, location = ?, subtitle = ? WHERE id = ?");
            $stmt->execute([
                trim($name),
                trim($location),
                trim($subtitle ?: 'Secondary Menu'),
                (int)$menuId
            ]);

            // 2. Fetch existing item IDs to know which ones to keep / delete
            $existingStmt = $db->prepare("SELECT id FROM menu_items WHERE menu_id = ?");
            $existingStmt->execute([(int)$menuId]);
            $existingIds = $existingStmt->fetchAll(\PDO::FETCH_COLUMN);

            $retainedIds = [];

            // Helper function to recursively save items
            $saveRecursive = function($itemList, $parentId = null) use (&$saveRecursive, $db, $menuId, &$retainedIds) {
                if (!is_array($itemList)) return;

                $order = 1;
                foreach ($itemList as $item) {
                    $itemId = !empty($item['id']) && is_numeric($item['id']) ? (int)$item['id'] : null;
                    $title = trim($item['title'] ?? $item['name'] ?? 'Menu Item');
                    $subtitle = trim($item['subtitle'] ?? '');
                    $linkType = trim($item['link_type'] ?? 'custom_url');
                    $linkValue = trim($item['link_value'] ?? $item['url'] ?? '#');
                    $entityId = !empty($item['entity_id']) ? (int)$item['entity_id'] : null;

                    $imageUrl = trim($item['image_url'] ?? '');

                    if ($itemId && in_array($itemId, $retainedIds)) {
                        // Avoid duplicates
                        $itemId = null;
                    }

                    if ($itemId) {
                        // Update existing item
                        $uStmt = $db->prepare("UPDATE menu_items SET parent_id = ?, title = ?, subtitle = ?, link_type = ?, link_value = ?, entity_id = ?, image_url = ?, sort_order = ? WHERE id = ? AND menu_id = ?");
                        $uStmt->execute([
                            $parentId,
                            $title,
                            $subtitle,
                            $linkType,
                            $linkValue,
                            $entityId,
                            $imageUrl,
                            $order,
                            $itemId,
                            (int)$menuId
                        ]);
                        $retainedIds[] = $itemId;
                        $currentId = $itemId;
                    } else {
                        // Insert new item
                        $iStmt = $db->prepare("INSERT INTO menu_items (menu_id, parent_id, title, subtitle, link_type, link_value, entity_id, image_url, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $iStmt->execute([
                            (int)$menuId,
                            $parentId,
                            $title,
                            $subtitle,
                            $linkType,
                            $linkValue,
                            $entityId,
                            $imageUrl,
                            $order
                        ]);
                        $currentId = (int)$db->lastInsertId();
                        $retainedIds[] = $currentId;
                    }

                    $order++;

                    // Process children if any
                    if (!empty($item['children']) && is_array($item['children'])) {
                        $saveRecursive($item['children'], $currentId);
                    }
                }
            };

            $saveRecursive($items, null);

            // 3. Delete removed items
            $existingIds = array_map('intval', $existingIds);
            $retainedIds = array_map('intval', $retainedIds);
            $toDelete = array_values(array_diff($existingIds, $retainedIds));
            if (!empty($toDelete)) {
                $placeholders = implode(',', array_fill(0, count($toDelete), '?'));
                $params = $toDelete;
                $params[] = (int)$menuId;

                // Sever self-referential parent_id links before deletion to avoid foreign key failures
                $db->prepare("UPDATE menu_items SET parent_id = NULL WHERE parent_id IN ($placeholders) AND menu_id = ?")->execute($params);
                $db->prepare("UPDATE menu_items SET parent_id = NULL WHERE id IN ($placeholders) AND menu_id = ?")->execute($params);

                $delStmt = $db->prepare("DELETE FROM menu_items WHERE id IN ($placeholders) AND menu_id = ?");
                $delStmt->execute($params);
            }

            $db->commit();
            return true;
        } catch (\Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            error_log("Menu::saveMenuAndTree error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete single menu item and all its descendant children
     */
    public static function deleteItem($itemId)
    {
        $db = self::getDB();
        if (!$db) return false;

        try {
            $db->beginTransaction();

            // Find all child IDs recursively
            $allIds = [(int)$itemId];
            $queue = [(int)$itemId];

            while (!empty($queue)) {
                $currentParent = array_shift($queue);
                $stmt = $db->prepare("SELECT id FROM menu_items WHERE parent_id = ?");
                $stmt->execute([$currentParent]);
                $children = $stmt->fetchAll(\PDO::FETCH_COLUMN);
                foreach ($children as $cId) {
                    $allIds[] = (int)$cId;
                    $queue[] = (int)$cId;
                }
            }

            $placeholders = implode(',', array_fill(0, count($allIds), '?'));

            // Sever self-referential parent_id links before deletion to prevent foreign key constraint violations
            $db->prepare("UPDATE menu_items SET parent_id = NULL WHERE parent_id IN ($placeholders)")->execute($allIds);
            $db->prepare("UPDATE menu_items SET parent_id = NULL WHERE id IN ($placeholders)")->execute($allIds);

            $delStmt = $db->prepare("DELETE FROM menu_items WHERE id IN ($placeholders)");
            $delStmt->execute($allIds);

            $db->commit();
            return true;
        } catch (\Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            error_log("Menu::deleteItem error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ensure all standard policy & info pages exist in pages table with correct titles and slugs
     */
    public static function syncDefaultPages($db)
    {
        if (!$db) return;
        try {
            $defaultPages = [
                'shipping-policy' => [
                    'title' => 'Shipping Policy',
                    'path' => '/page/shipping-policy',
                    'meta_title' => 'Shipping Policy | Vape City USA',
                    'meta_desc' => 'Information regarding delivery times, adult signature requirements, and shipping policies.'
                ],
                'refund-policy' => [
                    'title' => 'Refund Policy',
                    'path' => '/page/refund-policy',
                    'meta_title' => 'Refund & Return Policy | Vape City USA',
                    'meta_desc' => 'Official refund and return guidelines for Vape City USA products.'
                ],
                'fda-disclaimer' => [
                    'title' => 'FDA Disclaimer',
                    'path' => '/page/fda-disclaimer',
                    'meta_title' => 'FDA Disclaimer & Nicotine Warning | Vape City USA',
                    'meta_desc' => 'Mandatory FDA nicotine warning and age verification statements.'
                ],
                'terms-and-conditions' => [
                    'title' => 'Terms & Conditions',
                    'path' => '/page/terms-and-conditions',
                    'meta_title' => 'Terms & Conditions | Infinity Vape Shop',
                    'meta_desc' => 'Terms and conditions of use and purchase for Infinity Vape Shop.'
                ],
                'privacy-policy' => [
                    'title' => 'Privacy Policy',
                    'path' => '/page/privacy-policy',
                    'meta_title' => 'Privacy Policy | Vape City USA',
                    'meta_desc' => 'Official privacy policy of New Vape.'
                ],
                'about-us' => [
                    'title' => 'About Us',
                    'path' => '/page/about-us',
                    'meta_title' => 'About Us | New Vape',
                    'meta_desc' => 'Learn about New Vape, our mission, and authentic vape products.'
                ],
                'contact-us' => [
                    'title' => 'Contact Us',
                    'path' => '/page/contact-us',
                    'meta_title' => 'Contact Us | New Vape Support',
                    'meta_desc' => 'Reach out to our customer service team via email, phone, or contact form.'
                ],
                'faqs' => [
                    'title' => 'FAQs',
                    'path' => '/page/faqs',
                    'meta_title' => 'FAQs | Infinity Vape Shop',
                    'meta_desc' => 'Find answers to common questions about Infinity Vape Shop, including age verification, orders, shipping, tracking, returns, refunds, and vape products.'
                ],
                'track-order' => [
                    'title' => 'Track Your Order',
                    'path' => '/page/track-order',
                    'meta_title' => 'Track Your Order | New Vape',
                    'meta_desc' => 'Look up your package tracking status in real time.'
                ]
            ];

            // Normalize existing legacy names/slugs
            $db->exec("UPDATE pages SET title = 'Shipping Policy', custom_url_path = '/page/shipping-policy' WHERE slug = 'shipping-policy'");
            $db->exec("UPDATE pages SET title = 'FDA Disclaimer', slug = 'fda-disclaimer', custom_url_path = '/page/fda-disclaimer' WHERE slug = 'age-verification'");
            $db->exec("UPDATE pages SET title = 'Terms & Conditions', slug = 'terms-and-conditions', custom_url_path = '/page/terms-and-conditions' WHERE slug = 'terms-and-condition' OR slug = 'terms-of-service' OR slug = 'terms-and-conditions'");
            $db->exec("UPDATE menu_items SET link_value = 'page/terms-and-conditions' WHERE link_value LIKE '%terms-and-condition%' OR link_value LIKE '%terms-of-service%'");
            $db->exec("UPDATE pages SET title = 'FAQs', slug = 'faqs', custom_url_path = '/page/faqs' WHERE slug = 'faq'");
            $db->exec("UPDATE menu_items SET link_value = 'page/faqs' WHERE link_value LIKE '%page/faq%' OR link_value = 'faq'");
            $db->exec("UPDATE pages SET custom_url_path = '/page/privacy-policy' WHERE slug = 'privacy-policy'");
            $db->exec("UPDATE pages SET custom_url_path = '/page/about-us' WHERE slug = 'about-us'");
            $db->exec("UPDATE pages SET custom_url_path = '/page/contact-us' WHERE slug = 'contact-us'");

            // Insert any missing pages
            foreach ($defaultPages as $slug => $p) {
                $chk = $db->prepare("SELECT id FROM pages WHERE slug = ? LIMIT 1");
                $chk->execute([$slug]);
                if (!$chk->fetch()) {
                    $ins = $db->prepare("INSERT INTO pages (title, slug, custom_url_path, meta_title, meta_desc, is_active) VALUES (?, ?, ?, ?, ?, 1)");
                    $ins->execute([$p['title'], $slug, $p['path'], $p['meta_title'], $p['meta_desc']]);
                }
            }
        } catch (\Exception $e) {
            error_log("Menu::syncDefaultPages error: " . $e->getMessage());
        }
    }

    /**
     * Get available collections, brands, and pages for target link selector dropdowns
     */
    public static function getAvailableEntities()
    {
        $db = self::getDB();
        $res = [
            'collections' => [],
            'brands' => [],
            'pages' => [],
            'blog_categories' => []
        ];

        if (!$db) return $res;

        try {
            self::syncDefaultPages($db);

            $cStmt = $db->query("SELECT id, name, slug FROM collections WHERE is_active = 1 ORDER BY name ASC");
            if ($cStmt) $res['collections'] = $cStmt->fetchAll(\PDO::FETCH_ASSOC);

            $bStmt = $db->query("SELECT id, name, slug FROM brands WHERE is_active = 1 ORDER BY name ASC");
            if ($bStmt) $res['brands'] = $bStmt->fetchAll(\PDO::FETCH_ASSOC);

            $pStmt = $db->query("SELECT id, title, slug, custom_url_path FROM pages WHERE is_active = 1 ORDER BY title ASC");
            if ($pStmt) $res['pages'] = $pStmt->fetchAll(\PDO::FETCH_ASSOC);

            $bgStmt = $db->query("SELECT id, name, slug FROM blog_categories ORDER BY name ASC");
            if ($bgStmt) $res['blog_categories'] = $bgStmt->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\Exception $e) {
            error_log("Menu::getAvailableEntities error: " . $e->getMessage());
        }

        return $res;
    }

    /**
     * Get hierarchical menu tree by location (e.g. 'main_menu', 'footer_menu')
     * Returns structured nested array ready for front-end rendering
     */
    public static function getTreeByLocation($location = 'main_menu')
    {
        $db = self::getDB();
        if (!$db) return [];

        try {
            // Find menu by location or fallback to ID 1
            $stmt = $db->prepare("SELECT * FROM menus WHERE location = ? LIMIT 1");
            $stmt->execute([$location]);
            $menu = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$menu && $location === 'main_menu') {
                $stmt = $db->query("SELECT * FROM menus WHERE id = 1 LIMIT 1");
                $menu = $stmt ? $stmt->fetch(\PDO::FETCH_ASSOC) : null;
            }

            if (!$menu) return [];

            // Fetch items
            $stmt = $db->prepare("SELECT * FROM menu_items WHERE menu_id = ? ORDER BY sort_order ASC, id ASC");
            $stmt->execute([(int)$menu['id']]);
            $rawItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($rawItems)) return [];

            // Group by parent_id
            $itemsById = [];
            foreach ($rawItems as $item) {
                $item['children'] = [];
                $itemsById[$item['id']] = $item;
            }

            $tree = [];
            foreach ($itemsById as $id => &$node) {
                if (!empty($node['parent_id']) && isset($itemsById[$node['parent_id']])) {
                    $itemsById[$node['parent_id']]['children'][] = &$node;
                } else {
                    $tree[] = &$node;
                }
            }
            unset($node);

            return $tree;
        } catch (\Exception $e) {
            error_log("Menu::getTreeByLocation error: " . $e->getMessage());
            return [];
        }
    }
}
