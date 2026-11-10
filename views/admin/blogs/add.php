<?php
$page_title = 'Add Blog Post | Vape Store Admin';
$current_admin_page = 'blogs';
include dirname(__DIR__) . '/includes/header.php';
?>

<form action="<?= url('admin/blogs/store') ?>" method="POST" enctype="multipart/form-data" id="blogPostForm">
  <?= csrf_field() ?>
  <!-- Title & Action Bar -->
  <div class="page-title-bar">
    <div class="page-heading-group" style="display: flex; align-items: center; gap: 14px;">
      <a href="<?= url('admin/blogs') ?>" class="btn-action-icon" title="Back to blogs">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="19" y1="12" x2="5" y2="12"></line>
          <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
      </a>
      <div>
        <h1 style="margin: 0;">Add Blog Post</h1>
        <p class="page-subtitle" style="margin: 0;">Write and publish a new vape guide, device review, or news article.</p>
      </div>
    </div>
    <div class="page-actions-group">
      <a href="<?= url('admin/blogs') ?>" class="btn-admin btn-admin-outline">Discard</a>
      <button type="submit" class="btn-admin btn-admin-primary">Save Blog Post</button>
    </div>
  </div>

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

  <!-- Two-Column Form Layout -->
  <div class="admin-form-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
    
    <!-- LEFT MAIN COLUMN -->
    <div class="form-main-col" style="display: flex; flex-direction: column; gap: 24px;">
      
      <!-- Card: Blog Title -->
      <div class="admin-card">
        <div class="admin-card-body">
          <div class="admin-form-group mb-0">
            <label class="admin-form-label" style="font-weight: 700; font-size: 14px;">Blog Title <span style="color:#ef4444;">*</span></label>
            <input 
              type="text" 
              name="title" 
              id="blogTitleInput" 
              class="admin-form-input" 
              placeholder="e.g. Can You Refill a Foger Vape? Everything Explained" 
              required
              style="font-size: 16px; padding: 12px 14px;"
            >
          </div>
        </div>
      </div>

      <!-- Card: Modular Article Sections Builder -->
      <div class="admin-card blog-sections-card" id="blogSectionsCard">
        <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
          <div>
            <h2 class="admin-card-title">Article Content &amp; Sections</h2>
            <p class="form-help-text">Build your blog with modular sections. Add rich text write-ups or inline product spotlights in any order.</p>
          </div>
          <div class="section-top-actions" style="display: flex; gap: 8px;">
            <button type="button" class="btn-admin btn-admin-secondary btn-admin-sm btn-add-richtext-top">
              <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
              </svg>
              <span>+ Add Text Block</span>
            </button>
            <button type="button" class="btn-admin btn-admin-secondary btn-admin-sm btn-add-showcase-top">
              <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                <line x1="8" y1="21" x2="16" y2="21"></line>
                <line x1="12" y1="17" x2="12" y2="21"></line>
              </svg>
              <span>+ Add Product Showcase</span>
            </button>
          </div>
        </div>
        <div class="admin-card-body" style="padding: 16px 20px;">
          <!-- Sections List Container -->
          <div id="blogSectionsContainer" class="blog-sections-container">
            <!-- Dynamic section items inserted here by JS -->
          </div>

          <!-- Bottom Add Section Banner -->
          <div class="blog-add-section-toolbar">
            <span class="add-section-prompt">Choose next block to add:</span>
            <div class="add-section-buttons">
              <button type="button" class="btn-add-section-card btn-add-richtext">
                <div class="add-sec-icon text-icon">
                  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                </div>
                <div class="add-sec-info">
                  <strong>+ Rich Text Content</strong>
                  <span>Headings, paragraphs, bold, lists &amp; links</span>
                </div>
              </button>
              <button type="button" class="btn-add-section-card btn-add-showcase">
                <div class="add-sec-icon prod-icon">
                  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
                <div class="add-sec-info">
                  <strong>+ Product Showcase</strong>
                  <span>Spotlight (1 product) or Comparison (Max 2)</span>
                </div>
              </button>
            </div>
          </div>

          <!-- Hidden master compiled content textarea -->
          <textarea name="content" id="blogContentInput" style="display:none;"></textarea>
        </div>
      </div>

      <!-- Card: Search Engine Listing (SEO) -->
      <div class="admin-card">
        <div class="admin-card-header d-flex justify-between align-center">
          <div>
            <h2 class="admin-card-title">Search Engine Listing (SEO)</h2>
            <p class="form-help-text">Optimize title, meta description, and URL handle for Google search ranking.</p>
          </div>
          <button type="button" id="toggleSeoBtn" class="btn-admin btn-admin-secondary btn-admin-sm">
            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
              <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span>Edit website SEO</span>
          </button>
        </div>
        <div class="admin-card-body">
          
          <!-- SEO Editable Inputs -->
          <div id="seoFieldsContainer" class="seo-fields-container active">
            
            <div class="admin-form-group">
              <label for="seoTitleInput" class="admin-form-label">SEO Meta Title</label>
              <input type="text" id="seoTitleInput" name="seo_title" class="admin-form-input" placeholder="Enter custom SEO title" maxlength="70">
              <div class="char-counter"><span id="seoTitleCount">0</span> of 70 characters used</div>
            </div>

            <div class="admin-form-group">
              <label for="seoDescInput" class="admin-form-label">SEO Meta Description</label>
              <textarea id="seoDescInput" name="seo_description" class="admin-form-textarea" rows="3" placeholder="Enter meta description for search engines..." maxlength="320"></textarea>
              <div class="char-counter"><span id="seoDescCount">0</span> of 320 characters used</div>
            </div>

            <div class="admin-form-group mb-0">
              <label for="blogSlugInput" class="admin-form-label">URL Handle / Slug</label>
              <div class="input-prefix-container">
                <span class="prefix">.../</span>
                <input type="text" id="blogSlugInput" name="slug" placeholder="can-you-refill-foger-vape">
              </div>
              <p class="form-help-text mt-xs">Auto-generated from title. Unique lowercase letters, numbers, and hyphens only.</p>
            </div>

          </div>

          <!-- Live Google Search Preview Card -->
          <div class="seo-preview-box">
            <div class="seo-preview-header">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
              <span>Infinity Vape Shop</span>
            </div>
            <div class="seo-preview-url" id="previewUrl">www.infinityvapeshop.com/<span id="previewSlug">post-handle</span></div>
            <div class="seo-preview-title" id="previewTitle">Article Title</div>
            <div class="seo-preview-desc" id="previewDesc">Write a concise meta description to see live snippet preview here.</div>
          </div>

        </div>
      </div>

    </div>

    <!-- RIGHT SIDEBAR COLUMN -->
    <div class="form-sidebar-col" style="display: flex; flex-direction: column; gap: 20px;">
      
      <!-- Visibility Card -->
      <div class="admin-card" style="padding: 20px;">
        <h4 style="font-size: 14px; font-weight: 700; margin: 0 0 12px 0;">Publishing Visibility</h4>
        <select name="status" class="admin-form-select" style="width: 100%;">
          <option value="active" selected>Visible (Published)</option>
          <option value="hidden">Hidden (Draft)</option>
        </select>
        <small style="color: #94a3b8; font-size: 12px; margin-top: 6px; display: block;">Visible articles appear on the storefront and blog roll.</small>
      </div>

      <!-- Category Card -->
      <div class="admin-card" style="padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
          <h4 style="font-size: 14px; font-weight: 700; margin: 0;">Blog Categories</h4>
          <a href="<?= url('admin/blog-categories') ?>" target="_blank" style="font-size: 12px; color: #5c56e8; text-decoration: none; font-weight: 600;">+ Manage</a>
        </div>
        <p style="color: #64748b; font-size: 12px; margin: 0 0 10px 0;">Select one or multiple categories for this article:</p>
        <div class="category-checkbox-list" style="display: flex; flex-direction: column; gap: 8px; max-height: 220px; overflow-y: auto; padding-right: 4px;">
          <?php foreach ($categories as $cat): ?>
            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #334155; cursor: pointer; user-select: none;">
              <input type="checkbox" name="category_ids[]" value="<?= $cat['id'] ?>" style="width: 16px; height: 16px; accent-color: #5c56e8; cursor: pointer;">
              <span><?= e($cat['name']) ?></span>
            </label>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Featured Image Card -->
      <div class="admin-card" style="padding: 20px;">
        <h4 style="font-size: 14px; font-weight: 700; margin: 0 0 12px 0;">Featured Image</h4>
        <div class="admin-form-group">
          <label class="admin-form-label" style="font-size: 12px; margin-bottom: 6px; display: block;">Select Image File</label>
          <input 
            type="file" 
            name="featured_image" 
            id="featuredImageInput" 
            class="admin-form-input" 
            accept="image/*"
            style="padding: 8px 12px; cursor: pointer;"
          >
        </div>
        <!-- Live Preview Thumbnail -->
        <div style="width: 100%; height: 160px; border-radius: 8px; overflow: hidden; background: #f8fafc; border: 1px solid #e2e8f0; position: relative; margin-top: 10px;">
          <img 
            id="featuredImagePreview" 
            src="<?= url('assets/images/no-image.svg') ?>" 
            data-default-src="<?= url('assets/images/no-image.svg') ?>"
            alt="Preview" 
            style="width: 100%; height: 100%; object-fit: cover;"
            onerror="this.onerror=null;this.src='<?= url('assets/images/no-image.svg') ?>';"
          >
        </div>
        <small style="color: #94a3b8; font-size: 12px; margin-top: 6px; display: block;">Supports JPG, PNG, WEBP, AVIF. Default placeholder will be used if left empty.</small>
      </div>

      <!-- Excerpt Card -->
      <div class="admin-card" style="padding: 20px;">
        <h4 style="font-size: 14px; font-weight: 700; margin: 0 0 12px 0;">Excerpt (Summary)</h4>
        <textarea 
          name="excerpt" 
          id="excerptInput" 
          class="admin-form-textarea" 
          rows="4" 
          placeholder="Brief 1-2 sentence overview for home page and category listings..."
        ></textarea>
        <small style="color: #94a3b8; font-size: 12px; margin-top: 6px; display: block;">This summary appears on the homepage blog cards.</small>
      </div>
    </div>
  </div>

  <!-- Sticky Bottom Save Bar -->
  <div class="admin-sticky-actions-bar" style="position: sticky; bottom: 0; background: #ffffff; border-top: 1px solid #e2e8f0; padding: 14px 28px; margin: 32px -32px -32px -32px; display: flex; justify-content: space-between; align-items: center; z-index: 10; box-shadow: 0 -4px 16px rgba(15, 23, 42, 0.04);">
    <a href="<?= url('admin/blogs') ?>" class="btn-admin btn-admin-outline">Discard</a>
    <button type="submit" class="btn-admin btn-admin-primary" style="padding: 0 24px;">Save Blog Post</button>
  </div>
</form>

<script>
  window.AVAILABLE_PRODUCTS = <?= json_encode($products ?? []) ?>;
  window.APP_URL = "<?= rtrim(url(), '/') ?>";
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
