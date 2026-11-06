<?php
namespace App\Controllers\Admin;

use App\Models\Coupon;
use PDO;

class CouponController extends AdminController
{
    /**
     * Display Coupons Management list and metrics
     */
    public function index()
    {
        $search = trim($_GET['search'] ?? '');
        $type = trim($_GET['type'] ?? '');
        $status = trim($_GET['status'] ?? '');

        $coupons = Coupon::all($search, $type, $status);
        $metrics = Coupon::metrics();

        $this->render('admin.coupons.index', [
            'page_title'         => 'Coupons & Discounts',
            'current_admin_page' => 'coupons',
            'page_script'        => 'coupons.js',
            'coupons'            => $coupons,
            'metrics'            => $metrics,
            'filters'            => [
                'search' => $search,
                'type'   => $type,
                'status' => $status
            ]
        ]);
    }

    /**
     * Create a new coupon
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['success' => false, 'message' => 'Invalid request method.'], 405);
        }

        $input = $this->getPayload();

        $code = strtoupper(trim((string)($input['code'] ?? '')));
        $type = trim((string)($input['type'] ?? 'percentage'));
        $value = (float)($input['value'] ?? 0);
        $minOrder = (float)($input['min_order_amount'] ?? 0);
        $maxUses = !empty($input['max_uses']) ? (int)$input['max_uses'] : null;
        $startDate = !empty($input['start_date']) ? date('Y-m-d H:i:s', strtotime($input['start_date'])) : null;
        $endDate = !empty($input['end_date']) ? date('Y-m-d H:i:s', strtotime($input['end_date'])) : null;
        $isActive = isset($input['is_active']) ? (int)$input['is_active'] : 1;

        // Validations
        if ($code === '') {
            return $this->json(['success' => false, 'message' => 'Coupon code is required.'], 400);
        }

        if (!preg_match('/^[A-Z0-9_-]{3,30}$/', $code)) {
            return $this->json(['success' => false, 'message' => 'Coupon code must be 3-30 alphanumeric characters (hyphens and underscores allowed).'], 400);
        }

        $existing = Coupon::findByCode($code);
        if ($existing) {
            return $this->json(['success' => false, 'message' => "Coupon code '{$code}' already exists."], 400);
        }

        if (!in_array($type, ['percentage', 'fixed_amount', 'free_shipping'])) {
            return $this->json(['success' => false, 'message' => 'Invalid discount type.'], 400);
        }

        if ($type === 'percentage' && ($value <= 0 || $value > 100)) {
            return $this->json(['success' => false, 'message' => 'Percentage discount must be between 1% and 100%.'], 400);
        }

        if ($type === 'fixed_amount' && $value <= 0) {
            return $this->json(['success' => false, 'message' => 'Fixed discount amount must be greater than $0.00.'], 400);
        }

        if ($type === 'free_shipping' && $value <= 0) {
            $value = 5.99; // Standard shipping waiver value
        }

        if ($startDate && $endDate && strtotime($startDate) >= strtotime($endDate)) {
            return $this->json(['success' => false, 'message' => 'Expiration date must be after start date.'], 400);
        }

        $created = Coupon::create([
            'code'             => $code,
            'type'             => $type,
            'value'            => $value,
            'min_order_amount' => $minOrder,
            'max_uses'         => $maxUses,
            'start_date'       => $startDate,
            'end_date'         => $endDate,
            'is_active'        => $isActive
        ]);

        if ($created) {
            return $this->json([
                'success' => true,
                'message' => "Coupon '{$code}' created successfully!"
            ]);
        }

        return $this->json(['success' => false, 'message' => 'Failed to create coupon in database.'], 500);
    }

    /**
     * Update an existing coupon
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['success' => false, 'message' => 'Invalid request method.'], 405);
        }

        $input = $this->getPayload();
        $id = (int)($input['id'] ?? 0);

        if ($id <= 0) {
            return $this->json(['success' => false, 'message' => 'Invalid coupon ID.'], 400);
        }

        $coupon = Coupon::find($id);
        if (!$coupon) {
            return $this->json(['success' => false, 'message' => 'Coupon not found.'], 404);
        }

        $code = strtoupper(trim((string)($input['code'] ?? $coupon['code'])));
        $type = trim((string)($input['type'] ?? $coupon['type']));
        $value = (float)($input['value'] ?? $coupon['value']);
        $minOrder = isset($input['min_order_amount']) ? (float)$input['min_order_amount'] : (float)$coupon['min_order_amount'];
        $maxUses = !empty($input['max_uses']) ? (int)$input['max_uses'] : null;
        $startDate = !empty($input['start_date']) ? date('Y-m-d H:i:s', strtotime($input['start_date'])) : null;
        $endDate = !empty($input['end_date']) ? date('Y-m-d H:i:s', strtotime($input['end_date'])) : null;
        $isActive = isset($input['is_active']) ? (int)$input['is_active'] : (int)$coupon['is_active'];

        // Code uniqueness check (ignoring current coupon)
        $existing = Coupon::findByCode($code);
        if ($existing && (int)$existing['id'] !== $id) {
            return $this->json(['success' => false, 'message' => "Coupon code '{$code}' is already used by another coupon."], 400);
        }

        if ($type === 'percentage' && ($value <= 0 || $value > 100)) {
            return $this->json(['success' => false, 'message' => 'Percentage discount must be between 1% and 100%.'], 400);
        }

        if ($type === 'fixed_amount' && $value <= 0) {
            return $this->json(['success' => false, 'message' => 'Fixed discount amount must be greater than $0.00.'], 400);
        }

        if ($startDate && $endDate && strtotime($startDate) >= strtotime($endDate)) {
            return $this->json(['success' => false, 'message' => 'Expiration date must be after start date.'], 400);
        }

        $updated = Coupon::updateCoupon($id, [
            'code'             => $code,
            'type'             => $type,
            'value'            => $value,
            'min_order_amount' => $minOrder,
            'max_uses'         => $maxUses,
            'start_date'       => $startDate,
            'end_date'         => $endDate,
            'is_active'        => $isActive
        ]);

        if ($updated) {
            return $this->json([
                'success' => true,
                'message' => "Coupon '{$code}' updated successfully!"
            ]);
        }

        return $this->json(['success' => false, 'message' => 'Failed to update coupon.'], 500);
    }

    /**
     * Toggle coupon active / inactive
     */
    public function toggle()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['success' => false, 'message' => 'Invalid request method.'], 405);
        }

        $input = $this->getPayload();
        $id = (int)($input['id'] ?? 0);

        if ($id <= 0) {
            return $this->json(['success' => false, 'message' => 'Invalid coupon ID.'], 400);
        }

        $coupon = Coupon::find($id);
        if (!$coupon) {
            return $this->json(['success' => false, 'message' => 'Coupon not found.'], 404);
        }

        $toggled = Coupon::toggle($id);
        if ($toggled) {
            $updated = Coupon::find($id);
            return $this->json([
                'success'   => true,
                'message'   => "Coupon '{$coupon['code']}' status changed to " . ($updated['is_active'] ? 'Active' : 'Inactive') . ".",
                'is_active' => (int)$updated['is_active']
            ]);
        }

        return $this->json(['success' => false, 'message' => 'Failed to toggle coupon status.'], 500);
    }

    /**
     * Delete coupon
     */
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['success' => false, 'message' => 'Invalid request method.'], 405);
        }

        $input = $this->getPayload();
        $id = (int)($input['id'] ?? 0);

        if ($id <= 0) {
            return $this->json(['success' => false, 'message' => 'Invalid coupon ID.'], 400);
        }

        $coupon = Coupon::find($id);
        if (!$coupon) {
            return $this->json(['success' => false, 'message' => 'Coupon not found.'], 404);
        }

        $deleted = Coupon::deleteCoupon($id);
        if ($deleted) {
            return $this->json([
                'success' => true,
                'message' => "Coupon '{$coupon['code']}' has been permanently deleted."
            ]);
        }

        return $this->json(['success' => false, 'message' => 'Failed to delete coupon.'], 500);
    }

    /**
     * Helper to read JSON or POST payload
     */
    private function getPayload(): array
    {
        $raw = file_get_contents('php://input');
        $json = json_decode($raw, true);
        if (is_array($json)) {
            return $json;
        }
        return $_POST;
    }
}

