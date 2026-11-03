<?php
$page_title = 'Customer Reviews Management';
$current_admin_page = 'reviews';
include dirname(__DIR__) . '/includes/header.php';
?>

<!-- Title & Action Bar -->
<div class="page-title-bar">
  <div class="page-heading-group">
    <h1>Customer Reviews Management</h1>
    <p class="page-subtitle">Moderate, verify, and manage all customer ratings and feedback across your product catalog.</p>
  </div>
  <div class="page-actions-group">
    <button type="button" class="btn-admin btn-admin-primary" id="btnOpenAddReviewModal">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
      </svg>
      <span>Add Verified Review</span>
    </button>
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

<!-- Top Metrics / KPI Cards -->
<div class="admin-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px; margin-bottom: 24px;">
  
  <div class="admin-card" style="padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 16px;">
    <div style="width: 48px; height: 48px; border-radius: 10px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center;">
      <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
    </div>
    <div>
      <div style="font-size: 0.82rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Total Reviews</div>
      <div style="font-size: 1.6rem; font-weight: 800; color: #0f172a;"><?= number_format($stats['total'] ?? 0) ?></div>
    </div>
  </div>

  <div class="admin-card" style="padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 16px;">
    <div style="width: 48px; height: 48px; border-radius: 10px; background: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center;">
      <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
    </div>
    <div>
      <div style="font-size: 0.82rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Approved</div>
      <div style="font-size: 1.6rem; font-weight: 800; color: #10b981;"><?= number_format($stats['approved'] ?? 0) ?></div>
    </div>
  </div>

  <div class="admin-card" style="padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 16px;">
    <div style="width: 48px; height: 48px; border-radius: 10px; background: #fffbeb; color: #f59e0b; display: flex; align-items: center; justify-content: center;">
      <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
    </div>
    <div>
      <div style="font-size: 0.82rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Pending</div>
      <div style="font-size: 1.6rem; font-weight: 800; color: #f59e0b;"><?= number_format($stats['pending'] ?? 0) ?></div>
    </div>
  </div>

  <div class="admin-card" style="padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 16px;">
    <div style="width: 48px; height: 48px; border-radius: 10px; background: #fef2f2; color: #ef4444; display: flex; align-items: center; justify-content: center;">
      <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
    </div>
    <div>
      <div style="font-size: 0.82rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Rejected</div>
      <div style="font-size: 1.6rem; font-weight: 800; color: #ef4444;"><?= number_format($stats['rejected'] ?? 0) ?></div>
    </div>
  </div>

  <div class="admin-card" style="padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 16px;">
    <div style="width: 48px; height: 48px; border-radius: 10px; background: #fff7ed; color: #f97316; display: flex; align-items: center; justify-content: center;">
      <svg viewBox="0 0 24 24" width="24" height="24" fill="#f97316" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
    </div>
    <div>
      <div style="font-size: 0.82rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Store Average</div>
      <div style="font-size: 1.6rem; font-weight: 800; color: #f97316;"><?= $stats['avg_rating'] ?? '5.0' ?> <span style="font-size: 1rem; color: #94a3b8;">/ 5.0</span></div>
    </div>
  </div>

</div>

<!-- Reviews Table Card -->
<div class="admin-card">
  <div class="admin-card-header">
    <form method="GET" action="<?= url('admin/reviews') ?>" class="table-filter-bar" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
      
      <!-- Search Input -->
      <div class="table-search-box" style="position: relative; min-width: 260px;">
        <input type="text" name="search" value="<?= e($search_query) ?>" class="admin-form-input table-search-input" placeholder="Search customer, title, comment...">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="table-search-icon">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
      </div>

      <!-- Status Filter -->
      <div class="table-status-filter-wrap">
        <select name="status" class="admin-form-select admin-filter-select" onchange="this.form.submit()">
          <option value="all" <?= $selected_status === 'all' ? 'selected' : '' ?>>All Statuses</option>
          <option value="approved" <?= $selected_status === 'approved' ? 'selected' : '' ?>>Approved</option>
          <option value="pending" <?= $selected_status === 'pending' ? 'selected' : '' ?>>Pending Moderation</option>
          <option value="rejected" <?= $selected_status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
        </select>
      </div>

      <!-- Product Filter -->
      <div class="table-product-filter-wrap" style="max-width: 250px;">
        <select name="product_id" class="admin-form-select admin-filter-select" onchange="this.form.submit()">
          <option value="all">All Products</option>
          <?php if (!empty($products)): ?>
            <?php foreach ($products as $pr): ?>
              <option value="<?= $pr['id'] ?>" <?= ((string)$selected_product === (string)$pr['id']) ? 'selected' : '' ?>>
                <?= e(substr($pr['title'] ?? $pr['name'], 0, 32)) ?>...
              </option>
            <?php endforeach; ?>
          <?php endif; ?>
        </select>
      </div>

      <!-- Submit / Filter Button -->
      <button type="submit" class="btn-admin btn-admin-secondary" style="padding: 7px 14px;">Filter</button>
      
      <?php if (!empty($search_query) || $selected_status !== 'all' || $selected_product !== 'all'): ?>
        <a href="<?= url('admin/reviews') ?>" class="btn-admin btn-admin-link" style="color: #ef4444; font-size: 0.85rem; text-decoration: underline;">Reset Filters</a>
      <?php endif; ?>

    </form>
    <div>
      <span class="table-counter-text">Total: <strong><?= count($reviews) ?></strong> Reviews</span>
    </div>
  </div>

  <div class="admin-table-responsive">
    <table class="admin-table" id="reviewsTable">
      <thead>
        <tr>
          <th style="width: 50px;">ID</th>
          <th style="width: 240px;">Product</th>
          <th style="width: 160px;">Customer</th>
          <th style="width: 120px;">Rating</th>
          <th>Review Content</th>
          <th style="width: 110px;">Status</th>
          <th style="width: 110px;">Date</th>
          <th style="width: 150px; text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($reviews)): ?>
          <?php foreach ($reviews as $rev): 
            $ratingVal = (int)($rev['rating'] ?? 5);
            $pImg = !empty($rev['product_image']) ? $rev['product_image'] : 'assets/images/vape-image.webp';
            if (!preg_match('~^(https?://|//|#)~i', $pImg)) {
                $pImg = url($pImg);
            }
            $pUrl = !empty($rev['product_slug']) ? url('product/' . urlencode($rev['product_slug'])) : '#';
            $initials = strtoupper(substr(trim($rev['customer_name'] ?? 'U'), 0, 2));
          ?>
          <tr data-review-id="<?= $rev['id'] ?>">
            <td><strong>#<?= $rev['id'] ?></strong></td>
            
            <!-- Product Info -->
            <td>
              <div style="display: flex; align-items: center; gap: 10px;">
                <img src="<?= htmlspecialchars($pImg) ?>" alt="Product" style="width: 40px; height: 40px; border-radius: 8px; object-fit: contain; background: #f8fafc; border: 1px solid #e2e8f0; flex-shrink: 0;" onerror="this.onerror=null;this.src='<?= url('assets/images/vape-image.webp') ?>';">
                <div style="overflow: hidden;">
                  <a href="<?= htmlspecialchars($pUrl) ?>" target="_blank" style="font-weight: 600; color: #0f172a; font-size: 0.88rem; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;" title="<?= e($rev['product_title']) ?>">
                    <?= e($rev['product_title'] ?? 'Product #' . $rev['product_id']) ?>
                  </a>
                  <?php if (!empty($rev['brand_name'])): ?>
                    <span style="font-size: 0.75rem; color: #64748b;"><?= e($rev['brand_name']) ?></span>
                  <?php endif; ?>
                </div>
              </div>
            </td>

            <!-- Customer -->
            <td>
              <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 30px; height: 30px; border-radius: 50%; background: #e0e7ff; color: #4338ca; font-weight: 700; font-size: 0.75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                  <?= e($initials) ?>
                </div>
                <span style="font-weight: 600; font-size: 0.88rem; color: #1e293b;"><?= e($rev['customer_name']) ?></span>
              </div>
            </td>

            <!-- Rating Stars -->
            <td>
              <div style="color: #f59e0b; font-size: 0.95rem; line-height: 1;" title="<?= $ratingVal ?> out of 5 stars">
                <?= str_repeat('★', $ratingVal) . str_repeat('☆', 5 - $ratingVal) ?>
                <span style="font-size: 0.78rem; font-weight: 700; color: #64748b; margin-left: 4px;"><?= $ratingVal ?>.0</span>
              </div>
            </td>

            <!-- Review Title & Comment -->
            <td>
              <div style="font-weight: 700; color: #0f172a; font-size: 0.9rem; margin-bottom: 2px;">
                <?= e($rev['title']) ?>
              </div>
              <div style="color: #475569; font-size: 0.85rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                <?= e($rev['comment']) ?>
              </div>
            </td>

            <!-- Status Badge -->
            <td>
              <?php if ($rev['status'] === 'approved'): ?>
                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;">
                  <span style="width: 6px; height: 6px; border-radius: 50%; background: #059669;"></span> Approved
                </span>
              <?php elseif ($rev['status'] === 'pending'): ?>
                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; background: #fffbeb; color: #d97706; border: 1px solid #fde68a;">
                  <span style="width: 6px; height: 6px; border-radius: 50%; background: #d97706;"></span> Pending
                </span>
              <?php else: ?>
                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;">
                  <span style="width: 6px; height: 6px; border-radius: 50%; background: #dc2626;"></span> Rejected
                </span>
              <?php endif; ?>
            </td>

            <!-- Date -->
            <td style="color: #64748b; font-size: 0.82rem; white-space: nowrap;">
              <?= date('M d, Y', strtotime($rev['created_at'])) ?>
            </td>

            <!-- Action Buttons -->
            <td style="text-align: right; white-space: nowrap;">
              
              <!-- Quick Status Toggle -->
              <?php if ($rev['status'] !== 'approved'): ?>
                <a href="<?= url('admin/reviews/status?id=' . $rev['id'] . '&status=approved') ?>" class="btn-icon-only" title="Approve Review" style="color: #10b981;">
                  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </a>
              <?php else: ?>
                <a href="<?= url('admin/reviews/status?id=' . $rev['id'] . '&status=pending') ?>" class="btn-icon-only" title="Mark as Pending" style="color: #f59e0b;">
                  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </a>
              <?php endif; ?>

              <?php if ($rev['status'] !== 'rejected'): ?>
                <a href="<?= url('admin/reviews/status?id=' . $rev['id'] . '&status=rejected') ?>" class="btn-icon-only" title="Reject Review" style="color: #ef4444;">
                  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                </a>
              <?php endif; ?>

              <!-- Edit Button (Opens Modal) -->
              <button type="button" class="btn-icon-only btn-edit-review" 
                      data-id="<?= $rev['id'] ?>"
                      data-product="<?= e($rev['product_title'] ?? '') ?>"
                      data-customer="<?= e($rev['customer_name']) ?>"
                      data-rating="<?= $ratingVal ?>"
                      data-title="<?= e($rev['title']) ?>"
                      data-comment="<?= e($rev['comment']) ?>"
                      data-status="<?= e($rev['status']) ?>"
                      title="Edit Review" style="color: #3b82f6;">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
              </button>

              <!-- Delete Button (POST + CSRF) -->
              <form method="POST" action="<?= url('admin/reviews/delete') ?>" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to permanently delete this review?');">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= $rev['id'] ?>">
                <button type="submit" class="btn-icon-only" title="Delete Review" style="color: #94a3b8; hover: color: #ef4444;">
                  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
              </form>

            </td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="8" style="text-align: center; padding: 48px 24px; color: #64748b;">
              <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="margin-bottom: 12px;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
              <div style="font-size: 1.1rem; font-weight: 700; color: #1e293b; margin-bottom: 4px;">No Reviews Found</div>
              <p style="font-size: 0.9rem; margin: 0;">Try adjusting your filters or click "Add Verified Review" above to create one.</p>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ==========================================
     MODAL 1: ADD VERIFIED REVIEW
     ========================================== -->
<div class="admin-modal-backdrop" id="addReviewModalBackdrop" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 1050; align-items: center; justify-content: center; padding: 20px; backdrop-filter: blur(4px);">
  <div class="admin-modal-card" style="background: #ffffff; border-radius: 16px; width: 100%; max-width: 560px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15); overflow: hidden; animation: modalSlideIn 0.25s ease;">
    
    <div style="padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;">
      <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0;">Add Verified Review</h3>
      <button type="button" class="btn-close-modal" id="btnCloseAddModal" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #64748b;">&times;</button>
    </div>

    <form method="POST" action="<?= url('admin/reviews/store') ?>" style="padding: 24px;">
      <?= csrf_field() ?>
      
      <!-- Select Product -->
      <div class="admin-form-group" style="margin-bottom: 16px;">
        <label class="admin-form-label" style="font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 6px; display: block;">Product <span style="color:#ef4444;">*</span></label>
        <select name="product_id" class="admin-form-select" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
          <option value="" disabled selected>-- Select a Product --</option>
          <?php if (!empty($products)): ?>
            <?php foreach ($products as $pr): ?>
              <option value="<?= $pr['id'] ?>"><?= e($pr['title'] ?? $pr['name']) ?></option>
            <?php endforeach; ?>
          <?php endif; ?>
        </select>
      </div>

      <!-- Customer Name & Rating Row -->
      <div style="display: grid; grid-template-columns: 1fr 140px; gap: 14px; margin-bottom: 16px;">
        <div>
          <label class="admin-form-label" style="font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 6px; display: block;">Customer Name <span style="color:#ef4444;">*</span></label>
          <input type="text" name="customer_name" class="admin-form-input" placeholder="e.g. Michael R." required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
        </div>
        <div>
          <label class="admin-form-label" style="font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 6px; display: block;">Rating</label>
          <select name="rating" class="admin-form-select" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-weight: 700; color: #f59e0b;">
            <option value="5" selected>5 Stars ★★★★★</option>
            <option value="4">4 Stars ★★★★☆</option>
            <option value="3">3 Stars ★★★☆☆</option>
            <option value="2">2 Stars ★★☆☆☆</option>
            <option value="1">1 Star ★☆☆☆☆</option>
          </select>
        </div>
      </div>

      <!-- Review Title -->
      <div class="admin-form-group" style="margin-bottom: 16px;">
        <label class="admin-form-label" style="font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 6px; display: block;">Review Headline / Title <span style="color:#ef4444;">*</span></label>
        <input type="text" name="title" class="admin-form-input" placeholder="e.g. Amazing flavor and lasts forever!" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
      </div>

      <!-- Review Comment -->
      <div class="admin-form-group" style="margin-bottom: 16px;">
        <label class="admin-form-label" style="font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 6px; display: block;">Review Body <span style="color:#ef4444;">*</span></label>
        <textarea name="comment" rows="4" class="admin-form-textarea" placeholder="Detailed feedback from the customer..." required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px;"></textarea>
      </div>

      <!-- Status -->
      <div class="admin-form-group" style="margin-bottom: 24px;">
        <label class="admin-form-label" style="font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 6px; display: block;">Initial Status</label>
        <select name="status" class="admin-form-select" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
          <option value="approved" selected>Approved (Visible immediately on product page)</option>
          <option value="pending">Pending Moderation</option>
          <option value="rejected">Rejected</option>
        </select>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #f1f5f9; padding-top: 16px;">
        <button type="button" class="btn-admin btn-admin-secondary" id="btnCancelAddModal">Cancel</button>
        <button type="submit" class="btn-admin btn-admin-primary">Save Review</button>
      </div>

    </form>
  </div>
</div>

<!-- ==========================================
     MODAL 2: EDIT REVIEW
     ========================================== -->
<div class="admin-modal-backdrop" id="editReviewModalBackdrop" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 1050; align-items: center; justify-content: center; padding: 20px; backdrop-filter: blur(4px);">
  <div class="admin-modal-card" style="background: #ffffff; border-radius: 16px; width: 100%; max-width: 560px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15); overflow: hidden; animation: modalSlideIn 0.25s ease;">
    
    <div style="padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;">
      <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0;">Edit Customer Review</h3>
      <button type="button" class="btn-close-modal" id="btnCloseEditModal" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #64748b;">&times;</button>
    </div>

    <form method="POST" action="<?= url('admin/reviews/update') ?>" style="padding: 24px;">
      <?= csrf_field() ?>
      <input type="hidden" name="id" id="editReviewId">
      
      <div style="margin-bottom: 16px; padding: 10px 14px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
        <span style="font-size: 0.78rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Product:</span>
        <div id="editProductTitle" style="font-weight: 700; color: #0f172a; font-size: 0.92rem; margin-top: 2px;"></div>
      </div>

      <!-- Customer Name & Rating Row -->
      <div style="display: grid; grid-template-columns: 1fr 140px; gap: 14px; margin-bottom: 16px;">
        <div>
          <label class="admin-form-label" style="font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 6px; display: block;">Customer Name <span style="color:#ef4444;">*</span></label>
          <input type="text" name="customer_name" id="editCustomerName" class="admin-form-input" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
        </div>
        <div>
          <label class="admin-form-label" style="font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 6px; display: block;">Rating</label>
          <select name="rating" id="editRating" class="admin-form-select" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-weight: 700; color: #f59e0b;">
            <option value="5">5 Stars ★★★★★</option>
            <option value="4">4 Stars ★★★★☆</option>
            <option value="3">3 Stars ★★★☆☆</option>
            <option value="2">2 Stars ★★☆☆☆</option>
            <option value="1">1 Star ★☆☆☆☆</option>
          </select>
        </div>
      </div>

      <!-- Review Title -->
      <div class="admin-form-group" style="margin-bottom: 16px;">
        <label class="admin-form-label" style="font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 6px; display: block;">Review Headline / Title <span style="color:#ef4444;">*</span></label>
        <input type="text" name="title" id="editTitle" class="admin-form-input" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
      </div>

      <!-- Review Comment -->
      <div class="admin-form-group" style="margin-bottom: 16px;">
        <label class="admin-form-label" style="font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 6px; display: block;">Review Body <span style="color:#ef4444;">*</span></label>
        <textarea name="comment" id="editComment" rows="4" class="admin-form-textarea" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px;"></textarea>
      </div>

      <!-- Status -->
      <div class="admin-form-group" style="margin-bottom: 24px;">
        <label class="admin-form-label" style="font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 6px; display: block;">Status</label>
        <select name="status" id="editStatus" class="admin-form-select" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px;">
          <option value="approved">Approved</option>
          <option value="pending">Pending Moderation</option>
          <option value="rejected">Rejected</option>
        </select>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #f1f5f9; padding-top: 16px;">
        <button type="button" class="btn-admin btn-admin-secondary" id="btnCancelEditModal">Cancel</button>
        <button type="submit" class="btn-admin btn-admin-primary">Update Review</button>
      </div>

    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Modal 1: Add Review
  const addModal = document.getElementById('addReviewModalBackdrop');
  const btnOpenAdd = document.getElementById('btnOpenAddReviewModal');
  const btnCloseAdd = document.getElementById('btnCloseAddModal');
  const btnCancelAdd = document.getElementById('btnCancelAddModal');

  if (btnOpenAdd && addModal) {
    btnOpenAdd.addEventListener('click', () => {
      addModal.style.display = 'flex';
    });
    [btnCloseAdd, btnCancelAdd].forEach((btn) => {
      if (btn) btn.addEventListener('click', () => addModal.style.display = 'none');
    });
    addModal.addEventListener('click', (e) => {
      if (e.target === addModal) addModal.style.display = 'none';
    });
  }

  // Modal 2: Edit Review
  const editModal = document.getElementById('editReviewModalBackdrop');
  const btnCloseEdit = document.getElementById('btnCloseEditModal');
  const btnCancelEdit = document.getElementById('btnCancelEditModal');

  document.querySelectorAll('.btn-edit-review').forEach((btn) => {
    btn.addEventListener('click', () => {
      document.getElementById('editReviewId').value = btn.dataset.id || '';
      document.getElementById('editProductTitle').textContent = btn.dataset.product || 'Product';
      document.getElementById('editCustomerName').value = btn.dataset.customer || '';
      document.getElementById('editRating').value = btn.dataset.rating || '5';
      document.getElementById('editTitle').value = btn.dataset.title || '';
      document.getElementById('editComment').value = btn.dataset.comment || '';
      document.getElementById('editStatus').value = btn.dataset.status || 'approved';

      if (editModal) editModal.style.display = 'flex';
    });
  });

  if (editModal) {
    [btnCloseEdit, btnCancelEdit].forEach((btn) => {
      if (btn) btn.addEventListener('click', () => editModal.style.display = 'none');
    });
    editModal.addEventListener('click', (e) => {
      if (e.target === editModal) editModal.style.display = 'none';
    });
  }
});
</script>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
