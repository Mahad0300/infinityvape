/**
 * INFINITY VAPE SHOP - CART DRAWER JAVASCRIPT
 * Full-featured slide-out mini-cart drawer with localStorage persistence,
 * dynamic free shipping progress meter, live quantity counters, and smooth animations.
 */

(function () {
  'use strict';

  const STORAGE_KEY = 'infinity_cart_items';

  // DOM Elements Cache
  let backdrop, drawer, closeBtn, countBadge, bodyEl, emptyState, itemsList, subtotalVal;

  /**
   * Safe Cart Storage Helpers
   */
  function getCart() {
    try {
      const data = localStorage.getItem(STORAGE_KEY) || localStorage.getItem('newvape_cart_items');
      return data ? JSON.parse(data) : [];
    } catch (e) {
      console.error('Error reading cart from localStorage', e);
      return [];
    }
  }

  function saveCart(cart) {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
    } catch (e) {
      console.error('Error saving cart to localStorage', e);
    }
    updateCartUI();
  }

  let isAdding = false;

  /**
   * Add Item to Cart
   */
  function addItem(product) {
    if (isAdding) return;
    isAdding = true;
    setTimeout(() => { isAdding = false; }, 250);

    const cart = getCart();
    const existingIndex = cart.findIndex((item) => item.id === product.id);

    if (existingIndex > -1) {
      cart[existingIndex].quantity += product.quantity;
    } else {
      cart.push(product);
    }

    saveCart(cart);
    openDrawer();
  }

  /**
   * Add Multiple Items to Cart in a Single Operation
   */
  function addMultiple(items) {
    if (!Array.isArray(items) || items.length === 0) return;
    const cart = getCart();
    items.forEach(product => {
      const existingIndex = cart.findIndex((item) => item.id === product.id);
      if (existingIndex > -1) {
        cart[existingIndex].quantity += (product.quantity || 1);
      } else {
        cart.push(product);
      }
    });
    saveCart(cart);
    openDrawer();
  }

  /**
   * Update Item Quantity
   */
  function updateQty(id, delta) {
    let cart = getCart();
    const item = cart.find((i) => i.id === id);
    if (!item) return;

    item.quantity += delta;
    if (item.quantity <= 0) {
      cart = cart.filter((i) => i.id !== id);
    }

    saveCart(cart);
  }

  /**
   * Remove Item from Cart
   */
  function removeItem(id) {
    let cart = getCart();
    cart = cart.filter((i) => i.id !== id);
    saveCart(cart);
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
   * Parse Price String to Number
   */
  function parsePrice(priceStr) {
    if (!priceStr) return 0;
    const cleaned = priceStr.replace(/[^0-9.]/g, '');
    const num = parseFloat(cleaned);
    return isNaN(num) ? 0 : num;
  }

  /**
   * Update Full Cart UI (Header Badges, Drawer Items, Subtotal, Free Shipping)
   */
  function updateCartUI() {
    const cart = getCart();

    // 1. Calculate Totals
    let totalCount = 0;
    let subtotal = 0;

    cart.forEach((item) => {
      totalCount += item.quantity;
      subtotal += item.price * item.quantity;
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
            const itemTotal = (item.price * item.quantity).toFixed(2);
            return `
            <div class="cart-drawer-item" data-id="${item.id}">
              <div class="cart-item-thumb">
                <img src="${item.image}" alt="${escapeHtml(item.title)}">
              </div>
              <div class="cart-item-details">
                <div class="cart-item-top">
                  <h4 class="cart-item-title">${escapeHtml(item.title)}</h4>
                  <button type="button" class="cart-item-remove-btn" data-action="remove" data-id="${item.id}" aria-label="Remove item" title="Remove">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="3 6 5 6 21 6"></polyline>
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                  </button>
                </div>
                ${item.flavor ? `<div class="cart-item-variant">${item.flavor.includes('/') ? '' : 'Flavor: '}<span>${escapeHtml(item.flavor)}</span></div>` : ''}
                <div class="cart-item-bottom">
                  <div class="cart-item-qty-control">
                    <button type="button" class="cart-qty-btn" data-action="decrease" data-id="${item.id}" aria-label="Decrease quantity">−</button>
                    <span class="cart-qty-value">${item.quantity}</span>
                    <button type="button" class="cart-qty-btn" data-action="increase" data-id="${item.id}" aria-label="Increase quantity">+</button>
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
    return str
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

    // Checkout Button
    const checkoutBtn = document.getElementById('cartCheckoutBtn');
    if (checkoutBtn) {
      checkoutBtn.addEventListener('click', () => {
        const cart = getCart();
        if (cart.length === 0) {
          alert('Your cart is empty. Please add products to proceed!');
          return;
        }
        window.location.href = 'checkout.php';
      });
    }

    // Header Cart Icon Link Click -> Open Cart Drawer
    document.addEventListener('click', (e) => {
      const cartLink = e.target.closest('a.header-action-btn[href*="cart.php"], #headerCartBtn, .header-cart-badge');
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
        const titleEl = document.querySelector('.product-details-title');
        const priceEl = document.querySelector('.product-price-current');
        const qtyEl = document.getElementById('productQuantity');
        const flavorEl = document.querySelector('.product-flavor-select');
        const imgEl = document.getElementById('productMainImage');

        const title = titleEl ? titleEl.textContent.trim() : 'Premium Vape Product';
        const price = parsePrice(priceEl ? priceEl.textContent : '0');
        const quantity = qtyEl ? parseInt(qtyEl.value, 10) || 1 : 1;
        const flavor = flavorEl && flavorEl.value ? flavorEl.value : '';
        const image = imgEl ? imgEl.getAttribute('src') : 'assets/images/vape-image.webp';
        const id = (title + (flavor ? '-' + flavor : '')).toLowerCase().replace(/[^a-z0-9]+/g, '-');

        addItem({
          id: id,
          title: title,
          price: price,
          quantity: quantity,
          flavor: flavor,
          image: image
        });
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

        const card = cardAddBtn.closest('.product-card, .category-product-card');
        if (!card) return;

        const titleEl = card.querySelector('.product-title a, .product-title, .card-product-title a, .card-product-title');
        const priceEl = card.querySelector('.current-price, .card-current-price');
        const imgEl = card.querySelector('.product-img, .card-product-img');

        const btnTitle = cardAddBtn.getAttribute('data-title');
        const btnPrice = cardAddBtn.getAttribute('data-price');
        const btnImage = cardAddBtn.getAttribute('data-image');
        const btnId = cardAddBtn.getAttribute('data-id');

        const title = btnTitle || (titleEl ? titleEl.textContent.trim() : 'Vape Device');
        const price = btnPrice ? parseFloat(btnPrice) : parsePrice(priceEl ? priceEl.textContent : '19.99');
        const image = btnImage || (imgEl ? imgEl.getAttribute('src') : 'assets/images/vape-image.webp');
        const id = btnId ? String(btnId) : title.toLowerCase().replace(/[^a-z0-9]+/g, '-');

        // Button interactive pulse
        const originalText = cardAddBtn.textContent;
        cardAddBtn.textContent = 'ADDED! ✓';
        cardAddBtn.style.backgroundColor = '#10b981';
        setTimeout(() => {
          cardAddBtn.textContent = originalText;
          cardAddBtn.style.backgroundColor = '';
        }, 1200);

        addItem({
          id: id,
          title: title,
          price: price,
          quantity: 1,
          flavor: '',
          image: image
        });
      }
    });

    // Populate initial UI on page load
    updateCartUI();
  }

  // Run on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // Expose global controller for optional programmatic access
  window.InfinityCart = window.NewVapeCart = {
    open: openDrawer,
    close: closeDrawer,
    get: getCart,
    add: addItem,
    addMultiple: addMultiple,
    clear: function () {
      saveCart([]);
    }
  };
})();
