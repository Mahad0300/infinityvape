<?php
/**
 * INFINITY VAPE SHOP - MY ACCOUNT PAGE (my-account.php)
 * High-converting 2-column Login & Registration page with real database authentication.
 */
$page_title   = 'My Account | Login & Register | Infinity Vape Shop';
$current_page = 'my-account';
$page_js      = 'account';

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
      <li class="breadcrumb-item active" aria-current="page">My Account</li>
    </ol>
  </div>
</nav>

<!-- Main Account Page Content -->
<section class="account-page-main">
  <div class="container">
    
    <!-- Flash Messages (if any) -->
    <?php if (!empty($success)): ?>
      <div class="account-flash-alert success">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div class="account-flash-alert error">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <div class="account-forms-grid">
      
      <!-- Login Column -->
      <div class="account-form-column" id="loginColumn">
        <h1 class="account-box-title">Login</h1>
        <div class="account-form-card">
          <form class="account-form" id="loginForm" method="POST" action="<?= url('account/login') ?>" novalidate>
            <?= csrf_field() ?>
            
            <div class="account-form-group">
              <label for="loginUsername" class="account-label">Email address <span class="required-star">*</span></label>
              <input type="email" id="loginUsername" name="email" class="account-input" placeholder="you@example.com" required autocomplete="username">
              <span class="field-error-msg" id="errLoginUsername">Please enter your email address.</span>
            </div>

            <div class="account-form-group">
              <label for="loginPassword" class="account-label">Password <span class="required-star">*</span></label>
              <div class="password-input-wrap">
                <input type="password" id="loginPassword" name="password" class="account-input" placeholder="••••••••" required autocomplete="current-password">
                <button type="button" class="btn-password-toggle" aria-label="Toggle password visibility" onclick="togglePasswordVisibility('loginPassword', this)">
                  <svg class="eye-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                  </svg>
                </button>
              </div>
              <span class="field-error-msg" id="errLoginPassword">Please enter your password.</span>
            </div>

            <div class="account-remember-wrap">
              <input type="checkbox" id="rememberMe" name="rememberme" class="account-checkbox" checked>
              <label for="rememberMe" class="remember-label">Remember me</label>
            </div>

            <div class="account-btn-row">
              <button type="submit" class="btn-account-submit" id="btnLoginSubmit">Log in</button>
            </div>

            <div class="account-lost-password">
              <a href="#" class="lost-password-link" id="linkLostPassword">Lost your password?</a>
            </div>

            <div class="account-status-msg" id="loginStatusMsg" style="display: none;"></div>

          </form>
        </div>
      </div>

      <!-- Register Column -->
      <div class="account-form-column" id="registerColumn">
        <h2 class="account-box-title">Register</h2>
        <div class="account-form-card">
          <form class="account-form" id="registerForm" method="POST" action="<?= url('account/register') ?>" novalidate>
            <?= csrf_field() ?>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
              <div class="account-form-group">
                <label for="regFirstName" class="account-label">First Name <span class="required-star">*</span></label>
                <input type="text" id="regFirstName" name="first_name" class="account-input" placeholder="First Name" required autocomplete="given-name">
                <span class="field-error-msg" id="errRegFirstName">First name is required.</span>
              </div>
              <div class="account-form-group">
                <label for="regLastName" class="account-label">Last Name <span class="required-star">*</span></label>
                <input type="text" id="regLastName" name="last_name" class="account-input" placeholder="Last Name" required autocomplete="family-name">
                <span class="field-error-msg" id="errRegLastName">Last name is required.</span>
              </div>
            </div>

            <div class="account-form-group">
              <label for="regEmail" class="account-label">Email address <span class="required-star">*</span></label>
              <input type="email" id="regEmail" name="email" class="account-input" placeholder="you@example.com" required autocomplete="email">
              <span class="field-error-msg" id="errRegEmail">Please enter a valid email address.</span>
            </div>

            <div class="account-form-group">
              <label for="regPhone" class="account-label">Phone Number (Optional)</label>
              <input type="tel" id="regPhone" name="phone" class="account-input" placeholder="+1 (555) 000-0000" autocomplete="tel">
            </div>

            <div class="account-form-group">
              <label for="regPassword" class="account-label">Password <span class="required-star">*</span></label>
              <div class="password-input-wrap">
                <input type="password" id="regPassword" name="password" class="account-input" placeholder="Create strong password (min 6 characters)" required autocomplete="new-password">
                <button type="button" class="btn-password-toggle" aria-label="Toggle password visibility" onclick="togglePasswordVisibility('regPassword', this)">
                  <svg class="eye-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                  </svg>
                </button>
              </div>
              <span class="field-error-msg" id="errRegPassword">Password must be at least 6 characters.</span>
            </div>

            <p class="account-privacy-notice">
              Your personal data will be used to support your shopping experience, manage access to your account, and for orders as described in our <a href="<?= url('page/privacy-policy') ?>" target="_blank" style="color: #fa5a1e; text-decoration: underline;">privacy policy</a> and <a href="<?= url('page/terms-and-conditions') ?>" target="_blank" style="color: #fa5a1e; text-decoration: underline;">terms &amp; conditions</a>.
            </p>

            <div class="account-btn-row">
              <button type="submit" class="btn-account-submit" id="btnRegisterSubmit">Create Account</button>
            </div>

            <div class="account-status-msg" id="regStatusMsg" style="display: none;"></div>

          </form>
        </div>
      </div>

    </div>

  </div>
</section>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
