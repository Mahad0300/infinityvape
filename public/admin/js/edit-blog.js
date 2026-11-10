/**
 * Infinity Vape Shop - Edit Blog Post Page JavaScript
 * Features:
 * 1. Modular Section-Based Content Builder (Rich Text + Product Showcase max 2)
 * 2. Parses Existing Content & Restores Shortcodes into Dynamic Blocks
 * 3. Auto Slug Management & SEO Live Preview
 * 4. Featured Image Upload Preview
 * 5. Form Serialization with Shortcode Integration
 */
document.addEventListener('DOMContentLoaded', () => {
  // Common Form & SEO Elements
  const blogForm = document.getElementById('blogPostForm');
  const titleInput = document.getElementById('blogTitleInput');
  const slugInput = document.getElementById('blogSlugInput');
  const toggleSeoBtn = document.getElementById('toggleSeoBtn');
  const seoFieldsContainer = document.getElementById('seoFieldsContainer');
  const seoTitleInput = document.getElementById('seoTitleInput');
  const seoDescInput = document.getElementById('seoDescInput');
  const excerptInput = document.getElementById('excerptInput');
  const previewTitle = document.getElementById('previewTitle');
  const previewDesc = document.getElementById('previewDesc');
  const previewSlug = document.getElementById('previewSlug');
  const seoTitleCount = document.getElementById('seoTitleCount');
  const seoDescCount = document.getElementById('seoDescCount');
  const imgInput = document.getElementById('featuredImageInput');
  const imgPreview = document.getElementById('featuredImagePreview');
  const masterContentInput = document.getElementById('blogContentInput');
  const sectionsContainer = document.getElementById('blogSectionsContainer');

  // ==========================================================================
  // HELPER UTILITIES
  // ==========================================================================
  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function getProductById(id) {
    if (!id) return null;
    return (window.AVAILABLE_PRODUCTS || []).find(p => String(p.id) === String(id)) || null;
  }

  function resolveProductImg(img) {
    const defaultPlaceholder = (window.APP_URL || '') + '/assets/images/no-image.svg';
    if (!img || img === 'assets/images/vape-image.webp') return defaultPlaceholder;
    if (/^(https?:|\/\/)/i.test(img)) return img;
    return (window.APP_URL || '') + '/' + img.replace(/^\/+/, '');
  }

  function cleanJunkStyles(container) {
    if (!container) return;
    container.querySelectorAll('*').forEach(el => {
      el.style.color = '';
      el.style.fontFamily = '';
      el.style.fontSize = '';
      el.style.lineHeight = '';

      const bg = (el.style.backgroundColor || el.style.background || '').toLowerCase().replace(/\s+/g, '');
      const isYellowHighlight = (
        bg.includes('rgb(255,255,0)') || 
        bg.includes('#ffff00') || 
        bg.includes('#fef08a') || 
        bg.includes('#fde047') || 
        bg.includes('rgb(254,240,138)') ||
        bg.includes('rgb(253,224,71)') ||
        bg.includes('#fff59d')
      );

      if (!isYellowHighlight) {
        el.style.backgroundColor = '';
        el.style.background = '';
      }
    });
  }

  function getHighlightParent(node, contentRoot) {
    let curr = node;
    while (curr && curr !== contentRoot) {
      if (curr.nodeType === 1) {
        if (curr.tagName === 'MARK' || curr.classList.contains('rte-highlight')) {
          return curr;
        }
        const bg = (curr.style.backgroundColor || curr.style.background || '').toLowerCase().replace(/\s+/g, '');
        if (bg && bg !== 'transparent' && !bg.includes('rgba(0,0,0,0)')) {
          return curr;
        }
      }
      curr = curr.parentNode;
    }
    return null;
  }

  // ==========================================================================
  // SECTION BUILDER FACTORY & EVENT BINDINGS
  // ==========================================================================
  function updateSectionIndexes() {
    if (!sectionsContainer) return;
    const blocks = sectionsContainer.querySelectorAll('.blog-section-block');
    blocks.forEach((block, index) => {
      const num = index + 1;
      const indexLabel = block.querySelector('.section-index-label');
      if (indexLabel) {
        indexLabel.textContent = `Block #${num}`;
      }
      const btnUp = block.querySelector('.btn-sec-up');
      const btnDown = block.querySelector('.btn-sec-down');
      if (btnUp) btnUp.disabled = (index === 0);
      if (btnDown) btnDown.disabled = (index === blocks.length - 1);
    });
  }

  function serializeSections() {
    if (!sectionsContainer || !masterContentInput) return;
    const blocks = sectionsContainer.querySelectorAll('.blog-section-block');
    const parts = [];

    blocks.forEach(block => {
      const type = block.dataset.type;
      if (type === 'rich_text') {
        const editor = block.querySelector('.rte-editor-content');
        if (editor) {
          cleanJunkStyles(editor);
          const html = editor.innerHTML.trim();
          const text = editor.textContent.trim();
          if (text !== '' || html.includes('<img')) {
            parts.push(html);
          }
        }
      } else if (type === 'product_showcase') {
        const sel1 = block.querySelector('.slot-product-select[data-slot="1"]');
        const sel2 = block.querySelector('.slot-product-select[data-slot="2"]');
        const ids = [];
        if (sel1 && sel1.value) ids.push(sel1.value.trim());
        if (sel2 && sel2.value && sel2.value !== sel1.value) ids.push(sel2.value.trim());

        if (ids.length > 0) {
          parts.push(`[product_showcase ids="${ids.join(',')}"]`);
        }
      }
    });

    masterContentInput.value = parts.join('\n\n');
  }

  function attachBlockToolEvents(block) {
    const btnUp = block.querySelector('.btn-sec-tool btn-sec-up') || block.querySelector('.btn-sec-up');
    const btnDown = block.querySelector('.btn-sec-tool btn-sec-down') || block.querySelector('.btn-sec-down');
    const btnDelete = block.querySelector('.btn-sec-tool btn-sec-delete') || block.querySelector('.btn-sec-delete');

    if (btnUp) {
      btnUp.addEventListener('click', () => {
        const prev = block.previousElementSibling;
        if (prev && prev.classList.contains('blog-section-block')) {
          sectionsContainer.insertBefore(block, prev);
          updateSectionIndexes();
          serializeSections();
        }
      });
    }

    if (btnDown) {
      btnDown.addEventListener('click', () => {
        const next = block.nextElementSibling;
        if (next && next.classList.contains('blog-section-block')) {
          sectionsContainer.insertBefore(next, block);
          updateSectionIndexes();
          serializeSections();
        }
      });
    }

    if (btnDelete) {
      btnDelete.addEventListener('click', () => {
        const type = block.dataset.type;
        let hasContent = false;
        if (type === 'rich_text') {
          const editor = block.querySelector('.rte-editor-content');
          hasContent = editor && editor.textContent.trim().length > 0;
        } else if (type === 'product_showcase') {
          const sel1 = block.querySelector('.slot-product-select[data-slot="1"]');
          hasContent = sel1 && sel1.value !== '';
        }

        if (hasContent) {
          if (!confirm('Are you sure you want to remove this section? Its contents will be lost.')) {
            return;
          }
        }

        block.remove();

        // Ensure at least one section exists
        if (sectionsContainer.querySelectorAll('.blog-section-block').length === 0) {
          addRichTextSection('');
        }

        updateSectionIndexes();
        serializeSections();
      });
    }
  }

  // 1. Rich Text Section Creator
  function createRichTextBlock(initialHtml = '') {
    const block = document.createElement('div');
    block.className = 'blog-section-block section-type-text';
    block.dataset.type = 'rich_text';

    block.innerHTML = `
      <div class="section-block-header">
        <div class="section-drag-title">
          <span class="section-type-badge badge-text">
            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
            Rich Text
          </span>
          <span class="section-index-label">Block</span>
        </div>
        <div class="section-block-tools">
          <button type="button" class="btn-sec-tool btn-sec-up" title="Move Up">▲</button>
          <button type="button" class="btn-sec-tool btn-sec-down" title="Move Down">▼</button>
          <button type="button" class="btn-sec-tool btn-sec-delete" title="Delete Section">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
          </button>
        </div>
      </div>
      <div class="section-block-body">
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
          <div class="rte-editor-content" contenteditable="true" data-placeholder="Write your article paragraphs, headings, or vape recommendations here..." style="min-height: 220px;">${initialHtml}</div>
        </div>
      </div>
    `;

    attachBlockToolEvents(block);
    initRteEvents(block);
    return block;
  }

  function initRteEvents(block) {
    const toolbar = block.querySelector('.rte-toolbar');
    const content = block.querySelector('.rte-editor-content');
    if (!toolbar || !content) return;

    const formatSelect = toolbar.querySelector('.rte-format-select');
    if (formatSelect) {
      formatSelect.addEventListener('change', () => {
        document.execCommand('formatBlock', false, formatSelect.value);
        content.focus();
        serializeSections();
      });
    }

    toolbar.querySelectorAll('button').forEach(btn => {
      btn.addEventListener('mousedown', (e) => e.preventDefault());
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const cmd = btn.getAttribute('data-command');
        const val = btn.getAttribute('data-value') || null;

        if (cmd === 'createLink') {
          const url = prompt('Enter link URL (e.g. https://... or /product/...)', 'https://');
          if (url) {
            document.execCommand(cmd, false, url);
          }
        } else if (cmd === 'highlight') {
          const sel = window.getSelection();
          if (sel && sel.rangeCount > 0) {
            const range = sel.getRangeAt(0);
            const parentH = getHighlightParent(range.commonAncestorContainer, content) || getHighlightParent(range.startContainer, content);
            if (parentH) {
              parentH.style.backgroundColor = '';
              parentH.classList.remove('rte-highlight');
              if (parentH.tagName === 'MARK') {
                const txt = document.createTextNode(parentH.textContent);
                parentH.parentNode.replaceChild(txt, parentH);
              }
            } else if (!sel.isCollapsed) {
              document.execCommand('hiliteColor', false, '#fef08a');
            }
          }
        } else if (cmd === 'unlink') {
          document.execCommand('unlink', false, null);
        } else {
          document.execCommand(cmd, false, val);
        }
        content.focus();
        serializeSections();
      });
    });

    content.addEventListener('input', serializeSections);
    content.addEventListener('blur', serializeSections);
    content.addEventListener('paste', () => setTimeout(serializeSections, 60));
  }

  // 2. Product Showcase Section Creator (Max 2 Products)
  function createProductShowcaseBlock(initialIds = []) {
    const block = document.createElement('div');
    block.className = 'blog-section-block section-type-showcase';
    block.dataset.type = 'product_showcase';

    const products = window.AVAILABLE_PRODUCTS || [];
    const id1 = initialIds[0] ? String(initialIds[0]) : '';
    const id2 = initialIds[1] ? String(initialIds[1]) : '';
    const hasSlot2 = Boolean(id2);

    let options1Html = `<option value="">-- None --</option>`;
    let options2Html = `<option value="">-- None --</option>`;

    products.forEach(p => {
      const brand = p.brand_name ? ` (${p.brand_name})` : '';
      const price = `$${parseFloat(p.base_price || 0).toFixed(2)}`;
      const label = `${p.name} - ${price}${brand}`;

      options1Html += `<option value="${p.id}" ${String(p.id) === id1 ? 'selected' : ''}>${escapeHtml(label)}</option>`;
      options2Html += `<option value="${p.id}" ${String(p.id) === id2 ? 'selected' : ''}>${escapeHtml(label)}</option>`;
    });

    block.innerHTML = `
      <div class="section-block-header">
        <div class="section-drag-title">
          <span class="section-type-badge badge-showcase">
            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
            Product Showcase
          </span>
          <span class="section-index-label">Block</span>
          <span class="section-badge-limit">Max 2 Products</span>
        </div>
        <div class="section-block-tools">
          <button type="button" class="btn-sec-tool btn-sec-up" title="Move Up">▲</button>
          <button type="button" class="btn-sec-tool btn-sec-down" title="Move Down">▼</button>
          <button type="button" class="btn-sec-tool btn-sec-delete" title="Delete Section">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
          </button>
        </div>
      </div>
      <div class="section-block-body">
        <div class="showcase-guidance-note">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
          <span>Select <strong>1 product</strong> for a Spotlight Hero card, or click <strong>+ Add 2nd Product</strong> to build a side-by-side comparison grid. (Strict limit: 2 products max).</span>
        </div>

        <div class="showcase-builder-grid ${hasSlot2 ? 'is-comparison' : ''}">
          <!-- Slot 1 -->
          <div class="showcase-slot-card" data-slot="1">
            <div class="slot-header">
              <div class="slot-header-left">
                <span class="slot-badge primary">
                  <span class="slot-badge-dot"></span>
                  Product 1
                </span>
                <span class="slot-sub-badge">Spotlight Hero</span>
              </div>
              <span class="slot-mode-text">Required</span>
            </div>
            <select class="admin-form-select slot-product-select" data-slot="1" style="display:none;">
              ${options1Html}
            </select>
          </div>

          <!-- Slot 2 (Hidden by default unless id2 exists or user clicks '+') -->
          <div class="showcase-slot-card slot-card-2" data-slot="2" style="${hasSlot2 ? '' : 'display:none;'}">
            <div class="slot-header">
              <div class="slot-header-left">
                <span class="slot-badge comparison">
                  <span class="slot-badge-dot"></span>
                  Product 2
                </span>
                <span class="slot-sub-badge">Comparison Item</span>
              </div>
              <button type="button" class="btn-remove-slot2" title="Remove 2nd Product">
                <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                Remove
              </button>
            </div>
            <select class="admin-form-select slot-product-select" data-slot="2" style="display:none;">
              ${options2Html}
            </select>
          </div>
        </div>

        <!-- + Add 2nd Product for Comparison Toggle Button -->
        <div class="slot2-toggle-bar" style="${hasSlot2 ? 'display:none;' : ''}">
          <button type="button" class="btn-toggle-slot2-add">
            <span class="plus-icon-circle">+</span>
            <span class="btn-toggle-text">
              <strong>Add 2nd Product for Comparison (Optional)</strong>
              <small>Click to compare specs, puffs &amp; price side-by-side</small>
            </span>
          </button>
        </div>

        <div class="showcase-status-indicator">
          <span class="showcase-status-pill"></span>
          <span class="showcase-status-text"></span>
        </div>
      </div>
    `;

    attachBlockToolEvents(block);
    initShowcaseEvents(block);
    return block;
  }

  function setupSearchableSlot(slotCard, slotNum, sel, otherSel, onUpdateState) {
    if (!slotCard || !sel) return;

    const products = window.AVAILABLE_PRODUCTS || [];

    // Extract unique brands for filter chips
    const brandSet = new Set();
    products.forEach(p => {
      if (p.brand_name && p.brand_name.trim()) brandSet.add(p.brand_name.trim());
    });
    const uniqueBrands = Array.from(brandSet).sort();

    const widget = document.createElement('div');
    widget.className = 'slot-searchable-widget';
    widget.innerHTML = `
      <!-- Search Input & Dropdown -->
      <div class="slot-search-wrap">
        <div class="slot-search-input-wrap">
          <svg class="slot-search-icon" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          <input type="text" class="slot-search-input" placeholder="${slotNum === 1 ? 'Search product by name, brand, or flavor...' : 'Search 2nd product for comparison...'}" autocomplete="off">
          <button type="button" class="slot-search-clear" style="display:none;" title="Clear search">✕</button>
        </div>
        <div class="slot-dropdown-menu" style="display:none;">
          <div class="slot-dropdown-header">
            <span class="slot-dropdown-count">Type to search catalog</span>
            <span style="font-size:10px;color:#94a3b8;">ESC to close</span>
          </div>
          ${uniqueBrands.length > 0 ? `
          <div class="slot-brand-chips-row">
            <span class="slot-brand-chip active" data-brand="all">All Brands</span>
            ${uniqueBrands.slice(0, 8).map(b => `<span class="slot-brand-chip" data-brand="${escapeHtml(b)}">${escapeHtml(b)}</span>`).join('')}
          </div>` : ''}
          <div class="slot-results-list"></div>
        </div>
      </div>

      <!-- Selected Product Card -->
      <div class="slot-chosen-card" style="display:none;">
        <div class="chosen-thumb-col">
          <img src="" alt="" class="chosen-img" onerror="this.src='${escapeHtml(window.APP_URL)}/assets/images/no-image.svg';">
        </div>
        <div class="chosen-info-col">
          <div class="chosen-top-row">
            <span class="chosen-brand-badge"></span>
            <span class="chosen-stock-tag slot-stock-badge in">In Stock</span>
          </div>
          <div class="chosen-title-text" title=""></div>
          <div class="chosen-meta-row">
            <span class="chosen-price-val"></span>
          </div>
        </div>
        <div class="chosen-actions-col">
          <button type="button" class="btn-slot-action btn-slot-change" title="Choose another product">
            <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
            Change
          </button>
          <button type="button" class="btn-slot-action btn-slot-remove" title="Remove product">
            ✕
          </button>
        </div>
      </div>
    `;

    slotCard.appendChild(widget);

    // Elements
    const searchWrap = widget.querySelector('.slot-search-wrap');
    const searchInput = widget.querySelector('.slot-search-input');
    const searchClear = widget.querySelector('.slot-search-clear');
    const dropdown = widget.querySelector('.slot-dropdown-menu');
    const countEl = widget.querySelector('.slot-dropdown-count');
    const brandChips = widget.querySelectorAll('.slot-brand-chip');
    const resultsList = widget.querySelector('.slot-results-list');
    const chosenCard = widget.querySelector('.slot-chosen-card');
    const chosenImg = widget.querySelector('.chosen-img');
    const chosenBrand = widget.querySelector('.chosen-brand-badge');
    const chosenStock = widget.querySelector('.chosen-stock-tag');
    const chosenTitle = widget.querySelector('.chosen-title-text');
    const chosenPrice = widget.querySelector('.chosen-price-val');
    const btnChange = widget.querySelector('.btn-slot-change');
    const btnRemove = widget.querySelector('.btn-slot-remove');

    let activeBrandFilter = 'all';

    function renderResults(query = '') {
      const q = query.toLowerCase().trim();
      const forbiddenId = otherSel ? String(otherSel.value) : '';

      const filtered = products.filter(p => {
        if (forbiddenId && String(p.id) === forbiddenId) return false;
        if (activeBrandFilter !== 'all' && (p.brand_name || '').toLowerCase() !== activeBrandFilter.toLowerCase()) {
          return false;
        }
        if (!q) return true;
        const nameMatch = (p.name || '').toLowerCase().includes(q);
        const brandMatch = (p.brand_name || '').toLowerCase().includes(q);
        const slugMatch = (p.slug || '').toLowerCase().includes(q);
        return nameMatch || brandMatch || slugMatch;
      });

      if (countEl) {
        countEl.textContent = `${filtered.length} product${filtered.length === 1 ? '' : 's'} found`;
      }

      if (filtered.length === 0) {
        resultsList.innerHTML = `
          <div class="slot-no-results">
            No products matching "<strong>${escapeHtml(query)}</strong>"
          </div>
        `;
        return;
      }

      const displayList = filtered.slice(0, 35);
      resultsList.innerHTML = displayList.map(p => {
        const isSelected = String(p.id) === String(sel.value);
        const imgUrl = resolveProductImg(p.featured_image);
        const price = `$${parseFloat(p.base_price || 0).toFixed(2)}`;
        const brand = p.brand_name || 'Vape';
        const stockStatus = p.stock_status || 'In Stock';
        const isInStock = (stockStatus.toLowerCase() !== 'out of stock');

        return `
          <div class="slot-result-item ${isSelected ? 'selected' : ''}" data-id="${p.id}">
            <div class="slot-res-thumb">
              <img src="${escapeHtml(imgUrl)}" alt="${escapeHtml(p.name)}" onerror="this.src='${escapeHtml(window.APP_URL)}/assets/images/no-image.svg';">
            </div>
            <div class="slot-res-info">
              <span class="slot-res-brand">${escapeHtml(brand)}</span>
              <div class="slot-res-title" title="${escapeHtml(p.name)}">${escapeHtml(p.name)}</div>
              <div class="slot-res-meta">
                <span class="slot-res-price">${price}</span>
                <span class="slot-res-stock ${isInStock ? 'in' : 'out'}">${escapeHtml(stockStatus)}</span>
              </div>
            </div>
          </div>
        `;
      }).join('');

      resultsList.querySelectorAll('.slot-result-item').forEach(item => {
        item.addEventListener('click', () => {
          const id = item.dataset.id;
          selectProduct(id);
        });
      });
    }

    function openDropdown() {
      renderResults(searchInput.value);
      dropdown.style.display = 'flex';
      if (searchClear) searchClear.style.display = searchInput.value ? 'block' : 'none';
    }

    function closeDropdown() {
      dropdown.style.display = 'none';
    }

    function selectProduct(id) {
      sel.value = id;
      sel.dispatchEvent(new Event('change'));
      closeDropdown();
      searchInput.value = '';
      if (searchClear) searchClear.style.display = 'none';
      syncDisplay();
    }

    function syncDisplay() {
      const id = String(sel.value || '');
      if (id) {
        const p = getProductById(id);
        if (p) {
          chosenImg.src = resolveProductImg(p.featured_image);
          chosenBrand.textContent = p.brand_name || 'Vape';
          chosenTitle.textContent = p.name || 'Product';
          chosenTitle.title = p.name || 'Product';
          chosenPrice.textContent = `$${parseFloat(p.base_price || 0).toFixed(2)}`;
          const stock = p.stock_status || 'In Stock';
          const isInStock = (stock.toLowerCase() !== 'out of stock');
          chosenStock.textContent = stock;
          chosenStock.className = `chosen-stock-tag slot-stock-badge ${isInStock ? 'in' : 'out'}`;

          chosenCard.style.display = 'flex';
          searchWrap.style.display = 'none';
          slotCard.classList.add('has-product');
          return;
        }
      }

      // No product chosen
      chosenCard.style.display = 'none';
      slotCard.classList.remove('has-product');
      searchWrap.style.display = 'block';
    }

    searchInput.addEventListener('focus', openDropdown);
    searchInput.addEventListener('input', openDropdown);

    if (searchClear) {
      searchClear.addEventListener('click', () => {
        searchInput.value = '';
        searchClear.style.display = 'none';
        searchInput.focus();
        renderResults('');
      });
    }

    brandChips.forEach(chip => {
      chip.addEventListener('click', (e) => {
        e.stopPropagation();
        brandChips.forEach(c => c.classList.remove('active'));
        chip.classList.add('active');
        activeBrandFilter = chip.dataset.brand;
        renderResults(searchInput.value);
      });
    });

    if (btnChange) {
      btnChange.addEventListener('click', () => {
        chosenCard.style.display = 'none';
        searchWrap.style.display = 'block';
        searchInput.value = '';
        searchInput.focus();
        openDropdown();
      });
    }

    if (btnRemove) {
      btnRemove.addEventListener('click', () => {
        sel.value = '';
        sel.dispatchEvent(new Event('change'));
        syncDisplay();
        if (slotNum === 2 && typeof slotCard._onRemoveSlot2 === 'function') {
          slotCard._onRemoveSlot2();
        }
      });
    }

    document.addEventListener('click', (e) => {
      if (!slotCard.contains(e.target)) {
        closeDropdown();
      }
    });

    searchInput.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        closeDropdown();
      }
    });

    syncDisplay();
    slotCard._syncSearchable = syncDisplay;
    slotCard._focusInput = () => {
      searchInput.focus();
      openDropdown();
    };
  }

  function initShowcaseEvents(block) {
    const sel1 = block.querySelector('.slot-product-select[data-slot="1"]');
    const sel2 = block.querySelector('.slot-product-select[data-slot="2"]');
    const slotCard1 = block.querySelector('.showcase-slot-card[data-slot="1"]');
    const slotCard2 = block.querySelector('.showcase-slot-card[data-slot="2"]');
    const grid = block.querySelector('.showcase-builder-grid');
    const toggleBar = block.querySelector('.slot2-toggle-bar');
    const btnToggleSlot2 = block.querySelector('.btn-toggle-slot2-add');
    const btnRemoveSlot2 = block.querySelector('.btn-remove-slot2');
    const pill = block.querySelector('.showcase-status-pill');
    const text = block.querySelector('.showcase-status-text');

    setupSearchableSlot(slotCard1, 1, sel1, sel2, updateShowcaseState);
    setupSearchableSlot(slotCard2, 2, sel2, sel1, updateShowcaseState);

    function hideSlot2() {
      sel2.value = '';
      sel2.dispatchEvent(new Event('change'));
      if (grid) grid.classList.remove('is-comparison');
      if (slotCard2) slotCard2.style.display = 'none';
      if (toggleBar) toggleBar.style.display = '';
    }

    function showSlot2() {
      if (grid) grid.classList.add('is-comparison');
      if (slotCard2) slotCard2.style.display = '';
      if (toggleBar) toggleBar.style.display = 'none';
      if (slotCard2 && typeof slotCard2._focusInput === 'function') {
        setTimeout(slotCard2._focusInput, 50);
      }
    }

    if (slotCard2) {
      slotCard2._onRemoveSlot2 = hideSlot2;
    }

    if (btnToggleSlot2) {
      btnToggleSlot2.addEventListener('click', (e) => {
        e.preventDefault();
        showSlot2();
      });
    }

    if (btnRemoveSlot2) {
      btnRemoveSlot2.addEventListener('click', (e) => {
        e.preventDefault();
        hideSlot2();
      });
    }

    function updateShowcaseState() {
      const v1 = sel1 ? sel1.value : '';
      const v2 = sel2 ? sel2.value : '';

      if (slotCard1 && slotCard1._syncSearchable) slotCard1._syncSearchable();
      if (slotCard2 && slotCard2._syncSearchable) slotCard2._syncSearchable();

      // If slot 2 has a value, ensure it is shown
      if (v2) {
        if (grid) grid.classList.add('is-comparison');
        if (slotCard2) slotCard2.style.display = '';
        if (toggleBar) toggleBar.style.display = 'none';
      }

      // Disable selected product from other slot to prevent duplicate
      if (sel2) {
        Array.from(sel2.options).forEach(opt => {
          if (opt.value && opt.value === v1) {
            opt.disabled = true;
          } else {
            opt.disabled = false;
          }
        });
        if (v1 && v2 === v1) {
          sel2.value = '';
          if (slotCard2 && slotCard2._syncSearchable) slotCard2._syncSearchable();
        }
      }

      // Update indicator text
      const count = (v1 ? 1 : 0) + (v2 ? 1 : 0);
      const indicator = block.querySelector('.showcase-status-indicator');
      if (count === 0) {
        if (indicator) indicator.className = 'showcase-status-indicator is-empty';
        if (pill) pill.className = 'showcase-status-pill';
        if (text) text.innerHTML = '⚠️ Please select <strong>Product 1</strong> to display this showcase.';
      } else if (count === 1) {
        if (indicator) indicator.className = 'showcase-status-indicator is-spotlight';
        if (pill) pill.className = 'showcase-status-pill active';
        if (text) text.innerHTML = '⭐ <strong>1 / 2 Products Selected</strong> &bull; Storefront Mode: <strong>Spotlight Hero Card</strong>';
      } else {
        if (indicator) indicator.className = 'showcase-status-indicator is-comparison';
        if (pill) pill.className = 'showcase-status-pill active';
        if (text) text.innerHTML = '⚖️ <strong>2 / 2 Products Selected (Max limit reached)</strong> &bull; Storefront Mode: <strong>Side-by-Side Comparison Grid</strong>';
      }

      serializeSections();
    }

    if (sel1) sel1.addEventListener('change', updateShowcaseState);
    if (sel2) sel2.addEventListener('change', updateShowcaseState);

    updateShowcaseState();
  }

  function addRichTextSection(contentHtml = '') {
    if (!sectionsContainer) return;
    const block = createRichTextBlock(contentHtml);
    sectionsContainer.appendChild(block);
    updateSectionIndexes();
    serializeSections();
    const editor = block.querySelector('.rte-editor-content');
    if (editor && !contentHtml) {
      editor.focus();
    }
  }

  function addProductShowcaseSection(ids = []) {
    if (!sectionsContainer) return;
    const block = createProductShowcaseBlock(ids);
    sectionsContainer.appendChild(block);
    updateSectionIndexes();
    serializeSections();
  }

  // Connect Add Section Buttons
  document.querySelectorAll('.btn-add-richtext, .btn-add-richtext-top').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      addRichTextSection('');
    });
  });

  document.querySelectorAll('.btn-add-showcase, .btn-add-showcase-top').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      addProductShowcaseSection([]);
    });
  });

  // ==========================================================================
  // PARSE EXISTING CONTENT & RESTORE SECTIONS ON EDIT
  // ==========================================================================
  function parseAndRestoreSections() {
    if (!sectionsContainer || !masterContentInput) return;
    sectionsContainer.innerHTML = '';

    const raw = (masterContentInput.value || '').trim();
    if (!raw) {
      addRichTextSection('');
      return;
    }

    const shortcodeRegex = /\[product_showcase\s+ids=(?:&quot;|["']?)([^"'&\]\s]+)(?:&quot;|["']?)\s*\]/gi;
    let lastIndex = 0;
    let match;
    let sectionsFound = 0;

    while ((match = shortcodeRegex.exec(raw)) !== null) {
      const textBefore = raw.substring(lastIndex, match.index).trim();
      if (textBefore) {
        sectionsContainer.appendChild(createRichTextBlock(textBefore));
        sectionsFound++;
      }

      const ids = match[1].split(',').map(s => s.trim()).filter(Boolean).slice(0, 2);
      sectionsContainer.appendChild(createProductShowcaseBlock(ids));
      sectionsFound++;

      lastIndex = shortcodeRegex.lastIndex;
    }

    const textRemaining = raw.substring(lastIndex).trim();
    if (textRemaining) {
      sectionsContainer.appendChild(createRichTextBlock(textRemaining));
      sectionsFound++;
    }

    // If no shortcode was detected, this is a standard blog post -> load it whole into 1 Rich Text block
    if (sectionsFound === 0) {
      sectionsContainer.appendChild(createRichTextBlock(raw));
    }

    updateSectionIndexes();
  }

  // Initialize Sections from existing content
  parseAndRestoreSections();

  // Form Submit Handler: sync all sections and validate
  if (blogForm) {
    blogForm.addEventListener('submit', (e) => {
      serializeSections();

      // Check if any product showcase was added but no product was chosen
      const showcaseBlocks = sectionsContainer.querySelectorAll('.blog-section-block.section-type-showcase');
      for (let i = 0; i < showcaseBlocks.length; i++) {
        const blk = showcaseBlocks[i];
        const s1 = blk.querySelector('.slot-product-select[data-slot="1"]');
        if (!s1 || !s1.value) {
          e.preventDefault();
          alert(`Please select Product 1 in Product Showcase Block #${i + 1}, or remove that section before saving.`);
          s1.focus();
          return false;
        }
      }
    });
  }

  // ==========================================================================
  // SEO & TITLE & EXCERPT LIVE PREVIEWS
  // ==========================================================================
  if (toggleSeoBtn && seoFieldsContainer) {
    if (seoFieldsContainer.classList.contains('active')) {
      const span = toggleSeoBtn.querySelector('span');
      if (span) span.textContent = 'Hide website SEO';
    }

    toggleSeoBtn.addEventListener('click', (e) => {
      e.preventDefault();
      seoFieldsContainer.classList.toggle('active');
      const isNowActive = seoFieldsContainer.classList.contains('active');
      const span = toggleSeoBtn.querySelector('span');
      if (span) {
        span.textContent = isNowActive ? 'Hide website SEO' : 'Edit website SEO';
      }
    });
  }

  if (titleInput && slugInput) {
    titleInput.addEventListener('input', () => {
      if (!slugInput.dataset.manual) {
        const generated = titleInput.value.toLowerCase()
          .replace(/[^a-z0-9]+/g, '-')
          .replace(/^-+|-+$/g, '');
        slugInput.value = generated;
        if (previewSlug) previewSlug.textContent = generated || 'post-handle';
      }
      if (seoTitleInput && !seoTitleInput.value.trim() && previewTitle) {
        previewTitle.textContent = titleInput.value.trim() || 'Article Title';
      }
    });

    slugInput.addEventListener('input', () => {
      slugInput.dataset.manual = 'true';
      if (previewSlug) previewSlug.textContent = slugInput.value.trim() || 'post-handle';
    });
  }

  if (seoTitleInput) {
    seoTitleInput.addEventListener('input', () => {
      if (seoTitleCount) seoTitleCount.textContent = `${seoTitleInput.value.length}`;
      if (previewTitle) {
        previewTitle.textContent = seoTitleInput.value.trim() || (titleInput ? titleInput.value.trim() : '') || 'Article Title';
      }
    });
  }

  if (seoDescInput) {
    seoDescInput.addEventListener('input', () => {
      if (seoDescCount) seoDescCount.textContent = `${seoDescInput.value.length}`;
      if (previewDesc) {
        previewDesc.textContent = seoDescInput.value.trim() || (excerptInput ? excerptInput.value.trim() : '') || 'Write a concise meta description to see live snippet preview here.';
      }
    });
  }

  if (excerptInput) {
    excerptInput.addEventListener('input', () => {
      if (seoDescInput && !seoDescInput.value.trim() && previewDesc) {
        previewDesc.textContent = excerptInput.value.trim() || 'Write a concise meta description to see live snippet preview here.';
      }
    });
  }

  if (imgInput && imgPreview) {
    const defaultSrc = imgPreview.getAttribute('data-default-src') || imgPreview.src;
    imgInput.addEventListener('change', () => {
      const file = imgInput.files && imgInput.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          imgPreview.src = e.target.result;
        };
        reader.readAsDataURL(file);
      } else {
        imgPreview.src = defaultSrc;
      }
    });
  }
});
