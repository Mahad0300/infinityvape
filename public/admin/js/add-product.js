/**
 * New Vape - Add Product Page JavaScript
 * Handles Dynamic Multi-Media Gallery Uploader, Shopify-Style Dynamic Variants Matrix,
 * Rich Text Editor, Tags, and Live SEO Preview.
 */

document.addEventListener('DOMContentLoaded', () => {
  // ==========================================================================
  // 1. Search Engine Listing (SEO) Live Preview & Toggle
  // ==========================================================================
  const toggleSeoBtn = document.getElementById('toggleSeoBtn');
  const seoFieldsContainer = document.getElementById('seoFieldsContainer');
  const seoTitleInput = document.getElementById('seoTitleInput');
  const seoDescInput = document.getElementById('seoDescInput');
  const slugInput = document.getElementById('slugInput');
  const seoTitleCount = document.getElementById('seoTitleCount');
  const seoDescCount = document.getElementById('seoDescCount');
  const seoPreviewTitle = document.getElementById('seoPreviewTitle');
  const seoPreviewDesc = document.getElementById('seoPreviewDesc');
  const seoPreviewUrl = document.getElementById('seoPreviewUrl');

  const mainTitleInput = document.querySelector('input[name="title"]') || document.getElementById('productTitle');
  const shortDescInput = document.querySelector('textarea[name="short_desc"]') || document.getElementById('productShortDesc');
  const mainDescInput = document.querySelector('textarea[name="description"]') || document.getElementById('productDescription');

  function stripHtml(html) {
    if (!html) return '';
    const tmp = document.createElement('div');
    tmp.innerHTML = html;
    const text = tmp.textContent || tmp.innerText || '';
    return text.replace(/\s+/g, ' ').trim();
  }

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

  function updateSeoPreview() {
    const mainTitle = mainTitleInput ? mainTitleInput.value.trim() : '';
    const customTitle = seoTitleInput ? seoTitleInput.value.trim() : '';
    const customDesc = seoDescInput ? seoDescInput.value.trim() : '';
    const shortDesc = shortDescInput ? shortDescInput.value.trim() : '';
    const mainDescClean = mainDescInput ? stripHtml(mainDescInput.value) : '';

    if (seoPreviewTitle) {
      seoPreviewTitle.textContent = customTitle || mainTitle || 'Title Preview';
    }

    if (seoPreviewDesc) {
      let finalDesc = customDesc || shortDesc || mainDescClean;
      if (finalDesc) {
        if (finalDesc.length > 160) {
          finalDesc = finalDesc.substring(0, 157).trim() + '...';
        }
        seoPreviewDesc.textContent = finalDesc;
      } else {
        seoPreviewDesc.textContent = 'Description will appear in search engine results...';
      }
    }

    if (seoTitleCount && seoTitleInput) {
      seoTitleCount.textContent = seoTitleInput.value.length;
    }

    if (seoDescCount && seoDescInput) {
      seoDescCount.textContent = seoDescInput.value.length;
    }

    if (slugInput && !slugInput.dataset.touched && mainTitle) {
      const autoSlug = mainTitle.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
      slugInput.value = autoSlug;
    }

    if (seoPreviewUrl) {
      const currentSlug = slugInput ? slugInput.value.trim() : '';
      seoPreviewUrl.textContent = 'www.infinityvapeshop.com/products/' + (currentSlug || 'handle');
    }
  }

  if (mainTitleInput) mainTitleInput.addEventListener('input', updateSeoPreview);
  if (shortDescInput) shortDescInput.addEventListener('input', updateSeoPreview);
  if (mainDescInput) mainDescInput.addEventListener('input', updateSeoPreview);
  if (seoTitleInput) seoTitleInput.addEventListener('input', updateSeoPreview);
  if (seoDescInput) seoDescInput.addEventListener('input', updateSeoPreview);

  if (slugInput) {
    slugInput.addEventListener('input', () => {
      slugInput.dataset.touched = 'true';
      updateSeoPreview();
    });
  }

  // Initial calculation of SEO snippet & character counters
  updateSeoPreview();

  // ==========================================================================
  // 2. Rich Text Editor (RTE) Toolbar Execution & Form Sync
  // ==========================================================================
  document.querySelectorAll('.rich-text-editor').forEach(rte => {
    const toolbar = rte.querySelector('.rte-toolbar');
    const content = rte.querySelector('.rte-editor-content');
    const textarea = rte.querySelector('textarea');

    if (toolbar && content) {
      const formatSelect = toolbar.querySelector('.rte-format-select');
      if (formatSelect) {
        formatSelect.addEventListener('change', () => {
          const val = formatSelect.value;
          document.execCommand('formatBlock', false, val);
          content.focus();
          syncRte();
        });
      }

      toolbar.querySelectorAll('button').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          const cmd = btn.getAttribute('data-command');
          const val = btn.getAttribute('data-value') || null;

          if (cmd === 'createLink') {
            const url = prompt('Enter link URL (e.g. https://... or /collection/...)', 'https://');
            if (url) {
              document.execCommand(cmd, false, url);
            }
          } else if (cmd === 'highlight') {
            const sel = window.getSelection();
            if (sel && sel.rangeCount > 0 && !sel.isCollapsed) {
              const parent = sel.anchorNode ? (sel.anchorNode.nodeType === 3 ? sel.anchorNode.parentElement : sel.anchorNode) : null;
              const isHilited = parent && (parent.tagName === 'MARK' || parent.classList.contains('rte-highlight') || (parent.style.backgroundColor && parent.style.backgroundColor !== 'transparent'));
              if (isHilited) {
                document.execCommand('hiliteColor', false, 'transparent');
                if (parent && parent.tagName === 'MARK') {
                  const txt = document.createTextNode(parent.textContent);
                  parent.parentNode.replaceChild(txt, parent);
                }
              } else {
                document.execCommand('hiliteColor', false, '#fef08a');
              }
            }
          } else if (cmd === 'unlink') {
            document.execCommand('unlink', false, null);
          } else {
            document.execCommand(cmd, false, val);
          }
          content.focus();
          syncRte();
        });
      });

      function cleanJunkStyles(container) {
        if (!container) return;
        container.querySelectorAll('*').forEach(el => {
          // Remove inline colors and fonts so theme brand styles ALWAYS apply
          el.style.color = '';
          el.style.fontFamily = '';
          el.style.fontSize = '';
          el.style.lineHeight = '';

          // Check background: only keep real deliberate yellow highlight (#fef08a)
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

          if (isYellowHighlight) {
            el.style.backgroundColor = '#fef08a';
            el.style.background = '#fef08a';
            el.classList.add('rte-highlight');
          } else {
            el.style.backgroundColor = '';
            el.style.background = '';
            el.classList.remove('rte-highlight');
            if (el.tagName === 'MARK') {
              const textNode = document.createTextNode(el.textContent);
              el.parentNode.replaceChild(textNode, el);
              return;
            }
          }

          if (el.getAttribute('style') === '') el.removeAttribute('style');
        });
      }

      function updateFormatSelect() {
        if (!formatSelect) return;
        const sel = window.getSelection();
        if (sel && sel.rangeCount > 0) {
          let node = sel.anchorNode;
          while (node && node !== content) {
            const tag = (node.tagName || '').toLowerCase();
            if (['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p'].includes(tag)) {
              formatSelect.value = tag;
              break;
            }
            node = node.parentNode;
          }
        }
      }

      function syncRte() {
        if (textarea && content) {
          const textOnly = content.innerText.trim();
          textarea.value = (textOnly === '' && !content.querySelector('img')) ? '' : content.innerHTML.trim();
          updateSeoPreview();
        }
      }

      // Initial cleanup of any junk transparent backgrounds or non-theme colors
      cleanJunkStyles(content);
      syncRte();

      ['input', 'keyup', 'blur'].forEach(evt => {
        content.addEventListener(evt, syncRte);
      });

      content.addEventListener('paste', () => {
        setTimeout(() => {
          cleanJunkStyles(content);
          syncRte();
        }, 15);
      });

      ['keyup', 'mouseup', 'click'].forEach(evt => {
        content.addEventListener(evt, updateFormatSelect);
      });

      // Synchronize on form submit
      const form = rte.closest('form');
      if (form) {
        form.addEventListener('submit', syncRte);
        document.querySelectorAll(`button[type="submit"][form="${form.id}"]`).forEach(btn => {
          btn.addEventListener('click', syncRte);
        });
      }
    }
  });

  // ==========================================================================
  // 3. Multi-Media Gallery Uploader (DataTransfer for Multiple Files)
  // ==========================================================================
  const productMediaInput = document.getElementById('productMediaInput');
  const mediaDropzone = document.getElementById('mediaDropzone');
  const mediaPreviewGrid = document.getElementById('mediaPreviewGrid');
  const featuredImageIndexInput = document.getElementById('featuredImageIndex');

  let selectedFilesDT = new DataTransfer();
  let featuredIndex = 0;

  function renderMediaPreviews() {
    if (!mediaPreviewGrid) return;
    mediaPreviewGrid.innerHTML = '';

    if (selectedFilesDT.files.length === 0) {
      mediaPreviewGrid.style.display = 'none';
      featuredIndex = 0;
      if (featuredImageIndexInput) featuredImageIndexInput.value = '0';
      return;
    }

    if (featuredIndex >= selectedFilesDT.files.length) {
      featuredIndex = 0;
    }
    if (featuredImageIndexInput) {
      featuredImageIndexInput.value = featuredIndex;
    }

    mediaPreviewGrid.classList.remove('d-none');
    mediaPreviewGrid.style.display = 'grid';

    Array.from(selectedFilesDT.files).forEach((file, idx) => {
      const isFeatured = (idx === featuredIndex);
      const reader = new FileReader();
      reader.onload = (e) => {
        const item = document.createElement('div');
        item.className = 'media-preview-item' + (isFeatured ? ' is-featured' : '');
        item.innerHTML = `
          <img src="${e.target.result}" alt="${file.name}">
          ${isFeatured ? '<span class="media-preview-badge">★ Featured</span>' : ''}
          ${!isFeatured ? '<button type="button" class="media-set-featured-btn" title="Set as featured image">Set as Featured</button>' : ''}
          <button type="button" class="media-preview-remove" data-index="${idx}" title="Remove image">&times;</button>
        `;
        mediaPreviewGrid.appendChild(item);

        // Remove button
        item.querySelector('.media-preview-remove').addEventListener('click', (ev) => {
          ev.stopPropagation();
          removeFileAtIndex(idx);
        });

        // Set as featured button or clicking card
        const setFeaturedBtn = item.querySelector('.media-set-featured-btn');
        if (setFeaturedBtn) {
          setFeaturedBtn.addEventListener('click', (ev) => {
            ev.stopPropagation();
            featuredIndex = idx;
            if (featuredImageIndexInput) featuredImageIndexInput.value = featuredIndex;
            renderMediaPreviews();
          });
        }

        item.addEventListener('click', (ev) => {
          if (ev.target.closest('.media-preview-remove')) return;
          featuredIndex = idx;
          if (featuredImageIndexInput) featuredImageIndexInput.value = featuredIndex;
          renderMediaPreviews();
        });
      };
      reader.readAsDataURL(file);
    });
  }

  function removeFileAtIndex(idxToRemove) {
    const newDT = new DataTransfer();
    Array.from(selectedFilesDT.files).forEach((f, idx) => {
      if (idx !== idxToRemove) {
        newDT.items.add(f);
      }
    });
    selectedFilesDT = newDT;
    if (productMediaInput) {
      productMediaInput.files = selectedFilesDT.files;
    }

    if (featuredIndex === idxToRemove) {
      featuredIndex = 0;
    } else if (featuredIndex > idxToRemove) {
      featuredIndex--;
    }

    renderMediaPreviews();
  }

  function addFiles(filesList) {
    Array.from(filesList).forEach(file => {
      if (file.type && file.type.startsWith('image/')) {
        selectedFilesDT.items.add(file);
      }
    });
    if (productMediaInput) {
      productMediaInput.files = selectedFilesDT.files;
    }
    renderMediaPreviews();
  }

  if (mediaDropzone && productMediaInput) {
    mediaDropzone.addEventListener('click', () => productMediaInput.click());

    productMediaInput.addEventListener('change', function () {
      if (this.files && this.files.length > 0) {
        addFiles(this.files);
      }
    });

    mediaDropzone.addEventListener('dragover', (e) => {
      e.preventDefault();
      mediaDropzone.classList.add('drag-over');
    });

    mediaDropzone.addEventListener('dragleave', (e) => {
      e.preventDefault();
      mediaDropzone.classList.remove('drag-over');
    });

    mediaDropzone.addEventListener('drop', (e) => {
      e.preventDefault();
      mediaDropzone.classList.remove('drag-over');
      if (e.dataTransfer && e.dataTransfer.files) {
        addFiles(e.dataTransfer.files);
      }
    });
  }

  // ==========================================================================
  // 4. SHOPIFY-STYLE DYNAMIC VARIANTS MANAGER (NO HARDCODED DEFAULTS)
  // ==========================================================================
  const hasVariantsToggle = document.getElementById('hasVariantsToggle');
  const variantsOptionsWrapper = document.getElementById('variantsOptionsWrapper');
  const optionsListContainer = document.getElementById('optionsListContainer');
  const addAnotherOptionLink = document.getElementById('addAnotherOptionLink');
  const variantsTableBody = document.getElementById('variantsTableBody');

  let optionCount = 0;

  // Toggle Variants Section Display
  if (hasVariantsToggle) {
    hasVariantsToggle.addEventListener('change', function () {
      if (this.checked) {
        if (variantsOptionsWrapper) variantsOptionsWrapper.style.display = 'block';
        // If no options exist, initialize with Option 1
        if (optionsListContainer && optionsListContainer.children.length === 0) {
          addOptionRow('Option 1');
        }
        rebuildVariantsMatrix();
      } else {
        if (variantsOptionsWrapper) variantsOptionsWrapper.style.display = 'none';
      }
    });
  }

  // Helper function to create a Pill Tag
  function createPill(valText) {
    const span = document.createElement('span');
    span.className = 'variant-pill-tag';
    span.setAttribute('data-val', valText);
    span.innerHTML = `<span class="pill-text">${valText}</span> <button type="button" class="pill-remove" title="Remove">&times;</button>`;
    return span;
  }

  // Validate duplicate Option Names
  function validateOptionNames() {
    if (!optionsListContainer) return true;
    const allNames = optionsListContainer.querySelectorAll('.custom-option-name-input');
    const seen = new Map();
    let hasDup = false;

    allNames.forEach(inp => {
      const val = inp.value.trim().toLowerCase();
      const wrap = inp.closest('.variant-option-header-wrap');
      let errEl = wrap ? wrap.querySelector('.option-name-error-msg') : null;

      if (val && seen.has(val)) {
        hasDup = true;
        inp.classList.add('has-error');
        if (!errEl && wrap) {
          errEl = document.createElement('div');
          errEl.className = 'option-name-error-msg';
          errEl.innerHTML = `
            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <span>Option name has already been used.</span>
          `;
          wrap.appendChild(errEl);
        }
        if (errEl) errEl.style.display = 'flex';
      } else {
        if (val) seen.set(val, inp);
        inp.classList.remove('has-error');
        if (errEl) errEl.style.display = 'none';
      }
    });
    return !hasDup;
  }

  // Add Option Row Dynamically
  function addOptionRow(suggestedName = '') {
    if (!optionsListContainer) return;
    optionCount++;
    const optId = 'opt-' + Date.now() + '-' + optionCount;

    const row = document.createElement('div');
    row.className = 'variant-option-row';
    row.setAttribute('data-option-id', optId);
    row.innerHTML = `
      <div class="variant-drag-handle" title="Drag to reorder">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
          <circle cx="9" cy="6" r="1.5"></circle>
          <circle cx="15" cy="6" r="1.5"></circle>
          <circle cx="9" cy="12" r="1.5"></circle>
          <circle cx="15" cy="12" r="1.5"></circle>
          <circle cx="9" cy="18" r="1.5"></circle>
          <circle cx="15" cy="18" r="1.5"></circle>
        </svg>
      </div>
      <div class="variant-option-content" style="flex:1;">
        <div class="variant-option-header-wrap" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; flex-wrap:wrap; gap:8px;">
          <input type="text" name="option_names[]" class="custom-option-name-input" value="${suggestedName}" placeholder="Option name (e.g. Size, Color, Flavor)">
          <button type="button" class="btn-remove-custom-option" title="Delete Option">
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
          </button>
        </div>
        <div class="variant-pills-list">
          <div class="pills-holder" style="display:inline-flex; flex-wrap:wrap; gap:8px;"></div>
          <div class="inline-tag-input-wrap">
            <input type="text" class="inline-tag-input inline-option-value-input" placeholder="+ Add value (press Enter)">
          </div>
          <div class="option-val-error-msg" style="display:none;">
            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <span class="error-text">You've already used this option value.</span>
          </div>
        </div>
      </div>
    `;

    optionsListContainer.appendChild(row);

    const nameInput = row.querySelector('.custom-option-name-input');
    const valInput = row.querySelector('.inline-option-value-input');
    const pillsHolder = row.querySelector('.pills-holder');
    const removeBtn = row.querySelector('.btn-remove-custom-option');
    const inputWrap = row.querySelector('.inline-tag-input-wrap');
    const errorMsgEl = row.querySelector('.option-val-error-msg');
    const errorTextSpan = errorMsgEl ? errorMsgEl.querySelector('.error-text') : null;

    function showValError(msg, matchingPill = null) {
      if (errorTextSpan) errorTextSpan.textContent = msg;
      if (errorMsgEl) errorMsgEl.style.display = 'flex';
      if (inputWrap) {
        inputWrap.classList.remove('has-error');
        void inputWrap.offsetWidth;
        inputWrap.classList.add('has-error');
      }
      if (matchingPill) {
        matchingPill.classList.remove('pill-duplicate-match');
        void matchingPill.offsetWidth;
        matchingPill.classList.add('pill-duplicate-match');
        setTimeout(() => {
          matchingPill.classList.remove('pill-duplicate-match');
        }, 1500);
      }
    }

    function clearValError() {
      if (errorMsgEl) errorMsgEl.style.display = 'none';
      if (inputWrap) inputWrap.classList.remove('has-error');
    }

    function findMatchingPill(val) {
      const cleanVal = (val || '').trim().toLowerCase();
      if (!cleanVal) return null;
      return Array.from(pillsHolder.querySelectorAll('.variant-pill-tag')).find(
        p => (p.getAttribute('data-val') || p.textContent.replace('×', '')).trim().toLowerCase() === cleanVal
      );
    }

    // Remove option
    removeBtn.addEventListener('click', () => {
      row.remove();
      validateOptionNames();
      rebuildVariantsMatrix();
    });

    // Update table & validate option name on change
    nameInput.addEventListener('input', () => {
      validateOptionNames();
      rebuildVariantsMatrix();
    });

    // Helper to commit typed value into pills
    function commitOptionValues() {
      const raw = valInput.value;
      if (!raw || !raw.trim()) {
        clearValError();
        return;
      }

      const parts = raw.split(',').map(s => s.trim().replace(/^,|,$/g, '')).filter(Boolean);
      let added = false;
      let duplicateVal = null;
      let duplicatePill = null;

      parts.forEach(trimmed => {
        const existing = findMatchingPill(trimmed);
        if (!existing) {
          const pill = createPill(trimmed);
          pillsHolder.appendChild(pill);
          added = true;
        } else {
          duplicateVal = trimmed;
          duplicatePill = existing;
        }
      });

      if (duplicateVal) {
        valInput.value = duplicateVal;
        valInput.focus();
        showValError(`You've already used the option value "${duplicateVal}".`, duplicatePill);
      } else {
        valInput.value = '';
        clearValError();
      }

      if (added) {
        rebuildVariantsMatrix();
      }
    }

    // Add pill on Enter or comma or Tab
    valInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        commitOptionValues();
      } else if (e.key === 'Tab' && valInput.value.trim()) {
        const existing = findMatchingPill(valInput.value.trim());
        if (existing) {
          e.preventDefault();
          commitOptionValues();
        } else {
          commitOptionValues();
        }
      }
    });

    // Auto-commit on blur
    valInput.addEventListener('blur', () => {
      const raw = valInput.value.trim();
      if (!raw) {
        clearValError();
        return;
      }
      const existing = findMatchingPill(raw);
      if (existing) {
        showValError(`You've already used the option value "${raw}".`, existing);
      } else {
        commitOptionValues();
      }
    });

    // Real-time check while typing (Shopify style)
    valInput.addEventListener('input', () => {
      if (valInput.value.includes(',')) {
        commitOptionValues();
        return;
      }
      const typed = valInput.value.trim();
      if (!typed) {
        clearValError();
        return;
      }
      const existing = findMatchingPill(typed);
      if (existing) {
        const origVal = existing.getAttribute('data-val') || typed;
        showValError(`You've already used the option value "${origVal}".`, existing);
      } else {
        clearValError();
      }
    });

    validateOptionNames();

    // Focus input
    if (!suggestedName) {
      nameInput.focus();
    } else {
      valInput.focus();
    }
  }

  if (addAnotherOptionLink) {
    addAnotherOptionLink.addEventListener('click', (e) => {
      e.preventDefault();
      rebuildVariantsMatrix();
      const currentCount = optionsListContainer ? optionsListContainer.children.length : 0;
      addOptionRow(`Option ${currentCount + 1}`);
    });
  }

  // Handle Pill Tag Removal
  document.addEventListener('click', (e) => {
    const pillRemoveBtn = e.target.closest('.pill-remove');
    if (pillRemoveBtn) {
      const pill = pillRemoveBtn.closest('.variant-pill-tag');
      if (pill) {
        pill.remove();
        rebuildVariantsMatrix();
      }
    }
  });

  // Helper to resolve clean asset URLs without breaking under <base href>
  function getAssetUrl(path) {
    if (!path) return '';
    if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('data:')) {
      return path;
    }
    let baseUrl = (window.APP_BASE_URL || '').replace(/\/+$/, '');
    if (!baseUrl) {
      const baseEl = document.querySelector('base');
      if (baseEl && baseEl.href) {
        baseUrl = baseEl.href.replace(/\/admin\/?$/i, '').replace(/\/+$/, '');
      } else {
        const pathParts = window.location.pathname.split('/');
        baseUrl = window.location.origin + (pathParts[1] ? '/' + pathParts[1] : '');
      }
    }
    const cleanPath = path.replace(/^\/+/, '');
    return baseUrl ? `${baseUrl}/${cleanPath}` : `/${cleanPath}`;
  }

  // Persistent cache for variant row values
  let cachedVariantsMap = {};
  let groupImageMap = {};
  let customImageMap = {};

  // Helper to sync an uploaded variant image to the "Media Assets & Product Images" box
  function syncVariantImageToMediaGallery(imageUrl, assetUrl) {
    if (!imageUrl || !mediaPreviewGrid) return;
    const cleanImg = imageUrl.replace(/^\/+/, '');
    const existingInput = mediaPreviewGrid.querySelector(`input[name="existing_images[]"][value="${cleanImg}"], input[name="existing_images[]"][value="${imageUrl}"]`);
    if (existingInput) return;

    mediaPreviewGrid.classList.remove('d-none');
    mediaPreviewGrid.style.display = 'grid';

    const isFirstEver = mediaPreviewGrid.querySelectorAll('.media-preview-item').length === 0;
    const item = document.createElement('div');
    item.className = 'media-preview-item' + (isFirstEver ? ' is-featured' : '');
    item.setAttribute('data-existing', '1');
    item.innerHTML = `
      <img src="${assetUrl || getAssetUrl(imageUrl)}" alt="Product variant asset">
      <input type="hidden" name="existing_images[]" value="${cleanImg}">
      <button type="button" class="media-preview-remove btn-remove-existing-img" title="Remove">&times;</button>
      ${isFirstEver ? '<span class="media-preview-badge">★ Featured</span>' : '<button type="button" class="media-set-featured-btn" title="Set as featured image">Set as Featured</button>'}
    `;

    bindMediaItemEvents(item);
    mediaPreviewGrid.appendChild(item);

    if (isFirstEver) {
      const pImg = document.getElementById('productImageUrl');
      if (pImg) pImg.value = cleanImg;
    }
  }

  function handleMediaItemRemoval(item) {
    if (!item) return;
    const input = item.querySelector('input[name="existing_images[]"]');
    const removedVal = input ? input.value.trim() : '';
    const cleanRemoved = removedVal ? removedVal.replace(/^\/+/, '') : '';
    const wasFeatured = item.classList.contains('is-featured');

    item.remove();

    const remaining = mediaPreviewGrid.querySelectorAll('.media-preview-item');
    if (wasFeatured && remaining.length > 0) {
      setFeaturedMediaItem(remaining[0]);
    } else if (remaining.length === 0) {
      const pImg = document.getElementById('productImageUrl');
      if (pImg) pImg.value = '';
      mediaPreviewGrid.style.display = 'none';
    }

    if (cleanRemoved) {
      Object.keys(groupImageMap).forEach(gVal => {
        const gImg = (groupImageMap[gVal] || '').replace(/^\/+/, '');
        if (gImg === cleanRemoved) {
          groupImageMap[gVal] = '';
          const pTr = variantsTableBody.querySelector(`.variant-parent-row[data-group-name="${gVal}"]`);
          if (pTr) {
            const pImg = pTr.querySelector('.group-thumb-img');
            const pPh = pTr.querySelector('.variant-thumb-placeholder');
            const pThumb = pTr.querySelector('.group-thumb');
            const pClr = pTr.querySelector('.group-thumb-clear-btn');
            if (pImg) { pImg.src = ''; pImg.style.display = 'none'; }
            if (pPh) pPh.style.display = 'flex';
            if (pThumb) pThumb.style.border = '1px dashed #cbd5e1';
            if (pClr) pClr.style.display = 'none';
          }
        }
      });

      Object.keys(customImageMap).forEach(combo => {
        const cImg = (customImageMap[combo] || '').replace(/^\/+/, '');
        if (cImg === cleanRemoved) {
          delete customImageMap[combo];
        }
      });

      Object.keys(cachedVariantsMap).forEach(combo => {
        if (cachedVariantsMap[combo] && cachedVariantsMap[combo].image) {
          const cImg = cachedVariantsMap[combo].image.replace(/^\/+/, '');
          if (cImg === cleanRemoved) {
            cachedVariantsMap[combo].image = '';
            cachedVariantsMap[combo].imgPreview = '';
          }
        }
      });

      const allRows = variantsTableBody.querySelectorAll('.variant-matrix-row');
      allRows.forEach(tr => {
        const hiddenInput = tr.querySelector('.variant-img-url');
        const rowVal = hiddenInput ? (hiddenInput.value || '').replace(/^\/+/, '') : '';
        if (rowVal === cleanRemoved) {
          if (hiddenInput) hiddenInput.value = '';
          const cImg = tr.querySelector('.variant-thumb-img');
          const cPh = tr.querySelector('.variant-thumb-placeholder');
          const cThumb = tr.querySelector('.shopify-variant-thumb');
          const cClr = tr.querySelector('.variant-thumb-clear-btn');
          if (cImg) { cImg.src = ''; cImg.style.display = 'none'; }
          if (cPh) cPh.style.display = 'flex';
          if (cThumb) cThumb.style.border = '1px dashed #cbd5e1';
          if (cClr) cClr.style.display = 'none';
          tr.removeAttribute('data-has-custom-img');
        }
      });
    }
  }

  function setFeaturedMediaItem(item) {
    if (!item || !mediaPreviewGrid) return;
    mediaPreviewGrid.querySelectorAll('.media-preview-item').forEach(it => {
      it.classList.remove('is-featured');
      const b = it.querySelector('.media-preview-badge');
      if (b) b.remove();
      if (!it.querySelector('.media-set-featured-btn')) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'media-set-featured-btn';
        btn.title = 'Set as featured image';
        btn.textContent = 'Set as Featured';
        btn.addEventListener('click', function(e) {
          e.stopPropagation();
          setFeaturedMediaItem(it);
        });
        it.appendChild(btn);
      }
    });

    item.classList.add('is-featured');
    const existingBtn = item.querySelector('.media-set-featured-btn');
    if (existingBtn) existingBtn.remove();

    const badge = document.createElement('span');
    badge.className = 'media-preview-badge';
    badge.innerHTML = '★ Featured';
    item.appendChild(badge);

    mediaPreviewGrid.prepend(item);

    const input = item.querySelector('input[name="existing_images[]"], input[name="new_images[]"]');
    const val = input ? input.value.trim() : '';
    const productImageUrlInput = document.getElementById('productImageUrl');
    if (productImageUrlInput && val) {
      productImageUrlInput.value = val;
    }
    const featuredIndexInput = document.getElementById('featuredImageIndex');
    if (featuredIndexInput) {
      featuredIndexInput.value = '0';
    }
  }

  function bindMediaItemEvents(item) {
    if (!item) return;
    const removeBtn = item.querySelector('.media-preview-remove');
    if (removeBtn && !removeBtn.dataset.bound) {
      removeBtn.dataset.bound = '1';
      removeBtn.addEventListener('click', function(ev) {
        ev.stopPropagation();
        handleMediaItemRemoval(item);
      });
    }

    const setFeaturedBtn = item.querySelector('.media-set-featured-btn');
    if (setFeaturedBtn && !setFeaturedBtn.dataset.bound) {
      setFeaturedBtn.dataset.bound = '1';
      setFeaturedBtn.addEventListener('click', function(ev) {
        ev.stopPropagation();
        setFeaturedMediaItem(item);
      });
    }
  }

  // Rebuild the Variant Matrix Table from Options (Authentic Shopify Style)
  function rebuildVariantsMatrix() {
    if (!variantsTableBody || !optionsListContainer) return;

    // 1. Cache current user modifications from the DOM
    variantsTableBody.querySelectorAll('.variant-matrix-row').forEach(row => {
      const name = row.getAttribute('data-combo-name');
      if (name) {
        cachedVariantsMap[name] = {
          price: row.querySelector('.variant-price-input')?.value,
          stock: row.querySelector('.variant-stock-input')?.value,
          sku: row.querySelector('.variant-sku-input')?.value,
          image: row.querySelector('.variant-img-url')?.value,
          imgPreview: row.querySelector('.variant-thumb-img')?.src,
          selected: row.querySelector('.variant-row-select')?.checked
        };
      }
    });

    // 2. Collect options and their values
    const optionRows = optionsListContainer.querySelectorAll('.variant-option-row');
    const optionsData = [];

    optionRows.forEach((row, idx) => {
      // Auto-commit any uncommitted text currently in this option's input field
      const valInput = row.querySelector('.inline-option-value-input');
      const pillsHolder = row.querySelector('.pills-holder');
      if (valInput && valInput.value.trim() && pillsHolder) {
        const raw = valInput.value;
        const parts = raw.split(',').map(s => s.trim().replace(/^,|,$/g, '')).filter(Boolean);
        let dupVal = null;
        let dupPill = null;
        parts.forEach(trimmed => {
          const existing = Array.from(pillsHolder.querySelectorAll('.variant-pill-tag')).find(
            p => (p.getAttribute('data-val') || p.textContent.replace('×', '')).trim().toLowerCase() === trimmed.toLowerCase()
          );
          if (!existing) {
            pillsHolder.appendChild(createPill(trimmed));
          } else {
            dupVal = trimmed;
            dupPill = existing;
          }
        });
        if (dupVal) {
          valInput.value = dupVal;
          const inputWrap = row.querySelector('.inline-tag-input-wrap');
          const errorMsgEl = row.querySelector('.option-val-error-msg');
          const errorTextSpan = errorMsgEl ? errorMsgEl.querySelector('.error-text') : null;
          if (errorTextSpan) errorTextSpan.textContent = `You've already used the option value "${dupVal}".`;
          if (errorMsgEl) errorMsgEl.style.display = 'flex';
          if (inputWrap) inputWrap.classList.add('has-error');
          if (dupPill) {
            dupPill.classList.add('pill-duplicate-match');
            setTimeout(() => dupPill.classList.remove('pill-duplicate-match'), 1500);
          }
        } else {
          valInput.value = '';
          const inputWrap = row.querySelector('.inline-tag-input-wrap');
          const errorMsgEl = row.querySelector('.option-val-error-msg');
          if (errorMsgEl) errorMsgEl.style.display = 'none';
          if (inputWrap) inputWrap.classList.remove('has-error');
        }
      }

      const nameInput = row.querySelector('.custom-option-name-input');
      const optName = nameInput && nameInput.value.trim() ? nameInput.value.trim() : `Option ${idx + 1}`;
      const pills = row.querySelectorAll('.pills-holder .variant-pill-tag');
      const values = Array.from(pills).map(p => p.getAttribute('data-val') || p.textContent.replace('×', '').trim()).filter(Boolean);

      if (values.length > 0) {
        optionsData.push({ name: optName, values: values });
      }
    });

    const countBadge = document.getElementById('variantsCountBadge');
    const bulkGroup = document.getElementById('bulkActionsGroup');

    // If no values in any options, show empty state
    if (optionsData.length === 0) {
      variantsTableBody.innerHTML = `
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
      `;
      if (countBadge) countBadge.textContent = '0 variants';
      if (bulkGroup) bulkGroup.style.display = 'none';
      return;
    }

    // 3. Automatically group by primary option (Option 1) when 2 or more options exist (Authentic Shopify style)
    const selectedGroup = optionsData.length >= 2 ? optionsData[0].name : '_none_';

    // 4. Cartesian product of option values
    function cartesian(arr) {
      return arr.reduce((acc, curr) => {
        return acc.flatMap(c => curr.values.map(v => [...c, v]));
      }, [[]]);
    }

    const combinations = cartesian(optionsData);

    const basePriceEl = document.getElementById('productBasePrice');
    const basePrice = (basePriceEl && basePriceEl.value !== '' && !isNaN(parseFloat(basePriceEl.value)))
      ? parseFloat(basePriceEl.value).toFixed(2)
      : '';

    const baseStockEl = document.getElementById('productStock');
    const baseStock = (baseStockEl && baseStockEl.value !== '' && !isNaN(parseInt(baseStockEl.value)))
      ? parseInt(baseStockEl.value)
      : 0;

    const baseSkuEl = document.getElementById('productSku');
    const baseSku = baseSkuEl && baseSkuEl.value ? baseSkuEl.value.trim() : 'VH-PROD';

    variantsTableBody.innerHTML = '';

    // Global counter for variant array inputs: variants[idx][...]
    let globalVariantIndex = 0;
    const usedSkusInTable = new Set();

    function makeSkuPart(str) {
      const clean = (str || '').replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
      if (!clean) return '';
      if (clean.length <= 6) return clean;
      const digitsMatch = clean.match(/\d+$/);
      if (digitsMatch) {
        const digits = digitsMatch[0];
        const prefixLen = Math.max(1, 6 - digits.length);
        return clean.slice(0, prefixLen) + digits;
      }
      return clean.slice(0, 6);
    }

    // Determine if we should group
    const shouldGroup = selectedGroup !== '_none_' && optionsData.length >= 2;
    const groupOptIndex = shouldGroup 
      ? optionsData.findIndex(o => o.name.trim().toLowerCase() === selectedGroup.trim().toLowerCase()) 
      : -1;

    if (shouldGroup && groupOptIndex !== -1) {
      // Group combinations by the selected option value
      const groupMap = new Map();
      combinations.forEach(combo => {
        const groupVal = combo[groupOptIndex];
        if (!groupMap.has(groupVal)) {
          groupMap.set(groupVal, []);
        }
        groupMap.get(groupVal).push(combo);
      });

      let groupCounter = 0;
      groupMap.forEach((combosInGroup, groupVal) => {
        const groupId = 'grp-' + groupCounter;
        groupCounter++;
        
        // Calculate group totals
        let groupTotalStock = 0;
        const pricesInGroup = [];
        let firstGroupImg = '';

        combosInGroup.forEach(combo => {
          const comboName = combo.join(' / ');
          const cached = cachedVariantsMap[comboName] || {};
          const rowStock = cached.stock !== undefined && cached.stock !== '' ? parseInt(cached.stock) : baseStock;
          const rowPrice = cached.price !== undefined && cached.price !== '' 
            ? parseFloat(cached.price) 
            : (basePrice !== '' ? parseFloat(basePrice) : NaN);
          const rowImg = cached.imgPreview || cached.image || '';

          groupTotalStock += isNaN(rowStock) ? 0 : rowStock;
          if (!isNaN(rowPrice)) pricesInGroup.push(rowPrice);
          if (!firstGroupImg && rowImg) firstGroupImg = rowImg;
        });

        const minP = pricesInGroup.length > 0 ? Math.min(...pricesInGroup).toFixed(2) : (basePrice || '0.00');
        const maxP = pricesInGroup.length > 0 ? Math.max(...pricesInGroup).toFixed(2) : (basePrice || '0.00');
        const groupPriceDisplay = pricesInGroup.length > 0 
          ? (minP === maxP ? minP : `${minP} – $${maxP}`) 
          : (basePrice ? basePrice : '0.00');

        // 1. Create Parent Group Row (No variant count badge, clean available column)
        const parentTr = document.createElement('tr');
        parentTr.className = 'variant-parent-row';
        parentTr.setAttribute('data-group-id', groupId);
        parentTr.setAttribute('data-group-name', groupVal);
        parentTr.innerHTML = `
          <td style="width: 44px; text-align: center; padding: 12px 8px;">
            <input type="checkbox" class="variant-group-cb" data-group-id="${groupId}" style="width: 16px; height: 16px; accent-color: #0f172a; cursor: pointer;">
          </td>
          <td style="padding: 12px 14px;">
            <div style="display: flex; align-items: center; gap: 10px;">
              <button type="button" class="variant-toggle-btn open" data-group-id="${groupId}" title="Collapse/Expand group">
                <svg class="chevron-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" style="transform: rotate(0deg); transition: transform 0.2s ease;">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </button>
              <div class="group-toggle-area" style="display: flex; align-items: center; gap: 10px; cursor: pointer; user-select: none;">
                <div class="shopify-variant-thumb group-thumb" data-group-id="${groupId}" title="Click to upload photo for all ${groupVal} variants" style="width: 36px; height: 36px; border-radius: 6px; border: ${firstGroupImg ? '1px solid #e2e8f0' : '1px dashed #cbd5e1'}; background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; cursor: pointer; position: relative;">
                  <img class="variant-thumb-img group-thumb-img" src="${getAssetUrl(firstGroupImg)}" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';" style="${firstGroupImg ? 'width: 100%; height: 100%; object-fit: cover; display: block;' : 'display: none;'}">
                  <div class="variant-thumb-placeholder" style="${firstGroupImg ? 'display: none;' : 'display: flex; align-items: center; justify-content: center;'}">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#94a3b8" stroke-width="1.8">
                      <rect x="3" y="3" width="18" height="18" rx="3" ry="3"></rect>
                      <circle cx="8.5" cy="8.5" r="1.5"></circle>
                      <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                  </div>
                  <button type="button" class="group-thumb-clear-btn" title="Remove image from group" style="${firstGroupImg ? 'display: flex;' : 'display: none;'}">&times;</button>
                  <input type="file" class="group-img-file" accept="image/*" style="display: none;">
                </div>
                <div>
                  <span class="variant-group-title" style="font-size: 13.5px; font-weight: 700; color: #0f172a;">${groupVal}</span>
                </div>
              </div>
            </div>
          </td>
          <td style="padding: 12px 14px;">
            <span class="group-price-text" style="font-size: 13px; font-weight: 600; color: #0f172a;">$${groupPriceDisplay}</span>
          </td>
          <td style="padding: 12px 14px;">
            <span class="group-stock-badge" style="font-size: 13px; font-weight: 600; color: #0f172a; white-space: nowrap;">
              ${groupTotalStock} <span style="font-weight: 400; color: #64748b; font-size: 12px;">available</span>
            </span>
          </td>
          <td style="padding: 12px 14px;">
            <span style="font-size: 12px; color: #94a3b8;">—</span>
          </td>
        `;

        // Expand/collapse logic function
        function toggleGroup() {
          const btn = parentTr.querySelector('.variant-toggle-btn');
          const chevron = parentTr.querySelector('.chevron-icon');
          const isCurrentlyOpen = btn ? btn.classList.contains('open') : true;
          const subrows = variantsTableBody.querySelectorAll(`.variant-subrow[data-group-id="${groupId}"]`);
          
          if (isCurrentlyOpen) {
            if (btn) btn.classList.remove('open');
            if (chevron) chevron.style.transform = 'rotate(-90deg)';
            subrows.forEach(r => {
              r.classList.add('is-collapsed');
              r.style.display = 'none';
            });
          } else {
            if (btn) btn.classList.add('open');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
            subrows.forEach(r => {
              r.classList.remove('is-collapsed');
              r.style.display = '';
            });
          }
        }

        // Toggle on parent row click (ignore clicks on checkboxes, inputs, or group-thumb)
        parentTr.addEventListener('click', function(e) {
          if (e.target.closest('.variant-group-cb') || e.target.closest('input') || e.target.closest('.group-thumb')) {
            return;
          }
          toggleGroup();
        });

        // Toggle on chevron click
        const toggleBtn = parentTr.querySelector('.variant-toggle-btn');
        if (toggleBtn) {
          toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleGroup();
          });
        }

        // Also toggle on title click (excluding thumbnail)
        const toggleArea = parentTr.querySelector('.group-toggle-area');
        if (toggleArea) {
          toggleArea.addEventListener('click', function(e) {
            if (e.target.closest('.group-thumb')) return;
            e.stopPropagation();
            toggleGroup();
          });
        }

        // Parent Flavor Row Image Upload (Applies to all variants under this flavor)
        const groupThumb = parentTr.querySelector('.group-thumb');
        const groupFileInput = parentTr.querySelector('.group-img-file');
        const groupThumbImg = parentTr.querySelector('.group-thumb-img');
        const groupPlaceholder = parentTr.querySelector('.variant-thumb-placeholder');
        const groupClrBtn = parentTr.querySelector('.group-thumb-clear-btn');

        if (groupClrBtn) {
          groupClrBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            groupImageMap[groupVal] = '';
            if (groupThumbImg) {
              groupThumbImg.src = '';
              groupThumbImg.style.display = 'none';
            }
            if (groupPlaceholder) groupPlaceholder.style.display = 'flex';
            groupThumb.style.border = '1px dashed #cbd5e1';
            groupClrBtn.style.display = 'none';
            if (groupFileInput) groupFileInput.value = '';

            const childRows = variantsTableBody.querySelectorAll(`.variant-subrow[data-group-id="${groupId}"]`);
            childRows.forEach(cRow => {
              const combo = cRow.getAttribute('data-combo-name');
              if (combo && !customImageMap[combo]) {
                if (cachedVariantsMap[combo]) {
                  cachedVariantsMap[combo].image = '';
                  cachedVariantsMap[combo].imgPreview = '';
                }
                const cImg = cRow.querySelector('.variant-thumb-img');
                const cPh = cRow.querySelector('.variant-thumb-placeholder');
                const cThumb = cRow.querySelector('.child-thumb');
                const cHidden = cRow.querySelector('.variant-img-url');
                const cClr = cRow.querySelector('.variant-thumb-clear-btn');
                if (cImg) { cImg.src = ''; cImg.style.display = 'none'; }
                if (cPh) cPh.style.display = 'flex';
                if (cThumb) cThumb.style.border = '1px dashed #cbd5e1';
                if (cHidden) cHidden.value = '';
                if (cClr) cClr.style.display = 'none';
              }
            });
          });
        }

        if (groupThumb && groupFileInput) {
          groupThumb.addEventListener('click', (e) => {
            if (e.target.closest('.group-thumb-clear-btn')) return;
            e.stopPropagation();
            groupFileInput.click();
          });

          groupFileInput.addEventListener('change', function () {
            if (!this.files || !this.files[0]) return;
            const file = this.files[0];

            // 1. Immediate local preview on parent and all child rows
            const reader = new FileReader();
            reader.onload = (e) => {
              const dataUrl = e.target.result;

              if (groupThumbImg) {
                groupThumbImg.src = dataUrl;
                groupThumbImg.style.display = 'block';
              }
              if (groupPlaceholder) groupPlaceholder.style.display = 'none';
              groupThumb.style.border = '1px solid #e2e8f0';
              if (groupClrBtn) groupClrBtn.style.display = 'flex';

              const childRows = variantsTableBody.querySelectorAll(`.variant-subrow[data-group-id="${groupId}"]`);
              childRows.forEach(cRow => {
                const combo = cRow.getAttribute('data-combo-name');
                if (combo && !customImageMap[combo]) {
                  const cImg = cRow.querySelector('.variant-thumb-img');
                  const cPh = cRow.querySelector('.variant-thumb-placeholder');
                  const cThumb = cRow.querySelector('.child-thumb');
                  const cClr = cRow.querySelector('.variant-thumb-clear-btn');
                  if (cImg) {
                    cImg.src = dataUrl;
                    cImg.style.display = 'block';
                  }
                  if (cPh) cPh.style.display = 'none';
                  if (cThumb) cThumb.style.border = '1px solid #e2e8f0';
                  if (cClr) cClr.style.display = 'flex';
                }
              });
            };
            reader.readAsDataURL(file);

            // 2. Upload to server asynchronously and set image_url on all children
            const uploadUrl = new URL('upload-variant-image', document.baseURI).href;
            const fd = new FormData();
            fd.append('image', file);

            fetch(uploadUrl, { method: 'POST', body: fd })
              .then(r => r.json())
              .then(data => {
                if (data && data.success && data.image_url) {
                  const resolvedAssetUrl = data.asset_url || getAssetUrl(data.image_url);
                  groupImageMap[groupVal] = data.image_url;

                  if (groupThumbImg) {
                    groupThumbImg.src = resolvedAssetUrl;
                    groupThumbImg.style.display = 'block';
                  }
                  if (groupPlaceholder) groupPlaceholder.style.display = 'none';

                  // Sync into Media Assets card immediately
                  syncVariantImageToMediaGallery(data.image_url, resolvedAssetUrl);

                  const childRows = variantsTableBody.querySelectorAll(`.variant-subrow[data-group-id="${groupId}"]`);
                  childRows.forEach(cRow => {
                    const combo = cRow.getAttribute('data-combo-name');
                    if (combo && !customImageMap[combo]) {
                      cachedVariantsMap[combo] = cachedVariantsMap[combo] || {};
                      cachedVariantsMap[combo].image = data.image_url;
                      cachedVariantsMap[combo].imgPreview = resolvedAssetUrl;

                      const cImg = cRow.querySelector('.variant-thumb-img');
                      const cPh = cRow.querySelector('.variant-thumb-placeholder');
                      if (cImg) {
                        cImg.src = resolvedAssetUrl;
                        cImg.style.display = 'block';
                      }
                      if (cPh) cPh.style.display = 'none';

                      const hiddenInput = cRow.querySelector('.variant-img-url');
                      if (hiddenInput) hiddenInput.value = data.image_url;
                    }
                  });
                }
              })
              .catch(err => console.warn('Group image upload error:', err));
          });
        }

        // Parent checkbox selects all children in group
        parentTr.querySelector('.variant-group-cb').addEventListener('change', function() {
          const isChecked = this.checked;
          const childCbs = variantsTableBody.querySelectorAll(`.variant-row-select[data-group-id="${groupId}"]`);
          childCbs.forEach(cb => {
            cb.checked = isChecked;
            const row = cb.closest('.variant-matrix-row');
            if (row) row.style.background = isChecked ? '#f1f5f9' : '';
          });
          updateVariantsSelectionBar();
        });

        variantsTableBody.appendChild(parentTr);

        // 2. Create Child Rows for this group
        combosInGroup.forEach(combo => {
          const comboName = combo.join(' / ');
          const subOptionParts = combo.filter((_, idx) => idx !== groupOptIndex);
          const subOptionLabel = subOptionParts.join(' / ');
          const skuSuffix = combo.map(makeSkuPart).filter(Boolean).join('-');
          const defaultSku = `${baseSku}-${skuSuffix || (globalVariantIndex + 1)}`;

          const cached = cachedVariantsMap[comboName] || {};
          const rowPrice = cached.price !== undefined && cached.price !== '' ? cached.price : basePrice;
          const rowStock = cached.stock !== undefined && cached.stock !== '' ? cached.stock : baseStock;
          
          let candidateSku = cached.sku && cached.sku.trim() ? cached.sku.trim() : defaultSku;
          let rowSku = candidateSku;
          let skuCounter = 1;
          while (usedSkusInTable.has(rowSku.toUpperCase())) {
            rowSku = `${candidateSku}-${skuCounter++}`;
          }
          usedSkusInTable.add(rowSku.toUpperCase());

          const rowImage = cached.image || '';
          const rowImgPreview = cached.imgPreview || rowImage;
          const isSelected = cached.selected || false;

          const tr = document.createElement('tr');
          tr.className = 'variant-matrix-row variant-subrow show';
          tr.setAttribute('data-group-id', groupId);
          tr.setAttribute('data-combo-name', comboName);
          tr.style.cssText = 'border-bottom: 1px solid #f1f5f9; transition: background 0.15s ease;' + (isSelected ? ' background: #f1f5f9;' : '');
          tr.innerHTML = `
            <td style="width: 44px; text-align: center; padding: 10px 8px;">
              <input type="checkbox" class="variant-row-select" ${isSelected ? 'checked' : ''} data-group-id="${groupId}" style="width: 16px; height: 16px; accent-color: #0f172a; cursor: pointer;">
            </td>
            <td style="padding: 10px 14px;">
              <div style="display: flex; align-items: center; gap: 10px; padding-left: 28px;">
                <span class="variant-subrow-bullet" style="width: 6px; height: 6px; border-radius: 50%; background: #94a3b8; flex-shrink: 0;"></span>
                <div class="shopify-variant-thumb child-thumb" data-row-idx="${globalVariantIndex}" title="Click to upload variant image" style="width: 32px; height: 32px; border-radius: 5px; border: ${rowImgPreview ? '1px solid #e2e8f0' : '1px dashed #cbd5e1'}; background: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; position: relative; overflow: hidden; flex-shrink: 0;">
                  <img class="variant-thumb-img" src="${getAssetUrl(rowImgPreview)}" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';" style="${rowImgPreview ? 'width: 100%; height: 100%; object-fit: cover; display: block;' : 'display: none;'}">
                  <div class="variant-thumb-placeholder" style="${rowImgPreview ? 'display: none;' : 'display: flex; align-items: center; justify-content: center;'}">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="#94a3b8" stroke-width="1.8">
                      <rect x="3" y="3" width="18" height="18" rx="3" ry="3"></rect>
                      <circle cx="8.5" cy="8.5" r="1.5"></circle>
                      <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                  </div>
                  <button type="button" class="variant-thumb-clear-btn" title="Remove image" style="${rowImgPreview ? 'display: flex;' : 'display: none;'}">&times;</button>
                <input type="file" name="variant_images[${globalVariantIndex}]" class="variant-img-file" accept="image/*" style="display: none;">
                  <input type="hidden" name="variants[${globalVariantIndex}][image_url]" class="variant-img-url" value="${rowImage}">
                </div>
                <div style="min-width: 0;">
                  <div class="variant-subrow-title" style="font-weight: 600; color: #1e293b; font-size: 13px;">${subOptionLabel}</div>
                  <input type="hidden" name="variants[${globalVariantIndex}][name]" value="${comboName}">
                </div>
              </div>
            </td>
            <td style="padding: 10px 14px;">
              <div class="variant-price-box" style="height: 34px; width: 110px; display: inline-flex; align-items: center; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0 8px; background: #fff;">
                <span style="color: #64748b; font-size: 13px; font-weight: 500; margin-right: 4px; user-select: none;">$</span>
                <input type="number" step="0.01" name="variants[${globalVariantIndex}][price]" class="variant-price-input" value="${rowPrice}" placeholder="0.00" style="border: none; outline: none; width: 100%; font-size: 13px; font-weight: 500; color: #0f172a; padding: 0; background: transparent;">
              </div>
            </td>
            <td style="padding: 10px 14px;">
              <input type="number" name="variants[${globalVariantIndex}][stock]" class="admin-form-input variant-stock-input" style="height: 34px; width: 85px; font-size: 13px; font-weight: 500; padding: 0 8px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;" value="${rowStock}" min="0">
            </td>
            <td style="padding: 10px 14px;">
              <input type="text" name="variants[${globalVariantIndex}][sku]" class="admin-form-input variant-sku-input" style="height: 34px; font-size: 12.5px; padding: 0 8px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; width: 100%; max-width: 170px;" value="${rowSku}">
            </td>
          `;

          attachRowEvents(tr, groupId, parentTr, groupVal);
          variantsTableBody.appendChild(tr);
          globalVariantIndex++;
        });
      });
    } else {
      // Flat list rendering (Single option or user chose "_none_")
      combinations.forEach(combo => {
        const comboName = combo.join(' / ');
        const skuSuffix = combo.map(makeSkuPart).filter(Boolean).join('-');
        const defaultSku = `${baseSku}-${skuSuffix || (globalVariantIndex + 1)}`;

        const cached = cachedVariantsMap[comboName] || {};
        const rowPrice = cached.price !== undefined && cached.price !== '' ? cached.price : basePrice;
        const rowStock = cached.stock !== undefined && cached.stock !== '' ? cached.stock : baseStock;
        
        let candidateSku = cached.sku && cached.sku.trim() ? cached.sku.trim() : defaultSku;
        let rowSku = candidateSku;
        let skuCounter = 1;
        while (usedSkusInTable.has(rowSku.toUpperCase())) {
          rowSku = `${candidateSku}-${skuCounter++}`;
        }
        usedSkusInTable.add(rowSku.toUpperCase());

        const rowImage = cached.image || '';
        const rowImgPreview = cached.imgPreview || rowImage;
        const isSelected = cached.selected || false;

        const comboPillsHtml = combo.length > 1 
          ? combo.map(part => `<span class="variant-option-pill">${part}</span>`).join('') 
          : comboName;

        const tr = document.createElement('tr');
        tr.className = 'variant-matrix-row';
        tr.setAttribute('data-combo-name', comboName);
        tr.style.cssText = 'border-bottom: 1px solid #f1f5f9; transition: background 0.15s ease;' + (isSelected ? ' background: #f1f5f9;' : '');
        tr.innerHTML = `
          <td style="width: 44px; text-align: center; padding: 10px 8px;">
            <input type="checkbox" class="variant-row-select" ${isSelected ? 'checked' : ''} style="width: 16px; height: 16px; accent-color: #0f172a; cursor: pointer;">
          </td>
          <td style="padding: 10px 14px;">
            <div class="shopify-variant-cell" style="display: flex; align-items: center; gap: 12px;">
              <div class="shopify-variant-thumb" data-row-idx="${globalVariantIndex}" title="Click to upload variant image" style="width: 38px; height: 38px; border-radius: 6px; border: ${rowImgPreview ? '1px solid #e2e8f0' : '1px dashed #cbd5e1'}; background: #f8fafc; display: flex; align-items: center; justify-content: center; cursor: pointer; position: relative; overflow: hidden; flex-shrink: 0; transition: border-color 0.2s;">
                <img class="variant-thumb-img" src="${getAssetUrl(rowImgPreview)}" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';" style="${rowImgPreview ? 'width: 100%; height: 100%; object-fit: cover; display: block;' : 'display: none;'}">
                <div class="variant-thumb-placeholder" style="${rowImgPreview ? 'display: none;' : 'display: flex; align-items: center; justify-content: center;'}">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#94a3b8" stroke-width="1.8">
                    <rect x="3" y="3" width="18" height="18" rx="3" ry="3"></rect>
                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                    <polyline points="21 15 16 10 5 21"></polyline>
                  </svg>
                </div>
                <button type="button" class="variant-thumb-clear-btn" title="Remove image" style="${rowImgPreview ? 'display: flex;' : 'display: none;'}">&times;</button>
                <input type="file" name="variant_images[${globalVariantIndex}]" class="variant-img-file" accept="image/*" style="display: none;">
                <input type="hidden" name="variants[${globalVariantIndex}][image_url]" class="variant-img-url" value="${rowImage}">
              </div>
              <div style="min-width: 0;">
                <div style="font-weight: 600; color: #0f172a; font-size: 13.5px; line-height: 1.3;">${comboPillsHtml}</div>
                <input type="hidden" name="variants[${globalVariantIndex}][name]" value="${comboName}">
              </div>
            </div>
          </td>
          <td style="padding: 10px 14px;">
            <div class="variant-price-box" style="height: 34px; width: 110px; display: inline-flex; align-items: center; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0 8px; background: #fff;">
              <span style="color: #64748b; font-size: 13px; font-weight: 500; margin-right: 4px; user-select: none;">$</span>
              <input type="number" step="0.01" name="variants[${globalVariantIndex}][price]" class="variant-price-input" value="${rowPrice}" placeholder="0.00" style="border: none; outline: none; width: 100%; font-size: 13px; font-weight: 500; color: #0f172a; padding: 0; background: transparent;">
            </div>
          </td>
          <td style="padding: 10px 14px;">
            <input type="number" name="variants[${globalVariantIndex}][stock]" class="admin-form-input variant-stock-input" style="height: 34px; width: 85px; font-size: 13px; font-weight: 500; padding: 0 8px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;" value="${rowStock}" min="0">
          </td>
          <td style="padding: 10px 14px;">
            <input type="text" name="variants[${globalVariantIndex}][sku]" class="admin-form-input variant-sku-input" style="height: 34px; font-size: 12.5px; padding: 0 8px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; width: 100%; max-width: 170px;" value="${rowSku}">
          </td>
        `;

        attachRowEvents(tr, null, null);
        variantsTableBody.appendChild(tr);
        globalVariantIndex++;
      });
    }

    updateVariantsSelectionBar();
  }

  // Attach interactive events to a variant row
  function attachRowEvents(tr, groupId, parentTr, groupVal) {
    const thumb = tr.querySelector('.shopify-variant-thumb');
    const fileInput = tr.querySelector('.variant-img-file');
    const thumbImg = tr.querySelector('.variant-thumb-img');
    const placeholder = tr.querySelector('.variant-thumb-placeholder');
    const clrBtn = tr.querySelector('.variant-thumb-clear-btn');

    if (clrBtn) {
      clrBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const combo = tr.getAttribute('data-combo-name');
        if (combo) {
          delete customImageMap[combo];
          if (cachedVariantsMap[combo]) {
            cachedVariantsMap[combo].image = '';
            cachedVariantsMap[combo].imgPreview = '';
          }
        }
        tr.removeAttribute('data-has-custom-img');

        // Check if parent group image fallback exists
        const groupFallback = (groupVal && groupImageMap[groupVal]) ? groupImageMap[groupVal] : '';
        if (groupFallback) {
          thumbImg.src = getAssetUrl(groupFallback);
          thumbImg.style.display = 'block';
          if (placeholder) placeholder.style.display = 'none';
          thumb.style.border = '1px solid #e2e8f0';
          const hiddenInput = tr.querySelector('.variant-img-url');
          if (hiddenInput) hiddenInput.value = groupFallback;
        } else {
          thumbImg.src = '';
          thumbImg.style.display = 'none';
          if (placeholder) placeholder.style.display = 'flex';
          thumb.style.border = '1px dashed #cbd5e1';
          const hiddenInput = tr.querySelector('.variant-img-url');
          if (hiddenInput) hiddenInput.value = '';
          clrBtn.style.display = 'none';
        }
        if (fileInput) fileInput.value = '';
      });
    }

    if (thumb && fileInput) {
      thumb.addEventListener('click', (e) => {
        if (e.target.closest('.variant-thumb-clear-btn')) return;
        if (e.target !== fileInput) {
          fileInput.click();
        }
      });

      fileInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
          const file = this.files[0];
          const combo = tr.getAttribute('data-combo-name');

          const reader = new FileReader();
          reader.onload = (e) => {
            const dataUrl = e.target.result;
            tr.setAttribute('data-has-custom-img', 'true');

            thumbImg.src = dataUrl;
            thumbImg.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
            thumb.style.border = '1px solid #e2e8f0';
            if (clrBtn) clrBtn.style.display = 'flex';

            // Also mirror to parent thumbnail if parent is empty
            if (parentTr && groupVal) {
              const pImg = parentTr.querySelector('.group-thumb-img');
              const pPh = parentTr.querySelector('.variant-thumb-placeholder');
              const pThumb = parentTr.querySelector('.group-thumb');
              const pClr = parentTr.querySelector('.group-thumb-clear-btn');
              const parentHasGroupImg = !!groupImageMap[groupVal] || 
                                       (pImg && pImg.src && pImg.style.display !== 'none' && !pImg.src.includes('no-image') && !pImg.src.endsWith('/'));
              if (!parentHasGroupImg && pImg) {
                pImg.src = dataUrl;
                pImg.style.display = 'block';
                if (pPh) pPh.style.display = 'none';
                if (pThumb) pThumb.style.border = '1px solid #e2e8f0';
                if (pClr) pClr.style.display = 'flex';
              }
            }
          };
          reader.readAsDataURL(file);

          // Upload to server asynchronously and set variant image_url
          const uploadUrl = new URL('upload-variant-image', document.baseURI).href;
          const fd = new FormData();
          fd.append('image', file);

          fetch(uploadUrl, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
              if (data && data.success && data.image_url) {
                const resolvedAssetUrl = data.asset_url || getAssetUrl(data.image_url);
                if (combo) {
                  customImageMap[combo] = data.image_url;
                  cachedVariantsMap[combo] = cachedVariantsMap[combo] || {};
                  cachedVariantsMap[combo].image = data.image_url;
                  cachedVariantsMap[combo].imgPreview = resolvedAssetUrl;
                }
                const hiddenInput = tr.querySelector('.variant-img-url');
                if (hiddenInput) hiddenInput.value = data.image_url;

                if (thumbImg) {
                  thumbImg.src = resolvedAssetUrl;
                  thumbImg.style.display = 'block';
                }
                if (placeholder) placeholder.style.display = 'none';

                // Sync into Media Assets card immediately
                syncVariantImageToMediaGallery(data.image_url, resolvedAssetUrl);
              }
            })
            .catch(err => console.warn('Child variant upload error:', err));
        }
      });
    }

    // Row Checkbox Change
    const rowSelect = tr.querySelector('.variant-row-select');
    if (rowSelect) {
      rowSelect.addEventListener('change', () => {
        updateVariantsSelectionBar();
      });
    }

    // Stock Input: live update parent group total
    const stockInput = tr.querySelector('.variant-stock-input');
    if (stockInput) {
      stockInput.addEventListener('input', () => {
        delete stockInput.dataset.autoSync;
        if (groupId && parentTr) {
          syncGroupTotalStock(groupId, parentTr);
        }
      });
    }

    // Price Input: live update parent group price summary
    const priceInput = tr.querySelector('.variant-price-input');
    if (priceInput) {
      priceInput.addEventListener('input', () => {
        delete priceInput.dataset.autoSync;
        if (groupId && parentTr) {
          syncGroupPriceSummary(groupId, parentTr);
        }
      });
    }
  }

  // Live Sync helpers for parent groups
  function syncGroupTotalStock(groupId, parentTr) {
    const stockInputs = variantsTableBody.querySelectorAll(`.variant-subrow[data-group-id="${groupId}"] .variant-stock-input`);
    let total = 0;
    stockInputs.forEach(inp => total += (parseInt(inp.value) || 0));
    const badge = parentTr.querySelector('.group-stock-badge');
    if (badge) {
      badge.innerHTML = `${total} <span style="font-weight: 400; color: #64748b; font-size: 12px;">available</span>`;
    }
  }

  function syncGroupPriceSummary(groupId, parentTr) {
    const priceInputs = variantsTableBody.querySelectorAll(`.variant-subrow[data-group-id="${groupId}"] .variant-price-input`);
    const prices = Array.from(priceInputs).map(inp => parseFloat(inp.value)).filter(p => !isNaN(p));
    const priceEl = parentTr.querySelector('.group-price-text');
    if (!priceEl) return;
    if (prices.length > 0) {
      const minP = Math.min(...prices).toFixed(2);
      const maxP = Math.max(...prices).toFixed(2);
      const display = minP === maxP ? minP : `${minP} – $${maxP}`;
      priceEl.textContent = `$${display}`;
    } else {
      const basePriceEl = document.getElementById('productBasePrice');
      const bPrice = (basePriceEl && basePriceEl.value !== '' && !isNaN(parseFloat(basePriceEl.value)))
        ? parseFloat(basePriceEl.value).toFixed(2)
        : '0.00';
      priceEl.textContent = `$${bPrice}`;
    }
  }

  // Check if variants table is completely empty
  function checkIfTableEmpty() {
    if (variantsTableBody.querySelectorAll('.variant-matrix-row').length === 0) {
      variantsTableBody.innerHTML = `
        <tr id="emptyVariantsRow">
          <td colspan="5" class="variant-empty-cell" style="padding: 32px 20px; text-align: center; color: #64748b;">
            All variant combinations removed.
          </td>
        </tr>
      `;
      const countBadge = document.getElementById('variantsCountBadge');
      if (countBadge) countBadge.textContent = '0 variants';
      const bulkGroup = document.getElementById('bulkActionsGroup');
      if (bulkGroup) bulkGroup.style.display = 'none';
    }
  }

  // Update Selection Status and Bulk Toolbar
  function updateVariantsSelectionBar() {
    if (!variantsTableBody) return;
    const allRows = variantsTableBody.querySelectorAll('.variant-matrix-row');
    const allCbs = variantsTableBody.querySelectorAll('.variant-row-select');
    const checkedCbs = variantsTableBody.querySelectorAll('.variant-row-select:checked');
    const countBadge = document.getElementById('variantsCountBadge');
    const bulkGroup = document.getElementById('bulkActionsGroup');
    const countText = document.getElementById('selectedCountText');
    const masterCb = document.getElementById('selectAllVariants');

    if (countBadge) {
      countBadge.textContent = `${allRows.length} variant${allRows.length === 1 ? '' : 's'}`;
    }

    if (bulkGroup && countText) {
      if (checkedCbs.length > 0) {
        bulkGroup.style.display = 'inline-flex';
        countText.textContent = `${checkedCbs.length} selected`;
      } else {
        bulkGroup.style.display = 'none';
      }
    }

    if (masterCb) {
      masterCb.checked = allCbs.length > 0 && checkedCbs.length === allCbs.length;
      masterCb.indeterminate = checkedCbs.length > 0 && checkedCbs.length < allCbs.length;
    }

    allCbs.forEach(cb => {
      const row = cb.closest('.variant-matrix-row');
      if (row) {
        row.style.background = cb.checked ? '#f1f5f9' : '';
      }
    });

    // Also sync parent group checkboxes if grouped
    const groupCbs = variantsTableBody.querySelectorAll('.variant-group-cb');
    groupCbs.forEach(gCb => {
      const gId = gCb.getAttribute('data-group-id');
      if (gId) {
        const childCbs = variantsTableBody.querySelectorAll(`.variant-row-select[data-group-id="${gId}"]`);
        const checkedChildCbs = variantsTableBody.querySelectorAll(`.variant-row-select[data-group-id="${gId}"]:checked`);
        gCb.checked = childCbs.length > 0 && checkedChildCbs.length === childCbs.length;
        gCb.indeterminate = checkedChildCbs.length > 0 && checkedChildCbs.length < childCbs.length;
      }
    });
  }

  // Master Select All Checkbox Handler
  const selectAllVariantsCb = document.getElementById('selectAllVariants');
  if (selectAllVariantsCb) {
    selectAllVariantsCb.addEventListener('change', function () {
      if (!variantsTableBody) return;
      const allCbs = variantsTableBody.querySelectorAll('.variant-row-select');
      allCbs.forEach(cb => {
        cb.checked = this.checked;
      });
      const allGroupCbs = variantsTableBody.querySelectorAll('.variant-group-cb');
      allGroupCbs.forEach(gCb => {
        gCb.checked = this.checked;
      });
      updateVariantsSelectionBar();
    });
  }

  // Quick Bulk Action: Edit Price for Selected
  const btnBulkEditPrice = document.getElementById('btnBulkEditPrice');
  if (btnBulkEditPrice) {
    btnBulkEditPrice.addEventListener('click', () => {
      const checkedRows = variantsTableBody.querySelectorAll('.variant-row-select:checked');
      if (checkedRows.length === 0) return;
      const newPrice = prompt(`Enter new selling price ($) for ${checkedRows.length} selected variants:`);
      if (newPrice !== null && newPrice.trim() !== '') {
        const parsed = parseFloat(newPrice.replace(/[^0-9.]/g, '')).toFixed(2);
        checkedRows.forEach(cb => {
          const row = cb.closest('.variant-matrix-row');
          const input = row?.querySelector('.variant-price-input');
          if (input) {
            input.value = parsed;
            const gId = row.getAttribute('data-group-id');
            if (gId) {
              const pRow = variantsTableBody.querySelector(`.variant-parent-row[data-group-id="${gId}"]`);
              if (pRow) syncGroupPriceSummary(gId, pRow);
            }
          }
        });
      }
    });
  }

  // Quick Bulk Action: Edit Quantity for Selected
  const btnBulkEditStock = document.getElementById('btnBulkEditStock');
  if (btnBulkEditStock) {
    btnBulkEditStock.addEventListener('click', () => {
      const checkedRows = variantsTableBody.querySelectorAll('.variant-row-select:checked');
      if (checkedRows.length === 0) return;
      const newQty = prompt(`Enter available inventory quantity for ${checkedRows.length} selected variants:`);
      if (newQty !== null && newQty.trim() !== '') {
        const parsed = parseInt(newQty.replace(/[^0-9]/g, '')) || 0;
        checkedRows.forEach(cb => {
          const row = cb.closest('.variant-matrix-row');
          const input = row?.querySelector('.variant-stock-input');
          if (input) {
            input.value = parsed;
            const gId = row.getAttribute('data-group-id');
            if (gId) {
              const pRow = variantsTableBody.querySelector(`.variant-parent-row[data-group-id="${gId}"]`);
              if (pRow) syncGroupTotalStock(gId, pRow);
            }
          }
        });
      }
    });
  }

  // Quick Bulk Action: Delete Selected Variants
  const btnBulkDelete = document.getElementById('btnBulkDelete');
  if (btnBulkDelete) {
    btnBulkDelete.addEventListener('click', () => {
      const checkedRows = variantsTableBody.querySelectorAll('.variant-row-select:checked');
      if (checkedRows.length === 0) return;
      if (confirm(`Are you sure you want to delete ${checkedRows.length} selected variants?`)) {
        checkedRows.forEach(cb => {
          const row = cb.closest('.variant-matrix-row');
          if (row) {
            const gId = row.getAttribute('data-group-id');
            row.remove();
            if (gId) {
              const remaining = variantsTableBody.querySelectorAll(`.variant-subrow[data-group-id="${gId}"]`);
              const pRow = variantsTableBody.querySelector(`.variant-parent-row[data-group-id="${gId}"]`);
              if (remaining.length === 0 && pRow) {
                pRow.remove();
              } else if (pRow) {
                syncGroupTotalStock(gId, pRow);
                syncGroupPriceSummary(gId, pRow);
              }
            }
          }
        });
        updateVariantsSelectionBar();
        checkIfTableEmpty();
      }
    });
  }

  // Bulk Edit Variants Modal Handler
  const applyBulkEditBtn = document.getElementById('applyBulkEdit');
  if (applyBulkEditBtn) {
    applyBulkEditBtn.addEventListener('click', () => {
      const bulkPrice = document.getElementById('bulkPriceInput') ? document.getElementById('bulkPriceInput').value : '';
      const bulkStock = document.getElementById('bulkStockInput') ? document.getElementById('bulkStockInput').value : '';

      if (bulkPrice) {
        document.querySelectorAll('.variant-price-input').forEach(input => input.value = parseFloat(bulkPrice).toFixed(2));
        document.querySelectorAll('.variant-parent-row').forEach(pRow => {
          const gId = pRow.getAttribute('data-group-id');
          if (gId) syncGroupPriceSummary(gId, pRow);
        });
      }
      if (bulkStock) {
        document.querySelectorAll('.variant-stock-input').forEach(input => input.value = parseInt(bulkStock) || 0);
        document.querySelectorAll('.variant-parent-row').forEach(pRow => {
          const gId = pRow.getAttribute('data-group-id');
          if (gId) syncGroupTotalStock(gId, pRow);
        });
      }
      const bulkModal = document.getElementById('bulkEditModal');
      if (bulkModal && window.adminCloseModal) window.adminCloseModal(bulkModal);
    });
  }

  // Live inheritance: If user enters or changes base price/stock in top cards, sync to non-overridden variant rows
  const productBasePriceInput = document.getElementById('productBasePrice');
  if (productBasePriceInput) {
    productBasePriceInput.addEventListener('input', function() {
      const val = this.value.trim();
      const newPrice = (val !== '' && !isNaN(parseFloat(val))) ? parseFloat(val).toFixed(2) : '';
      if (!variantsTableBody) return;
      
      const priceInputs = variantsTableBody.querySelectorAll('.variant-price-input');
      priceInputs.forEach(input => {
        if (!input.value || input.dataset.autoSync === 'true') {
          input.value = newPrice;
          input.dataset.autoSync = newPrice ? 'true' : 'false';
        }
      });

      variantsTableBody.querySelectorAll('.variant-parent-row').forEach(pRow => {
        const gId = pRow.getAttribute('data-group-id');
        if (gId) syncGroupPriceSummary(gId, pRow);
      });
    });
  }

  const productStockInput = document.getElementById('productStock');
  if (productStockInput) {
    productStockInput.addEventListener('input', function() {
      const val = this.value.trim();
      const newStock = (val !== '' && !isNaN(parseInt(val))) ? parseInt(val) : 0;
      if (!variantsTableBody) return;

      const stockInputs = variantsTableBody.querySelectorAll('.variant-stock-input');
      stockInputs.forEach(input => {
        if (!input.value || input.value === '0' || input.dataset.autoSync === 'true') {
          input.value = newStock;
          input.dataset.autoSync = newStock ? 'true' : 'false';
        }
      });

      variantsTableBody.querySelectorAll('.variant-parent-row').forEach(pRow => {
        const gId = pRow.getAttribute('data-group-id');
        if (gId) syncGroupTotalStock(gId, pRow);
      });
    });
  }

  // ==========================================================================
  // 5. Dynamic Tags Input Chip Handler
  // ==========================================================================
  const tagInputField = document.getElementById('tagInputField');
  const tagsContainer = document.getElementById('tagsContainer');

  if (tagInputField && tagsContainer) {
    function addTag(val) {
      const trimmed = val.trim().replace(/^,|,$/g, '');
      if (!trimmed) return;
      const chip = document.createElement('span');
      chip.className = 'tag-chip';
      chip.innerHTML = `
        <span>${trimmed}</span>
        <button type="button" class="tag-chip-remove" title="Remove tag">&times;</button>
      `;
      tagsContainer.appendChild(chip);
    }

    tagInputField.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        addTag(tagInputField.value);
        tagInputField.value = '';
      }
    });
  }

  // Universal click delegation for tag chip cross buttons
  document.addEventListener('click', (e) => {
    const tagRemoveBtn = e.target.closest('.tag-chip-remove');
    if (tagRemoveBtn) {
      const chip = tagRemoveBtn.closest('.tag-chip');
      if (chip) {
        chip.style.transition = 'opacity 0.15s ease, transform 0.15s ease';
        chip.style.opacity = '0';
        chip.style.transform = 'scale(0.8)';
        setTimeout(() => chip.remove(), 150);
      }
    }
  });

  // ==========================================================================
  // 5.5 Detailed Specifications Dynamic Rows
  // ==========================================================================
  const btnAddSpecRow = document.getElementById('btnAddSpecRow');
  const specsContainer = document.getElementById('specsContainer');
  const emptySpecsNotice = document.getElementById('emptySpecsNotice');

  if (btnAddSpecRow && specsContainer) {
    btnAddSpecRow.addEventListener('click', (e) => {
      e.preventDefault();
      if (emptySpecsNotice) emptySpecsNotice.style.display = 'none';

      const sIdx = specsContainer.querySelectorAll('.spec-input-row').length;
      const row = document.createElement('div');
      row.className = 'spec-input-row';
      row.style = 'display: flex; gap: 12px; margin-bottom: 10px; align-items: center;';
      row.innerHTML = `
        <input type="text" name="specs[${sIdx}][key]" class="admin-form-input" style="flex: 1;" placeholder="Spec name (e.g. Coil Technology)" required>
        <input type="text" name="specs[${sIdx}][value]" class="admin-form-input" style="flex: 1.5;" placeholder="Value (e.g. Dual Mesh 0.8 ohm)" required>
        <button type="button" class="btn-remove-spec-row" title="Delete specification" style="background: none; border: none; color: #ef4444; font-size: 20px; cursor: pointer; padding: 2px 8px;">&times;</button>
      `;
      specsContainer.appendChild(row);
    });
  }

  document.addEventListener('click', (e) => {
    const removeSpecBtn = e.target.closest('.btn-remove-spec-row');
    if (removeSpecBtn) {
      const row = removeSpecBtn.closest('.spec-input-row');
      if (row) {
        row.remove();
        if (specsContainer && specsContainer.querySelectorAll('.spec-input-row').length === 0) {
          if (emptySpecsNotice) emptySpecsNotice.style.display = 'block';
        }
      }
    }
  });

  // ==========================================================================
  // 6. Form Submit Handler: Sync All RTEs, Variants and Tags
  // ==========================================================================
  const productForm = document.getElementById('productMainForm');
  if (productForm) {
    productForm.addEventListener('submit', (e) => {
      // Auto-commit any pending variant option input before submission
      let hasUncommittedOption = false;
      if (optionsListContainer) {
        optionsListContainer.querySelectorAll('.inline-option-value-input').forEach(inp => {
          if (inp.value && inp.value.trim()) hasUncommittedOption = true;
        });
      }
      if (hasUncommittedOption && typeof rebuildVariantsMatrix === 'function' && hasVariantsToggle && hasVariantsToggle.checked) {
        rebuildVariantsMatrix();
      }

      // Check for active variant duplicate errors
      if (hasVariantsToggle && hasVariantsToggle.checked && optionsListContainer) {
        const errorInput = optionsListContainer.querySelector('.inline-tag-input-wrap.has-error input, .custom-option-name-input.has-error');
        if (errorInput) {
          e.preventDefault();
          e.stopPropagation();
          errorInput.focus();
          return false;
        }
      }

      // Ensure all variant hidden inputs are populated with their resolved image before submit
      if (variantsTableBody) {
        variantsTableBody.querySelectorAll('.variant-matrix-row').forEach(r => {
          const combo = r.getAttribute('data-combo-name');
          const hiddenInp = r.querySelector('.variant-img-url');
          if (combo && hiddenInp) {
            const parts = combo.split(' / ').map(s => s.trim());
            const gVal = parts[0] || combo;
            let resolvedImg = customImageMap[combo] || hiddenInp.value || cachedVariantsMap[combo]?.image || groupImageMap[gVal] || '';
            if (resolvedImg && !resolvedImg.startsWith('data:')) {
              hiddenInp.value = resolvedImg;
            }
          }
        });
      }

      document.querySelectorAll('.rich-text-editor').forEach(rte => {
        const content = rte.querySelector('.rte-editor-content');
        const textarea = rte.querySelector('textarea');
        if (content && textarea) {
          textarea.value = content.innerHTML;
        }
      });

      const tagChips = document.querySelectorAll('#tagsContainer .tag-chip span');
      if (tagChips.length > 0) {
        const tagsArray = Array.from(tagChips).map(c => c.textContent.trim());
        let tagsInput = document.getElementById('tagsHiddenInput');
        if (!tagsInput) {
          tagsInput = document.createElement('input');
          tagsInput.type = 'hidden';
          tagsInput.name = 'tags';
          tagsInput.id = 'tagsHiddenInput';
          productForm.appendChild(tagsInput);
        }
        tagsInput.value = tagsArray.join(', ');
      }
    });
  }
});
