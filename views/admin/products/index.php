<?php
$page_title = 'Products Management';
$current_admin_page = 'products';
$page_script = 'products.js';
include dirname(__DIR__) . '/includes/header.php';
?>

<!-- Title & Action Bar -->
<div class="page-title-bar">
  <div class="page-heading-group">
    <h1>Products Management</h1>
    <p class="page-subtitle">Manage vape devices, disposable pods, e-liquids, and hardware inventory.</p>
  </div>
  <div class="page-actions-group">
    <a href="<?= url('admin/add-product') ?>" class="btn-admin btn-admin-primary">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
      </svg>
      <span>Add New Product</span>
    </a>
  </div>
</div>

<!-- Flash Notifications -->
<?php if (!empty($success)): ?>
<div class="admin-alert admin-alert-success mb-lg">
  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
    <polyline points="22 4 12 14.01 9 11.01"></polyline>
  </svg>
  <span><?= e($success) ?></span>
</div>
<?php endif; ?>

<?php if (!empty($error)): ?>
<div class="admin-alert admin-alert-danger mb-lg">
  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
    <circle cx="12" cy="12" r="10"></circle>
    <line x1="12" y1="8" x2="12" y2="12"></line>
    <line x1="12" y1="16" x2="12.01" y2="16"></line>
  </svg>
  <span><?= e($error) ?></span>
</div>
<?php endif; ?>

<!-- Products Table Card -->
<div class="admin-card">
  <div class="admin-card-header">
    <div class="table-filter-bar">
      <!-- Search Input -->
      <div class="table-search-box">
        <input type="text" id="adminTableSearch" class="admin-form-input table-search-input" placeholder="Search by title, SKU, brand...">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="table-search-icon">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
      </div>

      <!-- Status Filter -->
      <div class="table-status-select-wrap">
        <select id="adminStatusFilter" class="admin-form-select">
          <option value="all">All Statuses</option>
          <option value="in stock">In Stock</option>
          <option value="low stock">Low Stock</option>
          <option value="out of stock">Out of Stock</option>
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
      <span class="table-counter-text">Total: <strong><?= !empty($products) ? count($products) : 0 ?></strong> Products</span>
    </div>
  </div>

  <div class="admin-card-body p-0">
    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>Collection</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Status</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($products)): ?>
            <?php foreach ($products as $p): ?>
              <?php
                $imgSrc = !empty($p['featured_image']) ? asset($p['featured_image']) : (!empty($p['img']) ? asset($p['img']) : asset('assets/images/no-image.svg'));
                $priceVal = (float)str_replace(['$', ','], '', $p['base_price'] ?? $p['price'] ?? 0);
                $variantsCount = (int)($p['variants_count'] ?? 0);
                $stockVal = (int)($p['calculated_stock'] ?? $p['stock_quantity'] ?? $p['stock'] ?? 0);
                $statusVal = ($stockVal <= 0) ? 'Out of Stock' : (($stockVal < 15) ? 'Low Stock' : 'In Stock');
              ?>
              <tr>
                <td>
                  <div class="table-product-cell">
                    <img src="<?= e($imgSrc) ?>" alt="<?= e($p['title']) ?>" class="table-product-thumb" onerror="this.onerror=null;this.src='<?= asset('assets/images/no-image.svg') ?>';">
                    <div class="table-product-info">
                      <span class="table-product-title"><?= e($p['title']) ?></span>
                      <span class="table-product-meta"><?= e($p['brand'] ?? 'Infinity Vape') ?> &bull; SKU: <?= e($p['sku'] ?? 'IVS-PROD') ?></span>
                    </div>
                  </div>
                </td>
                <td><?= e($p['category'] ?? 'General') ?></td>
                <td><strong>$<?= number_format($priceVal, 2) ?></strong></td>
                <td>
                  <strong><?= number_format($stockVal) ?></strong> in stock
                  <?php if ($variantsCount > 0): ?>
                    <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                      for <?= $variantsCount ?> <?= $variantsCount === 1 ? 'variant' : 'variants' ?>
                    </div>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (strtolower($statusVal) === 'in stock'): ?>
                    <span class="badge-status badge-instock">In Stock</span>
                  <?php elseif (strtolower($statusVal) === 'low stock'): ?>
                    <span class="badge-status badge-lowstock">Low Stock</span>
                  <?php else: ?>
                    <span class="badge-status badge-outofstock">Out of Stock</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="table-actions-cell-right">
                    <!-- Preview on Storefront -->
                    <a href="<?= url('product/' . urlencode($p['slug'] ?? $p['id'])) ?>" target="_blank" class="btn-icon-only" title="Preview on Store">
                      <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                      </svg>
                    </a>
                    <!-- Edit Action -->
                    <a href="<?= url('admin/edit-product?id=' . $p['id']) ?>" class="btn-icon-only" title="Edit Product">
                      <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                      </svg>
                    </a>
                    <!-- Delete Action (POST + CSRF) -->
                    <form action="<?= url('admin/delete-product') ?>" method="POST" style="display:inline;">
                      <?= csrf_field() ?>
                      <input type="hidden" name="id" value="<?= $p['id'] ?>">
                      <button type="submit" class="btn-icon-only danger btn-delete-row" title="Delete Product" data-item-name="<?= e($p['title']) ?>" style="background:none; border:none; padding:0; cursor:pointer;">
                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <polyline points="3 6 5 6 21 6"></polyline>
                          <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center py-xl text-muted">
                No products found in catalog. Click "Add New Product" to create one.
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
      Showing <span class="pagination-start">1</span> to <span class="pagination-end"><?= min(10, count($products)) ?></span> of <span class="pagination-total"><?= count($products) ?></span> products
    </div>
    <div class="pagination-controls">
      <button type="button" class="btn-pagination prev-page" disabled title="Previous page">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
        <span>Previous</span>
      </button>
      <div class="pagination-pages"></div>
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
