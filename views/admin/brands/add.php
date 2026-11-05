<?php
$page_title = 'Add New Brand';
$current_admin_page = 'brands';
$page_script = 'brand-form.js';
include dirname(__DIR__) . '/includes/header.php';

$allCollections = $all_collections ?? [];
?>

<!-- Title & Action Bar -->
<div class="page-title-bar">
  <div class="page-heading-group">
    <div class="d-flex align-center gap-md">
      <a href="<?= url('admin/brands') ?>" class="btn-icon-only" title="Back to Brands">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="19" y1="12" x2="5" y2="12"></line>
          <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
      </a>
      <h1>Add Brand</h1>
    </div>
    <p class="page-subtitle">Configure brand identity, storefront banner quick links, SEO content, and FAQs.</p>
  </div>
  <div class="page-actions-group">
    <a href="<?= url('admin/brands') ?>" class="btn-admin btn-admin-secondary">Discard</a>
    <button type="submit" form="brandMainForm" class="btn-admin btn-admin-primary">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="20 6 9 17 4 12"></polyline>
      </svg>
      <span>Save Brand</span>
    </button>
  </div>
</div>

<!-- Flash Notifications -->
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

<?php if (!empty($success)): ?>
<div class="admin-alert admin-alert-success mb-lg">
  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
    <polyline points="22 4 12 14.01 9 11.01"></polyline>
  </svg>
  <span><?= e($success) ?></span>
</div>
<?php endif; ?>

<form id="brandMainForm" action="<?= url('admin/add-brand') ?>" method="POST" enctype="multipart/form-data">
  <?= csrf_field() ?>

  <div class="detail-grid-layout">
    
    <!-- Left Main Column: Brand Cards -->
    <div class="admin-col-main">
      
      <!-- 1. Main Brand Card (Logo on Left + Title & Short Description on Right) -->
      <div class="shopify-collection-card">
        
        <!-- Logo Upload Box -->
        <input type="file" id="brandLogoInput" name="brand_logo" accept="image/*" class="d-none">
        <input type="hidden" name="logo" id="brandLogoUrl" value="">
        <div class="shopify-col-image-box" id="brandImageBox" title="Upload brand logo">
          <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="upload-icon" id="brandUploadIcon">
            <path d="M12 19V5"></path>
            <polyline points="5 12 12 5 19 12"></polyline>
            <path d="M4 21h16"></path>
          </svg>
          <img src="" alt="Brand Logo Preview" class="shopify-col-preview-img d-none" id="brandPreviewImg">
          <button type="button" class="shopify-col-img-remove-btn d-none" id="btnRemoveBrandLogo" title="Remove logo">&times;</button>
        </div>

        <!-- Title & Short Description Form Elements -->
        <div class="shopify-col-main-content">
          <input type="text" id="brandName" name="name" class="shopify-col-title-input" placeholder="e.g. Lost Mary, Geek Bar" required autofocus>
          <textarea id="brandDescription" name="short_description" class="shopify-col-desc-input" placeholder="Add short summary/intro for this brand (appears in header banner)..." rows="3"></textarea>
        </div>

      </div>

      <!-- 2. Header Banner Quick Links Card -->
      <div class="admin-card">
        <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
          <div>
            <h2 class="admin-card-title">Header Banner Quick Links</h2>
            <p class="form-help-text" style="margin-top: 4px;">Select which collections to display as quick navigation pills in the brand header banner.</p>
          </div>
          <div style="display: flex; align-items: center; gap: 8px;">
            <span class="badge badge-secondary" id="selectedHeaderCountBadge" style="font-size: 12px; padding: 4px 10px; border-radius: 12px; background: #edf2f9; color: #082142; font-weight: 600;">0 selected</span>
            <button type="button" class="btn-admin btn-admin-secondary" id="btnClearAllHeader" style="padding: 5px 10px; font-size: 12px;">Clear All</button>
          </div>
        </div>
        <div class="admin-card-body">
          <!-- Search / Quick Filter Input -->
          <div style="margin-bottom: 14px; position: relative;">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#94a3b8" stroke-width="2" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); pointer-events: none;">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" id="searchHeaderCollectionsInput" class="admin-form-input" placeholder="Search collections for header banner..." style="padding-left: 36px; height: 38px;">
          </div>

          <!-- Scrollable Collections Grid -->
          <div id="headerCollectionsGrid" style="max-height: 280px; overflow-y: auto; border: 1px solid var(--border-color, #e2e8f0); border-radius: 8px; padding: 10px; background: #fafbfc; display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 8px;">
            <?php if (!empty($allCollections)): ?>
              <?php foreach ($allCollections as $availCol): 
                $availId = (int)$availCol['id'];
                $availImg = !empty($availCol['header_image_url']) ? asset($availCol['header_image_url']) : '';
                $pCount = (int)($availCol['products_count'] ?? 0);
              ?>
                <label class="header-col-checkbox-card" style="display: flex; align-items: center; gap: 10px; padding: 9px 12px; background: #ffffff; border: 1px solid var(--border-color, #e2e8f0); border-radius: 6px; cursor: pointer; transition: all 0.15s ease; user-select: none;">
                  <input type="checkbox" name="header_collections[]" value="<?= $availId ?>" class="header-col-checkbox" style="width: 16px; height: 16px; accent-color: #082142; cursor: pointer;">
                  <div style="width: 32px; height: 32px; border-radius: 4px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; border: 1px solid #e2e8f0;">
                    <?php if ($availImg): ?>
                      <img src="<?= $availImg ?>" alt="<?= htmlspecialchars($availCol['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#94a3b8" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    <?php endif; ?>
                  </div>
                  <div style="flex: 1; min-width: 0;">
                    <div class="header-col-title" style="font-size: 13px; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($availCol['name']) ?></div>
                    <div style="font-size: 11.5px; color: #64748b;"><?= $pCount ?> product<?= $pCount === 1 ? '' : 's' ?></div>
                  </div>
                </label>
              <?php endforeach; ?>
            <?php else: ?>
              <p style="grid-column: 1 / -1; padding: 20px; text-align: center; color: #94a3b8; font-size: 13px;">No collections found.</p>
            <?php endif; ?>
          </div>
          <p class="form-help-text mt-xs">Checkmarked collections will be shown as top pill navigation links on this brand's storefront page.</p>
        </div>
      </div>

      <!-- 3. Brand Long Description Card (Rich Text Editor + Read More) -->
      <div class="admin-card">
        <div class="admin-card-header">
          <div>
            <h2 class="admin-card-title">Brand Long Description</h2>
            <p class="form-help-text" style="margin-top: 4px;">Comprehensive SEO content placed right above the FAQs. On the storefront, the first few lines will be displayed with a smooth "Read More" button to expand.</p>
          </div>
        </div>
        <div class="admin-card-body">
          <div class="admin-form-group mb-0">
            <label class="admin-form-label">Long Description / SEO Article</label>
            <div class="rich-text-editor">
              <div class="rte-toolbar">
                <select class="rte-format-select" title="Heading Size / Paragraph">
                  <option value="p">Paragraph</option>
                  <option value="h1">Heading 1 (H1)</option>
                  <option value="h2">Heading 2 (H2)</option>
                  <option value="h3">Heading 3 (H3)</option>
                  <option value="h4">Heading 4 (H4)</option>
                  <option value="h5">Heading 5 (H5)</option>
                  <option value="h6">Heading 6 (H6)</option>
                </select>

                <div class="rte-divider"></div>

                <button type="button" data-command="bold" title="Bold"><strong>B</strong></button>
                <button type="button" data-command="italic" title="Italic"><em>I</em></button>
                <button type="button" data-command="underline" title="Underline"><u>U</u></button>
                <button type="button" data-command="highlight" title="Highlight Text" class="rte-btn-highlight"><span class="highlight-swatch"></span> Highlight</button>

                <div class="rte-divider"></div>

                <button type="button" data-command="insertUnorderedList" title="Bulleted List">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                </button>
                <button type="button" data-command="insertOrderedList" title="Numbered List">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><line x1="10" y1="6" x2="21" y2="6"></line><line x1="10" y1="12" x2="21" y2="12"></line><line x1="10" y1="18" x2="21" y2="18"></line><path d="M4 6h1v4"></path><path d="M4 10h2"></path><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"></path></svg>
                </button>

                <div class="rte-divider"></div>

                <button type="button" data-command="createLink" title="Insert Link">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                </button>
                <button type="button" data-command="unlink" title="Remove Link">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><line x1="18.36" y1="5.64" x2="5.64" y2="18.36"></line><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                </button>
                <button type="button" data-command="removeFormat" title="Clear Formatting">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
              </div>

              <div class="rte-editor-content" contenteditable="true" data-placeholder="Write rich SEO article content for this brand..."></div>
              <textarea name="long_description" class="d-none"></textarea>
            </div>
            <p class="form-help-text mt-xs">Use clear headings (H2, H3) and bullet points to maximize Google organic search rankings.</p>
          </div>
        </div>
      </div>

      <!-- 4. Image Banner Section (Promo Banner) -->
      <div class="admin-card">
        <div class="admin-card-header">
          <div>
            <h2 class="admin-card-title">Promotional Image Banner Section</h2>
            <p class="form-help-text" style="margin-top: 4px;">Optional promotional graphic or product bundle banner displayed across this brand page.</p>
          </div>
        </div>
        <div class="admin-card-body">
          <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
            <div class="admin-form-group">
              <label class="admin-form-label">Banner Image</label>
              <input type="file" id="promoBannerFileInput" name="promo_banner_file" accept="image/*" class="d-none">
              <input type="hidden" name="promo_banner_image" id="promoBannerImageUrl" value="">
              <div class="media-upload-area" id="promoBannerDropzone" style="padding: 20px; cursor: pointer;">
                <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="#94a3b8" stroke-width="2" style="margin-bottom: 6px;">
                  <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                  <circle cx="8.5" cy="8.5" r="1.5"></circle>
                  <polyline points="21 15 16 10 5 21"></polyline>
                </svg>
                <div class="media-dropzone-title" style="font-size: 13.5px;">Click to upload promotional banner</div>
                <p class="form-help-text">Recommended size: 1400 x 450px or 1200 x 400px (WebP, PNG, JPG)</p>
              </div>
              <div id="promoBannerPreviewBox" style="display: none; margin-top: 10px; position: relative;">
                <img src="" id="promoBannerPreviewImg" alt="Banner Preview" style="width: 100%; max-height: 180px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0;">
                <button type="button" id="btnRemovePromoBanner" style="position: absolute; top: 8px; right: 8px; background: rgba(15,23,42,0.75); color: #fff; border: none; border-radius: 50%; width: 28px; height: 28px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px;">&times;</button>
              </div>
            </div>

            <div class="admin-form-group mb-0">
              <label for="promoBannerLink" class="admin-form-label">Banner Click Link (Optional)</label>
              <input type="text" id="promoBannerLink" name="promo_banner_link" class="admin-form-input" placeholder="e.g. /disposable-vapes/15000-puff-disposable-vape or https://...">
              <p class="form-help-text mt-xs">When customers click the promotional banner, they will be navigated to this destination.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- 5. Frequently Asked Questions (FAQs Builder) -->
      <div class="admin-card">
        <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center;">
          <div>
            <h2 class="admin-card-title">Frequently Asked Questions</h2>
            <p class="form-help-text" style="margin-top: 4px;">Add helpful FAQs to address customer questions and boost search engine SEO rankings on this brand page.</p>
          </div>
          <button type="button" class="btn-admin btn-admin-secondary" id="btnAddFaqRow" style="padding: 7px 14px; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Add FAQ</span>
          </button>
        </div>
        <div class="admin-card-body">
          <div id="colFaqsContainer"></div>

          <!-- Empty State (Shown when no FAQs exist) -->
          <div id="emptyFaqsNotice" style="display: none; padding: 32px 20px; text-align: center; border: 1px dashed var(--border-color, #e2e8f0); border-radius: 8px; background: #fafbfc;">
            <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="#94a3b8" stroke-width="1.8" style="margin-bottom: 8px;">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
              <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
            <p style="font-weight: 600; color: #475569; margin: 0 0 4px 0; font-size: 14px;">No FAQs added yet</p>
            <p style="font-size: 13px; color: #94a3b8; margin: 0 0 12px 0;">Click "Add FAQ" above to add the first question and answer for this brand.</p>
          </div>
        </div>
      </div>

      <!-- 6. Explore Related Collections Card -->
      <div class="admin-card">
        <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
          <div>
            <h2 class="admin-card-title">Explore Related Collections</h2>
            <p class="form-help-text" style="margin-top: 4px;">Select which collections should be recommended under the Explore Related Collections section on this brand page.</p>
          </div>
          <div style="display: flex; align-items: center; gap: 8px;">
            <span class="badge badge-secondary" id="selectedRelCountBadge" style="font-size: 12px; padding: 4px 10px; border-radius: 12px; background: #edf2f9; color: #082142; font-weight: 600;">0 selected</span>
            <button type="button" class="btn-admin btn-admin-secondary" id="btnClearAllRel" style="padding: 5px 10px; font-size: 12px;">Clear All</button>
          </div>
        </div>
        <div class="admin-card-body">
          <!-- Search / Quick Filter Input -->
          <div style="margin-bottom: 14px; position: relative;">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#94a3b8" stroke-width="2" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); pointer-events: none;">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" id="searchRelatedCollectionsInput" class="admin-form-input" placeholder="Search collections to link..." style="padding-left: 36px; height: 38px;">
          </div>

          <!-- Scrollable Collections Grid -->
          <div id="relatedCollectionsGrid" style="max-height: 320px; overflow-y: auto; border: 1px solid var(--border-color, #e2e8f0); border-radius: 8px; padding: 10px; background: #fafbfc; display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 8px;">
            <?php if (!empty($allCollections)): ?>
              <?php foreach ($allCollections as $availCol): 
                $availId = (int)$availCol['id'];
                $availImg = !empty($availCol['header_image_url']) ? asset($availCol['header_image_url']) : '';
                $pCount = (int)($availCol['products_count'] ?? 0);
              ?>
                <label class="related-col-checkbox-card" style="display: flex; align-items: center; gap: 10px; padding: 9px 12px; background: #ffffff; border: 1px solid var(--border-color, #e2e8f0); border-radius: 6px; cursor: pointer; transition: all 0.15s ease; user-select: none;">
                  <input type="checkbox" name="related_collections[]" value="<?= $availId ?>" class="related-col-checkbox" style="width: 16px; height: 16px; accent-color: #082142; cursor: pointer;">
                  <div style="width: 32px; height: 32px; border-radius: 4px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; border: 1px solid #e2e8f0;">
                    <?php if ($availImg): ?>
                      <img src="<?= $availImg ?>" alt="<?= htmlspecialchars($availCol['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#94a3b8" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    <?php endif; ?>
                  </div>
                  <div style="flex: 1; min-width: 0;">
                    <div class="related-col-title" style="font-size: 13px; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($availCol['name']) ?></div>
                    <div style="font-size: 11.5px; color: #64748b;"><?= $pCount ?> product<?= $pCount === 1 ? '' : 's' ?></div>
                  </div>
                </label>
              <?php endforeach; ?>
            <?php else: ?>
              <p style="grid-column: 1 / -1; padding: 20px; text-align: center; color: #94a3b8; font-size: 13px;">No collections found.</p>
            <?php endif; ?>
          </div>
          <p class="form-help-text mt-xs">Checkmarked collections will be prominently featured on this brand's storefront page so visitors can discover complementary product lines.</p>
        </div>
      </div>

      <!-- Additional Details Card (Official Website URL) -->
      <div class="admin-card mb-lg">
        <div class="admin-card-header">
          <h2 class="admin-card-title">Brand Info &amp; External Links</h2>
        </div>
        <div class="admin-card-body">
          <div class="admin-form-group mb-0">
            <label for="brandWebsite" class="admin-form-label">Official Website URL (Optional)</label>
            <input type="url" id="brandWebsite" name="website_url" class="admin-form-input" placeholder="https://brand-domain.com">
            <p class="form-help-text mt-xs">Optional link to the manufacturer's verified official portal.</p>
          </div>
        </div>
      </div>

      <!-- 7. Search Engine Listing (SEO) Card -->
      <div class="admin-card">
        <div class="admin-card-header d-flex justify-between align-center">
          <div>
            <h2 class="admin-card-title">Search Engine Listing (SEO)</h2>
            <p class="form-help-text">Optimize brand meta title, description, and handle for Google search ranking.</p>
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
              <label for="slugInput" class="admin-form-label">URL Handle / Slug</label>
              <div class="input-prefix-container">
                <span class="prefix" id="urlPrefixSpan" data-base-url="<?= rtrim(url(), '/') ?>/brand/"><?= rtrim(url(), '/') ?>/brand/</span>
                <input type="text" id="slugInput" name="slug" placeholder="e.g. lost-mary">
              </div>
            </div>

          </div>

          <!-- Live Google Search Preview Card -->
          <div class="seo-preview-box">
            <div class="seo-preview-header">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
              <span>New Vape Official Store</span>
            </div>
            <div class="seo-preview-url" id="seoPreviewUrl"><?= rtrim(url(), '/') ?>/brand/handle</div>
            <div class="seo-preview-title" id="seoPreviewTitle">Brand Name</div>
            <div class="seo-preview-desc" id="seoPreviewDesc">Brand description will appear in search results...</div>
          </div>

        </div>
      </div>

    </div>

    <!-- Right Sidebar Column: Brand Status & Sort Order -->
    <div class="admin-col-side">
      
      <!-- Status Card -->
      <div class="admin-card">
        <div class="admin-card-header">
          <h2 class="admin-card-title">Status</h2>
        </div>
        <div class="admin-card-body">
          <div class="admin-form-group mb-0">
            <select id="brandStatus" name="status" class="admin-form-select">
              <option value="active" selected>Active</option>
              <option value="draft">Draft</option>
            </select>
          </div>
          <p class="form-help-text mt-sm">Active brands are published and visible on storefront navigation, pages, and filters.</p>
        </div>
      </div>

      <!-- Display Order Card -->
      <div class="admin-card">
        <div class="admin-card-header">
          <h2 class="admin-card-title">Display Order</h2>
        </div>
        <div class="admin-card-body">
          <div class="admin-form-group mb-0">
            <input type="number" id="brandSortOrder" name="sort_order" class="admin-form-input" value="0" min="0" step="1">
          </div>
          <p class="form-help-text mt-sm">Lower numbers appear first in the brand list and storefront navigation.</p>
        </div>
      </div>

    </div>

  </div>

  <!-- Bottom Actions Bar -->
  <div class="form-actions-bar">
    <div class="d-flex align-center gap-md">
      <a href="<?= url('admin/brands') ?>" class="btn-admin btn-admin-secondary">Discard</a>
      <button type="submit" class="btn-admin btn-admin-primary">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
        <span>Save Brand</span>
      </button>
    </div>
  </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // FAQs builder
  const container = document.getElementById('colFaqsContainer');
  const addBtn = document.getElementById('btnAddFaqRow');
  const emptyNotice = document.getElementById('emptyFaqsNotice');
  let faqCount = 0;

  function updateFaqBadges() {
    if (!container) return;
    const items = container.querySelectorAll('.col-faq-item-card');
    if (items.length === 0) {
      if (emptyNotice) emptyNotice.style.display = 'block';
    } else {
      if (emptyNotice) emptyNotice.style.display = 'none';
      items.forEach((item, idx) => {
        const badge = item.querySelector('.faq-row-badge');
        if (badge) badge.textContent = 'FAQ #' + (idx + 1);
      });
    }
  }

  function addFaqRow(question = '', answer = '') {
    if (!container) return;
    const index = faqCount++;
    const row = document.createElement('div');
    row.className = 'col-faq-item-card';
    row.style.cssText = 'border: 1px solid var(--border-color, #e2e8f0); border-radius: 8px; padding: 16px; background: #fff; margin-bottom: 14px; position: relative; box-shadow: 0 1px 3px rgba(0,0,0,0.02);';
    row.innerHTML = `
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <span class="faq-row-badge" style="font-size: 12px; font-weight: 700; color: var(--color-primary-navy, #082142); background: #f1f5f9; padding: 3px 8px; border-radius: 4px;">FAQ #${container.children.length + 1}</span>
        <button type="button" class="btn-remove-faq" title="Delete question" style="border: none; background: none; color: #ef4444; cursor: pointer; display: flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 600;">
          <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
          Remove
        </button>
      </div>
      <div class="admin-form-group" style="margin-bottom: 10px;">
        <label class="admin-form-label" style="font-size: 13px;">Question</label>
        <input type="text" name="faqs[${index}][question]" class="admin-form-input" placeholder="e.g. Are your products 100% authentic?" value="${question.replace(/"/g, '&quot;')}" required>
      </div>
      <div class="admin-form-group" style="margin-bottom: 0;">
        <label class="admin-form-label" style="font-size: 13px;">Answer</label>
        <textarea name="faqs[${index}][answer]" class="admin-form-textarea" rows="2" placeholder="e.g. Yes, all devices come with verification codes..." required>${answer}</textarea>
      </div>
    `;

    row.querySelector('.btn-remove-faq').addEventListener('click', function() {
      row.remove();
      updateFaqBadges();
    });

    container.appendChild(row);
    updateFaqBadges();
  }

  if (addBtn) {
    addBtn.addEventListener('click', function(e) {
      e.preventDefault();
      addFaqRow('', '');
    });
  }

  updateFaqBadges();

  // Promotional Banner Upload
  const promoDropzone = document.getElementById('promoBannerDropzone');
  const promoFileInput = document.getElementById('promoBannerFileInput');
  const promoPreviewBox = document.getElementById('promoBannerPreviewBox');
  const promoPreviewImg = document.getElementById('promoBannerPreviewImg');
  const btnRemovePromo = document.getElementById('btnRemovePromoBanner');
  const promoUrlInput = document.getElementById('promoBannerImageUrl');

  if (promoDropzone && promoFileInput) {
    promoDropzone.addEventListener('click', function() {
      promoFileInput.click();
    });
  }

  if (promoFileInput) {
    promoFileInput.addEventListener('change', function() {
      if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          if (promoPreviewImg) promoPreviewImg.src = e.target.result;
          if (promoPreviewBox) promoPreviewBox.style.display = 'block';
          if (promoDropzone) promoDropzone.style.display = 'none';
        };
        reader.readAsDataURL(this.files[0]);
      }
    });
  }

  if (btnRemovePromo) {
    btnRemovePromo.addEventListener('click', function(e) {
      e.preventDefault();
      if (promoFileInput) promoFileInput.value = '';
      if (promoUrlInput) promoUrlInput.value = '';
      if (promoPreviewImg) promoPreviewImg.src = '';
      if (promoPreviewBox) promoPreviewBox.style.display = 'none';
      if (promoDropzone) promoDropzone.style.display = 'block';
    });
  }
});
</script>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
