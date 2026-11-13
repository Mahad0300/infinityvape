<?php
$nav_active = isset($current_admin_page) ? $current_admin_page : 'dashboard';
?>

<!-- Mobile Drawer Backdrop -->
<div class="admin-drawer-backdrop" id="adminDrawerBackdrop"></div>

<!-- Left Sidebar Navigation -->
<aside class="admin-sidebar" id="adminSidebar">
  <!-- Sidebar Brand Header -->
  <div class="sidebar-brand">
    <a href="<?= url('admin') ?>" class="sidebar-logo" title="Infinity Vape Shop Admin">
      <img src="<?= asset('infinityvapeshoplogo.jpg') ?>" alt="Infinity Vape Shop Logo" onerror="this.onerror=null;this.src='<?= asset('assets/images/infinityvapeshoplogo.jpg') ?>';">
    </a>
    <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Close Sidebar">
      <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="6" x2="6" y2="18"></line>
        <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
    </button>
  </div>

  <!-- Sidebar Nav Menu Items -->
  <div class="sidebar-content">
    
    <div class="sidebar-section-title">Main</div>
    <ul class="sidebar-nav">
      <li class="sidebar-nav-item">
        <a href="<?= url('admin') ?>" class="sidebar-nav-link <?php echo ($nav_active === 'dashboard') ? 'active' : ''; ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7"></rect>
            <rect x="14" y="3" width="7" height="7"></rect>
            <rect x="14" y="14" width="7" height="7"></rect>
            <rect x="3" y="14" width="7" height="7"></rect>
          </svg>
          <span>Dashboard</span>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="<?= url('admin/analytics') ?>" class="sidebar-nav-link <?php echo ($nav_active === 'analytics') ? 'active' : ''; ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="20" x2="18" y2="10"></line>
            <line x1="12" y1="20" x2="12" y2="4"></line>
            <line x1="6" y1="20" x2="6" y2="14"></line>
          </svg>
          <span>Analytics</span>
        </a>
      </li>
    </ul>

    <div class="sidebar-section-title">Catalog Management</div>
    <ul class="sidebar-nav">
      <li class="sidebar-nav-item">
        <a href="<?= url('admin/collections') ?>" class="sidebar-nav-link <?php echo ($nav_active === 'collections' || $nav_active === 'add-collection' || $nav_active === 'edit-collection') ? 'active' : ''; ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <line x1="8" y1="6" x2="21" y2="6"></line>
            <line x1="8" y1="12" x2="21" y2="12"></line>
            <line x1="8" y1="18" x2="21" y2="18"></line>
            <line x1="3" y1="6" x2="3.01" y2="6"></line>
            <line x1="3" y1="12" x2="3.01" y2="12"></line>
            <line x1="3" y1="18" x2="3.01" y2="18"></line>
          </svg>
          <span>Collections</span>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="<?= url('admin/brands') ?>" class="sidebar-nav-link <?php echo ($nav_active === 'brands' || $nav_active === 'add-brand' || $nav_active === 'edit-brand') ? 'active' : ''; ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
            <line x1="7" y1="7" x2="7.01" y2="7"></line>
          </svg>
          <span>Brands</span>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="<?= url('admin/products') ?>" class="sidebar-nav-link <?php echo ($nav_active === 'products' || $nav_active === 'add-product' || $nav_active === 'edit-product') ? 'active' : ''; ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
            <line x1="12" y1="22.08" x2="12" y2="12"></line>
          </svg>
          <span>Products</span>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="<?= url('admin/inventory') ?>" class="sidebar-nav-link <?php echo ($nav_active === 'inventory') ? 'active' : ''; ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline>
            <path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path>
            <line x1="6" y1="16" x2="6.01" y2="16"></line>
            <line x1="10" y1="16" x2="10.01" y2="16"></line>
          </svg>
          <span>Inventory</span>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="<?= url('admin/reviews') ?>" class="sidebar-nav-link <?php echo ($nav_active === 'reviews') ? 'active' : ''; ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
          </svg>
          <span>Reviews</span>
        </a>
      </li>
    </ul>

    <div class="sidebar-section-title">Sales &amp; Customers</div>
    <ul class="sidebar-nav">
      <li class="sidebar-nav-item">
        <a href="<?= url('admin/orders') ?>" class="sidebar-nav-link <?php echo ($nav_active === 'orders' || $nav_active === 'order-detail') ? 'active' : ''; ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
            <polyline points="10 9 9 9 8 9"></polyline>
          </svg>
          <span>Orders</span>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="<?= url('admin/customers') ?>" class="sidebar-nav-link <?php echo ($nav_active === 'customers' || $nav_active === 'customer-detail') ? 'active' : ''; ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>
          <span>Customers</span>
        </a>
      </li>
    </ul>

    <div class="sidebar-section-title">Marketing</div>
    <ul class="sidebar-nav">
      <li class="sidebar-nav-item">
        <a href="<?= url('admin/coupons') ?>" class="sidebar-nav-link <?php echo ($nav_active === 'coupons') ? 'active' : ''; ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
            <line x1="7" y1="7" x2="7.01" y2="7"></line>
          </svg>
          <span>Coupons &amp; Discounts</span>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="<?= url('admin/blogs') ?>" class="sidebar-nav-link <?php echo ($nav_active === 'blogs' || $nav_active === 'add-blog' || $nav_active === 'edit-blog') ? 'active' : ''; ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
            <polyline points="10 9 9 9 8 9"></polyline>
          </svg>
          <span>Blogs</span>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="<?= url('admin/blog-categories') ?>" class="sidebar-nav-link <?php echo ($nav_active === 'blog-categories') ? 'active' : ''; ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="4" y1="9" x2="20" y2="9"></line>
            <line x1="4" y1="15" x2="20" y2="15"></line>
            <line x1="10" y1="3" x2="8" y2="21"></line>
            <line x1="16" y1="3" x2="14" y2="21"></line>
          </svg>
          <span>Blog Categories</span>
        </a>
      </li>
    </ul>

    <div class="sidebar-section-title">Website</div>
    <ul class="sidebar-nav">
      <li class="sidebar-nav-item">
        <a href="<?= url('admin/navigation') ?>" class="sidebar-nav-link <?php echo ($nav_active === 'navigation') ? 'active' : ''; ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
          </svg>
          <span>Navigation</span>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="<?= url('admin/theme-sections') ?>" class="sidebar-nav-link <?php echo ($nav_active === 'theme-sections') ? 'active' : ''; ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
            <polyline points="2 17 12 22 22 17"></polyline>
            <polyline points="2 12 12 17 22 12"></polyline>
          </svg>
          <span>Theme Sections</span>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="<?= url('admin/settings') ?>" class="sidebar-nav-link <?php echo ($nav_active === 'settings') ? 'active' : ''; ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="3"></circle>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
          </svg>
          <span>Settings</span>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="<?= url() ?>" target="_blank" class="sidebar-nav-link" title="Open Storefront in New Tab">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
            <polyline points="15 3 21 3 21 9"></polyline>
            <line x1="10" y1="14" x2="21" y2="3"></line>
          </svg>
          <span>Live Store</span>
        </a>
      </li>
    </ul>

  </div>

  <!-- Sidebar Bottom Section (Logout) -->
  <div class="sidebar-bottom">
    <ul class="sidebar-nav">
      <li class="sidebar-nav-item">
        <a href="<?= url('admin/logout') ?>" class="sidebar-nav-link sidebar-logout-btn" title="Logout from Admin">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
          </svg>
          <span>Logout</span>
        </a>
      </li>
    </ul>
  </div>
</aside>
