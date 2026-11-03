<?php
$page_title = 'Track Your Order | Infinity Vape Shop';
$active_page = 'track-order';

include dirname(__DIR__) . '/includes/header.php';
?>

<!-- ==========================================
     1. BREADCRUMBS
     ========================================== -->
<nav class="category-breadcrumb-nav" aria-label="Breadcrumb">
  <div class="container">
    <ol class="breadcrumb-list">
      <li class="breadcrumb-item">
        <a href="<?= url() ?>">Home</a>
      </li>
      <li class="breadcrumb-separator" aria-hidden="true">
        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </li>
      <li class="breadcrumb-item active" aria-current="page">
        Track Your Order
      </li>
    </ol>
  </div>
</nav>

<!-- ==========================================
     2. TRACK ORDER SECTION
     ========================================== -->
<section class="legal-page-section">
  <div class="container">
    <div class="legal-page-container" style="max-width: 680px; margin: 0 auto;">
      
      <header class="legal-page-header" style="text-align: center;">
        <h1 class="legal-page-title">Track Your Shipment</h1>
        <p class="legal-page-meta">Enter your order details or tracking number below to view shipment status.</p>
      </header>

      <div class="track-order-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 32px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); margin-top: 24px;">
        
        <?php if (!empty($tracking_result)): ?>
          <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
            <h3 style="margin: 0 0 10px; font-size: 16px; color: #166534; font-weight: 700;">Order #<?= htmlspecialchars($tracking_result['order_number']) ?></h3>
            <p style="margin: 0 0 6px; font-size: 14px; color: #15803d;"><strong>Status:</strong> <?= ucfirst(htmlspecialchars($tracking_result['shipping_status'])) ?></p>
            <p style="margin: 0 0 6px; font-size: 14px; color: #15803d;"><strong>Carrier:</strong> <?= htmlspecialchars($tracking_result['shipping_carrier']) ?></p>
            <p style="margin: 0; font-size: 14px; color: #15803d;"><strong>Tracking #:</strong> <?= htmlspecialchars($tracking_result['tracking_number'] ?: 'Processing') ?></p>
          </div>
        <?php elseif (isset($_POST['order_number'])): ?>
          <div class="legal-notice-box" style="border-left-color: #ef4444; background: #fef2f2; color: #991b1b; margin-bottom: 24px;">
            No matching order found for the provided information. Please check your order confirmation email.
          </div>
        <?php endif; ?>

        <form action="<?= url('page/track-order') ?>" method="POST">
          <?= csrf_field() ?>
          <div style="margin-bottom: 18px;">
            <label style="display: block; font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 8px;">Order Number *</label>
            <input type="text" name="order_number" required placeholder="e.g. 104820" style="width: 100%; padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; font-family: inherit; box-sizing: border-box;">
          </div>

          <div style="margin-bottom: 24px;">
            <label style="display: block; font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 8px;">Billing / Shipping Email *</label>
            <input type="email" name="customer_email" required placeholder="Email used during checkout" style="width: 100%; padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; font-family: inherit; box-sizing: border-box;">
          </div>

          <button type="submit" class="btn-primary" style="width: 100%; padding: 14px; font-size: 15px; font-weight: 700; border-radius: 8px; cursor: pointer; border: none; background: #0f172a; color: #ffffff;">
            Track Order Status
          </button>
        </form>

        <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #f1f5f9; text-align: center;">
          <p style="font-size: 13px; color: #64748b; margin: 0;">Have an account? <a href="<?= url('my-account?tab=orders') ?>" style="color: #5c56e8; font-weight: 600; text-decoration: none;">View your order history</a></p>
        </div>

      </div>

    </div>
  </div>
</section>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

