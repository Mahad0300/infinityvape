/**
 * New Vape - Brand Form (Add & Edit) JavaScript
 * Handles brand logo uploader, cross remove button, real-time SEO preview,
 * Rich Text Editor (RTE) for long description, Header Collections filter, and Related Collections filter.
 */

document.addEventListener('DOMContentLoaded', () => {
  // ==========================================================================
  // 1. Elements & Live SEO Preview
  // ==========================================================================
  const toggleSeoBtn = document.getElementById('toggleSeoBtn');
  const seoFieldsContainer = document.getElementById('seoFieldsContainer');
  const brandNameInput = document.getElementById('brandName') || document.querySelector('input[name="name"]');
  const brandDescInput = document.getElementById('brandDescription') || document.querySelector('textarea[name="short_description"]');
  const seoTitleInput = document.getElementById('seoTitleInput');
  const seoDescInput = document.getElementById('seoDescInput');
  const slugInput = document.getElementById('slugInput');
  const seoTitleCount = document.getElementById('seoTitleCount');
  const seoDescCount = document.getElementById('seoDescCount');
  const seoPreviewTitle = document.getElementById('seoPreviewTitle');
  const seoPreviewDesc = document.getElementById('seoPreviewDesc');
  const seoPreviewUrl = document.getElementById('seoPreviewUrl');

  const prefixSpan = document.getElementById('urlPrefixSpan') || document.querySelector('.input-prefix-container .prefix');
  const baseUrl = (prefixSpan && prefixSpan.dataset.baseUrl) ? prefixSpan.dataset.baseUrl : (prefixSpan ? prefixSpan.textContent.trim() : '/brand/');

  if (toggleSeoBtn && seoFieldsContainer) {
    toggleSeoBtn.addEventListener('click', (e) => {
      e.preventDefault();
      seoFieldsContainer.classList.toggle('active');
    });
  }

  function updateSeoPreview() {
    const brandName = brandNameInput ? brandNameInput.value.trim() : '';
    const brandDesc = brandDescInput ? brandDescInput.value.trim() : '';
    const customTitle = seoTitleInput ? seoTitleInput.value.trim() : '';
    const customDesc = seoDescInput ? seoDescInput.value.trim() : '';
    const currentSlug = slugInput ? slugInput.value.trim() : '';

    // Title preview: priority to custom SEO title, then brand name, then default
    if (seoPreviewTitle) {
      if (customTitle) {
        seoPreviewTitle.textContent = customTitle;
      } else if (brandName) {
        seoPreviewTitle.textContent = brandName + ' Official Store | New Vape';
      } else {
        seoPreviewTitle.textContent = 'Brand Name';
      }
    }

    // Description preview: priority to custom SEO desc, then brand description, then default
    if (seoPreviewDesc) {
      if (customDesc) {
        seoPreviewDesc.textContent = customDesc;
      } else if (brandDesc) {
        seoPreviewDesc.textContent = brandDesc;
      } else {
        seoPreviewDesc.textContent = 'Brand description will appear in search results...';
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
      const cleanBase = baseUrl.replace(/\/+$/, '') + '/';
      seoPreviewUrl.textContent = cleanBase + (currentSlug || 'handle');
    }
  }

  // Attach real-time listeners for all inputs
  const watchInputs = [brandNameInput, brandDescInput, seoTitleInput, seoDescInput, slugInput];
  watchInputs.forEach(input => {
    if (!input) return;
    ['input', 'keyup', 'change', 'paste'].forEach(evt => {
      input.addEventListener(evt, () => {
        if (input === slugInput) {
          slugInput.dataset.touched = 'true';
        }
        if (input === brandNameInput && slugInput && !slugInput.dataset.touched) {
          const autoSlug = brandNameInput.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
          slugInput.value = autoSlug;
        }
        updateSeoPreview();
      });
    });
  });

  // Run immediately on page load
  updateSeoPreview();

  // ==========================================================================
  // 2. Brand Logo Box Upload & Preview & Remove Cross Button
  // ==========================================================================
  const brandImageBox = document.getElementById('brandImageBox');
  const brandLogoInput = document.getElementById('brandLogoInput');
  const brandPreviewImg = document.getElementById('brandPreviewImg');
  const brandUploadIcon = document.getElementById('brandUploadIcon');
  const btnRemoveBrandLogo = document.getElementById('btnRemoveBrandLogo');
  const brandLogoUrl = document.getElementById('brandLogoUrl');

  // Trigger file selection when logo box is clicked (excluding remove button)
  if (brandImageBox && brandLogoInput) {
    brandImageBox.addEventListener('click', (e) => {
      if (e.target.closest('#btnRemoveBrandLogo')) return;
      brandLogoInput.click();
    });
  }

  // Handle selected logo file preview
  if (brandLogoInput) {
    brandLogoInput.addEventListener('change', function() {
      if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
          if (brandPreviewImg) {
            brandPreviewImg.src = e.target.result;
            brandPreviewImg.classList.remove('d-none');
          }
          if (brandUploadIcon) brandUploadIcon.classList.add('d-none');
          if (btnRemoveBrandLogo) btnRemoveBrandLogo.classList.remove('d-none');
        };
        reader.readAsDataURL(this.files[0]);
      }
    });
  }

  // Cross / Remove Button click handler
  if (btnRemoveBrandLogo) {
    btnRemoveBrandLogo.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation();

      if (brandLogoInput) {
        brandLogoInput.value = '';
      }
      if (brandLogoUrl) {
        brandLogoUrl.value = '';
      }
      if (brandPreviewImg) {
        brandPreviewImg.src = '';
        brandPreviewImg.classList.add('d-none');
      }
      btnRemoveBrandLogo.classList.add('d-none');
      if (brandUploadIcon) {
        brandUploadIcon.classList.remove('d-none');
      }
    });
  }

  // Handle broken image error fallback
  if (brandPreviewImg) {
    brandPreviewImg.addEventListener('error', function() {
      this.classList.add('d-none');
      if (brandUploadIcon) brandUploadIcon.classList.remove('d-none');
      if (btnRemoveBrandLogo) btnRemoveBrandLogo.classList.add('d-none');
    });
  }

  // ==========================================================================
  // 3. Rich Text Editor (RTE) Toolbar Execution & Form Sync
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
            if (!sel || sel.rangeCount === 0 || sel.isCollapsed) {
              content.focus();
              return;
            }

            const range = sel.getRangeAt(0);
            let commonNode = range.commonAncestorContainer;
            if (commonNode.nodeType === 3) commonNode = commonNode.parentNode;

            const existingHighlight = getHighlightParent(commonNode);

            if (existingHighlight) {
              unwrapHighlight(existingHighlight);
            } else {
              const highlightColor = '#fef08a';
              document.execCommand('hiliteColor', false, highlightColor);

              content.querySelectorAll('*').forEach(el => {
                const bg = (el.style.backgroundColor || el.style.background || '').toLowerCase().replace(/\s+/g, '');
                if (bg.includes('254,240,138') || bg.includes('#fef08a') || bg.includes('rgb(255,255,0)') || bg.includes('#ffff00')) {
                  el.style.backgroundColor = '#fef08a';
                  el.style.background = '#fef08a';
                  el.classList.add('rte-highlight');
                }
              });
            }
          } else if (cmd === 'removeFormat') {
            document.execCommand('removeFormat', false, null);
            document.execCommand('unlink', false, null);

            const sel = window.getSelection();
            if (sel && sel.rangeCount > 0) {
              const range = sel.getRangeAt(0);
              content.querySelectorAll('mark, .rte-highlight, [style*="background"]').forEach(el => {
                if (sel.containsNode(el, true) || range.intersectsNode(el)) {
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
  // 4. Header Banner Quick Links Selection & Live Search Filter
  // ==========================================================================
  const searchHeaderInput = document.getElementById('searchHeaderCollectionsInput');
  const headerGrid = document.getElementById('headerCollectionsGrid');
  const headerBadge = document.getElementById('selectedHeaderCountBadge');
  const btnClearAllHeader = document.getElementById('btnClearAllHeader');

  function updateHeaderCount() {
    if (!headerGrid || !headerBadge) return;
    const checkedBoxes = headerGrid.querySelectorAll('.header-col-checkbox:checked');
    const count = checkedBoxes.length;
    headerBadge.textContent = `${count} selected`;

    // Update visual card styles
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
    headerGrid.addEventListener('change', (e) => {
      if (e.target && e.target.classList.contains('header-col-checkbox')) {
        updateHeaderCount();
      }
    });

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

    if (btnClearAllHeader) {
      btnClearAllHeader.addEventListener('click', (e) => {
        e.preventDefault();
        headerGrid.querySelectorAll('.header-col-checkbox').forEach(cb => {
          cb.checked = false;
        });
        updateHeaderCount();
      });
    }

    updateHeaderCount();
  }

  // ==========================================================================
  // 5. Explore Related Collections Selection & Live Search Filter
  // ==========================================================================
  const searchRelInput = document.getElementById('searchRelatedCollectionsInput');
  const relGrid = document.getElementById('relatedCollectionsGrid');
  const relBadge = document.getElementById('selectedRelCountBadge');
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
    relGrid.addEventListener('change', (e) => {
      if (e.target && e.target.classList.contains('related-col-checkbox')) {
        updateRelCount();
      }
    });

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

    if (btnClearAllRel) {
      btnClearAllRel.addEventListener('click', (e) => {
        e.preventDefault();
        relGrid.querySelectorAll('.related-col-checkbox').forEach(cb => {
          cb.checked = false;
        });
        updateRelCount();
      });
    }

    updateRelCount();
  }
});
