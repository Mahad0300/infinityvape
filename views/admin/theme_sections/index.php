<?php
$page_title = 'Theme Sections | Vape Store Admin';
$current_admin_page = 'theme-sections';
include dirname(__DIR__) . '/includes/header.php';

$activeTab = $activeTab ?? 'hero';
$seoContent = $seoContent ?? \App\Models\ThemeSection::getSeoContent();
?>

<!-- Custom CSS for Modern Theme Sections Hub -->
<style>
.theme-hub-container {
  display: grid;
  grid-template-columns: 310px 1fr;
  gap: 24px;
  align-items: start;
  margin-top: 10px;
}

@media (max-width: 1024px) {
  .theme-hub-container {
    grid-template-columns: 1fr;
  }
}

/* Left Sections Navigation Menu */
.theme-nav-panel {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
  position: sticky;
  top: 90px;
}

.theme-nav-header {
  padding: 18px 20px;
  border-bottom: 1px solid #f1f5f9;
  background: #fafafa;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.theme-nav-title {
  font-size: 15px;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.theme-nav-count-badge {
  background: #f1f5f9;
  color: #64748b;
  font-size: 11px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 999px;
  border: 1px solid #e2e8f0;
}

.theme-nav-search-wrap {
  padding: 12px 16px;
  border-bottom: 1px solid #f1f5f9;
}

.theme-nav-search {
  width: 100%;
  padding: 8px 12px 8px 32px;
  font-size: 13px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  outline: none;
  background: #ffffff;
  position: relative;
  transition: border-color 0.2s ease;
}

.theme-nav-search:focus {
  border-color: #f97316;
  box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}

.theme-nav-list {
  list-style: none;
  padding: 8px 0;
  margin: 0;
}

.theme-nav-item {
  margin: 2px 8px;
}

.theme-nav-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 14px;
  border-radius: 10px;
  text-decoration: none;
  color: #475569;
  font-weight: 600;
  font-size: 13.5px;
  transition: all 0.2s ease;
  position: relative;
}

.theme-nav-link:hover {
  background: #f8fafc;
  color: #0f172a;
}

.theme-nav-link.active {
  background: #fff7ed;
  color: #ea580c;
  font-weight: 700;
}

.theme-nav-link.active .theme-nav-icon-wrap {
  background: #f97316;
  color: #ffffff;
  box-shadow: 0 2px 8px rgba(249, 115, 22, 0.35);
}

.theme-nav-icon-wrap {
  width: 34px;
  height: 34px;
  border-radius: 8px;
  background: #f1f5f9;
  color: #64748b;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all 0.2s ease;
}

.theme-nav-info {
  flex: 1;
  min-width: 0;
}

.theme-nav-name {
  display: block;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  line-height: 1.3;
}

.theme-nav-sub {
  display: block;
  font-size: 11px;
  font-weight: 400;
  color: #94a3b8;
  margin-top: 2px;
}

.theme-nav-status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #10b981;
  box-shadow: 0 0 6px rgba(16, 185, 129, 0.6);
  flex-shrink: 0;
}

/* Right Content Area */
.theme-content-panel {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.theme-section-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 24px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
}

.theme-section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid #f1f5f9;
  padding-bottom: 16px;
  margin-bottom: 22px;
}

.theme-section-heading-group h2 {
  font-size: 18px;
  font-weight: 800;
  color: #0f172a;
  margin: 0 0 4px 0;
  display: flex;
  align-items: center;
  gap: 10px;
}

.theme-section-heading-group p {
  font-size: 13px;
  color: #64748b;
  margin: 0;
}

/* Toggle Switch */
.toggle-switch-wrapper {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  user-select: none;
}
.toggle-switch {
  position: relative;
  width: 44px;
  height: 24px;
  background-color: #cbd5e1;
  border-radius: 24px;
  transition: background-color 0.2s ease;
}
.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}
.toggle-slider {
  position: absolute;
  cursor: pointer;
  top: 2px;
  left: 2px;
  right: 2px;
  bottom: 2px;
  background-color: #cbd5e1;
  transition: .25s ease;
  border-radius: 24px;
}
.toggle-slider:before {
  position: absolute;
  content: "";
  height: 20px;
  width: 20px;
  left: 1px;
  bottom: 0px;
  background-color: white;
  transition: .25s ease;
  border-radius: 50%;
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
input:checked + .toggle-slider {
  background-color: #10b981;
}
input:checked + .toggle-slider:before {
  transform: translateX(19px);
}

/* Live Preview Card */
.theme-card-preview {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
  background-color: #0f172a;
  min-height: 220px;
  display: flex;
  align-items: center;
  box-shadow: 0 4px 15px rgba(0,0,0,0.12);
}
.theme-card-preview-bg {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center right;
  z-index: 1;
}
.theme-card-preview-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(90deg, rgba(10, 14, 20, 0.95) 0%, rgba(10, 14, 20, 0.75) 45%, rgba(10, 14, 20, 0.25) 100%);
  z-index: 2;
}
.theme-card-preview-content {
  position: relative;
  z-index: 3;
  padding: 24px;
  max-width: 480px;
}

/* Sticky Action Bar */
.theme-sticky-action-bar {
  position: sticky;
  bottom: 20px;
  z-index: 90;
  background: rgba(255, 255, 255, 0.96);
  backdrop-filter: blur(10px);
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 14px 22px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
}

/* Rich Text Toolbar & Editor */
.rich-btn {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 6px 10px;
  font-size: 12px;
  font-weight: 600;
  border-radius: 6px;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  color: #334155;
  cursor: pointer;
  transition: all 0.15s ease;
  line-height: 1;
}
.rich-btn:hover {
  background: #f1f5f9;
  border-color: #94a3b8;
  color: #0f172a;
}
.rich-btn:active {
  transform: scale(0.97);
}
#richEditorVisual:focus {
  outline: 2px solid rgba(249, 115, 22, 0.2);
}
#richEditorVisual p {
  margin: 0 0 14px 0;
}
#richEditorVisual p.seo-content-disclaimer {
  font-size: 12px;
  color: #9f1239;
  background: #fff1f2;
  border: 1px solid #fecdd3;
  padding: 10px 14px;
  border-radius: 8px;
  margin-top: 14px;
}
#richEditorVisual p.seo-content-compliance {
  font-size: 12px;
  font-weight: 600;
  color: #065f46;
  background: #ecfdf5;
  border: 1px solid #a7f3d0;
  padding: 10px 14px;
  border-radius: 8px;
  margin-top: 10px;
}
#richEditorVisual a {
  color: #f97316;
  text-decoration: underline;
}
</style>

<!-- Title & Action Bar -->
<div class="page-title-bar">
  <div class="page-heading-group">
    <h1>Theme Sections Manager</h1>
    <p class="page-subtitle">Configure homepage visual showcases, compliance warning banners, promo cards, and SEO content.</p>
  </div>
  <div class="page-actions-group">
    <a href="<?= url() ?>" target="_blank" class="btn-admin btn-admin-secondary" style="display: inline-flex; align-items: center; gap: 8px;">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
        <polyline points="15 3 21 3 21 9"></polyline>
        <line x1="10" y1="14" x2="21" y2="3"></line>
      </svg>
      <span>Live Storefront</span>
    </a>
  </div>
</div>

<!-- Flash Notifications -->
<?php if (!empty($success)): ?>
<div class="admin-alert admin-alert-success mb-lg">
  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
    <polyline points="22 4 12 14.01 9 11.01"></polyline>
  </svg>
  <span><?= e($success) ?></span>
</div>
<?php endif; ?>

<?php if (!empty($error)): ?>
<div class="admin-alert admin-alert-danger mb-lg">
  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
    <circle cx="12" cy="12" r="10"></circle>
    <line x1="12" y1="8" x2="12" y2="12"></line>
    <line x1="12" y1="16" x2="12.01" y2="16"></line>
  </svg>
  <span><?= e($error) ?></span>
</div>
<?php endif; ?>

<!-- Main 2-Column Hub Layout -->
<div class="theme-hub-container">

  <!-- Left: Scalable Sections Navigator Menu -->
  <aside class="theme-nav-panel">
    <div class="theme-nav-header">
      <h3 class="theme-nav-title">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
          <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
          <polyline points="2 17 12 22 22 17"></polyline>
          <polyline points="2 12 12 17 22 12"></polyline>
        </svg>
        <span>UI Sections</span>
      </h3>
      <span class="theme-nav-count-badge">4 Sections</span>
    </div>

    <!-- Search / Filter Sections -->
    <div class="theme-nav-search-wrap">
      <div style="position: relative;">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" style="position: absolute; left: 10px; top: 11px; color: #94a3b8;">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input type="text" id="sectionSearchInput" class="theme-nav-search" placeholder="Search section...">
      </div>
    </div>

    <ul class="theme-nav-list" id="themeNavList">
      <!-- Section 1: Hero Slider -->
      <li class="theme-nav-item" data-keywords="hero banner slider headline flavors cta">
        <a href="<?= url('admin/theme-sections?tab=hero') ?>" class="theme-nav-link <?= $activeTab === 'hero' ? 'active' : '' ?>">
          <div class="theme-nav-icon-wrap">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
              <line x1="8" y1="21" x2="16" y2="21"></line>
              <line x1="12" y1="17" x2="12" y2="21"></line>
            </svg>
          </div>
          <div class="theme-nav-info">
            <span class="theme-nav-name">Hero Slider Banners</span>
            <span class="theme-nav-sub"><?= count($heroSlides) ?> Slide<?= count($heroSlides) > 1 ? 's' : '' ?> Configured</span>
          </div>
          <span class="theme-nav-status-dot" title="Active"></span>
        </a>
      </li>

      <!-- Section 2: Announcement Bars -->
      <li class="theme-nav-item" data-keywords="announcement warning nicotine fda free shipping bar header compliance">
        <a href="<?= url('admin/theme-sections?tab=announcements') ?>" class="theme-nav-link <?= $activeTab === 'announcements' ? 'active' : '' ?>">
          <div class="theme-nav-icon-wrap">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
              <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
          </div>
          <div class="theme-nav-info">
            <span class="theme-nav-name">Announcement Bars</span>
            <span class="theme-nav-sub">FDA Warning &amp; Shipping</span>
          </div>
          <span class="theme-nav-status-dot" title="Active"></span>
        </a>
      </li>

      <!-- Section 3: Twin Promo Banner Cards -->
      <li class="theme-nav-item" data-keywords="promo banner twin cards foger adjust lineup deals callout">
        <a href="<?= url('admin/theme-sections?tab=twin') ?>" class="theme-nav-link <?= $activeTab === 'twin' ? 'active' : '' ?>">
          <div class="theme-nav-icon-wrap">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="8" height="18" rx="1"></rect>
              <rect x="13" y="3" width="8" height="18" rx="1"></rect>
            </svg>
          </div>
          <div class="theme-nav-info">
            <span class="theme-nav-name">Twin Promo Cards</span>
            <span class="theme-nav-sub">Mid-Page Callouts (2 Cards)</span>
          </div>
          <span class="theme-nav-status-dot" title="Active"></span>
        </a>
      </li>

      <!-- Section 4: Your Trusted Online Vape Store -->
      <li class="theme-nav-item" data-keywords="trusted online vape store seo content description text legal compliance disclaimer paragraphs">
        <a href="<?= url('admin/theme-sections?tab=trusted_store') ?>" class="theme-nav-link <?= $activeTab === 'trusted_store' ? 'active' : '' ?>">
          <div class="theme-nav-icon-wrap">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            </svg>
          </div>
          <div class="theme-nav-info">
            <span class="theme-nav-name">Trusted Store &amp; SEO</span>
            <span class="theme-nav-sub">Rich Content &amp; Compliance</span>
          </div>
          <span class="theme-nav-status-dot" title="<?= !empty($seoContent['is_active']) ? 'Active' : 'Disabled' ?>" style="<?= empty($seoContent['is_active']) ? 'background: #94a3b8; box-shadow: none;' : '' ?>"></span>
        </a>
      </li>
    </ul>

    <!-- Quick Preview Callout -->
    <div style="padding: 16px; margin: 12px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; text-align: center;">
      <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 4px;">Dynamic Architecture</span>
      <p style="font-size: 12px; color: #64748b; margin: 0;">Updates reflect in real-time across the storefront without recompiling.</p>
    </div>
  </aside>

  <!-- Right: Active Section Configuration Panel -->
  <main class="theme-content-panel">

    <!-- =========================================================================
         SECTION 1: HERO SLIDER BANNERS
         ========================================================================= -->
    <?php if ($activeTab === 'hero'): ?>
    <form action="<?= url('admin/theme-sections/save-hero') ?>" method="POST" id="heroSliderForm">
      
      <div class="theme-section-card">
        <div class="theme-section-header">
          <div class="theme-section-heading-group">
            <h2>
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#f97316" stroke-width="2">
                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                <line x1="8" y1="21" x2="16" y2="21"></line>
                <line x1="12" y1="17" x2="12" y2="21"></line>
              </svg>
              <span>Hero Slider Banners</span>
            </h2>
            <p>Add, customize, and arrange prominent visual banners with real-time live preview cards.</p>
          </div>
          <button type="button" id="addNewSlideBtn" class="btn-admin btn-admin-secondary" style="display: inline-flex; align-items: center; gap: 6px;">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Add New Slide</span>
          </button>
        </div>

        <div id="slidesContainer" style="display: flex; flex-direction: column; gap: 24px;">
          <?php foreach ($heroSlides as $idx => $slide): ?>
          <div class="hero-slide-card" data-slide-index="<?= $idx ?>" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px;">
            
            <!-- Slide Top Bar -->
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 18px;">
              <div style="display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 6px; background: #f97316; color: #fff; font-weight: 700; font-size: 12px;">
                  <?= $idx + 1 ?>
                </span>
                <h3 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">Slide #<?= $idx + 1 ?>: <span class="slide-header-title"><?= e($slide['title']) ?></span></h3>
              </div>
              <div style="display: flex; align-items: center; gap: 14px;">
                <label class="toggle-switch-wrapper">
                  <span style="font-size: 12px; font-weight: 600; color: #475569;">Active:</span>
                  <div class="toggle-switch">
                    <input type="hidden" name="slides[<?= $idx ?>][is_active]" value="0">
                    <input type="checkbox" name="slides[<?= $idx ?>][is_active]" value="1" <?= !empty($slide['is_active']) ? 'checked' : '' ?> class="slide-active-toggle">
                    <span class="toggle-slider"></span>
                  </div>
                </label>
                <button type="button" class="btn-admin btn-admin-danger remove-slide-btn" style="padding: 5px 9px; font-size: 12px; height: auto;" title="Remove this slide">
                  <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                  </svg>
                  <span>Delete</span>
                </button>
              </div>
            </div>

            <!-- Controls Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; align-items: start;">
              
              <!-- Form Fields -->
              <div style="display: flex; flex-direction: column; gap: 12px;">
                <div class="admin-form-group" style="margin: 0;">
                  <label class="admin-form-label" style="font-weight: 600; font-size: 12px;">Badge / Pill Tag</label>
                  <input type="text" name="slides[<?= $idx ?>][badge_text]" class="admin-form-input slide-badge-input" value="<?= e($slide['badge_text'] ?? 'NEW FLAVORS') ?>" placeholder="e.g. NEW FLAVORS">
                </div>

                <div class="admin-form-group" style="margin: 0;">
                  <label class="admin-form-label" style="font-weight: 600; font-size: 12px;">Main Heading / Title <span style="color: #ef4444;">*</span></label>
                  <input type="text" name="slides[<?= $idx ?>][title]" class="admin-form-input slide-title-input" value="<?= e($slide['title']) ?>" placeholder="e.g. Every Flavor. In Stock." required>
                </div>

                <div class="admin-form-group" style="margin: 0;">
                  <label class="admin-form-label" style="font-weight: 600; font-size: 12px;">Subtitle / Description</label>
                  <textarea name="slides[<?= $idx ?>][subtitle]" class="admin-form-input slide-subtitle-input" rows="2" placeholder="e.g. The full Foger Switch Pro lineup is here."><?= e($slide['subtitle']) ?></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                  <div class="admin-form-group" style="margin: 0;">
                    <label class="admin-form-label" style="font-weight: 600; font-size: 12px;">CTA Button Text</label>
                    <input type="text" name="slides[<?= $idx ?>][button_text]" class="admin-form-input slide-btn-text-input" value="<?= e($slide['button_text']) ?>" placeholder="e.g. Find Your Flavor">
                  </div>
                  <div class="admin-form-group" style="margin: 0;">
                    <label class="admin-form-label" style="font-weight: 600; font-size: 12px;">CTA Target URL</label>
                    <input type="text" name="slides[<?= $idx ?>][button_url]" class="admin-form-input slide-btn-url-input" value="<?= e($slide['button_url']) ?>" placeholder="e.g. category?b=foger">
                  </div>
                </div>

                <div class="admin-form-group" style="margin: 0;">
                  <label class="admin-form-label" style="font-weight: 600; font-size: 12px;">Banner Image Path</label>
                  <input type="text" name="slides[<?= $idx ?>][image_url]" class="admin-form-input slide-img-input" value="<?= e($slide['image_url']) ?>" placeholder="e.g. assets/images/hero-banner.webp">
                </div>
              </div>

              <!-- Live Preview Card -->
              <div>
                <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.05em;">Live Preview</label>
                <div class="theme-card-preview" style="min-height: 200px;">
                  <img src="<?= e($slide['image_url']) ?>" alt="Banner Preview" class="theme-card-preview-bg slide-preview-img" onerror="this.onerror=null;this.src='assets/images/no-image.svg'">
                  <div class="theme-card-preview-overlay"></div>
                  <div class="theme-card-preview-content" style="padding: 18px;">
                    <div style="display: inline-flex; align-items: center; gap: 6px; padding: 3px 8px; background: rgba(249, 115, 22, 0.25); border: 1px solid rgba(249, 115, 22, 0.5); border-radius: 999px; color: #f97316; font-size: 10px; font-weight: 800; text-transform: uppercase; margin-bottom: 8px;">
                      <span style="width: 4px; height: 4px; background: #f97316; border-radius: 50%;"></span>
                      <span class="slide-preview-badge"><?= e($slide['badge_text'] ?? 'NEW FLAVORS') ?></span>
                    </div>
                    <h3 class="slide-preview-title" style="font-size: 17px; font-weight: 800; color: #ffffff; line-height: 1.2; margin: 0 0 6px 0;"><?= e($slide['title']) ?></h3>
                    <p class="slide-preview-subtitle" style="font-size: 12px; color: #e2e8f0; line-height: 1.35; margin: 0 0 12px 0;"><?= e($slide['subtitle']) ?></p>
                    <div class="slide-preview-btn" style="display: inline-flex; align-items: center; gap: 5px; background: #f97316; color: #fff; font-size: 11px; font-weight: 700; padding: 7px 14px; border-radius: 6px;">
                      <span class="slide-preview-btn-text"><?= e($slide['button_text']) ?></span>
                      <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                      </svg>
                    </div>
                  </div>
                </div>
              </div>

            </div>

          </div>
          <?php endforeach; ?>
        </div>

      </div>

      <!-- Sticky Save Bar -->
      <div class="theme-sticky-action-bar">
        <span style="font-size: 13px; color: #64748b; font-weight: 500;">
          Configuring <strong style="color: #0f172a;"><?= count($heroSlides) ?> Hero Slides</strong>
        </span>
        <button type="submit" class="btn-admin btn-admin-primary" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 24px; font-weight: 700;">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
            <polyline points="17 21 17 13 7 13 7 21"></polyline>
            <polyline points="7 3 7 8 15 8"></polyline>
          </svg>
          <span>Save Hero Slider Banners</span>
        </button>
      </div>

    </form>
    <?php endif; ?>

    <!-- =========================================================================
         SECTION 2: ANNOUNCEMENT BARS
         ========================================================================= -->
    <?php if ($activeTab === 'announcements'): ?>
    <form action="<?= url('admin/theme-sections/save-announcements') ?>" method="POST">
      
      <div class="theme-section-card">
        <div class="theme-section-header">
          <div class="theme-section-heading-group">
            <h2>
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#f97316" stroke-width="2">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
              </svg>
              <span>Announcement &amp; Warning Bars</span>
            </h2>
            <p>Customize FDA nicotine warning and top promotional shipping announcement bars.</p>
          </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 24px;">

          <!-- Bar 1: FDA Warning -->
          <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
              <div>
                <h3 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0 0 2px 0;">Top Bar 1: FDA Nicotine Warning Bar</h3>
                <p style="font-size: 12px; color: #64748b; margin: 0;">Federal compliance banner displayed at the absolute top of the page.</p>
              </div>
              <label class="toggle-switch-wrapper">
                <span style="font-size: 12px; font-weight: 600; color: #475569;">Show Bar:</span>
                <div class="toggle-switch">
                  <input type="checkbox" name="fda_warning_active" value="1" <?= !empty($announcements['fda_warning_active']) ? 'checked' : '' ?> id="fdaActiveToggle">
                  <span class="toggle-slider"></span>
                </div>
              </label>
            </div>

            <!-- Live Preview -->
            <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.05em;">Live Preview</label>
            <div id="fdaPreviewBar" style="background: #000000; color: #ffffff; padding: 8px 16px; text-align: center; font-size: 12px; font-weight: 700; border-radius: 6px; margin-bottom: 14px; transition: opacity 0.2s ease; <?= empty($announcements['fda_warning_active']) ? 'opacity: 0.4;' : '' ?>">
              <span id="fdaPreviewText"><?= $announcements['fda_warning_text'] ?></span>
            </div>

            <div class="admin-form-group" style="margin: 0;">
              <label class="admin-form-label" style="font-weight: 600; font-size: 12px;">Warning Notice Text</label>
              <textarea name="fda_warning_text" id="fdaTextInput" rows="2" class="admin-form-input"><?= e($announcements['fda_warning_text']) ?></textarea>
            </div>
          </div>

          <!-- Bar 2: Free Shipping Promo -->
          <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
              <div>
                <h3 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0 0 2px 0;">Top Bar 2: Promotional Shipping Announcement</h3>
                <p style="font-size: 12px; color: #64748b; margin: 0;">Highlight free shipping, sales deals, or discounts right under the compliance bar.</p>
              </div>
              <label class="toggle-switch-wrapper">
                <span style="font-size: 12px; font-weight: 600; color: #475569;">Show Bar:</span>
                <div class="toggle-switch">
                  <input type="checkbox" name="promo_bar_active" value="1" <?= !empty($announcements['promo_bar_active']) ? 'checked' : '' ?> id="promoActiveToggle">
                  <span class="toggle-slider"></span>
                </div>
              </label>
            </div>

            <!-- Live Preview -->
            <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.05em;">Live Preview</label>
            <div id="promoPreviewBar" style="background: #111827; color: #f97316; padding: 10px 16px; text-align: center; font-size: 13px; font-weight: 700; border-radius: 6px; margin-bottom: 14px; transition: opacity 0.2s ease; <?= empty($announcements['promo_bar_active']) ? 'opacity: 0.4;' : '' ?>">
              <span id="promoPreviewText"><?= e($announcements['promo_bar_text']) ?></span>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 14px;">
              <div class="admin-form-group" style="margin: 0;">
                <label class="admin-form-label" style="font-weight: 600; font-size: 12px;">Headline Text</label>
                <input type="text" name="promo_bar_text" id="promoTextInput" class="admin-form-input" value="<?= e($announcements['promo_bar_text']) ?>" placeholder="e.g. Free shipping on orders over $150">
              </div>

              <div class="admin-form-group" style="margin: 0;">
                <label class="admin-form-label" style="font-weight: 600; font-size: 12px;">Target URL (Optional)</label>
                <input type="text" name="promo_bar_link" class="admin-form-input" value="<?= e($announcements['promo_bar_link']) ?>" placeholder="e.g. category">
              </div>
            </div>
          </div>

        </div>

      </div>

      <!-- Sticky Save Bar -->
      <div class="theme-sticky-action-bar">
        <span style="font-size: 13px; color: #64748b; font-weight: 500;">
          Applies globally to all storefront pages.
        </span>
        <button type="submit" class="btn-admin btn-admin-primary" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 24px; font-weight: 700;">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
            <polyline points="17 21 17 13 7 13 7 21"></polyline>
            <polyline points="7 3 7 8 15 8"></polyline>
          </svg>
          <span>Save Announcement Bars</span>
        </button>
      </div>

    </form>
    <?php endif; ?>

    <!-- =========================================================================
         SECTION 3: TWIN PROMO BANNER CARDS
         ========================================================================= -->
    <?php if ($activeTab === 'twin'): ?>
    <form action="<?= url('admin/theme-sections/save-twin-banners') ?>" method="POST">
      
      <div class="theme-section-card">
        <div class="theme-section-header">
          <div class="theme-section-heading-group">
            <h2>
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#f97316" stroke-width="2">
                <rect x="3" y="3" width="8" height="18" rx="1"></rect>
                <rect x="13" y="3" width="8" height="18" rx="1"></rect>
              </svg>
              <span>Twin Promotional Cards</span>
            </h2>
            <p>Customize the two mid-page banner callouts featured on the homepage.</p>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
          <?php foreach ($twinBanners as $i => $card): ?>
          <div class="twin-card-editor" data-card-index="<?= $i ?>" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 16px;">
              <h3 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">Card <?= $i + 1 ?>: <?= $i === 0 ? 'Left Card' : 'Right Card' ?></h3>
              <label class="toggle-switch-wrapper">
                <span style="font-size: 12px; font-weight: 600; color: #475569;">Active:</span>
                <div class="toggle-switch">
                  <input type="hidden" name="banners[<?= $i ?>][is_active]" value="0">
                  <input type="checkbox" name="banners[<?= $i ?>][is_active]" value="1" <?= !empty($card['is_active']) ? 'checked' : '' ?>>
                  <span class="toggle-slider"></span>
                </div>
              </label>
            </div>

            <!-- Mini Live Preview -->
            <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.05em;">Live Preview</label>
            <div style="position: relative; border-radius: 10px; overflow: hidden; min-height: 160px; background: #0f172a; margin-bottom: 16px; display: flex; align-items: center;">
              <img src="<?= e($card['image_url']) ?>" alt="Card Preview" class="twin-preview-img" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null;this.src='assets/images/no-image.svg'">
              <div style="position: absolute; inset: 0; background: linear-gradient(90deg, rgba(10, 14, 20, 0.92) 0%, rgba(10, 14, 20, 0.65) 50%, rgba(10, 14, 20, 0.2) 100%); z-index: 2;"></div>
              <div style="position: relative; z-index: 3; padding: 16px; max-width: 280px;">
                <h4 class="twin-preview-title" style="color: #fff; font-size: 14.5px; font-weight: 800; line-height: 1.25; margin: 0 0 5px 0;"><?= $card['title'] ?></h4>
                <p class="twin-preview-subtitle" style="color: #cbd5e1; font-size: 11.5px; margin: 0 0 10px 0; line-height: 1.35;"><?= $card['subtitle'] ?></p>
                <div style="display: inline-flex; align-items: center; gap: 5px; background: #f97316; color: #fff; font-size: 10.5px; font-weight: 700; padding: 5px 12px; border-radius: 6px;">
                  <span class="twin-preview-btn-text"><?= e($card['button_text']) ?></span>
                  <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="9 18 15 12 9 6"></polyline>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Controls -->
            <div style="display: flex; flex-direction: column; gap: 12px;">
              <div class="admin-form-group" style="margin: 0;">
                <label class="admin-form-label" style="font-weight: 600; font-size: 12px;">Card Title <span style="color: #ef4444;">*</span></label>
                <input type="text" name="banners[<?= $i ?>][title]" class="admin-form-input twin-title-input" value="<?= e($card['title']) ?>" placeholder="e.g. Foger Switch Pro &mdash; Up to 30,000 Puffs" required>
              </div>

              <div class="admin-form-group" style="margin: 0;">
                <label class="admin-form-label" style="font-weight: 600; font-size: 12px;">Card Subtitle</label>
                <textarea name="banners[<?= $i ?>][subtitle]" class="admin-form-input twin-subtitle-input" rows="2"><?= e($card['subtitle']) ?></textarea>
              </div>

              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div class="admin-form-group" style="margin: 0;">
                  <label class="admin-form-label" style="font-weight: 600; font-size: 12px;">Button Text</label>
                  <input type="text" name="banners[<?= $i ?>][button_text]" class="admin-form-input twin-btn-text-input" value="<?= e($card['button_text']) ?>" placeholder="e.g. Shop Foger">
                </div>
                <div class="admin-form-group" style="margin: 0;">
                  <label class="admin-form-label" style="font-weight: 600; font-size: 12px;">Target URL</label>
                  <input type="text" name="banners[<?= $i ?>][button_url]" class="admin-form-input twin-btn-url-input" value="<?= e($card['button_url']) ?>" placeholder="e.g. category?b=foger">
                </div>
              </div>

              <div class="admin-form-group" style="margin: 0;">
                <label class="admin-form-label" style="font-weight: 600; font-size: 12px;">Background Image URL</label>
                <input type="text" name="banners[<?= $i ?>][image_url]" class="admin-form-input twin-img-input" value="<?= e($card['image_url']) ?>" placeholder="e.g. assets/images/promo-card.webp">
              </div>
            </div>

          </div>
          <?php endforeach; ?>
        </div>

      </div>

      <!-- Sticky Save Bar -->
      <div class="theme-sticky-action-bar">
        <span style="font-size: 13px; color: #64748b; font-weight: 500;">
          Configuring <strong style="color: #0f172a;">Both Mid-Page Promo Cards</strong>
        </span>
        <button type="submit" class="btn-admin btn-admin-primary" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 24px; font-weight: 700;">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
            <polyline points="17 21 17 13 7 13 7 21"></polyline>
            <polyline points="7 3 7 8 15 8"></polyline>
          </svg>
          <span>Save Promo Cards</span>
        </button>
      </div>

    </form>
    <?php endif; ?>

    <!-- =========================================================================
         SECTION 4: YOUR TRUSTED ONLINE VAPE STORE & SEO CONTENT
         ========================================================================= -->
    <?php if ($activeTab === 'trusted_store'): ?>
    <form action="<?= url('admin/theme-sections/save-seo-content') ?>" method="POST" id="seoContentForm">
      
      <div class="theme-section-card">
        <div class="theme-section-header">
          <div class="theme-section-heading-group">
            <h2>
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#f97316" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              </svg>
              <span>Your Trusted Online Vape Store (SEO &amp; Rich Content)</span>
            </h2>
            <p>Manage the high-authority homepage SEO text, compliance disclaimers, and trust statement in a single rich text editor.</p>
          </div>
          <label class="toggle-switch-wrapper">
            <span style="font-size: 13px; font-weight: 600; color: #475569;">Show Section:</span>
            <div class="toggle-switch">
              <input type="hidden" name="is_active" value="0">
              <input type="checkbox" name="is_active" value="1" <?= !empty($seoContent['is_active']) ? 'checked' : '' ?> id="seoActiveToggle">
              <span class="toggle-slider"></span>
            </div>
          </label>
        </div>

        <!-- Real-Time Interactive Storefront Preview -->
        <div style="margin-bottom: 24px;">
          <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.05em;">Live Storefront Visual Preview</label>
          <div id="seoPreviewBox" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 28px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); transition: opacity 0.2s ease; <?= empty($seoContent['is_active']) ? 'opacity: 0.4;' : '' ?>">
            <h3 id="seoPreviewHeading" style="font-size: 20px; font-weight: 800; color: #0f172a; margin: 0 0 16px 0; border-bottom: 2px solid #ffeedb; padding-bottom: 8px;"><?= e($seoContent['title'] ?? 'Your Trusted Online Vape Store') ?></h3>
            <div id="seoPreviewBody" style="font-size: 13.5px; color: #475569; line-height: 1.6;">
              <?= $seoContent['content'] ?? '' ?>
            </div>
          </div>
        </div>

        <!-- Section Title Field -->
        <div class="admin-form-group" style="margin-bottom: 22px;">
          <label class="admin-form-label" style="font-weight: 700; font-size: 13px;">Section Main Headline <span style="color: #ef4444;">*</span></label>
          <input type="text" name="title" id="seoTitleInput" class="admin-form-input" style="font-size: 15px; font-weight: 600;" value="<?= e($seoContent['title'] ?? 'Your Trusted Online Vape Store') ?>" placeholder="Your Trusted Online Vape Store" required>
        </div>

        <!-- Single Unified Rich Text Editor -->
        <div class="admin-form-group" style="margin-bottom: 0;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <label class="admin-form-label" style="font-weight: 700; font-size: 13px; margin: 0; color: #0f172a;">
              Content Body (Rich Text Editor)
            </label>
            <div style="display: flex; align-items: center; gap: 8px;">
              <span style="font-size: 11.5px; color: #64748b;">Supports Bold, Italic, Headings, Links &amp; Lists</span>
              <button type="button" id="toggleCodeViewBtn" class="btn-admin btn-admin-secondary" style="padding: 4px 10px; font-size: 11px; height: auto; display: inline-flex; align-items: center; gap: 5px;">
                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                <span id="codeViewBtnText">HTML View</span>
              </button>
            </div>
          </div>

          <!-- Rich Text Container -->
          <div class="rich-editor-wrapper" style="border: 1px solid #cbd5e1; border-radius: 10px; overflow: hidden; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <!-- Toolbar -->
            <div class="rich-editor-toolbar" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 8px 12px; display: flex; flex-wrap: wrap; gap: 6px; align-items: center;">
              <button type="button" class="rich-btn" data-cmd="bold" title="Bold (Ctrl+B)"><b>B</b></button>
              <button type="button" class="rich-btn" data-cmd="italic" title="Italic (Ctrl+I)"><i>I</i></button>
              <button type="button" class="rich-btn" data-cmd="underline" title="Underline (Ctrl+U)"><u>U</u></button>
              <span style="width: 1px; height: 18px; background: #cbd5e1; margin: 0 2px;"></span>
              
              <button type="button" class="rich-btn" data-cmd="formatBlock" data-val="h2" title="Heading 2"><b>H2</b></button>
              <button type="button" class="rich-btn" data-cmd="formatBlock" data-val="h3" title="Heading 3"><b>H3</b></button>
              <button type="button" class="rich-btn" data-cmd="formatBlock" data-val="p" title="Normal Paragraph">¶ P</button>
              <span style="width: 1px; height: 18px; background: #cbd5e1; margin: 0 2px;"></span>

              <button type="button" class="rich-btn" data-cmd="insertUnorderedList" title="Bullet List">• List</button>
              <button type="button" class="rich-btn" data-cmd="insertOrderedList" title="Numbered List">1. List</button>
              <span style="width: 1px; height: 18px; background: #cbd5e1; margin: 0 2px;"></span>

              <button type="button" class="rich-btn" id="insertLinkBtn" title="Insert Link">
                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                <span>Link</span>
              </button>
              <button type="button" class="rich-btn" data-cmd="unlink" title="Remove Link">
                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.84 12.25l1.72-1.71a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M5.16 11.75l-1.72 1.71a5 5 0 0 0 7.07 7.07l1.72-1.71"></path><line x1="2" y1="2" x2="22" y2="22"></line></svg>
              </button>
              <span style="width: 1px; height: 18px; background: #cbd5e1; margin: 0 2px;"></span>

              <button type="button" class="rich-btn" id="insertDisclaimerBtn" title="Insert Disclaimer Notice" style="color: #9f1239; background: #fff1f2; border-color: #fecdd3;">
                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <span>Disclaimer Box</span>
              </button>
              <button type="button" class="rich-btn" id="insertComplianceBtn" title="Insert Compliance Notice" style="color: #065f46; background: #ecfdf5; border-color: #a7f3d0;">
                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <span>Compliance Box</span>
              </button>
            </div>

            <!-- Visual Contenteditable Area -->
            <div id="richEditorVisual" contenteditable="true" style="min-height: 280px; max-height: 520px; overflow-y: auto; padding: 20px; font-size: 14.5px; line-height: 1.65; color: #334155; outline: none; background: #ffffff;">
              <?= $seoContent['content'] ?? '' ?>
            </div>

            <!-- HTML Source Code Textarea (hidden by default) -->
            <textarea name="content" id="seoContentTextarea" class="admin-form-input" style="display: none; min-height: 280px; max-height: 520px; font-family: monospace; font-size: 13px; line-height: 1.6; border: none; border-radius: 0; padding: 20px; outline: none; background: #0f172a; color: #f8fafc; resize: vertical;"><?= htmlspecialchars($seoContent['content'] ?? '') ?></textarea>
          </div>
          <small style="color: #64748b; font-size: 12px; margin-top: 6px; display: block;">You can edit visually using the formatting toolbar, or click <strong>HTML View</strong> to edit tags directly.</small>
        </div>

      </div>

      <!-- Sticky Save Bar -->
      <div class="theme-sticky-action-bar">
        <span style="font-size: 13px; color: #64748b; font-weight: 500;">
          Powers the SEO keyword ranking &amp; trust text on the homepage.
        </span>
        <button type="submit" class="btn-admin btn-admin-primary" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 24px; font-weight: 700;">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
            <polyline points="17 21 17 13 7 13 7 21"></polyline>
            <polyline points="7 3 7 8 15 8"></polyline>
          </svg>
          <span>Save Trusted Store Content</span>
        </button>
      </div>

    </form>
    <?php endif; ?>

  </main>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
