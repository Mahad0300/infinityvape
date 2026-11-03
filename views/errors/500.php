<?php
$page_title = '500 - Server Error | Infinity Vape Shop';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($page_title); ?></title>
  <base href="<?php echo function_exists('base_url') ? base_url('/') . '/' : ''; ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .error-page-wrap {
      min-height: 80vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 60px 20px;
    }
    .error-card {
      max-width: 560px;
      margin: 0 auto;
    }
    .error-code {
      font-family: 'Outfit', sans-serif;
      font-size: 110px;
      font-weight: 900;
      line-height: 1;
      color: #ef4444;
      letter-spacing: -2px;
      margin-bottom: 12px;
    }
    .error-title {
      font-family: 'Outfit', sans-serif;
      font-size: 28px;
      font-weight: 700;
      color: #1e293b;
      margin-bottom: 14px;
    }
    .error-desc {
      color: #64748b;
      font-size: 16px;
      line-height: 1.6;
      margin-bottom: 30px;
    }
    .btn-error-primary {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: #0f172a;
      color: #fff;
      padding: 13px 28px;
      border-radius: 999px;
      font-weight: 600;
      text-decoration: none;
      transition: background 0.2s ease, transform 0.2s ease;
    }
    .btn-error-primary:hover {
      background: #334155;
      transform: translateY(-1px);
      color: #fff;
    }
  </style>
</head>
<body>

  <div class="nicotine-warning-bar">
    <div class="container">
      <p class="nicotine-warning-text">
        <strong>WARNING:</strong> This product contains nicotine. Nicotine is an addictive chemical.
      </p>
    </div>
  </div>

  <div class="error-page-wrap">
    <div class="error-card">
      <div class="error-code">500</div>
      <h1 class="error-title">Internal Server Error</h1>
      <p class="error-desc">
        Something went wrong on our end. Please try refreshing or visit again later.
      </p>
      <div class="error-actions">
        <a href="<?= url() ?>" class="btn-error-primary">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
          Back to Homepage
        </a>
      </div>
    </div>
  </div>

</body>
</html>
