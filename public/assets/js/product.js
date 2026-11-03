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

    // Horizontal Scroll with Left / Right Buttons (Always Visible)
    if (thumbTrack && thumbPrevBtn && thumbNextBtn) {
      thumbPrevBtn.addEventListener('click', (e) => {
        e.preventDefault();
        thumbTrack.scrollBy({ left: -220, behavior: 'smooth' });
      });

      thumbNextBtn.addEventListener('click', (e) => {
        e.preventDefault();
        thumbTrack.scrollBy({ left: 220, behavior: 'smooth' });
      });

      // Mouse Wheel horizontal scroll over thumbnail track
      thumbTrack.addEventListener('wheel', (e) => {
        if (e.deltaY !== 0) {
          e.preventDefault();
          thumbTrack.scrollLeft += e.deltaY;
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
    btnAddToCart.addEventListener('click', () => {
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

  // 3.1 Dynamic Flavor / Variant Selection Live Switcher
  const flavorSelect = document.getElementById('flavorSelect');
  if (flavorSelect) {
    flavorSelect.addEventListener('change', () => {
      const selectedOption = flavorSelect.options[flavorSelect.selectedIndex];
      if (!selectedOption) return;

      const vPrice = selectedOption.getAttribute('data-price');
      const vCompare = selectedOption.getAttribute('data-compare-price');
      const vSku = selectedOption.getAttribute('data-sku');
      const vStock = parseInt(selectedOption.getAttribute('data-stock'), 10);
      const vImage = selectedOption.getAttribute('data-image');

      // Update Current Price
      const priceCurrentEl = document.querySelector('.product-price-current');
      if (priceCurrentEl && vPrice) {
        priceCurrentEl.textContent = `$${parseFloat(vPrice).toFixed(2)}`;
      }

      // Update Compare Price & Discount Pill
      let priceOldEl = document.querySelector('.product-price-old');
      let discountPill = document.querySelector('.product-discount-pill');
      const priceSection = document.querySelector('.product-price-section');

      if (vCompare && parseFloat(vCompare) > parseFloat(vPrice || 0)) {
        const compVal = parseFloat(vCompare);
        const curVal = parseFloat(vPrice);
        const pct = Math.round(((compVal - curVal) / compVal) * 100);

        if (!priceOldEl && priceSection) {
          priceOldEl = document.createElement('span');
          priceOldEl.className = 'product-price-old';
          priceSection.insertBefore(priceOldEl, priceCurrentEl);
        }
        if (priceOldEl) {
          priceOldEl.textContent = `$${compVal.toFixed(2)}`;
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

      // Update SKU
      const skuEl = document.querySelector('.product-sku');
      if (skuEl && vSku) {
        skuEl.textContent = `SKU: ${vSku}`;
      }

      // Update Stock Status
      const stockStatusWrap = document.querySelector('.product-stock-status');
      const stockTextEl = document.querySelector('.product-stock-status .stock-text');
      if (stockStatusWrap && stockTextEl && !isNaN(vStock)) {
        if (vStock <= 0) {
          stockStatusWrap.className = 'product-stock-status out-of-stock';
          stockTextEl.innerHTML = '<strong>Out of Stock.</strong> Backorder available.';
        } else if (vStock < 15) {
          stockStatusWrap.className = 'product-stock-status low-stock';
          stockTextEl.innerHTML = `<strong>Low Stock (${vStock} left).</strong> Order soon.`;
        } else {
          stockStatusWrap.className = 'product-stock-status in-stock';
          stockTextEl.innerHTML = '<strong>In Stock.</strong> Ships today if ordered by 3:00 PM EST';
        }
      }

      // Switch Main Image if Variant has a custom photo
      if (vImage && mainImage) {
        mainImage.style.opacity = '0.3';
        setTimeout(() => {
          mainImage.src = vImage;
          mainImage.style.opacity = '1';
        }, 150);

        if (thumbButtons && thumbButtons.length > 0) {
          thumbButtons.forEach((b) => {
            const bSrc = b.getAttribute('data-img') || '';
            if (bSrc === vImage || bSrc.endsWith(vImage) || vImage.endsWith(bSrc)) {
              thumbButtons.forEach(btn => btn.classList.remove('active'));
              b.classList.add('active');
              b.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            }
          });
        }
      }
    });
  }

  // 4. Buy It Now Action Feedback & Instant Checkout
  const btnBuyNow = document.getElementById('btnProductBuyNow');
  if (btnBuyNow) {
    btnBuyNow.addEventListener('click', (e) => {
      e.preventDefault();
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
      const id = (title + (flavor ? '-' + flavor : '')).toLowerCase().replace(/[^a-z0-9]+/g, '-');

      const cartInstance = window.InfinityCart || window.NewVapeCart;
      if (cartInstance) {
        cartInstance.add({
          id: id,
          title: title,
          price: price,
          quantity: addedQty,
          flavor: flavor,
          image: image
        });
      }

      setTimeout(() => {
        // Leverages <base href="..."> defined in <head> for universal root & subfolder compatibility
        window.location.href = 'checkout';
      }, 350);
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
});


