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
    <div class="legal-page-container track-order-container">
      
      <header class="legal-page-header track-order-header">
        <h1 class="legal-page-title">Track Your Shipment</h1>
        <p class="legal-page-meta">Enter your order details or tracking number below to view shipment status.</p>
      </header>

      <div class="track-order-card">
        
        <?php if (!empty($tracking_result)): ?>
          <div class="track-order-result-box">
            <h3 class="track-order-result-title">Order #<?= htmlspecialchars($tracking_result['order_number']) ?></h3>
            <p class="track-order-result-text"><strong>Status:</strong> <?= ucfirst(htmlspecialchars($tracking_result['shipping_status'])) ?></p>
            <p class="track-order-result-text"><strong>Carrier:</strong> <?= htmlspecialchars($tracking_result['shipping_carrier']) ?></p>
            <p class="track-order-result-text"><strong>Tracking #:</strong> <?= htmlspecialchars($tracking_result['tracking_number'] ?: 'Processing') ?></p>
          </div>
        <?php elseif (isset($_POST['order_number'])): ?>
          <div class="legal-notice-box danger">
            No matching order found for the provided information. Please check your order confirmation email.
          </div>
        <?php endif; ?>

        <form action="<?= url('page/track-order') ?>" method="POST">
          <?= csrf_field() ?>
          <div class="track-order-form-group">
            <label class="track-order-label">Order Number *</label>
            <input type="text" name="order_number" class="track-order-input" required placeholder="e.g. 104820">
          </div>

          <div class="track-order-form-group mb-lg">
            <label class="track-order-label">Billing / Shipping Email *</label>
            <input type="email" name="customer_email" class="track-order-input" required placeholder="Email used during checkout">
          </div>

          <button type="submit" class="btn-track-submit">
            Track Order Status
          </button>
        </form>

        <div class="track-order-footer">
          <p>Have an account? <a href="<?= url('my-account?tab=orders') ?>">View your order history</a></p>
        </div>

      </div>

    </div>
  </div>
</section>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

