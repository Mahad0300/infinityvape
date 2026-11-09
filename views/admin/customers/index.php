<?php
$page_title = 'Customers';
$current_admin_page = 'customers';
$page_script = 'customers.js';
include dirname(__DIR__) . '/includes/header.php';

// Dynamic Customers from Controller or Model fallback
$customers = $customers ?? \App\Models\Customer::all();
?>

<!-- Title & Action Bar -->
<div class="page-title-bar">
  <div class="page-heading-group">
    <h1>Customers Directory</h1>
    <p class="page-subtitle">Manage customer accounts, purchase history, and locations.</p>
  </div>
</div>

<!-- Customers Table Card -->
<div class="admin-card">
  <div class="admin-card-header">
    <div class="table-filter-bar">
      <!-- Search Input -->
      <div class="table-search-box">
        <input type="text" id="adminTableSearch" class="admin-form-input table-search-input" placeholder="Search customers by name, location, email..." value="<?= htmlspecialchars($search ?? '') ?>">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="table-search-icon">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
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
      <span class="table-counter-text">Total: <strong id="customerTotalCount"><?php echo count($customers); ?></strong> Customers</span>
    </div>
  </div>

  <div class="admin-card-body p-0">
    <div class="table-responsive">
      <table class="admin-table" id="customersTable">
        <thead>
          <tr>
            <th>Customer Name</th>
            <th>Location</th>
            <th>Orders</th>
            <th>Amount Spent</th>
            <th>Email</th>
          </tr>
        </thead>
        <tbody id="customersTableBody">
          <?php if (!empty($customers)): ?>
            <?php foreach ($customers as $c): ?>
              <?php 
                $loc = !empty($c['location']) ? $c['location'] : ((!empty($c['city']) && !empty($c['state'])) ? ($c['city'] . ', ' . $c['state']) : 'Not set yet');
              ?>
              <tr class="customer-row" 
                  data-name="<?= htmlspecialchars(strtolower($c['name'])) ?>" 
                  data-email="<?= htmlspecialchars(strtolower($c['email'])) ?>" 
                  data-location="<?= htmlspecialchars(strtolower($loc)) ?>">
                <!-- Customer Name (Clickable link to customer profile) -->
                <td>
                  <a href="<?= url('admin/customer-detail') ?>?id=<?php echo $c['id']; ?>" class="table-customer-link" title="View <?php echo htmlspecialchars($c['name']); ?>">
                    <strong><?php echo htmlspecialchars($c['name']); ?></strong>
                  </a>
                </td>

                <!-- Location -->
                <td>
                  <span class="table-cell-text"><?php echo htmlspecialchars($loc); ?></span>
                </td>

                <!-- Orders -->
                <td>
                  <span class="table-cell-text"><?php echo htmlspecialchars($c['orders']); ?></span>
                </td>

                <!-- Amount Spent -->
                <td>
                  <span class="table-cell-text font-medium"><?php echo htmlspecialchars($c['spent']); ?></span>
                </td>

                <!-- Email -->
                <td>
                  <span class="table-cell-text"><?php echo htmlspecialchars($c['email']); ?></span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr id="emptyCustomersRow">
              <td colspan="5" class="text-center p-xl text-muted">
                No customer accounts found in the database.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Table Pagination Footer -->
  <div class="table-pagination-bar" id="customersPaginationBar">
    <div class="pagination-info-text">
      Showing <span class="pagination-start">1</span> to <span class="pagination-end"><?php echo min(10, count($customers)); ?></span> of <span class="pagination-total"><?php echo count($customers); ?></span> customers
    </div>
    <div class="pagination-controls">
      <button type="button" class="btn-pagination prev-page" id="prevPageBtn" disabled title="Previous page">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
        <span>Previous</span>
      </button>
      <div class="pagination-pages" id="paginationPagesWrap">
        <button type="button" class="page-num active">1</button>
      </div>
      <button type="button" class="btn-pagination next-page" id="nextPageBtn" title="Next page">
        <span>Next</span>
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
      </button>
    </div>
  </div>
</div>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
