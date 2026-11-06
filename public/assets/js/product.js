/**
 * PRODUCT DETAILS PAGE JAVASCRIPT
 * Handles image gallery switching, quantity counter, flavor selection, and add to cart feedback.
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Interactive Image Gallery (Thumbnails -> Main Image with Horizontal Scroll)
  const mainImage = document.getElementById('productMainImage');
  const thumbButtons = document.querySelectorAll('.product-thumb-btn');
  const thumbTrack = document.getElementById('productThumbnailsTrack');
  const thumbPrevBtn = document.getElementById('thumbScrollPrev');
  const thumbNextBtn = document.getElementById('thumbScrollNext');

  if (mainImage && thumbButtons.length > 0) {
    thumbButtons.forEach((btn) => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const newSrc = btn.getAttribute('data-img');
        if (!newSrc || mainImage.getAttribute('src') === newSrc) return;

        // Active state highlight
        thumbButtons.forEach((b) => b.classList.remove('active'));
        btn.classList.add('active');

        // Scroll clicked thumbnail into view
        btn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });

        // Smooth image switch transition
        mainImage.style.opacity = '0.3';
        mainImage.style.transform = 'scale(0.97)';

        setTimeout(() => {
          mainImage.src = newSrc;
          mainImage.style.opacity = '1';
          mainImage.style.transform = '';
        }, 150);
      });
    });

    // Main Image Hover Navigation (< and > on Main Large Image)
    const mainImgPrevBtn = document.getElementById('mainImgPrev');
    const mainImgNextBtn = document.getElementById('mainImgNext');

    function getActiveThumbIndex() {
      const activeBtn = document.querySelector('.product-thumb-btn.active');
      if (!activeBtn) return 0;
      const idx = Array.from(thumbButtons).indexOf(activeBtn);
      return idx >= 0 ? idx : 0;
    }

    function goToImageIndex(idx) {
      if (idx < 0) idx = thumbButtons.length - 1;
      if (idx >= thumbButtons.length) idx = 0;
      const targetBtn = thumbButtons[idx];
      if (targetBtn) {
        targetBtn.click();
      }
    }

    if (mainImgPrevBtn) {
      mainImgPrevBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        goToImageIndex(getActiveThumbIndex() - 1);
      });
    }

    if (mainImgNextBtn) {
      mainImgNextBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        goToImageIndex(getActiveThumbIndex() + 1);
      });
    }

    // Horizontal Scroll with Left / Right Buttons (Visible ONLY when thumbnails overflow)
    if (thumbTrack && thumbPrevBtn && thumbNextBtn) {
      const thumbWrap = thumbTrack.closest('.product-thumbnails-wrap');

      function updateThumbScrollButtons() {
        if (!thumbTrack || !thumbWrap) return;

        // Accurate overflow detection: check actual button widths vs available container
        let totalThumbsWidth = 0;
        thumbButtons.forEach((btn, i) => {
          totalThumbsWidth += (btn.offsetWidth || 76);
          if (i > 0) totalThumbsWidth += 10;
        });

        // Add small padding buffer
        totalThumbsWidth += 8;

        // Available width inside gallery column without scroll buttons
        const availableWidth = thumbWrap.clientWidth || thumbTrack.clientWidth || 0;
        
        // Overflow ONLY if thumbnails genuinely exceed available container width
        const hasOverflow = (thumbButtons.length > 1) && (availableWidth > 0 ? (totalThumbsWidth > (availableWidth - 10)) : (thumbButtons.length > 5));

        thumbWrap.classList.toggle('has-scroll', hasOverflow);

        if (!hasOverflow) {
          thumbPrevBtn.style.display = 'none';
          thumbNextBtn.style.display = 'none';
        } else {
          thumbPrevBtn.style.display = 'flex';
          thumbNextBtn.style.display = 'flex';

          const atStart = thumbTrack.scrollLeft <= 4;
          const atEnd = (thumbTrack.scrollLeft + thumbTrack.clientWidth) >= (thumbTrack.scrollWidth - 6);

          thumbPrevBtn.disabled = atStart;
          thumbPrevBtn.classList.toggle('is-disabled', atStart);

          thumbNextBtn.disabled = atEnd;
          thumbNextBtn.classList.toggle('is-disabled', atEnd);
        }
      }

      thumbPrevBtn.addEventListener('click', (e) => {
        e.preventDefault();
        thumbTrack.scrollBy({ left: -220, behavior: 'smooth' });
      });

      thumbNextBtn.addEventListener('click', (e) => {
        e.preventDefault();
        thumbTrack.scrollBy({ left: 220, behavior: 'smooth' });
      });

      thumbTrack.addEventListener('scroll', updateThumbScrollButtons, { passive: true });
      window.addEventListener('resize', updateThumbScrollButtons, { passive: true });

      // Run on page load and after layout stabilizes
      updateThumbScrollButtons();
      setTimeout(updateThumbScrollButtons, 100);
      setTimeout(updateThumbScrollButtons, 350);
      window.addEventListener('load', updateThumbScrollButtons, { passive: true });

      // Mouse Wheel horizontal scroll over thumbnail track
      thumbTrack.addEventListener('wheel', (e) => {
        if (e.deltaY !== 0) {
          e.preventDefault();
          thumbTrack.scrollLeft += e.deltaY;
          updateThumbScrollButtons();
        }
      }, { passive: false });
    }
  }

  // 2. Quantity Counter Controls
  const qtyInput = document.getElementById('productQuantity');
  const qtyMinus = document.getElementById('qtyMinus');
  const qtyPlus = document.getElementById('qtyPlus');

  if (qtyInput && qtyMinus && qtyPlus) {
    qtyMinus.addEventListener('click', (e) => {
      e.preventDefault();
      let currentVal = parseInt(qtyInput.value, 10) || 1;
      if (currentVal > 1) {
        qtyInput.value = currentVal - 1;
      }
    });

    qtyPlus.addEventListener('click', (e) => {
      e.preventDefault();
      let currentVal = parseInt(qtyInput.value, 10) || 1;
      if (currentVal < 99) {
        qtyInput.value = currentVal + 1;
      }
    });
  }

  // 3. Add to Cart Feedback (Cart Drawer Handles Actual Cart Addition & Drawer Open)
  const btnAddToCart = document.getElementById('btnProductAddToCart');

  if (btnAddToCart) {
    btnAddToCart.addEventListener('click', (e) => {
      if (btnAddToCart.disabled || btnAddToCart.classList.contains('is-disabled')) {
        e.preventDefault();
        e.stopPropagation();
        return;
      }
      const originalText = btnAddToCart.innerHTML;
      const addedQty = parseInt(qtyInput ? qtyInput.value : '1', 10) || 1;

      // Visual feedback
      btnAddToCart.innerHTML = `ADDED (${addedQty})! ✓`;
      btnAddToCart.style.backgroundColor = '#10b981';
      btnAddToCart.style.color = '#ffffff';
      btnAddToCart.style.boxShadow = '0 6px 18px rgba(16, 185, 129, 0.4)';

      // Revert button after 1.8s
      setTimeout(() => {
        btnAddToCart.innerHTML = originalText;
        btnAddToCart.style.backgroundColor = '';
        btnAddToCart.style.color = '';
        btnAddToCart.style.boxShadow = '';
      }, 1800);
    });
  }

  // 3.1 Dynamic Multi-Option & Variant Badges Selector (Shopify / eCommerce Style)
  const variantsWrapper = document.getElementById('productVariantsWrapper');
  const flavorSelect = document.getElementById('flavorSelect');
  const variantsJsonEl = document.getElementById('productVariantsJson');

  let productVariantsList = [];
  if (variantsJsonEl) {
    try {
      productVariantsList = JSON.parse(variantsJsonEl.textContent || '[]');
    } catch (e) {
      console.error('Error parsing product variants JSON', e);
      productVariantsList = [];
    }
  }

  function getVariantForValues(selectedValues) {
    if (!productVariantsList || productVariantsList.length === 0) return null;
    const targetCombo = selectedValues.join(' / ').toLowerCase();

    let matched = productVariantsList.find(
      (v) => (v.variant_name || '').trim().toLowerCase() === targetCombo
    );

    if (!matched) {
      matched = productVariantsList.find((v) => {
        const parts = (v.variant_name || '').split(' / ').map((s) => s.trim().toLowerCase());
        if (parts.length !== selectedValues.length) return false;
        return selectedValues.every((val, i) => val.toLowerCase() === parts[i]);
      });
    }

    return matched || null;
  }

  function syncCustomItemState(cItem, inStock, isSelected) {
    if (!cItem) return;
    if (!inStock) {
      cItem.classList.add('is-disabled');
      cItem.setAttribute('aria-disabled', 'true');
      cItem.classList.remove('is-selected');
      cItem.setAttribute('aria-selected', 'false');
      if (!cItem.querySelector('.item-badge-out')) {
        const out = document.createElement('span');
        out.className = 'item-badge-out';
        out.textContent = 'Out of stock';
        cItem.appendChild(out);
      }
    } else {
      cItem.classList.remove('is-disabled');
      cItem.removeAttribute('aria-disabled');
      const out = cItem.querySelector('.item-badge-out');
      if (out) out.remove();
      cItem.classList.toggle('is-selected', isSelected);
      cItem.setAttribute('aria-selected', isSelected ? 'true' : 'false');
    }
  }

  function updateBadgesAvailability() {
    if (!variantsWrapper || productVariantsList.length === 0) return;

    const customSelects = Array.from(variantsWrapper.querySelectorAll('.custom-variant-select'));
    const dropdowns = Array.from(variantsWrapper.querySelectorAll('.variant-dropdown-select'));

    if (customSelects.length > 0) {
      const maxDims = customSelects.length;
      if (maxDims === 1) {
        const cSelect = customSelects[0];
        const select = cSelect.querySelector('.variant-dropdown-select');
        const menu = cSelect.querySelector('.custom-variant-menu');

        Array.from(select.options).forEach((opt) => {
          const val = opt.value.trim().toLowerCase();
          const v = productVariantsList.find(
            (item) => (item.variant_name || '').trim().toLowerCase() === val
          );
          const stock = v ? parseInt(v.stock_quantity, 10) : 0;
          const orig = opt.getAttribute('data-original-text') || opt.value;
          if (!opt.hasAttribute('data-original-text')) {
            opt.setAttribute('data-original-text', orig);
          }
          const inStock = stock > 0;
          opt.disabled = !inStock;
          opt.textContent = inStock ? orig : `${orig} (Out of stock)`;

          if (menu) {
            const cItem = menu.querySelector(`.custom-variant-item[data-value="${CSS.escape(opt.value)}"]`);
            syncCustomItemState(cItem, inStock, opt.value === select.value);
          }
        });

        // Ensure active selection is in-stock
        if (select.selectedOptions.length === 0 || select.selectedOptions[0].disabled) {
          const firstAvail = Array.from(select.options).find(o => !o.disabled);
          if (firstAvail) {
            select.value = firstAvail.value;
          }
        }
        const activeVal = select.value;
        const lbl = document.getElementById('selectedVal-0');
        if (lbl) lbl.textContent = activeVal;
        const trigVal = cSelect.querySelector('.custom-variant-current-val');
        if (trigVal) trigVal.textContent = activeVal;
        if (menu) {
          menu.querySelectorAll('.custom-variant-item').forEach(it => {
            const isMatch = it.getAttribute('data-value') === activeVal && !it.classList.contains('is-disabled');
            it.classList.toggle('is-selected', isMatch);
            it.setAttribute('aria-selected', isMatch ? 'true' : 'false');
          });
        }
      } else {
        // Multi-Option Dimensions (e.g. Option 0 = Flavor, Option 1 = Strength)
        const cSelect0 = customSelects[0];
        const select0 = cSelect0.querySelector('.variant-dropdown-select');
        const menu0 = cSelect0.querySelector('.custom-variant-menu');

        Array.from(select0.options).forEach((opt) => {
          const val0 = opt.value.trim().toLowerCase();
          const hasAnyInStock = productVariantsList.some((v) => {
            const parts = (v.variant_name || '').split(' / ').map((s) => s.trim().toLowerCase());
            const vStock = parseInt(v.stock_quantity, 10) || 0;
            return (parts[0] === val0) && (vStock > 0);
          });

          const orig = opt.getAttribute('data-original-text') || opt.value;
          if (!opt.hasAttribute('data-original-text')) {
            opt.setAttribute('data-original-text', orig);
          }
          opt.disabled = !hasAnyInStock;
          opt.textContent = hasAnyInStock ? orig : `${orig} (Out of stock)`;

          if (menu0) {
            const cItem = menu0.querySelector(`.custom-variant-item[data-value="${CSS.escape(opt.value)}"]`);
            syncCustomItemState(cItem, hasAnyInStock, opt.value === select0.value);
          }
        });

        if (select0.selectedOptions.length === 0 || select0.selectedOptions[0].disabled) {
          const firstAvail0 = Array.from(select0.options).find(o => !o.disabled);
          if (firstAvail0) {
            select0.value = firstAvail0.value;
          }
        }
        const activeVal0 = select0.value;
        const lbl0 = document.getElementById('selectedVal-0');
        if (lbl0) lbl0.textContent = activeVal0;
        const trigVal0 = cSelect0.querySelector('.custom-variant-current-val');
        if (trigVal0) trigVal0.textContent = activeVal0;
        if (menu0) {
          menu0.querySelectorAll('.custom-variant-item').forEach(it => {
            const isMatch = it.getAttribute('data-value') === activeVal0 && !it.classList.contains('is-disabled');
            it.classList.toggle('is-selected', isMatch);
            it.setAttribute('aria-selected', isMatch ? 'true' : 'false');
          });
        }

        const activeValues = [activeVal0 ? activeVal0.trim() : ''];

        for (let g = 1; g < maxDims; g++) {
          const cSelectG = customSelects[g];
          const selectG = cSelectG.querySelector('.variant-dropdown-select');
          const menuG = cSelectG.querySelector('.custom-variant-menu');

          Array.from(selectG.options).forEach((opt) => {
            const testVal = opt.value.trim();
            const testCombo = activeValues.slice(0, g);
            testCombo[g] = testVal;

            const matchedVar = getVariantForValues(testCombo);
            const vStock = matchedVar ? parseInt(matchedVar.stock_quantity, 10) : 0;
            const inStock = matchedVar && vStock > 0;
            const orig = opt.getAttribute('data-original-text') || opt.value;
            if (!opt.hasAttribute('data-original-text')) {
              opt.setAttribute('data-original-text', orig);
            }

            opt.disabled = !inStock;
            opt.textContent = inStock ? orig : `${orig} (Out of stock)`;

            if (menuG) {
              const cItem = menuG.querySelector(`.custom-variant-item[data-value="${CSS.escape(opt.value)}"]`);
              syncCustomItemState(cItem, inStock, opt.value === selectG.value);
            }
          });

          if (selectG.selectedOptions.length === 0 || selectG.selectedOptions[0].disabled) {
            const firstAvail = Array.from(selectG.options).find(o => !o.disabled);
            if (firstAvail) {
              selectG.value = firstAvail.value;
            }
          }
          const activeValG = selectG.value;
          const lblG = document.getElementById(`selectedVal-${g}`);
          if (lblG) lblG.textContent = activeValG;
          const trigValG = cSelectG.querySelector('.custom-variant-current-val');
          if (trigValG) trigValG.textContent = activeValG;
          if (menuG) {
            menuG.querySelectorAll('.custom-variant-item').forEach(it => {
              const isMatch = it.getAttribute('data-value') === activeValG && !it.classList.contains('is-disabled');
              it.classList.toggle('is-selected', isMatch);
              it.setAttribute('aria-selected', isMatch ? 'true' : 'false');
            });
          }

          activeValues[g] = activeValG ? activeValG.trim() : '';
        }
      }
      return;
    }

    if (dropdowns.length > 0) {
      const maxDims = dropdowns.length;
      if (maxDims === 1) {
        const select = dropdowns[0];
        Array.from(select.options).forEach((opt) => {
          const val = opt.value.trim().toLowerCase();
          const v = productVariantsList.find(
            (item) => (item.variant_name || '').trim().toLowerCase() === val
          );
          const stock = v ? parseInt(v.stock_quantity, 10) : 0;
          const orig = opt.getAttribute('data-original-text') || opt.value;
          if (!opt.hasAttribute('data-original-text')) {
            opt.setAttribute('data-original-text', orig);
          }
          if (stock <= 0) {
            opt.disabled = true;
            opt.textContent = `${orig} (Out of stock)`;
          } else {
            opt.disabled = false;
            opt.textContent = orig;
          }
        });

        // Ensure active selection is in-stock
        if (select.selectedOptions.length === 0 || select.selectedOptions[0].disabled) {
          const firstAvail = Array.from(select.options).find(o => !o.disabled);
          if (firstAvail) {
            select.value = firstAvail.value;
          }
        }
        const lbl = document.getElementById('selectedVal-0');
        if (lbl) lbl.textContent = select.value;
      } else {
        // Multi-Option Dimensions (e.g. Option 0 = Flavor, Option 1 = Strength)
        const select0 = dropdowns[0];
        Array.from(select0.options).forEach((opt) => {
          const val0 = opt.value.trim().toLowerCase();
          const hasAnyInStock = productVariantsList.some((v) => {
            const parts = (v.variant_name || '').split(' / ').map((s) => s.trim().toLowerCase());
            const vStock = parseInt(v.stock_quantity, 10) || 0;
            return (parts[0] === val0) && (vStock > 0);
          });

          const orig = opt.getAttribute('data-original-text') || opt.value;
          if (!opt.hasAttribute('data-original-text')) {
            opt.setAttribute('data-original-text', orig);
          }
          if (!hasAnyInStock) {
            opt.disabled = true;
            opt.textContent = `${orig} (Out of stock)`;
          } else {
            opt.disabled = false;
            opt.textContent = orig;
          }
        });

        if (select0.selectedOptions.length === 0 || select0.selectedOptions[0].disabled) {
          const firstAvail0 = Array.from(select0.options).find(o => !o.disabled);
          if (firstAvail0) {
            select0.value = firstAvail0.value;
          }
        }
        const lbl0 = document.getElementById('selectedVal-0');
        if (lbl0) lbl0.textContent = select0.value;

        const activeValues = [select0.value ? select0.value.trim() : ''];

        for (let g = 1; g < maxDims; g++) {
          const selectG = dropdowns[g];
          Array.from(selectG.options).forEach((opt) => {
            const testVal = opt.value.trim();
            const testCombo = activeValues.slice(0, g);
            testCombo[g] = testVal;

            const matchedVar = getVariantForValues(testCombo);
            const vStock = matchedVar ? parseInt(matchedVar.stock_quantity, 10) : 0;
            const orig = opt.getAttribute('data-original-text') || opt.value;
            if (!opt.hasAttribute('data-original-text')) {
              opt.setAttribute('data-original-text', orig);
            }

            if (!matchedVar || vStock <= 0) {
              opt.disabled = true;
              opt.textContent = `${orig} (Out of stock)`;
            } else {
              opt.disabled = false;
              opt.textContent = orig;
            }
          });

          if (selectG.selectedOptions.length === 0 || selectG.selectedOptions[0].disabled) {
            const firstAvail = Array.from(selectG.options).find(o => !o.disabled);
            if (firstAvail) {
              selectG.value = firstAvail.value;
            }
          }
          const lblG = document.getElementById(`selectedVal-${g}`);
          if (lblG) lblG.textContent = selectG.value;

          activeValues[g] = selectG.value ? selectG.value.trim() : '';
        }
      }
      return;
    }

    const groups = Array.from(variantsWrapper.querySelectorAll('.variant-option-group'));
    const maxDims = groups.length;
    if (maxDims === 0) return;

    if (maxDims === 1) {
      // Single Option Dimension (e.g. Only Flavor)
      const group = groups[0];
      const btns = group.querySelectorAll('.variant-badge-btn');
      btns.forEach((btn) => {
        const val = btn.getAttribute('data-value').trim().toLowerCase();
        const v = productVariantsList.find(
          (item) => (item.variant_name || '').trim().toLowerCase() === val
        );
        const stock = v ? parseInt(v.stock_quantity, 10) : 0;
        if (stock <= 0) {
          btn.disabled = true;
          btn.classList.add('is-out-of-stock', 'is-disabled');
          btn.setAttribute('aria-disabled', 'true');
          btn.setAttribute('title', `${btn.getAttribute('data-value')} (Out of stock)`);
        } else {
          btn.disabled = false;
          btn.classList.remove('is-out-of-stock', 'is-disabled');
          btn.removeAttribute('aria-disabled');
          btn.removeAttribute('title');
        }
      });

      // Ensure active button is in-stock
      let activeBtn = group.querySelector('.variant-badge-btn.active:not(:disabled)');
      if (!activeBtn) {
        const firstAvail = group.querySelector('.variant-badge-btn:not(:disabled)');
        if (firstAvail) {
          group.querySelectorAll('.variant-badge-btn').forEach((b) => {
            b.classList.remove('active');
            b.setAttribute('aria-pressed', 'false');
          });
          firstAvail.classList.add('active');
          firstAvail.setAttribute('aria-pressed', 'true');
          const lbl = document.getElementById('selectedVal-0');
          if (lbl) lbl.textContent = firstAvail.getAttribute('data-value');
        }
      }
    } else {
      // Multi-Option Dimensions (e.g. Option 0 = Flavor, Option 1 = Strength, etc.)
      
      // Step A: Evaluate Level 0 (Option 0: Flavor)
      // A flavor is out-of-stock ONLY if ALL its variants have stock <= 0
      const group0 = groups[0];
      group0.querySelectorAll('.variant-badge-btn').forEach((btn) => {
        const val0 = btn.getAttribute('data-value').trim().toLowerCase();
        const hasAnyInStock = productVariantsList.some((v) => {
          const parts = (v.variant_name || '').split(' / ').map((s) => s.trim().toLowerCase());
          const vStock = parseInt(v.stock_quantity, 10) || 0;
          return (parts[0] === val0) && (vStock > 0);
        });

        if (!hasAnyInStock) {
          btn.disabled = true;
          btn.classList.add('is-out-of-stock', 'is-disabled');
          btn.setAttribute('aria-disabled', 'true');
          btn.setAttribute('title', `${btn.getAttribute('data-value')} (Out of stock)`);
        } else {
          btn.disabled = false;
          btn.classList.remove('is-out-of-stock', 'is-disabled');
          btn.removeAttribute('aria-disabled');
          btn.removeAttribute('title');
        }
      });

      // Ensure Option 0 has an in-stock active selection
      let activeBtn0 = group0.querySelector('.variant-badge-btn.active:not(:disabled)');
      if (!activeBtn0) {
        const firstAvail0 = group0.querySelector('.variant-badge-btn:not(:disabled)');
        if (firstAvail0) {
          group0.querySelectorAll('.variant-badge-btn').forEach((b) => {
            b.classList.remove('active');
            b.setAttribute('aria-pressed', 'false');
          });
          firstAvail0.classList.add('active');
          firstAvail0.setAttribute('aria-pressed', 'true');
          activeBtn0 = firstAvail0;
          const lbl0 = document.getElementById('selectedVal-0');
          if (lbl0) lbl0.textContent = firstAvail0.getAttribute('data-value');
        }
      }

      const activeValues = [activeBtn0 ? activeBtn0.getAttribute('data-value').trim() : ''];

      // Step B: For Level 1..N, evaluate availability against current preceding choices
      for (let g = 1; g < maxDims; g++) {
        const grp = groups[g];
        const btns = grp.querySelectorAll('.variant-badge-btn');

        btns.forEach((btn) => {
          const testVal = btn.getAttribute('data-value').trim();
          const testCombo = activeValues.slice(0, g);
          testCombo[g] = testVal;

          const matchedVar = getVariantForValues(testCombo);
          const vStock = matchedVar ? parseInt(matchedVar.stock_quantity, 10) : 0;

          if (!matchedVar || vStock <= 0) {
            btn.disabled = true;
            btn.classList.add('is-out-of-stock', 'is-disabled');
            btn.setAttribute('aria-disabled', 'true');
            btn.setAttribute('title', `${testVal} (Out of stock)`);
          } else {
            btn.disabled = false;
            btn.classList.remove('is-out-of-stock', 'is-disabled');
            btn.removeAttribute('aria-disabled');
            btn.removeAttribute('title');
          }
        });

        // If the currently active badge in this group is now disabled, auto-switch to first available in-stock option
        let activeBtn = grp.querySelector('.variant-badge-btn.active:not(:disabled)');
        if (!activeBtn) {
          const firstAvail = grp.querySelector('.variant-badge-btn:not(:disabled)');
          if (firstAvail) {
            grp.querySelectorAll('.variant-badge-btn').forEach((b) => {
              b.classList.remove('active');
              b.setAttribute('aria-pressed', 'false');
            });
            firstAvail.classList.add('active');
            firstAvail.setAttribute('aria-pressed', 'true');
            activeBtn = firstAvail;
            const lbl = document.getElementById(`selectedVal-${g}`);
            if (lbl) lbl.textContent = firstAvail.getAttribute('data-value');
          }
        }

        activeValues[g] = activeBtn ? activeBtn.getAttribute('data-value').trim() : '';
      }
    }
  }

  function resolveSelectedVariant() {
    if (!variantsWrapper || productVariantsList.length === 0) return null;

    const customSelects = variantsWrapper.querySelectorAll('.custom-variant-select');
    if (customSelects.length > 0) {
      const selectedValues = [];
      customSelects.forEach((cSel) => {
        const activeItem = cSel.querySelector('.custom-variant-item.is-selected:not(.is-disabled)') || cSel.querySelector('.custom-variant-item.is-selected');
        const nativeSel = cSel.querySelector('.variant-dropdown-select');
        const val = activeItem ? activeItem.getAttribute('data-value') : (nativeSel ? nativeSel.value : '');
        if (val) {
          selectedValues.push(val.trim());
        }
      });
      if (selectedValues.length === 0) return null;
      const matched = getVariantForValues(selectedValues);
      return matched || productVariantsList[0];
    }

    const dropdowns = variantsWrapper.querySelectorAll('.variant-dropdown-select');
    if (dropdowns.length > 0) {
      const selectedValues = [];
      dropdowns.forEach((sel) => {
        if (sel.value) {
          selectedValues.push(sel.value.trim());
        }
      });
      if (selectedValues.length === 0) return null;
      const matched = getVariantForValues(selectedValues);
      return matched || productVariantsList[0];
    }

    const groups = variantsWrapper.querySelectorAll('.variant-option-group');
    const selectedValues = [];

    groups.forEach((group) => {
      const activeBtn = group.querySelector('.variant-badge-btn.active:not(:disabled)') || group.querySelector('.variant-badge-btn.active');
      if (activeBtn) {
        selectedValues.push(activeBtn.getAttribute('data-value').trim());
      }
    });

    if (selectedValues.length === 0) return null;

    const matched = getVariantForValues(selectedValues);
    return matched || productVariantsList[0];
  }

  function applyVariant(variant) {
    if (!variant) return;

    // 1. Sync hidden select for cart-drawer & external scripts
    if (flavorSelect) {
      flavorSelect.value = variant.variant_name;
      flavorSelect.dataset.selectedVariantId = variant.id || '';
    }

    const btnAddToCart = document.getElementById('btnProductAddToCart');
    const btnBuyNow = document.getElementById('btnProductBuyNow');
    if (btnAddToCart) {
      if (variant.id) btnAddToCart.dataset.variantId = variant.id;
      if (variant.product_id) btnAddToCart.dataset.productId = variant.product_id;
    }
    if (btnBuyNow) {
      if (variant.id) btnBuyNow.dataset.variantId = variant.id;
      if (variant.product_id) btnBuyNow.dataset.productId = variant.product_id;
    }

    const priceSection = document.querySelector('.product-price-section');
    const baseCompareAttr = priceSection && priceSection.dataset.baseCompare ? parseFloat(priceSection.dataset.baseCompare) : null;
    const basePriceAttr = priceSection && priceSection.dataset.basePrice ? parseFloat(priceSection.dataset.basePrice) : null;

    const vPrice = (variant.price !== undefined && variant.price !== null && variant.price !== '') ? parseFloat(variant.price) : basePriceAttr;
    let vCompare = (variant.compare_price !== undefined && variant.compare_price !== null && variant.compare_price !== '') 
      ? parseFloat(variant.compare_price) 
      : (baseCompareAttr && !isNaN(baseCompareAttr) && baseCompareAttr > 0 ? baseCompareAttr : null);
    const vSku = variant.sku;
    const vStock = variant.stock_quantity !== undefined ? parseInt(variant.stock_quantity, 10) : 0;
    const vImage = variant.image_url ? variant.image_url : null;

    // 2. Update Current Price
    const priceCurrentEl = document.querySelector('.product-price-current');
    if (priceCurrentEl && vPrice !== null && !isNaN(vPrice)) {
      priceCurrentEl.textContent = `$${vPrice.toFixed(2)}`;
    }

    // 3. Update Compare Price & Discount Pill
    let priceOldEl = document.querySelector('.product-price-old');
    let discountPill = document.querySelector('.product-discount-pill');

    if (vCompare && !isNaN(vCompare) && vPrice && vCompare > vPrice) {
      const pct = Math.round(((vCompare - vPrice) / vCompare) * 100);
      if (!priceOldEl && priceSection) {
        priceOldEl = document.createElement('span');
        priceOldEl.className = 'product-price-old';
        if (priceCurrentEl && priceCurrentEl.nextSibling) {
          priceSection.insertBefore(priceOldEl, priceCurrentEl.nextSibling);
        } else {
          priceSection.appendChild(priceOldEl);
        }
      }
      if (priceOldEl) {
        priceOldEl.textContent = `$${vCompare.toFixed(2)}`;
        priceOldEl.style.display = 'inline-block';
      }

      if (!discountPill && priceSection) {
        discountPill = document.createElement('span');
        discountPill.className = 'product-discount-pill';
        priceSection.appendChild(discountPill);
      }
      if (discountPill) {
        discountPill.textContent = `SAVE ${pct}%`;
        discountPill.style.display = 'inline-block';
      }
    } else {
      if (priceOldEl) priceOldEl.style.display = 'none';
      if (discountPill) discountPill.style.display = 'none';
    }

    // 4. Update SKU
    const skuEl = document.querySelector('.product-sku');
    if (skuEl && vSku) {
      skuEl.textContent = `SKU: ${vSku}`;
    }

    // 5. Update Stock Status & Buttons
    const stockStatusWrap = document.querySelector('.product-stock-status');
    const stockTextEl = document.querySelector('.product-stock-status .stock-text');
    const btnAddToCart = document.getElementById('btnProductAddToCart');
    const btnBuyNow = document.getElementById('btnProductBuyNow');

    if (stockStatusWrap && stockTextEl && !isNaN(vStock)) {
      if (vStock <= 0) {
        stockStatusWrap.className = 'product-stock-status out-of-stock';
        stockTextEl.innerHTML = '<strong>Out of Stock</strong>';
        if (btnAddToCart) {
          btnAddToCart.disabled = true;
          btnAddToCart.style.opacity = '0.6';
          btnAddToCart.style.cursor = 'not-allowed';
          btnAddToCart.textContent = 'Out of Stock';
        }
        if (btnBuyNow) {
          btnBuyNow.disabled = true;
          btnBuyNow.style.opacity = '0.6';
          btnBuyNow.style.cursor = 'not-allowed';
        }
      } else {
        stockStatusWrap.className = 'product-stock-status in-stock';
        stockTextEl.innerHTML = '<strong>In Stock</strong>';
        if (btnAddToCart) {
          btnAddToCart.disabled = false;
          btnAddToCart.style.opacity = '';
          btnAddToCart.style.cursor = '';
          btnAddToCart.textContent = 'Add to cart';
        }
        if (btnBuyNow) {
          btnBuyNow.disabled = false;
          btnBuyNow.style.opacity = '';
          btnBuyNow.style.cursor = '';
        }
      }
    }

    // 6. Switch Main Image & Alt Text if Variant has a custom photo (instant, no flash/blink)
    if (mainImage) {
      if (!mainImage.dataset.defaultSrc) {
        mainImage.dataset.defaultSrc = mainImage.getAttribute('src') || '';
      }
      if (!mainImage.dataset.baseAlt) {
        mainImage.dataset.baseAlt = mainImage.getAttribute('alt') || '';
      }

      const rawImg = variant.full_image_url || variant.image_url || vImage || '';
      let targetImg = '';

      if (rawImg) {
        if (rawImg.startsWith('http://') || rawImg.startsWith('https://') || rawImg.startsWith('data:')) {
          targetImg = rawImg;
        } else {
          const baseUrl = (window.APP_BASE_URL || '').replace(/\/+$/, '');
          const cleanPath = rawImg.replace(/^\/+/, '');
          if (baseUrl) {
            targetImg = `${baseUrl}/${cleanPath}`;
          } else {
            const baseEl = document.querySelector('base');
            if (baseEl && baseEl.href) {
              const bHref = baseEl.href.replace(/\/+$/, '');
              targetImg = `${bHref}/${cleanPath}`;
            } else {
              const pathParts = window.location.pathname.split('/');
              const rootFolder = pathParts[1] ? '/' + pathParts[1] : '';
              targetImg = window.location.origin + rootFolder + '/' + cleanPath;
            }
          }
        }
      } else {
        targetImg = mainImage.dataset.defaultSrc || '';
      }

      if (targetImg) {
        const currentSrc = mainImage.getAttribute('src') || '';
        if (currentSrc !== targetImg && !currentSrc.endsWith(targetImg)) {
          mainImage.src = targetImg;
        }
      }

      if (mainImage.dataset.baseAlt) {
        mainImage.alt = (variant.variant_name && targetImg !== mainImage.dataset.defaultSrc)
          ? `${mainImage.dataset.baseAlt} - ${variant.variant_name}`
          : mainImage.dataset.baseAlt;
      }

      if (thumbButtons && thumbButtons.length > 0 && targetImg) {
        let matchedThumb = false;
        thumbButtons.forEach((b) => {
          const bSrc = b.getAttribute('data-img') || '';
          if (bSrc === targetImg || bSrc.endsWith(targetImg) || targetImg.endsWith(bSrc)) {
            thumbButtons.forEach(btn => btn.classList.remove('active'));
            b.classList.add('active');
            matchedThumb = true;
          }
        });
        if (!matchedThumb && targetImg === mainImage.dataset.defaultSrc) {
          thumbButtons.forEach(btn => btn.classList.remove('active'));
          if (thumbButtons[0]) thumbButtons[0].classList.add('active');
        }
      }
    }
  }

  if (variantsWrapper) {
    const customSelects = variantsWrapper.querySelectorAll('.custom-variant-select');
    if (customSelects.length > 0) {
      customSelects.forEach((cSelect) => {
        const trigger = cSelect.querySelector('.custom-variant-trigger');
        const menu = cSelect.querySelector('.custom-variant-menu');
        const nativeSelect = cSelect.querySelector('.variant-dropdown-select');
        const optIndex = cSelect.getAttribute('data-option-index');

        if (trigger) {
          trigger.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const isOpen = cSelect.classList.contains('is-open');
            document.querySelectorAll('.custom-variant-select.is-open').forEach((s) => {
              if (s !== cSelect) {
                s.classList.remove('is-open');
                s.setAttribute('aria-expanded', 'false');
              }
            });
            cSelect.classList.toggle('is-open', !isOpen);
            cSelect.setAttribute('aria-expanded', !isOpen ? 'true' : 'false');
          });
        }

        if (menu) {
          menu.addEventListener('click', (e) => {
            const item = e.target.closest('.custom-variant-item');
            if (!item || item.classList.contains('is-disabled')) return;
            e.preventDefault();
            e.stopPropagation();

            const val = item.getAttribute('data-value');

            const currentValSpan = cSelect.querySelector('.custom-variant-current-val');
            if (currentValSpan) currentValSpan.textContent = val;

            const lbl = document.getElementById(`selectedVal-${optIndex}`);
            if (lbl) lbl.textContent = val;

            menu.querySelectorAll('.custom-variant-item').forEach(it => {
              it.classList.remove('is-selected');
              it.setAttribute('aria-selected', 'false');
            });
            item.classList.add('is-selected');
            item.setAttribute('aria-selected', 'true');

            cSelect.classList.remove('is-open');
            cSelect.setAttribute('aria-expanded', 'false');

            if (nativeSelect) {
              nativeSelect.value = val;
            }

            updateBadgesAvailability();

            const matched = resolveSelectedVariant();
            if (matched) {
              applyVariant(matched);
            }
          });
        }
      });

      document.addEventListener('click', (e) => {
        if (!e.target.closest('.custom-variant-select')) {
          document.querySelectorAll('.custom-variant-select.is-open').forEach((s) => {
            s.classList.remove('is-open');
            s.setAttribute('aria-expanded', 'false');
          });
          document.querySelectorAll('.fbt-item-card.has-open-select').forEach((c) => {
            c.classList.remove('has-open-select');
          });
        }
      });

      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          document.querySelectorAll('.custom-variant-select.is-open').forEach((s) => {
            s.classList.remove('is-open');
            s.setAttribute('aria-expanded', 'false');
          });
          document.querySelectorAll('.fbt-item-card.has-open-select').forEach((c) => {
            c.classList.remove('has-open-select');
          });
        }
      });

      // Initial setup on page load: compute availability and apply initial variant
      updateBadgesAvailability();
      const initialMatched = resolveSelectedVariant();
      if (initialMatched) {
        applyVariant(initialMatched);
      }
    } else {
      const badgeBtns = variantsWrapper.querySelectorAll('.variant-badge-btn');
      badgeBtns.forEach((btn) => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();

          // If option is disabled or out-of-stock, strictly prevent selection!
          if (btn.disabled || btn.classList.contains('is-out-of-stock') || btn.classList.contains('is-disabled')) {
            return;
          }

          const group = btn.closest('.variant-option-group');
          if (!group) return;

          const optIndex = group.getAttribute('data-option-index');
          const val = btn.getAttribute('data-value');

          group.querySelectorAll('.variant-badge-btn').forEach((b) => {
            b.classList.remove('active');
            b.setAttribute('aria-pressed', 'false');
          });
          btn.classList.add('active');
          btn.setAttribute('aria-pressed', 'true');

          const labelEl = document.getElementById(`selectedVal-${optIndex}`);
          if (labelEl) {
            labelEl.textContent = val;
          }

          // Dynamically recalculate availability for dependent options
          updateBadgesAvailability();

          const matched = resolveSelectedVariant();
          if (matched) {
            applyVariant(matched);
          }
        });
      });

      // Initial setup on page load: compute availability and apply initial variant
      updateBadgesAvailability();
      const initialMatched = resolveSelectedVariant();
      if (initialMatched) {
        applyVariant(initialMatched);
      }
    }
  } else if (flavorSelect) {
    // Fallback if badges wrapper is not present
    flavorSelect.addEventListener('change', () => {
      const selectedOption = flavorSelect.options[flavorSelect.selectedIndex];
      if (!selectedOption) return;
      const vName = selectedOption.value;
      const matched = productVariantsList.find(v => v.variant_name === vName);
      if (matched) {
        applyVariant(matched);
      }
    });
  } else {
    // For Simple Products (No Variants): check out-of-stock state
    const isOutOfStock = document.querySelector('.product-stock-status.out-of-stock') || document.querySelector('.product-purchase-row[data-in-stock="0"]');
    if (isOutOfStock) {
      if (btnAddToCart) {
        btnAddToCart.disabled = true;
        btnAddToCart.classList.add('is-disabled');
        btnAddToCart.textContent = 'Out of Stock';
        btnAddToCart.style.opacity = '0.6';
        btnAddToCart.style.cursor = 'not-allowed';
      }
      if (btnBuyNow) {
        btnBuyNow.disabled = true;
        btnBuyNow.classList.add('is-disabled');
        btnBuyNow.style.opacity = '0.6';
        btnBuyNow.style.cursor = 'not-allowed';
      }
      if (qtyInput) qtyInput.disabled = true;
      if (qtyMinus) {
        qtyMinus.disabled = true;
        qtyMinus.style.pointerEvents = 'none';
      }
      if (qtyPlus) {
        qtyPlus.disabled = true;
        qtyPlus.style.pointerEvents = 'none';
      }
    }
  }

  // 4. Buy It Now Action Feedback & Instant Checkout
  const btnBuyNow = document.getElementById('btnProductBuyNow');
  if (btnBuyNow) {
    btnBuyNow.addEventListener('click', (e) => {
      e.preventDefault();
      if (btnBuyNow.disabled || btnBuyNow.classList.contains('is-disabled')) {
        e.stopPropagation();
        return;
      }
      const originalText = btnBuyNow.textContent;
      btnBuyNow.textContent = 'PROCEEDING TO CHECKOUT...';
      btnBuyNow.style.opacity = '0.85';

      const addedQty = parseInt(qtyInput ? qtyInput.value : '1', 10) || 1;
      const titleEl = document.querySelector('.product-details-title');
      const priceEl = document.querySelector('.product-price-current');
      const flavorEl = document.querySelector('.product-flavor-select');
      const imgEl = document.getElementById('productMainImage');

      const title = titleEl ? titleEl.textContent.trim() : 'Vape Device';
      const priceStr = priceEl ? priceEl.textContent.replace(/[^0-9.]/g, '') : '17.99';
      const price = parseFloat(priceStr) || 17.99;
      const flavor = flavorEl && flavorEl.value ? flavorEl.value : '';
      const image = imgEl ? imgEl.getAttribute('src') : 'assets/images/vape-image.webp';
      const productId = btnBuyNow.dataset.productId ? parseInt(btnBuyNow.dataset.productId, 10) : 0;
      const variantId = btnBuyNow.dataset.variantId ? parseInt(btnBuyNow.dataset.variantId, 10) : (flavorEl && flavorEl.dataset.selectedVariantId ? parseInt(flavorEl.dataset.selectedVariantId, 10) : null);
      const id = variantId ? `${productId}-${variantId}` : `${productId}`;

      const checkoutUrl = (window.APP_BASE_URL || '').replace(/\/+$/, '') + '/checkout';

      const cartInstance = window.InfinityCart || window.NewVapeCart;
      if (cartInstance && typeof cartInstance.add === 'function') {
        cartInstance.add({
          product_id: productId,
          variant_id: variantId,
          id: id,
          title: title,
          price: price,
          quantity: addedQty,
          flavor: flavor,
          image: image
        }, function () {
          window.location.href = checkoutUrl;
        });
      } else {
        window.location.href = checkoutUrl;
      }
    });
  }

  // 5. Product Tabs Switching (Description, More Info, Reviews, FAQs, Related Posts)
  const tabButtons = document.querySelectorAll('.product-tab-btn');
  const tabPanes = document.querySelectorAll('.product-tab-pane');

  function switchTab(targetTabId) {
    if (!targetTabId) return;

    tabButtons.forEach((btn) => {
      const isTarget = btn.getAttribute('data-tab') === targetTabId;
      btn.classList.toggle('active', isTarget);
      btn.setAttribute('aria-selected', isTarget ? 'true' : 'false');
    });

    tabPanes.forEach((pane) => {
      pane.classList.toggle('active', pane.id === targetTabId);
    });
  }

  tabButtons.forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const targetId = btn.getAttribute('data-tab');
      switchTab(targetId);
    });
  });

  // Handle #reviews anchor link from product header meta
  const reviewLink = document.querySelector('.rating-count');
  if (reviewLink) {
    reviewLink.addEventListener('click', (e) => {
      e.preventDefault();
      switchTab('tab-reviews');
      const tabsSection = document.getElementById('productTabsSection');
      if (tabsSection) {
        tabsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  }

  // 6. Interactive Star Rating in "Write a Review"
  const starRatingWrap = document.getElementById('reviewStarRating');
  const selectedRatingInput = document.getElementById('selectedRating');

  if (starRatingWrap && selectedRatingInput) {
    const starBtns = starRatingWrap.querySelectorAll('.star-rating-btn');

    function updateStars(rating) {
      starBtns.forEach((star) => {
        const starVal = parseInt(star.getAttribute('data-rating'), 10);
        star.classList.toggle('active', starVal <= rating);
      });
    }

    starBtns.forEach((star) => {
      star.addEventListener('click', () => {
        const rating = parseInt(star.getAttribute('data-rating'), 10);
        selectedRatingInput.value = rating;
        updateStars(rating);
      });

      star.addEventListener('mouseenter', () => {
        const rating = parseInt(star.getAttribute('data-rating'), 10);
        starBtns.forEach((s) => {
          const sVal = parseInt(s.getAttribute('data-rating'), 10);
          s.classList.toggle('hover', sVal <= rating);
        });
      });
    });

    starRatingWrap.addEventListener('mouseleave', () => {
      starBtns.forEach((s) => s.classList.remove('hover'));
      updateStars(parseInt(selectedRatingInput.value, 10) || 5);
    });
  }

  // 7. Write a Review Form Submission (Live AJAX persistence)
  const writeReviewForm = document.getElementById('writeReviewForm');
  if (writeReviewForm) {
    writeReviewForm.addEventListener('submit', async (e) => {
      e.preventDefault();

      const btnSubmit = document.getElementById('btnSubmitReview');
      const alertBox = document.getElementById('reviewAlertBox');
      const prodIdEl = document.getElementById('reviewProductId');
      const productId = prodIdEl ? parseInt(prodIdEl.value, 10) : 0;
      const nickname = document.getElementById('reviewNickname').value.trim();
      const title = document.getElementById('reviewTitle').value.trim();
      const body = document.getElementById('reviewBody').value.trim();
      const rating = parseInt(selectedRatingInput ? selectedRatingInput.value : '5', 10) || 5;

      if (!productId || !nickname || !title || !body) {
        if (alertBox) {
          alertBox.style.display = 'block';
          alertBox.style.backgroundColor = '#fef2f2';
          alertBox.style.color = '#dc2626';
          alertBox.textContent = 'Please fill out all required fields before submitting.';
        }
        return;
      }

      if (btnSubmit) {
        btnSubmit.disabled = true;
        btnSubmit.textContent = 'Submitting Review...';
      }

      try {
        // Leverages <base href="..."> defined in <head> for universal root & subfolder compatibility
        const response = await fetch('api/reviews/submit', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            product_id: productId,
            nickname: nickname,
            title: title,
            comment: body,
            rating: rating
          })
        });

        const data = await response.json();

        if (data && data.success) {
          // If approved immediately (e.g. from admin), prepend to reviews list
          if (data.status === 'approved' && data.review) {
            const reviewsList = document.querySelector('.reviews-list');
            if (reviewsList) {
              const placeholder = reviewsList.querySelector('p');
              if (placeholder && placeholder.textContent.includes('No customer reviews yet')) {
                placeholder.remove();
              }

              const starStr = '★'.repeat(rating) + '☆'.repeat(5 - rating);
              const dateStr = data.review.created_at || 'Just now';

              const newReviewEl = document.createElement('div');
              newReviewEl.className = 'review-item';
              newReviewEl.style.animation = 'fadeInTab 0.35s ease';
              newReviewEl.innerHTML = `
                <div class="review-stars-row" aria-label="${rating} stars">${starStr}</div>
                <h4 class="review-item-title">${escapeHTML(title)}</h4>
                <p class="review-item-body">${escapeHTML(body)}</p>
                <div class="review-meta">${escapeHTML(nickname)} &bull; ${dateStr}</div>
              `;
              reviewsList.prepend(newReviewEl);

              const totalReviews = reviewsList.querySelectorAll('.review-item').length;
              const countText = document.querySelector('.reviews-count-text');
              if (countText) countText.textContent = `${totalReviews} Item(s)`;

              const metaCount = document.querySelector('.rating-count');
              if (metaCount) metaCount.textContent = `${totalReviews} Reviews`;
            }
          }

          if (alertBox) {
            alertBox.style.display = 'block';
            alertBox.style.backgroundColor = '#ecfdf5';
            alertBox.style.color = '#059669';
            alertBox.textContent = data.message || 'Thank you! Your review has been submitted and is pending moderation. It will be published once approved by our team.';
          }

          writeReviewForm.reset();
          if (selectedRatingInput) selectedRatingInput.value = '5';
          if (starRatingWrap) {
            starRatingWrap.querySelectorAll('.star-rating-btn').forEach((s) => s.classList.add('active'));
          }
        } else {
          if (alertBox) {
            alertBox.style.display = 'block';
            alertBox.style.backgroundColor = '#fef2f2';
            alertBox.style.color = '#dc2626';
            alertBox.textContent = data.message || 'Failed to submit review. Please try again.';
          }
        }
      } catch (err) {
        if (alertBox) {
          alertBox.style.display = 'block';
          alertBox.style.backgroundColor = '#fef2f2';
          alertBox.style.color = '#dc2626';
          alertBox.textContent = 'A network error occurred while submitting your review.';
        }
      } finally {
        if (btnSubmit) {
          btnSubmit.disabled = false;
          btnSubmit.textContent = 'Submit Review';
        }
      }
    });
  }

  function escapeHTML(str) {
    const p = document.createElement('p');
    p.appendChild(document.createTextNode(str));
    return p.innerHTML;
  }

  // 8. FAQ Accordion Toggle
  const faqHeaderBtns = document.querySelectorAll('.faq-header-btn');
  faqHeaderBtns.forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const card = btn.closest('.faq-accordion-card');
      if (!card) return;

      const isOpen = card.classList.contains('open');

      // Close other FAQs for clean single-accordion feel (optional: or leave open)
      document.querySelectorAll('.faq-accordion-card').forEach((otherCard) => {
        if (otherCard !== card) {
          otherCard.classList.remove('open');
          const otherBtn = otherCard.querySelector('.faq-header-btn');
          if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
        }
      });

      card.classList.toggle('open', !isOpen);
      btn.setAttribute('aria-expanded', !isOpen ? 'true' : 'false');
    });
  });

  // 9. Card Interactions for "You May Also Like" Grid
  // Handled cleanly by centralized cart-drawer.js delegation to prevent duplicate additions.

  // 10. Frequently Bought Together (FBT) Cross-Sell Bundle Interactions
  const fbtContainer = document.getElementById('fbtBundleContainer');
  if (fbtContainer) {
    const fbtCards = fbtContainer.querySelectorAll('.fbt-item-card');
    const fbtAddBtn = document.getElementById('btnFbtAddToCart');
    const fbtTotalEl = fbtContainer.querySelector('.fbt-btn-total-price');
    const fbtLabelEl = fbtContainer.querySelector('.btn-fbt-label');
    const fbtActionFooter = fbtContainer.querySelector('.fbt-action-footer');

    function calculateFbtTotal() {
      let total = 0;
      let count = 0;

      fbtCards.forEach((card) => {
        const checkbox = card.querySelector('.fbt-item-checkbox');
        const isOutOfStock = card.dataset.outOfStock === '1' || (checkbox && checkbox.disabled);
        if (checkbox && checkbox.checked && !isOutOfStock) {
          const price = parseFloat(card.dataset.currentPrice || card.dataset.basePrice) || 0;
          const qtyInput = card.querySelector('.fbt-qty-num');
          const qty = qtyInput ? (parseInt(qtyInput.value, 10) || 1) : 1;
          total += price * qty;
          count++;
          card.classList.add('is-checked');
        } else {
          if (isOutOfStock && checkbox) {
            checkbox.checked = false;
          }
          card.classList.remove('is-checked');
        }
      });

      if (fbtTotalEl) {
        fbtTotalEl.textContent = '$' + total.toFixed(2);
      }

      // Hide or Show "Add to cart" button based on selection
      if (fbtActionFooter) {
        if (count === 0) {
          fbtActionFooter.style.display = 'none';
          if (fbtAddBtn) fbtAddBtn.disabled = true;
        } else {
          fbtActionFooter.style.display = 'block';
          if (fbtAddBtn) {
            fbtAddBtn.disabled = false;
            if (fbtLabelEl) {
              fbtLabelEl.textContent = count === 1 ? 'Add 1 item to cart' : `Add ${count} items to cart`;
            }
            if (fbtTotalEl) fbtTotalEl.style.display = 'inline';
            const dot = fbtContainer.querySelector('.fbt-btn-dot');
            if (dot) dot.style.display = 'inline';
          }
        }
      }
    }

    fbtCards.forEach((card) => {
      // 1. Toggle Options Card
      const toggleBtn = card.querySelector('.fbt-toggle-options-btn');
      const popover = card.querySelector('.fbt-options-popover');
      if (toggleBtn && popover) {
        toggleBtn.addEventListener('click', (e) => {
          e.preventDefault();
          const isHidden = (popover.style.display === 'none' || !popover.style.display);
          popover.style.display = isHidden ? 'block' : 'none';
          toggleBtn.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
        });
      }

      // 2. Variants resolution from option dropdowns
      let variants = [];
      try {
        variants = JSON.parse(card.getAttribute('data-variants') || '[]');
      } catch (e) {
        variants = [];
      }

      function resolveFbtVariant() {
        if (!variants || variants.length === 0) return null;

        const selects = card.querySelectorAll('.fbt-dropdown-select');
        const chosenValues = Array.from(selects).map(s => s.value.trim());

        // Match exact or multi-dimension variant
        let matched = variants.find(v => {
          const parts = v.variant_name.split(' / ').map(s => s.trim());
          if (parts.length === chosenValues.length) {
            return parts.every((p, idx) => p.toLowerCase() === chosenValues[idx].toLowerCase());
          }
          if (chosenValues.length === 1) {
            return v.variant_name.trim().toLowerCase() === chosenValues[0].toLowerCase();
          }
          return false;
        });

        if (!matched && variants.length > 0) {
          matched = variants[0];
        }

        return matched;
      }

      function updateFbtCardOptions() {
        if (!variants || variants.length === 0) return;

        const customSelects = Array.from(card.querySelectorAll('.fbt-custom-select'));
        if (customSelects.length === 0) return;

        const maxDims = customSelects.length;

        if (maxDims === 1) {
          const cSelect = customSelects[0];
          const nativeSelect = cSelect.querySelector('.fbt-dropdown-select');
          const menu = cSelect.querySelector('.custom-variant-menu');
          if (!nativeSelect) return;

          Array.from(nativeSelect.options).forEach((opt) => {
            const val = opt.value.trim().toLowerCase();
            const v = variants.find(
              (item) => (item.variant_name || '').trim().toLowerCase() === val
            );
            const stock = v ? (parseInt(v.stock_quantity, 10) || 0) : 0;
            const inStock = stock > 0;
            opt.disabled = !inStock;

            if (menu) {
              const cItem = menu.querySelector(`.custom-variant-item[data-value="${CSS.escape(opt.value)}"]`);
              syncCustomItemState(cItem, inStock, opt.value === nativeSelect.value);
            }
          });

          // Ensure active selection is in-stock if possible
          if (nativeSelect.selectedOptions.length === 0 || nativeSelect.selectedOptions[0].disabled) {
            const firstAvail = Array.from(nativeSelect.options).find(o => !o.disabled);
            if (firstAvail) {
              nativeSelect.value = firstAvail.value;
            }
          }

          const activeVal = nativeSelect.value;
          const trigVal = cSelect.querySelector('.custom-variant-current-val');
          if (trigVal) trigVal.textContent = activeVal;
          if (menu) {
            menu.querySelectorAll('.custom-variant-item').forEach(it => {
              const isMatch = it.getAttribute('data-value') === activeVal && !it.classList.contains('is-disabled');
              it.classList.toggle('is-selected', isMatch);
              it.setAttribute('aria-selected', isMatch ? 'true' : 'false');
            });
          }
        } else {
          // Multi-Option Dimensions (e.g. Option 0 = Flavor, Option 1 = Strength)
          const cSelect0 = customSelects[0];
          const select0 = cSelect0.querySelector('.fbt-dropdown-select');
          const menu0 = cSelect0.querySelector('.custom-variant-menu');
          if (!select0) return;

          Array.from(select0.options).forEach((opt) => {
            const val0 = opt.value.trim().toLowerCase();
            const hasAnyInStock = variants.some((v) => {
              const parts = (v.variant_name || '').split(' / ').map((s) => s.trim().toLowerCase());
              const vStock = parseInt(v.stock_quantity, 10) || 0;
              return (parts[0] === val0) && (vStock > 0);
            });

            opt.disabled = !hasAnyInStock;
            if (menu0) {
              const cItem = menu0.querySelector(`.custom-variant-item[data-value="${CSS.escape(opt.value)}"]`);
              syncCustomItemState(cItem, hasAnyInStock, opt.value === select0.value);
            }
          });

          if (select0.selectedOptions.length === 0 || select0.selectedOptions[0].disabled) {
            const firstAvail0 = Array.from(select0.options).find(o => !o.disabled);
            if (firstAvail0) {
              select0.value = firstAvail0.value;
            }
          }

          const activeVal0 = select0.value;
          const trigVal0 = cSelect0.querySelector('.custom-variant-current-val');
          if (trigVal0) trigVal0.textContent = activeVal0;
          if (menu0) {
            menu0.querySelectorAll('.custom-variant-item').forEach(it => {
              const isMatch = it.getAttribute('data-value') === activeVal0 && !it.classList.contains('is-disabled');
              it.classList.toggle('is-selected', isMatch);
              it.setAttribute('aria-selected', isMatch ? 'true' : 'false');
            });
          }

          const activeValues = [activeVal0 ? activeVal0.trim() : ''];

          for (let g = 1; g < maxDims; g++) {
            const cSelectG = customSelects[g];
            const selectG = cSelectG.querySelector('.fbt-dropdown-select');
            const menuG = cSelectG.querySelector('.custom-variant-menu');
            if (!selectG) continue;

            Array.from(selectG.options).forEach((opt) => {
              const testVal = opt.value.trim();
              const testCombo = activeValues.slice(0, g);
              testCombo[g] = testVal;

              const matchingVars = variants.filter((v) => {
                const parts = (v.variant_name || '').split(' / ').map((s) => s.trim().toLowerCase());
                return testCombo.every((val, i) => (parts[i] || '') === val.toLowerCase());
              });

              const inStock = matchingVars.some((v) => (parseInt(v.stock_quantity, 10) || 0) > 0);

              opt.disabled = !inStock;
              if (menuG) {
                const cItem = menuG.querySelector(`.custom-variant-item[data-value="${CSS.escape(opt.value)}"]`);
                syncCustomItemState(cItem, inStock, opt.value === selectG.value);
              }
            });

            // If current selection is out-of-stock, auto switch to first in-stock option
            if (selectG.selectedOptions.length === 0 || selectG.selectedOptions[0].disabled) {
              const firstAvailG = Array.from(selectG.options).find(o => !o.disabled);
              if (firstAvailG) {
                selectG.value = firstAvailG.value;
              }
            }

            const activeValG = selectG.value;
            const trigValG = cSelectG.querySelector('.custom-variant-current-val');
            if (trigValG) trigValG.textContent = activeValG;
            if (menuG) {
              menuG.querySelectorAll('.custom-variant-item').forEach(it => {
                const isMatch = it.getAttribute('data-value') === activeValG && !it.classList.contains('is-disabled');
                it.classList.toggle('is-selected', isMatch);
                it.setAttribute('aria-selected', isMatch ? 'true' : 'false');
              });
            }

            activeValues[g] = activeValG ? activeValG.trim() : '';
          }
        }
      }

      function syncFbtCardVariant() {
        const hasVariants = variants && variants.length > 0;
        const matchedVariant = resolveFbtVariant();
        let isCurrentOutOfStock = false;
        let price = parseFloat(card.dataset.basePrice) || 0;

        if (hasVariants) {
          if (matchedVariant) {
            price = parseFloat(matchedVariant.price || card.dataset.basePrice) || price;
            card.dataset.currentPrice = price;
            card.dataset.selectedVariantId = matchedVariant.id || '';
            card.dataset.selectedVariantName = matchedVariant.variant_name || '';

            const varStock = (typeof matchedVariant.stock_quantity !== 'undefined') 
              ? parseInt(matchedVariant.stock_quantity, 10) 
              : null;
            isCurrentOutOfStock = (varStock !== null && !isNaN(varStock) && varStock <= 0);

            if (matchedVariant.image_url) {
              const img = card.querySelector('.fbt-item-img');
              if (img) {
                const imgUrl = matchedVariant.image_url.startsWith('http') || matchedVariant.image_url.startsWith('data:')
                  ? matchedVariant.image_url
                  : (window.APP_BASE_URL || '').replace(/\/+$/, '') + '/' + matchedVariant.image_url.replace(/^\/+/, '');
                img.src = imgUrl;
              }
            }
          } else {
            isCurrentOutOfStock = true;
          }
        } else {
          isCurrentOutOfStock = (card.dataset.parentOutOfStock === '1' || card.dataset.outOfStock === '1');
        }

        card.dataset.currentPrice = price;
        const priceDisplay = card.querySelector('.fbt-price-display');
        if (priceDisplay) {
          priceDisplay.textContent = '$' + price.toFixed(2);
        }

        const checkbox = card.querySelector('.fbt-item-checkbox');
        const priceRow = card.querySelector('.fbt-item-price-row');
        let outBadge = priceRow ? priceRow.querySelector('.fbt-badge-out') : null;

        if (isCurrentOutOfStock) {
          card.dataset.outOfStock = '1';
          card.classList.add('is-out-of-stock');
          if (checkbox) {
            checkbox.checked = false;
            checkbox.disabled = true;
          }
          if (!outBadge && priceRow) {
            const badge = document.createElement('span');
            badge.className = 'fbt-badge-out';
            badge.textContent = 'Out of stock';
            priceRow.appendChild(badge);
          }
        } else {
          // Restore in-stock state
          card.dataset.outOfStock = '0';
          card.classList.remove('is-out-of-stock');
          if (checkbox) {
            checkbox.disabled = false;
          }
          if (outBadge) {
            outBadge.remove();
          }
        }

        calculateFbtTotal();
      }

      // Handle Custom Dropdown Selects inside this FBT card
      const customSelects = card.querySelectorAll('.fbt-custom-select');
      customSelects.forEach((cSelect) => {
        const trigger = cSelect.querySelector('.custom-variant-trigger');
        const menu = cSelect.querySelector('.custom-variant-menu');
        const nativeSelect = cSelect.querySelector('.fbt-dropdown-select');

        if (trigger) {
          trigger.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const isOpen = cSelect.classList.contains('is-open');
            document.querySelectorAll('.custom-variant-select.is-open').forEach((s) => {
              if (s !== cSelect) {
                s.classList.remove('is-open');
                s.setAttribute('aria-expanded', 'false');
              }
            });
            document.querySelectorAll('.fbt-item-card.has-open-select').forEach((c) => {
              if (c !== card) c.classList.remove('has-open-select');
            });
            cSelect.classList.toggle('is-open', !isOpen);
            cSelect.setAttribute('aria-expanded', !isOpen ? 'true' : 'false');
            card.classList.toggle('has-open-select', !isOpen);
          });
        }

        if (menu) {
          menu.addEventListener('click', (e) => {
            const item = e.target.closest('.custom-variant-item');
            if (!item) return;
            e.preventDefault();
            e.stopPropagation();

            const val = item.getAttribute('data-value');
            const currentValSpan = cSelect.querySelector('.custom-variant-current-val');
            if (currentValSpan) currentValSpan.textContent = val;

            menu.querySelectorAll('.custom-variant-item').forEach(it => {
              it.classList.remove('is-selected');
              it.setAttribute('aria-selected', 'false');
            });
            item.classList.add('is-selected');
            item.setAttribute('aria-selected', 'true');

            cSelect.classList.remove('is-open');
            cSelect.setAttribute('aria-expanded', 'false');
            card.classList.remove('has-open-select');

            if (nativeSelect) {
              nativeSelect.value = val;
            }

            // Recalculate options availability and auto-switch dependent options if needed
            updateFbtCardOptions();

            // Sync active variant, stock status, price and total
            syncFbtCardVariant();
          });
        }
      });

      // Initialize dependent dropdown availability & in-stock option selection on page load
      updateFbtCardOptions();
      syncFbtCardVariant();

      // 3. Quantity controls
      const minusBtn = card.querySelector('.btn-minus');
      const plusBtn = card.querySelector('.btn-plus');
      const qtyInput = card.querySelector('.fbt-qty-num');

      if (minusBtn && plusBtn && qtyInput) {
        minusBtn.addEventListener('click', (e) => {
          e.preventDefault();
          let current = parseInt(qtyInput.value, 10) || 1;
          if (current > 1) {
            qtyInput.value = current - 1;
            calculateFbtTotal();
          }
        });

        plusBtn.addEventListener('click', (e) => {
          e.preventDefault();
          let current = parseInt(qtyInput.value, 10) || 1;
          if (current < 99) {
            qtyInput.value = current + 1;
            calculateFbtTotal();
          }
        });
      }

      // 4. Checkbox change
      const checkbox = card.querySelector('.fbt-item-checkbox');
      if (checkbox) {
        checkbox.addEventListener('change', () => {
          calculateFbtTotal();
        });
      }
    });

    // 5. Add to Cart Button Click
    if (fbtAddBtn) {
      fbtAddBtn.addEventListener('click', (e) => {
        e.preventDefault();
        if (fbtAddBtn.disabled) return;

        const itemsToAdd = [];

        fbtCards.forEach((card) => {
          const checkbox = card.querySelector('.fbt-item-checkbox');
          const isOutOfStock = card.dataset.outOfStock === '1' || (checkbox && checkbox.disabled);
          if (checkbox && checkbox.checked && !isOutOfStock) {
            const title = card.getAttribute('data-product-title') || 'Bundle Product';
            const price = parseFloat(card.dataset.currentPrice || card.dataset.basePrice) || 0;
            const qtyInput = card.querySelector('.fbt-qty-num');
            const qty = qtyInput ? (parseInt(qtyInput.value, 10) || 1) : 1;
            const flavor = card.dataset.selectedVariantName || '';
            const imgEl = card.querySelector('.fbt-item-img');
            const img = imgEl ? imgEl.src : (card.getAttribute('data-product-img') || '');
            const pId = parseInt(card.dataset.productId, 10) || 0;
            const vId = card.dataset.selectedVariantId ? parseInt(card.dataset.selectedVariantId, 10) : null;
            const id = vId ? `${pId}-${vId}` : `${pId}`;

            itemsToAdd.push({
              product_id: pId,
              variant_id: vId,
              id: id,
              title: title,
              price: price,
              quantity: qty,
              flavor: flavor,
              image: img
            });
          }
        });

        if (itemsToAdd.length === 0) return;

        const originalText = fbtAddBtn.innerHTML;
        fbtAddBtn.innerHTML = `<span>ADDING ${itemsToAdd.length} ITEM(S)...</span>`;
        fbtAddBtn.style.opacity = '0.85';

        const cartInstance = window.InfinityCart || window.NewVapeCart;
        if (cartInstance) {
          if (typeof cartInstance.addMultiple === 'function') {
            cartInstance.addMultiple(itemsToAdd);
          } else {
            itemsToAdd.forEach(it => cartInstance.add(it));
          }
        }

        setTimeout(() => {
          fbtAddBtn.innerHTML = `<span>ADDED TO CART! ✓</span>`;
          fbtAddBtn.style.backgroundColor = '#10b981';
          setTimeout(() => {
            fbtAddBtn.innerHTML = originalText;
            fbtAddBtn.style.backgroundColor = '';
            fbtAddBtn.style.opacity = '';
            calculateFbtTotal();
          }, 1500);
        }, 300);
      });
    }

    // Initial total calculation on page load (starts with 0 checked, button hidden)
    calculateFbtTotal();
  }
});


