<?php
$page_title = 'Edit Collection: ' . ($collection['name'] ?? 'Collection');
$current_admin_page = 'collections';
$page_script = 'edit-collection.js';
include dirname(__DIR__) . '/includes/header.php';

$colId = (int)($collection['id'] ?? 0);
$colName = $collection['name'] ?? '';
$colSlug = $collection['slug'] ?? '';
$colDesc = $collection['short_description'] ?? '';
$colLongDesc = $collection['long_description'] ?? '';
$colImage = $collection['header_image_url'] ?? '';
$colSeoTitle = $collection['meta_title'] ?? ($colName . ' | New Vape');
$colSeoDesc = $collection['meta_desc'] ?? $colDesc;
$colStatus = !empty($collection['is_active']) ? 'active' : 'draft';
$colParentId = $collection['parent_id'] ?? null;
$colFaqs = $faqs ?? [];

$colRelated = $collection['related_collection_ids'] ?? '';
$savedRelatedIds = [];
if (!empty($colRelated)) {
    if (is_array($colRelated)) {
        $savedRelatedIds = array_map('intval', $colRelated);
    } else {
        $dec = json_decode($colRelated, true);
        if (is_array($dec)) {
            $savedRelatedIds = array_map('intval', $dec);
        } else {
            $savedRelatedIds = array_map('intval', explode(',', $colRelated));
        }
    }
}
?>

<style>
.rich-text-editor {
  border: 1px solid var(--border-color, #e2e8f0);
  border-radius: 8px;
  background: #ffffff;
  overflow: hidden;
  box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}
.rich-text-editor:focus-within {
  border-color: #082142;
  box-shadow: 0 0 0 3px rgba(8, 33, 66, 0.12);
}
.rte-toolbar {
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 8px 10px;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
  flex-wrap: wrap;
}
.rte-format-select {
  height: 30px;
  padding: 2px 8px;
  font-size: 13px;
  font-weight: 600;
  color: #1e293b;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 4px;
  cursor: pointer;
  outline: none;
  min-width: 140px;
  transition: all 0.15s ease;
}
.rte-format-select:focus {
  border-color: #082142;
}
.rte-divider {
  width: 1px;
  height: 20px;
  background: #cbd5e1;
  margin: 0 3px;
}
.rte-toolbar button {
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 4px;
  padding: 4px 9px;
  font-size: 12.5px;
  font-weight: 600;
  color: #334155;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 30px;
  height: 30px;
  transition: all 0.15s ease;
}
.rte-toolbar button:hover {
  background: #f1f5f9;
  border-color: #94a3b8;
  color: #0f172a;
}
.rte-toolbar button:active {
  background: #e2e8f0;
}
.highlight-swatch {
  display: inline-block;
  width: 13px;
  height: 13px;
  background: #fde047;
  border: 1px solid #ca8a04;
  border-radius: 2px;
  margin-right: 5px;
}
.rte-editor-content {
  min-height: 200px;
  padding: 14px;
  font-size: 14px;
  line-height: 1.65;
  color: #1e293b;
  outline: none;
}
.rte-editor-content:empty::before {
  content: attr(data-placeholder);
  color: #94a3b8;
  pointer-events: none;
}
.rte-editor-content h1,
.rte-editor-content h2,
.rte-editor-content h3,
.rte-editor-content h4,
.rte-editor-content h5,
.rte-editor-content h6 {
  color: #082142 !important;
}
.rte-editor-content h1 { font-size: 1.5rem; font-weight: 800; margin: 16px 0 8px; }
.rte-editor-content h2 { font-size: 1.35rem; font-weight: 700; margin: 14px 0 6px; }
.rte-editor-content h3 { font-size: 1.18rem; font-weight: 600; margin: 12px 0 5px; }
.rte-editor-content h4 { font-size: 1.05rem; font-weight: 600; margin: 10px 0 4px; }
.rte-editor-content h5 { font-size: 0.95rem; font-weight: 600; margin: 8px 0 3px; }
.rte-editor-content h6 { font-size: 0.88rem; font-weight: 600; margin: 8px 0 3px; text-transform: uppercase; }
.rte-editor-content p,
.rte-editor-content li,
.rte-editor-content div {
  color: #1e293b !important;
}
.rte-editor-content span:not(.rte-highlight):not(mark) {
  color: inherit !important;
}
.rte-editor-content p { margin: 0 0 8px; }
.rte-editor-content ul, .rte-editor-content ol { margin: 0 0 10px 22px; padding-left: 0; }
.rte-editor-content li { margin-bottom: 4px; }
.rte-editor-content a { color: #f25c27 !important; text-decoration: underline; font-weight: 600; }
.rte-editor-content mark, .rte-editor-content .rte-highlight { background-color: #fef08a !important; padding: 1px 3px; border-radius: 2px; color: #082142 !important; }
</style>

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
      <h1>Edit Collection: <?= e($colName) ?></h1>
    </div>
    <p class="page-subtitle">Update collection banner, description, hierarchy, and SEO handle.</p>
  </div>
  <div class="page-actions-group">
    <a href="<?= url('admin/collections') ?>" class="btn-admin btn-admin-secondary">Discard</a>
    <a href="<?= url('category?c=' . urlencode($colSlug)) ?>" target="_blank" class="btn-admin btn-admin-secondary">
      <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
        <polyline points="15 3 21 3 21 9"></polyline>
        <line x1="10" y1="14" x2="21" y2="3"></line>
      </svg>
      <span>View on Store</span>
    </a>
    <button type="submit" form="collectionEditForm" class="btn-admin btn-admin-primary">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="20 6 9 17 4 12"></polyline>
      </svg>
      <span>Save Changes</span>
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

<form id="collectionEditForm" action="<?= url('admin/edit-collection') ?>?id=<?= $colId ?>" method="POST" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= $colId ?>">

  <div class="detail-grid-layout">
    
    <!-- Left Main Column: Collection Card & Search Engine Listing -->
    <div class="admin-col-main">
      
      <!-- 1. Shopify-Style Main Collection Card (Image on Left + Title & Description on Right) -->
      <div class="shopify-collection-card">
        
        <!-- Image Upload Box -->
        <input type="file" id="collectionImageInput" name="collection_image" accept="image/*" class="d-none">
        <input type="hidden" name="image" id="collectionImageUrl" value="<?= e($colImage) ?>">
        <div class="shopify-col-image-box" id="collectionImageBox" title="Change collection image">
          <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="upload-icon <?= !empty($colImage) ? 'd-none' : '' ?>" id="colUploadIcon">
            <path d="M12 19V5"></path>
            <polyline points="5 12 12 5 19 12"></polyline>
            <path d="M4 21h16"></path>
          </svg>
          <img src="<?= !empty($colImage) ? asset($colImage) : '' ?>" alt="<?= e($colName) ?>" class="shopify-col-preview-img <?= empty($colImage) ? 'd-none' : '' ?>" id="collectionPreviewImg">
          <button type="button" class="shopify-col-img-remove-btn <?= empty($colImage) ? 'd-none' : '' ?>" id="btnRemoveColImg" title="Remove image">&times;</button>
        </div>

        <!-- Title & Description Form Elements -->
        <div class="shopify-col-main-content">
          <input type="text" id="collectionTitle" name="title" class="shopify-col-title-input" value="<?= e($colName) ?>" placeholder="Collection title" required autofocus>
          <textarea id="collectionDescription" name="description" class="shopify-col-desc-input" placeholder="Add short summary/intro for this collection (appears in header)..." rows="3"><?= e($colDesc) ?></textarea>
        </div>

      </div>

      <!-- 2. Collection Long Description (Storefront Content & Read More) -->
      <div class="admin-card">
        <div class="admin-card-header">
          <div>
            <h2 class="admin-card-title">Collection Long Description (Storefront Content &amp; Read More)</h2>
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
              <div class="rte-editor-content" id="collectionLongDescEditor" contenteditable="true" data-placeholder="Write detailed buying guides, specifications, device reviews, or SEO article content for this collection..." style="min-height: 200px;"><?= $colLongDesc ?></div>
              <textarea name="long_description" id="collectionLongDesc" style="display:none;"><?= htmlspecialchars($colLongDesc) ?></textarea>
            </div>
            <p class="form-help-text mt-xs">Use the toolbar to choose Heading 1 to 6 or Paragraph, Bold, Italic, Underline, Highlight, Bullets, Numbers, and Links.</p>
          </div>
        </div>
      </div>

      <!-- 3. Collection FAQs Card -->
      <div class="admin-card">
        <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center;">
          <div>
            <h2 class="admin-card-title">Collection FAQs (Frequently Asked Questions)</h2>
            <p class="form-help-text" style="margin-top: 4px;">Add helpful FAQs to address customer questions and boost search engine SEO rankings on this collection page.</p>
          </div>
          <button type="button" class="btn-admin btn-admin-secondary" id="btnAddFaqRow" style="padding: 7px 14px; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>+ Add FAQ</span>
          </button>
        </div>
        <div class="admin-card-body">
          <div id="colFaqsContainer">
            <?php if (!empty($colFaqs)): ?>
              <?php foreach ($colFaqs as $fIndex => $faq): ?>
                <div class="col-faq-item-card" style="border: 1px solid var(--border-color); border-radius: 8px; padding: 16px; background: #fff; margin-bottom: 14px; position: relative; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span class="faq-row-badge" style="font-size: 12px; font-weight: 700; color: var(--color-primary-navy); background: #f1f5f9; padding: 3px 8px; border-radius: 4px;">FAQ #<?= ($fIndex + 1) ?></span>
                    <button type="button" class="btn-remove-faq" title="Delete question" style="border: none; background: none; color: #ef4444; cursor: pointer; display: flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 600;">
                      <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                      Remove
                    </button>
                  </div>
                  <div class="admin-form-group" style="margin-bottom: 10px;">
                    <label class="admin-form-label" style="font-size: 13px;">Question</label>
                    <input type="text" name="faqs[<?= $fIndex ?>][question]" class="admin-form-input" placeholder="e.g. How many puffs does a disposable vape last?" value="<?= htmlspecialchars($faq['question']) ?>" required>
                  </div>
                  <div class="admin-form-group" style="margin-bottom: 0;">
                    <label class="admin-form-label" style="font-size: 13px;">Answer</label>
                    <textarea name="faqs[<?= $fIndex ?>][answer]" class="admin-form-textarea" rows="2" placeholder="e.g. Puff counts range from 5,000 to 35,000 puffs depending on model..." required><?= htmlspecialchars($faq['answer']) ?></textarea>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <!-- Empty State (Shown when no FAQs exist) -->
          <div id="emptyFaqsNotice" style="<?= !empty($colFaqs) ? 'display: none;' : '' ?> padding: 32px 20px; text-align: center; border: 1px dashed var(--border-color); border-radius: 8px; background: #fafbfc;">
            <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="#94a3b8" stroke-width="1.8" style="margin-bottom: 8px;">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
              <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
            <p style="font-weight: 600; color: #475569; margin: 0 0 4px 0; font-size: 14px;">No FAQs added yet</p>
            <p style="font-size: 13px; color: #94a3b8; margin: 0 0 12px 0;">Click "+ Add FAQ" above to add the first question and answer for this collection.</p>
          </div>
        </div>
      </div>

      <!-- 4. Explore Related Collections (Storefront Cross-Sell) Card -->
      <div class="admin-card">
        <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
          <div>
            <h2 class="admin-card-title">Explore Related Collections (Storefront Cross-Sell)</h2>
            <p class="form-help-text" style="margin-top: 4px;">Select which collections should be recommended to customers under the "Explore Related Collections" section on this collection page.</p>
          </div>
          <div style="display: flex; align-items: center; gap: 8px;">
            <span class="badge badge-secondary" id="selectedRelCountBadge" style="font-size: 12px; padding: 4px 10px; border-radius: 12px; background: #edf2f9; color: #082142; font-weight: 600;"><?= count($savedRelatedIds) ?> selected</span>
            <button type="button" class="btn-admin btn-admin-secondary" id="btnSelectAllRel" style="padding: 5px 10px; font-size: 12px;">Select All</button>
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
                if ($availId === $colId) continue; // Do not cross-sell the same collection
                $isChecked = in_array($availId, $savedRelatedIds, true);
                $availImg = !empty($availCol['header_image_url']) ? asset($availCol['header_image_url']) : '';
                $pCount = (int)($availCol['products_count'] ?? 0);
              ?>
                <label class="related-col-checkbox-card <?= $isChecked ? 'is-selected' : '' ?>" style="display: flex; align-items: center; gap: 10px; padding: 9px 12px; background: #ffffff; border: 1px solid <?= $isChecked ? 'var(--admin-primary, #082142)' : 'var(--border-color, #e2e8f0)' ?>; border-radius: 6px; cursor: pointer; transition: all 0.15s ease; user-select: none;">
                  <input type="checkbox" name="related_collections[]" value="<?= $availId ?>" class="related-col-checkbox" <?= $isChecked ? 'checked' : '' ?> style="width: 16px; height: 16px; accent-color: #082142; cursor: pointer;">
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
        <div class="admin-card-header">
          <div>
            <h2 class="admin-card-title">Search Engine Listing (SEO)</h2>
            <p class="form-help-text">Optimize title, meta description, and URL handle for Google search ranking.</p>
          </div>
          <a href="#" id="toggleSeoBtn" class="btn-link-seo">Edit website SEO</a>
        </div>
        <div class="admin-card-body">
          
          <!-- SEO Editable Inputs -->
          <div id="seoFieldsContainer" class="seo-fields-container active">
            
            <div class="admin-form-group">
              <label for="seoTitleInput" class="admin-form-label">SEO Meta Title</label>
              <input type="text" id="seoTitleInput" name="seo_title" class="admin-form-input" value="<?= e($colSeoTitle) ?>" placeholder="Enter custom SEO title" maxlength="70">
              <div class="char-counter"><span id="seoTitleCount"><?= mb_strlen($colSeoTitle) ?></span> of 70 characters used</div>
            </div>

            <div class="admin-form-group">
              <label for="seoDescInput" class="admin-form-label">SEO Meta Description</label>
              <textarea id="seoDescInput" name="seo_description" class="admin-form-textarea" rows="3" placeholder="Enter meta description for search engines..." maxlength="320"><?= e($colSeoDesc) ?></textarea>
              <div class="char-counter"><span id="seoDescCount"><?= mb_strlen($colSeoDesc) ?></span> of 320 characters used</div>
            </div>

            <div class="admin-form-group mb-0">
              <label for="slugInput" class="admin-form-label">URL Handle / Slug</label>
              <div class="input-prefix-container">
                <span class="prefix" id="urlPrefixSpan" data-base-url="<?= rtrim(url(), '/') ?>/"><?= rtrim(url(), '/') ?>/</span>
                <input type="text" id="slugInput" name="slug" value="<?= e($colSlug) ?>">
              </div>
            </div>

          </div>

          <!-- Live Google Search Preview Card -->
          <div class="seo-preview-box">
            <div class="seo-preview-header">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
              <span>Infinity Vape Official Store</span>
            </div>
            <div class="seo-preview-url" id="seoPreviewUrl"><?= rtrim(url(), '/') ?>/<?= e($colSlug) ?></div>
            <div class="seo-preview-title" id="seoPreviewTitle"><?= e($colSeoTitle) ?></div>
            <div class="seo-preview-desc" id="seoPreviewDesc"><?= e($colSeoDesc) ?></div>
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
              <option value="active" <?= $colStatus === 'active' ? 'selected' : '' ?>>Active</option>
              <option value="draft" <?= $colStatus === 'draft' ? 'selected' : '' ?>>Draft</option>
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
              <option value="" data-slug="" <?= empty($colParentId) ? 'selected' : '' ?>>None (Top Level Category)</option>
              <?php if (!empty($parents)): ?>
                <?php foreach ($parents as $p): ?>
                  <option value="<?= $p['id'] ?>" data-slug="<?= e($p['slug']) ?>" <?= $colParentId == $p['id'] ? 'selected' : '' ?>><?= e($p['name']) ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>
          <p class="form-help-text mt-sm">Nest this collection as a sub-category under an existing parent.</p>
        </div>
      </div>

      <!-- Quick Stats Card -->
      <div class="admin-card">
        <div class="admin-card-header">
          <h2 class="admin-card-title">Collection Stats</h2>
        </div>
        <div class="admin-card-body">
          <div class="d-flex justify-between align-center mb-sm">
            <span class="text-muted" style="font-size: 13px;">Attached Products</span>
            <strong class="text-dark" style="font-size: 14px;"><?= (int)($collection['products_count'] ?? 0) ?> items</strong>
          </div>
          <div class="d-flex justify-between align-center">
            <span class="text-muted" style="font-size: 13px;">Collection ID</span>
            <code style="font-size: 12px; background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">#<?= $colId ?></code>
          </div>
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
        <span>Save Changes</span>
      </button>
    </div>
  </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const container = document.getElementById('colFaqsContainer');
  const addBtn = document.getElementById('btnAddFaqRow');
  const emptyNotice = document.getElementById('emptyFaqsNotice');
  let faqCount = <?= count($colFaqs) ?>;

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

  // Bind existing remove buttons
  container.querySelectorAll('.btn-remove-faq').forEach(btn => {
    btn.addEventListener('click', function() {
      btn.closest('.col-faq-item-card')?.remove();
      updateFaqBadges();
    });
  });

  if (addBtn) {
    addBtn.addEventListener('click', function(e) {
      e.preventDefault();
      addFaqRow('', '');
    });
  }

  updateFaqBadges();
});
</script>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
