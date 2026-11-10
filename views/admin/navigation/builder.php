<?php
$page_title = $menu['name'] ?? 'Menu Builder';
$current_admin_page = 'navigation';
$page_script = 'navigation-builder.js';
include dirname(__DIR__) . '/includes/header.php';
?>

<!-- Title & Action Bar -->
<div class="page-title-bar">
  <div class="page-heading-group">
    <div class="d-flex align-center gap-md">
      <a href="<?= url('admin/navigation') ?>" class="btn-icon-only" title="Back to Navigation">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="19" y1="12" x2="5" y2="12"></line>
          <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
      </a>
      <h1 id="pageTitleHeading" style="font-size: 26px; font-weight: 800; color: #0f172a; margin: 0;"><?= htmlspecialchars($menu['name']) ?></h1>
    </div>
  </div>
  <div class="page-actions-group">
    <button type="button" class="btn-admin btn-admin-secondary open-add-item-modal-btn" title="Add new menu item">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
      </svg>
      <span>Add Item</span>
    </button>
    <button type="button" class="btn-purple-primary" id="saveMenuBtn">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
        <polyline points="17 21 17 13 7 13 7 21"></polyline>
        <polyline points="7 3 7 8 15 8"></polyline>
      </svg>
      <span>Save Menu</span>
    </button>
  </div>
</div>

<!-- Builder Grid Layout -->
<div class="menu-builder-grid">
  
  <!-- Left Side: Menu Items Tree Card -->
  <div class="menu-builder-card menu-items-column-card">
    <div class="menu-builder-header d-flex align-center justify-between" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px;">
      <div>
        <h2>Menu Items</h2>
        <p>Drag and drop items to reorder or nest them. You can add up to 3 levels of nesting.</p>
      </div>
      <button type="button" class="btn-purple-primary open-add-item-modal-btn" style="flex-shrink: 0; padding: 7px 14px; font-size: 13px;" title="Add new menu item">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>+ Add item</span>
      </button>
    </div>

    <!-- Tree Items Container (Rendered by JS) -->
    <div id="menuItemsTree" class="menu-tree-wrap">
      <!-- Items dynamically populated by navigation-builder.js -->
    </div>

    <!-- Sticky Add Item Trigger at the Bottom -->
    <div class="sticky-add-item-bar">
      <button type="button" class="btn-add-item-trigger open-add-item-modal-btn" id="openAddItemModalBtn">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>+ Add menu item</span>
      </button>
    </div>
  </div>

  <!-- Right Side: Menu Settings Card -->
  <div class="menu-builder-card menu-settings-box">
    <div class="menu-builder-header">
      <h2>Menu Settings</h2>
    </div>

    <div class="form-group">
      <label class="admin-form-label font-medium" for="menuTitleInput">Title</label>
      <input type="text" id="menuTitleInput" class="admin-form-input" value="<?= htmlspecialchars($menu['name']) ?>" placeholder="e.g. Main menu">
    </div>

    <div class="form-group">
      <label class="admin-form-label font-medium" for="menuHandleInput">Handle / Location</label>
      <input type="text" id="menuHandleInput" class="admin-form-input" value="<?= htmlspecialchars($menu['location']) ?>" placeholder="e.g. main_menu">
      <p class="menu-settings-help">The location handle is used in the theme code to display this menu.</p>
    </div>

    <input type="hidden" id="menuIdInput" value="<?= (int)$menu['id'] ?>">
    <input type="hidden" id="menuSubtitleInput" value="<?= htmlspecialchars($menu['subtitle'] ?? 'Secondary Menu') ?>">
  </div>

</div>

<!-- Add / Edit Menu Item Modal (Screenshots 3 & 4) -->
<div class="menu-modal-overlay" id="menuItemModal">
  <div class="menu-modal-content">
    <div class="menu-modal-header">
      <h3 class="menu-modal-title" id="itemModalTitle">Add Menu Item</h3>
      <button type="button" class="menu-modal-close-btn" id="closeItemModalBtn">&times;</button>
    </div>
    
    <form id="menuItemForm" onsubmit="return false;">
      <input type="hidden" id="editingItemId" value="">

      <div class="menu-modal-body">
        
        <!-- Name Field -->
        <div class="admin-form-group">
          <label class="admin-form-label font-medium" for="itemNameInput">Name</label>
          <input type="text" id="itemNameInput" class="admin-form-input" placeholder="e.g. Disposables" required autofocus>
        </div>

        <!-- Link Type Dropdown (7 exact options from Screenshot 4) -->
        <div class="admin-form-group">
          <label class="admin-form-label font-medium" for="itemLinkTypeSelect">Link Type</label>
          <select id="itemLinkTypeSelect" class="admin-form-select">
            <option value="collection">Collection</option>
            <option value="brand">Brand</option>
            <option value="blog_category">Blog Category</option>
            <option value="page">Page</option>
            <option value="custom_url" selected>Custom URL</option>
            <option value="text_block">No Link (Text Only)</option>
            <option value="mega_menu_column">Mega Menu Heading (Column)</option>
            <option value="promo_banner">Promo Banner</option>
          </select>
        </div>

        <!-- Dynamic Target Selectors -->
        <!-- 1. Custom URL Input -->
        <div class="admin-form-group" id="groupCustomUrl">
          <label class="admin-form-label font-medium" for="itemCustomUrlInput">Link URL</label>
          <input type="text" id="itemCustomUrlInput" class="admin-form-input" placeholder="Type to search or paste URL" value="/shop">
        </div>

        <!-- 2. Collection Select -->
        <div class="admin-form-group" id="groupCollectionSelect" style="display: none;">
          <label class="admin-form-label font-medium" for="itemCollectionSelect">Select Collection</label>
          <select id="itemCollectionSelect" class="admin-form-select">
            <option value="">-- Choose Collection --</option>
            <?php foreach ($entities['collections'] as $col): ?>
              <option value="<?= (int)$col['id'] ?>" data-slug="<?= htmlspecialchars($col['slug']) ?>" data-title="<?= htmlspecialchars($col['name']) ?>">
                <?= htmlspecialchars($col['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- 3. Brand Select -->
        <div class="admin-form-group" id="groupBrandSelect" style="display: none;">
          <label class="admin-form-label font-medium" for="itemBrandSelect">Select Brand</label>
          <select id="itemBrandSelect" class="admin-form-select">
            <option value="">-- Choose Brand --</option>
            <?php foreach ($entities['brands'] as $br): ?>
              <option value="<?= (int)$br['id'] ?>" data-slug="<?= htmlspecialchars($br['slug']) ?>" data-title="<?= htmlspecialchars($br['name']) ?>">
                <?= htmlspecialchars($br['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- 4. Blog Category Select -->
        <div class="admin-form-group" id="groupBlogCategorySelect" style="display: none;">
          <label class="admin-form-label font-medium" for="itemBlogCategorySelect">Select Blog Category</label>
          <select id="itemBlogCategorySelect" class="admin-form-select">
            <option value="">-- Choose Blog Category --</option>
            <?php if (!empty($entities['blog_categories'])): ?>
              <?php foreach ($entities['blog_categories'] as $bcat): ?>
                <option value="<?= (int)$bcat['id'] ?>" data-slug="<?= htmlspecialchars($bcat['slug']) ?>" data-title="<?= htmlspecialchars($bcat['name']) ?>">
                  <?= htmlspecialchars($bcat['name']) ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>

        <!-- 5. Page Select -->
        <div class="admin-form-group" id="groupPageSelect" style="display: none;">
          <label class="admin-form-label font-medium" for="itemPageSelect">Select Page</label>
          <select id="itemPageSelect" class="admin-form-select">
            <option value="">-- Choose Page --</option>
            <?php foreach ($entities['pages'] as $pg): ?>
              <option value="<?= (int)$pg['id'] ?>" data-slug="<?= htmlspecialchars($pg['slug']) ?>" data-title="<?= htmlspecialchars($pg['title']) ?>">
                <?= htmlspecialchars($pg['title']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- 5. Promo Banner Fields (Exact match to Screenshot 2) -->
        <div id="groupPromoBanner" style="display: none;">
          <div class="admin-form-group">
            <label class="admin-form-label font-medium" for="itemPromoLinkInput">Image / Promotion Link</label>
            <input type="text" id="itemPromoLinkInput" class="admin-form-input" placeholder="/collection/adjust-vapes">
          </div>

          <div class="admin-form-group">
            <label class="admin-form-label font-medium" for="itemPromoTextInput">Promo Text / Subtitle</label>
            <input type="text" id="itemPromoTextInput" class="admin-form-input" placeholder="Buy 5 for £20 on selected brands">
          </div>

          <div class="admin-form-group">
            <label class="admin-form-label font-medium" for="itemPromoImageUrlInput">Image URL</label>
            <div style="display: flex; gap: 8px;">
              <input type="text" id="itemPromoImageUrlInput" class="admin-form-input" placeholder="http://... or assets/images/banner.webp" style="flex: 1;">
              <label class="btn-admin btn-admin-secondary" style="margin: 0; cursor: pointer; white-space: nowrap; display: inline-flex; align-items: center; gap: 6px;">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                <span>Upload</span>
                <input type="file" id="promoImageFileInput" accept="image/*" style="display: none;">
              </label>
            </div>
            <div id="promoImagePreviewWrap" style="margin-top: 8px; display: none;">
              <img id="promoImagePreview" src="" alt="Promo preview" style="max-height: 80px; border-radius: 6px; border: 1px solid #e2e8f0; object-fit: cover;">
            </div>
          </div>
        </div>

        <!-- Nesting: Parent Item Select -->
        <div class="admin-form-group">
          <label class="admin-form-label font-medium" for="itemParentSelect">Parent Item (Hierarchy)</label>
          <select id="itemParentSelect" class="admin-form-select">
            <option value="">None (Top Level Item)</option>
            <!-- Dynamically populated with existing items for nesting -->
          </select>
          <p class="form-help-text" style="font-size: 12px; color: #64748b; margin-top: 5px; line-height: 1.4;">
            <strong>Note:</strong> To display items inside columns in a Mega Menu (such as Disposables), select a column heading (e.g. <code>↳↳ By Puff Count</code>).
          </p>
        </div>

      </div>

      <div class="menu-modal-footer">
        <button type="button" class="btn-modal-cancel" id="cancelItemModalBtn">Cancel</button>
        <button type="button" class="btn-purple-primary" id="saveItemModalBtn">Add Item</button>
      </div>
    </form>
  </div>
</div>

<!-- Toast Feedback Container -->
<div id="builderToast" class="admin-toast" style="display: none; position: fixed; bottom: 24px; right: 24px; background: #0f172a; color: #fff; padding: 12px 20px; border-radius: 8px; font-weight: 500; font-size: 14px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); z-index: 9999; transition: all 0.3s ease;">
  Menu saved successfully!
</div>

<!-- Initial Data for JavaScript -->
<script>
window.initialMenuItems = <?= json_encode($menu['items'] ?? []) ?>;
window.menuEntities = <?= json_encode($entities ?? []) ?>;
window.saveMenuEndpoint = "<?= url('admin/navigation/save') ?>";
window.deleteItemEndpoint = "<?= url('admin/navigation/delete-item') ?>";
window.uploadBannerEndpoint = "<?= url('admin/navigation/upload-banner') ?>";
</script>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
