/**
 * INFINITY VAPE SHOP - DYNAMIC PRODUCTION CHECKOUT JAVASCRIPT (assets/js/checkout.js)
 * Fully connected to MariaDB backend order processing, atomic transactions,
 * live stock decrement, coupon engine, and sandbox test checkout.
 */

document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // Universal API Base URL
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

  // Dynamic Base Values (initialized from PHP rendered markup)
  const summaryTotalsList = document.querySelector('.summary-totals-list');
  let currentSubtotal = summaryTotalsList && summaryTotalsList.dataset.subtotal ? parseFloat(summaryTotalsList.dataset.subtotal) : 0;
  let taxRate = summaryTotalsList && summaryTotalsList.dataset.taxRate ? (parseFloat(summaryTotalsList.dataset.taxRate) / 100) : 0.045;
  let freeThreshold = summaryTotalsList && summaryTotalsList.dataset.freeThreshold ? parseFloat(summaryTotalsList.dataset.freeThreshold) : 150.00;
  let currentDiscount = 0;
  let currentShipping = (currentSubtotal >= freeThreshold) ? 0.00 : 5.99;
  let selectedMethodName = (currentSubtotal >= freeThreshold) ? 'Free Ground Shipping' : 'Standard Ground Shipping';

  // Check initial active radio
  const activeShippingRadio = document.querySelector('input[name="shippingMethod"]:checked');
  if (activeShippingRadio) {
    currentShipping = parseFloat(activeShippingRadio.value) || 0;
    selectedMethodName = activeShippingRadio.getAttribute('data-name') || selectedMethodName;
  }

  // 1. Recalculate Totals Helper
  function recalculateCheckout() {
    const discountedSubtotal = Math.max(0, currentSubtotal - currentDiscount);
    
    // Auto free shipping if threshold met and not pickup
    let effectiveShipping = currentShipping;
    if (currentSubtotal >= freeThreshold && selectedMethodName.indexOf('Express') === -1 && selectedMethodName.indexOf('Pickup') === -1) {
      effectiveShipping = 0.00;
    }

    const calculatedTax = Math.round(discountedSubtotal * taxRate * 100) / 100;
    const finalTotal = Math.round((discountedSubtotal + effectiveShipping + calculatedTax) * 100) / 100;

    if (subtotalEl) subtotalEl.textContent = `$${currentSubtotal.toFixed(2)}`;

    if (discountRow && discountEl) {
      if (currentDiscount > 0) {
        discountRow.style.display = 'flex';
        discountEl.textContent = `-$${currentDiscount.toFixed(2)}`;
      } else {
        discountRow.style.display = 'none';
      }
    }

    if (shippingFeeEl) {
      if (effectiveShipping === 0) {
        shippingFeeEl.textContent = 'FREE';
        shippingFeeEl.classList.add('text-success');
      } else {
        shippingFeeEl.textContent = `$${effectiveShipping.toFixed(2)}`;
        shippingFeeEl.classList.remove('text-success');
      }
    }

    if (taxEl) taxEl.textContent = `$${calculatedTax.toFixed(2)}`;
    if (grandTotalEl) grandTotalEl.textContent = `$${finalTotal.toFixed(2)}`;
    if (btnOrderPriceTotal) btnOrderPriceTotal.textContent = `$${finalTotal.toFixed(2)}`;
  }

  // 2. Shipping Method Selection
  const shippingRadios = document.querySelectorAll('input[name="shippingMethod"]');
  shippingRadios.forEach((radio) => {
    radio.addEventListener('change', function () {
      document.querySelectorAll('.shipping-method-option').forEach((opt) => opt.classList.remove('active'));

      const parentOption = this.closest('.shipping-method-option');
      if (parentOption) parentOption.classList.add('active');

      currentShipping = parseFloat(this.value) || 0;
      selectedMethodName = this.getAttribute('data-name') || 'Standard Ground Shipping';

      if (shippingLabelEl) shippingLabelEl.textContent = selectedMethodName;

      recalculateCheckout();
    });
  });

  // 3. Payment Method Switcher
  paymentRadios.forEach((radio) => {
    radio.addEventListener('change', function () {
      if (this.value === 'credit_card') {
        if (tabCreditCard) tabCreditCard.classList.add('active');
        if (tabApplePay) tabApplePay.classList.remove('active');
        if (contentCreditCard) contentCreditCard.style.display = 'block';
        if (contentApplePay) contentApplePay.style.display = 'none';
      } else if (this.value === 'apple_pay') {
        if (tabCreditCard) tabCreditCard.classList.remove('active');
        if (tabApplePay) tabApplePay.classList.add('active');
        if (contentCreditCard) contentCreditCard.style.display = 'none';
        if (contentApplePay) contentApplePay.style.display = 'block';
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

  // 4. Coupon Code Engine
  if (btnApplyCoupon && couponInput) {
    btnApplyCoupon.addEventListener('click', function () {
      const code = couponInput.value.trim();
      if (!code) {
        alert('Please enter a coupon code.');
        return;
      }

      btnApplyCoupon.disabled = true;
      btnApplyCoupon.textContent = 'Applying...';

      fetch(`${getApiBaseUrl()}/checkout/apply-coupon`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ code: code })
      })
        .then((res) => res.json())
        .then((data) => {
          btnApplyCoupon.disabled = false;
          btnApplyCoupon.textContent = 'Apply';

          if (data.success) {
            currentDiscount = parseFloat(data.discount) || 0;
            if (couponSuccessPill) {
              couponSuccessPill.style.display = 'flex';
              const textEl = document.getElementById('couponSuccessText');
              if (textEl) {
                textEl.innerHTML = `Code <strong>${escapeHtml(data.coupon_code)}</strong> applied (${data.formatted_discount})`;
              }
            }
            recalculateCheckout();
          } else {
            alert(data.message || 'Invalid coupon code.');
          }
        })
        .catch((err) => {
          btnApplyCoupon.disabled = false;
          btnApplyCoupon.textContent = 'Apply';
          console.error('Coupon apply error:', err);
          alert('Network error while applying coupon.');
        });
    });
  }

  if (btnRemoveCoupon) {
    btnRemoveCoupon.addEventListener('click', function () {
      fetch(`${getApiBaseUrl()}/checkout/remove-coupon`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then((res) => res.json())
        .then(() => {
          currentDiscount = 0;
          if (couponSuccessPill) couponSuccessPill.style.display = 'none';
          if (couponInput) couponInput.value = '';
          recalculateCheckout();
        });
    });
  }

  // 5. Card Formatting Helpers
  const ccNumberInput = document.getElementById('ccNumber');
  if (ccNumberInput) {
    ccNumberInput.addEventListener('input', function (e) {
      let val = e.target.value.replace(/\D/g, '').substring(0, 16);
      let formatted = val.match(/.{1,4}/g)?.join(' ') || val;
      e.target.value = formatted;
    });
  }

  const ccExpiryInput = document.getElementById('ccExpiry');
  if (ccExpiryInput) {
    ccExpiryInput.addEventListener('input', function (e) {
      let val = e.target.value.replace(/\D/g, '').substring(0, 4);
      if (val.length >= 2) {
        val = val.substring(0, 2) + ' / ' + val.substring(2);
      }
      e.target.value = val;
    });
  }

  const ccCvcInput = document.getElementById('ccCvc');
  if (ccCvcInput) {
    ccCvcInput.addEventListener('input', function (e) {
      e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
    });
  }

  // 6. Submit Order Handler
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
    requiredInputs.forEach((input) => {
      input.classList.remove('has-error');
      const errorMsg = input.parentElement ? input.parentElement.querySelector('.field-error-msg') : null;
      if (errorMsg) errorMsg.style.display = 'none';
    });

    // Check age verification checkbox (Must be 21+)
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
    if (emailInput) {
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailInput.value.trim() || !emailPattern.test(emailInput.value.trim())) {
        isValid = false;
        emailInput.classList.add('has-error');
        const errorMsg = emailInput.parentElement ? emailInput.parentElement.querySelector('.field-error-msg') : null;
        if (errorMsg) {
          errorMsg.textContent = 'Please enter a valid email address.';
          errorMsg.style.display = 'block';
        }
      }
    }

    // Check other required inputs
    requiredInputs.forEach((input) => {
      if (selectedPayment === 'apple_pay' && (input.id === 'ccNumber' || input.id === 'ccName' || input.id === 'ccExpiry' || input.id === 'ccCvc')) {
        return;
      }

      if (!input.value.trim()) {
        isValid = false;
        input.classList.add('has-error');
        const errorMsg = input.parentElement ? input.parentElement.querySelector('.field-error-msg') : null;
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

    // Assemble payload for backend order processing
    const payload = {
      email: emailInput ? emailInput.value.trim() : '',
      firstName: document.getElementById('shippingFirstName')?.value.trim() || '',
      lastName: document.getElementById('shippingLastName')?.value.trim() || '',
      address1: document.getElementById('shippingAddress1')?.value.trim() || '',
      address2: document.getElementById('shippingAddress2')?.value.trim() || '',
      city: document.getElementById('shippingCity')?.value.trim() || '',
      state: document.getElementById('shippingState')?.value.trim() || '',
      zip: document.getElementById('shippingZip')?.value.trim() || '',
      phone: document.getElementById('shippingPhone')?.value.trim() || '',
      shippingMethod: currentShipping.toFixed(2),
      deliveryMethodName: selectedMethodName,
      paymentType: selectedPayment,
      ageVerification: ageCheck ? ageCheck.checked : true
    };

    const originalBtnHtml = btnPlaceOrder.innerHTML;
    btnPlaceOrder.disabled = true;
    btnPlaceOrder.innerHTML = `
      <span class="btn-order-text">Processing Order...</span>
    `;

    fetch(`${getApiBaseUrl()}/checkout/process`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify(payload)
    })
      .then((res) => res.json())
      .then((data) => {
        btnPlaceOrder.disabled = false;
        btnPlaceOrder.innerHTML = originalBtnHtml;

        if (data.success) {
          // Clear client cart cache
          try {
            localStorage.removeItem('infinity_cart_items');
            localStorage.removeItem('newvape_cart_items');
          } catch (e) {}

          // Update header badges
          const headerBadges = document.querySelectorAll('.header-cart-badge, .cart-drawer-count-badge');
          headerBadges.forEach((badge) => {
            badge.textContent = '0';
            badge.style.display = 'none';
            badge.setAttribute('data-count', '0');
          });

          // Populate Confirmation Modal
          if (modalOrderNumber) modalOrderNumber.textContent = data.order_number;
          if (modalPaymentType) modalPaymentType.textContent = data.payment_method + (data.test_mode ? ' (Sandbox Test)' : '');
          if (modalDeliveryEstimate) modalDeliveryEstimate.textContent = data.delivery_estimate;
          if (modalTotalPaid) modalTotalPaid.textContent = data.total;

          // Display Confirmation Modal
          if (successModal) {
            successModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
          }
        } else {
          alert(data.message || 'Unable to place order. Please review your details.');
        }
      })
      .catch((err) => {
        btnPlaceOrder.disabled = false;
        btnPlaceOrder.innerHTML = originalBtnHtml;
        console.error('Order process error:', err);
        alert('Network error while processing order. Please try again.');
      });
  }

  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  // Run initial recalculation
  recalculateCheckout();
});
