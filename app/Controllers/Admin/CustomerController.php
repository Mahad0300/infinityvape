<?php
namespace App\Controllers\Admin;

use App\Models\Customer;

class CustomerController extends AdminController
{
    public function index()
    {
        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');

        $customers = Customer::all($search, $status);

        $this->render('admin.customers.index', [
            'page_title' => 'Customers Directory',
            'current_admin_page' => 'customers',
            'page_script' => 'customers.js',
            'customers' => $customers,
            'search' => $search,
            'status' => $status
        ]);
    }

    public function detail()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            set_flash('error', 'Customer ID is required.');
            $this->redirect('admin/customers');
            return;
        }

        $customer = Customer::find($id);

        if (!$customer) {
            set_flash('error', 'The requested customer profile does not exist.');
            $this->redirect('admin/customers');
            return;
        }

        $this->render('admin.customers.detail', [
            'page_title' => 'Customer Profile - ' . htmlspecialchars($customer['name']),
            'current_admin_page' => 'customers',
            'page_script' => 'customer-detail.js',
            'customer' => $customer
        ]);
    }

    public function updateNote()
    {
        header('Content-Type: application/json');

        // Check if JSON request or POST form
        $input = json_decode(file_get_contents('php://input'), true);
        $token = $input['_token'] ?? $_POST['_token'] ?? null;
        if (!verify_csrf_token($token)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Security token expired. Please refresh the page.']);
            exit;
        }

        $customerId = isset($input['customer_id']) ? (int)$input['customer_id'] : (int)($_POST['customer_id'] ?? 0);
        $notes = isset($input['admin_notes']) ? $input['admin_notes'] : ($_POST['admin_notes'] ?? '');

        if (!$customerId) {
            echo json_encode(['success' => false, 'message' => 'Customer ID is required.']);
            exit;
        }

        $result = Customer::updateNotes($customerId, $notes);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Customer notes saved successfully.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to save notes.']);
        }
        exit;
    }
}
