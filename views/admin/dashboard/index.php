<?php
$page_title = 'Admin Dashboard';
$current_admin_page = 'dashboard';
$page_script = 'dashboard.js';
include dirname(__DIR__) . '/includes/header.php';
?>

<!-- Title & Action Bar -->
<div class="page-title-bar">
  <div class="page-heading-group">
    <h1>Admin Dashboard</h1>
    <p class="page-subtitle">Real-time store performance, catalog metrics, and fulfillment queue.</p>
  </div>
  <div class="page-actions-group">
    <a href="<?= url('admin/add-product') ?>" class="btn-admin btn-admin-primary">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
      </svg>
      <span>Add New Product</span>
    </a>
  </div>
</div>

<!-- 4 KPI Summary Cards -->
<div class="stats-grid">
  
  <!-- 1. Total Revenue -->
  <div class="stat-card">
    <div class="stat-info">
      <span class="stat-label">Total Revenue (Paid)</span>
      <div class="stat-value"><?= $metrics['total_revenue'] ?? '$0.00' ?></div>
      <span class="stat-trend up">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
          <polyline points="17 6 23 6 23 12"></polyline>
        </svg>
        <span>Live store gross receipts</span>
      </span>
    </div>
    <div class="stat-icon-wrap green">
      <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="1" x2="12" y2="23"></line>
        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
      </svg>
    </div>
  </div>

  <!-- 2. Total Orders -->
  <div class="stat-card">
    <div class="stat-info">
      <span class="stat-label">Total Orders</span>
      <div class="stat-value"><?= number_format($metrics['total_orders'] ?? 0) ?></div>
      <span class="stat-trend up">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
          <polyline points="17 6 23 6 23 12"></polyline>
        </svg>
        <span><?= (int)($metrics['pending_orders'] ?? 0) ?> awaiting fulfillment</span>
      </span>
    </div>
    <div class="stat-icon-wrap blue">
      <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
        <line x1="3" y1="6" x2="21" y2="6"></line>
        <path d="M16 10a4 4 0 0 1-8 0"></path>
      </svg>
    </div>
  </div>

  <!-- 3. Active Products -->
  <div class="stat-card">
    <div class="stat-info">
      <span class="stat-label">Published Catalog</span>
      <div class="stat-value"><?= number_format($metrics['published_products'] ?? 0) ?></div>
      <span class="stat-trend up">
        <span>In <?= (int)($metrics['total_collections'] ?? 0) ?> Collections</span>
      </span>
    </div>
    <div class="stat-icon-wrap purple">
      <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
        <polyline points="2 17 12 22 22 17"></polyline>
        <polyline points="2 12 12 17 22 12"></polyline>
      </svg>
    </div>
  </div>

  <!-- 4. Total Customers -->
  <div class="stat-card">
    <div class="stat-info">
      <span class="stat-label">Registered Customers</span>
      <div class="stat-value"><?= number_format($metrics['total_customers'] ?? 0) ?></div>
      <span class="stat-trend up">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
          <polyline points="17 6 23 6 23 12"></polyline>
        </svg>
        <span><?= (int)($metrics['verified_customers'] ?? 0) ?> verified (21+)</span>
      </span>
    </div>
    <div class="stat-icon-wrap orange">
      <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
        <circle cx="9" cy="7" r="4"></circle>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
      </svg>
    </div>
  </div>

</div>

<!-- Main Dashboard Grid: Recent Orders & Top Selling Products -->
<div class="detail-grid-layout">
  
  <!-- Left Side: Recent Orders Table -->
  <div class="admin-card">
    <div class="admin-card-header d-flex justify-between align-center">
      <h2 class="admin-card-title">Recent Orders</h2>
      <a href="<?= url('admin/orders') ?>" class="btn-admin btn-admin-secondary btn-admin-sm">View All Orders</a>
    </div>
    <div class="admin-card-body p-0">
      <div class="table-responsive">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Customer</th>
              <th>Date</th>
              <th>Total</th>
              <th>Status</th>
              <th class="text-right">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($recent_orders)): ?>
              <?php foreach ($recent_orders as $ord): ?>
                <tr>
                  <td>
                    <a href="<?= url('admin/order-detail') ?>?id=<?= htmlspecialchars($ord['id']) ?>" class="table-order-id-link">
                      #<?= htmlspecialchars($ord['id']) ?>
                    </a>
                  </td>
                  <td>
                    <div class="table-customer-cell">
                      <div class="table-customer-avatar"><?= htmlspecialchars($ord['initials']) ?></div>
                      <div>
                        <strong><?= htmlspecialchars($ord['customer']) ?></strong>
                        <div class="table-sub-email"><?= htmlspecialchars($ord['email']) ?></div>
                      </div>
                    </div>
                  </td>
                  <td><?= htmlspecialchars($ord['date']) ?></td>
                  <td><strong><?= htmlspecialchars($ord['total']) ?></strong></td>
                  <td>
                    <span class="badge-status <?= $ord['badge_class'] ?>">
                      <?= htmlspecialchars($ord['status']) ?>
                    </span>
                  </td>
                  <td class="text-right">
                    <a href="<?= url('admin/order-detail') ?>?id=<?= htmlspecialchars($ord['id']) ?>" class="btn-icon-only" title="View Order Details">
                      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                      </svg>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-center" style="padding: 32px; color: #64748b;">
                  No orders recorded yet. As orders are placed in the storefront, they will appear here live.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Right Side: Top Selling Products -->
  <div class="admin-card">
    <div class="admin-card-header d-flex justify-between align-center">
      <h2 class="admin-card-title">Top Selling Products</h2>
      <a href="<?= url('admin/products') ?>" class="btn-admin btn-admin-secondary btn-admin-sm">Catalog</a>
    </div>
    <div class="admin-card-body">
      <div class="info-list-group">
        
        <?php if (!empty($top_products)): ?>
          <?php foreach ($top_products as $tp): 
            $sold = (int)($tp['total_sold'] ?? 0);
            $rev = (float)($tp['total_revenue'] ?? 0);
            $basePrice = (float)($tp['base_price'] ?? 0);
            $pImg = !empty($tp['featured_image']) ? asset($tp['featured_image']) : asset('assets/images/product-1.webp');
          ?>
            <div class="info-list-item" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
              <div class="table-product-cell" style="display: flex; align-items: center; gap: 12px;">
                <img src="<?= $pImg ?>" alt="<?= htmlspecialchars($tp['product_title']) ?>" class="table-product-thumb" style="width: 44px; height: 44px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0;" onerror="this.src='<?= asset('assets/images/vape-image.webp') ?>';">
                <div class="table-product-info">
                  <span class="table-product-title" style="font-weight: 600; font-size: 13.5px; color: #0f172a; display: block;">
                    <?= htmlspecialchars($tp['product_title']) ?>
                  </span>
                  <span class="table-product-meta" style="font-size: 12px; color: #64748b;">
                    <?= $sold > 0 ? "{$sold} sold • " : "In Catalog • " ?>$<?= number_format($basePrice, 2) ?>
                  </span>
                </div>
              </div>
              <div class="info-list-val <?= $rev > 0 ? 'text-success' : '' ?>" style="font-weight: 700; font-size: 13.5px;">
                <?= $rev > 0 ? '+$' . number_format($rev, 2) : '$' . number_format($basePrice, 2) ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-center" style="padding: 24px; color: #64748b; font-size: 13px;">No product sales activity logged yet.</p>
        <?php endif; ?>

      </div>
    </div>
  </div>

</div>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
