<?php
$page_title = 'Orders';
$current_admin_page = 'orders';
$page_script = 'orders.js';
include dirname(__DIR__) . '/includes/header.php';

// Orders Data Master List
$orders = isset($orders) && !empty($orders) ? $orders : [];
?>

<!-- Title & Action Bar -->
<div class="page-title-bar">
  <div class="page-heading-group">
    <h1>Orders Management</h1>
    <p class="page-subtitle">Track customer orders, shipping status, receipts, and fulfillment.</p>
  </div>
</div>

<!-- Orders Table Card -->
<div class="admin-card">
  <div class="admin-card-header">
    <div class="table-filter-bar">
      <!-- Search Input -->
      <div class="table-search-box">
        <input type="text" id="adminTableSearch" class="admin-form-input table-search-input" placeholder="Search by Order ID, customer, delivery...">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="table-search-icon">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
      </div>

      <!-- Status Filter Dropdown -->
      <div class="table-status-select-wrap">
        <select id="adminStatusFilter" class="admin-form-select">
          <option value="all">All Statuses</option>
          <option value="paid">Paid</option>
          <option value="pending">Pending</option>
          <option value="fulfilled">Fulfilled</option>
          <option value="unfulfilled">Unfulfilled</option>
          <option value="in transit">In Transit</option>
          <option value="delivered">Delivered</option>
          <option value="refunded">Refunded</option>
        </select>
      </div>

      <!-- Per Page Filter -->
      <div class="table-per-page-wrap">
        <select id="adminPerPageSelect" class="admin-form-select admin-per-page-select">
          <option value="10" selected>10 per page</option>
          <option value="25">25 per page</option>
          <option value="50">50 per page</option>
        </select>
      </div>
    </div>

    <div>
      <span class="table-counter-text">Total: <strong><?php echo count($orders); ?></strong> Orders</span>
    </div>
  </div>

  <div class="admin-card-body p-0">
    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Date</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Payment Status</th>
            <th>Fulfillment Status</th>
            <th>Item</th>
            <th>Delivery Status</th>
            <th>Delivery Method</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $o): ?>
            <tr>
              <!-- Order ID (Click opens order-detail.php) -->
              <td>
                <a href="<?= url('admin/order-detail') ?>?id=<?php echo $o['id']; ?>" class="table-order-id-link" title="View Order #<?php echo $o['id']; ?>">
                  #<?php echo $o['id']; ?>
                </a>
              </td>

              <!-- Date -->
              <td>
                <span class="table-cell-text"><?php echo $o['date']; ?></span>
              </td>

              <!-- Customer -->
              <td>
                <span class="table-cell-text"><?php echo htmlspecialchars($o['customer']); ?></span>
              </td>

              <!-- Total -->
              <td>
                <span class="table-cell-text"><?php echo $o['total']; ?></span>
              </td>

              <!-- Payment Status -->
              <td>
                <?php if ($o['payment_status'] === 'Paid'): ?>
                  <span class="badge-status badge-paid">Paid</span>
                <?php elseif ($o['payment_status'] === 'Pending'): ?>
                  <span class="badge-status badge-pending">Pending</span>
                <?php elseif ($o['payment_status'] === 'Partially Paid'): ?>
                  <span class="badge-status badge-partially-paid">Partially Paid</span>
                <?php else: ?>
                  <span class="badge-status badge-refunded">Refunded</span>
                <?php endif; ?>
              </td>

              <!-- Fulfillment Status -->
              <td>
                <?php if ($o['fulfillment_status'] === 'Fulfilled'): ?>
                  <span class="badge-status badge-fulfilled">Fulfilled</span>
                <?php elseif ($o['fulfillment_status'] === 'Partially Fulfilled'): ?>
                  <span class="badge-status badge-partially-fulfilled">Partially Fulfilled</span>
                <?php else: ?>
                  <span class="badge-status badge-unfulfilled">Unfulfilled</span>
                <?php endif; ?>
              </td>

              <!-- Item -->
              <td>
                <span class="table-cell-text"><?php echo $o['items']; ?></span>
              </td>

              <!-- Delivery Status -->
              <td>
                <?php 
                  $normStatus = strtolower(trim($o['delivery_status'] ?? 'pending'));
                  if ($normStatus === 'delivered'): 
                ?>
                  <span class="badge-status badge-delivered">Delivered</span>
                <?php elseif ($normStatus === 'in transit' || $normStatus === 'in_transit'): ?>
                  <span class="badge-status badge-in-transit">In Transit</span>
                <?php elseif ($normStatus === 'out for delivery' || $normStatus === 'out_for_delivery'): ?>
                  <span class="badge-status badge-out-for-delivery">Out for Delivery</span>
                <?php elseif ($normStatus === 'shipped'): ?>
                  <span class="badge-status badge-shipped">Shipped</span>
                <?php elseif ($normStatus === 'confirmed' || $normStatus === 'processing'): ?>
                  <span class="badge-status badge-paid" style="background:#ecfdf5; color:#059669; border-color:#a7f3d0;"><?= htmlspecialchars(ucwords($normStatus)) ?></span>
                <?php elseif ($normStatus === 'cancelled'): ?>
                  <span class="badge-status badge-cancelled">Cancelled</span>
                <?php else: ?>
                  <span class="badge-status badge-pending">Pending</span>
                <?php endif; ?>
              </td>

              <!-- Delivery Method -->
              <td>
                <span class="table-cell-text"><?php echo htmlspecialchars($o['delivery_method']); ?></span>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="9" class="text-center p-xl text-muted">
                No customer orders found in the database.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Table Pagination Footer -->
  <div class="table-pagination-bar">
    <div class="pagination-info-text">
      Showing <span class="pagination-start">1</span> to <span class="pagination-end"><?php echo min(10, count($orders)); ?></span> of <span class="pagination-total"><?php echo count($orders); ?></span> orders
    </div>
    <div class="pagination-controls">
      <button type="button" class="btn-pagination prev-page" disabled title="Previous page">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
        <span>Previous</span>
      </button>
      <div class="pagination-pages">
        <button type="button" class="page-num active">1</button>
        <button type="button" class="page-num">2</button>
      </div>
      <button type="button" class="btn-pagination next-page" title="Next page">
        <span>Next</span>
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
      </button>
    </div>
  </div>
</div>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
