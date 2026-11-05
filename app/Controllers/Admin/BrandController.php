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
            $shortDescription = trim($_POST['short_description'] ?? ($_POST['description'] ?? ''));
            $description = trim($_POST['description'] ?? $shortDescription);
            $longDescription = trim($_POST['long_description'] ?? '');
            $websiteUrl = trim($_POST['website_url'] ?? '');
            $seoTitle = trim($_POST['seo_title'] ?? '');
            $seoDescription = trim($_POST['seo_description'] ?? '');
            $sortOrder = isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0;
            $isActive = isset($_POST['is_active']) ? 1 : (isset($_POST['status']) && $_POST['status'] === 'active' ? 1 : 1);
            $promoBannerLink = trim($_POST['promo_banner_link'] ?? '');

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

            // Process Brand Header Banner Upload
            $uploadedHeader = null;
            if (!empty($_FILES['header_image']['name']) && $_FILES['header_image']['error'] === UPLOAD_ERR_OK) {
                $fileTmp = $_FILES['header_image']['tmp_name'];
                $cleanExt = null;
                if (is_valid_image_upload($fileTmp, $_FILES['header_image']['name'], $cleanExt)) {
                    $fileName = 'brand_hdr_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $cleanExt;
                    $targetDir = ROOT_PATH . '/public/assets/images/brands/';
                    if (!is_dir($targetDir)) {
                        @mkdir($targetDir, 0777, true);
                    }
                    if (@move_uploaded_file($fileTmp, $targetDir . $fileName)) {
                        $uploadedHeader = 'assets/images/brands/' . $fileName;
                    }
                }
            }
            $headerImageUrl = $uploadedHeader ?: (!empty($_POST['header_image_url']) ? trim($_POST['header_image_url']) : null);

            // Process Promotional Banner Upload
            $uploadedPromo = null;
            if (!empty($_FILES['promo_banner_file']['name']) && $_FILES['promo_banner_file']['error'] === UPLOAD_ERR_OK) {
                $fileTmp = $_FILES['promo_banner_file']['tmp_name'];
                $cleanExt = null;
                if (is_valid_image_upload($fileTmp, $_FILES['promo_banner_file']['name'], $cleanExt)) {
                    $fileName = 'brand_promo_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $cleanExt;
                    $targetDir = ROOT_PATH . '/public/assets/images/brands/';
                    if (!is_dir($targetDir)) {
                        @mkdir($targetDir, 0777, true);
                    }
                    if (@move_uploaded_file($fileTmp, $targetDir . $fileName)) {
                        $uploadedPromo = 'assets/images/brands/' . $fileName;
                    }
                }
            }
            $promoBannerImage = $uploadedPromo ?: (!empty($_POST['promo_banner_image']) ? trim($_POST['promo_banner_image']) : null);

            $headerCollections = isset($_POST['header_collections']) && is_array($_POST['header_collections']) ? $_POST['header_collections'] : [];
            $relatedCollections = isset($_POST['related_collections']) && is_array($_POST['related_collections']) ? $_POST['related_collections'] : [];

            $newId = Brand::create([
                'name' => $name,
                'slug' => $slug,
                'logo_url' => $logoUrl,
                'header_image_url' => $headerImageUrl,
                'short_description' => $shortDescription,
                'description' => $description,
                'long_description' => $longDescription,
                'header_collection_ids' => $headerCollections,
                'related_collection_ids' => $relatedCollections,
                'promo_banner_image' => $promoBannerImage,
                'promo_banner_link' => $promoBannerLink,
                'website_url' => $websiteUrl,
                'meta_title' => $seoTitle,
                'meta_desc' => $seoDescription,
                'sort_order' => $sortOrder,
                'is_active' => $isActive
            ]);

            if ($newId) {
                // Save FAQs
                if (!empty($_POST['faqs']) && is_array($_POST['faqs'])) {
                    Brand::saveFaqs($newId, $_POST['faqs']);
                }

                set_flash('success', "Brand '{$name}' created successfully.");
                $this->redirect('admin/brands');
            } else {
                set_flash('error', 'Failed to create brand. Please try again.');
                $this->redirect('admin/add-brand');
            }
        }

        $allCollections = class_exists('\\App\\Models\\Collection') ? \App\Models\Collection::all() : [];

        $this->render('admin.brands.add', [
            'page_title' => 'Add New Brand',
            'current_admin_page' => 'brands',
            'page_script' => 'brand-form.js',
            'all_collections' => $allCollections,
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
            $shortDescription = trim($_POST['short_description'] ?? ($_POST['description'] ?? ''));
            $description = trim($_POST['description'] ?? $shortDescription);
            $longDescription = trim($_POST['long_description'] ?? '');
            $websiteUrl = trim($_POST['website_url'] ?? '');
            $seoTitle = trim($_POST['seo_title'] ?? '');
            $seoDescription = trim($_POST['seo_description'] ?? '');
            $sortOrder = isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0;
            $isActive = isset($_POST['is_active']) ? (int)$_POST['is_active'] : (isset($_POST['status']) && $_POST['status'] === 'active' ? 1 : 0);
            $promoBannerLink = trim($_POST['promo_banner_link'] ?? '');

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

            // Process Header Image Upload
            $uploadedHeader = null;
            if (!empty($_FILES['header_image']['name']) && $_FILES['header_image']['error'] === UPLOAD_ERR_OK) {
                $fileTmp = $_FILES['header_image']['tmp_name'];
                $cleanExt = null;
                if (is_valid_image_upload($fileTmp, $_FILES['header_image']['name'], $cleanExt)) {
                    $fileName = 'brand_hdr_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $cleanExt;
                    $targetDir = ROOT_PATH . '/public/assets/images/brands/';
                    if (!is_dir($targetDir)) {
                        @mkdir($targetDir, 0777, true);
                    }
                    if (@move_uploaded_file($fileTmp, $targetDir . $fileName)) {
                        $uploadedHeader = 'assets/images/brands/' . $fileName;
                    }
                }
            }
            $headerImageUrl = $uploadedHeader ?: (isset($_POST['header_image_url']) ? trim($_POST['header_image_url']) : ($brand['header_image_url'] ?? null));

            // Process Promo Banner Upload
            $uploadedPromo = null;
            if (!empty($_FILES['promo_banner_file']['name']) && $_FILES['promo_banner_file']['error'] === UPLOAD_ERR_OK) {
                $fileTmp = $_FILES['promo_banner_file']['tmp_name'];
                $cleanExt = null;
                if (is_valid_image_upload($fileTmp, $_FILES['promo_banner_file']['name'], $cleanExt)) {
                    $fileName = 'brand_promo_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $cleanExt;
                    $targetDir = ROOT_PATH . '/public/assets/images/brands/';
                    if (!is_dir($targetDir)) {
                        @mkdir($targetDir, 0777, true);
                    }
                    if (@move_uploaded_file($fileTmp, $targetDir . $fileName)) {
                        $uploadedPromo = 'assets/images/brands/' . $fileName;
                    }
                }
            }
            $promoBannerImage = $uploadedPromo ?: (isset($_POST['promo_banner_image']) ? trim($_POST['promo_banner_image']) : ($brand['promo_banner_image'] ?? null));

            $headerCollections = isset($_POST['header_collections']) && is_array($_POST['header_collections']) ? $_POST['header_collections'] : [];
            $relatedCollections = isset($_POST['related_collections']) && is_array($_POST['related_collections']) ? $_POST['related_collections'] : [];

            $updated = Brand::update($id, [
                'name' => $name,
                'slug' => $slug,
                'logo_url' => $logoUrl,
                'header_image_url' => $headerImageUrl,
                'short_description' => $shortDescription,
                'description' => $description,
                'long_description' => $longDescription,
                'header_collection_ids' => $headerCollections,
                'related_collection_ids' => $relatedCollections,
                'promo_banner_image' => $promoBannerImage,
                'promo_banner_link' => $promoBannerLink,
                'website_url' => $websiteUrl,
                'meta_title' => $seoTitle,
                'meta_desc' => $seoDescription,
                'sort_order' => $sortOrder,
                'is_active' => $isActive
            ]);

            // Save FAQs
            Brand::saveFaqs($id, $_POST['faqs'] ?? []);

            if ($updated) {
                set_flash('success', "Brand '{$name}' updated successfully.");
                $this->redirect('admin/brands');
            } else {
                set_flash('error', 'Failed to update brand.');
                $this->redirect("admin/edit-brand?id={$id}");
            }
        }

        $allCollections = class_exists('\\App\\Models\\Collection') ? \App\Models\Collection::all() : [];
        $brandFaqs = Brand::getFaqs($id);

        $savedHeaderIds = [];
        if (!empty($brand['header_collection_ids'])) {
            $raw = $brand['header_collection_ids'];
            $dec = is_array($raw) ? $raw : json_decode($raw, true);
            $savedHeaderIds = is_array($dec) ? array_map('intval', $dec) : array_map('intval', explode(',', $raw));
        }

        $savedRelatedIds = [];
        if (!empty($brand['related_collection_ids'])) {
            $raw = $brand['related_collection_ids'];
            $dec = is_array($raw) ? $raw : json_decode($raw, true);
            $savedRelatedIds = is_array($dec) ? array_map('intval', $dec) : array_map('intval', explode(',', $raw));
        }

        $this->render('admin.brands.edit', [
            'page_title' => 'Edit Brand: ' . $brand['name'],
            'current_admin_page' => 'brands',
            'page_script' => 'brand-form.js',
            'brand' => $brand,
            'all_collections' => $allCollections,
            'faqs' => $brandFaqs,
            'savedHeaderIds' => $savedHeaderIds,
            'savedRelatedIds' => $savedRelatedIds,
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
