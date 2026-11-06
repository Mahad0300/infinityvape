<?php
/**
 * INFINITY VAPE SHOP - DYNAMIC PRODUCTION CHECKOUT PAGE
 * 100% Connected to MariaDB, Real-Time Stock, Dynamic Shipping & Tax,
 * and Admin Sandbox Test Checkout Mode.
 */
$page_title   = 'Secure Checkout - Infinity Vape Shop';
$current_page = 'checkout';
$page_js      = 'checkout';

include dirname(__DIR__) . '/includes/header.php';

$cartItems = !empty($cart) ? $cart : [];
$subtotal = (float)($totals['subtotal'] ?? 0);
$itemCount = (int)($totals['count'] ?? 0);
$taxRate = $taxRate ?? 4.50;
$standardShipping = $standardShipping ?? 5.99;
$expressShipping = $expressShipping ?? 14.99;
$freeShippingThreshold = $freeShippingThreshold ?? 150.00;
$testCheckoutEnabled = !empty($testCheckoutEnabled);

$isFreeShipping = ($subtotal >= $freeShippingThreshold);
$initialShippingFee = $isFreeShipping ? 0.00 : $standardShipping;
$discountVal = (float)($discountAmount ?? 0);
$taxVal = (float)($taxAmount ?? round(max(0, $subtotal - $discountVal) * ($taxRate / 100), 2));
$totalVal = (float)($grandTotal ?? round(max(0, $subtotal - $discountVal) + $initialShippingFee + $taxVal, 2));

$loggedUser = $currentUser ?? null;
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

    <!-- Test Checkout Sandbox Banner (if enabled by admin) -->
    <?php if ($testCheckoutEnabled): ?>
      <div class="checkout-sandbox-banner" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1.5px solid #f59e0b; border-radius: 10px; padding: 16px 20px; margin-bottom: 24px; display: flex; align-items: center; gap: 14px; box-shadow: 0 2px 10px rgba(245, 158, 11, 0.1);">
        <div style="background-color: #f59e0b; color: #ffffff; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.2rem;">
          ✓
        </div>
        <div>
          <strong style="color: #92400e; font-size: 0.95rem; display: block; margin-bottom: 2px;">
            Sandbox / Test Checkout Mode is Active
          </strong>
          <span style="color: #78350f; font-size: 0.85rem; line-height: 1.4; display: block;">
            You can complete this order without an actual card charge. A real order will be generated in the database, product inventory will be deducted, and an order invoice will be saved.
          </span>
        </div>
      </div>
    <?php endif; ?>

    <?php if (empty($cartItems)): ?>
      <!-- Empty Cart Message -->
      <div class="checkout-empty-wrap" style="background-color: #ffffff; border-radius: 12px; padding: 60px 24px; text-align: center; border: 1px solid #e2e8f0; margin-bottom: 40px;">
        <svg viewBox="0 0 24 24" width="64" height="64" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 16px;">
          <circle cx="9" cy="21" r="1"></circle>
          <circle cx="20" cy="21" r="1"></circle>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
        <h2 style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin-bottom: 8px;">Your Shopping Cart is Empty</h2>
        <p style="color: #64748b; font-size: 0.95rem; max-width: 460px; margin: 0 auto 24px;">You don't have any items in your cart to checkout. Browse our wide selection of authentic vapes and e-liquids.</p>
        <a href="<?= url('collections') ?>" class="btn-primary" style="display: inline-block; background-color: #f25c27; color: #ffffff; padding: 12px 32px; border-radius: 8px; font-weight: 700; text-decoration: none;">Explore Products</a>
      </div>
    <?php else: ?>

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
              <?php if (!$loggedUser): ?>
                <span class="section-login-prompt">Already have an account? <a href="<?= url('my-account') ?>" class="link-highlight">Log in</a></span>
              <?php else: ?>
                <span class="section-login-prompt" style="color: #059669; font-weight: 600;">Logged in as <?= htmlspecialchars($loggedUser['email']) ?></span>
              <?php endif; ?>
            </div>
            
            <div class="checkout-section-body">
              <div class="form-group">
                <label for="checkoutEmail" class="form-label">Email Address <span class="required-star">*</span></label>
                <input type="email" id="checkoutEmail" name="email" class="form-input" placeholder="you@example.com" value="<?= htmlspecialchars($loggedUser['email'] ?? '') ?>" required autocomplete="email">
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
                  <input type="text" id="shippingFirstName" name="firstName" class="form-input" placeholder="First Name" value="<?= htmlspecialchars($loggedUser['first_name'] ?? '') ?>" required autocomplete="given-name">
                  <span class="field-error-msg">Please enter your first name.</span>
                </div>
                <div class="form-group">
                  <label for="shippingLastName" class="form-label">Last Name <span class="required-star">*</span></label>
                  <input type="text" id="shippingLastName" name="lastName" class="form-input" placeholder="Last Name" value="<?= htmlspecialchars($loggedUser['last_name'] ?? '') ?>" required autocomplete="family-name">
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
                <input type="tel" id="shippingPhone" name="phone" class="form-input" placeholder="(405) 555-0199" value="<?= htmlspecialchars($loggedUser['phone'] ?? '') ?>" required autocomplete="tel">
                <span class="field-help-text">Used for shipping updates and adult signature delivery notifications.</span>
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
                  <input type="radio" name="shippingMethod" id="shipMethodStandard" value="<?= $isFreeShipping ? '0.00' : number_format($standardShipping, 2, '.', '') ?>" data-name="Standard Ground Shipping" checked>
                  <div class="shipping-method-details">
                    <span class="shipping-method-name">Standard Ground Shipping <?= $isFreeShipping ? '(Free Threshold Met)' : '' ?></span>
                    <span class="shipping-method-estimate">3 - 5 Business Days • Tracking &amp; Adult Signature Included</span>
                  </div>
                  <span class="shipping-method-price <?= $isFreeShipping ? 'text-success' : '' ?>">
                    <?= $isFreeShipping ? 'FREE' : '$' . number_format($standardShipping, 2) ?>
                  </span>
                </label>

                <!-- Express Shipping -->
                <label class="shipping-method-option" for="shipMethodExpress">
                  <input type="radio" name="shippingMethod" id="shipMethodExpress" value="<?= number_format($expressShipping, 2, '.', '') ?>" data-name="Express Priority Delivery">
                  <div class="shipping-method-details">
                    <span class="shipping-method-name">Express Priority Delivery</span>
                    <span class="shipping-method-estimate">1 - 2 Business Days • Fast Priority Dispatch</span>
                  </div>
                  <span class="shipping-method-price">$<?= number_format($expressShipping, 2) ?></span>
                </label>

                <!-- In Store Pickup -->
                <label class="shipping-method-option" for="shipMethodPickup">
                  <input type="radio" name="shippingMethod" id="shipMethodPickup" value="0.00" data-name="In-Store Pickup">
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
                    <span class="payment-tab-title">
                      <?= $testCheckoutEnabled ? 'Credit / Debit Card (Sandbox Test Mode)' : 'Credit / Debit Card' ?>
                    </span>
                    <div class="payment-card-logos">
                      <img src="assets/images/visa.webp" alt="Visa" loading="lazy">
                      <img src="assets/images/mastercard.webp" alt="Mastercard" loading="lazy">
                      <img src="assets/images/american-express.webp" alt="Amex" loading="lazy">
                      <img src="assets/images/discover.webp" alt="Discover" loading="lazy">
                    </div>
                  </label>

                  <!-- Card Details Fields -->
                  <div class="payment-tab-content" id="contentCreditCard">
                    <?php if ($testCheckoutEnabled): ?>
                      <p style="font-size: 0.825rem; color: #059669; background: #ecfdf5; padding: 8px 12px; border-radius: 6px; margin-bottom: 12px; font-weight: 600;">
                        ✓ Test Sandbox is active. Any 16-digit card or test number will be approved without charging.
                      </p>
                    <?php endif; ?>
                    <div class="form-group">
                      <label for="ccNumber" class="form-label">Card Number <span class="required-star">*</span></label>
                      <div class="input-with-icon">
                        <input type="text" id="ccNumber" name="cardNumber" class="form-input" placeholder="4242 •••• •••• 4242" value="<?= $testCheckoutEnabled ? '4242 4242 4242 4242' : '' ?>" maxlength="19" autocomplete="cc-number">
                        <svg class="input-icon-right" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#888" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                          <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                      </div>
                      <span class="field-error-msg">Please enter a valid card number.</span>
                    </div>

                    <div class="form-group">
                      <label for="ccName" class="form-label">Name on Card <span class="required-star">*</span></label>
                      <input type="text" id="ccName" name="cardName" class="form-input" placeholder="Cardholder Name" value="<?= $testCheckoutEnabled ? 'Test Shopper' : '' ?>" autocomplete="cc-name">
                      <span class="field-error-msg">Please enter the name on your card.</span>
                    </div>

                    <div class="form-row form-row-2">
                      <div class="form-group">
                        <label for="ccExpiry" class="form-label">Expiration Date <span class="required-star">*</span></label>
                        <input type="text" id="ccExpiry" name="cardExpiry" class="form-input" placeholder="MM / YY" value="<?= $testCheckoutEnabled ? '12/28' : '' ?>" maxlength="7" autocomplete="cc-exp">
                        <span class="field-error-msg">Valid MM/YY required.</span>
                      </div>
                      <div class="form-group">
                        <label for="ccCvc" class="form-label">Security Code (CVV) <span class="required-star">*</span></label>
                        <div class="input-with-icon">
                          <input type="password" id="ccCvc" name="cardCvc" class="form-input" placeholder="CVC" value="<?= $testCheckoutEnabled ? '123' : '' ?>" maxlength="4" autocomplete="cc-csc">
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

                <!-- Apple Pay Option -->
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

              <!-- Age Verification & Legal Agreement -->
              <div class="checkout-age-agreement" style="margin-top: 24px;">
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
                  <span class="btn-order-text"><?= $testCheckoutEnabled ? 'PLACE TEST ORDER' : 'PLACE ORDER' ?></span>
                  <span class="btn-order-price" id="btnOrderPriceTotal">$<?= number_format($totalVal, 2) ?></span>
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
            <span class="summary-item-count" id="summaryItemsCount">(<?= $itemCount ?> <?= $itemCount === 1 ? 'item' : 'items' ?>)</span>
          </div>

          <!-- Dynamic Products List from Database / Session -->
          <div class="checkout-items-list" id="checkoutItemsList">
            <?php foreach ($cartItems as $cItem): 
              $pPrice = (float)($cItem['price'] ?? 0);
              $pQty = (int)($cItem['quantity'] ?? 1);
              $lineTotal = '$' . number_format($pPrice * $pQty, 2);
              $pImg = !empty($cItem['image']) ? $cItem['image'] : asset('assets/images/vape-image.webp');
              $pTitle = $cItem['title'] ?? 'Product';
              $pFlavor = $cItem['variant_name'] ?? ($cItem['flavor'] ?? '');
            ?>
            <div class="checkout-item-row" data-price="<?= number_format($pPrice, 2, '.', '') ?>">
              <div class="checkout-item-thumb">
                <img src="<?= htmlspecialchars($pImg) ?>" alt="<?= htmlspecialchars($pTitle) ?>" onerror="this.onerror=null;this.src='<?= asset('assets/images/vape-image.webp') ?>';" loading="lazy">
                <span class="checkout-item-qty-badge"><?= $pQty ?></span>
              </div>
              <div class="checkout-item-info">
                <h4 class="checkout-item-title"><?= htmlspecialchars($pTitle) ?></h4>
                <?php if ($pFlavor): ?>
                  <div class="checkout-item-meta"><?= htmlspecialchars($pFlavor) ?></div>
                <?php endif; ?>
              </div>
              <div class="checkout-item-price"><?= $lineTotal ?></div>
            </div>
            <?php endforeach; ?>
          </div>

          <!-- Coupon Code Input -->
          <div class="checkout-promo-box">
            <form class="checkout-coupon-form" id="checkoutCouponForm" onsubmit="return false;">
              <input type="text" class="checkout-coupon-input" id="checkoutCouponInput" placeholder="Promo / Coupon Code (e.g. VAPE10)" autocomplete="off">
              <button type="button" class="btn-checkout-coupon" id="btnCheckoutCoupon">Apply</button>
            </form>
            <div class="coupon-success-pill" id="checkoutCouponSuccess" style="<?= $coupon ? 'display: flex;' : 'display: none;' ?>">
              <span id="couponSuccessText">Code <strong><?= htmlspecialchars($coupon['code'] ?? '') ?></strong> applied (-$<?= number_format($discountVal, 2) ?>)</span>
              <button type="button" class="btn-remove-coupon" id="btnRemoveCoupon">&times;</button>
            </div>
          </div>

          <hr class="summary-card-divider">

          <!-- Price Calculation Lines -->
          <div class="summary-totals-list" 
               data-subtotal="<?= number_format($subtotal, 2, '.', '') ?>"
               data-tax-rate="<?= number_format($taxRate, 2, '.', '') ?>"
               data-free-threshold="<?= number_format($freeShippingThreshold, 2, '.', '') ?>">
            
            <div class="summary-calc-row">
              <span class="calc-label">Subtotal</span>
              <span class="calc-val" id="checkoutSubtotal">$<?= number_format($subtotal, 2) ?></span>
            </div>
            
            <div class="summary-calc-row" id="checkoutDiscountRow" style="<?= $discountVal > 0 ? 'display: flex;' : 'display: none;' ?>">
              <span class="calc-label text-success">Discount</span>
              <span class="calc-val text-success" id="checkoutDiscount">-$<?= number_format($discountVal, 2) ?></span>
            </div>

            <div class="summary-calc-row">
              <span class="calc-label">Shipping (<span id="selectedShippingLabel"><?= $isFreeShipping ? 'Free Ground' : 'Standard Ground' ?></span>)</span>
              <span class="calc-val <?= $initialShippingFee == 0 ? 'text-success' : '' ?>" id="checkoutShippingFee">
                <?= $initialShippingFee == 0 ? 'FREE' : '$' . number_format($initialShippingFee, 2) ?>
              </span>
            </div>

            <div class="summary-calc-row">
              <span class="calc-label">Estimated Tax (<?= number_format($taxRate, 1) ?>%)</span>
              <span class="calc-val" id="checkoutTax">$<?= number_format($taxVal, 2) ?></span>
            </div>

            <hr class="summary-card-divider">

            <div class="summary-calc-row total-calc-row">
              <div class="total-label-wrap">
                <span class="total-calc-label">Grand Total</span>
                <span class="total-currency-note">USD</span>
              </div>
              <span class="total-calc-val" id="checkoutGrandTotal">$<?= number_format($totalVal, 2) ?></span>
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
              <span>Support: <?= htmlspecialchars($settings['site_phone'] ?? '(800) 555-VAPE') ?></span>
            </div>
          </div>

        </div>
      </div>

    </div>
    <?php endif; ?>

  </div>
</section>

<!-- Order Placed Success Confirmation Modal -->
<div class="order-success-modal-backdrop" id="orderSuccessModal" style="display: none;">
  <div class="order-success-modal-box">
    <div class="success-icon-wrap">
      <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
        <polyline points="22 4 12 14.01 9 11.01"></polyline>
      </svg>
    </div>
    <h2 class="success-title">Thank You For Your Order!</h2>
    <p class="success-order-num">Order Confirmation: <strong id="modalOrderNumber">#INF-104825</strong></p>
    <p class="success-message">A confirmation receipt and order details have been saved in our database. We are packing your order right now!</p>
    
    <div class="success-details-card">
      <div class="success-row">
        <span>Payment Method:</span>
        <strong id="modalPaymentType">Credit Card (Sandbox / Direct Test)</strong>
      </div>
      <div class="success-row">
        <span>Delivery Method:</span>
        <strong id="modalDeliveryEstimate">Standard Ground Shipping</strong>
      </div>
      <div class="success-row">
        <span>Total Paid:</span>
        <strong class="text-success" id="modalTotalPaid">$0.00</strong>
      </div>
    </div>

    <div class="success-actions">
      <a href="<?= url() ?>" class="btn-success-home">BACK TO HOME</a>
      <a href="<?= url('collections') ?>" class="btn-success-shop">CONTINUE SHOPPING</a>
    </div>
  </div>
</div>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
