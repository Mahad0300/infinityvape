<?php
$page_title = '404 - Page Not Found | Infinity Vape Shop';
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
  <link rel="stylesheet" href="assets/css/style.css?v=<?= file_exists(dirname(dirname(__DIR__)) . '/public/assets/css/style.css') ? filemtime(dirname(dirname(__DIR__)) . '/public/assets/css/style.css') : '1.0.2' ?>">
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
      <div class="error-code">404</div>
      <h1 class="error-title">Page Not Found</h1>
      <p class="error-desc">
        The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
      </p>
      <div class="error-actions">
        <a href="<?= url() ?>" class="btn-error-primary">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
          Back to Homepage
        </a>
        <a href="<?= url('category') ?>" class="btn-error-secondary">
          Explore Products
        </a>
      </div>
    </div>
  </div>

</body>
</html>
