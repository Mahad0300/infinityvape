<?php
/**
 * INFINITY VAPE SHOP - SECURE CHECKOUT PAGE (checkout.php)
 * High-converting 2-column e-commerce checkout page matching vaporhatch.
 */
$page_title   = 'Secure Checkout - Infinity Vape Shop';
$current_page = 'checkout';
$page_js      = 'checkout';

include dirname(__DIR__) . '/includes/header.php';
?>

<!-- Breadcrumb Navigation -->
<nav class="category-breadcrumb-nav" aria-label="Breadcrumb">
  <div class="container">
    <ol class="breadcrumb-list">
      <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
      <li class="breadcrumb-separator" aria-hidden="true">
        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </li>
      <li class="breadcrumb-item"><a href="<?= url('collections') ?>">Shop</a></li>
      <li class="breadcrumb-separator" aria-hidden="true">
        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </li>
      <li class="breadcrumb-item active" aria-current="page">Checkout</li>
    </ol>
  </div>
</nav>

<!-- Main Checkout Section -->
<section class="checkout-page-main">
  <div class="container">
    
    <!-- Page Header -->
    <div class="checkout-page-header">
      <h1 class="checkout-page-title">Secure Checkout</h1>
      <p class="checkout-page-subtitle">Please fill in your details below to complete your order.</p>
    </div>

    <!-- 2-Column Checkout Layout -->
    <div class="checkout-layout">
      
      <!-- Left Column: Checkout Forms & Steps -->
      <div class="checkout-main-column">
        <form id="checkoutMainForm" class="checkout-form" onsubmit="return false;" novalidate>
          
          <!-- STEP 1: Contact Information -->
          <div class="checkout-section-card">
            <div class="checkout-section-header">
              <div class="section-step-num">1</div>
              <h2 class="section-step-title">Contact Information</h2>
              <span class="section-login-prompt">Already have an account? <a href="<?= url('my-account') ?>" class="link-highlight">Log in</a></span>
            </div>
            
            <div class="checkout-section-body">
              <div class="form-group">
                <label for="checkoutEmail" class="form-label">Email Address <span class="required-star">*</span></label>
                <input type="email" id="checkoutEmail" name="email" class="form-input" placeholder="you@example.com" required autocomplete="email">
                <span class="field-error-msg">Please enter a valid email address.</span>
              </div>
              <div class="form-checkbox-wrap">
                <input type="checkbox" id="newsletterOptIn" name="newsletter" class="form-checkbox" checked>
                <label for="newsletterOptIn" class="checkbox-label">Keep me updated with exclusive deals, restocks, and vaping news</label>
              </div>
            </div>
          </div>

          <!-- STEP 2: Shipping Address -->
          <div class="checkout-section-card">
            <div class="checkout-section-header">
              <div class="section-step-num">2</div>
              <h2 class="section-step-title">Shipping Address</h2>
            </div>

            <div class="checkout-section-body">
              <div class="form-row form-row-2">
                <div class="form-group">
                  <label for="shippingFirstName" class="form-label">First Name <span class="required-star">*</span></label>
                  <input type="text" id="shippingFirstName" name="firstName" class="form-input" placeholder="First Name" required autocomplete="given-name">
                  <span class="field-error-msg">Please enter your first name.</span>
                </div>
                <div class="form-group">
                  <label for="shippingLastName" class="form-label">Last Name <span class="required-star">*</span></label>
                  <input type="text" id="shippingLastName" name="lastName" class="form-input" placeholder="Last Name" required autocomplete="family-name">
                  <span class="field-error-msg">Please enter your last name.</span>
                </div>
              </div>

              <div class="form-group">
                <label for="shippingAddress1" class="form-label">Street Address <span class="required-star">*</span></label>
                <input type="text" id="shippingAddress1" name="address1" class="form-input" placeholder="House number and street name" required autocomplete="street-address">
                <span class="field-error-msg">Please enter your street address.</span>
              </div>

              <div class="form-group">
                <label for="shippingAddress2" class="form-label">Apartment, suite, unit, etc. (optional)</label>
                <input type="text" id="shippingAddress2" name="address2" class="form-input" placeholder="Apt, Suite, Building, Floor, etc.">
              </div>

              <div class="form-row form-row-3">
                <div class="form-group">
                  <label for="shippingCity" class="form-label">City <span class="required-star">*</span></label>
                  <input type="text" id="shippingCity" name="city" class="form-input" placeholder="City" required autocomplete="address-level2">
                  <span class="field-error-msg">Required.</span>
                </div>
                
                <div class="form-group">
                  <label for="shippingState" class="form-label">State <span class="required-star">*</span></label>
                  <select id="shippingState" name="state" class="form-select" required autocomplete="address-level1">
                    <option value="" disabled selected>Select State</option>
                    <option value="AL">Alabama</option>
                    <option value="AK">Alaska</option>
                    <option value="AZ">Arizona</option>
                    <option value="AR">Arkansas</option>
                    <option value="CO">Colorado</option>
                    <option value="CT">Connecticut</option>
                    <option value="DE">Delaware</option>
                    <option value="FL">Florida</option>
                    <option value="GA">Georgia</option>
                    <option value="HI">Hawaii</option>
                    <option value="ID">Idaho</option>
                    <option value="IL">Illinois</option>
                    <option value="IN">Indiana</option>
                    <option value="IA">Iowa</option>
                    <option value="KS">Kansas</option>
                    <option value="KY">Kentucky</option>
                    <option value="LA">Louisiana</option>
                    <option value="MD">Maryland</option>
                    <option value="MI">Michigan</option>
                    <option value="MN">Minnesota</option>
                    <option value="MS">Mississippi</option>
                    <option value="MO">Missouri</option>
                    <option value="MT">Montana</option>
                    <option value="NE">Nebraska</option>
                    <option value="NV">Nevada</option>
                    <option value="NH">New Hampshire</option>
                    <option value="NM">New Mexico</option>
                    <option value="NC">North Carolina</option>
                    <option value="ND">North Dakota</option>
                    <option value="OK">Oklahoma</option>
                    <option value="OR">Oregon</option>
                    <option value="PA">Pennsylvania</option>
                    <option value="SC">South Carolina</option>
                    <option value="SD">South Dakota</option>
                    <option value="TN">Tennessee</option>
                    <option value="TX">Texas</option>
                    <option value="UT">Utah</option>
                    <option value="VA">Virginia</option>
                    <option value="WA">Washington</option>
                    <option value="WV">West Virginia</option>
                    <option value="WI">Wisconsin</option>
                    <option value="WY">Wyoming</option>
                  </select>
                  <span class="field-error-msg">Required.</span>
                </div>

                <div class="form-group">
                  <label for="shippingZip" class="form-label">ZIP Code <span class="required-star">*</span></label>
                  <input type="text" id="shippingZip" name="zip" class="form-input" placeholder="73170" required autocomplete="postal-code" maxlength="10">
                  <span class="field-error-msg">Required.</span>
                </div>
              </div>

              <div class="form-group">
                <label for="shippingPhone" class="form-label">Phone Number <span class="required-star">*</span></label>
                <input type="tel" id="shippingPhone" name="phone" class="form-input" placeholder="(405) 555-0199" required autocomplete="tel">
                <span class="field-help-text">Used for shipping updates and delivery notifications.</span>
                <span class="field-error-msg">Please enter a valid phone number.</span>
              </div>

              <div class="form-checkbox-wrap">
                <input type="checkbox" id="saveAddressInfo" name="saveAddress" class="form-checkbox" checked>
                <label for="saveAddressInfo" class="checkbox-label">Save this shipping information for next time</label>
              </div>
            </div>
          </div>

          <!-- STEP 3: Shipping Method -->
          <div class="checkout-section-card">
            <div class="checkout-section-header">
              <div class="section-step-num">3</div>
              <h2 class="section-step-title">Shipping Method</h2>
            </div>

            <div class="checkout-section-body">
              <div class="shipping-methods-list">
                
                <!-- Standard Shipping -->
                <label class="shipping-method-option active" for="shipMethodStandard">
                  <input type="radio" name="shippingMethod" id="shipMethodStandard" value="5.99" data-name="Standard Shipping" checked>
                  <div class="shipping-method-details">
                    <span class="shipping-method-name">Standard Ground Shipping</span>
                    <span class="shipping-method-estimate">3 - 5 Business Days • Tracking Included</span>
                  </div>
                  <span class="shipping-method-price">$5.99</span>
                </label>

                <!-- Express Shipping -->
                <label class="shipping-method-option" for="shipMethodExpress">
                  <input type="radio" name="shippingMethod" id="shipMethodExpress" value="14.99" data-name="Express Priority">
                  <div class="shipping-method-details">
                    <span class="shipping-method-name">Express Priority Delivery</span>
                    <span class="shipping-method-estimate">1 - 2 Business Days • Fast Priority Dispatch</span>
                  </div>
                  <span class="shipping-method-price">$14.99</span>
                </label>

                <!-- In Store Pickup -->
                <label class="shipping-method-option" for="shipMethodPickup">
                  <input type="radio" name="shippingMethod" id="shipMethodPickup" value="0.00" data-name="Store Pickup">
                  <div class="shipping-method-details">
                    <span class="shipping-method-name">Free In-Store Pickup (Oklahoma City)</span>
                    <span class="shipping-method-estimate">Ready within 2 hours • 10800 S Penn Ave</span>
                  </div>
                  <span class="shipping-method-price text-success">FREE</span>
                </label>

              </div>
            </div>
          </div>

          <!-- STEP 4: Payment Method -->
          <div class="checkout-section-card">
            <div class="checkout-section-header">
              <div class="section-step-num">4</div>
              <h2 class="section-step-title">Payment Method</h2>
              <span class="encrypted-badge">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                256-Bit Encrypted
              </span>
            </div>

            <div class="checkout-section-body">
              
              <!-- Payment Method Selection Tabs / Radios -->
              <div class="payment-tabs-group">
                
                <!-- Credit Card Option -->
                <div class="payment-tab-item active" id="tabCreditCard">
                  <label class="payment-tab-header" for="paymentRadioCC">
                    <input type="radio" name="paymentType" id="paymentRadioCC" value="credit_card" checked>
                    <span class="payment-tab-title">Credit / Debit Card</span>
                    <div class="payment-card-logos">
                      <img src="assets/images/visa.webp" alt="Visa" loading="lazy">
                      <img src="assets/images/mastercard.webp" alt="Mastercard" loading="lazy">
                      <img src="assets/images/american-express.webp" alt="Amex" loading="lazy">
                      <img src="assets/images/discover.webp" alt="Discover" loading="lazy">
                    </div>
                  </label>

                  <!-- Card Details Fields -->
                  <div class="payment-tab-content" id="contentCreditCard">
                    <div class="form-group">
                      <label for="ccNumber" class="form-label">Card Number <span class="required-star">*</span></label>
                      <div class="input-with-icon">
                        <input type="text" id="ccNumber" name="cardNumber" class="form-input" placeholder="4242 •••• •••• 4242" maxlength="19" autocomplete="cc-number">
                        <svg class="input-icon-right" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#888" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                          <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                      </div>
                      <span class="field-error-msg">Please enter a valid card number.</span>
                    </div>

                    <div class="form-group">
                      <label for="ccName" class="form-label">Name on Card <span class="required-star">*</span></label>
                      <input type="text" id="ccName" name="cardName" class="form-input" placeholder="Cardholder Name" autocomplete="cc-name">
                      <span class="field-error-msg">Please enter the name on your card.</span>
                    </div>

                    <div class="form-row form-row-2">
                      <div class="form-group">
                        <label for="ccExpiry" class="form-label">Expiration Date <span class="required-star">*</span></label>
                        <input type="text" id="ccExpiry" name="cardExpiry" class="form-input" placeholder="MM / YY" maxlength="7" autocomplete="cc-exp">
                        <span class="field-error-msg">Valid MM/YY required.</span>
                      </div>
                      <div class="form-group">
                        <label for="ccCvc" class="form-label">Security Code (CVV) <span class="required-star">*</span></label>
                        <div class="input-with-icon">
                          <input type="password" id="ccCvc" name="cardCvc" class="form-input" placeholder="CVC" maxlength="4" autocomplete="cc-csc">
                          <svg class="input-icon-right" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#888" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                          </svg>
                        </div>
                        <span class="field-error-msg">3 or 4 digits.</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Apple Pay / Express Option -->
                <div class="payment-tab-item" id="tabApplePay">
                  <label class="payment-tab-header" for="paymentRadioApple">
                    <input type="radio" name="paymentType" id="paymentRadioApple" value="apple_pay">
                    <span class="payment-tab-title">Apple Pay / Express Wallet</span>
                    <div class="payment-card-logos">
                      <img src="assets/images/apple-pay.webp" alt="Apple Pay" loading="lazy">
                    </div>
                  </label>
                  <div class="payment-tab-content" id="contentApplePay" style="display: none;">
                    <div class="express-wallet-box">
                      <p>You will authenticate seamlessly with Apple Touch ID, Face ID, or your Wallet upon clicking Place Order.</p>
                      <button type="button" class="btn-apple-pay-quick" id="btnApplePayQuick">
                        <span>Pay with</span>
                        <img src="assets/images/apple-pay.webp" alt="Apple Pay" height="20">
                      </button>
                    </div>
                  </div>
                </div>

              </div>

              <!-- Billing Address Toggle -->
              <div class="billing-address-section">
                <h3 class="sub-section-title">Billing Address</h3>
                <div class="billing-radio-group">
                  <label class="billing-radio-option" for="billingSame">
                    <input type="radio" name="billingAddressType" id="billingSame" value="same" checked>
                    <span>Same as shipping address</span>
                  </label>
                  <label class="billing-radio-option" for="billingDifferent">
                    <input type="radio" name="billingAddressType" id="billingDifferent" value="different">
                    <span>Use a different billing address</span>
                  </label>
                </div>

                <!-- Different Billing Address Form (Hidden by default) -->
                <div class="different-billing-form" id="differentBillingForm" style="display: none;">
                  <div class="form-row form-row-2">
                    <div class="form-group">
                      <label class="form-label">Billing First Name</label>
                      <input type="text" class="form-input" placeholder="First Name">
                    </div>
                    <div class="form-group">
                      <label class="form-label">Billing Last Name</label>
                      <input type="text" class="form-input" placeholder="Last Name">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Billing Street Address</label>
                    <input type="text" class="form-input" placeholder="House number and street name">
                  </div>
                  <div class="form-row form-row-3">
                    <div class="form-group">
                      <label class="form-label">City</label>
                      <input type="text" class="form-input" placeholder="City">
                    </div>
                    <div class="form-group">
                      <label class="form-label">State</label>
                      <input type="text" class="form-input" placeholder="State">
                    </div>
                    <div class="form-group">
                      <label class="form-label">ZIP Code</label>
                      <input type="text" class="form-input" placeholder="ZIP Code">
                    </div>
                  </div>
                </div>
              </div>

              <!-- Age Verification & Legal Agreement -->
              <div class="checkout-age-agreement">
                <div class="form-checkbox-wrap">
                  <input type="checkbox" id="ageVerificationCheck" name="ageVerification" class="form-checkbox" required checked>
                  <label for="ageVerificationCheck" class="checkbox-label">
                    <strong>Age Verification Agreement:</strong> I certify that I am at least <strong>21 years of age</strong> (or the legal smoking age in my jurisdiction) and agree to Infinity Vape Shop's <a href="<?= url('page/terms-and-conditions') ?>" target="_blank" class="link-highlight">Terms &amp; Conditions</a> and <a href="<?= url('page/privacy-policy') ?>" target="_blank" class="link-highlight">Privacy Policy</a>.
                  </label>
                </div>
                <span class="field-error-msg" id="ageErrorMsg" style="display: none;">You must confirm you are 21+ to proceed.</span>
              </div>

              <!-- Submit Place Order Button -->
              <div class="checkout-submit-wrapper">
                <button type="submit" class="btn-place-order" id="btnPlaceOrder">
                  <span class="btn-lock-icon">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                      <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                  </span>
                  <span class="btn-order-text">PLACE ORDER</span>
                  <span class="btn-order-price" id="btnOrderPriceTotal">$52.99</span>
                </button>
                <p class="order-guarantee-micro">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                  </svg>
                  Guaranteed Safe &amp; Encrypted 256-bit SSL Checkout
                </p>
              </div>

            </div>
          </div>

        </form>
      </div>

      <!-- Right Column: Sticky Order Summary & Review -->
      <div class="checkout-sidebar-column">
        <div class="checkout-summary-card">
          
          <div class="summary-card-header">
            <h2 class="summary-card-title">Order Summary</h2>
            <span class="summary-item-count" id="summaryItemsCount">(2 items)</span>
          </div>

          <!-- Products mini list -->
          <div class="checkout-items-list" id="checkoutItemsList">
            
            <!-- Item 1 -->
            <div class="checkout-item-row" data-price="29.99">
              <div class="checkout-item-thumb">
                <img src="assets/images/product-1.webp" alt="Juice Head Salt x Vaporesso 50K Bundle" loading="lazy">
                <span class="checkout-item-qty-badge">1</span>
              </div>
              <div class="checkout-item-info">
                <h4 class="checkout-item-title">Juice Head Salt x Vaporesso 50K Bundle</h4>
                <div class="checkout-item-meta">Peach Pear • 50mg</div>
              </div>
              <div class="checkout-item-price">$29.99</div>
            </div>

            <!-- Item 2 -->
            <div class="checkout-item-row" data-price="14.99">
              <div class="checkout-item-thumb">
                <img src="assets/images/most-popular-1.webp" alt="Geek Bar Pulse 15K Disposable" loading="lazy">
                <span class="checkout-item-qty-badge">1</span>
              </div>
              <div class="checkout-item-info">
                <h4 class="checkout-item-title">Geek Bar Pulse 15K Disposable</h4>
                <div class="checkout-item-meta">Watermelon Ice • 50mg</div>
              </div>
              <div class="checkout-item-price">$14.99</div>
            </div>

          </div>

          <!-- Coupon Code Input -->
          <div class="checkout-promo-box">
            <form class="checkout-coupon-form" id="checkoutCouponForm" onsubmit="return false;">
              <input type="text" class="checkout-coupon-input" id="checkoutCouponInput" placeholder="Promo / Coupon Code" autocomplete="off">
              <button type="button" class="btn-checkout-coupon" id="btnCheckoutCoupon">Apply</button>
            </form>
            <div class="coupon-success-pill" id="checkoutCouponSuccess" style="display: none;">
              <span>Code <strong>NEWVAPE10</strong> applied (-$4.50)</span>
              <button type="button" class="btn-remove-coupon" id="btnRemoveCoupon">&times;</button>
            </div>
          </div>

          <hr class="summary-card-divider">

          <!-- Price Calculation Lines -->
          <div class="summary-totals-list">
            <div class="summary-calc-row">
              <span class="calc-label">Subtotal</span>
              <span class="calc-val" id="checkoutSubtotal">$44.98</span>
            </div>
            
            <div class="summary-calc-row" id="checkoutDiscountRow" style="display: none;">
              <span class="calc-label text-success">Discount</span>
              <span class="calc-val text-success" id="checkoutDiscount">-$0.00</span>
            </div>

            <div class="summary-calc-row">
              <span class="calc-label">Shipping (<span id="selectedShippingLabel">Standard</span>)</span>
              <span class="calc-val" id="checkoutShippingFee">$5.99</span>
            </div>

            <div class="summary-calc-row">
              <span class="calc-label">Estimated Tax (4.5%)</span>
              <span class="calc-val" id="checkoutTax">$2.02</span>
            </div>

            <hr class="summary-card-divider">

            <div class="summary-calc-row total-calc-row">
              <div class="total-label-wrap">
                <span class="total-calc-label">Grand Total</span>
                <span class="total-currency-note">USD</span>
              </div>
              <span class="total-calc-val" id="checkoutGrandTotal">$52.99</span>
            </div>
          </div>

          <!-- Customer Service / Guarantee Badges -->
          <div class="checkout-guarantee-box">
            <div class="guarantee-item">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
              </svg>
              <span>Discreet &amp; Secure Packaging</span>
            </div>
            <div class="guarantee-item">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
              </svg>
              <span>100% Authentic Vapor Gear</span>
            </div>
            <div class="guarantee-item">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15.05 5A5 5 0 0 1 19 8.95M15.05 1A9 9 0 0 1 23 8.94m-1 7.98v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
              </svg>
              <span>Support: (405) 555-0199</span>
            </div>
          </div>

        </div>
      </div>

    </div>

  </div>
</section>

<!-- Order Placed Success Modal -->
<div class="order-success-modal-backdrop" id="orderSuccessModal" style="display: none;">
  <div class="order-success-modal-box">
    <div class="success-icon-wrap">
      <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
        <polyline points="22 4 12 14.01 9 11.01"></polyline>
      </svg>
    </div>
    <h2 class="success-title">Thank You For Your Order!</h2>
    <p class="success-order-num">Order Confirmation: <strong id="modalOrderNumber">#INF-<?php echo rand(104820, 989210); ?></strong></p>
    <p class="success-message">A confirmation receipt and tracking number have been sent to your email address. We're packing your order right now!</p>
    
    <div class="success-details-card">
      <div class="success-row">
        <span>Payment Method:</span>
        <strong id="modalPaymentType">Credit Card (Ending in 4242)</strong>
      </div>
      <div class="success-row">
        <span>Estimated Delivery:</span>
        <strong id="modalDeliveryEstimate">3-5 Business Days</strong>
      </div>
      <div class="success-row">
        <span>Total Paid:</span>
        <strong class="text-success" id="modalTotalPaid">$52.99</strong>
      </div>
    </div>

    <div class="success-actions">
      <a href="<?= url() ?>" class="btn-success-home">BACK TO HOME</a>
      <a href="<?= url('category') ?>" class="btn-success-shop">CONTINUE SHOPPING</a>
    </div>
  </div>
</div>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
