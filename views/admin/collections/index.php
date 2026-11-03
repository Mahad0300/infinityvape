<?php
$page_title = 'Collections Management';
$current_admin_page = 'collections';
$page_script = 'collections.js';
include dirname(__DIR__) . '/includes/header.php';
?>

<!-- Title & Action Bar -->
<div class="page-title-bar">
  <div class="page-heading-group">
    <h1>Collections Management</h1>
    <p class="page-subtitle">Organize product lines, disposables, hardware, and e-liquids catalog taxonomy.</p>
  </div>
  <div class="page-actions-group">
    <a href="<?= url('admin/add-collection') ?>" class="btn-admin btn-admin-primary">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
      </svg>
      <span>Add New Collection</span>
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

<!-- All Collections Full Width Table Card -->
<div class="admin-card">
  <div class="admin-card-header">
    <div class="table-filter-bar">
      <!-- Search Input -->
      <div class="table-search-box">
        <input type="text" id="adminTableSearch" class="admin-form-input table-search-input" placeholder="Search collections...">
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
      <span class="table-counter-text">Total: <strong><?= !empty($collections) ? count($collections) : 0 ?></strong> Collections</span>
    </div>
  </div>

  <div class="admin-card-body p-0">
    <div class="table-responsive">
      <table class="admin-table" id="collectionsTable">
        <thead>
          <tr>
            <th>Collection Name</th>
            <th>Products Count</th>
            <th>Status</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($collections)): ?>
            <?php foreach ($collections as $col): ?>
              <?php
                $isActive = !empty($col['is_active']);
                $statusLabel = $isActive ? 'Active' : 'Inactive';
                $badgeClass = $isActive ? 'badge-active' : 'badge-draft';
                $productCount = (int)($col['products_count'] ?? 0);
                $initial = strtoupper(substr($col['name'] ?? 'C', 0, 1));
              ?>
              <tr>
                <td>
                  <div class="d-flex align-center gap-md">
                    <div class="collection-avatar-icon">
                      <?= e($initial) ?>
                    </div>
                    <div>
                      <strong class="text-dark"><?= e($col['name']) ?></strong>
                      <?php if (!empty($col['parent_name'])): ?>
                        <div class="table-cell-subtext text-muted" style="font-size: 11px;">Parent: <?= e($col['parent_name']) ?></div>
                      <?php endif; ?>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="table-cell-text">
                    <strong><?= $productCount ?></strong> <?= $productCount === 1 ? 'product' : 'products' ?>
                  </span>
                </td>
                <td>
                  <span class="badge-status <?= $badgeClass ?>"><?= $statusLabel ?></span>
                </td>
                <td>
                  <div class="table-actions-cell-right">
                    <!-- Edit Action -->
                    <a href="<?= url('admin/edit-collection') ?>?id=<?= $col['id'] ?>" class="btn-icon-only" title="Edit Collection">
                      <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                      </svg>
                    </a>

                    <!-- View on Live Storefront -->
                    <a href="<?= url('category?c=' . urlencode($col['slug'])) ?>" target="_blank" class="btn-icon-only" title="View on Storefront">
                      <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                      </svg>
                    </a>

                    <!-- Delete Action with confirmation (POST + CSRF) -->
                    <form action="<?= url('admin/delete-collection') ?>" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete collection \'<?= e(addslashes($col['name'])) ?>\'?');">
                      <?= csrf_field() ?>
                      <input type="hidden" name="id" value="<?= $col['id'] ?>">
                      <button type="submit" class="btn-icon-only danger btn-delete-row" title="Delete Collection" style="background:none; border:none; padding:0; cursor:pointer;">
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
              <td colspan="4" class="text-center py-xl text-muted">
                No collections found in database. Click "Add New Collection" to create one.
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
      Showing <span class="pagination-start">1</span> to <span class="pagination-end"><?= min(10, count($collections ?? [])) ?></span> of <span class="pagination-total"><?= count($collections ?? []) ?></span> collections
    </div>
    <div class="pagination-controls">
      <button type="button" class="btn-pagination prev-page" disabled title="Previous page">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
        <span>Previous</span>
      </button>
      <div class="pagination-pages">
        <button type="button" class="page-num active">1</button>
      </div>
      <button type="button" class="btn-pagination next-page" disabled title="Next page">
        <span>Next</span>
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
      </button>
    </div>
  </div>
</div>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
