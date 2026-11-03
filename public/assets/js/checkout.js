/**
 * INFINITY VAPE SHOP - SECURE CHECKOUT PAGE JAVASCRIPT (assets/js/checkout.js)
 * High-converting checkout logic matching vaporhatch:
 * - Dynamic cart rendering from localStorage with default fallbacks
 * - Live shipping method calculation ($5.99, $14.99, FREE)
 * - Payment tabs (Credit Card & Apple Pay)
 * - Billing address toggle
 * - Credit card formatting
 * - Coupon code discount application
 * - Strict field and 21+ age verification validation
 * - Instant Thank You confirmation popup modal
 */

document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // DOM Elements
  const checkoutForm = document.getElementById('checkoutMainForm');
  const btnPlaceOrder = document.getElementById('btnPlaceOrder');
  const btnOrderPriceTotal = document.getElementById('btnOrderPriceTotal');

  // Modal Elements
  const successModal = document.getElementById('orderSuccessModal');
  const modalOrderNumber = document.getElementById('modalOrderNumber');
  const modalPaymentType = document.getElementById('modalPaymentType');
  const modalDeliveryEstimate = document.getElementById('modalDeliveryEstimate');
  const modalTotalPaid = document.getElementById('modalTotalPaid');

  // Summary Elements
  const itemsListEl = document.getElementById('checkoutItemsList');
  const itemsCountEl = document.getElementById('summaryItemsCount');
  const subtotalEl = document.getElementById('checkoutSubtotal');
  const discountRow = document.getElementById('checkoutDiscountRow');
  const discountEl = document.getElementById('checkoutDiscount');
  const shippingFeeEl = document.getElementById('checkoutShippingFee');
  const shippingLabelEl = document.getElementById('selectedShippingLabel');
  const taxEl = document.getElementById('checkoutTax');
  const grandTotalEl = document.getElementById('checkoutGrandTotal');

  // Coupon Elements
  const couponInput = document.getElementById('checkoutCouponInput');
  const btnApplyCoupon = document.getElementById('btnCheckoutCoupon');
  const couponSuccessPill = document.getElementById('checkoutCouponSuccess');
  const btnRemoveCoupon = document.getElementById('btnRemoveCoupon');

  // Payment Tabs
  const paymentRadios = document.querySelectorAll('input[name="paymentType"]');
  const tabCreditCard = document.getElementById('tabCreditCard');
  const tabApplePay = document.getElementById('tabApplePay');
  const contentCreditCard = document.getElementById('contentCreditCard');
  const contentApplePay = document.getElementById('contentApplePay');

  // Billing address radios
  const billingRadios = document.querySelectorAll('input[name="billingAddressType"]');
  const differentBillingForm = document.getElementById('differentBillingForm');

  // Base state values
  let currentSubtotal = 44.98;
  let currentDiscount = 0;
  let currentShipping = 5.99;
  const taxRate = 0.045; // 4.5%

  // Escape HTML helper
  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  // 1. Load Cart Items from LocalStorage
  function loadCartItems() {
    try {
      const stored = localStorage.getItem('infinity_cart_items') || localStorage.getItem('newvape_cart_items');
      const cart = stored ? JSON.parse(stored) : [];

      if (Array.isArray(cart) && cart.length > 0) {
        let calculatedSubtotal = 0;
        let totalItems = 0;

        itemsListEl.innerHTML = cart
          .map(item => {
            const qty = parseInt(item.quantity, 10) || 1;
            const price = parseFloat(item.price) || 0;
            const lineTotal = price * qty;
            calculatedSubtotal += lineTotal;
            totalItems += qty;

            const imgSrc = item.image || 'assets/images/product-1.webp';
            const title = item.title || item.name || 'Vape Product';
            const meta = item.flavor || item.variant || 'Standard Edition';

            return `
              <div class="checkout-item-row" data-price="${price.toFixed(2)}">
                <div class="checkout-item-thumb">
                  <img src="${escapeHtml(imgSrc)}" alt="${escapeHtml(title)}" loading="lazy">
                  <span class="checkout-item-qty-badge">${qty}</span>
                </div>
                <div class="checkout-item-info">
                  <h4 class="checkout-item-title">${escapeHtml(title)}</h4>
                  <div class="checkout-item-meta">${escapeHtml(meta)}</div>
                </div>
                <div class="checkout-item-price">$${lineTotal.toFixed(2)}</div>
              </div>
            `;
          })
          .join('');

        currentSubtotal = calculatedSubtotal;
        if (itemsCountEl) {
          itemsCountEl.textContent = `(${totalItems} ${totalItems === 1 ? 'item' : 'items'})`;
        }
        return;
      }
    } catch (e) {
      console.warn('Error reading cart from localStorage', e);
    }

    // Default Fallback (matches vaporhatch)
    currentSubtotal = 44.98;
    if (itemsCountEl) itemsCountEl.textContent = '(2 items)';
  }

  // 2. Shipping Method Selection
  const shippingRadios = document.querySelectorAll('input[name="shippingMethod"]');
  shippingRadios.forEach(radio => {
    radio.addEventListener('change', function () {
      document.querySelectorAll('.shipping-method-option').forEach(opt => opt.classList.remove('active'));
      
      const parentOption = this.closest('.shipping-method-option');
      if (parentOption) parentOption.classList.add('active');

      currentShipping = parseFloat(this.value) || 0;
      const methodName = this.getAttribute('data-name') || 'Standard Shipping';
      
      if (shippingLabelEl) shippingLabelEl.textContent = methodName;
      if (shippingFeeEl) {
        shippingFeeEl.textContent = currentShipping === 0 ? 'FREE' : '$' + currentShipping.toFixed(2);
        if (currentShipping === 0) {
          shippingFeeEl.classList.add('text-success');
        } else {
          shippingFeeEl.classList.remove('text-success');
        }
      }

      if (modalDeliveryEstimate) {
        if (currentShipping === 0) {
          modalDeliveryEstimate.textContent = 'Ready within 2 hours (In-Store Pickup)';
        } else if (currentShipping >= 10) {
          modalDeliveryEstimate.textContent = '1-2 Business Days (Express Priority)';
        } else {
          modalDeliveryEstimate.textContent = '3-5 Business Days (Standard Ground)';
        }
      }

      recalculateCheckout();
    });
  });

  // 3. Payment Method Switcher
  paymentRadios.forEach(radio => {
    radio.addEventListener('change', function () {
      if (this.value === 'credit_card') {
        if (tabCreditCard) tabCreditCard.classList.add('active');
        if (tabApplePay) tabApplePay.classList.remove('active');
        if (contentCreditCard) contentCreditCard.style.display = 'block';
        if (contentApplePay) contentApplePay.style.display = 'none';
        if (modalPaymentType) modalPaymentType.textContent = 'Credit Card (Ending in 4242)';
      } else if (this.value === 'apple_pay') {
        if (tabCreditCard) tabCreditCard.classList.remove('active');
        if (tabApplePay) tabApplePay.classList.add('active');
        if (contentCreditCard) contentCreditCard.style.display = 'none';
        if (contentApplePay) contentApplePay.style.display = 'block';
        if (modalPaymentType) modalPaymentType.textContent = 'Apple Pay / Express Wallet';
      }
    });
  });

  // Apple Pay Quick Button Handler
  const btnApplePayQuick = document.getElementById('btnApplePayQuick');
  if (btnApplePayQuick) {
    btnApplePayQuick.addEventListener('click', function () {
      validateAndSubmitOrder('apple_pay');
    });
  }

  // 4. Billing Address Toggle
  billingRadios.forEach(radio => {
    radio.addEventListener('change', function () {
      if (this.value === 'different') {
        if (differentBillingForm) differentBillingForm.style.display = 'block';
      } else {
        if (differentBillingForm) differentBillingForm.style.display = 'none';
      }
    });
  });

  // 5. Card Formatting Helpers
  const ccNumberInput = document.getElementById('ccNumber');
  if (ccNumberInput) {
    ccNumberInput.addEventListener('input', function (e) {
      const val = e.target.value.replace(/\D/g, '');
      const formatted = val.match(/.{1,4}/g)?.join(' ') || val;
      e.target.value = formatted.substring(0, 19);
    });
  }

  const ccExpiryInput = document.getElementById('ccExpiry');
  if (ccExpiryInput) {
    ccExpiryInput.addEventListener('input', function (e) {
      const val = e.target.value.replace(/\D/g, '');
      if (val.length >= 2) {
        e.target.value = val.substring(0, 2) + ' / ' + val.substring(2, 4);
      } else {
        e.target.value = val;
      }
    });
  }

  // 6. Promo Code Application
  if (btnApplyCoupon && couponInput) {
    btnApplyCoupon.addEventListener('click', function () {
      applyCouponCode();
    });

    couponInput.addEventListener('keypress', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        applyCouponCode();
      }
    });
  }

  function applyCouponCode() {
    const code = couponInput.value.trim().toUpperCase();
    if (!code) {
      alert('Please enter a promo or coupon code.');
      return;
    }

    // Apply 10% discount
    currentDiscount = currentSubtotal * 0.10;
    if (discountRow) discountRow.style.display = 'flex';
    if (discountEl) discountEl.textContent = '-$' + currentDiscount.toFixed(2);
    if (couponSuccessPill) {
      couponSuccessPill.innerHTML = `<span>Code <strong>${escapeHtml(code)}</strong> applied (-$${currentDiscount.toFixed(2)})</span><button type="button" class="btn-remove-coupon" id="btnRemoveCoupon">&times;</button>`;
      couponSuccessPill.style.display = 'inline-flex';
      
      const newBtnRemove = document.getElementById('btnRemoveCoupon');
      if (newBtnRemove) {
        newBtnRemove.addEventListener('click', removeCouponCode);
      }
    }
    
    couponInput.value = '';
    couponInput.disabled = true;
    btnApplyCoupon.disabled = true;

    recalculateCheckout();
  }

  function removeCouponCode() {
    currentDiscount = 0;
    if (discountRow) discountRow.style.display = 'none';
    if (couponSuccessPill) couponSuccessPill.style.display = 'none';
    
    if (couponInput) {
      couponInput.disabled = false;
      couponInput.value = '';
    }
    if (btnApplyCoupon) btnApplyCoupon.disabled = false;

    recalculateCheckout();
  }

  if (btnRemoveCoupon) {
    btnRemoveCoupon.addEventListener('click', removeCouponCode);
  }

  // 7. Recalculate Totals
  function recalculateCheckout() {
    const taxable = Math.max(0, currentSubtotal - currentDiscount);
    const tax = taxable * taxRate;
    const grandTotal = taxable + currentShipping + tax;

    if (subtotalEl) subtotalEl.textContent = '$' + currentSubtotal.toFixed(2);
    if (taxEl) taxEl.textContent = '$' + tax.toFixed(2);
    if (grandTotalEl) grandTotalEl.textContent = '$' + grandTotal.toFixed(2);
    if (btnOrderPriceTotal) btnOrderPriceTotal.textContent = '$' + grandTotal.toFixed(2);
    if (modalTotalPaid) modalTotalPaid.textContent = '$' + grandTotal.toFixed(2);
  }

  // 8. Order Placement & Form Validation
  if (checkoutForm) {
    checkoutForm.addEventListener('submit', function (e) {
      e.preventDefault();
      validateAndSubmitOrder();
    });
  }

  if (btnPlaceOrder) {
    btnPlaceOrder.addEventListener('click', function (e) {
      e.preventDefault();
      validateAndSubmitOrder();
    });
  }

  function validateAndSubmitOrder(paymentOverride) {
    let isValid = true;
    const requiredInputs = checkoutForm ? checkoutForm.querySelectorAll('input[required], select[required]') : [];
    
    // Clear previous errors
    requiredInputs.forEach(input => {
      input.classList.remove('has-error');
      const errorMsg = input.parentElement.querySelector('.field-error-msg');
      if (errorMsg) errorMsg.style.display = 'none';
    });

    // Check age verification checkbox
    const ageCheck = document.getElementById('ageVerificationCheck');
    const ageError = document.getElementById('ageErrorMsg');
    if (ageCheck && !ageCheck.checked) {
      isValid = false;
      if (ageError) ageError.style.display = 'block';
    } else if (ageError) {
      ageError.style.display = 'none';
    }

    // Determine current payment method
    const selectedPayment = paymentOverride || document.querySelector('input[name="paymentType"]:checked')?.value || 'credit_card';

    // Validate email format
    const emailInput = document.getElementById('checkoutEmail');
    if (emailInput && emailInput.value.trim()) {
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailPattern.test(emailInput.value.trim())) {
        isValid = false;
        emailInput.classList.add('has-error');
        const errorMsg = emailInput.parentElement.querySelector('.field-error-msg');
        if (errorMsg) {
          errorMsg.textContent = 'Please enter a valid email address.';
          errorMsg.style.display = 'block';
        }
      }
    }

    // Check required inputs
    requiredInputs.forEach(input => {
      // If Apple Pay is chosen, ignore credit card validation
      if (selectedPayment === 'apple_pay' && (input.id === 'ccNumber' || input.id === 'ccName' || input.id === 'ccExpiry' || input.id === 'ccCvc')) {
        return;
      }

      if (!input.value.trim()) {
        isValid = false;
        input.classList.add('has-error');
        const errorMsg = input.parentElement.querySelector('.field-error-msg');
        if (errorMsg) errorMsg.style.display = 'block';
      }
    });

    if (!isValid) {
      const firstError = document.querySelector('.has-error, #ageVerificationCheck:not(:checked)');
      if (firstError) {
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
      return;
    }

    // Submit Simulation
    if (btnPlaceOrder) {
      btnPlaceOrder.disabled = true;
      btnPlaceOrder.innerHTML = `
        <span class="btn-order-text">Processing Order...</span>
      `;
    }

    setTimeout(() => {
      // Generate Order confirmation number
      const randomId = Math.floor(100000 + Math.random() * 900000);
      if (modalOrderNumber) {
        modalOrderNumber.textContent = `#INF-${randomId}`;
      }

      // Clear Cart from localStorage
      try {
        localStorage.removeItem('infinity_cart_items');
        localStorage.removeItem('newvape_cart_items');
      } catch (e) {}

      // Update cart count badges in header
      const cartBadges = document.querySelectorAll('.cart-count-badge, .header-cart-badge, .cart-count, .cart-counter');
      cartBadges.forEach(badge => {
        badge.textContent = '';
        badge.style.display = 'none';
        badge.setAttribute('data-count', '0');
        badge.classList.add('is-empty');
      });

      // Display Confirmation Modal
      if (successModal) {
        successModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
      }
    }, 800);
  }

  // Initialize
  loadCartItems();
  recalculateCheckout();
});
