<?php

/**
 * INFINITY VAPE SHOP - CUSTOMER ACCOUNT DASHBOARD (dashboard.php)
 * Real database-driven customer account management portal:
 * - Orders history & status
 * - Shipping & Billing addresses management
 * - Account details & password update
 */
$page_title = 'My Account Dashboard | Infinity Vape Shop';
$current_page = 'my-account';
$page_js = 'account';

include dirname(__DIR__) . '/includes/header.php';

$cust = $customer ?? [];
$userName = trim(($cust['first_name'] ?? '') . ' ' . ($cust['last_name'] ?? '')) ?: 'Valued Customer';
$userEmail = $cust['email'] ?? '';
$userPhone = $cust['phone'] ?? '';
$shippingAddr = $cust['shipping'] ?? [];
$billingAddr = $cust['billing'] ?? [];
$orders = $cust['orders'] ?? [];
?>

<!-- Breadcrumbs -->
<nav class="category-breadcrumb-nav" aria-label="Breadcrumb">
  <div class="container">
    <ol class="breadcrumb-list">
      <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
      <li class="breadcrumb-separator" aria-hidden="true">
        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </li>
      <li class="breadcrumb-item"><a href="<?= url('my-account') ?>">My Account</a></li>
      <li class="breadcrumb-separator" aria-hidden="true">
        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </li>
      <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
    </ol>
  </div>
</nav>

<!-- Main Customer Dashboard Section -->
<section class="account-dashboard-main">
  <div class="container">

    <!-- Flash Messages -->
    <?php if (!empty($success)): ?>
      <div class="account-flash-alert success">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div class="account-flash-alert error">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <!-- Dashboard Top Welcome Hero -->
    <div class="dashboard-hero-card">
      <div class="dashboard-hero-info">
        <span class="dashboard-hero-badge">
          ✓ Verified 21+ Member
        </span>
        <h1 class="dashboard-hero-title">Hello, <?= htmlspecialchars($userName) ?>!</h1>
        <p class="dashboard-hero-sub">
          Logged in as <strong><?= htmlspecialchars($userEmail) ?></strong>
        </p>
      </div>
      <div class="dashboard-hero-actions">
        <a href="<?= url('collection/all') ?>" class="btn-dash-shop">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
          Shop Vapes
        </a>
        <a href="<?= url('account/logout') ?>" class="btn-dash-logout">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
          Sign Out
        </a>
      </div>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="dashboard-kpi-grid">
      
      <div class="dashboard-kpi-card">
        <span class="dashboard-kpi-label">Total Orders</span>
        <div class="dashboard-kpi-val"><?= count($orders) ?></div>
        <span class="dashboard-kpi-sub">Lifetime Orders</span>
      </div>

      <div class="dashboard-kpi-card">
        <span class="dashboard-kpi-label">Total Spent</span>
        <div class="dashboard-kpi-val"><?= htmlspecialchars($cust['total_spent'] ?? '$0.00') ?></div>
        <span class="dashboard-kpi-sub text-muted">Delivered Value</span>
      </div>

      <div class="dashboard-kpi-card">
        <span class="dashboard-kpi-label">Shipping Destination</span>
        <div class="dashboard-kpi-val-text">
          <?= !empty($shippingAddr['city']) ? htmlspecialchars($shippingAddr['city'] . ', ' . $shippingAddr['state']) : 'Not set yet' ?>
        </div>
        <span class="dashboard-kpi-sub text-muted">Default Address</span>
      </div>

      <div class="dashboard-kpi-card">
        <span class="dashboard-kpi-label">Account Status</span>
        <div class="dashboard-kpi-val-status">Active &amp; Compliant</div>
        <span class="dashboard-kpi-sub text-muted">Same-Day Shipping Ready</span>
      </div>

    </div>

    <!-- 2-Column Dashboard Tabs Layout -->
    <div class="dashboard-grid-layout">
      
      <!-- Left Column: Navigation Tabs -->
      <aside class="dashboard-nav-card">
        <ul class="dashboard-nav-list" role="tablist">
          <li>
            <button type="button" class="dash-tab-btn active" data-tab="tabOrders">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
              My Orders (<?= count($orders) ?>)
            </button>
          </li>
          <li>
            <button type="button" class="dash-tab-btn" data-tab="tabAddresses">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
              Addresses
            </button>
          </li>
          <li>
            <button type="button" class="dash-tab-btn" data-tab="tabProfile">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
              Account Details
            </button>
          </li>
          <li>
            <a href="<?= url('account/logout') ?>" class="dashboard-nav-logout">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
              Log Out
            </a>
          </li>
        </ul>
      </aside>

      <!-- Right Column: Tab Panels Content -->
      <main class="dashboard-content-main">
 
        <!-- TAB 1: ORDERS LIST -->
        <div class="dash-tab-pane active" id="tabOrders">
          <div class="dash-pane-header">
            <h2 class="dash-pane-title">Your Order History</h2>
            <span class="dash-pane-count"><?= count($orders) ?> order(s) placed</span>
          </div>

          <?php if (!empty($orders)): ?>
            <div class="dash-table-wrap">
              <table class="dash-orders-table">
                <thead>
                  <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($orders as $ord): ?>
                    <tr>
                      <td class="dash-order-id">
                        #<?= htmlspecialchars($ord['id']) ?>
                      </td>
                      <td class="dash-order-muted">
                        <?= htmlspecialchars($ord['date']) ?>
                      </td>
                      <td class="dash-order-muted">
                        <?= htmlspecialchars($ord['items']) ?>
                      </td>
                      <td class="dash-order-total">
                        <?= htmlspecialchars($ord['total']) ?>
                      </td>
                      <td>
                        <span class="dash-order-badge <?= strtolower($ord['status']) === 'delivered' ? 'delivered' : 'processing' ?>">
                          <?= htmlspecialchars($ord['status']) ?>
                        </span>
                      </td>
                      <td style="text-align: right;">
                        <a href="<?= url('track-order') ?>?order=<?= urlencode($ord['id']) ?>" class="btn-dash-track">
                          Track Order
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="dash-empty-box">
              <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" class="dash-empty-icon">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <path d="M16 10a4 4 0 0 1-8 0"></path>
              </svg>
              <h3 class="dash-empty-title">No orders placed yet</h3>
              <p class="dash-empty-desc">Ready to find your favorite vape device or e-liquid flavor?</p>
              <a href="<?= url('collection/all') ?>" class="btn-dash-catalog">
                Explore Catalog
              </a>
            </div>
          <?php endif; ?>
        </div>

        <!-- TAB 2: ADDRESSES -->
        <div class="dash-tab-pane" id="tabAddresses">
          <h2 class="dash-pane-title">Saved Addresses</h2>
          <p class="dash-pane-desc">The following addresses will be used on the checkout page by default.</p>

          <div class="dash-address-grid">
            
            <!-- Shipping Address Card -->
            <?php if (!empty($shippingAddr['street'])): ?>
            <div class="dash-address-card">
              <div>
                <div class="dash-address-header">
                  <h3 class="dash-address-title">Shipping Address</h3>
                  <span class="dash-address-tag">Default</span>
                </div>
                <div class="dash-address-body">
                  <strong><?= htmlspecialchars($shippingAddr['name'] ?? $userName) ?></strong><br>
                  <?= htmlspecialchars($shippingAddr['street']) ?><br>
                  <?= !empty($shippingAddr['apartment']) ? htmlspecialchars($shippingAddr['apartment']) . '<br>' : '' ?>
                  <?= htmlspecialchars(($shippingAddr['city'] ?? '') . ', ' . ($shippingAddr['state'] ?? '') . ' ' . ($shippingAddr['zip'] ?? '')) ?><br>
                  <?= htmlspecialchars($shippingAddr['country'] ?? 'United States') ?><br>
                  <?= !empty($shippingAddr['phone']) ? 'Phone: ' . htmlspecialchars($shippingAddr['phone']) : '' ?>
                </div>
              </div>
              <button type="button" onclick="openAddressModal('shipping', true)" class="btn-dash-edit-addr">
                Edit Address
              </button>
            </div>
            <?php else: ?>
            <div class="dash-address-empty">
              <div class="dash-address-empty-icon">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
              </div>
              <h3 class="dash-address-title" style="margin-bottom: 4px;">Shipping Address</h3>
              <p style="color: #6b7280; font-size: 13px; margin: 0 0 16px;">You have not saved a shipping address yet.</p>
              <button type="button" onclick="openAddressModal('shipping', false)" class="btn-dash-add-addr">
                + Add Shipping Address
              </button>
            </div>
            <?php endif; ?>

            <!-- Billing Address Card -->
            <?php if (!empty($billingAddr['street'])): ?>
            <div class="dash-address-card">
              <div>
                <div class="dash-address-header">
                  <h3 class="dash-address-title">Billing Address</h3>
                </div>
                <div class="dash-address-body">
                  <strong><?= htmlspecialchars($billingAddr['name'] ?? $userName) ?></strong><br>
                  <?= htmlspecialchars($billingAddr['street']) ?><br>
                  <?= !empty($billingAddr['apartment']) ? htmlspecialchars($billingAddr['apartment']) . '<br>' : '' ?>
                  <?= htmlspecialchars(($billingAddr['city'] ?? '') . ', ' . ($billingAddr['state'] ?? '') . ' ' . ($billingAddr['zip'] ?? '')) ?><br>
                  <?= htmlspecialchars($billingAddr['country'] ?? 'United States') ?>
                </div>
              </div>
              <button type="button" onclick="openAddressModal('billing', true)" class="btn-dash-edit-addr">
                Edit Address
              </button>
            </div>
            <?php else: ?>
            <div class="dash-address-empty">
              <div class="dash-address-empty-icon">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
              </div>
              <h3 class="dash-address-title" style="margin-bottom: 4px;">Billing Address</h3>
              <p style="color: #6b7280; font-size: 13px; margin: 0 0 16px;">You have not saved a billing address yet.</p>
              <button type="button" onclick="openAddressModal('billing', false)" class="btn-dash-add-addr dark">
                + Add Billing Address
              </button>
            </div>
            <?php endif; ?>

          </div>

          <!-- Edit Address Form Modal Container -->
          <div id="addressEditModal" class="dash-modal-box" style="display: none;">
            <h3 id="addressModalTitle" class="dash-modal-title">Edit Address</h3>
            <form id="addressForm" method="POST" action="<?= url('account/save-address') ?>">
              <?= csrf_field() ?>
              <input type="hidden" name="address_type" id="addrTypeInput" value="shipping">

              <div class="dash-form-row-2">
                <div>
                  <label class="dash-form-label">First Name</label>
                  <input type="text" name="first_name" id="addrFirstName" class="account-input" value="<?= htmlspecialchars($cust['first_name'] ?? '') ?>" required>
                </div>
                <div>
                  <label class="dash-form-label">Last Name</label>
                  <input type="text" name="last_name" id="addrLastName" class="account-input" value="<?= htmlspecialchars($cust['last_name'] ?? '') ?>" required>
                </div>
              </div>

              <div class="dash-form-group">
                <label class="dash-form-label">Street Address</label>
                <input type="text" name="street" id="addrStreet" class="account-input" placeholder="House number and street name" value="" required>
              </div>

              <div class="dash-form-group">
                <label class="dash-form-label">Apartment, suite, unit (optional)</label>
                <input type="text" name="apartment" id="addrSuite" class="account-input" placeholder="Apt 4B (optional)" value="">
              </div>

              <div class="dash-form-row-3">
                <div>
                  <label class="dash-form-label">City</label>
                  <input type="text" name="city" id="addrCity" class="account-input" placeholder="City" value="" required>
                </div>
                <div>
                  <label class="dash-form-label">State</label>
                  <input type="text" name="state" id="addrState" class="account-input" placeholder="TX" value="" required>
                </div>
                <div>
                  <label class="dash-form-label">ZIP Code</label>
                  <input type="text" name="zip" id="addrZip" class="account-input" placeholder="78701" value="" required>
                </div>
              </div>

              <div class="dash-form-actions">
                <button type="submit" class="btn-account-submit" style="width: auto; padding: 10px 24px;">Save Address</button>
                <button type="button" onclick="document.getElementById('addressEditModal').style.display='none'" class="btn-dash-cancel">Cancel</button>
              </div>
            </form>
          </div>
        </div>

        <!-- TAB 3: ACCOUNT DETAILS -->
        <div class="dash-tab-pane" id="tabProfile">
          <h2 class="dash-pane-title">Account Details</h2>
          <p class="dash-pane-desc">Update your personal profile and security password.</p>

          <form id="profileForm" method="POST" action="<?= url('account/update-profile') ?>">
            <?= csrf_field() ?>

            <div class="dash-form-row-2">
              <div>
                <label class="dash-form-label">First Name <span class="required-star">*</span></label>
                <input type="text" name="first_name" class="account-input" value="<?= htmlspecialchars($cust['first_name'] ?? '') ?>" required>
              </div>
              <div>
                <label class="dash-form-label">Last Name <span class="required-star">*</span></label>
                <input type="text" name="last_name" class="account-input" value="<?= htmlspecialchars($cust['last_name'] ?? '') ?>" required>
              </div>
            </div>

            <div class="dash-form-row-2 mb-lg">
              <div>
                <label class="dash-form-label">Email Address</label>
                <input type="email" class="account-input account-input-readonly" value="<?= htmlspecialchars($userEmail) ?>" readonly>
                <span class="account-input-hint">Email is tied to your order records.</span>
              </div>
              <div>
                <label class="dash-form-label">Phone Number</label>
                <input type="tel" name="phone" class="account-input" value="<?= htmlspecialchars($userPhone) ?>" placeholder="+1 (555) 000-0000">
              </div>
            </div>

            <!-- Password Change Fieldset -->
            <div class="dash-profile-divider">
              <h3 class="dash-profile-subtitle">Password Change</h3>
              
              <div class="dash-form-group">
                <label class="dash-form-label">Current Password (leave blank to keep unchanged)</label>
                <input type="password" name="current_password" class="account-input" placeholder="••••••••">
              </div>

              <div class="dash-form-row-2">
                <div>
                  <label class="dash-form-label">New Password</label>
                  <input type="password" name="new_password" class="account-input" placeholder="New password">
                </div>
                <div>
                  <label class="dash-form-label">Confirm New Password</label>
                  <input type="password" name="confirm_password" class="account-input" placeholder="Confirm new password">
                </div>
              </div>
            </div>

            <div style="margin-top: 20px;">
              <button type="submit" class="btn-account-submit" style="width: auto; padding: 12px 28px;">Save Changes</button>
            </div>

          </form>
        </div>

      </main>

    </div>

  </div>
</section>

<script>
window.savedShipping = <?= json_encode($shippingAddr ?: new stdClass()) ?>;
window.savedBilling  = <?= json_encode($billingAddr ?: new stdClass()) ?>;
</script>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
