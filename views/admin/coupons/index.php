<?php
$page_title = 'Coupons & Discounts';
$current_admin_page = 'coupons';
$page_script = 'coupons.js';
include dirname(__DIR__) . '/includes/header.php';
?>

<!-- Title & Action Bar -->
<div class="page-title-bar">
  <div class="page-heading-group">
    <h1>Coupons &amp; Discounts</h1>
    <p class="page-subtitle">Create promotional codes, manage percentage discounts, and configure checkout incentives.</p>
  </div>
  <div class="page-actions-group">
    <button type="button" class="btn-admin btn-admin-primary" id="btnOpenAddCouponModal">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
      </svg>
      <span>Create New Coupon</span>
    </button>
  </div>
</div>

<!-- Alert Banner Container for Live Toast/AJAX messages -->
<div id="couponPageAlert" style="display: none; padding: 12px 18px; border-radius: 8px; font-size: 14px; font-weight: 500; margin-bottom: 20px;"></div>

<!-- Top Metrics / KPI Cards -->
<div class="admin-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px; margin-bottom: 24px;">
  
  <div class="admin-card" style="padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 16px;">
    <div style="width: 48px; height: 48px; border-radius: 10px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center;">
      <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
        <line x1="7" y1="7" x2="7.01" y2="7"></line>
      </svg>
    </div>
    <div>
      <div style="font-size: 0.82rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Total Coupons</div>
      <div style="font-size: 1.6rem; font-weight: 800; color: #0f172a;"><?= number_format($metrics['total'] ?? 0) ?></div>
    </div>
  </div>

  <div class="admin-card" style="padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 16px;">
    <div style="width: 48px; height: 48px; border-radius: 10px; background: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center;">
      <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"></circle>
        <polyline points="12 6 12 12 14 14"></polyline>
      </svg>
    </div>
    <div>
      <div style="font-size: 0.82rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Active Coupons</div>
      <div style="font-size: 1.6rem; font-weight: 800; color: #10b981;"><?= number_format($metrics['active'] ?? 0) ?></div>
    </div>
  </div>

  <div class="admin-card" style="padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 16px;">
    <div style="width: 48px; height: 48px; border-radius: 10px; background: #fffbeb; color: #f59e0b; display: flex; align-items: center; justify-content: center;">
      <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
        <circle cx="9" cy="7" r="4"></circle>
        <polyline points="16 11 18 13 22 9"></polyline>
      </svg>
    </div>
    <div>
      <div style="font-size: 0.82rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Redemptions</div>
      <div style="font-size: 1.6rem; font-weight: 800; color: #0f172a;"><?= number_format($metrics['total_uses'] ?? 0) ?> <span style="font-size: 0.8rem; font-weight: 500; color: #64748b;">uses</span></div>
    </div>
  </div>

  <div class="admin-card" style="padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 16px;">
    <div style="width: 48px; height: 48px; border-radius: 10px; background: #faf5ff; color: #9333ea; display: flex; align-items: center; justify-content: center;">
      <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
      </svg>
    </div>
    <div>
      <div style="font-size: 0.82rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Top Performer</div>
      <div style="font-size: 1.3rem; font-weight: 800; color: #0f172a; font-family: monospace;"><?= htmlspecialchars($metrics['top_code'] ?? 'None') ?></div>
    </div>
  </div>

</div>

<!-- Filter & Search Toolbar -->
<div class="admin-card mb-lg">
  <div class="admin-card-body" style="padding: 16px 20px;">
    <form method="GET" action="<?= url('admin/coupons') ?>" class="admin-filter-bar" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
      
      <!-- Search Input -->
      <div style="flex: 1; min-width: 220px; position: relative;">
        <input type="text" name="search" class="admin-form-input" placeholder="Search by coupon code (e.g. VAPE10)..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
      </div>

      <!-- Type Filter -->
      <div style="min-width: 170px;">
        <select name="type" class="admin-form-select" onchange="this.form.submit()">
          <option value="">All Discount Types</option>
          <option value="percentage" <?= ($filters['type'] ?? '') === 'percentage' ? 'selected' : '' ?>>Percentage (%)</option>
          <option value="fixed_amount" <?= ($filters['type'] ?? '') === 'fixed_amount' ? 'selected' : '' ?>>Fixed Amount ($)</option>
          <option value="free_shipping" <?= ($filters['type'] ?? '') === 'free_shipping' ? 'selected' : '' ?>>Free Shipping</option>
        </select>
      </div>

      <!-- Status Filter -->
      <div style="min-width: 150px;">
        <select name="status" class="admin-form-select" onchange="this.form.submit()">
          <option value="">All Statuses</option>
          <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
          <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
          <option value="expired" <?= ($filters['status'] ?? '') === 'expired' ? 'selected' : '' ?>>Expired / Maxed</option>
        </select>
      </div>

      <button type="submit" class="btn-admin btn-admin-secondary">
        Filter
      </button>

      <?php if (!empty($filters['search']) || !empty($filters['type']) || !empty($filters['status'])): ?>
        <a href="<?= url('admin/coupons') ?>" class="btn-admin btn-admin-text" style="color: #ef4444; text-decoration: none;">Reset</a>
      <?php endif; ?>

    </form>
  </div>
</div>

<!-- Coupons Table Card -->
<div class="admin-card">
  <div class="admin-card-header d-flex justify-between align-center">
    <h2 class="admin-card-title">All Promotional Coupons (<?= count($coupons) ?>)</h2>
  </div>
  
  <div class="admin-card-body p-0">
    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Coupon Code</th>
            <th>Discount Value</th>
            <th>Min. Spend</th>
            <th>Usage &amp; Limits</th>
            <th>Validity Period</th>
            <th>Status</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($coupons)): ?>
            <?php foreach ($coupons as $c): ?>
              <tr id="couponRow-<?= $c['id'] ?>">
                
                <!-- Code with 1-click copy -->
                <td>
                  <div class="d-flex align-center gap-xs">
                    <span style="font-family: monospace; font-size: 14px; font-weight: 700; color: #0f172a; background: #f1f5f9; padding: 4px 8px; border-radius: 6px; letter-spacing: 0.5px; border: 1px dashed #cbd5e1;">
                      <?= htmlspecialchars($c['code']) ?>
                    </span>
                    <button type="button" class="btn-icon-only btn-copy-code" data-code="<?= htmlspecialchars($c['code']) ?>" title="Copy Code" style="background: none; border: none; cursor: pointer; color: #64748b; padding: 4px;">
                      <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                      </svg>
                    </button>
                  </div>
                </td>

                <!-- Discount Value & Type -->
                <td>
                  <strong style="color: #0f172a; font-size: 14px;"><?= htmlspecialchars($c['discount_label']) ?></strong>
                  <div style="font-size: 12px; color: #64748b;"><?= htmlspecialchars($c['type_label']) ?></div>
                </td>

                <!-- Min Spend Requirement -->
                <td>
                  <span style="color: #334155; font-size: 13px; font-weight: 500;">
                    <?= htmlspecialchars($c['min_order_label']) ?>
                  </span>
                </td>

                <!-- Usage Progress -->
                <td>
                  <div style="font-size: 13px; font-weight: 600; color: #0f172a; margin-bottom: 4px;">
                    <?= htmlspecialchars($c['usage_label']) ?>
                  </div>
                  <?php if (!empty($c['max_uses'])): ?>
                    <div style="background: #e2e8f0; border-radius: 4px; height: 6px; width: 110px; overflow: hidden;">
                      <div style="background: <?= $c['usage_percent'] >= 90 ? '#ef4444' : '#3b82f6' ?>; width: <?= $c['usage_percent'] ?>%; height: 100%;"></div>
                    </div>
                  <?php endif; ?>
                </td>

                <!-- Validity Dates -->
                <td>
                  <span style="font-size: 12.5px; color: #475569;">
                    <?= htmlspecialchars($c['validity_label']) ?>
                  </span>
                </td>

                <!-- Status Badge -->
                <td>
                  <span class="badge-status <?= $c['badge_class'] ?>" id="badgeStatus-<?= $c['id'] ?>">
                    <?= htmlspecialchars($c['status_label']) ?>
                  </span>
                </td>

                <!-- Actions -->
                <td class="text-right">
                  <div class="d-flex align-center justify-end gap-xs">
                    
                    <!-- Toggle Switch Button -->
                    <button type="button" class="btn-icon-only btn-toggle-coupon" data-id="<?= $c['id'] ?>" title="Toggle Active / Inactive" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px; cursor: pointer; color: <?= $c['is_active'] ? '#10b981' : '#94a3b8' ?>;">
                      <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M12 2v10"></path>
                      </svg>
                    </button>

                    <!-- Edit Button -->
                    <button type="button" class="btn-icon-only btn-edit-coupon" 
                            data-id="<?= $c['id'] ?>"
                            data-code="<?= htmlspecialchars($c['code']) ?>"
                            data-type="<?= htmlspecialchars($c['type']) ?>"
                            data-value="<?= htmlspecialchars($c['value']) ?>"
                            data-min="<?= htmlspecialchars($c['min_order_amount']) ?>"
                            data-max="<?= htmlspecialchars($c['max_uses'] ?? '') ?>"
                            data-start="<?= !empty($c['start_date']) ? date('Y-m-d\TH:i', strtotime($c['start_date'])) : '' ?>"
                            data-end="<?= !empty($c['end_date']) ? date('Y-m-d\TH:i', strtotime($c['end_date'])) : '' ?>"
                            data-active="<?= $c['is_active'] ?>"
                            title="Edit Coupon" 
                            style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px; cursor: pointer; color: #0284c7;">
                      <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                      </svg>
                    </button>

                    <!-- Delete Button -->
                    <button type="button" class="btn-icon-only btn-delete-coupon" data-id="<?= $c['id'] ?>" data-code="<?= htmlspecialchars($c['code']) ?>" title="Delete Coupon" style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: 6px; padding: 6px; cursor: pointer; color: #ef4444;">
                      <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                      </svg>
                    </button>

                  </div>
                </td>

              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center" style="padding: 48px; color: #64748b;">
                <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="margin-bottom: 12px;">
                  <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                  <line x1="7" y1="7" x2="7.01" y2="7"></line>
                </svg>
                <div style="font-size: 15px; font-weight: 600; color: #334155;">No coupons found</div>
                <div style="font-size: 13px; color: #94a3b8; margin-top: 4px;">Click "+ Create New Coupon" above to launch a discount code.</div>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ==========================================
     CREATE / EDIT COUPON MODAL
     ========================================== -->
<div id="couponModalOverlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.55); z-index: 1050; align-items: center; justify-content: center; padding: 20px;">
  
  <div style="background: #ffffff; border-radius: 12px; width: 100%; max-width: 540px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden; animation: modalFadeIn 0.2s ease;">
    
    <div style="padding: 18px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
      <h3 id="modalCouponTitle" style="margin: 0; font-size: 18px; font-weight: 700; color: #0f172a;">Create New Coupon</h3>
      <button type="button" id="btnCloseCouponModal" style="background: none; border: none; cursor: pointer; color: #64748b; font-size: 20px; line-height: 1;">&times;</button>
    </div>

    <form id="couponForm" style="padding: 24px;">
      <input type="hidden" id="couponId" name="id" value="">
      
      <div id="modalAlertBox" style="display: none; padding: 10px 14px; border-radius: 6px; font-size: 13px; margin-bottom: 16px;"></div>

      <!-- Coupon Code & Generate Button -->
      <div class="admin-form-group">
        <label for="formCouponCode" class="admin-form-label">Coupon Code *</label>
        <div style="display: flex; gap: 8px;">
          <input type="text" id="formCouponCode" name="code" class="admin-form-input" placeholder="e.g. SUMMER20" required style="text-transform: uppercase; font-family: monospace; font-weight: 700;">
          <button type="button" id="btnGenerateCode" class="btn-admin btn-admin-secondary" style="white-space: nowrap; font-size: 12px;">Generate</button>
        </div>
      </div>

      <!-- Discount Type & Value Row -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
        <div class="admin-form-group">
          <label for="formCouponType" class="admin-form-label">Discount Type *</label>
          <select id="formCouponType" name="type" class="admin-form-select" required>
            <option value="percentage">Percentage (%)</option>
            <option value="fixed_amount">Fixed Amount ($)</option>
            <option value="free_shipping">Free Shipping</option>
          </select>
        </div>
        <div class="admin-form-group">
          <label for="formCouponValue" class="admin-form-label" id="lblCouponValue">Discount Value (%) *</label>
          <input type="number" step="0.01" min="0.01" id="formCouponValue" name="value" class="admin-form-input" placeholder="e.g. 15" required>
        </div>
      </div>

      <!-- Minimum Order & Maximum Uses -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
        <div class="admin-form-group">
          <label for="formCouponMinOrder" class="admin-form-label">Min. Order Subtotal ($)</label>
          <input type="number" step="0.01" min="0" id="formCouponMinOrder" name="min_order_amount" class="admin-form-input" placeholder="0.00 for no min">
        </div>
        <div class="admin-form-group">
          <label for="formCouponMaxUses" class="admin-form-label">Max Uses Limit</label>
          <input type="number" min="1" id="formCouponMaxUses" name="max_uses" class="admin-form-input" placeholder="Blank for unlimited">
        </div>
      </div>

      <!-- Start Date & Expiration Date -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
        <div class="admin-form-group">
          <label for="formCouponStart" class="admin-form-label">Start Date (Optional)</label>
          <input type="datetime-local" id="formCouponStart" name="start_date" class="admin-form-input">
        </div>
        <div class="admin-form-group">
          <label for="formCouponEnd" class="admin-form-label">Expiry Date (Optional)</label>
          <input type="datetime-local" id="formCouponEnd" name="end_date" class="admin-form-input">
        </div>
      </div>

      <!-- Active Checkbox -->
      <div style="margin: 10px 0 20px; display: flex; align-items: center; gap: 8px;">
        <input type="checkbox" id="formCouponActive" name="is_active" value="1" checked style="width: 16px; height: 16px; accent-color: #f25c27;">
        <label for="formCouponActive" style="font-size: 13.5px; font-weight: 500; color: #334155; cursor: pointer;">Enable this coupon immediately</label>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #e2e8f0; padding-top: 16px;">
        <button type="button" id="btnCancelCouponModal" class="btn-admin btn-admin-secondary">Cancel</button>
        <button type="submit" id="btnSubmitCouponForm" class="btn-admin btn-admin-primary">
          <span id="btnSubmitCouponText">Save Coupon</span>
        </button>
      </div>

    </form>

  </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

