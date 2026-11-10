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

    let options1Html = `<option value="">-- Choose 1st Product --</option>`;
    let options2Html = `<option value="">-- None (Single Product Spotlight) --</option>`;

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
          Select <strong>1 product</strong> for a Spotlight Hero card, or <strong>2 products</strong> for a Side-by-Side Comparison Grid. (Limit: strictly 2 products maximum per showcase block).
        </div>
        <div class="showcase-builder-grid">
          <!-- Slot 1 -->
          <div class="showcase-slot-card" data-slot="1">
            <div class="slot-header">
              <span class="slot-badge">Product 1 (Required)</span>
              <span class="slot-mode-text">Spotlight / Option 1</span>
            </div>
            <select class="admin-form-select slot-product-select" data-slot="1">
              ${options1Html}
            </select>
            <div class="slot-product-preview slot-preview-1"></div>
          </div>

          <!-- Slot 2 -->
          <div class="showcase-slot-card" data-slot="2">
            <div class="slot-header">
              <span class="slot-badge secondary">Product 2 (Optional)</span>
              <span class="slot-mode-text">Option 2 Comparison</span>
            </div>
            <select class="admin-form-select slot-product-select" data-slot="2">
              ${options2Html}
            </select>
            <div class="slot-product-preview slot-preview-2"></div>
          </div>
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

  function renderSlotPreview(slotCard, productId) {
    const previewEl = slotCard.querySelector('.slot-product-preview');
    if (!previewEl) return;

    if (!productId) {
      previewEl.innerHTML = `
        <div class="slot-empty-state">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
          <span>No product selected</span>
        </div>
      `;
      return;
    }

    const p = getProductById(productId);
    if (!p) {
      previewEl.innerHTML = '';
      return;
    }

    const imgUrl = resolveProductImg(p.featured_image);
    const price = `$${parseFloat(p.base_price || 0).toFixed(2)}`;
    const brand = p.brand_name || 'Brand';
    const stockStatus = p.stock_status || 'In Stock';
    const isInStock = (stockStatus.toLowerCase() !== 'out of stock');

    previewEl.innerHTML = `
      <div class="slot-preview-card">
        <div class="slot-preview-img-wrap">
          <img src="${escapeHtml(imgUrl)}" alt="${escapeHtml(p.name)}" onerror="this.src='${escapeHtml(window.APP_URL)}/assets/images/no-image.svg';">
        </div>
        <div class="slot-preview-details">
          <span class="slot-preview-brand">${escapeHtml(brand)}</span>
          <strong class="slot-preview-title" title="${escapeHtml(p.name)}">${escapeHtml(p.name)}</strong>
          <div class="slot-preview-meta">
            <span class="slot-preview-price">${price}</span>
            <span class="slot-stock-badge ${isInStock ? 'in' : 'out'}">${escapeHtml(stockStatus)}</span>
          </div>
        </div>
      </div>
    `;
  }

  function initShowcaseEvents(block) {
    const sel1 = block.querySelector('.slot-product-select[data-slot="1"]');
    const sel2 = block.querySelector('.slot-product-select[data-slot="2"]');
    const slotCard1 = block.querySelector('.showcase-slot-card[data-slot="1"]');
    const slotCard2 = block.querySelector('.showcase-slot-card[data-slot="2"]');
    const pill = block.querySelector('.showcase-status-pill');
    const text = block.querySelector('.showcase-status-text');

    function updateShowcaseState() {
      const v1 = sel1 ? sel1.value : '';
      const v2 = sel2 ? sel2.value : '';

      // Render previews
      renderSlotPreview(slotCard1, v1);
      renderSlotPreview(slotCard2, v2);

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
          renderSlotPreview(slotCard2, '');
        }
      }

      // Update indicator text
      const count = (v1 ? 1 : 0) + (v2 ? 1 : 0);
      if (count === 0) {
        if (pill) pill.className = 'showcase-status-pill';
        if (text) text.innerHTML = '⚠️ Please select <strong>Product 1</strong> to display this showcase.';
      } else if (count === 1) {
        if (pill) pill.className = 'showcase-status-pill active';
        if (text) text.innerHTML = '⭐ <strong>1 / 2 Products Selected</strong> &bull; Storefront Mode: <strong>Spotlight Hero Card</strong>';
      } else {
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

    const shortcodeRegex = /\[product_showcase\s+ids=["']([^"']+)["']\s*\]/gi;
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
