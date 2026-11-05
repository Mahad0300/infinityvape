/**
 * Infinity Vape Shop - Add Blog Post Page JavaScript
 * Handles auto slug generation, Rich Text Editor toolbar, SEO panel toggle, live SEO preview, char counters, and featured image preview.
 */
document.addEventListener('DOMContentLoaded', () => {
  const toggleSeoBtn = document.getElementById('toggleSeoBtn');
  const seoFieldsContainer = document.getElementById('seoFieldsContainer');
  const titleInput = document.getElementById('blogTitleInput');
  const slugInput = document.getElementById('blogSlugInput');
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
  const blogForm = document.getElementById('blogPostForm');

  // ==========================================================================
  // 1. Rich Text Editor (RTE) Toolbar Execution & Form Sync (Collection Style)
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

      toolbar.querySelectorAll('button').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          const cmd = btn.getAttribute('data-command');
          const val = btn.getAttribute('data-value') || null;

          if (cmd === 'createLink') {
            const url = prompt('Enter link URL (e.g. https://... or /blog/...)', 'https://');
            if (url) {
              document.execCommand(cmd, false, url);
            }
          } else if (cmd === 'highlight') {
            const sel = window.getSelection();
            if (sel && sel.rangeCount > 0) {
              const range = sel.getRangeAt(0);
              const parentH = getHighlightParent(range.commonAncestorContainer) || getHighlightParent(range.startContainer);
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
          syncRte();
        });
      });

      function syncRte() {
        cleanJunkStyles(content);
        const html = content.innerHTML.trim();
        const textContent = content.textContent.trim();
        if (textarea) {
          textarea.value = (textContent === '' && !html.includes('<img')) ? '' : html;
        }
      }

      content.addEventListener('input', syncRte);
      content.addEventListener('blur', syncRte);
      content.addEventListener('paste', () => {
        setTimeout(syncRte, 50);
      });

      // Synchronize before submitting form
      if (blogForm) {
        blogForm.addEventListener('submit', () => {
          syncRte();
        });
      }
    }
  });

  // ==========================================================================
  // 2. Search Engine Listing (SEO) Live Preview & Toggle
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

  // Auto-generate slug from title
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

  // Live SEO Title counter & preview
  if (seoTitleInput) {
    seoTitleInput.addEventListener('input', () => {
      if (seoTitleCount) seoTitleCount.textContent = `${seoTitleInput.value.length}`;
      if (previewTitle) {
        previewTitle.textContent = seoTitleInput.value.trim() || (titleInput ? titleInput.value.trim() : '') || 'Article Title';
      }
    });
  }

  // Live SEO Description counter & preview
  if (seoDescInput) {
    seoDescInput.addEventListener('input', () => {
      if (seoDescCount) seoDescCount.textContent = `${seoDescInput.value.length}`;
      if (previewDesc) {
        previewDesc.textContent = seoDescInput.value.trim() || (excerptInput ? excerptInput.value.trim() : '') || 'Write a concise meta description to see live snippet preview here.';
      }
    });
  }

  // Excerpt fallback into SEO preview if meta description is empty
  if (excerptInput) {
    excerptInput.addEventListener('input', () => {
      if (seoDescInput && !seoDescInput.value.trim() && previewDesc) {
        previewDesc.textContent = excerptInput.value.trim() || 'Write a concise meta description to see live snippet preview here.';
      }
    });
  }

  // Featured Image File Reader Preview
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
