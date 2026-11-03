<?php
/**
 * INFINITY VAPE SHOP - CUSTOMER ACCOUNT DASHBOARD (dashboard.php)
 * Real database-driven customer account management portal:
 * - Orders history & status
 * - Shipping & Billing addresses management
 * - Account details & password update
 */
$page_title   = 'My Account Dashboard | Infinity Vape Shop';
$current_page = 'my-account';
$page_js      = 'account';

include dirname(__DIR__) . '/includes/header.php';

$cust = $customer ?? [];
$userName = trim(($cust['first_name'] ?? '') . ' ' . ($cust['last_name'] ?? '')) ?: 'Valued Customer';
$userEmail = $cust['email'] ?? '';
$userPhone = $cust['phone'] ?? '';
$shippingAddr = $cust['shipping'] ?? [];
$billingAddr  = $cust['billing'] ?? [];
$orders       = $cust['orders'] ?? [];
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
<section class="account-dashboard-main" style="padding: 40px 0 80px; min-height: 70vh;">
  <div class="container">

    <!-- Flash Messages -->
    <?php if (!empty($success)): ?>
      <div class="account-flash-alert success" style="margin-bottom: 24px; padding: 14px 18px; border-radius: 8px; background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; font-size: 14px;">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div class="account-flash-alert error" style="margin-bottom: 24px; padding: 14px 18px; border-radius: 8px; background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca; font-size: 14px;">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <!-- Dashboard Top Welcome Hero -->
    <div class="dashboard-hero-card" style="background: linear-gradient(135deg, #111827 0%, #1f2937 100%); color: #ffffff; border-radius: 14px; padding: 30px; margin-bottom: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 20px;">
      <div class="dashboard-hero-info">
        <span style="display: inline-block; padding: 4px 10px; background-color: rgba(16, 185, 129, 0.2); color: #34d399; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 8px; border: 1px solid rgba(16, 185, 129, 0.3);">
          ✓ Verified 21+ Member
        </span>
        <h1 style="font-size: 26px; font-weight: 700; margin: 0 0 6px 0; color: #ffffff;">Hello, <?= htmlspecialchars($userName) ?>!</h1>
        <p style="margin: 0; color: #9ca3af; font-size: 14px;">
          Logged in as <strong style="color: #e5e7eb;"><?= htmlspecialchars($userEmail) ?></strong>
        </p>
      </div>
      <div class="dashboard-hero-actions" style="display: flex; gap: 12px; align-items: center;">
        <a href="<?= url('collection/all') ?>" class="btn-dash-shop" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; background-color: #10b981; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; transition: background 0.2s;">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
          Shop Vapes
        </a>
        <a href="<?= url('account/logout') ?>" class="btn-dash-logout" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 18px; background-color: rgba(255,255,255,0.1); color: #e5e7eb; text-decoration: none; border-radius: 8px; font-weight: 500; font-size: 14px; border: 1px solid rgba(255,255,255,0.15); transition: all 0.2s;">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
          Sign Out
        </a>
      </div>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 35px;">
      
      <div style="background: #ffffff; padding: 20px; border-radius: 10px; border: 1px solid #e5e7eb; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
        <span style="font-size: 12px; font-weight: 600; text-transform: uppercase; color: #6b7280; letter-spacing: 0.5px;">Total Orders</span>
        <div style="font-size: 24px; font-weight: 700; color: #111827; margin-top: 6px;"><?= count($orders) ?></div>
        <span style="font-size: 12px; color: #10b981; font-weight: 500; margin-top: 4px; display: block;">Lifetime Orders</span>
      </div>

      <div style="background: #ffffff; padding: 20px; border-radius: 10px; border: 1px solid #e5e7eb; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
        <span style="font-size: 12px; font-weight: 600; text-transform: uppercase; color: #6b7280; letter-spacing: 0.5px;">Total Spent</span>
        <div style="font-size: 24px; font-weight: 700; color: #111827; margin-top: 6px;"><?= htmlspecialchars($cust['total_spent'] ?? '$0.00') ?></div>
        <span style="font-size: 12px; color: #6b7280; margin-top: 4px; display: block;">Delivered Value</span>
      </div>

      <div style="background: #ffffff; padding: 20px; border-radius: 10px; border: 1px solid #e5e7eb; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
        <span style="font-size: 12px; font-weight: 600; text-transform: uppercase; color: #6b7280; letter-spacing: 0.5px;">Shipping Destination</span>
        <div style="font-size: 16px; font-weight: 600; color: #111827; margin-top: 8px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
          <?= !empty($shippingAddr['city']) ? htmlspecialchars($shippingAddr['city'] . ', ' . $shippingAddr['state']) : 'Not set yet' ?>
        </div>
        <span style="font-size: 12px; color: #6b7280; margin-top: 4px; display: block;">Default Address</span>
      </div>

      <div style="background: #ffffff; padding: 20px; border-radius: 10px; border: 1px solid #e5e7eb; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
        <span style="font-size: 12px; font-weight: 600; text-transform: uppercase; color: #6b7280; letter-spacing: 0.5px;">Account Status</span>
        <div style="font-size: 16px; font-weight: 700; color: #10b981; margin-top: 8px;">Active &amp; Compliant</div>
        <span style="font-size: 12px; color: #6b7280; margin-top: 4px; display: block;">Same-Day Shipping Ready</span>
      </div>

    </div>

    <!-- 2-Column Dashboard Tabs Layout -->
    <div style="display: grid; grid-template-columns: 240px 1fr; gap: 30px; align-items: flex-start;" class="dashboard-grid-layout">
      
      <!-- Left Column: Navigation Tabs -->
      <aside class="dashboard-nav-card" style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
        <ul style="list-style: none; margin: 0; padding: 0;" class="dashboard-nav-list" role="tablist">
          <li>
            <button type="button" class="dash-tab-btn active" data-tab="tabOrders" style="width: 100%; text-align: left; padding: 14px 20px; background: none; border: none; border-left: 3px solid #10b981; font-weight: 600; font-size: 14px; color: #111827; display: flex; align-items: center; gap: 10px; cursor: pointer;">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
              My Orders (<?= count($orders) ?>)
            </button>
          </li>
          <li>
            <button type="button" class="dash-tab-btn" data-tab="tabAddresses" style="width: 100%; text-align: left; padding: 14px 20px; background: none; border: none; border-left: 3px solid transparent; font-weight: 500; font-size: 14px; color: #4b5563; display: flex; align-items: center; gap: 10px; cursor: pointer;">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
              Addresses
            </button>
          </li>
          <li>
            <button type="button" class="dash-tab-btn" data-tab="tabProfile" style="width: 100%; text-align: left; padding: 14px 20px; background: none; border: none; border-left: 3px solid transparent; font-weight: 500; font-size: 14px; color: #4b5563; display: flex; align-items: center; gap: 10px; cursor: pointer;">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
              Account Details
            </button>
          </li>
          <li>
            <a href="<?= url('account/logout') ?>" style="width: 100%; text-align: left; padding: 14px 20px; border-top: 1px solid #f3f4f6; font-weight: 500; font-size: 14px; color: #dc2626; display: flex; align-items: center; gap: 10px; text-decoration: none;">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
              Log Out
            </a>
          </li>
        </ul>
      </aside>

      <!-- Right Column: Tab Panels Content -->
      <main class="dashboard-content-main" style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 28px; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
 
        <!-- TAB 1: ORDERS LIST -->
        <div class="dash-tab-pane active" id="tabOrders">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="font-size: 20px; font-weight: 700; margin: 0; color: #111827;">Your Order History</h2>
            <span style="font-size: 13px; color: #6b7280;"><?= count($orders) ?> order(s) placed</span>
          </div>

          <?php if (!empty($orders)): ?>
            <div style="overflow-x: auto;">
              <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                <thead>
                  <tr style="border-bottom: 2px solid #e5e7eb; color: #6b7280; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                    <th style="padding: 12px 14px;">Order #</th>
                    <th style="padding: 12px 14px;">Date</th>
                    <th style="padding: 12px 14px;">Items</th>
                    <th style="padding: 12px 14px;">Total</th>
                    <th style="padding: 12px 14px;">Status</th>
                    <th style="padding: 12px 14px; text-align: right;">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($orders as $ord): ?>
                    <tr style="border-bottom: 1px solid #f3f4f6;">
                      <td style="padding: 14px; font-weight: 600; color: #111827;">
                        #<?= htmlspecialchars($ord['id']) ?>
                      </td>
                      <td style="padding: 14px; color: #4b5563;">
                        <?= htmlspecialchars($ord['date']) ?>
                      </td>
                      <td style="padding: 14px; color: #4b5563;">
                        <?= htmlspecialchars($ord['items']) ?>
                      </td>
                      <td style="padding: 14px; font-weight: 600; color: #111827;">
                        <?= htmlspecialchars($ord['total']) ?>
                      </td>
                      <td style="padding: 14px;">
                        <span style="display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; 
                          background-color: <?= strtolower($ord['status']) === 'delivered' ? '#ecfdf5' : '#eff6ff' ?>;
                          color: <?= strtolower($ord['status']) === 'delivered' ? '#059669' : '#2563eb' ?>;">
                          <?= htmlspecialchars($ord['status']) ?>
                        </span>
                      </td>
                      <td style="padding: 14px; text-align: right;">
                        <a href="<?= url('track-order') ?>?order=<?= urlencode($ord['id']) ?>" style="display: inline-block; padding: 6px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; font-weight: 500; color: #374151; text-decoration: none;">
                          Track Order
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div style="text-align: center; padding: 50px 20px; background: #f9fafb; border-radius: 10px; border: 1px dashed #d1d5db;">
              <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="#9ca3af" stroke-width="1.5" style="margin-bottom: 12px;">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <path d="M16 10a4 4 0 0 1-8 0"></path>
              </svg>
              <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 6px;">No orders placed yet</h3>
              <p style="color: #6b7280; font-size: 14px; margin: 0 0 20px;">Ready to find your favorite vape device or e-liquid flavor?</p>
              <a href="<?= url('collection/all') ?>" style="display: inline-block; padding: 10px 22px; background-color: #10b981; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px;">
                Explore Catalog
              </a>
            </div>
          <?php endif; ?>
        </div>

        <!-- TAB 2: ADDRESSES -->
        <div class="dash-tab-pane" id="tabAddresses" style="display: none;">
          <h2 style="font-size: 20px; font-weight: 700; margin: 0 0 8px; color: #111827;">Saved Addresses</h2>
          <p style="color: #6b7280; font-size: 14px; margin: 0 0 24px;">The following addresses will be used on the checkout page by default.</p>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
            
            <!-- Shipping Address Card -->
            <?php if (!empty($shippingAddr['street'])): ?>
            <div style="border: 1px solid #e5e7eb; border-radius: 10px; padding: 22px; background: #fafafa; display: flex; flex-direction: column; justify-content: space-between;">
              <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                  <h3 style="font-size: 16px; font-weight: 600; margin: 0; color: #111827;">Shipping Address</h3>
                  <span style="font-size: 11px; font-weight: 600; background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 12px;">Default</span>
                </div>
                <div style="font-size: 14px; color: #374151; line-height: 1.6;">
                  <strong><?= htmlspecialchars($shippingAddr['name'] ?? $userName) ?></strong><br>
                  <?= htmlspecialchars($shippingAddr['street']) ?><br>
                  <?= !empty($shippingAddr['apartment']) ? htmlspecialchars($shippingAddr['apartment']) . '<br>' : '' ?>
                  <?= htmlspecialchars(($shippingAddr['city'] ?? '') . ', ' . ($shippingAddr['state'] ?? '') . ' ' . ($shippingAddr['zip'] ?? '')) ?><br>
                  <?= htmlspecialchars($shippingAddr['country'] ?? 'United States') ?><br>
                  <?= !empty($shippingAddr['phone']) ? 'Phone: ' . htmlspecialchars($shippingAddr['phone']) : '' ?>
                </div>
              </div>
              <button type="button" onclick="openAddressModal('shipping', true)" style="margin-top: 16px; width: fit-content; padding: 7px 16px; background: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer;">
                Edit Address
              </button>
            </div>
            <?php else: ?>
            <div style="border: 2px dashed #e5e7eb; border-radius: 10px; padding: 28px 20px; background: #fafafa; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center;">
              <div style="width: 42px; height: 42px; border-radius: 50%; background: #f3f4f6; display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#6b7280" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
              </div>
              <h3 style="font-size: 16px; font-weight: 600; margin: 0 0 4px; color: #111827;">Shipping Address</h3>
              <p style="color: #6b7280; font-size: 13px; margin: 0 0 16px;">You have not saved a shipping address yet.</p>
              <button type="button" onclick="openAddressModal('shipping', false)" style="padding: 8px 18px; background: #10b981; color: #ffffff; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer;">
                + Add Shipping Address
              </button>
            </div>
            <?php endif; ?>

            <!-- Billing Address Card -->
            <?php if (!empty($billingAddr['street'])): ?>
            <div style="border: 1px solid #e5e7eb; border-radius: 10px; padding: 22px; background: #fafafa; display: flex; flex-direction: column; justify-content: space-between;">
              <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                  <h3 style="font-size: 16px; font-weight: 600; margin: 0; color: #111827;">Billing Address</h3>
                </div>
                <div style="font-size: 14px; color: #374151; line-height: 1.6;">
                  <strong><?= htmlspecialchars($billingAddr['name'] ?? $userName) ?></strong><br>
                  <?= htmlspecialchars($billingAddr['street']) ?><br>
                  <?= !empty($billingAddr['apartment']) ? htmlspecialchars($billingAddr['apartment']) . '<br>' : '' ?>
                  <?= htmlspecialchars(($billingAddr['city'] ?? '') . ', ' . ($billingAddr['state'] ?? '') . ' ' . ($billingAddr['zip'] ?? '')) ?><br>
                  <?= htmlspecialchars($billingAddr['country'] ?? 'United States') ?>
                </div>
              </div>
              <button type="button" onclick="openAddressModal('billing', true)" style="margin-top: 16px; width: fit-content; padding: 7px 16px; background: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer;">
                Edit Address
              </button>
            </div>
            <?php else: ?>
            <div style="border: 2px dashed #e5e7eb; border-radius: 10px; padding: 28px 20px; background: #fafafa; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center;">
              <div style="width: 42px; height: 42px; border-radius: 50%; background: #f3f4f6; display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#6b7280" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
              </div>
              <h3 style="font-size: 16px; font-weight: 600; margin: 0 0 4px; color: #111827;">Billing Address</h3>
              <p style="color: #6b7280; font-size: 13px; margin: 0 0 16px;">You have not saved a billing address yet.</p>
              <button type="button" onclick="openAddressModal('billing', false)" style="padding: 8px 18px; background: #111827; color: #ffffff; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer;">
                + Add Billing Address
              </button>
            </div>
            <?php endif; ?>

          </div>

          <!-- Edit Address Form Modal Container -->
          <div id="addressEditModal" style="display: none; margin-top: 30px; padding: 24px; border: 1px solid #10b981; border-radius: 10px; background: #f0fdf4;">
            <h3 id="addressModalTitle" style="font-size: 17px; font-weight: 600; margin: 0 0 16px; color: #065f46;">Edit Address</h3>
            <form id="addressForm" method="POST" action="<?= url('account/save-address') ?>">
              <?= csrf_field() ?>
              <input type="hidden" name="address_type" id="addrTypeInput" value="shipping">

              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                <div>
                  <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">First Name</label>
                  <input type="text" name="first_name" id="addrFirstName" class="account-input" value="<?= htmlspecialchars($cust['first_name'] ?? '') ?>" required>
                </div>
                <div>
                  <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">Last Name</label>
                  <input type="text" name="last_name" id="addrLastName" class="account-input" value="<?= htmlspecialchars($cust['last_name'] ?? '') ?>" required>
                </div>
              </div>

              <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">Street Address</label>
                <input type="text" name="street" id="addrStreet" class="account-input" placeholder="House number and street name" value="" required>
              </div>

              <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">Apartment, suite, unit (optional)</label>
                <input type="text" name="apartment" id="addrSuite" class="account-input" placeholder="Apt 4B (optional)" value="">
              </div>

              <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                <div>
                  <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">City</label>
                  <input type="text" name="city" id="addrCity" class="account-input" placeholder="City" value="" required>
                </div>
                <div>
                  <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">State</label>
                  <input type="text" name="state" id="addrState" class="account-input" placeholder="TX" value="" required>
                </div>
                <div>
                  <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">ZIP Code</label>
                  <input type="text" name="zip" id="addrZip" class="account-input" placeholder="78701" value="" required>
                </div>
              </div>

              <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn-account-submit" style="width: auto; padding: 10px 24px;">Save Address</button>
                <button type="button" onclick="document.getElementById('addressEditModal').style.display='none'" style="padding: 10px 18px; background: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; cursor: pointer;">Cancel</button>
              </div>
            </form>
          </div>
        </div>

        <!-- TAB 3: ACCOUNT DETAILS -->
        <div class="dash-tab-pane" id="tabProfile" style="display: none;">
          <h2 style="font-size: 20px; font-weight: 700; margin: 0 0 8px; color: #111827;">Account Details</h2>
          <p style="color: #6b7280; font-size: 14px; margin: 0 0 24px;">Update your personal profile and security password.</p>

          <form id="profileForm" method="POST" action="<?= url('account/update-profile') ?>">
            <?= csrf_field() ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
              <div>
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">First Name <span class="required-star">*</span></label>
                <input type="text" name="first_name" class="account-input" value="<?= htmlspecialchars($cust['first_name'] ?? '') ?>" required>
              </div>
              <div>
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">Last Name <span class="required-star">*</span></label>
                <input type="text" name="last_name" class="account-input" value="<?= htmlspecialchars($cust['last_name'] ?? '') ?>" required>
              </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
              <div>
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">Email Address</label>
                <input type="email" class="account-input" value="<?= htmlspecialchars($userEmail) ?>" readonly style="background-color: #f9fafb; cursor: not-allowed;">
                <span style="font-size: 12px; color: #9ca3af; margin-top: 4px; display: block;">Email is tied to your order records.</span>
              </div>
              <div>
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">Phone Number</label>
                <input type="tel" name="phone" class="account-input" value="<?= htmlspecialchars($userPhone) ?>" placeholder="+1 (555) 000-0000">
              </div>
            </div>

            <!-- Password Change Fieldset -->
            <div style="border-top: 1px solid #e5e7eb; padding-top: 24px; margin-top: 24px;">
              <h3 style="font-size: 16px; font-weight: 600; margin: 0 0 14px; color: #111827;">Password Change</h3>
              
              <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">Current Password (leave blank to keep unchanged)</label>
                <input type="password" name="current_password" class="account-input" placeholder="••••••••">
              </div>

              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 14px;">
                <div>
                  <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">New Password</label>
                  <input type="password" name="new_password" class="account-input" placeholder="New password">
                </div>
                <div>
                  <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px;">Confirm New Password</label>
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
