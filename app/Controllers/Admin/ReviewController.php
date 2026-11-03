<?php
namespace App\Controllers\Admin;

use App\Models\Review;
use App\Models\Product;

class ReviewController extends AdminController
{
    /**
     * List all reviews with filters, search, and KPI statistics
     */
    public function index()
    {
        $status    = $_GET['status'] ?? 'all';
        $productId = $_GET['product_id'] ?? 'all';
        $search    = $_GET['search'] ?? '';

        $reviews = Review::all([
            'status'     => $status,
            'product_id' => $productId,
            'search'     => $search
        ]);

        $stats    = Review::getStats();
        $products = Product::all();

        $this->render('admin.reviews.index', [
            'page_title'         => 'Customer Reviews Management',
            'current_admin_page' => 'reviews',
            'reviews'            => $reviews,
            'stats'              => $stats,
            'products'           => $products,
            'selected_status'    => $status,
            'selected_product'   => $productId,
            'search_query'       => $search,
            'success'            => get_flash('success'),
            'error'              => get_flash('error')
        ]);
    }

    /**
     * Store a new review created manually from admin
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token()) {
                set_flash('error', 'Security token expired or invalid request. Please try again.');
                $this->redirect('admin/reviews');
                return;
            }

            $productId    = (int)($_POST['product_id'] ?? 0);
            $customerName = trim($_POST['customer_name'] ?? '');
            $rating       = (int)($_POST['rating'] ?? 5);
            $title        = trim($_POST['title'] ?? '');
            $comment      = trim($_POST['comment'] ?? '');
            $status       = in_array($_POST['status'] ?? '', ['approved', 'pending', 'rejected']) ? $_POST['status'] : 'approved';

            if (!$productId || empty($customerName) || empty($comment)) {
                set_flash('error', 'Product, customer name, and review comment are required.');
                $this->redirect('admin/reviews');
                return;
            }

            $created = Review::create([
                'product_id'    => $productId,
                'customer_name' => $customerName,
                'rating'        => $rating,
                'title'         => $title ?: 'Verified Review',
                'comment'       => $comment,
                'status'        => $status
            ]);

            if ($created) {
                set_flash('success', 'Review added successfully and product ratings updated.');
            } else {
                set_flash('error', 'Failed to add review.');
            }
        }

        $this->redirect('admin/reviews');
    }

    /**
     * Update an existing review
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token()) {
                set_flash('error', 'Security token expired or invalid request. Please try again.');
                $this->redirect('admin/reviews');
                return;
            }

            $id           = (int)($_POST['id'] ?? 0);
            $customerName = trim($_POST['customer_name'] ?? '');
            $rating       = (int)($_POST['rating'] ?? 5);
            $title        = trim($_POST['title'] ?? '');
            $comment      = trim($_POST['comment'] ?? '');
            $status       = in_array($_POST['status'] ?? '', ['approved', 'pending', 'rejected']) ? $_POST['status'] : 'approved';

            if (!$id || empty($customerName) || empty($comment)) {
                set_flash('error', 'Review ID, customer name, and comment are required.');
                $this->redirect('admin/reviews');
                return;
            }

            $updated = Review::update($id, [
                'customer_name' => $customerName,
                'rating'        => $rating,
                'title'         => $title,
                'comment'       => $comment,
                'status'        => $status
            ]);

            if ($updated) {
                set_flash('success', 'Review updated successfully.');
            } else {
                set_flash('error', 'Failed to update review.');
            }
        }

        $this->redirect('admin/reviews');
    }

    /**
     * Quick status update (Approve, Reject, Pending)
     */
    public function updateStatus()
    {
        $id     = (int)($_POST['id'] ?? ($_GET['id'] ?? 0));
        $status = $_POST['status'] ?? ($_GET['status'] ?? '');

        if ($id && in_array($status, ['approved', 'pending', 'rejected'])) {
            Review::updateStatus($id, $status);
            
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'status' => $status]);
                exit;
            }

            set_flash('success', "Review marked as " . ucfirst($status) . ".");
        } else {
            set_flash('error', 'Invalid status update request.');
        }

        $this->redirect('admin/reviews');
    }

    /**
     * Delete review (Strictly via POST + CSRF)
     */
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token()) {
            set_flash('error', 'Security token expired or invalid request method.');
            $this->redirect('admin/reviews');
            return;
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id && Review::delete($id)) {
            set_flash('success', 'Review deleted successfully and product rating recalculated.');
        } else {
            set_flash('error', 'Unable to delete review.');
        }

        $this->redirect('admin/reviews');
    }
}
