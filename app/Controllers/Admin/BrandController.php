<?php
namespace App\Controllers\Admin;

use App\Models\Brand;

class BrandController extends AdminController
{
    /**
     * Display all brands with dynamic counts and live status
     */
    public function index()
    {
        $brands = Brand::all();

        $this->render('admin.brands.index', [
            'page_title' => 'Brands Management',
            'current_admin_page' => 'brands',
            'page_script' => 'brands.js',
            'brands' => $brands,
            'success' => get_flash('success'),
            'error' => get_flash('error')
        ]);
    }

    /**
     * Show create brand form or process POST creation
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token()) {
                set_flash('error', 'Security token expired or invalid request. Please try again.');
                $this->redirect('admin/add-brand');
                return;
            }

            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                set_flash('error', 'Brand name is required.');
                $this->redirect('admin/add-brand');
            }

            $slug = trim($_POST['slug'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $websiteUrl = trim($_POST['website_url'] ?? '');
            $seoTitle = trim($_POST['seo_title'] ?? '');
            $seoDescription = trim($_POST['seo_description'] ?? '');
            $sortOrder = isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0;
            $isActive = isset($_POST['is_active']) ? 1 : (isset($_POST['status']) && $_POST['status'] === 'active' ? 1 : 1);

            // Process Brand Logo Upload
            $uploadedLogo = null;
            if (!empty($_FILES['brand_logo']['name']) && $_FILES['brand_logo']['error'] === UPLOAD_ERR_OK) {
                $fileTmp = $_FILES['brand_logo']['tmp_name'];
                $cleanExt = null;
                if (is_valid_image_upload($fileTmp, $_FILES['brand_logo']['name'], $cleanExt)) {
                    $fileName = 'brand_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $cleanExt;
                    $targetDir = ROOT_PATH . '/public/assets/images/brands/';
                    if (!is_dir($targetDir)) {
                        @mkdir($targetDir, 0777, true);
                    }
                    if (@move_uploaded_file($fileTmp, $targetDir . $fileName)) {
                        $uploadedLogo = 'assets/images/brands/' . $fileName;
                    }
                }
            }
            $logoUrl = $uploadedLogo ?: (!empty($_POST['logo']) ? trim($_POST['logo']) : '');

            $newId = Brand::create([
                'name' => $name,
                'slug' => $slug,
                'logo_url' => $logoUrl,
                'description' => $description,
                'website_url' => $websiteUrl,
                'meta_title' => $seoTitle,
                'meta_desc' => $seoDescription,
                'sort_order' => $sortOrder,
                'is_active' => $isActive
            ]);

            if ($newId) {
                set_flash('success', "Brand '{$name}' created successfully.");
                $this->redirect('admin/brands');
            } else {
                set_flash('error', 'Failed to create brand. Please try again.');
                $this->redirect('admin/add-brand');
            }
        }

        $this->render('admin.brands.add', [
            'page_title' => 'Add New Brand',
            'current_admin_page' => 'brands',
            'page_script' => 'brand-form.js',
            'error' => get_flash('error'),
            'success' => get_flash('success')
        ]);
    }

    /**
     * Show edit brand form or process POST update
     */
    public function edit()
    {
        $id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
        $brand = Brand::find($id);

        if (!$brand) {
            set_flash('error', 'Brand not found.');
            $this->redirect('admin/brands');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token()) {
                set_flash('error', 'Security token expired or invalid request. Please try again.');
                $this->redirect("admin/edit-brand?id={$id}");
                return;
            }

            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                set_flash('error', 'Brand name cannot be empty.');
                $this->redirect("admin/edit-brand?id={$id}");
            }

            $slug = trim($_POST['slug'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $websiteUrl = trim($_POST['website_url'] ?? '');
            $seoTitle = trim($_POST['seo_title'] ?? '');
            $seoDescription = trim($_POST['seo_description'] ?? '');
            $sortOrder = isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0;
            $isActive = isset($_POST['is_active']) ? (int)$_POST['is_active'] : (isset($_POST['status']) && $_POST['status'] === 'active' ? 1 : 0);

            // Process Logo Upload or Removal
            $uploadedLogo = null;
            if (!empty($_FILES['brand_logo']['name']) && $_FILES['brand_logo']['error'] === UPLOAD_ERR_OK) {
                $fileTmp = $_FILES['brand_logo']['tmp_name'];
                $cleanExt = null;
                if (is_valid_image_upload($fileTmp, $_FILES['brand_logo']['name'], $cleanExt)) {
                    $fileName = 'brand_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $cleanExt;
                    $targetDir = ROOT_PATH . '/public/assets/images/brands/';
                    if (!is_dir($targetDir)) {
                        @mkdir($targetDir, 0777, true);
                    }
                    if (@move_uploaded_file($fileTmp, $targetDir . $fileName)) {
                        $uploadedLogo = 'assets/images/brands/' . $fileName;
                    }
                }
            }
            $logoUrl = $uploadedLogo ?: (isset($_POST['logo']) ? trim($_POST['logo']) : $brand['logo_url']);

            $updated = Brand::update($id, [
                'name' => $name,
                'slug' => $slug,
                'logo_url' => $logoUrl,
                'description' => $description,
                'website_url' => $websiteUrl,
                'meta_title' => $seoTitle,
                'meta_desc' => $seoDescription,
                'sort_order' => $sortOrder,
                'is_active' => $isActive
            ]);

            if ($updated) {
                set_flash('success', "Brand '{$name}' updated successfully.");
                $this->redirect('admin/brands');
            } else {
                set_flash('error', 'Failed to update brand.');
                $this->redirect("admin/edit-brand?id={$id}");
            }
        }

        $this->render('admin.brands.edit', [
            'page_title' => 'Edit Brand: ' . $brand['name'],
            'current_admin_page' => 'brands',
            'page_script' => 'brand-form.js',
            'brand' => $brand,
            'error' => get_flash('error'),
            'success' => get_flash('success')
        ]);
    }

    /**
     * Delete brand (Strictly via POST + CSRF)
     */
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token()) {
            set_flash('error', 'Security token expired or invalid request method.');
            $this->redirect('admin/brands');
            return;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id > 0) {
            $brand = Brand::find($id);
            $name = $brand['name'] ?? 'Brand #' . $id;
            Brand::delete($id);
            set_flash('success', "Brand '{$name}' was deleted successfully.");
        } else {
            set_flash('error', 'Invalid brand ID.');
        }
        $this->redirect('admin/brands');
    }
}
