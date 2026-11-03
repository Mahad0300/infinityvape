/**
 * New Vape - Add Collection Page JavaScript
 * Handles automated condition rules builder, collection image uploader, and live real-time SEO preview
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Elements & SEO Live Preview
  const toggleSeoBtn = document.getElementById('toggleSeoBtn');
  const seoFieldsContainer = document.getElementById('seoFieldsContainer');
  const mainTitleInput = document.getElementById('collectionTitle') || document.querySelector('input[name="title"]');
  const mainDescInput = document.getElementById('collectionDescription') || document.querySelector('textarea[name="description"]');
  const seoTitleInput = document.getElementById('seoTitleInput');
  const seoDescInput = document.getElementById('seoDescInput');
  const slugInput = document.getElementById('slugInput');
  const seoTitleCount = document.getElementById('seoTitleCount');
  const seoDescCount = document.getElementById('seoDescCount');
  const seoPreviewTitle = document.getElementById('seoPreviewTitle');
  const seoPreviewDesc = document.getElementById('seoPreviewDesc');
  const seoPreviewUrl = document.getElementById('seoPreviewUrl');

  const parentSelect = document.getElementById('collectionParent');
  const prefixSpan = document.getElementById('urlPrefixSpan') || document.querySelector('.input-prefix-container .prefix');
  const baseUrl = (prefixSpan && prefixSpan.dataset.baseUrl) ? prefixSpan.dataset.baseUrl : (prefixSpan ? prefixSpan.textContent.trim() : '/');

  function getCurrentUrlPrefix() {
    let parentSlug = '';
    if (parentSelect && parentSelect.selectedIndex >= 0) {
      const opt = parentSelect.options[parentSelect.selectedIndex];
      parentSlug = (opt && opt.dataset.slug) ? opt.dataset.slug.trim() : '';
    }
    const cleanBase = baseUrl.replace(/\/+$/, '') + '/';
    return parentSlug ? (cleanBase + parentSlug + '/') : cleanBase;
  }

  if (toggleSeoBtn && seoFieldsContainer) {
    toggleSeoBtn.addEventListener('click', (e) => {
      e.preventDefault();
      seoFieldsContainer.classList.toggle('active');
    });
  }

  function updateSeoPreview() {
    const mainTitle = mainTitleInput ? mainTitleInput.value.trim() : '';
    const mainDesc = mainDescInput ? mainDescInput.value.trim() : '';
    const customTitle = seoTitleInput ? seoTitleInput.value.trim() : '';
    const customDesc = seoDescInput ? seoDescInput.value.trim() : '';
    const currentSlug = slugInput ? slugInput.value.trim() : '';
    const activePrefix = getCurrentUrlPrefix();

    if (prefixSpan) {
      prefixSpan.textContent = activePrefix;
    }

    // Title preview: priority to custom SEO title, then collection main title, then default
    if (seoPreviewTitle) {
      if (customTitle) {
        seoPreviewTitle.textContent = customTitle;
      } else if (mainTitle) {
        seoPreviewTitle.textContent = mainTitle + ' | Infinity Vape Shop';
      } else {
        seoPreviewTitle.textContent = 'Collection Title';
      }
    }

    // Description preview: priority to custom SEO desc, then collection description, then default
    if (seoPreviewDesc) {
      if (customDesc) {
        seoPreviewDesc.textContent = customDesc;
      } else if (mainDesc) {
        seoPreviewDesc.textContent = mainDesc;
      } else {
        seoPreviewDesc.textContent = 'Collection description will appear in search results...';
      }
    }

    // Character counters
    if (seoTitleCount && seoTitleInput) {
      seoTitleCount.textContent = seoTitleInput.value.length;
    }
    if (seoDescCount && seoDescInput) {
      seoDescCount.textContent = seoDescInput.value.length;
    }

    // URL preview
    if (seoPreviewUrl) {
      seoPreviewUrl.textContent = activePrefix + (currentSlug || 'handle');
    }
  }

  if (parentSelect) {
    parentSelect.addEventListener('change', updateSeoPreview);
  }

  // Attach real-time listeners for all inputs
  const watchInputs = [mainTitleInput, mainDescInput, seoTitleInput, seoDescInput, slugInput];
  watchInputs.forEach(input => {
    if (!input) return;
    ['input', 'keyup', 'change', 'paste'].forEach(evt => {
      input.addEventListener(evt, () => {
        if (input === slugInput) {
          slugInput.dataset.touched = 'true';
        }
        if (input === mainTitleInput && slugInput && !slugInput.dataset.touched) {
          const autoSlug = mainTitleInput.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
          slugInput.value = autoSlug;
        }
        updateSeoPreview();
      });
    });
  });

  // Run immediately on page load
  updateSeoPreview();

  // 2. Collection Type Radios (Manual vs Automated)
  const collectionTypeRadios = document.querySelectorAll('input[name="collection_type"]');
  const automatedConditionsBox = document.getElementById('automatedConditionsBox');
  const radioCardManual = document.getElementById('radioCardManual');
  const radioCardAutomated = document.getElementById('radioCardAutomated');

  if (collectionTypeRadios.length > 0) {
    collectionTypeRadios.forEach(radio => {
      radio.addEventListener('change', function() {
        if (this.value === 'automated') {
          if (automatedConditionsBox) automatedConditionsBox.classList.remove('d-none');
          if (radioCardAutomated) radioCardAutomated.classList.add('selected');
          if (radioCardManual) radioCardManual.classList.remove('selected');
        } else {
          if (automatedConditionsBox) automatedConditionsBox.classList.add('d-none');
          if (radioCardManual) radioCardManual.classList.add('selected');
          if (radioCardAutomated) radioCardAutomated.classList.remove('selected');
        }
      });
    });
  }

  // 3. Add / Remove Automated Condition Rows
  const btnAddCondition = document.getElementById('btnAddCondition');
  const conditionsListContainer = document.getElementById('conditionsListContainer');

  if (btnAddCondition && conditionsListContainer) {
    btnAddCondition.addEventListener('click', () => {
      const newRow = document.createElement('div');
      newRow.className = 'condition-row';
      newRow.innerHTML = `
        <select name="condition_field[]" class="admin-form-select">
          <option value="tag" selected>Product tag</option>
          <option value="title">Product title</option>
          <option value="brand">Product vendor / brand</option>
          <option value="price">Product price</option>
          <option value="stock">Inventory stock</option>
        </select>
        <select name="condition_operator[]" class="admin-form-select">
          <option value="equals" selected>is equal to</option>
          <option value="not_equals">is not equal to</option>
          <option value="contains">contains</option>
          <option value="starts_with">starts with</option>
          <option value="greater_than">is greater than</option>
          <option value="less_than">is less than</option>
        </select>
        <input type="text" name="condition_value[]" class="admin-form-input" placeholder="Enter condition value...">
        <button type="button" class="btn-icon-only danger btn-remove-condition" title="Remove condition">
          <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
      `;
      conditionsListContainer.appendChild(newRow);
    });
  }

  // Delegated remove for condition rows
  document.addEventListener('click', (e) => {
    const removeBtn = e.target.closest('.btn-remove-condition');
    if (removeBtn) {
      const row = removeBtn.closest('.condition-row');
      if (row) row.remove();
    }
  });

  // 4. Collection Image Box Upload & Preview & Remove Button
  const collectionImageBox = document.getElementById('collectionImageBox');
  const collectionImageInput = document.getElementById('collectionImageInput');
  const collectionPreviewImg = document.getElementById('collectionPreviewImg');
  const colUploadIcon = document.getElementById('colUploadIcon');
  const btnRemoveColImg = document.getElementById('btnRemoveColImg');
  const collectionImageUrl = document.getElementById('collectionImageUrl');

  // Trigger file selection when image box is clicked (excluding remove button)
  if (collectionImageBox && collectionImageInput) {
    collectionImageBox.addEventListener('click', (e) => {
      if (e.target.closest('#btnRemoveColImg')) return;
      collectionImageInput.click();
    });
  }

  // Handle selected file preview
  if (collectionImageInput) {
    collectionImageInput.addEventListener('change', function() {
      if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
          if (collectionPreviewImg) {
            collectionPreviewImg.src = e.target.result;
            collectionPreviewImg.classList.remove('d-none');
          }
          if (colUploadIcon) colUploadIcon.classList.add('d-none');
          if (btnRemoveColImg) btnRemoveColImg.classList.remove('d-none');
        };
        reader.readAsDataURL(this.files[0]);
      }
    });
  }

  // Cross / Remove Button click handler
  if (btnRemoveColImg) {
    btnRemoveColImg.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation();

      if (collectionImageInput) {
        collectionImageInput.value = '';
      }
      if (collectionImageUrl) {
        collectionImageUrl.value = '';
      }
      if (collectionPreviewImg) {
        collectionPreviewImg.src = '';
        collectionPreviewImg.classList.add('d-none');
      }
      btnRemoveColImg.classList.add('d-none');
      if (colUploadIcon) {
        colUploadIcon.classList.remove('d-none');
      }
    });
  }

  // Handle broken image error fallback
  if (collectionPreviewImg) {
    collectionPreviewImg.addEventListener('error', function() {
      this.classList.add('d-none');
      if (colUploadIcon) colUploadIcon.classList.remove('d-none');
      if (btnRemoveColImg) btnRemoveColImg.classList.add('d-none');
    });
  }

  // ==========================================================================
  // 5. Rich Text Editor (RTE) Toolbar Execution & Form Sync
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

      // Prevent focus loss when clicking toolbar buttons so editor selection is preserved
      toolbar.querySelectorAll('button').forEach(btn => {
        btn.addEventListener('mousedown', (e) => {
          e.preventDefault();
        });
      });

      // Helper: find closest highlight ancestor inside content
      function getHighlightParent(node) {
        let curr = node;
        while (curr && curr !== content) {
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

      // Helper: unwrap or remove highlight from an element
      function unwrapHighlight(el) {
        if (!el || !el.parentNode) return;
        el.style.backgroundColor = '';
        el.style.background = '';
        el.classList.remove('rte-highlight');

        el.querySelectorAll('mark, .rte-highlight, [style*="background"]').forEach(child => {
          child.style.backgroundColor = '';
          child.style.background = '';
          child.classList.remove('rte-highlight');
          if (child.getAttribute('style') !== null && child.getAttribute('style').trim() === '') {
            child.removeAttribute('style');
          }
        });

        const styleAttr = el.getAttribute('style');
        if (styleAttr !== null && styleAttr.trim() === '') {
          el.removeAttribute('style');
        }

        // If it's a MARK or SPAN with no attributes, unwrap it cleanly
        if (el.tagName === 'MARK' || (el.tagName === 'SPAN' && (!el.getAttribute('style') || el.getAttribute('style').trim() === '') && el.classList.length === 0)) {
          while (el.firstChild) {
            el.parentNode.insertBefore(el.firstChild, el);
          }
          el.parentNode.removeChild(el);
        }
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
            if (sel && sel.rangeCount > 0) {
              const range = sel.getRangeAt(0);

              // 1. Check if selection or cursor is already highlighted
              const startH = getHighlightParent(range.startContainer);
              const endH = getHighlightParent(range.endContainer);
              const commonH = getHighlightParent(range.commonAncestorContainer);
              let targetHighlight = commonH || startH || endH;

              let hasIntersectingHighlight = false;
              const allHighlights = content.querySelectorAll('mark, .rte-highlight, [style*="background"]');
              const intersectingElements = [];

              allHighlights.forEach(el => {
                const bg = (el.style.backgroundColor || el.style.background || '').toLowerCase().replace(/\s+/g, '');
                const isHilite = el.tagName === 'MARK' || el.classList.contains('rte-highlight') || (bg && bg !== 'transparent' && !bg.includes('rgba(0,0,0,0)'));
                if (isHilite) {
                  if (range.intersectsNode ? range.intersectsNode(el) : false) {
                    hasIntersectingHighlight = true;
                    intersectingElements.push(el);
                  }
                }
              });

              const isAlreadyHilited = Boolean(targetHighlight || hasIntersectingHighlight);

              if (isAlreadyHilited) {
                // ==============================
                // UNHIGHLIGHT: Remove highlight
                // ==============================
                if (targetHighlight && targetHighlight.contains(range.startContainer) && targetHighlight.contains(range.endContainer) && !sel.isCollapsed && range.toString().trim() !== targetHighlight.textContent.trim()) {
                  // Partial selection inside a single highlighted element: split so only selected text is unhighlighted
                  try {
                    const makeClone = () => {
                      const c = targetHighlight.cloneNode(false);
                      c.classList.add('rte-highlight');
                      c.style.backgroundColor = '#fef08a';
                      return c;
                    };

                    const beforeRange = document.createRange();
                    beforeRange.setStart(targetHighlight, 0);
                    beforeRange.setEnd(range.startContainer, range.startOffset);
                    const beforeFrag = beforeRange.extractContents();

                    const afterRange = document.createRange();
                    afterRange.setStart(range.endContainer, range.endOffset);
                    afterRange.setEnd(targetHighlight, targetHighlight.childNodes.length);
                    const afterFrag = afterRange.extractContents();

                    const selectedFrag = document.createDocumentFragment();
                    while (targetHighlight.firstChild) {
                      selectedFrag.appendChild(targetHighlight.firstChild);
                    }

                    if (selectedFrag.querySelectorAll) {
                      selectedFrag.querySelectorAll('*').forEach(c => {
                        c.style.backgroundColor = '';
                        c.style.background = '';
                        c.classList.remove('rte-highlight');
                      });
                    }

                    if (beforeFrag.textContent.length > 0) {
                      const beforeEl = makeClone();
                      beforeEl.appendChild(beforeFrag);
                      targetHighlight.parentNode.insertBefore(beforeEl, targetHighlight);
                    }

                    targetHighlight.parentNode.insertBefore(selectedFrag, targetHighlight);

                    if (afterFrag.textContent.length > 0) {
                      const afterEl = makeClone();
                      afterEl.appendChild(afterFrag);
                      targetHighlight.parentNode.insertBefore(afterEl, targetHighlight);
                    }

                    targetHighlight.parentNode.removeChild(targetHighlight);
                  } catch (err) {
                    unwrapHighlight(targetHighlight);
                  }
                } else if (targetHighlight) {
                  unwrapHighlight(targetHighlight);
                }

                // Also unwrap any intersecting highlighted elements
                intersectingElements.forEach(el => unwrapHighlight(el));

                content.normalize();
              } else if (!sel.isCollapsed) {
                // ==============================
                // HIGHLIGHT: Apply yellow highlight
                // ==============================
                if (!document.execCommand('hiliteColor', false, '#fef08a')) {
                  document.execCommand('backColor', false, '#fef08a');
                }
                content.querySelectorAll('[style*="background"]').forEach(el => {
                  const bg = (el.style.backgroundColor || el.style.background || '').toLowerCase().replace(/\s+/g, '');
                  if (bg.includes('254,240,138') || bg.includes('fef08a') || bg.includes('255,255,0') || bg.includes('ffff00')) {
                    el.classList.add('rte-highlight');
                    el.style.backgroundColor = '#fef08a';
                  }
                });
              }
            }
          } else if (cmd === 'removeFormat') {
            document.execCommand('removeFormat', false, null);
            const sel = window.getSelection();
            if (sel && sel.rangeCount > 0) {
              const range = sel.getRangeAt(0);
              content.querySelectorAll('mark, .rte-highlight, [style*="background"]').forEach(el => {
                if (range.intersectsNode && range.intersectsNode(el)) {
                  unwrapHighlight(el);
                }
              });
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
          // Remove inline colors and fonts so theme styles ALWAYS apply
          el.style.color = '';
          el.style.fontFamily = '';
          el.style.fontSize = '';
          el.style.lineHeight = '';

          // Check background: only keep real deliberate yellow highlight
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
            if (el.tagName === 'MARK' || (el.tagName === 'SPAN' && (!el.getAttribute('style') || el.getAttribute('style').trim() === '') && el.classList.length === 0)) {
              while (el.firstChild) {
                el.parentNode.insertBefore(el.firstChild, el);
              }
              el.parentNode.removeChild(el);
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
  // 6. Explore Related Collections Selection & Live Search Filter
  // ==========================================================================
  const searchRelInput = document.getElementById('searchRelatedCollectionsInput');
  const relGrid = document.getElementById('relatedCollectionsGrid');
  const relBadge = document.getElementById('selectedRelCountBadge');
  const btnSelectAllRel = document.getElementById('btnSelectAllRel');
  const btnClearAllRel = document.getElementById('btnClearAllRel');

  function updateRelCount() {
    if (!relGrid || !relBadge) return;
    const checkedBoxes = relGrid.querySelectorAll('.related-col-checkbox:checked');
    const count = checkedBoxes.length;
    relBadge.textContent = `${count} selected`;

    // Update visual card styles
    relGrid.querySelectorAll('.related-col-checkbox-card').forEach(card => {
      const cb = card.querySelector('.related-col-checkbox');
      if (cb && cb.checked) {
        card.classList.add('is-selected');
        card.style.borderColor = 'var(--admin-primary, #082142)';
        card.style.background = '#f8fafc';
      } else {
        card.classList.remove('is-selected');
        card.style.borderColor = 'var(--border-color, #e2e8f0)';
        card.style.background = '#ffffff';
      }
    });
  }

  if (relGrid) {
    // Listen for checkbox changes
    relGrid.addEventListener('change', (e) => {
      if (e.target && e.target.classList.contains('related-col-checkbox')) {
        updateRelCount();
      }
    });

    // Live search filter
    if (searchRelInput) {
      searchRelInput.addEventListener('input', () => {
        const query = searchRelInput.value.trim().toLowerCase();
        const cards = relGrid.querySelectorAll('.related-col-checkbox-card');
        cards.forEach(card => {
          const title = (card.querySelector('.related-col-title')?.textContent || '').toLowerCase();
          if (!query || title.includes(query)) {
            card.style.display = 'flex';
          } else {
            card.style.display = 'none';
          }
        });
      });
    }

    // Select All visible
    if (btnSelectAllRel) {
      btnSelectAllRel.addEventListener('click', (e) => {
        e.preventDefault();
        relGrid.querySelectorAll('.related-col-checkbox-card').forEach(card => {
          if (card.style.display !== 'none') {
            const cb = card.querySelector('.related-col-checkbox');
            if (cb) cb.checked = true;
          }
        });
        updateRelCount();
      });
    }

    // Clear All
    if (btnClearAllRel) {
      btnClearAllRel.addEventListener('click', (e) => {
        e.preventDefault();
        relGrid.querySelectorAll('.related-col-checkbox').forEach(cb => {
          cb.checked = false;
        });
        updateRelCount();
      });
    }

    // Initialize state on load
    updateRelCount();
  }

  // 7. Header Banner Quick Links Search & Counter
  const searchHeaderInput = document.getElementById('searchHeaderCollectionsInput');
  const headerGrid = document.getElementById('headerCollectionsGrid');
  const btnSelectAllHeader = document.getElementById('btnSelectAllHeader');
  const btnClearAllHeader = document.getElementById('btnClearAllHeader');
  const selectedHeaderCountBadge = document.getElementById('selectedHeaderCountBadge');

  function updateHeaderCount() {
    if (!headerGrid) return;
    const checkedBoxes = headerGrid.querySelectorAll('.header-col-checkbox:checked');
    const count = checkedBoxes.length;
    if (selectedHeaderCountBadge) {
      selectedHeaderCountBadge.textContent = count + ' selected';
    }

    // Toggle active card styling
    headerGrid.querySelectorAll('.header-col-checkbox-card').forEach(card => {
      const cb = card.querySelector('.header-col-checkbox');
      if (cb && cb.checked) {
        card.classList.add('is-selected');
        card.style.borderColor = 'var(--admin-primary, #082142)';
        card.style.background = '#f8fafc';
      } else {
        card.classList.remove('is-selected');
        card.style.borderColor = 'var(--border-color, #e2e8f0)';
        card.style.background = '#ffffff';
      }
    });
  }

  if (headerGrid) {
    // Listen for checkbox changes
    headerGrid.addEventListener('change', (e) => {
      if (e.target && e.target.classList.contains('header-col-checkbox')) {
        updateHeaderCount();
      }
    });

    // Live search filter
    if (searchHeaderInput) {
      searchHeaderInput.addEventListener('input', () => {
        const query = searchHeaderInput.value.trim().toLowerCase();
        const cards = headerGrid.querySelectorAll('.header-col-checkbox-card');
        cards.forEach(card => {
          const title = (card.querySelector('.header-col-title')?.textContent || '').toLowerCase();
          if (!query || title.includes(query)) {
            card.style.display = 'flex';
          } else {
            card.style.display = 'none';
          }
        });
      });
    }

    // Select All visible
    if (btnSelectAllHeader) {
      btnSelectAllHeader.addEventListener('click', (e) => {
        e.preventDefault();
        headerGrid.querySelectorAll('.header-col-checkbox-card').forEach(card => {
          if (card.style.display !== 'none') {
            const cb = card.querySelector('.header-col-checkbox');
            if (cb) cb.checked = true;
          }
        });
        updateHeaderCount();
      });
    }

    // Clear All
    if (btnClearAllHeader) {
      btnClearAllHeader.addEventListener('click', (e) => {
        e.preventDefault();
        headerGrid.querySelectorAll('.header-col-checkbox').forEach(cb => {
          cb.checked = false;
        });
        updateHeaderCount();
      });
    }

    // Initialize state on load
    updateHeaderCount();
  }
});
