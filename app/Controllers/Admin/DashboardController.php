<?php
namespace App\Controllers\Admin;

use App\Core\Database;
use PDO;

class DashboardController extends AdminController
{
    public function index()
    {
        $db = Database::getConnection();

        // 1. Financials & Core KPI Metrics
        $totalRevenue = 0.00;
        $totalOrders = 0;
        $pendingOrders = 0;
        $publishedProducts = 0;
        $totalCollections = 0;
        $totalCustomers = 0;
        $verifiedCustomers = 0;
        $recentOrders = [];
        $topProducts = [];

        if ($db) {
            try {
                // Total Revenue from Paid Orders
                $revStmt = $db->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE payment_status = 'paid'");
                $totalRevenue = (float)$revStmt->fetchColumn();

                // Orders count & Fulfillment backlog
                $ordStmt = $db->query("SELECT COUNT(*) FROM orders");
                $totalOrders = (int)$ordStmt->fetchColumn();

                $pendingStmt = $db->query("SELECT COUNT(*) FROM orders WHERE shipping_status IN ('pending', 'confirmed', 'processing')");
                $pendingOrders = (int)$pendingStmt->fetchColumn();

                // Catalog stats
                $prodStmt = $db->query("SELECT COUNT(*) FROM products WHERE status = 'published'");
                $publishedProducts = (int)$prodStmt->fetchColumn();

                $colStmt = $db->query("SELECT COUNT(*) FROM collections");
                $totalCollections = (int)$colStmt->fetchColumn();

                // Customers stats
                $custStmt = $db->query("SELECT COUNT(*) FROM users WHERE role = 'customer'");
                $totalCustomers = (int)$custStmt->fetchColumn();

                $verStmt = $db->query("SELECT COUNT(*) FROM users WHERE role = 'customer' AND age_verified = 1");
                $verifiedCustomers = (int)$verStmt->fetchColumn();

                // 2. Recent 5 Orders
                $recentStmt = $db->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5");
                $rawRecent = $recentStmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($rawRecent as $r) {
                    $fullName = $r['customer_name'] ?: trim(($r['customer_first_name'] ?? '') . ' ' . ($r['customer_last_name'] ?? ''));
                    if (empty($fullName)) {
                        $fullName = 'Guest Customer';
                    }

                    // Compute avatar initials
                    $parts = explode(' ', trim($fullName));
                    $initials = strtoupper(substr($parts[0] ?? 'C', 0, 1));
                    if (isset($parts[1])) {
                        $initials .= strtoupper(substr($parts[1], 0, 1));
                    }

                    $shipStatus = strtolower($r['shipping_status'] ?? 'pending');
                    $badgeClass = match ($shipStatus) {
                        'delivered' => 'badge-delivered',
                        'shipped', 'in_transit', 'out_for_delivery' => 'badge-shipped',
                        'confirmed', 'processing' => 'badge-completed',
                        'cancelled' => 'badge-cancelled',
                        default => 'badge-pending'
                    };

                    $recentOrders[] = [
                        'id'          => $r['order_number'] ?: (string)$r['id'],
                        'db_id'       => $r['id'],
                        'customer'    => $fullName,
                        'email'       => $r['customer_email'],
                        'initials'    => $initials,
                        'date'        => date('M d, Y', strtotime($r['created_at'])),
                        'total'       => '$' . number_format((float)$r['total_amount'], 2),
                        'status'      => ucwords(str_replace('_', ' ', $shipStatus)),
                        'badge_class' => $badgeClass
                    ];
                }

                // 3. Top Selling Products
                $topStmt = $db->query("
                    SELECT 
                        oi.product_id, 
                        oi.product_title, 
                        p.slug,
                        COALESCE(p.featured_image, 'assets/images/product-1.webp') as featured_image,
                        COALESCE(p.base_price, oi.price_at_purchase) as base_price,
                        SUM(oi.quantity) as total_sold, 
                        SUM(oi.quantity * oi.price_at_purchase) as total_revenue
                    FROM order_items oi
                    LEFT JOIN products p ON p.id = oi.product_id
                    GROUP BY oi.product_id, oi.product_title, p.slug, p.featured_image, p.base_price
                    ORDER BY total_sold DESC
                    LIMIT 5
                ");
                $topProducts = $topStmt->fetchAll(PDO::FETCH_ASSOC);

                // If fewer than 4 sold products, supplement with top published catalog products
                if (count($topProducts) < 4) {
                    $existingIds = array_column($topProducts, 'product_id');
                    $notInClause = !empty($existingIds) 
                        ? "WHERE id NOT IN (" . implode(',', array_map('intval', $existingIds)) . ") AND status = 'published'" 
                        : "WHERE status = 'published'";
                    $needed = 4 - count($topProducts);
                    $suppStmt = $db->query("SELECT id as product_id, title as product_title, slug, featured_image, base_price, 0 as total_sold, 0 as total_revenue FROM products {$notInClause} LIMIT {$needed}");
                    $topProducts = array_merge($topProducts, $suppStmt->fetchAll(PDO::FETCH_ASSOC));
                }

            } catch (\Exception $e) {}
        }

        $this->render('admin.dashboard.index', [
            'page_title'         => 'Admin Dashboard',
            'current_admin_page' => 'dashboard',
            'page_script'        => 'dashboard.js',
            'metrics'            => [
                'total_revenue'      => '$' . number_format($totalRevenue, 2),
                'total_orders'       => $totalOrders,
                'pending_orders'     => $pendingOrders,
                'published_products' => $publishedProducts,
                'total_collections'  => $totalCollections,
                'total_customers'    => $totalCustomers,
                'verified_customers' => $verifiedCustomers
            ],
            'recent_orders'      => $recentOrders,
            'top_products'       => $topProducts
        ]);
    }
}
