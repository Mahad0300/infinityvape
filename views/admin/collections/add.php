<?php
$page_title = 'Add New Collection';
$current_admin_page = 'collections';
$page_script = 'add-collection.js';
include dirname(__DIR__) . '/includes/header.php';
?>

<!-- Title & Action Bar -->
<div class="page-title-bar">
  <div class="page-heading-group">
    <div class="d-flex align-center gap-md">
      <a href="<?= url('admin/collections') ?>" class="btn-icon-only" title="Back to Collections">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="19" y1="12" x2="5" y2="12"></line>
          <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
      </a>
      <h1>Add Collection</h1>
    </div>
    <p class="page-subtitle">Create a new collection to organize and showcase products on your storefront.</p>
  </div>
  <div class="page-actions-group">
    <a href="<?= url('admin/collections') ?>" class="btn-admin btn-admin-secondary">Discard</a>
    <button type="submit" form="collectionMainForm" class="btn-admin btn-admin-primary">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="20 6 9 17 4 12"></polyline>
      </svg>
      <span>Save Collection</span>
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

<form id="collectionMainForm" action="<?= url('admin/add-collection') ?>" method="POST" enctype="multipart/form-data">
  <?= csrf_field() ?>

  <div class="detail-grid-layout">
    
    <!-- Left Main Column: Collection Card & Search Engine Listing -->
    <div class="admin-col-main">
      
      <!-- 1. Shopify-Style Main Collection Card (Image on Left + Title & Description on Right) -->
      <div class="shopify-collection-card">
        
        <!-- Image Upload Box -->
        <input type="file" id="collectionImageInput" name="collection_image" accept="image/*" class="d-none">
        <input type="hidden" name="image" id="collectionImageUrl" value="">
        <div class="shopify-col-image-box" id="collectionImageBox" title="Upload collection image">
          <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="upload-icon" id="colUploadIcon">
            <path d="M12 19V5"></path>
            <polyline points="5 12 12 5 19 12"></polyline>
            <path d="M4 21h16"></path>
          </svg>
          <img src="" alt="Collection Preview" class="shopify-col-preview-img d-none" id="collectionPreviewImg">
          <button type="button" class="shopify-col-img-remove-btn d-none" id="btnRemoveColImg" title="Remove image">&times;</button>
        </div>

        <!-- Title & Description Form Elements -->
        <div class="shopify-col-main-content">
          <input type="text" id="collectionTitle" name="title" class="shopify-col-title-input" placeholder="e.g. Disposable Vapes" required autofocus>
          <textarea id="collectionDescription" name="description" class="shopify-col-desc-input" placeholder="Add short summary/intro for this collection (appears in header)..." rows="3"></textarea>
        </div>

      </div>

      <!-- 2. Header Banner Quick Links Card -->
      <div class="admin-card">
        <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
          <div>
            <h2 class="admin-card-title">Header Banner Quick Links</h2>
            <p class="form-help-text" style="margin-top: 4px;">Select which collections to display as quick navigation pills in the header banner.</p>
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
          <div id="headerCollectionsGrid" style="max-height: 280px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: 8px; padding: 10px; background: #fafbfc; display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 8px;">
            <?php if (!empty($all_collections)): ?>
              <?php foreach ($all_collections as $availCol): 
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
              <p style="grid-column: 1 / -1; padding: 20px; text-align: center; color: #94a3b8; font-size: 13px;">No other collections found.</p>
            <?php endif; ?>
          </div>
          <p class="form-help-text mt-xs">Checkmarked collections will be shown as top pill navigation links on this collection page.</p>
        </div>
      </div>

      <!-- 3. Collection Long Description Card -->
      <div class="admin-card">
        <div class="admin-card-header">
          <div>
            <h2 class="admin-card-title">Collection Long Description</h2>
            <p class="form-help-text" style="margin-top: 4px;">Comprehensive SEO content placed right above the FAQs. On the storefront, the first 3 lines will be displayed with a smooth "Read More" button to expand.</p>
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

                <button type="button" data-command="insertUnorderedList" title="Bullet List">• Bullets</button>
                <button type="button" data-command="insertOrderedList" title="Numbered List">1. Numbers</button>

                <div class="rte-divider"></div>

                <button type="button" data-command="createLink" title="Insert Link">🔗 Link</button>
                <button type="button" data-command="unlink" title="Remove Link">Unlink</button>
                <button type="button" data-command="removeFormat" title="Clear Formatting">Tx Clear</button>
              </div>
              <div class="rte-editor-content" id="collectionLongDescEditor" contenteditable="true" data-placeholder="Write detailed buying guides, specifications, device reviews, or SEO article content for this collection..." style="min-height: 200px;"></div>
              <textarea name="long_description" id="collectionLongDesc" style="display:none;"></textarea>
            </div>
            <p class="form-help-text mt-xs">Use the toolbar to choose Heading 1 to 6 or Paragraph, Bold, Italic, Underline, Highlight, Bullets, Numbers, and Links.</p>
          </div>
        </div>
      </div>

      <!-- 4. Frequently Asked Questions Card -->
      <div class="admin-card">
        <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center;">
          <div>
            <h2 class="admin-card-title">Frequently Asked Questions</h2>
            <p class="form-help-text" style="margin-top: 4px;">Add helpful FAQs to address customer questions and boost search engine SEO rankings on this collection page.</p>
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
          <div id="emptyFaqsNotice" style="display: none; padding: 32px 20px; text-align: center; border: 1px dashed var(--border-color); border-radius: 8px; background: #fafbfc;">
            <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="#94a3b8" stroke-width="1.8" style="margin-bottom: 8px;">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
              <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
            <p style="font-weight: 600; color: #475569; margin: 0 0 4px 0; font-size: 14px;">No FAQs added yet</p>
            <p style="font-size: 13px; color: #94a3b8; margin: 0 0 12px 0;">Click "Add FAQ" above to add the first question and answer for this collection.</p>
          </div>
        </div>
      </div>

      <!-- 5. Explore Related Collections Card -->
      <div class="admin-card">
        <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
          <div>
            <h2 class="admin-card-title">Explore Related Collections</h2>
            <p class="form-help-text" style="margin-top: 4px;">Select which collections should be recommended under the Explore Related Collections section.</p>
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
          <div id="relatedCollectionsGrid" style="max-height: 320px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: 8px; padding: 10px; background: #fafbfc; display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 8px;">
            <?php if (!empty($all_collections)): ?>
              <?php foreach ($all_collections as $availCol): 
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
              <p style="grid-column: 1 / -1; padding: 20px; text-align: center; color: #94a3b8; font-size: 13px;">No other collections found.</p>
            <?php endif; ?>
          </div>
          <p class="form-help-text mt-xs">Checkmarked collections will be prominently featured on this collection's storefront page so visitors can discover complementary product lines.</p>
        </div>
      </div>

      <!-- 5. Search Engine Listing (SEO) Card -->
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
              <label for="slugInput" class="admin-form-label">URL Handle / Slug</label>
              <div class="input-prefix-container">
                <span class="prefix" id="urlPrefixSpan" data-base-url="<?= rtrim(url(), '/') ?>/"><?= rtrim(url(), '/') ?>/</span>
                <input type="text" id="slugInput" name="slug" placeholder="e.g. 5000-puff-disposable-vape">
              </div>
            </div>

          </div>

          <!-- Live Google Search Preview Card -->
          <div class="seo-preview-box">
            <div class="seo-preview-header">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
              <span>Infinity Vape Official Store</span>
            </div>
            <div class="seo-preview-url" id="seoPreviewUrl"><?= rtrim(url(), '/') ?>/handle</div>
            <div class="seo-preview-title" id="seoPreviewTitle">Collection Title</div>
            <div class="seo-preview-desc" id="seoPreviewDesc">Collection description will appear in search results...</div>
          </div>

        </div>
      </div>

    </div>

    <!-- Right Sidebar Column: Collection Status & Parent Taxonomy -->
    <div class="admin-col-side">
      
      <!-- Status Card -->
      <div class="admin-card">
        <div class="admin-card-header">
          <h2 class="admin-card-title">Status</h2>
        </div>
        <div class="admin-card-body">
          <div class="admin-form-group mb-0">
            <select id="collectionStatus" name="status" class="admin-form-select">
              <option value="active" selected>Active</option>
              <option value="draft">Draft</option>
            </select>
          </div>
          <p class="form-help-text mt-sm">Active collections are published and visible on the storefront.</p>
        </div>
      </div>

      <!-- Parent Category Card -->
      <div class="admin-card">
        <div class="admin-card-header">
          <h2 class="admin-card-title">Parent Category</h2>
        </div>
        <div class="admin-card-body">
          <div class="admin-form-group mb-0">
            <select id="collectionParent" name="parent_id" class="admin-form-select">
              <option value="" data-slug="">None (Top Level Category)</option>
              <?php if (!empty($parents)): ?>
                <?php foreach ($parents as $p): ?>
                  <option value="<?= $p['id'] ?>" data-slug="<?= e($p['slug']) ?>"><?= e($p['name']) ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>
          <p class="form-help-text mt-sm">Nest this collection as a sub-category under an existing parent.</p>
        </div>
      </div>

    </div>

  </div>

  <!-- Bottom Actions Bar -->
  <div class="form-actions-bar">
    <div class="d-flex align-center gap-md">
      <a href="<?= url('admin/collections') ?>" class="btn-admin btn-admin-secondary">Discard</a>
      <button type="submit" class="btn-admin btn-admin-primary">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
        <span>Save Collection</span>
      </button>
    </div>
  </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const container = document.getElementById('colFaqsContainer');
  const addBtn = document.getElementById('btnAddFaqRow');
  const emptyNotice = document.getElementById('emptyFaqsNotice');
  let faqCount = 0;

  function updateFaqBadges() {
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
    const index = faqCount++;
    const row = document.createElement('div');
    row.className = 'col-faq-item-card';
    row.style.cssText = 'border: 1px solid var(--border-color); border-radius: 8px; padding: 16px; background: #fff; margin-bottom: 14px; position: relative; box-shadow: 0 1px 3px rgba(0,0,0,0.02);';
    row.innerHTML = `
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <span class="faq-row-badge" style="font-size: 12px; font-weight: 700; color: var(--color-primary-navy); background: #f1f5f9; padding: 3px 8px; border-radius: 4px;">FAQ #${container.children.length + 1}</span>
        <button type="button" class="btn-remove-faq" title="Delete question" style="border: none; background: none; color: #ef4444; cursor: pointer; display: flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 600;">
          <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
          Remove
        </button>
      </div>
      <div class="admin-form-group" style="margin-bottom: 10px;">
        <label class="admin-form-label" style="font-size: 13px;">Question</label>
        <input type="text" name="faqs[${index}][question]" class="admin-form-input" placeholder="e.g. How many puffs does a disposable vape last?" value="${question.replace(/"/g, '&quot;')}" required>
      </div>
      <div class="admin-form-group" style="margin-bottom: 0;">
        <label class="admin-form-label" style="font-size: 13px;">Answer</label>
        <textarea name="faqs[${index}][answer]" class="admin-form-textarea" rows="2" placeholder="e.g. Puff counts range from 5,000 to 35,000 puffs depending on model..." required>${answer}</textarea>
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

  // Initial check
  updateFaqBadges();
});
</script>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
