<?php
$page_title = 'Blog Categories | Vape Store Admin';
$current_admin_page = 'blog-categories';
include dirname(__DIR__) . '/includes/header.php';
?>

<!-- Title & Action Bar -->
<div class="page-title-bar">
  <div class="page-heading-group">
    <h1>Blog Categories</h1>
    <p class="page-subtitle">Organize and segment your articles, device tutorials, and reviews.</p>
  </div>
  <div class="page-actions-group">
    <button type="button" class="btn-admin btn-admin-primary" onclick="openCategoryModal()">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
      </svg>
      <span>Add Category</span>
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

<!-- Categories Table Card -->
<div class="admin-card" style="padding: 0; overflow: hidden;">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Category Name</th>
          <th>Slug</th>
          <th>Description</th>
          <th style="text-align: center;">Posts Count</th>
          <th style="text-align: right;">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($categories)): ?>
          <?php foreach ($categories as $cat): ?>
          <tr>
            <td style="font-weight: 700; color: #0f172a;">
              <div style="display: flex; align-items: center; gap: 8px;">
                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #5c56e8;"></span>
                <?= e($cat['name']) ?>
              </div>
            </td>
            <td><code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 12px; color: #475569;"><?= e($cat['slug']) ?></code></td>
            <td style="color: #64748b; font-size: 13.5px; max-width: 300px;"><?= e($cat['description'] ?? 'No description provided.') ?></td>
            <td style="text-align: center;">
              <span class="badge-status-active" style="display: inline-block; padding: 3px 10px; border-radius: 9999px; font-size: 11.5px; font-weight: 700;">
                <?= (int)($cat['post_count'] ?? 0) ?> posts
              </span>
            </td>
            <td style="text-align: right;">
              <div style="display: inline-flex; gap: 6px;">
                <button 
                  type="button" 
                  class="btn-action-icon edit-btn" 
                  title="Edit Category"
                  onclick='openCategoryModal(<?= json_encode($cat) ?>)'
                >
                  <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"></path>
                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                  </svg>
                </button>
                <form action="<?= url('admin/blog-categories/delete') ?>" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this category? Associated posts will be uncategorized.');">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                  <button type="submit" class="btn-action-icon delete-btn" title="Delete Category">
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
            <td colspan="5" style="text-align: center; padding: 50px 20px;">
              <p style="color: #64748b; font-size: 14px; margin-bottom: 12px;">No blog categories found. Create your first category to organize articles.</p>
              <button type="button" class="btn-admin btn-admin-primary" onclick="openCategoryModal()">
                + Add First Category
              </button>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal for Add/Edit Category -->
<div id="categoryModal" class="admin-modal" style="display: none; position: fixed; inset: 0; z-index: 9999; align-items: center; justify-content: center;">
  <div class="admin-modal-backdrop" onclick="closeCategoryModal()" style="position: absolute; inset: 0; background: rgba(15, 23, 42, 0.55); backdrop-filter: blur(4px);"></div>
  <div class="admin-modal-dialog" style="position: relative; background: #ffffff; width: 100%; max-width: 480px; border-radius: 14px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); overflow: hidden; z-index: 1;">
    
    <div style="padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
      <h3 id="modalTitle" style="margin: 0; font-size: 17px; font-weight: 700; color: #0f172a;">Add Category</h3>
      <button type="button" onclick="closeCategoryModal()" style="background: none; border: none; cursor: pointer; color: #94a3b8; padding: 4px;">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
      </button>
    </div>

    <form id="categoryForm" action="<?= url('admin/blog-categories/store') ?>" data-store-url="<?= url('admin/blog-categories/store') ?>" data-update-url="<?= url('admin/blog-categories/update') ?>" method="POST">
      <?= csrf_field() ?>
      <input type="hidden" name="id" id="catId">
      
      <div style="padding: 24px;">
        <div class="admin-form-group">
          <label class="admin-form-label" style="font-weight: 700;">Category Name <span style="color:#ef4444;">*</span></label>
          <input type="text" name="name" id="catName" class="admin-form-input" placeholder="e.g. Vaping Guides" required>
        </div>

        <div class="admin-form-group">
          <label class="admin-form-label">URL Slug</label>
          <input type="text" name="slug" id="catSlug" class="admin-form-input" placeholder="vaping-guides">
          <small style="color: #94a3b8; font-size: 11.5px; margin-top: 4px; display: block;">Leave blank to auto-generate from category name.</small>
        </div>

        <div class="admin-form-group" style="margin-bottom: 0;">
          <label class="admin-form-label">Description</label>
          <textarea name="description" id="catDesc" class="admin-form-textarea" rows="3" placeholder="Short description of articles in this topic..."></textarea>
        </div>
      </div>

      <div style="padding: 16px 24px; background: #f8fafc; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" class="btn-admin btn-admin-outline" onclick="closeCategoryModal()">Cancel</button>
        <button type="submit" id="catSubmitBtn" class="btn-admin btn-admin-primary">Save Category</button>
      </div>
    </form>
  </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
