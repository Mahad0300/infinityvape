<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($page_title ?? 'Admin Login - Infinity Vape Shop') ?></title>
  
  <!-- Google Fonts: Poppins -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --primary-navy: #082142;
      --primary-navy-dark: #05162e;
      --primary-navy-light: #0d3161;
      --accent-orange: #f25c27;
      --accent-orange-hover: #db4915;
      --text-main: #0f172a;
      --text-muted: #64748b;
      --border-color: #e2e8f0;
      --card-bg: #ffffff;
      --body-bg: #f8fafc;
      --radius: 12px;
      --shadow-lg: 0 20px 25px -5px rgba(8, 33, 66, 0.1), 0 10px 10px -5px rgba(8, 33, 66, 0.04);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
      background: radial-gradient(circle at 10% 20%, rgba(8, 33, 66, 0.04) 0%, rgba(242, 92, 39, 0.04) 90%), #f8fafc;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
      color: var(--text-main);
    }

    .login-container {
      width: 100%;
      max-width: 440px;
    }

    .login-header-branding {
      text-align: center;
      margin-bottom: 28px;
    }

    .brand-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: #ffffff;
      padding: 8px 18px;
      border-radius: 9999px;
      box-shadow: 0 2px 8px rgba(8, 33, 66, 0.06);
      border: 1px solid var(--border-color);
      margin-bottom: 16px;
    }

    .brand-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: var(--accent-orange);
      box-shadow: 0 0 10px rgba(242, 92, 39, 0.5);
    }

    .brand-name {
      font-size: 13px;
      font-weight: 700;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: var(--primary-navy);
    }

    .portal-title {
      font-size: 26px;
      font-weight: 700;
      color: var(--primary-navy);
      margin-bottom: 6px;
      letter-spacing: -0.5px;
    }

    .portal-subtitle {
      font-size: 14px;
      color: var(--text-muted);
    }

    .login-card {
      background: var(--card-bg);
      border-radius: var(--radius);
      padding: 36px 32px;
      box-shadow: var(--shadow-lg);
      border: 1px solid var(--border-color);
      position: relative;
    }

    .login-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--primary-navy) 0%, var(--accent-orange) 100%);
      border-top-left-radius: var(--radius);
      border-top-right-radius: var(--radius);
    }

    .alert {
      padding: 12px 16px;
      border-radius: 8px;
      font-size: 13.5px;
      margin-bottom: 22px;
      display: flex;
      align-items: center;
      gap: 10px;
      line-height: 1.4;
    }

    .alert-error {
      background: #fee2e2;
      border: 1px solid #fecaca;
      color: #991b1b;
    }

    .alert-success {
      background: #dcfce7;
      border: 1px solid #bbf7d0;
      color: #166534;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: var(--primary-navy);
      margin-bottom: 8px;
    }

    .input-wrapper {
      position: relative;
    }

    .input-icon {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
      pointer-events: none;
      display: flex;
      align-items: center;
    }

    .form-control {
      width: 100%;
      height: 46px;
      padding: 10px 14px 10px 44px;
      font-family: inherit;
      font-size: 14px;
      color: var(--text-main);
      background: #ffffff;
      border: 1.5px solid var(--border-color);
      border-radius: 8px;
      transition: all 0.2s ease;
      outline: none;
    }

    .form-control:focus {
      border-color: var(--primary-navy);
      box-shadow: 0 0 0 4px rgba(8, 33, 66, 0.08);
    }

    .toggle-pwd-btn {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: var(--text-muted);
      cursor: pointer;
      padding: 4px;
      display: flex;
      align-items: center;
      transition: color 0.2s;
    }

    .toggle-pwd-btn:hover {
      color: var(--primary-navy);
    }

    .form-meta-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 24px;
      font-size: 13px;
    }

    .checkbox-label {
      display: flex;
      align-items: center;
      gap: 8px;
      color: var(--text-muted);
      cursor: pointer;
      user-select: none;
    }

    .checkbox-label input[type="checkbox"] {
      width: 16px;
      height: 16px;
      accent-color: var(--primary-navy);
      cursor: pointer;
    }

    .btn-submit {
      width: 100%;
      height: 48px;
      background: var(--primary-navy);
      color: #ffffff;
      border: none;
      border-radius: 8px;
      font-family: inherit;
      font-size: 15px;
      font-weight: 600;
      letter-spacing: 0.3px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      transition: all 0.2s ease;
      box-shadow: 0 4px 12px rgba(8, 33, 66, 0.2);
    }

    .btn-submit:hover {
      background: var(--accent-orange);
      box-shadow: 0 6px 16px rgba(242, 92, 39, 0.3);
      transform: translateY(-1px);
    }

    .btn-submit:active {
      transform: translateY(0);
    }

    .demo-credentials-card {
      margin-top: 24px;
      padding: 14px 16px;
      background: #f1f5f9;
      border: 1px dashed #cbd5e1;
      border-radius: 8px;
      font-size: 12.5px;
      color: #334155;
    }

    .demo-title {
      font-weight: 600;
      color: var(--primary-navy);
      margin-bottom: 4px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .demo-creds-row {
      display: flex;
      gap: 16px;
      margin-top: 4px;
    }

    .demo-code {
      background: #ffffff;
      padding: 2px 6px;
      border-radius: 4px;
      font-family: monospace;
      font-size: 12px;
      border: 1px solid #e2e8f0;
      color: var(--primary-navy);
    }

    .login-footer-links {
      text-align: center;
      margin-top: 24px;
    }

    .back-to-store-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 13.5px;
      color: var(--text-muted);
      text-decoration: none;
      transition: color 0.2s ease;
    }

    .back-to-store-link:hover {
      color: var(--accent-orange);
    }

    .security-notice {
      text-align: center;
      margin-top: 18px;
      font-size: 11.5px;
      color: #94a3b8;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
    }
  </style>
</head>
<body>

  <div class="login-container">
    
    <!-- Branding Header -->
    <div class="login-header-branding">
      <div class="brand-badge">
        <span class="brand-dot"></span>
        <span class="brand-name">Infinity Vape Shop Admin</span>
      </div>
      <h1 class="portal-title">Store Management</h1>
      <p class="portal-subtitle">Secure administrative staff authentication</p>
    </div>

    <!-- Login Box Card -->
    <div class="login-card">
      
      <!-- Flash Notifications -->
      <?php if (!empty($error)): ?>
      <div class="alert alert-error" role="alert">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="8" x2="12" y2="12"></line>
          <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <span><?= e($error) ?></span>
      </div>
      <?php endif; ?>

      <?php if (!empty($success)): ?>
      <div class="alert alert-success" role="alert">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
          <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <span><?= e($success) ?></span>
      </div>
      <?php endif; ?>

      <form action="<?= url('admin/login') ?>" method="POST" id="adminLoginForm">
        <?= csrf_field() ?>

        <!-- Email Field -->
        <div class="form-group">
          <label for="adminEmail" class="form-label">Email Address</label>
          <div class="input-wrapper">
            <span class="input-icon">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
              </svg>
            </span>
            <input 
              type="email" 
              name="email" 
              id="adminEmail" 
              class="form-control" 
              placeholder="admin@yourdomain.com" 
              value="<?= e($_POST['email'] ?? '') ?>" 
              required 
              autofocus 
              autocomplete="email"
            >
          </div>
        </div>

        <!-- Password Field -->
        <div class="form-group">
          <label for="adminPassword" class="form-label">Password</label>
          <div class="input-wrapper">
            <span class="input-icon">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
            </span>
            <input 
              type="password" 
              name="password" 
              id="adminPassword" 
              class="form-control" 
              placeholder="••••••••" 
              required 
              autocomplete="current-password"
            >
            <button type="button" class="toggle-pwd-btn" id="togglePwdBtn" aria-label="Toggle password visibility">
              <svg id="eyeIcon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
            </button>
          </div>
        </div>

        <!-- Meta Row -->
        <div class="form-meta-row">
          <label class="checkbox-label">
            <input type="checkbox" name="remember" value="1" checked>
            <span>Remember session</span>
          </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-submit" id="btnSubmit">
          <span>Sign In to Dashboard</span>
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </button>



      </form>
    </div>

    <!-- Return to Storefront -->
    <div class="login-footer-links">
      <a href="<?= url('') ?>" class="back-to-store-link">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="19" y1="12" x2="5" y2="12"></line>
          <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        <span>Return to Storefront</span>
      </a>
    </div>

    <!-- Security Footnote -->
    <div class="security-notice">
      <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
      </svg>
      <span>256-Bit SSL Encrypted Admin Portal • Authorized Staff Only</span>
    </div>

  </div>

  <script>
    // Show/Hide password toggle logic
    const pwdInput = document.getElementById('adminPassword');
    const toggleBtn = document.getElementById('togglePwdBtn');
    if (pwdInput && toggleBtn) {
      toggleBtn.addEventListener('click', function() {
        const isPassword = pwdInput.type === 'password';
        pwdInput.type = isPassword ? 'text' : 'password';
        this.innerHTML = isPassword 
          ? '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>'
          : '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
      });
    }
  </script>
</body>
</html>
