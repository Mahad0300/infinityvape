<?php
namespace App\Controllers\Admin;

use App\Models\Collection;

class CollectionController extends AdminController
{
    /**
     * Display all collections with dynamic counts and live status
     */
    public function index()
    {
        $collections = Collection::all();

        $this->render('admin.collections.index', [
            'page_title' => 'Collections Management',
            'current_admin_page' => 'collections',
            'page_script' => 'collections.js',
            'collections' => $collections,
            'success' => get_flash('success'),
            'error' => get_flash('error')
        ]);
    }

    /**
     * Show create form or process POST creation
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token()) {
                set_flash('error', 'Security token expired or invalid request. Please try again.');
                $this->redirect('admin/add-collection');
                return;
            }

            $name = trim($_POST['title'] ?? $_POST['name'] ?? '');
            if (empty($name)) {
                set_flash('error', 'Collection title is required.');
                $this->redirect('admin/add-collection');
            }

            $slug = trim($_POST['slug'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $longDescription = trim($_POST['long_description'] ?? '');
            $seoTitle = trim($_POST['seo_title'] ?? '');
            $seoDescription = trim($_POST['seo_description'] ?? '');
            $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            $isActive = isset($_POST['is_active']) ? 1 : (isset($_POST['status']) && $_POST['status'] === 'active' ? 1 : 1);

            // Process Image Upload
            $uploadedImage = null;
            if (!empty($_FILES['collection_image']['name']) && $_FILES['collection_image']['error'] === UPLOAD_ERR_OK) {
                $fileTmp = $_FILES['collection_image']['tmp_name'];
                $cleanExt = null;
                if (is_valid_image_upload($fileTmp, $_FILES['collection_image']['name'], $cleanExt)) {
                    $fileName = 'col_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $cleanExt;
                    $targetDir = ROOT_PATH . '/public/assets/images/collections/';
                    if (!is_dir($targetDir)) {
                        @mkdir($targetDir, 0777, true);
                    }
                    if (@move_uploaded_file($fileTmp, $targetDir . $fileName)) {
                        $uploadedImage = 'assets/images/collections/' . $fileName;
                    }
                }
            }
            $headerImageUrl = $uploadedImage ?: (!empty($_POST['image']) ? trim($_POST['image']) : '');

            $relatedCollections = isset($_POST['related_collections']) && is_array($_POST['related_collections'])
                ? array_values(array_filter(array_map('intval', $_POST['related_collections'])))
                : [];

            $newId = Collection::create([
                'name' => $name,
                'slug' => $slug,
                'short_description' => $description,
                'long_description' => $longDescription,
                'header_image_url' => $headerImageUrl,
                'parent_id' => $parentId,
                'related_collection_ids' => !empty($relatedCollections) ? json_encode($relatedCollections) : null,
                'seo_title' => $seoTitle,
                'seo_description' => $seoDescription,
                'is_active' => $isActive
            ]);

            if ($newId) {
                // Save FAQs if provided
                if (!empty($_POST['faqs']) && is_array($_POST['faqs'])) {
                    Collection::saveFaqs($newId, $_POST['faqs']);
                }

                set_flash('success', "Collection '{$name}' created successfully.");
                $this->redirect('admin/collections');
            } else {
                set_flash('error', 'Failed to create collection. Please try again.');
                $this->redirect('admin/add-collection');
            }
        }

        $parents = Collection::parents();
        $allCollections = Collection::all();

        $this->render('admin.collections.add', [
            'page_title' => 'Add New Collection',
            'current_admin_page' => 'collections',
            'page_script' => 'add-collection.js',
            'parents' => $parents,
            'all_collections' => $allCollections,
            'error' => get_flash('error'),
            'success' => get_flash('success')
        ]);
    }

    /**
     * Show edit form or process POST update
     */
    public function edit()
    {
        $id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
        $collection = Collection::find($id);

        if (!$collection) {
            set_flash('error', 'Collection not found.');
            $this->redirect('admin/collections');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token()) {
                set_flash('error', 'Security token expired or invalid request. Please try again.');
                $this->redirect("admin/edit-collection?id={$id}");
                return;
            }

            $name = trim($_POST['title'] ?? $_POST['name'] ?? '');
            if (empty($name)) {
                set_flash('error', 'Collection title cannot be empty.');
                $this->redirect("admin/edit-collection?id={$id}");
            }

            $slug = trim($_POST['slug'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $longDescription = trim($_POST['long_description'] ?? '');
            $seoTitle = trim($_POST['seo_title'] ?? '');
            $seoDescription = trim($_POST['seo_description'] ?? '');
            $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            $isActive = isset($_POST['is_active']) ? (int)$_POST['is_active'] : (isset($_POST['status']) && $_POST['status'] === 'active' ? 1 : 0);

            // Process Image Upload or Removal
            $uploadedImage = null;
            if (!empty($_FILES['collection_image']['name']) && $_FILES['collection_image']['error'] === UPLOAD_ERR_OK) {
                $fileTmp = $_FILES['collection_image']['tmp_name'];
                $cleanExt = null;
                if (is_valid_image_upload($fileTmp, $_FILES['collection_image']['name'], $cleanExt)) {
                    $fileName = 'col_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $cleanExt;
                    $targetDir = ROOT_PATH . '/public/assets/images/collections/';
                    if (!is_dir($targetDir)) {
                        @mkdir($targetDir, 0777, true);
                    }
                    if (@move_uploaded_file($fileTmp, $targetDir . $fileName)) {
                        $uploadedImage = 'assets/images/collections/' . $fileName;
                    }
                }
            }
            $headerImageUrl = $uploadedImage ?: (isset($_POST['image']) ? trim($_POST['image']) : $collection['header_image_url']);

            $relatedCollections = isset($_POST['related_collections']) && is_array($_POST['related_collections'])
                ? array_values(array_filter(array_map('intval', $_POST['related_collections'])))
                : [];

            $updated = Collection::update($id, [
                'name' => $name,
                'slug' => $slug,
                'short_description' => $description,
                'long_description' => $longDescription,
                'header_image_url' => $headerImageUrl,
                'parent_id' => $parentId,
                'related_collection_ids' => !empty($relatedCollections) ? json_encode($relatedCollections) : null,
                'seo_title' => $seoTitle,
                'seo_description' => $seoDescription,
                'is_active' => $isActive
            ]);

            if ($updated) {
                // Save FAQs
                $faqs = (!empty($_POST['faqs']) && is_array($_POST['faqs'])) ? $_POST['faqs'] : [];
                Collection::saveFaqs($id, $faqs);

                set_flash('success', "Collection '{$name}' updated successfully.");
                $this->redirect('admin/collections');
            } else {
                set_flash('error', 'Failed to update collection.');
                $this->redirect("admin/edit-collection?id={$id}");
            }
        }

        $parents = Collection::parents($id);
        $faqs = Collection::getFaqs($id);
        $allCollections = Collection::all();

        $this->render('admin.collections.edit', [
            'page_title' => 'Edit Collection: ' . $collection['name'],
            'current_admin_page' => 'collections',
            'page_script' => 'edit-collection.js',
            'collection' => $collection,
            'parents' => $parents,
            'faqs' => $faqs,
            'all_collections' => $allCollections,
            'error' => get_flash('error'),
            'success' => get_flash('success')
        ]);
    }

    /**
     * Delete collection (Strictly via POST + CSRF)
     */
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token()) {
            set_flash('error', 'Security token expired or invalid request method.');
            $this->redirect('admin/collections');
            return;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id > 0) {
            $col = Collection::find($id);
            $name = $col['name'] ?? 'Collection #' . $id;
            Collection::delete($id);
            set_flash('success', "Collection '{$name}' was deleted successfully.");
        } else {
            set_flash('error', 'Invalid collection ID.');
        }
        $this->redirect('admin/collections');
    }
}
