/**
 * New Vape - Theme Sections Interactive Manager JavaScript
 * Handles live search filtering, Hero slider dynamic addition/deletion,
 * live visual banner preview, rich text editor, and compliance insertions.
 */
document.addEventListener('DOMContentLoaded', () => {
  // 0. Filter Sections on Search Input
  const sectionSearchInput = document.getElementById('sectionSearchInput');
  const navItems = document.querySelectorAll('#themeNavList .theme-nav-item');
  if (sectionSearchInput) {
    sectionSearchInput.addEventListener('input', () => {
      const q = sectionSearchInput.value.toLowerCase().trim();
      navItems.forEach(item => {
        const text = item.textContent.toLowerCase();
        const kw = item.getAttribute('data-keywords') || '';
        if (text.includes(q) || kw.includes(q)) {
          item.style.display = '';
        } else {
          item.style.display = 'none';
        }
      });
    });
  }

  // 1. Hero Slides Realtime Feedback
  const bindHeroCard = (card) => {
    const badgeInput = card.querySelector('.slide-badge-input');
    const titleInput = card.querySelector('.slide-title-input');
    const subtitleInput = card.querySelector('.slide-subtitle-input');
    const btnTextInput = card.querySelector('.slide-btn-text-input');
    const imgInput = card.querySelector('.slide-img-input');

    const previewBadge = card.querySelector('.slide-preview-badge');
    const previewTitle = card.querySelector('.slide-preview-title');
    const previewSubtitle = card.querySelector('.slide-preview-subtitle');
    const previewBtnText = card.querySelector('.slide-preview-btn-text');
    const previewImg = card.querySelector('.slide-preview-img');
    const headerTitle = card.querySelector('.slide-header-title');

    if (badgeInput && previewBadge) {
      badgeInput.addEventListener('input', () => {
        previewBadge.textContent = badgeInput.value || 'NEW FLAVORS';
      });
    }

    if (titleInput && previewTitle) {
      titleInput.addEventListener('input', () => {
        previewTitle.textContent = titleInput.value || 'Banner Title';
        if (headerTitle) headerTitle.textContent = titleInput.value || 'Untitled';
      });
    }

    if (subtitleInput && previewSubtitle) {
      subtitleInput.addEventListener('input', () => {
        previewSubtitle.textContent = subtitleInput.value || '';
      });
    }

    if (btnTextInput && previewBtnText) {
      btnTextInput.addEventListener('input', () => {
        previewBtnText.textContent = btnTextInput.value || 'Shop Now';
      });
    }

    if (imgInput && previewImg) {
      imgInput.addEventListener('input', () => {
        previewImg.src = imgInput.value;
      });
    }

    // Remove slide
    const removeBtn = card.querySelector('.remove-slide-btn');
    if (removeBtn) {
      removeBtn.addEventListener('click', () => {
        const total = document.querySelectorAll('.hero-slide-card').length;
        if (total <= 1) {
          alert('You must have at least one hero banner slide.');
          return;
        }
        if (confirm('Are you sure you want to delete this slide?')) {
          card.remove();
          reindexHeroSlides();
        }
      });
    }
  };

  const reindexHeroSlides = () => {
    const cards = document.querySelectorAll('.hero-slide-card');
    cards.forEach((card, index) => {
      card.setAttribute('data-slide-index', index);
      const badgeIndex = card.querySelector('span[style*="background: #f97316"]');
      if (badgeIndex) badgeIndex.textContent = index + 1;
      
      card.querySelectorAll('input, textarea, select').forEach(input => {
        const name = input.getAttribute('name');
        if (name) {
          input.setAttribute('name', name.replace(/slides\[\d+\]/, `slides[${index}]`));
        }
      });
    });
  };

  document.querySelectorAll('.hero-slide-card').forEach(bindHeroCard);

  // Add New Slide Button
  const addBtn = document.getElementById('addNewSlideBtn');
  const container = document.getElementById('slidesContainer');
  if (addBtn && container) {
    addBtn.addEventListener('click', () => {
      const cards = document.querySelectorAll('.hero-slide-card');
      const newIndex = cards.length;
      
      const newCardHtml = `
      <div class="hero-slide-card" data-slide-index="${newIndex}" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 18px;">
          <div style="display: flex; align-items: center; gap: 10px;">
            <span style="display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 6px; background: #f97316; color: #fff; font-weight: 700; font-size: 12px;">
              ${newIndex + 1}
            </span>
            <h3 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">Slide #${newIndex + 1}: <span class="slide-header-title">New Banner Slide</span></h3>
          </div>
          <div style="display: flex; align-items: center; gap: 14px;">
            <label class="toggle-switch-wrapper">
              <span style="font-size: 12px; font-weight: 600; color: #475569;">Active:</span>
              <div class="toggle-switch">
                <input type="hidden" name="slides[${newIndex}][is_active]" value="0">
                <input type="checkbox" name="slides[${newIndex}][is_active]" value="1" checked class="slide-active-toggle">
                <span class="toggle-slider"></span>
              </div>
            </label>
            <button type="button" class="btn-admin btn-admin-danger remove-slide-btn" style="padding: 5px 9px; font-size: 12px; height: auto;" title="Remove this slide">
              <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
              </svg>
              <span>Delete</span>
            </button>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; align-items: start;">
          <div style="display: flex; flex-direction: column; gap: 12px;">
            <div class="admin-form-group" style="margin: 0;">
              <label class="admin-form-label" style="font-weight: 600; font-size: 12px;">Badge / Pill Tag</label>
              <input type="text" name="slides[${newIndex}][badge_text]" class="admin-form-input slide-badge-input" value="FEATURED SPECIAL" placeholder="e.g. NEW FLAVORS">
            </div>

            <div class="admin-form-group" style="margin: 0;">
              <label class="admin-form-label" style="font-weight: 600; font-size: 12px;">Main Heading / Title <span style="color: #ef4444;">*</span></label>
              <input type="text" name="slides[${newIndex}][title]" class="admin-form-input slide-title-input" value="Premium Disposable Collection" placeholder="e.g. Every Flavor. In Stock." required>
            </div>

            <div class="admin-form-group" style="margin: 0;">
              <label class="admin-form-label" style="font-weight: 600; font-size: 12px;">Subtitle / Description</label>
              <textarea name="slides[${newIndex}][subtitle]" class="admin-form-input slide-subtitle-input" rows="2">Explore our top-rated disposables and e-liquids.</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
              <div class="admin-form-group" style="margin: 0;">
                <label class="admin-form-label" style="font-weight: 600; font-size: 12px;">CTA Button Text</label>
                <input type="text" name="slides[${newIndex}][button_text]" class="admin-form-input slide-btn-text-input" value="Shop Now" placeholder="e.g. Find Your Flavor">
              </div>
              <div class="admin-form-group" style="margin: 0;">
                <label class="admin-form-label" style="font-weight: 600; font-size: 12px;">CTA Target URL</label>
                <input type="text" name="slides[${newIndex}][button_url]" class="admin-form-input slide-btn-url-input" value="category" placeholder="e.g. category?b=foger">
              </div>
            </div>

            <div class="admin-form-group" style="margin: 0;">
              <label class="admin-form-label" style="font-weight: 600; font-size: 12px;">Banner Image Path</label>
              <input type="text" name="slides[${newIndex}][image_url]" class="admin-form-input slide-img-input" value="" placeholder="e.g. assets/images/hero-banner.webp">
            </div>
          </div>

          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.05em;">Live Preview</label>
            <div class="theme-card-preview" style="min-height: 200px;">
              <img src="assets/images/no-image.svg" alt="Banner Preview" class="theme-card-preview-bg slide-preview-img" onerror="this.onerror=null;this.src='assets/images/no-image.svg'">
              <div class="theme-card-preview-overlay"></div>
              <div class="theme-card-preview-content" style="padding: 18px;">
                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 3px 8px; background: rgba(249, 115, 22, 0.25); border: 1px solid rgba(249, 115, 22, 0.5); border-radius: 999px; color: #f97316; font-size: 10px; font-weight: 800; text-transform: uppercase; margin-bottom: 8px;">
                  <span style="width: 4px; height: 4px; background: #f97316; border-radius: 50%;"></span>
                  <span class="slide-preview-badge">FEATURED SPECIAL</span>
                </div>
                <h3 class="slide-preview-title" style="font-size: 17px; font-weight: 800; color: #ffffff; line-height: 1.2; margin: 0 0 6px 0;">Premium Disposable Collection</h3>
                <p class="slide-preview-subtitle" style="font-size: 12px; color: #e2e8f0; line-height: 1.35; margin: 0 0 12px 0;">Explore our top-rated disposables and e-liquids.</p>
                <div class="slide-preview-btn" style="display: inline-flex; align-items: center; gap: 5px; background: #f97316; color: #fff; font-size: 11px; font-weight: 700; padding: 7px 14px; border-radius: 6px;">
                  <span class="slide-preview-btn-text">Shop Now</span>
                  <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>`;

      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = newCardHtml;
      const newCardEl = tempDiv.firstElementChild;
      container.appendChild(newCardEl);
      bindHeroCard(newCardEl);
      newCardEl.scrollIntoView({ behavior: 'smooth' });
    });
  }

  // 2. Announcements Realtime Feedback
  const fdaInput = document.getElementById('fdaTextInput');
  const fdaPreview = document.getElementById('fdaPreviewText');
  const fdaToggle = document.getElementById('fdaActiveToggle');
  const fdaBar = document.getElementById('fdaPreviewBar');

  if (fdaInput && fdaPreview) {
    fdaInput.addEventListener('input', () => {
      fdaPreview.innerHTML = fdaInput.value;
    });
  }
  if (fdaToggle && fdaBar) {
    fdaToggle.addEventListener('change', () => {
      fdaBar.style.opacity = fdaToggle.checked ? '1' : '0.35';
    });
  }

  const promoInput = document.getElementById('promoTextInput');
  const promoPreview = document.getElementById('promoPreviewText');
  const promoToggle = document.getElementById('promoActiveToggle');
  const promoBar = document.getElementById('promoPreviewBar');

  if (promoInput && promoPreview) {
    promoInput.addEventListener('input', () => {
      promoPreview.textContent = promoInput.value;
    });
  }
  if (promoToggle && promoBar) {
    promoToggle.addEventListener('change', () => {
      promoBar.style.opacity = promoToggle.checked ? '1' : '0.35';
    });
  }

  // 3. Twin Promo Realtime Feedback
  document.querySelectorAll('.twin-card-editor').forEach(editor => {
    const titleInput = editor.querySelector('.twin-title-input');
    const subtitleInput = editor.querySelector('.twin-subtitle-input');
    const btnTextInput = editor.querySelector('.twin-btn-text-input');
    const imgInput = editor.querySelector('.twin-img-input');

    const previewTitle = editor.querySelector('.twin-preview-title');
    const previewSubtitle = editor.querySelector('.twin-preview-subtitle');
    const previewBtnText = editor.querySelector('.twin-preview-btn-text');
    const previewImg = editor.querySelector('.twin-preview-img');

    if (titleInput && previewTitle) {
      titleInput.addEventListener('input', () => {
        previewTitle.innerHTML = titleInput.value;
      });
    }
    if (subtitleInput && previewSubtitle) {
      subtitleInput.addEventListener('input', () => {
        previewSubtitle.innerHTML = subtitleInput.value;
      });
    }
    if (btnTextInput && previewBtnText) {
      btnTextInput.addEventListener('input', () => {
        previewBtnText.textContent = btnTextInput.value;
      });
    }
    if (imgInput && previewImg) {
      imgInput.addEventListener('input', () => {
        previewImg.src = imgInput.value;
      });
    }
  });

  // 4. Trusted Store & SEO Content Single Rich Text Editor
  const seoToggle = document.getElementById('seoActiveToggle');
  const seoBox = document.getElementById('seoPreviewBox');
  const seoTitleInput = document.getElementById('seoTitleInput');
  const seoPreviewHeading = document.getElementById('seoPreviewHeading');
  const seoPreviewBody = document.getElementById('seoPreviewBody');

  const richEditorVisual = document.getElementById('richEditorVisual');
  const seoContentTextarea = document.getElementById('seoContentTextarea');
  const toggleCodeViewBtn = document.getElementById('toggleCodeViewBtn');
  const codeViewBtnText = document.getElementById('codeViewBtnText');
  let isCodeView = false;

  const syncToPreview = () => {
    const html = isCodeView ? (seoContentTextarea ? seoContentTextarea.value : '') : (richEditorVisual ? richEditorVisual.innerHTML : '');
    if (seoPreviewBody) {
      seoPreviewBody.innerHTML = html;
    }
  };

  const syncInputs = () => {
    if (!isCodeView && richEditorVisual && seoContentTextarea) {
      seoContentTextarea.value = richEditorVisual.innerHTML;
    }
    syncToPreview();
  };

  if (seoToggle && seoBox) {
    seoToggle.addEventListener('change', () => {
      seoBox.style.opacity = seoToggle.checked ? '1' : '0.35';
    });
  }

  if (seoTitleInput && seoPreviewHeading) {
    seoTitleInput.addEventListener('input', () => {
      seoPreviewHeading.textContent = seoTitleInput.value || 'Your Trusted Online Vape Store';
    });
  }

  if (richEditorVisual) {
    richEditorVisual.addEventListener('input', syncInputs);
    richEditorVisual.addEventListener('blur', syncInputs);
  }

  if (seoContentTextarea) {
    seoContentTextarea.addEventListener('input', () => {
      if (isCodeView && richEditorVisual) {
        richEditorVisual.innerHTML = seoContentTextarea.value;
      }
      syncToPreview();
    });
  }

  // Formatting Toolbar Buttons (Bold, Italic, Headings, Lists, etc.)
  document.querySelectorAll('.rich-btn[data-cmd]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      if (isCodeView || !richEditorVisual) return;

      const cmd = btn.getAttribute('data-cmd');
      const val = btn.getAttribute('data-val') || null;

      richEditorVisual.focus();
      document.execCommand(cmd, false, val);
      syncInputs();
    });
  });

  // Insert Link
  const insertLinkBtn = document.getElementById('insertLinkBtn');
  if (insertLinkBtn && richEditorVisual) {
    insertLinkBtn.addEventListener('click', (e) => {
      e.preventDefault();
      if (isCodeView) return;

      const url = prompt('Enter destination URL (e.g. category?cat=disposables or https://...):', 'category');
      if (url) {
        richEditorVisual.focus();
        document.execCommand('createLink', false, url);
        syncInputs();
      }
    });
  }

  // Insert Disclaimer Notice
  const insertDisclaimerBtn = document.getElementById('insertDisclaimerBtn');
  if (insertDisclaimerBtn && richEditorVisual) {
    insertDisclaimerBtn.addEventListener('click', (e) => {
      e.preventDefault();
      if (isCodeView) return;

      richEditorVisual.focus();
      const selection = window.getSelection();
      const selectedText = selection.toString() || 'All products on this site are intended for adults of legal smoking age only. Keep all products out of the reach of children and pets.';
      const disclaimerHtml = `<p class="seo-content-disclaimer">${selectedText}</p>`;
      document.execCommand('insertHTML', false, disclaimerHtml);
      syncInputs();
    });
  }

  // Insert Compliance Statement
  const insertComplianceBtn = document.getElementById('insertComplianceBtn');
  if (insertComplianceBtn && richEditorVisual) {
    insertComplianceBtn.addEventListener('click', (e) => {
      e.preventDefault();
      if (isCodeView) return;

      richEditorVisual.focus();
      const selection = window.getSelection();
      const selectedText = selection.toString() || 'Our store is fully PACT Act compliant. We verify every order meets federal and state regulations so you can shop our online vape store worry-free.';
      const compHtml = `<p class="seo-content-compliance">${selectedText}</p>`;
      document.execCommand('insertHTML', false, compHtml);
      syncInputs();
    });
  }

  // Toggle HTML Code View / Visual View
  if (toggleCodeViewBtn && richEditorVisual && seoContentTextarea) {
    toggleCodeViewBtn.addEventListener('click', (e) => {
      e.preventDefault();
      isCodeView = !isCodeView;

      if (isCodeView) {
        seoContentTextarea.value = richEditorVisual.innerHTML;
        richEditorVisual.style.display = 'none';
        seoContentTextarea.style.display = 'block';
        if (codeViewBtnText) codeViewBtnText.textContent = 'Visual View';
        toggleCodeViewBtn.classList.add('btn-admin-primary');
        toggleCodeViewBtn.classList.remove('btn-admin-secondary');
      } else {
        richEditorVisual.innerHTML = seoContentTextarea.value;
        seoContentTextarea.style.display = 'none';
        richEditorVisual.style.display = 'block';
        if (codeViewBtnText) codeViewBtnText.textContent = 'HTML View';
        toggleCodeViewBtn.classList.remove('btn-admin-primary');
        toggleCodeViewBtn.classList.add('btn-admin-secondary');
      }
      syncToPreview();
    });
  }

  // On form submit, guarantee textarea has current content
  const seoForm = document.getElementById('seoContentForm');
  if (seoForm) {
    seoForm.addEventListener('submit', () => {
      if (!isCodeView && richEditorVisual && seoContentTextarea) {
        seoContentTextarea.value = richEditorVisual.innerHTML;
      }
    });
  }
});
