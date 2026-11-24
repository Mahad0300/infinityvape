<?php
$page_title = 'Brands Management';
$current_admin_page = 'brands';
$page_script = 'brands.js';
include dirname(__DIR__) . '/includes/header.php';
?>

<!-- Title & Action Bar -->
<div class="page-title-bar">
  <div class="page-heading-group">
    <h1>Brands Management</h1>
    <p class="page-subtitle">Manage vape hardware vendors, disposable manufacturers, and e-liquid brands.</p>
  </div>
  <div class="page-actions-group">
    <a href="<?= url('admin/add-brand') ?>" class="btn-admin btn-admin-primary">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
      </svg>
      <span>Add New Brand</span>
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

<!-- All Brands Table Card -->
<div class="admin-card">
  <div class="admin-card-header">
    <div class="table-filter-bar">
      <!-- Search Input -->
      <div class="table-search-box">
        <input type="text" id="adminTableSearch" class="admin-form-input table-search-input" placeholder="Search brands...">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="table-search-icon">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
      </div>

      <!-- Status Filter -->
      <div class="table-status-filter-wrap">
        <select id="adminStatusFilter" class="admin-form-select admin-filter-select">
          <option value="all" selected>All Statuses</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
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
      <span class="table-counter-text">Total: <strong><?= !empty($brands) ? count($brands) : 0 ?></strong> Brands</span>
    </div>
  </div>

  <div class="admin-card-body p-0">
    <div class="table-responsive">
      <table class="admin-table" id="brandsTable">
        <thead>
          <tr>
            <th>Brand Name &amp; Logo</th>
            <th>Website</th>
            <th>Products Count</th>
            <th>Sort Order</th>
            <th>Status</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($brands)): ?>
            <?php foreach ($brands as $brand): ?>
              <?php
                $isActive = !empty($brand['is_active']);
                $statusLabel = $isActive ? 'Active' : 'Inactive';
                $badgeClass = $isActive ? 'badge-active' : 'badge-draft';
                $productCount = (int)($brand['products_count'] ?? 0);
                $initial = strtoupper(substr($brand['name'] ?? 'B', 0, 1));
                $logoUrl = !empty($brand['logo_url']) ? asset($brand['logo_url']) : '';
              ?>
              <tr>
                <td>
                  <div class="d-flex align-center gap-md">
                    <?php if (!empty($logoUrl)): ?>
                      <div class="brand-avatar-box">
                        <img src="<?= e($logoUrl) ?>" alt="<?= e($brand['name']) ?>" onerror="this.parentElement.innerHTML='<div class=\'collection-avatar-icon\'><?= e($initial) ?></div>';">
                      </div>
                    <?php else: ?>
                      <div class="collection-avatar-icon">
                        <?= e($initial) ?>
                      </div>
                    <?php endif; ?>
                    <div>
                      <strong class="text-dark"><?= e($brand['name']) ?></strong>
                      <div class="table-cell-subtext text-muted" style="font-size: 11px;">Slug: <?= e($brand['slug']) ?></div>
                    </div>
                  </div>
                </td>
                <td>
                  <?php if (!empty($brand['website_url'])): ?>
                    <a href="<?= e($brand['website_url']) ?>" target="_blank" rel="noopener noreferrer" class="text-primary d-flex align-center gap-xs" style="font-size: 13px;">
                      <span><?= e(preg_replace('#^https?://(www\.)?#', '', rtrim($brand['website_url'], '/'))) ?></span>
                      <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                    </a>
                  <?php else: ?>
                    <span class="text-muted" style="font-size: 13px;">&mdash;</span>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="table-cell-text">
                    <strong><?= $productCount ?></strong> <?= $productCount === 1 ? 'product' : 'products' ?>
                  </span>
                </td>
                <td>
                  <span class="text-dark" style="font-size: 13px; font-weight: 500;"><?= (int)($brand['sort_order'] ?? 0) ?></span>
                </td>
                <td>
                  <span class="badge-status <?= $badgeClass ?>"><?= $statusLabel ?></span>
                </td>
                <td>
                  <div class="table-actions-cell-right">
                    <!-- Edit Action -->
                    <a href="<?= url('admin/edit-brand') ?>?id=<?= $brand['id'] ?>" class="btn-icon-only" title="Edit Brand">
                      <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                      </svg>
                    </a>

                    <!-- View on Live Storefront -->
                    <a href="<?= \App\Models\Brand::getUrl($brand) ?>" target="_blank" class="btn-icon-only" title="View Brand Storefront Page">
                      <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                      </svg>
                    </a>

                    <!-- Delete Action with confirmation (POST + CSRF) -->
                    <form action="<?= url('admin/delete-brand') ?>" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete brand \'<?= e(addslashes($brand['name'])) ?>\'?');">
                      <?= csrf_field() ?>
                      <input type="hidden" name="id" value="<?= $brand['id'] ?>">
                      <button type="submit" class="btn-icon-only danger btn-delete-row" title="Delete Brand" style="background:none; border:none; padding:0; cursor:pointer;">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                No brands found in database. Click "Add New Brand" to create one.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Client-side Pagination Footer Engine -->
    <div class="table-pagination-bar">
      <div class="pagination-info">
        Showing <span class="pagination-start">1</span> to <span class="pagination-end"><?= min(10, count($brands)) ?></span> of <span class="pagination-total"><?= count($brands) ?></span> brands
      </div>
      <div class="pagination-controls">
        <button type="button" class="btn-pagination prev-page" disabled>Previous</button>
        <div class="pagination-pages"></div>
        <button type="button" class="btn-pagination next-page">Next</button>
      </div>
    </div>

  </div>
</div>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
