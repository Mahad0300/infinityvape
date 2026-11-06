<?php
$page_title = 'Store & Checkout Settings';
$current_admin_page = 'settings';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="page-title-bar">
  <div class="page-heading-group">
    <h1>Store &amp; Checkout Settings</h1>
    <p class="page-subtitle">Configure payment mode, sandbox checkout, shipping thresholds, and store policies.</p>
  </div>
</div>

<?php if (function_exists('get_flash') && $msg = get_flash('success')): ?>
  <div class="admin-alert admin-alert-success" style="margin-bottom: 20px; padding: 14px 18px; background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; color: #065f46; display: flex; align-items: center; gap: 10px;">
    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <polyline points="20 6 9 17 4 12"></polyline>
    </svg>
    <span><?= htmlspecialchars($msg) ?></span>
  </div>
<?php endif; ?>

<form action="<?= url('admin/settings/save') ?>" method="POST" class="admin-form-wrap">

  <!-- Card 1: Payment & Sandbox Mode Settings -->
  <div class="admin-card" style="margin-bottom: 24px;">
    <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center;">
      <h2 style="font-size: 1.15rem; font-weight: 700; color: #1e293b; margin: 0;">Payment &amp; Sandbox Checkout Mode</h2>
      <span class="badge" style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700;">PAYMENT CONFIG</span>
    </div>
    <div class="admin-card-body" style="padding: 24px;">
      
      <!-- Test Checkout Toggle Banner -->
      <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px; margin-bottom: 20px; display: flex; align-items: flex-start; gap: 16px;">
        <div style="margin-top: 4px;">
          <input type="checkbox" id="test_checkout_enabled" name="test_checkout_enabled" value="1" <?= (!empty($settings['test_checkout_enabled']) && $settings['test_checkout_enabled'] == '1') ? 'checked' : '' ?> style="width: 22px; height: 22px; cursor: pointer; accent-color: #f25c27;">
        </div>
        <div>
          <label for="test_checkout_enabled" style="font-size: 1rem; font-weight: 700; color: #1e293b; cursor: pointer; display: block; margin-bottom: 4px;">
            Enable Test Checkout / Sandbox Mode (Without Live Card Charge)
          </label>
          <p style="font-size: 0.85rem; color: #64748b; margin: 0; line-height: 1.5;">
            When enabled, users can test the complete, dynamic checkout process without connecting an external card gateway. Real orders will still be generated, saved in the database, stocks decremented, and logged in the Admin Orders panel.
          </p>
        </div>
      </div>

      <div class="admin-form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div class="admin-form-group">
          <label class="admin-form-label" for="tax_rate_percent">Estimated Sales Tax Rate (%)</label>
          <input type="number" step="0.01" min="0" max="100" class="admin-form-input" id="tax_rate_percent" name="tax_rate_percent" value="<?= htmlspecialchars($settings['tax_rate_percent'] ?? '4.50') ?>">
          <small style="color: #64748b; font-size: 0.775rem;">Default tax percentage calculated during checkout (e.g. 4.5%).</small>
        </div>
      </div>

    </div>
  </div>

  <!-- Card 2: Shipping Rates & Thresholds -->
  <div class="admin-card" style="margin-bottom: 24px;">
    <div class="admin-card-header">
      <h2 style="font-size: 1.15rem; font-weight: 700; color: #1e293b; margin: 0;">Shipping Rates &amp; Free Shipping</h2>
    </div>
    <div class="admin-card-body" style="padding: 24px;">
      
      <div class="admin-form-row" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
        <div class="admin-form-group">
          <label class="admin-form-label" for="standard_shipping_rate">Standard Shipping Rate ($)</label>
          <input type="number" step="0.01" min="0" class="admin-form-input" id="standard_shipping_rate" name="standard_shipping_rate" value="<?= htmlspecialchars($settings['standard_shipping_rate'] ?? '5.99') ?>">
          <small style="color: #64748b; font-size: 0.775rem;">Ground shipping fee for orders below threshold.</small>
        </div>

        <div class="admin-form-group">
          <label class="admin-form-label" for="express_shipping_rate">Express Priority Rate ($)</label>
          <input type="number" step="0.01" min="0" class="admin-form-input" id="express_shipping_rate" name="express_shipping_rate" value="<?= htmlspecialchars($settings['express_shipping_rate'] ?? '14.99') ?>">
          <small style="color: #64748b; font-size: 0.775rem;">Expedited priority dispatch shipping fee.</small>
        </div>

        <div class="admin-form-group">
          <label class="admin-form-label" for="free_shipping_threshold">Free Shipping Threshold ($)</label>
          <input type="number" step="0.01" min="0" class="admin-form-input" id="free_shipping_threshold" name="free_shipping_threshold" value="<?= htmlspecialchars($settings['free_shipping_threshold'] ?? '150.00') ?>">
          <small style="color: #64748b; font-size: 0.775rem;">Orders with subtotal above this get free shipping.</small>
        </div>
      </div>

    </div>
  </div>

  <!-- Card 3: Store Information & Compliance -->
  <div class="admin-card" style="margin-bottom: 24px;">
    <div class="admin-card-header">
      <h2 style="font-size: 1.15rem; font-weight: 700; color: #1e293b; margin: 0;">Store Information &amp; Legal Age</h2>
    </div>
    <div class="admin-card-body" style="padding: 24px;">
      
      <div class="admin-form-row" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
        <div class="admin-form-group">
          <label class="admin-form-label" for="site_name">Store Name</label>
          <input type="text" class="admin-form-input" id="site_name" name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? 'Infinity Vape Shop') ?>">
        </div>

        <div class="admin-form-group">
          <label class="admin-form-label" for="site_email">Support Email</label>
          <input type="email" class="admin-form-input" id="site_email" name="site_email" value="<?= htmlspecialchars($settings['site_email'] ?? 'support@infinityvape.com') ?>">
        </div>

        <div class="admin-form-group">
          <label class="admin-form-label" for="min_legal_age">Minimum Legal Age Requirement</label>
          <input type="number" min="18" max="25" class="admin-form-input" id="min_legal_age" name="min_legal_age" value="<?= htmlspecialchars($settings['min_legal_age'] ?? '21') ?>">
        </div>
      </div>

    </div>
  </div>

  <!-- Save Action Button -->
  <div style="display: flex; justify-content: flex-end; gap: 12px; margin-bottom: 40px;">
    <button type="submit" class="admin-btn-primary" style="background-color: #f25c27; color: #ffffff; border: none; padding: 12px 28px; border-radius: 8px; font-weight: 700; font-size: 0.95rem; cursor: pointer; display: flex; align-items: center; gap: 8px;">
      <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
        <polyline points="17 21 17 13 7 13 7 21"></polyline>
        <polyline points="7 3 7 8 15 8"></polyline>
      </svg>
      Save Settings
    </button>
  </div>

</form>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>

