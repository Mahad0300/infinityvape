/**
 * INFINITY VAPE SHOP - DYNAMIC PRODUCTION CART DRAWER
 * Fully connected to PHP MVC Backend with Real Database Validation,
 * Live Stock Verification, and Session Synchronization.
 */

(function () {
  'use strict';

  const STORAGE_KEY = 'infinity_cart_items';

  // DOM Elements Cache
  let backdrop, drawer, closeBtn, countBadge, bodyEl, emptyState, itemsList, subtotalVal;
  let isRequestPending = false;

  /**
   * Resolve universal API base URL (handles localhost/subfolder & root domains)
   */
  function getApiBaseUrl() {
    if (window.APP_BASE_URL && typeof window.APP_BASE_URL === 'string') {
      return window.APP_BASE_URL.replace(/\/+$/, '');
    }
    const baseEl = document.querySelector('base');
    if (baseEl && baseEl.href) {
      return baseEl.href.replace(/\/+$/, '');
    }
    return window.location.origin;
  }

  /**
   * Safe Cart Storage Cache
   */
  function getCart() {
    try {
      const data = localStorage.getItem(STORAGE_KEY) || localStorage.getItem('newvape_cart_items');
      return data ? JSON.parse(data) : [];
    } catch (e) {
      return [];
    }
  }

  function saveCart(cart) {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
    } catch (e) {
      console.warn('LocalStorage write failed:', e);
    }
    updateCartUI(cart);
  }

  /**
   * Floating Toast Notification
   */
  function showCartNotice(message, type = 'info') {
    if (!message) return;
    let toast = document.getElementById('cartToastNotice');
    if (!toast) {
      toast = document.createElement('div');
      toast.id = 'cartToastNotice';
      toast.className = 'cart-toast-notice';
      document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.className = `cart-toast-notice show ${type}`;
    clearTimeout(toast.__timer);
    toast.__timer = setTimeout(() => {
      toast.classList.remove('show');
    }, 3800);
  }

  /**
   * Open / Close Drawer
   */
  function openDrawer() {
    if (!drawer || !backdrop) return;
    drawer.classList.add('active');
    backdrop.classList.add('active');
    drawer.setAttribute('aria-hidden', 'false');
    backdrop.setAttribute('aria-hidden', 'false');
    document.body.classList.add('cart-drawer-open');
  }

  function closeDrawer() {
    if (!drawer || !backdrop) return;
    drawer.classList.remove('active');
    backdrop.classList.remove('active');
    drawer.setAttribute('aria-hidden', 'true');
    backdrop.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('cart-drawer-open');
  }

  /**
   * Synchronize Cart with Backend Database & Session
   */
  function syncWithServer() {
    fetch(`${getApiBaseUrl()}/cart/items`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
      .then((res) => res.json())
      .then((data) => {
        if (data && data.success && Array.isArray(data.cart)) {
          saveCart(data.cart);
        }
      })
      .catch((err) => {
        console.warn('Cart server sync failed, falling back to local cache', err);
        updateCartUI();
      });
  }

  /**
   * Add Item to Cart (Backend API call with DB stock validation)
   */
  function addItem(product, callback) {
    if (isRequestPending) return;
    isRequestPending = true;

    // Resolve IDs
    let productId = 0;
    let variantId = null;

    if (product.product_id) {
      productId = parseInt(product.product_id, 10);
    } else if (product.productId) {
      productId = parseInt(product.productId, 10);
    } else if (product.id && typeof product.id === 'string' && product.id.includes('-')) {
      const parts = product.id.split('-');
      productId = parseInt(parts[0], 10) || 0;
      variantId = parseInt(parts[1], 10) || null;
    } else if (product.id) {
      productId = parseInt(product.id, 10) || 0;
    }

    if (product.variant_id) {
      variantId = parseInt(product.variant_id, 10);
    } else if (product.variantId) {
      variantId = parseInt(product.variantId, 10);
    }

    const quantity = Math.max(1, parseInt(product.quantity || product.qty || 1, 10));

    fetch(`${getApiBaseUrl()}/cart/add`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        product_id: productId,
        variant_id: variantId,
        quantity: quantity
      })
    })
      .then((res) => res.json())
      .then((data) => {
        isRequestPending = false;
        if (data.success && Array.isArray(data.cart)) {
          saveCart(data.cart);
          openDrawer();
          if (typeof callback === 'function') callback(data);
        } else {
          showCartNotice(data.message || 'Unable to add item to cart.', 'error');
          if (typeof callback === 'function') callback(data);
        }
      })
      .catch((err) => {
        isRequestPending = false;
        console.error('Cart add error:', err);
        showCartNotice('Network error. Please try again.', 'error');
        if (typeof callback === 'function') callback({ success: false });
      });
  }

  /**
   * Add Multiple Items (e.g. FBT bundle)
   */
  function addMultiple(items, callback) {
    if (!Array.isArray(items) || items.length === 0) return;
    if (isRequestPending) return;
    isRequestPending = true;

    const formattedItems = items.map((item) => {
      let pId = item.product_id || item.productId;
      let vId = item.variant_id || item.variantId;
      if (!pId && item.id) {
        if (typeof item.id === 'string' && item.id.includes('-')) {
          const parts = item.id.split('-');
          pId = parseInt(parts[0], 10);
          vId = parseInt(parts[1], 10);
        } else {
          pId = parseInt(item.id, 10);
        }
      }
      return {
        product_id: parseInt(pId, 10) || 0,
        variant_id: vId ? parseInt(vId, 10) : null,
        quantity: Math.max(1, parseInt(item.quantity || 1, 10))
      };
    });

    fetch(`${getApiBaseUrl()}/cart/add-multiple`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ items: formattedItems })
    })
      .then((res) => res.json())
      .then((data) => {
        isRequestPending = false;
        if (data.success && Array.isArray(data.cart)) {
          saveCart(data.cart);
          openDrawer();
          if (typeof callback === 'function') callback(data);
        } else {
          if (data.cart) saveCart(data.cart);
          showCartNotice(data.message || 'Could not add some bundle items.', 'warning');
          if (typeof callback === 'function') callback(data);
        }
      })
      .catch((err) => {
        isRequestPending = false;
        console.error('Bundle add error:', err);
        showCartNotice('Network error. Please try again.', 'error');
        if (typeof callback === 'function') callback({ success: false });
      });
  }

  /**
   * Update Item Quantity
   */
  function updateQty(id, delta) {
    const cart = getCart();
    const item = cart.find((i) => String(i.id) === String(id));
    if (!item) return;

    const newQty = item.quantity + delta;

    fetch(`${getApiBaseUrl()}/cart/update`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        id: id,
        quantity: newQty
      })
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.cart) {
          saveCart(data.cart);
        }
        if (!data.success && data.message) {
          showCartNotice(data.message, 'warning');
        }
      })
      .catch((err) => {
        console.error('Update qty error:', err);
        showCartNotice('Could not update quantity. Please try again.', 'error');
      });
  }

  /**
   * Remove Item from Cart
   */
  function removeItem(id) {
    fetch(`${getApiBaseUrl()}/cart/remove`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ id: id })
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.cart) {
          saveCart(data.cart);
        }
      })
      .catch((err) => {
        console.error('Remove item error:', err);
      });
  }

  /**
   * Clear Cart
   */
  function clearCart() {
    fetch(`${getApiBaseUrl()}/cart/clear`, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
      .then((res) => res.json())
      .then((data) => {
        saveCart([]);
      })
      .catch((err) => {
        console.error('Clear cart error:', err);
      });
  }

  /**
   * Update Full Cart UI (Header Badges, Drawer Items, Subtotal)
   */
  function updateCartUI(cartParam) {
    const cart = Array.isArray(cartParam) ? cartParam : getCart();

    // 1. Calculate Totals
    let totalCount = 0;
    let subtotal = 0;

    cart.forEach((item) => {
      const q = parseInt(item.quantity, 10) || 1;
      const p = parseFloat(item.price) || 0;
      totalCount += q;
      subtotal += p * q;
    });

    // 2. Header Badges Update
    const headerBadges = document.querySelectorAll('.header-cart-badge');
    headerBadges.forEach((badge) => {
      badge.setAttribute('data-count', totalCount);
      if (totalCount > 0) {
        badge.textContent = totalCount;
        badge.style.display = 'inline-flex';
        badge.classList.remove('is-empty');
        badge.classList.remove('cart-badge-bounce');
        void badge.offsetWidth; // trigger reflow
        badge.classList.add('cart-badge-bounce');
      } else {
        badge.textContent = '';
        badge.style.display = 'none';
        badge.classList.add('is-empty');
        badge.classList.remove('cart-badge-bounce');
      }
    });

    // 3. Drawer Header Count Badge
    if (countBadge) {
      countBadge.textContent = totalCount;
    }

    // 4. Subtotal Display
    if (subtotalVal) {
      subtotalVal.textContent = `$${subtotal.toFixed(2)}`;
    }

    // 5. Empty State vs Items List Display
    if (emptyState && itemsList) {
      const footerEl = document.getElementById('cartDrawerFooter');

      if (cart.length === 0) {
        emptyState.style.display = 'flex';
        itemsList.style.display = 'none';
        itemsList.innerHTML = '';
        if (footerEl) footerEl.classList.add('cart-footer-dimmed');
      } else {
        emptyState.style.display = 'none';
        itemsList.style.display = 'flex';
        if (footerEl) footerEl.classList.remove('cart-footer-dimmed');

        // Render Items
        itemsList.innerHTML = cart
          .map((item) => {
            const price = parseFloat(item.price) || 0;
            const qty = parseInt(item.quantity, 10) || 1;
            const maxStock = parseInt(item.max_stock, 10) || 999;
            const isMaxReached = qty >= maxStock;
            const itemTotal = (price * qty).toFixed(2);
            const variantDisplay = item.variant_name || item.flavor || '';
            const productUrl = item.product_slug ? `${getApiBaseUrl()}/product/${encodeURIComponent(item.product_slug)}` : 'javascript:void(0);';

            return `
            <div class="cart-drawer-item" data-id="${item.id}">
              <a href="${productUrl}" class="cart-item-thumb" title="${escapeHtml(item.title)}">
                <img src="${item.image}" alt="${escapeHtml(item.title)}" onerror="this.onerror=null;this.src='${getApiBaseUrl()}/assets/images/vape-image.webp';">
              </a>
              <div class="cart-item-details">
                <div class="cart-item-top">
                  <h4 class="cart-item-title">
                    <a href="${productUrl}">${escapeHtml(item.title)}</a>
                  </h4>
                  <button type="button" class="cart-item-remove-btn" data-action="remove" data-id="${item.id}" aria-label="Remove item" title="Remove">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="3 6 5 6 21 6"></polyline>
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                  </button>
                </div>
                ${variantDisplay ? `<div class="cart-item-variant"><span>${escapeHtml(variantDisplay)}</span></div>` : ''}
                <div class="cart-item-bottom">
                  <div class="cart-item-qty-control">
                    <button type="button" class="cart-qty-btn" data-action="decrease" data-id="${item.id}" aria-label="Decrease quantity">−</button>
                    <span class="cart-qty-value">${qty}</span>
                    <button type="button" class="cart-qty-btn ${isMaxReached ? 'is-disabled' : ''}" data-action="increase" data-id="${item.id}" ${isMaxReached ? 'title="Maximum available stock reached"' : ''} aria-label="Increase quantity">+</button>
                  </div>
                  <div class="cart-item-price-wrap">
                    <span class="cart-item-price">$${itemTotal}</span>
                  </div>
                </div>
              </div>
            </div>
          `;
          })
          .join('');
      }
    }
  }

  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  /**
   * Initialize Global Event Listeners
   */
  function init() {
    backdrop = document.getElementById('cartDrawerBackdrop');
    drawer = document.getElementById('cartDrawer');
    closeBtn = document.getElementById('cartDrawerCloseBtn');
    countBadge = document.getElementById('cartDrawerCountBadge');
    bodyEl = document.getElementById('cartDrawerBody');
    emptyState = document.getElementById('cartEmptyState');
    itemsList = document.getElementById('cartItemsList');
    subtotalVal = document.getElementById('cartSubtotalVal');

    // Close Button & Backdrop
    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
    if (backdrop) backdrop.addEventListener('click', closeDrawer);

    // Continue Shopping Buttons
    const emptyContinueBtn = document.getElementById('cartEmptyContinueBtn');
    if (emptyContinueBtn) emptyContinueBtn.addEventListener('click', closeDrawer);

    const continueLink = document.getElementById('cartContinueShoppingLink');
    if (continueLink) continueLink.addEventListener('click', closeDrawer);

    // Escape Key to Close
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && drawer && drawer.classList.contains('active')) {
        closeDrawer();
      }
    });

    // Checkout Button Redirect
    const checkoutBtn = document.getElementById('cartCheckoutBtn');
    if (checkoutBtn) {
      checkoutBtn.addEventListener('click', () => {
        const cart = getCart();
        if (cart.length === 0) {
          showCartNotice('Your cart is empty. Please add products to proceed!', 'warning');
          return;
        }
        window.location.href = `${getApiBaseUrl()}/checkout`;
      });
    }

    // Header Cart Icon Link Click -> Open Cart Drawer
    document.addEventListener('click', (e) => {
      const cartLink = e.target.closest('a.header-action-btn[href*="cart.php"], a.header-action-btn[href*="cart"], #headerCartBtn, .header-cart-badge');
      if (cartLink) {
        e.preventDefault();
        openDrawer();
      }
    });

    // Delegated clicks inside Cart Drawer Items List (+, -, Remove)
    if (itemsList) {
      itemsList.addEventListener('click', (e) => {
        const btn = e.target.closest('button[data-action]');
        if (!btn) return;

        const action = btn.getAttribute('data-action');
        const id = btn.getAttribute('data-id');

        if (action === 'increase') {
          updateQty(id, 1);
        } else if (action === 'decrease') {
          updateQty(id, -1);
        } else if (action === 'remove') {
          removeItem(id);
        }
      });
    }

    // Intercept All "ADD TO CART" Buttons Globally
    document.addEventListener('click', (e) => {
      // 1. Single Product Detail Page Main Add to Cart
      const singleAddBtn = e.target.closest('#btnProductAddToCart');
      if (singleAddBtn) {
        e.preventDefault();
        if (singleAddBtn.disabled || singleAddBtn.classList.contains('is-disabled')) {
          return;
        }
        const qtyEl = document.getElementById('productQuantity');
        const flavorEl = document.querySelector('.product-flavor-select');

        const productId = singleAddBtn.dataset.productId ? parseInt(singleAddBtn.dataset.productId, 10) : 0;
        let variantId = singleAddBtn.dataset.variantId ? parseInt(singleAddBtn.dataset.variantId, 10) : null;
        if (!variantId && flavorEl && flavorEl.dataset.selectedVariantId) {
          variantId = parseInt(flavorEl.dataset.selectedVariantId, 10);
        }
        if (!variantId && flavorEl && flavorEl.selectedOptions && flavorEl.selectedOptions[0]) {
          const opt = flavorEl.selectedOptions[0];
          if (opt.dataset.variantId) {
            variantId = parseInt(opt.dataset.variantId, 10);
          }
        }
        const quantity = qtyEl ? parseInt(qtyEl.value, 10) || 1 : 1;

        const originalText = singleAddBtn.textContent;
        singleAddBtn.textContent = 'ADDING...';
        singleAddBtn.disabled = true;

        addItem(
          {
            product_id: productId,
            variant_id: variantId,
            quantity: quantity
          },
          function (res) {
            singleAddBtn.disabled = false;
            if (res && res.success) {
              singleAddBtn.textContent = 'ADDED! ✓';
              singleAddBtn.style.backgroundColor = '#10b981';
              singleAddBtn.style.color = '#ffffff';
              setTimeout(() => {
                singleAddBtn.textContent = originalText;
                singleAddBtn.style.backgroundColor = '';
                singleAddBtn.style.color = '';
              }, 1800);
            } else {
              singleAddBtn.textContent = originalText;
            }
          }
        );
        return;
      }

      // 2. Product Card Add to Cart (Home, Category, You May Also Like)
      const cardAddBtn = e.target.closest('.btn-add-to-cart, .btn-card-add-cart');
      if (cardAddBtn) {
        e.preventDefault();
        e.stopPropagation();
        if (cardAddBtn.disabled || cardAddBtn.classList.contains('is-disabled') || cardAddBtn.classList.contains('out-of-stock')) {
          return;
        }

        const pId = parseInt(cardAddBtn.getAttribute('data-id'), 10) || 0;
        if (!pId) return;

        const originalText = cardAddBtn.textContent;
        cardAddBtn.textContent = 'ADDING...';
        cardAddBtn.disabled = true;

        addItem(
          {
            product_id: pId,
            variant_id: null,
            quantity: 1
          },
          function (res) {
            cardAddBtn.disabled = false;
            if (res && res.success) {
              cardAddBtn.textContent = 'ADDED! ✓';
              cardAddBtn.style.backgroundColor = '#10b981';
              setTimeout(() => {
                cardAddBtn.textContent = originalText;
                cardAddBtn.style.backgroundColor = '';
              }, 1400);
            } else {
              cardAddBtn.textContent = originalText;
            }
          }
        );
      }
    });

    // Populate initial UI on page load & synchronize with live database session
    updateCartUI();
    syncWithServer();
  }

  // Run on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // Expose global controller for programmatic access across other scripts (e.g. FBT bundle)
  window.InfinityCart = window.NewVapeCart = {
    open: openDrawer,
    close: closeDrawer,
    get: getCart,
    add: addItem,
    addMultiple: addMultiple,
    update: updateQty,
    remove: removeItem,
    clear: clearCart,
    sync: syncWithServer
  };
})();
