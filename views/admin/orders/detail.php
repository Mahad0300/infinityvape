<?php
$order_id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : ($order['order_number'] ?? ($order['id'] ?? ''));
$page_title = 'Order #' . $order_id;
$current_admin_page = 'orders';
$page_script = 'order-detail.js';
include dirname(__DIR__) . '/includes/header.php';

$custName = $order['customer_name'] ?? trim(($order['customer_first_name'] ?? '') . ' ' . ($order['customer_last_name'] ?? ''));
if (empty($custName)) {
    $custName = 'Guest Customer';
}
$custEmail = $order['customer_email'] ?? 'N/A';
$custPhone = $order['customer_phone'] ?? 'N/A';
$orderNum = $order['order_number'] ?? $order_id;
$paymentStatus = ucfirst($order['payment_status'] ?? 'Pending');
$shippingStatus = ucwords(str_replace('_', ' ', $order['shipping_status'] ?? 'Pending'));
$itemsList = !empty($order['items_list']) ? $order['items_list'] : [];
$subtotal = $order['formatted_subtotal'] ?? ('$' . number_format((float)($order['subtotal'] ?? 0), 2));
$shippingFee = $order['formatted_shipping'] ?? ('$' . number_format((float)($order['shipping_cost'] ?? 0), 2));
$taxAmount = $order['formatted_tax'] ?? ('$' . number_format((float)($order['tax_amount'] ?? 0), 2));
$discountAmount = $order['formatted_discount'] ?? ('$' . number_format((float)($order['discount_amount'] ?? 0), 2));
$grandTotal = $order['formatted_total'] ?? ('$' . number_format((float)($order['total_amount'] ?? 0), 2));
$orderDate = $order['date'] ?? (!empty($order['created_at']) ? date('M d, Y h:i A', strtotime($order['created_at'])) : date('M d, Y h:i A'));
$shippingAddr = !empty($order['shipping_address']) ? $order['shipping_address'] : 'No shipping address recorded';
?>

<!-- Title & Action Bar -->
<div class="page-title-bar">
  <div class="page-heading-group">
    <div class="d-flex align-center gap-md">
      <a href="<?= url('admin/orders') ?>" class="btn-icon-only" title="Back to Orders">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="19" y1="12" x2="5" y2="12"></line>
          <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
      </a>
      <h1>Order #<?= htmlspecialchars($orderNum); ?></h1>
      <span id="badgePaymentHeader" class="badge-status badge-<?= strtolower($paymentStatus) === 'paid' ? 'paid' : (strtolower($paymentStatus) === 'refunded' ? 'refunded' : 'pending') ?>"><?= htmlspecialchars($paymentStatus) ?></span>
      <?php 
        $shipStatus = strtolower($order['shipping_status'] ?? 'pending');
        $shipBadgeClass = match($shipStatus) {
            'delivered' => 'delivered',
            'shipped', 'in_transit', 'out_for_delivery', 'processing' => 'shipped',
            'cancelled' => 'cancelled',
            default => 'pending'
        };
      ?>
      <span id="badgeShippingHeader" class="badge-status badge-<?= $shipBadgeClass ?>"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $order['shipping_status'] ?? 'Pending'))) ?></span>
      <span id="badgeFulfillmentHeader" class="badge-status badge-<?= strtolower($order['fulfillment_status'] ?? 'unfulfilled') === 'fulfilled' ? 'fulfilled' : 'unfulfilled' ?>"><?= htmlspecialchars(ucfirst($order['fulfillment_status'] ?? 'Unfulfilled')) ?></span>
    </div>
    <p class="page-subtitle">Placed on <?= htmlspecialchars($orderDate); ?> • <?= htmlspecialchars(ucwords(str_replace('_', ' ', $order['payment_method'] ?? 'Credit Card'))) ?></p>
  </div>
  <div class="page-actions-group">
    <button type="button" class="btn-admin btn-admin-secondary" onclick="window.print()">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="6 9 6 2 18 2 18 9"></polyline>
        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
        <rect x="6" y="14" width="12" height="8"></rect>
      </svg>
      <span>Print Invoice</span>
    </button>
  </div>
</div>

<div class="detail-grid-layout">
  
  <!-- Left Side: Order Items & Financial Summary -->
  <div class="admin-col-main">
    
    <!-- Ordered Items Card -->
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 class="admin-card-title">Order Items (<?= count($itemsList) ?>)</h2>
      </div>
      <div class="admin-card-body p-0">
        <div class="table-responsive">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Qty</th>
                <th class="text-right">Total</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($itemsList)): ?>
                <?php foreach ($itemsList as $it): 
                  $linePrice = (float)($it['price_at_purchase'] ?? 0);
                  $lineQty = (int)($it['quantity'] ?? 1);
                  $lineTotal = '$' . number_format($linePrice * $lineQty, 2);
                  $pImg = !empty($it['featured_image']) ? asset($it['featured_image']) : asset('assets/images/product-1.webp');
                ?>
                <tr>
                  <td>
                    <div class="table-product-cell">
                      <img src="<?= $pImg ?>" alt="<?= htmlspecialchars($it['product_title']) ?>" class="table-product-thumb" onerror="this.onerror=null;this.src='<?= asset('assets/images/vape-image.webp') ?>';">
                      <div class="table-product-info">
                        <span class="table-product-title"><?= htmlspecialchars($it['product_title']) ?></span>
                        <?php if (!empty($it['variant_name'])): ?>
                          <span class="table-product-meta">Flavor / Option: <?= htmlspecialchars($it['variant_name']) ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                  </td>
                  <td>$<?= number_format($linePrice, 2) ?></td>
                  <td><?= $lineQty ?></td>
                  <td class="text-right"><strong><?= $lineTotal ?></strong></td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" class="text-center" style="padding: 24px; color: #64748b;">No items recorded for this order.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Financial Breakdown Card -->
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 class="admin-card-title">Order Financials</h2>
      </div>
      <div class="admin-card-body">
        <div class="info-list-group">
          <div class="info-list-item">
            <span class="info-list-label">Items Subtotal</span>
            <span class="info-list-val"><?= $subtotal ?></span>
          </div>
          <?php if (!empty($order['discount_amount']) && (float)$order['discount_amount'] > 0): ?>
          <div class="info-list-item">
            <span class="info-list-label">Discount Applied</span>
            <span class="info-list-val text-success">-<?= $discountAmount ?></span>
          </div>
          <?php endif; ?>
          <div class="info-list-item">
            <span class="info-list-label">Shipping (<?= htmlspecialchars($order['delivery_method'] ?? 'Standard Ground') ?>)</span>
            <span class="info-list-val"><?= $shippingFee ?></span>
          </div>
          <div class="info-list-item">
            <span class="info-list-label">Sales Tax</span>
            <span class="info-list-val"><?= $taxAmount ?></span>
          </div>
          <div class="info-list-item info-grand-total-row">
            <span class="info-list-label grand-total-label">Grand Total Paid</span>
            <span class="info-list-val grand-total-val"><?= $grandTotal ?></span>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- Right Side: Customer Details & Fulfillment Controls -->
  <div class="admin-col-side">
    
    <!-- Fulfillment & Status Card -->
    <div class="admin-card">
      <div class="admin-card-header d-flex justify-between align-center">
        <h2 class="admin-card-title">Order Status &amp; Tracking</h2>
        <span class="badge-status badge-<?= strtolower($order['fulfillment_status'] ?? 'unfulfilled') === 'fulfilled' ? 'fulfilled' : 'unfulfilled' ?>" id="cardFulfillmentBadge">
          <?= htmlspecialchars(ucfirst($order['fulfillment_status'] ?? 'Unfulfilled')) ?>
        </span>
      </div>
      <div class="admin-card-body">
        <div id="statusUpdateAlert" style="display: none; padding: 10px 14px; border-radius: 6px; font-size: 13px; margin-bottom: 15px;"></div>

        <form id="orderStatusForm" class="admin-ajax-form">
          <input type="hidden" name="order_id" value="<?= htmlspecialchars($orderNum) ?>">
          
          <div class="admin-form-group">
            <label for="orderStatusSelect" class="admin-form-label">Shipping / Delivery Status</label>
            <select id="orderStatusSelect" name="shipping_status" class="admin-form-select">
              <option value="pending" <?= ($order['shipping_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending (Order Placed)</option>
              <option value="confirmed" <?= ($order['shipping_status'] ?? '') === 'confirmed' ? 'selected' : '' ?>>Confirmed (Payment Verified)</option>
              <option value="processing" <?= ($order['shipping_status'] ?? '') === 'processing' ? 'selected' : '' ?>>Processing (Items Picking &amp; Packing)</option>
              <option value="shipped" <?= ($order['shipping_status'] ?? '') === 'shipped' ? 'selected' : '' ?>>Shipped (Handed to Carrier)</option>
              <option value="in_transit" <?= ($order['shipping_status'] ?? '') === 'in_transit' ? 'selected' : '' ?>>In Transit (On the Way)</option>
              <option value="out_for_delivery" <?= ($order['shipping_status'] ?? '') === 'out_for_delivery' ? 'selected' : '' ?>>Out for Delivery (Arriving Today)</option>
              <option value="delivered" <?= ($order['shipping_status'] ?? '') === 'delivered' ? 'selected' : '' ?>>Delivered (Completed)</option>
              <option value="cancelled" <?= ($order['shipping_status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled (Restock Items)</option>
            </select>
          </div>

          <div class="admin-form-group">
            <label for="orderPaymentStatusSelect" class="admin-form-label">Payment Status</label>
            <select id="orderPaymentStatusSelect" name="payment_status" class="admin-form-select">
              <option value="paid" <?= ($order['payment_status'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
              <option value="pending" <?= ($order['payment_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
              <option value="refunded" <?= ($order['payment_status'] ?? '') === 'refunded' ? 'selected' : '' ?>>Refunded</option>
              <option value="failed" <?= ($order['payment_status'] ?? '') === 'failed' ? 'selected' : '' ?>>Failed</option>
            </select>
          </div>

          <div class="admin-form-group">
            <label for="shippingCarrierInput" class="admin-form-label">Shipping Carrier / Delivery Method</label>
            <input list="carrierList" id="shippingCarrierInput" name="shipping_carrier" class="admin-form-input" 
                   value="<?= htmlspecialchars($order['shipping_carrier'] ?: ($order['delivery_method'] ?: 'USPS Priority (Adult Signature 21+)')) ?>" 
                   placeholder="Select or enter carrier">
            <datalist id="carrierList">
              <option value="USPS Priority (Adult Signature 21+)">
              <option value="UPS Ground (Adult Signature Required)">
              <option value="FedEx Ground (Direct Signature)">
              <option value="In-Store Pickup (Oklahoma City)">
              <option value="Local Courier">
            </datalist>
          </div>

          <div class="admin-form-group">
            <label for="trackingNumberInput" class="admin-form-label">Tracking Number</label>
            <div class="d-flex gap-sm">
              <input type="text" id="trackingNumberInput" name="tracking_number" class="admin-form-input" 
                     value="<?= htmlspecialchars($order['tracking_number'] ?? '') ?>" 
                     placeholder="e.g. 9400111899562534567890">
            </div>
            <?php if (!empty($order['tracking_number'])): 
              $carrier = strtolower($order['shipping_carrier'] ?? '');
              $trackUrl = 'https://www.google.com/search?q=' . urlencode($order['tracking_number']);
              if (str_contains($carrier, 'usps')) {
                $trackUrl = 'https://tools.usps.com/go/TrackConfirmAction?tLabels=' . urlencode($order['tracking_number']);
              } elseif (str_contains($carrier, 'ups')) {
                $trackUrl = 'https://www.ups.com/track?tracknum=' . urlencode($order['tracking_number']);
              } elseif (str_contains($carrier, 'fedex')) {
                $trackUrl = 'https://www.fedex.com/fedextrack/?trknbr=' . urlencode($order['tracking_number']);
              }
            ?>
              <div class="mt-xs" style="font-size: 12px;">
                <a href="<?= $trackUrl ?>" target="_blank" rel="noopener" class="link-primary" style="display: inline-flex; align-items: center; gap: 4px;">
                  <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                    <polyline points="15 3 21 3 21 9"></polyline>
                    <line x1="10" y1="14" x2="21" y2="3"></line>
                  </svg>
                  Track on <?= htmlspecialchars($order['shipping_carrier'] ?: 'Carrier Site') ?>
                </a>
              </div>
            <?php endif; ?>
          </div>

          <div class="admin-form-group">
            <label for="adminNotesInput" class="admin-form-label">Internal Fulfillment &amp; Audit Notes</label>
            <textarea id="adminNotesInput" name="admin_notes" class="admin-form-input" rows="2" 
                      placeholder="Add dispatch notes, warehouse scan notes, or cancellation reasons..."><?= htmlspecialchars($order['admin_notes'] ?? '') ?></textarea>
          </div>

          <button type="submit" id="btnSaveOrderTracking" class="btn-admin btn-admin-primary btn-full-width">
            <span class="btn-text">Update Order Status</span>
          </button>
        </form>
      </div>
    </div>

    <!-- Customer Profile Card -->
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 class="admin-card-title">Customer Information</h2>
      </div>
      <div class="admin-card-body">
        <div class="table-customer-cell customer-card-header-user">
          <div class="table-customer-avatar customer-avatar-lg">
            <?= strtoupper(substr($custName, 0, 1)) ?>
          </div>
          <div>
            <strong class="customer-detail-name"><?= htmlspecialchars($custName) ?></strong>
            <div class="customer-since-meta">Order Customer</div>
          </div>
        </div>

        <div class="info-list-group">
          <div class="info-list-item">
            <span class="info-list-label">Email</span>
            <span class="info-list-val"><a href="mailto:<?= htmlspecialchars($custEmail) ?>" class="link-primary"><?= htmlspecialchars($custEmail) ?></a></span>
          </div>
          <div class="info-list-item">
            <span class="info-list-label">Phone</span>
            <span class="info-list-val"><?= htmlspecialchars($custPhone) ?></span>
          </div>
          <div class="info-list-item">
            <span class="info-list-label">Age Verified</span>
            <span class="info-list-val text-success">✓ 21+ Confirmed</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Shipping & Billing Address Card -->
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 class="admin-card-title">Shipping Address</h2>
      </div>
      <div class="admin-card-body">
        <div class="address-block">
          <p class="address-text" style="line-height: 1.6; color: #334155; margin: 0;">
            <strong><?= htmlspecialchars($custName) ?></strong><br>
            <?= nl2br(htmlspecialchars($shippingAddr)) ?><br>
            <?= htmlspecialchars($custPhone) ?>
          </p>
        </div>
      </div>
    </div>

  </div>

</div>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
