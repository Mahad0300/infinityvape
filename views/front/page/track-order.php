<?php
$page_title = 'Track Your Order | Infinity Vape Shop';
$active_page = 'track-order';

$prefillOrder = trim($_GET['order'] ?? ($_POST['order_number'] ?? ''));
$loggedCustomerEmail = class_exists('\\App\\Core\\CustomerAuth') && \App\Core\CustomerAuth::check() ? (\App\Core\CustomerAuth::user()['email'] ?? '') : '';
$prefillEmail = trim($_POST['customer_email'] ?? $loggedCustomerEmail);

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
        
        <?php if (!empty($tracking_result)): 
          $sStatus = strtolower($tracking_result['shipping_status'] ?? 'pending');
          $steps = [
            'pending'          => 1,
            'confirmed'        => 2,
            'processing'       => 2,
            'shipped'          => 3,
            'in_transit'       => 3,
            'out_for_delivery' => 4,
            'delivered'        => 5,
            'cancelled'        => 0
          ];
          $currentStep = $steps[$sStatus] ?? 1;
          $isCancelled = ($sStatus === 'cancelled');
          
          $carrier = $tracking_result['shipping_carrier'] ?: ($tracking_result['delivery_method'] ?: 'Standard Shipping');
          $trackingNum = $tracking_result['tracking_number'] ?? '';
          $trackUrl = 'https://www.google.com/search?q=' . urlencode($trackingNum);
          if (stripos($carrier, 'usps') !== false) {
            $trackUrl = 'https://tools.usps.com/go/TrackConfirmAction?tLabels=' . urlencode($trackingNum);
          } elseif (stripos($carrier, 'ups') !== false) {
            $trackUrl = 'https://www.ups.com/track?tracknum=' . urlencode($trackingNum);
          } elseif (stripos($carrier, 'fedex') !== false) {
            $trackUrl = 'https://www.fedex.com/fedextrack/?trknbr=' . urlencode($trackingNum);
          }
        ?>
          <div class="track-order-result-box" style="background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:24px; box-shadow:0 2px 10px rgba(0,0,0,0.03);">
            
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:20px; border-bottom:1px solid #f1f5f9; padding-bottom:16px;">
              <div>
                <h3 style="margin:0 0 4px; font-size:18px; color:#0f172a; font-weight:700;">
                  Order #<?= htmlspecialchars($tracking_result['order_number']) ?>
                </h3>
                <span style="font-size:13px; color:#64748b;">
                  Placed on <?= date('M d, Y', strtotime($tracking_result['created_at'])) ?>
                </span>
              </div>
              <div>
                <?php if ($isCancelled): ?>
                  <span style="background:#fee2e2; color:#991b1b; padding:6px 14px; border-radius:20px; font-size:12px; font-weight:700; text-transform:uppercase;">Cancelled</span>
                <?php else: ?>
                  <span style="background:#dbeafe; color:#1e40af; padding:6px 14px; border-radius:20px; font-size:12px; font-weight:700; text-transform:uppercase;">
                    <?= htmlspecialchars(ucwords(str_replace('_', ' ', $sStatus))) ?>
                  </span>
                <?php endif; ?>
              </div>
            </div>

            <?php if ($isCancelled): ?>
              <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:16px; margin-bottom:20px; color:#991b1b; font-size:14px;">
                <strong>Order Cancelled:</strong> This shipment was cancelled. Any reserved items have been returned to warehouse inventory.
              </div>
            <?php else: ?>
              <!-- Visual Step Tracker -->
              <div style="margin: 24px 0 32px;">
                <div style="display:flex; justify-content:space-between; position:relative; max-width:540px; margin:0 auto;">
                  
                  <!-- Step 1: Placed -->
                  <div style="text-align:center; flex:1; position:relative; z-index:2;">
                    <div style="width:34px; height:34px; border-radius:50%; margin:0 auto 8px; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; <?= $currentStep >= 1 ? 'background:#10b981; color:#fff;' : 'background:#e2e8f0; color:#64748b;' ?>">
                      ✓
                    </div>
                    <span style="font-size:12px; font-weight:600; color:<?= $currentStep >= 1 ? '#0f172a' : '#94a3b8' ?>;">Placed</span>
                  </div>

                  <!-- Step 2: Processing -->
                  <div style="text-align:center; flex:1; position:relative; z-index:2;">
                    <div style="width:34px; height:34px; border-radius:50%; margin:0 auto 8px; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; <?= $currentStep >= 2 ? 'background:#10b981; color:#fff;' : ($currentStep == 1 ? 'background:#3b82f6; color:#fff;' : 'background:#e2e8f0; color:#64748b;') ?>">
                      <?= $currentStep >= 2 ? '✓' : '2' ?>
                    </div>
                    <span style="font-size:12px; font-weight:600; color:<?= $currentStep >= 2 ? '#0f172a' : '#94a3b8' ?>;">Processing</span>
                  </div>

                  <!-- Step 3: Shipped -->
                  <div style="text-align:center; flex:1; position:relative; z-index:2;">
                    <div style="width:34px; height:34px; border-radius:50%; margin:0 auto 8px; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; <?= $currentStep >= 3 ? 'background:#10b981; color:#fff;' : 'background:#e2e8f0; color:#64748b;' ?>">
                      <?= $currentStep >= 3 ? '✓' : '3' ?>
                    </div>
                    <span style="font-size:12px; font-weight:600; color:<?= $currentStep >= 3 ? '#0f172a' : '#94a3b8' ?>;">Shipped</span>
                  </div>

                  <!-- Step 4: Out for Delivery -->
                  <div style="text-align:center; flex:1; position:relative; z-index:2;">
                    <div style="width:34px; height:34px; border-radius:50%; margin:0 auto 8px; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; <?= $currentStep >= 4 ? 'background:#10b981; color:#fff;' : 'background:#e2e8f0; color:#64748b;' ?>">
                      <?= $currentStep >= 4 ? '✓' : '4' ?>
                    </div>
                    <span style="font-size:12px; font-weight:600; color:<?= $currentStep >= 4 ? '#0f172a' : '#94a3b8' ?>;">Out for Delivery</span>
                  </div>

                  <!-- Step 5: Delivered -->
                  <div style="text-align:center; flex:1; position:relative; z-index:2;">
                    <div style="width:34px; height:34px; border-radius:50%; margin:0 auto 8px; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; <?= $currentStep >= 5 ? 'background:#10b981; color:#fff;' : 'background:#e2e8f0; color:#64748b;' ?>">
                      <?= $currentStep >= 5 ? '✓' : '5' ?>
                    </div>
                    <span style="font-size:12px; font-weight:600; color:<?= $currentStep >= 5 ? '#0f172a' : '#94a3b8' ?>;">Delivered</span>
                  </div>

                  <!-- Connector line -->
                  <div style="position:absolute; top:17px; left:10%; right:10%; height:3px; background:#e2e8f0; z-index:1;">
                    <div style="height:100%; background:#10b981; width:<?= min(100, max(0, ($currentStep - 1) * 25)) ?>%; transition:width 0.4s ease;"></div>
                  </div>

                </div>
              </div>
            <?php endif; ?>

            <!-- Shipping Details Card -->
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:16px; margin-bottom:20px;">
              <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:14px; font-size:13px;">
                <div>
                  <span style="color:#64748b; display:block; margin-bottom:2px;">Carrier &amp; Service</span>
                  <strong style="color:#0f172a;"><?= htmlspecialchars($carrier) ?></strong>
                </div>
                <div>
                  <span style="color:#64748b; display:block; margin-bottom:2px;">Tracking Number</span>
                  <?php if (!empty($trackingNum)): ?>
                    <strong style="color:#0f172a; word-break:break-all;"><?= htmlspecialchars($trackingNum) ?></strong>
                  <?php else: ?>
                    <span style="color:#94a3b8; font-style:italic;">Carrier dispatch in progress</span>
                  <?php endif; ?>
                </div>
                <div>
                  <span style="color:#64748b; display:block; margin-bottom:2px;">Fulfillment Status</span>
                  <strong style="color:#0f172a;"><?= htmlspecialchars(ucfirst($tracking_result['fulfillment_status'] ?? 'Unfulfilled')) ?></strong>
                </div>
              </div>

              <?php if (!empty($trackingNum)): ?>
                <div style="margin-top:14px; padding-top:12px; border-top:1px dashed #cbd5e1;">
                  <a href="<?= $trackUrl ?>" target="_blank" rel="noopener" style="display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:600; color:#5c56e8; text-decoration:none;">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                      <polyline points="15 3 21 3 21 9"></polyline>
                      <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                    Track directly on carrier portal
                  </a>
                </div>
              <?php endif; ?>
            </div>

            <!-- Items in this Order -->
            <?php if (!empty($tracking_result['items'])): ?>
              <div>
                <h4 style="margin:0 0 12px; font-size:14px; color:#334155; font-weight:600;">Items in this Shipment</h4>
                <div style="display:flex; flex-direction:column; gap:10px;">
                  <?php foreach ($tracking_result['items'] as $item): 
                    $pImg = !empty($item['featured_image']) ? asset($item['featured_image']) : asset('assets/images/product-1.webp');
                  ?>
                    <div style="display:flex; align-items:center; gap:12px; padding:8px 0; border-bottom:1px solid #f1f5f9;">
                      <img src="<?= $pImg ?>" alt="<?= htmlspecialchars($item['product_title']) ?>" style="width:42px; height:42px; object-fit:cover; border-radius:6px; border:1px solid #e2e8f0;" onerror="this.src='<?= asset('assets/images/vape-image.webp') ?>';">
                      <div style="flex:1;">
                        <div style="font-size:13px; font-weight:600; color:#0f172a;"><?= htmlspecialchars($item['product_title']) ?></div>
                        <?php if (!empty($item['variant_name'])): ?>
                          <div style="font-size:12px; color:#64748b;">Option: <?= htmlspecialchars($item['variant_name']) ?></div>
                        <?php endif; ?>
                      </div>
                      <div style="font-size:13px; font-weight:600; color:#475569;">
                        Qty: <?= (int)$item['quantity'] ?>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>

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
            <input type="text" name="order_number" class="track-order-input" required placeholder="e.g. 104827" value="<?= htmlspecialchars($prefillOrder) ?>">
          </div>

          <div class="track-order-form-group mb-lg">
            <label class="track-order-label">Billing / Shipping Email *</label>
            <input type="email" name="customer_email" class="track-order-input" required placeholder="Email used during checkout" value="<?= htmlspecialchars($prefillEmail) ?>">
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

