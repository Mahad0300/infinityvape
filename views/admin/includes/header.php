<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($page_title) ? $page_title . ' - Infinity Vape Shop Admin' : 'Infinity Vape Shop Admin Dashboard'; ?></title>
  <base href="<?php echo function_exists('base_url') ? rtrim(base_url('admin'), '/') . '/' : ''; ?>">
  <script>
    window.APP_BASE_URL = "<?php echo function_exists('base_url') ? rtrim(base_url(), '/') : ''; ?>";
  </script>
  
  <!-- Google Fonts: Poppins -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
  
  <!-- Dedicated Admin Stylesheet -->
  <link rel="stylesheet" href="css/admin.css?v=<?= file_exists(dirname(dirname(dirname(__DIR__))) . '/public/admin/css/admin.css') ? filemtime(dirname(dirname(dirname(__DIR__))) . '/public/admin/css/admin.css') : '1.0.1' ?>">
</head>
<body>

  <!-- Admin Page Wrapper -->
  <div class="admin-wrapper">
    
    <!-- Left Navigation Sidebar -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- Main Workspace Container -->
    <div class="admin-main-container">
      
      <!-- Top Navigation Bar -->
      <header class="admin-topbar">
        <div class="topbar-left">
          <!-- Mobile Hamburger Toggle -->
          <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" aria-label="Toggle Sidebar">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="3" y1="12" x2="21" y2="12"></line>
              <line x1="3" y1="6" x2="21" y2="6"></line>
              <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
          </button>

          <!-- Topbar Search Bar -->
          <div class="topbar-search-form">
            <svg class="topbar-search-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" class="topbar-search-input" placeholder="Search orders, products, customers..." autocomplete="off">
          </div>
        </div>

        <div class="topbar-right">
          <?php
            $currentAdmin = class_exists('\App\Core\AdminAuth') ? \App\Core\AdminAuth::user() : null;
            $adminName = !empty($currentAdmin['name']) ? $currentAdmin['name'] : 'Super Admin';
            $adminRole = !empty($currentAdmin['role']) ? ucwords(str_replace('_', ' ', $currentAdmin['role'])) : 'Super Admin';
            $initials = '';
            $parts = explode(' ', trim($adminName));
            foreach ($parts as $p) { if (!empty($p)) $initials .= strtoupper($p[0]); }
            $initials = substr($initials, 0, 2) ?: 'NV';
          ?>
          <!-- Admin Profile Badge -->
          <div class="admin-user-profile" title="Logged in as <?= e($adminName) ?> (<?= e($adminRole) ?>)">
            <div class="admin-avatar"><?= e($initials) ?></div>
            <div class="admin-user-meta">
              <span class="admin-user-name"><?= e($adminName) ?></span>
              <span class="admin-user-role"><?= e($adminRole) ?></span>
            </div>
          </div>
        </div>
      </header>

      <!-- Main Dynamic Content Region -->
      <main class="admin-page-content">
        <?php if ($flashErr = get_flash('error')): ?>
          <div class="admin-alert admin-alert-danger" style="margin-bottom: 20px; padding: 12px 18px; background: #fee2e2; border-left: 4px solid #ef4444; border-radius: 6px; color: #991b1b; font-size: 14px;">
            <?= e($flashErr) ?>
          </div>
        <?php endif; ?>
        <?php if ($flashSucc = get_flash('success')): ?>
          <div class="admin-alert admin-alert-success" style="margin-bottom: 20px; padding: 12px 18px; background: #ecfdf5; border-left: 4px solid #10b981; border-radius: 6px; color: #065f46; font-size: 14px;">
            <?= e($flashSucc) ?>
          </div>
        <?php endif; ?>
