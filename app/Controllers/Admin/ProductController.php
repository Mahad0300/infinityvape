<?php
namespace App\Controllers\Admin;

use App\Models\Product;
use App\Models\Collection;
use App\Models\Brand;

class ProductController extends AdminController
{
    /**
     * Display all products in catalog
     */
    public function index()
    {
        $products = Product::all();

        $this->render('admin.products.index', [
            'page_title' => 'Products Management',
            'current_admin_page' => 'products',
            'page_script' => 'products.js',
            'products' => $products,
            'success' => get_flash('success'),
            'error' => get_flash('error')
        ]);
    }

    /**
     * Show create product form or process POST creation
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token()) {
                set_flash('error', 'Security token expired or invalid request. Please try again.');
                $this->redirect('admin/add-product');
                return;
            }

            $title = trim($_POST['title'] ?? '');
            if (empty($title)) {
                set_flash('error', 'Product title is required.');
                $this->redirect('admin/add-product');
            }

            // Multiple Images Upload Handling
            $uploadedImages = [];
            $targetDir = ROOT_PATH . '/public/assets/images/products/';
            if (!is_dir($targetDir)) {
                @mkdir($targetDir, 0777, true);
            }

            if (!empty($_FILES['product_images']['name'])) {
                $names = (array)$_FILES['product_images']['name'];
                $tmpNames = (array)$_FILES['product_images']['tmp_name'];
                $errors = (array)$_FILES['product_images']['error'];

                foreach ($names as $i => $origName) {
                    if (!empty($origName) && isset($errors[$i]) && $errors[$i] === UPLOAD_ERR_OK && !empty($tmpNames[$i])) {
                        $cleanExt = null;
                        if (is_valid_image_upload($tmpNames[$i], $origName, $cleanExt)) {
                            $fileName = 'prod_' . time() . '_' . bin2hex(random_bytes(4)) . '_' . ($i + 1) . '.' . $cleanExt;
                            if (@move_uploaded_file($tmpNames[$i], $targetDir . $fileName)) {
                                $uploadedImages[] = 'assets/images/products/' . $fileName;
                            }
                        }
                    }
                }
            }

            // Single image fallback
            if (empty($uploadedImages) && !empty($_FILES['product_image']['name']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                $fileTmp = $_FILES['product_image']['tmp_name'];
                $cleanExt = null;
                if (is_valid_image_upload($fileTmp, $_FILES['product_image']['name'], $cleanExt)) {
                    $fileName = 'prod_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $cleanExt;
                    if (@move_uploaded_file($fileTmp, $targetDir . $fileName)) {
                        $uploadedImages[] = 'assets/images/products/' . $fileName;
                    }
                }
            }

            $featuredIndex = isset($_POST['featured_image_index']) ? (int)$_POST['featured_image_index'] : 0;
            $featuredImage = !empty($uploadedImages[$featuredIndex]) ? $uploadedImages[$featuredIndex] : (!empty($uploadedImages[0]) ? $uploadedImages[0] : (!empty($_POST['image']) ? trim($_POST['image']) : 'assets/images/vape-image.webp'));

            // Re-order so featured image is at the top of product_images
            $orderedImages = [];
            if (!empty($uploadedImages)) {
                if (isset($uploadedImages[$featuredIndex])) {
                    $orderedImages[] = $uploadedImages[$featuredIndex];
                }
                foreach ($uploadedImages as $k => $uImg) {
                    if ($k !== $featuredIndex) {
                        $orderedImages[] = $uImg;
                    }
                }
            }

            // Variants parsing
            $variants = [];
            if (!empty($_POST['has_variants']) && !empty($_POST['variants']) && is_array($_POST['variants'])) {
                $varDir = ROOT_PATH . '/public/assets/images/products/';
                if (!is_dir($varDir)) {
                    @mkdir($varDir, 0777, true);
                }

                foreach ($_POST['variants'] as $idx => $v) {
                    if (!empty($v['name'])) {
                        $vImg = trim($v['image_url'] ?? '');
                        if (!empty($_FILES['variant_images']['name'][$idx]) && $_FILES['variant_images']['error'][$idx] === UPLOAD_ERR_OK) {
                            $vTmp = $_FILES['variant_images']['tmp_name'][$idx];
                            $vCleanExt = null;
                            if (is_valid_image_upload($vTmp, $_FILES['variant_images']['name'][$idx], $vCleanExt)) {
                                $vFileName = 'var_' . time() . '_' . bin2hex(random_bytes(3)) . '_' . ($idx + 1) . '.' . $vCleanExt;
                                if (@move_uploaded_file($vTmp, $varDir . $vFileName)) {
                                    $vImg = 'assets/images/products/' . $vFileName;
                                }
                            }
                        }

                        $varPrice = trim($v['price'] ?? '');
                        if ($varPrice === '') {
                            $varPrice = trim($_POST['price'] ?? '0.00');
                        }
                        if ($varPrice === '') {
                            $varPrice = '0.00';
                        }

                        $varStock = trim($v['stock'] ?? '');
                        if ($varStock === '') {
                            $varStock = trim($_POST['stock'] ?? '0');
                        }
                        if ($varStock === '') {
                            $varStock = '0';
                        }

                        $variants[] = [
                            'name' => trim($v['name']),
                            'sku' => trim($v['sku'] ?? ''),
                            'price' => $varPrice,
                            'stock' => $varStock,
                            'image_url' => $vImg
                        ];
                    }
                }
            }

            // Option names parsing
            $optionNames = [];
            if (!empty($_POST['option_names']) && is_array($_POST['option_names'])) {
                foreach ($_POST['option_names'] as $opt) {
                    $opt = trim($opt);
                    if (!empty($opt)) {
                        $optionNames[] = $opt;
                    }
                }
            }

            // Promotional Deal Banner Upload Handling
            $bundleBannerImg = null;
            if (!empty($_FILES['bundle_banner_img']['name']) && $_FILES['bundle_banner_img']['error'] === UPLOAD_ERR_OK) {
                $fileTmp = $_FILES['bundle_banner_img']['tmp_name'];
                $cleanExt = null;
                if (is_valid_image_upload($fileTmp, $_FILES['bundle_banner_img']['name'], $cleanExt)) {
                    $bannerDir = ROOT_PATH . '/public/assets/images/banners/';
                    if (!is_dir($bannerDir)) {
                        @mkdir($bannerDir, 0777, true);
                    }
                    $bannerFileName = 'deal_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $cleanExt;
                    if (@move_uploaded_file($fileTmp, $bannerDir . $bannerFileName)) {
                        $bundleBannerImg = 'assets/images/banners/' . $bannerFileName;
                    }
                }
            } elseif (!empty($_POST['bundle_banner_img_existing'])) {
                $bundleBannerImg = trim($_POST['bundle_banner_img_existing']);
            }

            $bundleBannerActive = (!empty($_POST['bundle_banner_active']) && !empty($bundleBannerImg)) ? 1 : 0;
            $bundleBannerLink = trim($_POST['bundle_banner_link'] ?? '');

            // Process Specifications (Highlights + Detailed Specs)
            $highlights = [];
            if (!empty($_POST['highlights']) && is_array($_POST['highlights'])) {
                foreach ($_POST['highlights'] as $h) {
                    $hKey = trim($h['key'] ?? $h['label'] ?? '');
                    $hVal = trim($h['value'] ?? '');
                    if (!empty($hKey) && !empty($hVal)) {
                        $highlights[] = [
                            'key' => $hKey,
                            'value' => $hVal,
                            'icon_type' => trim($h['icon_type'] ?? 'general')
                        ];
                    }
                }
            }

            $specs = [];
            if (!empty($_POST['specs']) && is_array($_POST['specs'])) {
                foreach ($_POST['specs'] as $s) {
                    $sKey = trim($s['key'] ?? '');
                    $sVal = trim($s['value'] ?? '');
                    if (!empty($sKey) && !empty($sVal)) {
                        $specs[] = [
                            'key' => $sKey,
                            'value' => $sVal,
                            'spec_group' => trim($s['spec_group'] ?? 'General Specs')
                        ];
                    }
                }
            }

            $data = [
                'title' => $title,
                'name' => $title,
                'sku' => trim($_POST['sku'] ?? ''),
                'slug' => trim($_POST['slug'] ?? ''),
                'brand' => trim($_POST['brand'] ?? ''),
                'collection' => trim($_POST['collection'] ?? ''),
                'price' => trim($_POST['price'] ?? '0.00'),
                'compare_price' => trim($_POST['compare_price'] ?? ''),
                'stock' => trim($_POST['stock'] ?? $_POST['stock_quantity'] ?? '0'),
                'status' => trim($_POST['status'] ?? 'published'),
                'description' => trim($_POST['description'] ?? ''),
                'short_desc' => trim($_POST['short_desc'] ?? ''),
                'related_posts_content' => trim($_POST['related_posts_content'] ?? ''),
                'tags' => trim($_POST['tags'] ?? ''),
                'featured_image' => $featuredImage,
                'images' => !empty($orderedImages) ? $orderedImages : $uploadedImages,
                'variants' => $variants,
                'option_names' => !empty($optionNames) ? json_encode(array_values($optionNames)) : '["Flavor"]',
                'highlights' => $highlights,
                'specs' => $specs,
                'seo_title' => trim($_POST['seo_title'] ?? ''),
                'seo_description' => trim($_POST['seo_description'] ?? ''),
                'bundle_banner_active' => $bundleBannerActive,
                'bundle_banner_img' => $bundleBannerImg,
                'bundle_banner_link' => $bundleBannerLink
            ];

            $newId = Product::create($data);

            if ($newId) {
                // Save custom FAQs if provided
                if (!empty($_POST['faqs']) && is_array($_POST['faqs'])) {
                    Product::saveFaqs($newId, $_POST['faqs']);
                }

                set_flash('success', "Product '{$title}' created successfully.");
                $this->redirect('admin/products');
            } else {
                set_flash('error', 'Failed to create product. Please try again.');
                $this->redirect('admin/add-product');
            }
        }

        $collections = Collection::all();
        $brands = Brand::all();

        $this->render('admin.products.add', [
            'page_title' => 'Add New Product',
            'current_admin_page' => 'products',
            'page_script' => 'add-product.js',
            'collections' => $collections,
            'brands' => $brands,
            'error' => get_flash('error'),
            'success' => get_flash('success')
        ]);
    }

    /**
     * Show edit product form or process POST update
     */
    public function edit()
    {
        $id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 1;
        $product = Product::find($id);

        if (!$product) {
            set_flash('error', 'Product not found.');
            $this->redirect('admin/products');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token()) {
                set_flash('error', 'Security token expired or invalid request. Please try again.');
                $this->redirect("admin/edit-product?id={$id}");
                return;
            }

            $title = trim($_POST['title'] ?? '');
            if (empty($title)) {
                set_flash('error', 'Product title cannot be empty.');
                $this->redirect("admin/edit-product?id={$id}");
            }

            // Multiple Images Upload Handling
            $uploadedImages = [];
            $targetDir = ROOT_PATH . '/public/assets/images/products/';
            if (!is_dir($targetDir)) {
                @mkdir($targetDir, 0777, true);
            }

            if (!empty($_FILES['product_images']['name'])) {
                $names = (array)$_FILES['product_images']['name'];
                $tmpNames = (array)$_FILES['product_images']['tmp_name'];
                $errors = (array)$_FILES['product_images']['error'];

                foreach ($names as $i => $origName) {
                    if (!empty($origName) && isset($errors[$i]) && $errors[$i] === UPLOAD_ERR_OK && !empty($tmpNames[$i])) {
                        $cleanExt = null;
                        if (is_valid_image_upload($tmpNames[$i], $origName, $cleanExt)) {
                            $fileName = 'prod_' . time() . '_' . bin2hex(random_bytes(4)) . '_' . ($i + 1) . '.' . $cleanExt;
                            if (@move_uploaded_file($tmpNames[$i], $targetDir . $fileName)) {
                                $uploadedImages[] = 'assets/images/products/' . $fileName;
                            }
                        }
                    }
                }
            }

            // Single image fallback
            if (empty($uploadedImages) && !empty($_FILES['product_image']['name']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                $fileTmp = $_FILES['product_image']['tmp_name'];
                $cleanExt = null;
                if (is_valid_image_upload($fileTmp, $_FILES['product_image']['name'], $cleanExt)) {
                    $fileName = 'prod_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $cleanExt;
                    if (@move_uploaded_file($fileTmp, $targetDir . $fileName)) {
                        $uploadedImages[] = 'assets/images/products/' . $fileName;
                    }
                }
            }

            // Existing images array from form
            $existingImages = [];
            if (isset($_POST['existing_images']) && is_array($_POST['existing_images'])) {
                $existingImages = array_values(array_filter(array_map('trim', $_POST['existing_images'])));
            }

            $featuredIndex = isset($_POST['featured_image_index']) ? (int)$_POST['featured_image_index'] : 0;
            if (!empty($uploadedImages)) {
                $featuredImage = $uploadedImages[$featuredIndex] ?? $uploadedImages[0];
            } elseif (!empty($existingImages)) {
                $featuredImage = $existingImages[$featuredIndex] ?? $existingImages[0];
            } else {
                $featuredImage = isset($_POST['image']) ? trim($_POST['image']) : ($product['featured_image'] ?? 'assets/images/vape-image.webp');
            }

            $orderedImages = [];
            if (!empty($uploadedImages)) {
                if (isset($uploadedImages[$featuredIndex])) {
                    $orderedImages[] = $uploadedImages[$featuredIndex];
                }
                foreach ($uploadedImages as $k => $uImg) {
                    if ($k !== $featuredIndex) {
                        $orderedImages[] = $uImg;
                    }
                }
            }

            // Variants parsing
            $variants = null;
            if (isset($_POST['has_variants'])) {
                $variants = [];
                $varDir = ROOT_PATH . '/public/assets/images/products/';
                if (!is_dir($varDir)) {
                    @mkdir($varDir, 0777, true);
                }

                if (!empty($_POST['variants']) && is_array($_POST['variants'])) {
                    foreach ($_POST['variants'] as $idx => $v) {
                        if (!empty($v['name'])) {
                            $vImg = trim($v['image_url'] ?? '');
                            if (!empty($_FILES['variant_images']['name'][$idx]) && $_FILES['variant_images']['error'][$idx] === UPLOAD_ERR_OK) {
                                $vTmp = $_FILES['variant_images']['tmp_name'][$idx];
                                $vCleanExt = null;
                                if (is_valid_image_upload($vTmp, $_FILES['variant_images']['name'][$idx], $vCleanExt)) {
                                    $vFileName = 'var_' . time() . '_' . bin2hex(random_bytes(3)) . '_' . ($idx + 1) . '.' . $vCleanExt;
                                    if (@move_uploaded_file($vTmp, $varDir . $vFileName)) {
                                        $vImg = 'assets/images/products/' . $vFileName;
                                    }
                                }
                            }

                            $varPrice = trim($v['price'] ?? '');
                            if ($varPrice === '') {
                                $varPrice = trim($_POST['price'] ?? $product['base_price'] ?? '0.00');
                            }
                            if ($varPrice === '') {
                                $varPrice = '0.00';
                            }

                            $varStock = trim($v['stock'] ?? '');
                            if ($varStock === '') {
                                $varStock = trim($_POST['stock'] ?? $product['stock_quantity'] ?? '0');
                            }
                            if ($varStock === '') {
                                $varStock = '0';
                            }

                            $variants[] = [
                                'name' => trim($v['name']),
                                'sku' => trim($v['sku'] ?? ''),
                                'price' => $varPrice,
                                'stock' => $varStock,
                                'image_url' => $vImg
                            ];
                        }
                    }
                }
            }

            // Option names parsing
            $optionNames = [];
            if (!empty($_POST['option_names']) && is_array($_POST['option_names'])) {
                foreach ($_POST['option_names'] as $opt) {
                    $opt = trim($opt);
                    if (!empty($opt)) {
                        $optionNames[] = $opt;
                    }
                }
            }

            // Specifications parsing (Highlights + Detailed Specs)
            $highlights = [];
            if (!empty($_POST['highlights']) && is_array($_POST['highlights'])) {
                foreach ($_POST['highlights'] as $h) {
                    $hKey = trim($h['key'] ?? $h['label'] ?? '');
                    $hVal = trim($h['value'] ?? '');
                    if (!empty($hKey) && !empty($hVal)) {
                        $highlights[] = [
                            'key' => $hKey,
                            'value' => $hVal,
                            'icon_type' => trim($h['icon_type'] ?? 'general')
                        ];
                    }
                }
            }

            $specs = [];
            if (!empty($_POST['specs']) && is_array($_POST['specs'])) {
                foreach ($_POST['specs'] as $s) {
                    $sKey = trim($s['key'] ?? '');
                    $sVal = trim($s['value'] ?? '');
                    if (!empty($sKey) && !empty($sVal)) {
                        $specs[] = [
                            'key' => $sKey,
                            'value' => $sVal,
                            'spec_group' => trim($s['spec_group'] ?? 'General Specs')
                        ];
                    }
                }
            }

            // Promotional Deal Banner Upload Handling
            $bundleBannerActive = !empty($_POST['bundle_banner_active']) ? 1 : 0;
            $bundleBannerImg = $product['bundle_banner_img'] ?? null;
            $bundleBannerLink = trim($_POST['bundle_banner_link'] ?? '');

            if (!empty($_POST['remove_bundle_banner'])) {
                $bundleBannerImg = null;
                $bundleBannerActive = 0;
            } elseif (!empty($_FILES['bundle_banner_img']['name']) && $_FILES['bundle_banner_img']['error'] === UPLOAD_ERR_OK) {
                $fileTmp = $_FILES['bundle_banner_img']['tmp_name'];
                $cleanExt = null;
                if (is_valid_image_upload($fileTmp, $_FILES['bundle_banner_img']['name'], $cleanExt)) {
                    $bannerDir = ROOT_PATH . '/public/assets/images/banners/';
                    if (!is_dir($bannerDir)) {
                        @mkdir($bannerDir, 0777, true);
                    }
                    $bannerFileName = 'deal_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $cleanExt;
                    if (@move_uploaded_file($fileTmp, $bannerDir . $bannerFileName)) {
                        $bundleBannerImg = 'assets/images/banners/' . $bannerFileName;
                    }
                }
            }

            if (empty($bundleBannerImg)) {
                $bundleBannerActive = 0;
            }

            $data = [
                'title' => $title,
                'name' => $title,
                'sku' => trim($_POST['sku'] ?? $product['sku']),
                'slug' => trim($_POST['slug'] ?? $product['slug']),
                'brand' => trim($_POST['brand'] ?? $product['brand']),
                'collection' => trim($_POST['collection'] ?? $product['category']),
                'price' => trim($_POST['price'] ?? $product['base_price']),
                'compare_price' => trim($_POST['compare_price'] ?? $product['compare_price']),
                'stock' => trim($_POST['stock'] ?? $_POST['stock_quantity'] ?? $product['stock_quantity']),
                'status' => trim($_POST['status'] ?? $product['status']),
                'description' => trim($_POST['description'] ?? $product['long_desc']),
                'short_desc' => trim($_POST['short_desc'] ?? $product['short_desc']),
                'related_posts_content' => trim($_POST['related_posts_content'] ?? ($product['related_posts_content'] ?? '')),
                'tags' => trim($_POST['tags'] ?? $product['tags']),
                'featured_image' => $featuredImage,
                'seo_title' => trim($_POST['seo_title'] ?? $product['seo_title']),
                'seo_description' => trim($_POST['seo_description'] ?? $product['seo_description']),
                'bundle_banner_active' => $bundleBannerActive,
                'bundle_banner_img' => $bundleBannerImg,
                'bundle_banner_link' => $bundleBannerLink,
                'highlights' => $highlights,
                'specs' => $specs
            ];

            if (isset($_POST['existing_images'])) {
                $data['existing_images'] = $existingImages;
            }
            if (!empty($orderedImages)) {
                $data['images'] = $orderedImages;
            } elseif (!empty($uploadedImages)) {
                $data['images'] = $uploadedImages;
            }
            if ($variants !== null) {
                $data['variants'] = $variants;
            }
            if (!empty($optionNames)) {
                $data['option_names'] = json_encode(array_values($optionNames));
            }

            $updated = Product::update($id, $data);

            if ($updated) {
                // Save FAQs
                $faqs = (!empty($_POST['faqs']) && is_array($_POST['faqs'])) ? $_POST['faqs'] : [];
                Product::saveFaqs($id, $faqs);

                set_flash('success', "Product '{$title}' updated successfully.");
                $this->redirect('admin/products');
            } else {
                set_flash('error', 'Failed to update product.');
                $this->redirect("admin/edit-product?id={$id}");
            }
        }

        $collections = Collection::all();
        $brands = Brand::all();
        $images = Product::getRawImages($id);
        if (empty($images) && !empty($product['featured_image'])) {
            $images = [['image_url' => $product['featured_image']]];
        }
        $rawVariants = Product::getRawVariants($id);
        $specData = Product::getRawSpecifications($id);
        $faqs = Product::getFaqs($id, $product);

        $this->render('admin.products.edit', [
            'page_title' => 'Edit Product: ' . $product['title'],
            'current_admin_page' => 'products',
            'page_script' => 'edit-product.js',
            'product' => $product,
            'images' => $images,
            'variants' => $rawVariants,
            'highlights' => $specData['highlights'] ?? [],
            'specs' => $specData['specs'] ?? [],
            'faqs' => $faqs,
            'collections' => $collections,
            'brands' => $brands,
            'error' => get_flash('error'),
            'success' => get_flash('success')
        ]);
    }

    /**
     * Delete product (Strictly via POST + CSRF)
     */
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token()) {
            set_flash('error', 'Security token expired or invalid request method.');
            $this->redirect('admin/products');
            return;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id > 0) {
            $product = Product::find($id);
            $title = $product['title'] ?? 'Product #' . $id;
            Product::delete($id);
            set_flash('success', "Product '{$title}' was deleted successfully.");
        } else {
            set_flash('error', 'Invalid product ID.');
        }
        $this->redirect('admin/products');
    }
}
