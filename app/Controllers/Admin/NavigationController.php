<?php
namespace App\Controllers\Admin;

use App\Models\Menu;

class NavigationController extends AdminController
{
    /**
     * Display all menus in Navigation list table (matching Screenshot 1)
     */
    public function index()
    {
        $menus = Menu::all();

        $this->render('admin.navigation.index', [
            'page_title' => 'Navigation',
            'current_admin_page' => 'navigation',
            'page_script' => 'navigation.js',
            'menus' => $menus,
            'success' => get_flash('success'),
            'error' => get_flash('error')
        ]);
    }

    /**
     * Display Menu Builder page (matching Screenshot 2)
     */
    public function builder()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
        $menu = Menu::find($id);

        if (!$menu) {
            $all = Menu::all();
            if (!empty($all)) {
                $this->redirect('admin/navigation/builder?id=' . $all[0]['id']);
                return;
            } else {
                set_flash('error', 'No menu found.');
                $this->redirect('admin/navigation');
                return;
            }
        }

        $entities = Menu::getAvailableEntities();

        $this->render('admin.navigation.builder', [
            'page_title' => $menu['name'],
            'current_admin_page' => 'navigation',
            'page_script' => 'navigation-builder.js',
            'menu' => $menu,
            'entities' => $entities,
            'success' => get_flash('success'),
            'error' => get_flash('error')
        ]);
    }

    /**
     * Create a new menu
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? $_POST['title'] ?? '');
            $location = trim($_POST['location'] ?? $_POST['handle'] ?? '');
            $subtitle = trim($_POST['subtitle'] ?? 'Secondary Menu');

            if (empty($name)) {
                set_flash('error', 'Menu title is required.');
                $this->redirect('admin/navigation');
                return;
            }

            if (empty($location)) {
                // Auto generate location handle from name
                $location = strtolower(preg_replace('/[^A-Za-z0-9_]+/', '_', $name));
                $location = trim($location, '_');
            }

            $newId = Menu::create([
                'name' => $name,
                'location' => $location,
                'subtitle' => $subtitle
            ]);

            if ($newId) {
                set_flash('success', 'Menu created successfully!');
                $this->redirect('admin/navigation/builder?id=' . $newId);
                return;
            } else {
                set_flash('error', 'Failed to create menu.');
                $this->redirect('admin/navigation');
                return;
            }
        }

        $this->redirect('admin/navigation');
    }

    /**
     * Asynchronously save the menu settings and full tree of items
     */
    public function save()
    {
        header('Content-Type: application/json');

        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);

        if (!$payload) {
            $payload = $_POST;
        }

        $menuId = !empty($payload['id']) ? (int)$payload['id'] : 0;
        $title = trim($payload['title'] ?? $payload['name'] ?? '');
        $handle = trim($payload['handle'] ?? $payload['location'] ?? '');
        $subtitle = trim($payload['subtitle'] ?? 'Secondary Menu');
        $items = $payload['items'] ?? [];

        if (!$menuId || empty($title) || empty($handle)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Menu ID, Title, and Handle/Location are required.'
            ]);
            exit;
        }

        $result = Menu::saveMenuAndTree($menuId, $title, $handle, $subtitle, $items);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Menu saved successfully!'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to save menu changes.'
            ]);
        }
        exit;
    }

    /**
     * Delete a menu (Strictly via POST + CSRF)
     */
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token()) {
            set_flash('error', 'Security token expired or invalid request method.');
            $this->redirect('admin/navigation');
            return;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        if ($id) {
            Menu::delete($id);
            set_flash('success', 'Menu deleted successfully.');
        } else {
            set_flash('error', 'Invalid menu ID.');
        }

        $this->redirect('admin/navigation');
    }

    /**
     * Delete a single menu item
     */
    public function deleteItem()
    {
        header('Content-Type: application/json');

        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);

        $itemId = !empty($payload['item_id']) ? (int)$payload['item_id'] : (int)($_POST['item_id'] ?? 0);

        if (!$itemId) {
            echo json_encode(['success' => false, 'message' => 'Item ID is required.']);
            exit;
        }

        $result = Menu::deleteItem($itemId);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Item removed.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to delete menu item.']);
        }
        exit;
    }

    /**
     * Asynchronously upload a promo banner image
     */
    public function uploadBanner()
    {
        header('Content-Type: application/json');

        if (!empty($_FILES['banner_image']['name']) && $_FILES['banner_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmp = $_FILES['banner_image']['tmp_name'];
            $ext = strtolower(pathinfo($_FILES['banner_image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (!in_array($ext, $allowed)) {
                echo json_encode(['success' => false, 'message' => 'Invalid image format.']);
                exit;
            }

            $targetDir = ROOT_PATH . '/public/assets/images/banners/';
            if (!is_dir($targetDir)) {
                @mkdir($targetDir, 0777, true);
            }

            $fileName = 'banner_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
            if (move_uploaded_file($fileTmp, $targetDir . $fileName)) {
                echo json_encode([
                    'success' => true,
                    'url' => asset('assets/images/banners/' . $fileName),
                    'relative_path' => 'assets/images/banners/' . $fileName
                ]);
                exit;
            }
        }

        echo json_encode(['success' => false, 'message' => 'Failed to upload image.']);
        exit;
    }
}

