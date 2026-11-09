<?php
$page_title = 'Customer Profile';
$current_admin_page = 'customers';
$page_script = 'customer-detail.js';
include dirname(__DIR__) . '/includes/header.php';

// If $customer is not set by controller, load dynamically from model
if (!isset($customer) || empty($customer)) {
    $cust_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $customer = $cust_id > 0 ? \App\Models\Customer::find($cust_id) : null;
}

if (!$customer) {
    echo '<div class="admin-card"><div class="admin-card-body text-center p-xl"><h3>Customer Profile Not Found</h3><p class="text-muted">The requested customer could not be found in the database.</p><a href="' . url('admin/customers') . '" class="btn-admin btn-admin-primary mt-md">Back to Customers Directory</a></div></div>';
    include dirname(__DIR__) . '/includes/footer.php';
    return;
}

// Ensure avatar initials
$name_parts = explode(' ', trim($customer['name'] ?? 'Guest Customer'));
$avatar_initials = strtoupper(substr($name_parts[0], 0, 1) . (isset($name_parts[1]) ? substr($name_parts[1], 0, 1) : ''));
if (empty($avatar_initials)) $avatar_initials = 'CU';
?>

<!-- Title & Action Bar -->
<div class="page-title-bar">
  <div class="page-heading-group">
    <div class="d-flex align-center gap-md">
      <a href="<?= url('admin/customers') ?>" class="btn-icon-only" title="Back to Customers">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="19" y1="12" x2="5" y2="12"></line>
          <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
      </a>
      <h1><?php echo htmlspecialchars($customer['name']); ?></h1>
      <?php if (!empty($customer['age_verified'])): ?>
        <span class="badge-status badge-completed">21+ Age Verified</span>
      <?php else: ?>
        <span class="badge-status badge-pending">Age Unverified</span>
      <?php endif; ?>
    </div>
    <p class="page-subtitle">Customer #VH-CUST<?php echo str_pad((string)$customer['id'], 4, '0', STR_PAD_LEFT); ?> • Registered <?php echo htmlspecialchars($customer['joined']); ?></p>
  </div>
  <div class="page-actions-group">
    <a href="mailto:<?php echo htmlspecialchars($customer['email']); ?>" class="btn-admin btn-admin-secondary">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
        <polyline points="22,6 12,13 2,6"></polyline>
      </svg>
      <span>Contact Customer</span>
    </a>
  </div>
</div>

<!-- Customer Stats Summary Row (Styled with stats-grid) -->
<div class="stats-grid mb-xl">
  <!-- 1. Lifetime Spend -->
  <div class="stat-card">
    <div class="stat-info">
      <span class="stat-label">Lifetime Spend</span>
      <div class="stat-value"><?php echo $customer['total_spent']; ?></div>
      <span class="stat-trend up">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
          <polyline points="17 6 23 6 23 12"></polyline>
        </svg>
        <span>Completed revenue</span>
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
      <div class="stat-value"><?php echo $customer['orders_count']; ?></div>
      <span class="stat-trend up">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
          <polyline points="17 6 23 6 23 12"></polyline>
        </svg>
        <span>Lifetime purchases</span>
      </span>
    </div>
    <div class="stat-icon-wrap blue">
      <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="9" cy="21" r="1"></circle>
        <circle cx="20" cy="21" r="1"></circle>
        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
      </svg>
    </div>
  </div>

  <!-- 3. Avg. Order Value -->
  <div class="stat-card">
    <div class="stat-info">
      <span class="stat-label">Avg. Order Value</span>
      <div class="stat-value"><?php echo $customer['avg_order']; ?></div>
      <span class="stat-trend up">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
          <polyline points="17 6 23 6 23 12"></polyline>
        </svg>
        <span>Consistent buyer</span>
      </span>
    </div>
    <div class="stat-icon-wrap purple">
      <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="20" x2="18" y2="10"></line>
        <line x1="12" y1="20" x2="12" y2="4"></line>
        <line x1="6" y1="20" x2="6" y2="14"></line>
      </svg>
    </div>
  </div>

  <!-- 4. Account Standing -->
  <div class="stat-card">
    <div class="stat-info">
      <span class="stat-label">Account Standing</span>
      <?php 
        $statusVal = strtolower($customer['status'] ?? 'active');
        $statusLabel = ucfirst($statusVal);
        $statusClass = $statusVal === 'active' ? 'text-success' : ($statusVal === 'banned' ? 'text-danger' : 'text-warning');
      ?>
      <div class="stat-value <?= $statusClass ?>"><?= $statusLabel ?></div>
      <span class="stat-trend up">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
        <span><?= !empty($customer['tags']) ? htmlspecialchars($customer['tags']) : 'Verified Customer' ?></span>
      </span>
    </div>
    <div class="stat-icon-wrap amber">
      <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
        <polyline points="22 4 12 14.01 9 11.01"></polyline>
      </svg>
    </div>
  </div>
</div>

<!-- Main Detail Layout -->
<div class="detail-grid-layout">
  
  <!-- Left Side: Order History & Purchased Products -->
  <div class="admin-col-main">
    
    <!-- Order History Card -->
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 class="admin-card-title">Order History (<?php echo count($customer['orders']); ?>)</h2>
      </div>
      <div class="admin-card-body p-0">
        <div class="table-responsive">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Items</th>
                <th>Status</th>
                <th>Total</th>
                <th class="text-right">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($customer['orders'])): ?>
                <?php foreach ($customer['orders'] as $ord): ?>
                  <tr>
                    <td>
                      <a href="<?= url('admin/order-detail') ?>?id=<?php echo $ord['db_id']; ?>" class="customer-detail-name">
                        #VH-<?php echo htmlspecialchars($ord['id']); ?>
                      </a>
                    </td>
                    <td><?php echo htmlspecialchars($ord['date']); ?></td>
                    <td><?php echo htmlspecialchars($ord['items']); ?></td>
                    <td>
                      <span class="badge-status <?php echo htmlspecialchars($ord['status_class']); ?>">
                        <?php echo htmlspecialchars($ord['status']); ?>
                      </span>
                    </td>
                    <td><strong><?php echo htmlspecialchars($ord['total']); ?></strong></td>
                    <td class="text-right">
                      <a href="<?= url('admin/order-detail') ?>?id=<?php echo $ord['db_id']; ?>" class="btn-admin btn-admin-secondary btn-admin-sm">
                        <span>View</span>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center p-xl text-muted">
                    No orders placed yet by this customer.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Favorite Purchased Products Card -->
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 class="admin-card-title">Frequently Purchased Items</h2>
      </div>
      <div class="admin-card-body p-0">
        <div class="table-responsive">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Last Purchased</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($customer['frequent_items'])): ?>
                <?php foreach ($customer['frequent_items'] as $fav): ?>
                  <?php 
                    if (!empty($fav['featured_image'])) {
                        if (strpos($fav['featured_image'], 'assets/') === 0 || strpos($fav['featured_image'], '/') !== false) {
                            $thumb = asset(ltrim($fav['featured_image'], '/'));
                        } else {
                            $thumb = asset('assets/images/products/' . $fav['featured_image']);
                        }
                    } else {
                        $thumb = asset('assets/images/no-image.png');
                    }
                  ?>
                  <tr>
                    <td>
                      <div class="table-product-cell">
                        <img src="<?= $thumb ?>" alt="<?= htmlspecialchars($fav['product_title']) ?>" class="table-product-thumb" onerror="this.src='<?= asset('assets/images/no-image.png') ?>'">
                        <div class="table-product-info">
                          <span class="table-product-title"><?= htmlspecialchars($fav['product_title']) ?></span>
                          <?php if (!empty($fav['variant_name'])): ?>
                            <span class="table-product-meta"><?= htmlspecialchars($fav['variant_name']) ?></span>
                          <?php endif; ?>
                        </div>
                      </div>
                    </td>
                    <td><span class="badge-status badge-completed"><?= (int)$fav['total_qty'] ?> purchased</span></td>
                    <td><?= date('M d, Y', strtotime($fav['last_purchased'])) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="3" class="text-center p-xl text-muted">
                    No purchased items recorded for this customer yet.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>

  <!-- Right Side: Customer Info, Verification & Addresses -->
  <div class="admin-col-side">
    
    <!-- Profile Card -->
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 class="admin-card-title">Customer Details</h2>
      </div>
      <div class="admin-card-body">
        <div class="table-customer-cell customer-card-header-user">
          <div class="table-customer-avatar customer-avatar-lg"><?php echo $avatar_initials; ?></div>
          <div>
            <strong class="customer-detail-name"><?php echo htmlspecialchars($customer['name']); ?></strong>
            <div class="customer-since-meta">Member since <?php echo htmlspecialchars($customer['joined']); ?></div>
          </div>
        </div>

        <div class="info-list-group">
          <div class="info-list-item">
            <span class="info-list-label">Email</span>
            <span class="info-list-val"><a href="mailto:<?php echo htmlspecialchars($customer['email']); ?>" class="link-primary"><?php echo htmlspecialchars($customer['email']); ?></a></span>
          </div>
          <div class="info-list-item">
            <span class="info-list-label">Phone</span>
            <span class="info-list-val"><?php echo htmlspecialchars($customer['phone'] ?? 'Not provided'); ?></span>
          </div>
          <div class="info-list-item">
            <span class="info-list-label">Newsletter</span>
            <span class="info-list-val text-success">Subscribed</span>
          </div>
          <div class="info-list-item">
            <span class="info-list-label">Account Status</span>
            <span class="info-list-val <?= $statusClass ?>"><strong><?= $statusLabel ?></strong></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Age Verification Details -->
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 class="admin-card-title">21+ Compliance &amp; Verification</h2>
      </div>
      <div class="admin-card-body">
        <div class="info-list-group">
          <div class="info-list-item">
            <span class="info-list-label">Verification Status</span>
            <?php if (!empty($customer['age_verified'])): ?>
              <span class="info-list-val text-success"><strong>✓ Pass (21+)</strong></span>
            <?php else: ?>
              <span class="info-list-val text-warning"><strong>⚠ Pending Verification</strong></span>
            <?php endif; ?>
          </div>
          <div class="info-list-item">
            <span class="info-list-label">Date of Birth</span>
            <span class="info-list-val"><?php echo htmlspecialchars($customer['dob']); ?></span>
          </div>
          <div class="info-list-item">
            <span class="info-list-label">Provider Method</span>
            <span class="info-list-val customer-since-meta"><?php echo htmlspecialchars($customer['verify_method']); ?></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Shipping & Billing Address Card -->
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 class="admin-card-title">Saved Addresses</h2>
      </div>
      <div class="admin-card-body">
        <div class="address-block">
          <div class="address-title">Default Shipping Address</div>
          <p class="address-text">
            <strong><?php echo htmlspecialchars($customer['shipping']['name']); ?></strong><br>
            <?php echo htmlspecialchars($customer['shipping']['street_address'] ?? ($customer['shipping']['street'] ?? '')); ?>
            <?php if (!empty($customer['shipping']['apartment'])): ?>
              , <?php echo htmlspecialchars($customer['shipping']['apartment']); ?>
            <?php endif; ?><br>
            <?php echo htmlspecialchars(($customer['shipping']['city'] ?? '') . ', ' . ($customer['shipping']['state'] ?? '') . ' ' . ($customer['shipping']['zip'] ?? '')); ?><br>
            <?php echo htmlspecialchars($customer['shipping']['country'] ?? 'United States'); ?>
          </p>
        </div>

        <div>
          <div class="address-title">Default Billing Address</div>
          <?php if (!empty($customer['billing']) && ($customer['billing'] !== $customer['shipping'])): ?>
            <p class="address-text">
              <strong><?php echo htmlspecialchars($customer['billing']['name']); ?></strong><br>
              <?php echo htmlspecialchars($customer['billing']['street_address'] ?? ($customer['billing']['street'] ?? '')); ?>
              <?php if (!empty($customer['billing']['apartment'])): ?>
                , <?php echo htmlspecialchars($customer['billing']['apartment']); ?>
              <?php endif; ?><br>
              <?php echo htmlspecialchars(($customer['billing']['city'] ?? '') . ', ' . ($customer['billing']['state'] ?? '') . ' ' . ($customer['billing']['zip'] ?? '')); ?><br>
              <?php echo htmlspecialchars($customer['billing']['country'] ?? 'United States'); ?>
            </p>
          <?php else: ?>
            <p class="address-text-muted">Same as shipping address</p>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Admin Notes -->
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 class="admin-card-title">Staff Internal Notes</h2>
      </div>
      <div class="admin-card-body">
        <form id="customerNoteForm" class="admin-ajax-form">
          <?= csrf_field() ?>
          <input type="hidden" name="customer_id" value="<?php echo (int)$customer['id']; ?>">
          <div class="admin-form-group">
            <textarea name="admin_notes" id="adminNotesTextarea" class="admin-form-textarea" rows="3" placeholder="Add private note about this customer (e.g. VIP buyer, preferred delivery instructions)..."><?php echo htmlspecialchars($customer['admin_notes'] ?? ''); ?></textarea>
          </div>
          <div id="noteStatusMsg" class="mb-sm text-sm" style="display:none;"></div>
          <button type="submit" id="saveNoteBtn" class="btn-admin btn-admin-primary btn-full-width">Save Note</button>
        </form>
      </div>
    </div>

  </div>

</div>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
