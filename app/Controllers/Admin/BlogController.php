<?php
namespace App\Controllers\Admin;

use App\Models\BlogPost;
use App\Models\Product;

class BlogController extends AdminController
{
    /**
     * Display listing of blog posts
     */
    public function index()
    {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $search = trim($_GET['search'] ?? $_GET['q'] ?? '');
        $status = trim($_GET['status'] ?? 'all');
        $categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;

        $pagination = BlogPost::paginate($page, 12, $search, $status, $categoryId);
        $categories = BlogPost::getCategories();

        $this->render('admin.blogs.index', [
            'page_title'         => 'Blogs | Vape Store Admin',
            'current_admin_page' => 'blogs',
            'page_script'        => 'blogs.js',
            'posts'              => $pagination['data'],
            'pagination'         => $pagination,
            'categories'         => $categories,
            'search'             => $search,
            'status'             => $status,
            'success'            => $_GET['success'] ?? null,
            'error'              => $_GET['error'] ?? null
        ]);
    }

    /**
     * Show form for creating a new blog post
     */
    public function create()
    {
        $categories = BlogPost::getCategories();
        $products   = Product::getPublishedForBlog();

        $this->render('admin.blogs.add', [
            'page_title'         => 'Add Blog Post | Vape Store Admin',
            'current_admin_page' => 'blogs',
            'page_script'        => 'add-blog.js',
            'categories'         => $categories,
            'products'           => $products,
            'error'              => $_GET['error'] ?? null
        ]);
    }

    /**
     * Store newly created blog post
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token()) {
                $this->redirect('admin/blogs/add?error=' . urlencode('Security token expired or invalid. Please try again.'));
                return;
            }

            $title = trim($_POST['title'] ?? '');
            if (empty($title)) {
                $this->redirect('admin/blogs/add?error=' . urlencode('Blog title is required'));
                return;
            }

            $slug = trim($_POST['slug'] ?? '');
            if (empty($slug)) {
                $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $title));
                $slug = trim($slug, '-');
            }

            $customUrl = '/' . $slug;

            $statusVal = $_POST['status'] ?? 'active';
            $isActive = ($statusVal === 'active' || $statusVal === 'visible' || $statusVal === '1') ? 1 : 0;

            // Process Featured Image Upload
            $featuredImageUrl = !empty($_POST['existing_featured_image']) ? trim($_POST['existing_featured_image']) : 'assets/images/no-image.svg';
            if (!empty($_FILES['featured_image']['name']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
                $fileTmp = $_FILES['featured_image']['tmp_name'];
                $cleanExt = null;
                if (is_valid_image_upload($fileTmp, $_FILES['featured_image']['name'], $cleanExt)) {
                    $fileName = 'blog_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $cleanExt;
                    $baseDir = defined('ROOT_PATH') ? ROOT_PATH : dirname(dirname(dirname(__DIR__)));
                    $targetDir = $baseDir . '/public/assets/images/blogs/';
                    if (!is_dir($targetDir)) {
                        @mkdir($targetDir, 0777, true);
                    }
                    if (@move_uploaded_file($fileTmp, $targetDir . $fileName)) {
                        $featuredImageUrl = 'assets/images/blogs/' . $fileName;
                    }
                }
            } elseif (!empty($_POST['featured_image_url'])) {
                $featuredImageUrl = trim($_POST['featured_image_url']);
            }

            // Process Categories (Multiple Checkboxes or Single fallback)
            $catIds = $_POST['category_ids'] ?? $_POST['category_id'] ?? [];
            if (!is_array($catIds) && $catIds !== '') {
                $catIds = explode(',', (string)$catIds);
            }
            $cleanCatIds = array_filter(array_map('intval', (array)$catIds));
            $categoryIdsString = !empty($cleanCatIds) ? implode(',', $cleanCatIds) : null;

            $data = [
                'title'              => $title,
                'slug'               => $slug,
                'custom_url_path'    => $customUrl,
                'category_id'        => $categoryIdsString,
                'featured_image_url' => $featuredImageUrl,
                'author_name'        => 'Infinity Vape Shop',
                'excerpt'            => trim($_POST['excerpt'] ?? ''),
                'content'            => trim($_POST['content'] ?? ''),
                'tags'               => null,
                'meta_title'         => trim($_POST['seo_title'] ?? $_POST['meta_title'] ?? ''),
                'meta_desc'          => trim($_POST['seo_description'] ?? $_POST['meta_desc'] ?? ''),
                'is_active'          => $isActive,
                'published_at'       => date('Y-m-d H:i:s')
            ];

            $newId = BlogPost::create($data);
            if ($newId) {
                $this->redirect('admin/blogs?success=' . urlencode('Blog post created successfully'));
                return;
            } else {
                $this->redirect('admin/blogs/add?error=' . urlencode('Failed to create blog post. Please check for unique title/slug.'));
                return;
            }
        }

        $this->redirect('admin/blogs');
    }

    /**
     * Show form for editing a blog post
     */
    public function edit()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('admin/blogs');
            return;
        }

        $post = BlogPost::find($id);
        if (!$post) {
            $this->redirect('admin/blogs?error=' . urlencode('Blog post not found'));
            return;
        }

        $categories = BlogPost::getCategories();
        $products   = Product::getPublishedForBlog();

        $this->render('admin.blogs.edit', [
            'page_title'         => 'Edit Blog Post | Vape Store Admin',
            'current_admin_page' => 'blogs',
            'page_script'        => 'edit-blog.js',
            'post'               => $post,
            'categories'         => $categories,
            'products'           => $products,
            'success'            => $_GET['success'] ?? null,
            'error'              => $_GET['error'] ?? null
        ]);
    }

    /**
     * Update existing blog post
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? $_GET['id'] ?? null;
            if (!$id) {
                $this->redirect('admin/blogs');
                return;
            }

            if (!verify_csrf_token()) {
                $this->redirect('admin/blogs/edit?id=' . $id . '&error=' . urlencode('Security token expired or invalid. Please try again.'));
                return;
            }

            $title = trim($_POST['title'] ?? '');
            if (empty($title)) {
                $this->redirect('admin/blogs/edit?id=' . $id . '&error=' . urlencode('Blog title is required'));
                return;
            }

            $slug = trim($_POST['slug'] ?? '');
            if (empty($slug)) {
                $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $title));
                $slug = trim($slug, '-');
            }

            $customUrl = '/' . $slug;

            $statusVal = $_POST['status'] ?? 'active';
            $isActive = ($statusVal === 'active' || $statusVal === 'visible' || $statusVal === '1') ? 1 : 0;

            // Process Featured Image Upload
            $featuredImageUrl = !empty($_POST['existing_featured_image']) ? trim($_POST['existing_featured_image']) : 'assets/images/no-image.svg';
            if (!empty($_FILES['featured_image']['name']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
                $fileTmp = $_FILES['featured_image']['tmp_name'];
                $cleanExt = null;
                if (is_valid_image_upload($fileTmp, $_FILES['featured_image']['name'], $cleanExt)) {
                    $fileName = 'blog_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $cleanExt;
                    $baseDir = defined('ROOT_PATH') ? ROOT_PATH : dirname(dirname(dirname(__DIR__)));
                    $targetDir = $baseDir . '/public/assets/images/blogs/';
                    if (!is_dir($targetDir)) {
                        @mkdir($targetDir, 0777, true);
                    }
                    if (@move_uploaded_file($fileTmp, $targetDir . $fileName)) {
                        $featuredImageUrl = 'assets/images/blogs/' . $fileName;
                    }
                }
            } elseif (!empty($_POST['featured_image_url'])) {
                $featuredImageUrl = trim($_POST['featured_image_url']);
            }

            // Process Categories (Multiple Checkboxes or Single fallback)
            $catIds = $_POST['category_ids'] ?? $_POST['category_id'] ?? [];
            if (!is_array($catIds) && $catIds !== '') {
                $catIds = explode(',', (string)$catIds);
            }
            $cleanCatIds = array_filter(array_map('intval', (array)$catIds));
            $categoryIdsString = !empty($cleanCatIds) ? implode(',', $cleanCatIds) : null;

            $data = [
                'title'              => $title,
                'slug'               => $slug,
                'custom_url_path'    => $customUrl,
                'category_id'        => $categoryIdsString,
                'featured_image_url' => $featuredImageUrl,
                'author_name'        => 'Infinity Vape Shop',
                'excerpt'            => trim($_POST['excerpt'] ?? ''),
                'content'            => trim($_POST['content'] ?? ''),
                'tags'               => null,
                'meta_title'         => trim($_POST['seo_title'] ?? $_POST['meta_title'] ?? ''),
                'meta_desc'          => trim($_POST['seo_description'] ?? $_POST['meta_desc'] ?? ''),
                'is_active'          => $isActive
            ];

            if (BlogPost::update($id, $data)) {
                $this->redirect('admin/blogs?success=' . urlencode('Blog post updated successfully'));
                return;
            } else {
                $this->redirect('admin/blogs/edit?id=' . $id . '&error=' . urlencode('Failed to update blog post'));
                return;
            }
        }

        $this->redirect('admin/blogs');
    }

    /**
     * Delete blog post (Strictly via POST + CSRF)
     */
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token()) {
            $this->redirect('admin/blogs?error=' . urlencode('Security token expired or invalid request method.'));
            return;
        }

        $id = $_POST['id'] ?? null;
        if ($id && BlogPost::delete($id)) {
            $this->redirect('admin/blogs?success=' . urlencode('Blog post deleted successfully'));
            return;
        }

        $this->redirect('admin/blogs?error=' . urlencode('Failed to delete blog post'));
    }

    /**
     * Display Blog Categories management
     */
    public function categories()
    {
        $categories = BlogPost::getCategories();

        $this->render('admin.blogs.categories', [
            'page_title'         => 'Blog Categories | Vape Store Admin',
            'current_admin_page' => 'blog-categories',
            'page_script'        => 'blog-categories.js',
            'categories'         => $categories,
            'success'            => $_GET['success'] ?? null,
            'error'              => $_GET['error'] ?? null
        ]);
    }

    /**
     * Store new blog category
     */
    public function storeCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token()) {
                $this->redirect('admin/blog-categories?error=' . urlencode('Security token expired or invalid request.'));
                return;
            }

            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                $this->redirect('admin/blog-categories?error=' . urlencode('Category name is required'));
                return;
            }

            $slug = trim($_POST['slug'] ?? '');
            if (empty($slug)) {
                $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
                $slug = trim($slug, '-');
            }

            $description = trim($_POST['description'] ?? '');

            if (BlogPost::createCategory(['name' => $name, 'slug' => $slug, 'description' => $description])) {
                $this->redirect('admin/blog-categories?success=' . urlencode('Category created successfully'));
                return;
            }
        }

        $this->redirect('admin/blog-categories?error=' . urlencode('Failed to create category'));
    }

    /**
     * Update existing blog category
     */
    public function updateCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token()) {
                $this->redirect('admin/blog-categories?error=' . urlencode('Security token expired or invalid request.'));
                return;
            }

            $id = $_POST['id'] ?? null;
            $name = trim($_POST['name'] ?? '');
            if ($id && !empty($name)) {
                $slug = trim($_POST['slug'] ?? '');
                if (empty($slug)) {
                    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
                    $slug = trim($slug, '-');
                }
                $description = trim($_POST['description'] ?? '');

                if (BlogPost::updateCategory($id, ['name' => $name, 'slug' => $slug, 'description' => $description])) {
                    $this->redirect('admin/blog-categories?success=' . urlencode('Category updated successfully'));
                    return;
                }
            }
        }

        $this->redirect('admin/blog-categories?error=' . urlencode('Failed to update category'));
    }

    /**
     * Delete blog category (Strictly via POST + CSRF)
     */
    public function deleteCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token()) {
            $this->redirect('admin/blog-categories?error=' . urlencode('Security token expired or invalid request method.'));
            return;
        }

        $id = $_POST['id'] ?? null;
        if ($id && BlogPost::deleteCategory($id)) {
            $this->redirect('admin/blog-categories?success=' . urlencode('Category deleted successfully'));
            return;
        }

        $this->redirect('admin/blog-categories?error=' . urlencode('Failed to delete category'));
    }
}
