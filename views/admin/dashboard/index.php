<?php
$page_title = 'Dashboard';
$current_admin_page = 'dashboard';
$page_script = 'dashboard.js';
include dirname(__DIR__) . '/includes/header.php';
?>

<!-- Title & Action Bar -->
<div class="page-title-bar">
  <div class="page-heading-group">
    <h1>Admin Dashboard</h1>
    <p class="page-subtitle">Welcome back! Here is what's happening in your vape store today.</p>
  </div>
  <div class="page-actions-group">
    <a href="<?= url('admin/add-product') ?>" class="btn-admin btn-admin-primary">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
      <span class="stat-label">Total Revenue</span>
      <div class="stat-value">$18,492.50</div>
      <span class="stat-trend up">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
          <polyline points="17 6 23 6 23 12"></polyline>
        </svg>
        <span>+14.8% vs last month</span>
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
      <div class="stat-value">342</div>
      <span class="stat-trend up">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
          <polyline points="17 6 23 6 23 12"></polyline>
        </svg>
        <span>+8.2% new orders</span>
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
      <span class="stat-label">Active Products</span>
      <div class="stat-value">20</div>
      <span class="stat-trend up">
        <span>In 9 Collections</span>
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
      <span class="stat-label">Customers</span>
      <div class="stat-value">1,248</div>
      <span class="stat-trend up">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
          <polyline points="17 6 23 6 23 12"></polyline>
        </svg>
        <span>+24 verified (21+)</span>
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
    <div class="admin-card-header">
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
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>#VH-104820</strong></td>
              <td>
                <div class="table-customer-cell">
                  <div class="table-customer-avatar">JD</div>
                  <div>
                    <strong>John Doe</strong>
                    <div class="table-sub-email">johndoe@gmail.com</div>
                  </div>
                </div>
              </td>
              <td>Sep 21, 2026</td>
              <td><strong>$52.99</strong></td>
              <td><span class="badge-status badge-completed">Completed</span></td>
              <td>
                <a href="<?= url('admin/order-detail') ?>?id=104820" class="btn-icon-only" title="View Order Details">
                  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                </a>
              </td>
            </tr>
            <tr>
              <td><strong>#VH-104819</strong></td>
              <td>
                <div class="table-customer-cell">
                  <div class="table-customer-avatar">MS</div>
                  <div>
                    <strong>Michael Smith</strong>
                    <div class="table-sub-email">msmith@yahoo.com</div>
                  </div>
                </div>
              </td>
              <td>Sep 21, 2026</td>
              <td><strong>$39.99</strong></td>
              <td><span class="badge-status badge-processing">Processing</span></td>
              <td>
                <a href="<?= url('admin/order-detail') ?>?id=104819" class="btn-icon-only" title="View Order Details">
                  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                </a>
              </td>
            </tr>
            <tr>
              <td><strong>#VH-104818</strong></td>
              <td>
                <div class="table-customer-cell">
                  <div class="table-customer-avatar">EW</div>
                  <div>
                    <strong>Emily Watson</strong>
                    <div class="table-sub-email">emily.w@outlook.com</div>
                  </div>
                </div>
              </td>
              <td>Sep 20, 2026</td>
              <td><strong>$84.50</strong></td>
              <td><span class="badge-status badge-shipped">Shipped</span></td>
              <td>
                <a href="<?= url('admin/order-detail') ?>?id=104818" class="btn-icon-only" title="View Order Details">
                  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                </a>
              </td>
            </tr>
            <tr>
              <td><strong>#VH-104817</strong></td>
              <td>
                <div class="table-customer-cell">
                  <div class="table-customer-avatar">DK</div>
                  <div>
                    <strong>David Kim</strong>
                    <div class="table-sub-email">david.kim@gmail.com</div>
                  </div>
                </div>
              </td>
              <td>Sep 20, 2026</td>
              <td><strong>$17.99</strong></td>
              <td><span class="badge-status badge-pending">Pending</span></td>
              <td>
                <a href="<?= url('admin/order-detail') ?>?id=104817" class="btn-icon-only" title="View Order Details">
                  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                </a>
              </td>
            </tr>
            <tr>
              <td><strong>#VH-104816</strong></td>
              <td>
                <div class="table-customer-cell">
                  <div class="table-customer-avatar">SL</div>
                  <div>
                    <strong>Sarah Lopez</strong>
                    <div class="table-sub-email">slopez@icloud.com</div>
                  </div>
                </div>
              </td>
              <td>Sep 19, 2026</td>
              <td><strong>$112.40</strong></td>
              <td><span class="badge-status badge-completed">Completed</span></td>
              <td>
                <a href="<?= url('admin/order-detail') ?>?id=104816" class="btn-icon-only" title="View Order Details">
                  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                </a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Right Side: Top Selling Products -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h2 class="admin-card-title">Top Selling Products</h2>
      <a href="<?= url('admin/products') ?>" class="btn-admin btn-admin-secondary btn-admin-sm">Catalog</a>
    </div>
    <div class="admin-card-body">
      <div class="info-list-group">
        
        <div class="info-list-item">
          <div class="table-product-cell">
            <img src="../assets/images/product-1.webp" alt="Juice Head Salt" class="table-product-thumb">
            <div class="table-product-info">
              <span class="table-product-title">Juice Head Salt 50K Bundle</span>
              <span class="table-product-meta">148 sold • $29.99</span>
            </div>
          </div>
          <div class="info-list-val text-success">+$4,438.52</div>
        </div>

        <div class="info-list-item">
          <div class="table-product-cell">
            <img src="../assets/images/most-popular-1.webp" alt="Geek Bar Pulse" class="table-product-thumb">
            <div class="table-product-info">
              <span class="table-product-title">Geek Bar Pulse 15K</span>
              <span class="table-product-meta">215 sold • $14.99</span>
            </div>
          </div>
          <div class="info-list-val text-success">+$3,222.85</div>
        </div>

        <div class="info-list-item">
          <div class="table-product-cell">
            <img src="../assets/images/most-popular-2.webp" alt="GB Pulse 2" class="table-product-thumb">
            <div class="table-product-info">
              <span class="table-product-title">GB Pulse 2 25K</span>
              <span class="table-product-meta">110 sold • $17.99</span>
            </div>
          </div>
          <div class="info-list-val text-success">+$1,978.90</div>
        </div>

        <div class="info-list-item">
          <div class="table-product-cell">
            <img src="../assets/images/most-popular-3.webp" alt="Voopoo Drag S2" class="table-product-thumb">
            <div class="table-product-info">
              <span class="table-product-title">Voopoo Drag S2 Pod Kit</span>
              <span class="table-product-meta">48 sold • $39.99</span>
            </div>
          </div>
          <div class="info-list-val text-success">+$1,919.52</div>
        </div>

      </div>
    </div>
  </div>

</div>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
