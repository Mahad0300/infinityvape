<?php
$page_title = 'Add New Product';
$current_admin_page = 'products';
$page_script = 'add-product.js';
include dirname(__DIR__) . '/includes/header.php';

// Dynamic Brands & Collections from Database
$allBrands = \App\Models\Brand::all();
$brands = !empty($allBrands) ? array_column($allBrands, 'name') : ['LOST MARY', 'GEEK BAR', 'RAZ', 'JUICE HEAD', 'VOOPOO'];
$allCols = \App\Models\Collection::all();
$collections = !empty($allCols) ? array_column($allCols, 'name') : ['E-Liquids', 'Disposable', 'Kits', 'Mods', 'Tanks'];
?>

<!-- Title & Action Bar -->
<div class="page-title-bar">
  <div class="page-heading-group">
    <div class="d-flex align-center gap-md">
      <a href="<?= url('admin/products') ?>" class="btn-icon-only" title="Back to Products">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="19" y1="12" x2="5" y2="12"></line>
          <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
      </a>
      <h1>Add New Product</h1>
    </div>
    <p class="page-subtitle">Configure product content, variants matrix, SEO optimization, and inventory.</p>
  </div>
  <div class="page-actions-group">
    <a href="<?= url('admin/products') ?>" class="btn-admin btn-admin-secondary">Discard</a>
    <button type="submit" form="productMainForm" class="btn-admin btn-admin-primary">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="20 6 9 17 4 12"></polyline>
      </svg>
      <span>Publish Product</span>
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

<form id="productMainForm" action="<?= url('admin/add-product') ?>" method="POST" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <div class="detail-grid-layout">
    
    <!-- Left Column: Main Product Configuration -->
    <div class="admin-col-main">
      
      <!-- 1. Product Title, Short Description & Feature Highlights Card -->
      <div class="admin-card">
        <div class="admin-card-header">
          <h2 class="admin-card-title">Product Title, Short Description &amp; Feature Highlights</h2>
        </div>
        <div class="admin-card-body">
          
          <!-- Product Title -->
          <div class="admin-form-group">
            <label for="productTitle" class="admin-form-label">Product Title <span class="required">*</span></label>
            <input type="text" id="productTitle" name="title" class="admin-form-input" placeholder="e.g. Juice Head Salt x Vaporesso 50K Bundle" required>
          </div>

          <!-- Short Description -->
          <div class="admin-form-group">
            <label for="productShortDesc" class="admin-form-label">Short Description</label>
            <textarea id="productShortDesc" name="short_desc" class="admin-form-textarea" rows="3" placeholder="Brief product summary displayed right next to images, price, and buy button on storefront..."></textarea>
          </div>

          <!-- Top 4 Feature -->
          <div style="margin-top: 20px;">
            <div style="margin-bottom: 12px;">
              <h3 style="font-size: 14px; font-weight: 700; color: #1e293b; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">Top 4 Feature</h3>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
              <div class="admin-form-group mb-0">
                <label class="admin-form-label">Puff Count</label>
                <input type="hidden" name="highlights[0][key]" value="Puffs">
                <input type="hidden" name="highlights[0][icon_type]" value="puffs">
                <input type="text" name="highlights[0][value]" class="admin-form-input" placeholder="e.g. 15,000 Puffs">
              </div>
              <div class="admin-form-group mb-0">
                <label class="admin-form-label">Nicotine Strength</label>
                <input type="hidden" name="highlights[1][key]" value="Nicotine">
                <input type="hidden" name="highlights[1][icon_type]" value="nicotine">
                <input type="text" name="highlights[1][value]" class="admin-form-input" placeholder="e.g. 50mg (5%)">
              </div>
              <div class="admin-form-group mb-0">
                <label class="admin-form-label">Battery Capacity</label>
                <input type="hidden" name="highlights[2][key]" value="Battery">
                <input type="hidden" name="highlights[2][icon_type]" value="battery">
                <input type="text" name="highlights[2][value]" class="admin-form-input" placeholder="e.g. 850mAh Type-C">
              </div>
              <div class="admin-form-group mb-0">
                <label class="admin-form-label">Liquid Capacity</label>
                <input type="hidden" name="highlights[3][key]" value="Capacity">
                <input type="hidden" name="highlights[3][icon_type]" value="capacity">
                <input type="text" name="highlights[3][value]" class="admin-form-input" placeholder="e.g. 16mL">
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- 2. Media Gallery Card -->
      <div class="admin-card">
        <div class="admin-card-header">
          <h2 class="admin-card-title">Media Assets &amp; Product Images</h2>
        </div>
        <div class="admin-card-body">
          <input type="file" id="productMediaInput" name="product_images[]" multiple accept="image/*" class="d-none">
          <input type="hidden" name="media_assets_managed" value="1">
          <input type="hidden" name="image" id="productImageUrl" value="">
          <input type="hidden" name="featured_image_index" id="featuredImageIndex" value="0">
          
          <div class="media-upload-area" id="mediaDropzone">
            <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="media-dropzone-icon">
              <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
              <circle cx="8.5" cy="8.5" r="1.5"></circle>
              <polyline points="21 15 16 10 5 21"></polyline>
            </svg>
            <div class="media-dropzone-title">Drag &amp; drop product images, or browse files</div>
            <p class="form-help-text">Supports WebP, PNG, JPG (Select multiple images at once)</p>
          </div>

          <div class="media-preview-grid" id="mediaPreviewGrid" style="display: none;"></div>
        </div>
      </div>

      <!-- 3. Pricing Card -->
      <div class="admin-card">
        <div class="admin-card-header">
          <h2 class="admin-card-title">Pricing</h2>
        </div>
        <div class="admin-card-body">
          <div class="form-grid-2">
            <div class="admin-form-group price-input-narrow">
              <label for="productBasePrice" class="admin-form-label">Price <span class="required">*</span></label>
              <div class="input-prefix-container">
                <span class="prefix">$</span>
                <input type="number" id="productBasePrice" name="price" step="0.01" placeholder="29.99" required>
              </div>
            </div>
            <div class="admin-form-group price-input-narrow">
              <label for="productComparePrice" class="admin-form-label">Compare-at Price (Optional)</label>
              <div class="input-prefix-container">
                <span class="prefix">$</span>
                <input type="number" id="productComparePrice" name="compare_price" step="0.01" placeholder="39.99">
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 4. Inventory Management Card -->
      <div class="admin-card">
        <div class="admin-card-header">
          <h2 class="admin-card-title">Inventory &amp; Stock Tracking</h2>
        </div>
        <div class="admin-card-body">
          <div class="form-grid-2">
            
            <div class="admin-form-group">
              <label for="productSku" class="admin-form-label">SKU (Stock Keeping Unit)</label>
              <input type="text" id="productSku" name="sku" class="admin-form-input" placeholder="e.g. VH-JH-50K">
            </div>

            <div class="admin-form-group">
              <label for="productStock" class="admin-form-label">Quantity Available</label>
              <input type="number" id="productStock" name="stock" class="admin-form-input" placeholder="0" min="0">
            </div>

          </div>

          <div class="checkbox-group mt-md">
            <label class="checkbox-label">
              <input type="checkbox" name="track_inventory" checked>
              <span>Track inventory quantity automatically (Prevents out-of-stock orders)</span>
            </label>
          </div>
        </div>
      </div>      <!-- 5. Shopify-Style Variants Section -->
      <div class="admin-card">
        <div class="admin-card-header">
          <div>
            <h2 class="admin-card-title">Variants</h2>
            <p class="form-help-text">Add variations of this product, like sizes, colors, and flavors.</p>
          </div>
        </div>
        
        <div class="admin-card-body">
          <!-- Options Toggle (Shopify style) -->
          <div class="checkbox-group mb-md">
            <label class="checkbox-label" style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px;">
              <input type="checkbox" id="hasVariantsToggle" name="has_variants" value="1">
              <span><strong>This product has options</strong>, like size, flavor, nicotine strength, or color</span>
            </label>
          </div>

          <div id="variantsOptionsWrapper" style="display: none;">
            <!-- Options Box -->
            <div class="variants-options-card">
              <div id="optionsListContainer">
                <!-- Option rows generated purely dynamically -->
              </div>

              <!-- Add another option trigger -->
              <div class="variant-add-option-footer">
                <button type="button" class="btn-add-option-link" id="addAnotherOptionLink">
                  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                  </svg>
                  <span>Add another option</span>
                </button>
              </div>
            </div>

            <!-- Variants Matrix Table (Shopify Style) -->
            <div class="variants-table-wrap" id="variantsTableWrap" style="margin-top: 20px; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; background: #fff;">
              <!-- Bulk Actions / Counter Toolbar -->
              <div class="variants-table-header-toolbar" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 16px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; min-height: 46px;">
                <div class="d-flex align-center gap-md" style="flex-wrap: wrap;">
                  <span id="variantsCountBadge" style="font-size: 13px; font-weight: 600; color: #475569;">0 variants</span>
                  <div id="bulkActionsGroup" style="display: none; align-items: center; gap: 8px; margin-left: 12px; padding-left: 12px; border-left: 1px solid #cbd5e1;">
                    <span id="selectedCountText" style="font-size: 12.5px; font-weight: 600; color: #0f172a;">0 selected</span>
                    <button type="button" class="btn-admin btn-admin-secondary btn-admin-sm" id="btnBulkEditPrice" style="padding: 3px 8px; font-size: 12px;">Edit price</button>
                    <button type="button" class="btn-admin btn-admin-secondary btn-admin-sm" id="btnBulkEditStock" style="padding: 3px 8px; font-size: 12px;">Edit quantity</button>
                    <button type="button" class="btn-admin btn-admin-secondary btn-admin-sm" id="btnBulkDelete" style="padding: 3px 8px; font-size: 12px; color: #ef4444;">Delete</button>
                  </div>
                </div>
                <div>
                  <button type="button" class="btn-admin btn-admin-secondary btn-admin-sm" data-modal-target="#bulkEditModal" style="padding: 4px 10px; font-size: 12px;">Bulk edit all</button>
                </div>
              </div>

              <div class="table-responsive">
                <table class="variants-matrix-table" style="width: 100%; border-collapse: collapse;">
                  <thead>
                    <tr style="background: #ffffff; border-bottom: 1px solid #e2e8f0;">
                      <th style="width: 44px; text-align: center; padding: 10px;">
                        <input type="checkbox" id="selectAllVariants" title="Select all variants" style="width: 16px; height: 16px; accent-color: #0f172a; cursor: pointer;">
                      </th>
                      <th style="min-width: 220px; font-size: 12px; font-weight: 600; color: #475569; padding: 10px 14px; text-align: left;">Variant</th>
                      <th style="width: 130px; font-size: 12px; font-weight: 600; color: #475569; padding: 10px 14px; text-align: left;">Price</th>
                      <th style="width: 135px; min-width: 135px; font-size: 12px; font-weight: 600; color: #475569; padding: 10px 14px; text-align: left;">Available</th>
                      <th style="min-width: 160px; font-size: 12px; font-weight: 600; color: #475569; padding: 10px 14px; text-align: left;">SKU</th>
                    </tr>
                  </thead>
                  <tbody id="variantsTableBody">
                    <tr id="emptyVariantsRow">
                      <td colspan="5" class="variant-empty-cell" style="padding: 32px 20px; text-align: center; color: #64748b;">
                        <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" style="margin: 0 auto 8px; display: block; stroke: #94a3b8;">
                          <circle cx="12" cy="12" r="10"></circle>
                          <line x1="12" y1="8" x2="12" y2="12"></line>
                          <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        No variant values added yet. Add an option name and values above to generate combinations.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 6. Full Detailed Description Card -->
      <div class="admin-card">
        <div class="admin-card-header">
          <div>
            <h2 class="admin-card-title">Full Detailed Description</h2>
            <p class="form-help-text">Comprehensive rich text product description, features, flavor profiles, and guidelines displayed on the storefront. If left empty, the Description tab will not appear on the website.</p>
          </div>
        </div>
        <div class="admin-card-body">
          <div class="admin-form-group mb-0">
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
              <div class="rte-editor-content" contenteditable="true" data-placeholder="Write comprehensive product description, flavor notes, safety instructions, and features..."></div>
              <textarea name="description" class="d-none"></textarea>
            </div>
            <p class="form-help-text mt-xs">Use the toolbar to choose Heading 1 to 6 or Paragraph, Bold, Italic, Underline, Highlight, Bullets, Numbers, and Links.</p>
          </div>
        </div>
      </div>

      <!-- 7. Specifications Card -->
      <div class="admin-card">
        <div class="admin-card-header d-flex justify-between align-center">
          <div>
            <h2 class="admin-card-title">Specifications</h2>
            <p class="form-help-text">Configure detailed specification rows for the More Information tab. If no specifications are added, the More Information tab will not appear on the website.</p>
          </div>
          <button type="button" class="btn-admin btn-admin-secondary btn-admin-sm" id="btnAddSpecRow">
            <span>+ Add Specification</span>
          </button>
        </div>
        <div class="admin-card-body">
          <div id="specsContainer"></div>
          <div id="emptySpecsNotice" style="padding: 16px; text-align: center; border: 1px dashed var(--border-color); border-radius: 8px; background: #fafbfc; color: #64748b; font-size: 13px;">
            No custom detailed specs added yet. If left empty, the More Information tab will be hidden on the storefront. Click "+ Add Specification" above to add custom specs.
          </div>
        </div>
      </div>

      <!-- 8. Customer Reviews Card -->
      <div class="admin-card">
        <div class="admin-card-header d-flex justify-between align-center">
          <div>
            <h2 class="admin-card-title">Reviews</h2>
            <p class="form-help-text">Reviews are submitted directly by verified customers on the product page and require administrator approval before appearing live.</p>
          </div>
          <a href="<?= url('admin/reviews') ?>" class="btn-admin btn-admin-secondary btn-admin-sm" target="_blank">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
            <span>Moderate Reviews</span>
          </a>
        </div>
        <div class="admin-card-body">
          <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; display: flex; align-items: flex-start; gap: 14px;">
            <div style="background: #e0f2fe; color: #0284c7; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            </div>
            <div>
              <p style="margin: 0 0 6px 0; font-weight: 600; color: #1e293b; font-size: 14px;">How Customer Reviews Work</p>
              <p style="margin: 0; font-size: 13px; color: #64748b; line-height: 1.5;">
                Users add reviews directly from the product page with a star rating and comment. All submissions go to the <strong>Admin &gt; Reviews</strong> dashboard as <em>Pending</em>. Once you approve them, they appear under the Reviews tab on the product page.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- 9. Product FAQs Card -->
      <div class="admin-card">
        <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center;">
          <div>
            <h2 class="admin-card-title">FAQs</h2>
            <p class="form-help-text" style="margin-top: 4px;">Add specific questions and answers for this product. If left empty, the FAQs tab will not appear on the website.</p>
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
          <div id="productFaqsContainer"></div>

          <!-- Empty State (Shown when no FAQs exist) -->
          <div id="emptyFaqsNotice" style="display: none; padding: 32px 20px; text-align: center; border: 1px dashed var(--border-color); border-radius: 8px; background: #fafbfc;">
            <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="#94a3b8" stroke-width="1.8" style="margin-bottom: 8px;">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
              <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
            <p style="font-weight: 600; color: #475569; margin: 0 0 4px 0; font-size: 14px;">No FAQs added</p>
            <p style="font-size: 13px; color: #94a3b8; margin: 0 0 12px 0;">If left empty, the FAQs tab will be completely hidden on the storefront. Click "+ Add FAQ" above to add custom questions.</p>
          </div>
        </div>
      </div>

      <!-- 10. Related Posts Card -->
      <div class="admin-card">
        <div class="admin-card-header">
          <div>
            <h2 class="admin-card-title">Related Posts</h2>
            <p class="form-help-text">Write custom related articles, guides, usage tips, or recommendations using the rich text editor. If left empty, the Related Posts tab will not appear on the website.</p>
          </div>
        </div>
        <div class="admin-card-body">
          <div class="admin-form-group mb-0">
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
              <div class="rte-editor-content" contenteditable="true" data-placeholder="Write related articles, blog snippets, flavor guides, maintenance tips, or links..."></div>
              <textarea name="related_posts_content" class="d-none"></textarea>
            </div>
            <p class="form-help-text mt-xs">Use the toolbar to choose Heading 1 to 6 or Paragraph, Bold, Italic, Underline, Highlight, Bullets, Numbers, and Links.</p>
          </div>
        </div>
      </div>

      <!-- 6. Promotional Deal Banner Card (Optional) -->
      <div class="admin-card">
        <div class="admin-card-header d-flex justify-between align-center">
          <div>
            <h2 class="admin-card-title">Promotional Deal Banner (Optional)</h2>
            <p class="form-help-text">Attach an exclusive deal or bundle banner specifically for this product.</p>
          </div>
        </div>
        <div class="admin-card-body">
          <div class="admin-form-group mb-md">
            <label class="d-flex align-center gap-sm" style="cursor: pointer; user-select: none;">
              <input type="checkbox" name="bundle_banner_active" value="1" id="bundleBannerActiveCheckbox" style="width: 18px; height: 18px; accent-color: var(--color-accent-orange); cursor: pointer;">
              <span style="font-weight: 600; font-size: 14px; color: var(--color-primary-navy);">Enable Promotional Deal Banner for this product</span>
            </label>
          </div>

          <div id="bundleBannerConfigWrap" style="display: none; padding-top: 8px;">
            <div class="admin-form-group">
              <label class="admin-form-label">Banner Image</label>
              
              <div id="bundleBannerPreviewBox" style="display: none; margin-bottom: 12px; position: relative; max-width: 600px; border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; background: #0b1528;">
                <img id="bundleBannerPreviewImg" src="" alt="Promotional Banner Preview" style="width: 100%; height: auto; display: block; max-height: 140px; object-fit: contain;">
                <button type="button" id="btnRemoveBundleBanner" title="Remove banner" style="position: absolute; top: 8px; right: 8px; background: rgba(0,0,0,0.7); color: #fff; border: none; border-radius: 50%; width: 28px; height: 28px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 18px; line-height: 1;">
                  &times;
                </button>
              </div>

              <input type="file" name="bundle_banner_img" id="bundleBannerFileInput" accept="image/*" class="admin-form-input">
              <p class="form-help-text" style="margin-top: 6px;">Recommended format: WebP, PNG, or JPG (approx 1200 x 280 or 800 x 160 px).</p>
            </div>

            <div class="admin-form-group mb-0">
              <label for="bundleBannerLinkInput" class="admin-form-label">Banner Target Link (Optional)</label>
              <input type="text" id="bundleBannerLinkInput" name="bundle_banner_link" class="admin-form-input" placeholder="e.g. collection/starter-kits or /discounts/deal-1">
              <p class="form-help-text" style="margin-top: 6px;">Where customers are redirected when clicking the deal banner.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- 7. Frequently Bought Together (Optional - Max 4) -->
      <div class="admin-card">
        <div class="admin-card-header d-flex justify-between align-center">
          <div>
            <h2 class="admin-card-title">Frequently Bought Together (Cross-Sell Bundle)</h2>
            <p class="form-help-text">Choose up to 4 companion products to recommend together on this product's page.</p>
          </div>
          <span id="fbtCounterBadge" style="font-size: 12px; font-weight: 700; background: #f1f5f9; color: var(--color-primary-navy); padding: 4px 10px; border-radius: 9999px; border: 1px solid #e2e8f0;">
            <span id="fbtSelectedCount">0</span> / 4 Selected
          </span>
        </div>
        <div class="admin-card-body">
          <div class="admin-form-group mb-sm">
            <input type="text" id="fbtSearchInput" class="admin-form-input" placeholder="Search catalog by product name or SKU..." autocomplete="off">
          </div>

          <div id="fbtProductList" style="max-height: 290px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: 8px; background: #fff; padding: 6px;">
            <?php if (!empty($all_products)): ?>
              <?php foreach ($all_products as $pItem): ?>
                <?php
                $pStock = (int)($pItem['stock_quantity'] ?? 0);
                $pIsOut = ($pStock <= 0) || (strtolower(str_replace(' ', '_', $pItem['stock_status'] ?? '')) === 'out_of_stock');
                ?>
                <label class="fbt-product-row" data-search="<?= htmlspecialchars(strtolower($pItem['title'] . ' ' . $pItem['sku'])) ?>" style="display: flex; align-items: center; gap: 12px; padding: 8px 10px; border-radius: 6px; cursor: pointer; transition: background 0.15s ease; border-bottom: 1px solid #f1f5f9;">
                  <input type="checkbox" name="frequently_bought_together[]" value="<?= $pItem['id'] ?>" class="fbt-checkbox" style="width: 17px; height: 17px; accent-color: var(--color-accent-orange); cursor: pointer; flex-shrink: 0;">
                  <img src="<?= asset($pItem['img'] ?? 'assets/images/no-image.svg') ?>" alt="<?= htmlspecialchars($pItem['title']) ?>" style="width: 38px; height: 38px; object-fit: contain; border-radius: 6px; background: #f8fafc; border: 1px solid #e2e8f0; flex-shrink: 0;" onerror="this.onerror=null;this.src='<?= asset('assets/images/no-image.svg') ?>';">
                  <div style="flex: 1; min-width: 0;">
                    <div style="font-weight: 600; font-size: 13px; color: var(--color-primary-navy); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($pItem['title']) ?></div>
                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                      SKU: <?= htmlspecialchars($pItem['sku'] ?: 'N/A') ?> &bull; <strong style="color: #0f172a;">$<?= number_format((float)($pItem['base_price'] ?? 0), 2) ?></strong>
                      <?php if ($pIsOut): ?>
                        <span style="font-size: 10px; font-weight: 700; color: #dc2626; background: #fee2e2; padding: 2px 6px; border-radius: 4px; margin-left: 6px;">Out of stock</span>
                      <?php else: ?>
                        <span style="font-size: 10px; font-weight: 600; color: #059669; margin-left: 6px;">(<?= $pStock ?> in stock)</span>
                      <?php endif; ?>
                    </div>
                  </div>
                </label>
              <?php endforeach; ?>
            <?php else: ?>
              <div style="padding: 24px; text-align: center; color: #94a3b8; font-size: 13px;">No other products available in catalog.</div>
            <?php endif; ?>
          </div>
          <p class="form-help-text mt-xs">Maximum 4 products can be selected. Customers will be able to configure options and add them all with 1-click.</p>
        </div>
      </div>

      <!-- 8. Search Engine Listing (SEO) Card -->
      <div class="admin-card">
        <div class="admin-card-header d-flex justify-between align-center">
          <div>
            <h2 class="admin-card-title">Search Engine Listing (SEO)</h2>
            <p class="form-help-text">Optimize search engine snippet for Google search rankings.</p>
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
          <div id="seoFieldsContainer" class="seo-fields-container">
            
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

            <div class="admin-form-group">
              <label for="slugInput" class="admin-form-label">URL Handle / Slug</label>
              <div class="input-prefix-container">
                <span class="prefix">.../products/</span>
                <input type="text" id="slugInput" name="slug" placeholder="e.g. juice-head-salt-vaporesso-50k">
              </div>
            </div>

          </div>

          <!-- Live Google Search Preview Card -->
          <div class="seo-preview-box">
            <div class="seo-preview-header">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
              <span>Infinity Vape Shop</span>
            </div>
            <div class="seo-preview-url" id="seoPreviewUrl">www.infinityvapeshop.com/products/product-handle</div>
            <div class="seo-preview-title" id="seoPreviewTitle">Product Title</div>
            <div class="seo-preview-desc" id="seoPreviewDesc">Product description will appear in search results...</div>
          </div>

        </div>
      </div>

    </div>

    <!-- Right Sidebar Column: Status & Organization -->
    <div class="admin-col-side">
      
      <!-- Product Status Card -->
      <div class="admin-card">
        <div class="admin-card-header">
          <h2 class="admin-card-title">Product Status</h2>
        </div>
        <div class="admin-card-body">
          <div class="admin-form-group">
            <select name="status" class="admin-form-select">
              <option value="active" selected>Active (Live on Storefront)</option>
              <option value="draft">Draft (Hidden)</option>
              <option value="archived">Archived</option>
            </select>
          </div>
          <p class="form-help-text">Active products are instantly available for purchase by 21+ verified customers.</p>
        </div>
      </div>

      <!-- Product Organization Card -->
      <div class="admin-card">
        <div class="admin-card-header">
          <h2 class="admin-card-title">Product Organization</h2>
        </div>
        <div class="admin-card-body">
          
          <!-- Brand Selector -->
          <div class="admin-form-group">
            <label for="productBrand" class="admin-form-label">Brand / Manufacturer <span class="required">*</span></label>
            <select id="productBrand" name="brand" class="admin-form-select" required>
              <option value="">Select Brand</option>
              <?php foreach ($brands as $b): ?>
                <option value="<?php echo htmlspecialchars($b); ?>"><?php echo htmlspecialchars($b); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Collection Selector -->
          <div class="admin-form-group">
            <label for="productCollection" class="admin-form-label">Primary Collection <span class="required">*</span></label>
            <select id="productCollection" name="collection" class="admin-form-select" required>
              <option value="">Select Collection</option>
              <?php foreach ($collections as $col): ?>
                <option value="<?php echo htmlspecialchars($col); ?>"><?php echo htmlspecialchars($col); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Product Tags with Chips -->
          <div class="admin-form-group mb-0">
            <label for="tagInputField" class="admin-form-label">Product Tags</label>
            <input type="text" id="tagInputField" class="admin-form-input" placeholder="Type tag &amp; press Enter (e.g. Salt Nic, 50mg, Best Seller)...">
            <div class="selected-tags" id="tagsContainer"></div>
          </div>

        </div>
      </div>

    </div>

  </div>

  <!-- Bottom Actions Bar -->
  <div class="form-actions-bar">
    <div class="d-flex align-center gap-md">
      <a href="<?= url('admin/products') ?>" class="btn-admin btn-admin-secondary">Discard</a>
      <button type="submit" class="btn-admin btn-admin-primary">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
        <span>Publish Product</span>
      </button>
    </div>
  </div>
</form>

<!-- Bulk Edit Variants Modal -->
<div class="admin-modal-overlay" id="bulkEditModal">
  <div class="admin-modal-dialog">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title">Bulk Edit Variant Prices &amp; Stock</h3>
      <button type="button" class="admin-modal-close btn-close-modal" aria-label="Close Modal">&times;</button>
    </div>
    <div class="admin-modal-body">
      <p class="modal-desc-text">Apply a unified selling price and inventory stock count across all active combinations.</p>
      
      <div class="admin-form-group">
        <label for="bulkPriceInput" class="admin-form-label">Apply Price ($)</label>
        <div class="input-prefix-container">
          <span class="prefix">$</span>
          <input type="number" id="bulkPriceInput" placeholder="29.99" step="0.01">
        </div>
      </div>

      <div class="admin-form-group">
        <label for="bulkStockInput" class="admin-form-label">Apply Stock Quantity</label>
        <input type="number" id="bulkStockInput" class="admin-form-input" placeholder="50">
      </div>
    </div>
    <div class="admin-modal-footer">
      <button type="button" class="btn-admin btn-admin-secondary btn-close-modal">Cancel</button>
      <button type="button" class="btn-admin btn-admin-primary" id="applyBulkEdit">Apply to All Variants</button>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const toggle = document.getElementById('bundleBannerActiveCheckbox');
  const wrap = document.getElementById('bundleBannerConfigWrap');
  const fileInput = document.getElementById('bundleBannerFileInput');
  const previewBox = document.getElementById('bundleBannerPreviewBox');
  const previewImg = document.getElementById('bundleBannerPreviewImg');
  const removeBtn = document.getElementById('btnRemoveBundleBanner');

  if (toggle && wrap) {
    toggle.addEventListener('change', function() {
      wrap.style.display = this.checked ? 'block' : 'none';
    });
  }

  if (fileInput && previewBox && previewImg) {
    fileInput.addEventListener('change', function() {
      const file = this.files && this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          previewImg.src = e.target.result;
          previewBox.style.display = 'block';
        };
        reader.readAsDataURL(file);
      }
    });
  }

  if (removeBtn) {
    removeBtn.addEventListener('click', function(e) {
      e.preventDefault();
      if (fileInput) fileInput.value = '';
      if (previewImg) previewImg.src = '';
      if (previewBox) previewBox.style.display = 'none';
    });
  }

  // --- Dynamic Product FAQs Manager ---
  const faqContainer = document.getElementById('productFaqsContainer');
  const addFaqBtn = document.getElementById('btnAddFaqRow');
  const emptyFaqsNotice = document.getElementById('emptyFaqsNotice');
  let faqCount = 0;

  function updateFaqBadges() {
    const items = faqContainer.querySelectorAll('.prod-faq-item-card');
    if (items.length === 0) {
      if (emptyFaqsNotice) emptyFaqsNotice.style.display = 'block';
    } else {
      if (emptyFaqsNotice) emptyFaqsNotice.style.display = 'none';
      items.forEach((item, idx) => {
        const badge = item.querySelector('.faq-row-badge');
        if (badge) badge.textContent = 'FAQ #' + (idx + 1);
      });
    }
  }

  function addFaqRow(question = '', answer = '') {
    const index = faqCount++;
    const row = document.createElement('div');
    row.className = 'prod-faq-item-card';
    row.style.cssText = 'border: 1px solid var(--border-color); border-radius: 8px; padding: 16px; background: #fff; margin-bottom: 14px; position: relative; box-shadow: 0 1px 3px rgba(0,0,0,0.02);';
    row.innerHTML = `
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <span class="faq-row-badge" style="font-size: 12px; font-weight: 700; color: var(--color-primary-navy); background: #f1f5f9; padding: 3px 8px; border-radius: 4px;">FAQ #${faqContainer.children.length + 1}</span>
        <button type="button" class="btn-remove-faq" title="Delete question" style="border: none; background: none; color: #ef4444; cursor: pointer; display: flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 600;">
          <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
          Remove
        </button>
      </div>
      <div class="admin-form-group" style="margin-bottom: 10px;">
        <label class="admin-form-label" style="font-size: 13px;">Question</label>
        <input type="text" name="faqs[${index}][question]" class="admin-form-input" placeholder="e.g. How many puffs does this device deliver?" value="${question.replace(/"/g, '&quot;')}" required>
      </div>
      <div class="admin-form-group" style="margin-bottom: 0;">
        <label class="admin-form-label" style="font-size: 13px;">Answer</label>
        <textarea name="faqs[${index}][answer]" class="admin-form-textarea" rows="2" placeholder="e.g. Up to 15,000 puffs depending on draw duration..." required>${answer}</textarea>
      </div>
    `;

    row.querySelector('.btn-remove-faq').addEventListener('click', function() {
      row.remove();
      updateFaqBadges();
    });

    faqContainer.appendChild(row);
    updateFaqBadges();
  }

  if (addFaqBtn) {
    addFaqBtn.addEventListener('click', function(e) {
      e.preventDefault();
      addFaqRow('', '');
    });
  }

  updateFaqBadges();

  // --- Frequently Bought Together (FBT) Selector Logic ---
  const fbtCheckboxes = document.querySelectorAll('.fbt-checkbox');
  const fbtCounter = document.getElementById('fbtSelectedCount');
  const MAX_FBT = 4;

  function updateFbtState() {
    const checked = document.querySelectorAll('.fbt-checkbox:checked');
    const count = checked.length;
    if (fbtCounter) fbtCounter.textContent = count;

    fbtCheckboxes.forEach(cb => {
      const row = cb.closest('.fbt-product-row');
      if (cb.checked) {
        if (row) row.style.backgroundColor = '#fdfbf7';
        cb.disabled = false;
        if (row) {
          row.style.opacity = '1';
          row.style.cursor = 'pointer';
        }
      } else {
        if (row) row.style.backgroundColor = '';
        if (count >= MAX_FBT) {
          cb.disabled = true;
          if (row) {
            row.style.opacity = '0.45';
            row.style.cursor = 'not-allowed';
          }
        } else {
          cb.disabled = false;
          if (row) {
            row.style.opacity = '1';
            row.style.cursor = 'pointer';
          }
        }
      }
    });
  }

  fbtCheckboxes.forEach(cb => {
    cb.addEventListener('change', function() {
      const checked = document.querySelectorAll('.fbt-checkbox:checked');
      if (checked.length > MAX_FBT) {
        this.checked = false;
        alert('You can select a maximum of 4 products for Frequently Bought Together.');
      }
      updateFbtState();
    });
  });

  const fbtSearch = document.getElementById('fbtSearchInput');
  if (fbtSearch) {
    fbtSearch.addEventListener('input', function() {
      const q = this.value.trim().toLowerCase();
      document.querySelectorAll('.fbt-product-row').forEach(row => {
        const searchTxt = row.getAttribute('data-search') || '';
        row.style.display = (q === '' || searchTxt.includes(q)) ? 'flex' : 'none';
      });
    });
  }

  updateFbtState();
});
window.APP_BASE_URL = <?= json_encode(base_url()) ?>;
</script>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
