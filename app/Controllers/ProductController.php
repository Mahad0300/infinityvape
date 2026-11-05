<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $identifier = $_GET['slug'] ?? ($_GET['p'] ?? ($_GET['id'] ?? null));
        
        $product = null;
        if (!empty($identifier)) {
            $product = Product::find($identifier);
            if (!$product) {
                http_response_code(404);
                $this->render('errors.404', ['uri' => 'Product Not Found: ' . htmlspecialchars($identifier)]);
                return;
            }
        } else {
            // Only if accessed via bare /product route without slug, load first published product
            $db = Product::getDB();
            if ($db) {
                $stmt = $db->query("SELECT id FROM products WHERE status = 'published' ORDER BY id ASC LIMIT 1");
                $first = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($first) {
                    $product = Product::find($first['id']);
                }
            }
        }

        if (!$product) {
            http_response_code(404);
            $this->render('errors.404', ['uri' => 'Product Not Found']);
            return;
        }

        // Product Status Access Control:
        // 1. Draft: Forbidden on storefront even via direct URL (returns 404).
        //    Only authenticated admins with explicit ?preview=admin can preview.
        $prodStatus = strtolower(trim($product['status'] ?? 'published'));
        $isAdminPreview = (!empty($_GET['preview']) && class_exists('\\App\\Core\\AdminAuth') && \App\Core\AdminAuth::check());

        if ($prodStatus === 'draft' && !$isAdminPreview) {
            http_response_code(404);
            $this->render('errors.404', ['uri' => 'Product Not Found: ' . htmlspecialchars($identifier ?? '')]);
            return;
        }

        // Clean URL Enforcement: Redirect any ?slug= or ?id= query param to clean /product/{slug}
        $rawUri = $_SERVER['REQUEST_URI'] ?? '';
        if (strpos($rawUri, '?slug=') !== false || strpos($rawUri, '?id=') !== false || strpos($rawUri, '?p=') !== false) {
            $cleanSlug = !empty($product['slug']) ? $product['slug'] : $product['id'];
            $previewSuffix = $isAdminPreview ? '?preview=admin' : '';
            $targetUrl = url('product/' . urlencode($cleanSlug)) . $previewSuffix;
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: " . $targetUrl);
            exit;
        }

        $id = $product['id'];

        // Dynamic Product Specifications, Highlights & Content
        $productHighlights = Product::getHighlights($id, $product);
        $productSpecs      = Product::getSpecifications($id, $product);
        $productVariants   = Product::getVariants($id, $product);
        $productFaqs       = Product::getFaqs($id, $product);
        $productReviews    = Product::getReviews($id);
        $relatedPosts      = Product::getRelatedPosts($id);
        $productImages     = Product::getImages($id, $product);
        $relatedProducts   = Product::getRelatedProducts($id, $product['brand_id'] ?? null, $product['collection_id'] ?? null, 6);

        $cleanLongDesc = !empty($product['long_desc']) ? trim(preg_replace('/\s+/', ' ', strip_tags($product['long_desc']))) : '';
        if (mb_strlen($cleanLongDesc) > 160) {
            $cleanLongDesc = mb_substr($cleanLongDesc, 0, 157) . '...';
        }
        $cleanShortDesc = !empty($product['short_desc']) ? trim(preg_replace('/\s+/', ' ', strip_tags($product['short_desc']))) : '';
        if (mb_strlen($cleanShortDesc) > 160) {
            $cleanShortDesc = mb_substr($cleanShortDesc, 0, 157) . '...';
        }

        $pageTitle = !empty($product['seo_title']) 
            ? trim($product['seo_title']) 
            : trim($product['title'] ?? ($product['name'] ?? 'Product'));

        $metaDesc = !empty($product['seo_description']) 
            ? trim($product['seo_description']) 
            : (!empty($cleanShortDesc) ? $cleanShortDesc : $cleanLongDesc);
        $canonicalUrl = url('product/' . urlencode(!empty($product['slug']) ? $product['slug'] : $product['id']));

        $this->render('front.product.index', [
            'page_title'        => $pageTitle,
            'meta_description'  => $metaDesc,
            'canonical_url'     => $canonicalUrl,
            'page_js'           => 'product',
            'product'           => $product,
            'productImages'     => $productImages,
            'relatedProducts'   => $relatedProducts,
            'productHighlights' => $productHighlights,
            'productSpecs'      => $productSpecs,
            'productVariants'   => $productVariants,
            'productFaqs'       => $productFaqs,
            'productReviews'    => $productReviews,
            'relatedPosts'      => $relatedPosts,
            'isArchived'        => ($prodStatus === 'archived'),
            'isDraftPreview'    => $isAdminPreview
        ]);
    }

    /**
     * Handle Customer Review Submission
     */
    public function submitReview()
    {
        $input = [];
        $rawJson = file_get_contents('php://input');
        if (!empty($rawJson)) {
            $decoded = json_decode($rawJson, true);
            if (is_array($decoded)) {
                $input = $decoded;
            }
        }
        if (empty($input)) {
            $input = $_POST;
        }

        $productId    = (int)($input['product_id'] ?? 0);
        $customerName = trim($input['nickname'] ?? ($input['customer_name'] ?? ''));
        $title        = trim($input['title'] ?? '');
        $comment      = trim($input['comment'] ?? ($input['body'] ?? ''));
        $rating       = max(1, min(5, (int)($input['rating'] ?? 5)));

        if (!$productId || empty($customerName) || empty($title) || empty($comment)) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'All fields (Nickname, Rating, Title, and Review) are required.'
            ]);
            return;
        }

        $reviewId = \App\Models\Review::create([
            'product_id'    => $productId,
            'customer_name' => $customerName,
            'rating'        => $rating,
            'title'         => $title,
            'comment'       => $comment,
            'status'        => 'pending'
        ]);

        if ($reviewId) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'status'  => 'pending',
                'message' => 'Thank you! Your review has been submitted and is pending moderation. It will be published once approved by our team.'
            ]);
            return;
        }

        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Unable to save review. Please try again.'
        ]);
    }
}
