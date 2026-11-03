<?php
/**
 * INFINITY VAPE SHOP - CART DRAWER COMPONENT
 * Slide-out mini-cart drawer included globally on all pages.
 */
?>
<!-- Cart Drawer Backdrop Overlay -->
<div class="cart-drawer-backdrop" id="cartDrawerBackdrop" aria-hidden="true"></div>

<!-- Cart Drawer Container -->
<aside class="cart-drawer" id="cartDrawer" aria-label="Shopping Cart Drawer" role="dialog" aria-modal="true" aria-hidden="true">
  
  <!-- Drawer Header -->
  <div class="cart-drawer-header">
    <div class="cart-drawer-title-wrap">
      <div class="cart-drawer-icon">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="9" cy="21" r="1"></circle>
          <circle cx="20" cy="21" r="1"></circle>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
      </div>
      <h2 class="cart-drawer-title">Shopping Cart</h2>
      <span class="cart-drawer-count-badge" id="cartDrawerCountBadge">0</span>
    </div>

    <button type="button" class="cart-drawer-close-btn" id="cartDrawerCloseBtn" aria-label="Close cart drawer">
      <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="6" x2="6" y2="18"></line>
        <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
    </button>
  </div>

  <!-- Drawer Body: Product List & Empty State -->
  <div class="cart-drawer-body" id="cartDrawerBody">
    
    <!-- Empty Cart State -->
    <div class="cart-empty-state" id="cartEmptyState" style="display: none;">
      <div class="cart-empty-icon-wrap">
        <svg viewBox="0 0 24 24" width="52" height="52" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="9" cy="21" r="1"></circle>
          <circle cx="20" cy="21" r="1"></circle>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
      </div>
      <h3 class="cart-empty-heading">Your Cart is Empty</h3>
      <p class="cart-empty-desc">You haven't added any items yet. Discover our best-selling disposable vapes, e-liquids and pods.</p>
      <button type="button" class="btn-cart-empty-shop" id="cartEmptyContinueBtn">
        Start Shopping
      </button>
    </div>

    <!-- Active Cart Items List -->
    <div class="cart-items-list" id="cartItemsList">
      <!-- Items dynamically populated via cart-drawer.js -->
    </div>

  </div>

  <!-- Drawer Footer: Totals, Trust, and Checkout Button -->
  <div class="cart-drawer-footer" id="cartDrawerFooter">
    <div class="cart-subtotal-row">
      <span class="cart-subtotal-label">Estimated Subtotal</span>
      <span class="cart-subtotal-val" id="cartSubtotalVal">$0.00</span>
    </div>

    <p class="cart-footer-note">Shipping, taxes &amp; coupon discounts calculated at checkout.</p>

    <div class="cart-drawer-actions">
      <button type="button" class="btn-cart-checkout" id="cartCheckoutBtn">
        <span>Proceed to Checkout</span>
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="5" y1="12" x2="19" y2="12"></line>
          <polyline points="12 5 19 12 12 19"></polyline>
        </svg>
      </button>

      <button type="button" class="btn-cart-continue-link" id="cartContinueShoppingLink">
        Continue Shopping
      </button>
    </div>
  </div>

</aside>
