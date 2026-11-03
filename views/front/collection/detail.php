<?php
/**
 * COLLECTION / CATEGORY DETAIL PAGE (detail.php)
 * 100% Dynamic Collection & Shop Catalog with Live Filters, Sorting, and Responsive Product Grid.
 */

$page_title = $page_title ?? 'Shop All Vapes, E-Liquids & Disposables | Infinity Vape Shop';
$page_js    = 'category';

include dirname(__DIR__) . '/includes/header.php';

// Safe variables
$collection       = $collection ?? [];
$allCollections   = $allCollections ?? [];
$childCollections = $childCollections ?? [];
$brands           = $brands ?? [];
$products         = $products ?? [];
$priceBounds      = $priceBounds ?? ['min' => 0, 'max' => 100];
$currentSlug      = $currentSlug ?? 'disposables';
$currentSort      = $currentSort ?? 'position';
$totalCount       = count($products);

// Format collection title & description
$collName = !empty($collection['name']) ? $collection['name'] : 'Shop All Vapes';
$collDesc = !empty($collection['short_description']) 
    ? $collection['short_description'] 
    : 'Browse our full lineup of authentic disposable vapes, premium e-liquids, nicotine pouches, and kits.';
?>

<!-- ==========================================
     1. BREADCRUMBS
     ========================================== -->
<nav class="category-breadcrumb-nav" aria-label="Breadcrumb">
  <div class="container">
    <ol class="breadcrumb-list">
      <li class="breadcrumb-item">
        <a href="<?= url() ?>">Home</a>
      </li>
      <?php if (!empty($collection['parent_name'])): ?>
      <li class="breadcrumb-separator" aria-hidden="true">
        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </li>
      <li class="breadcrumb-item">
        <a href="<?= \App\Models\Collection::getUrl($collection['parent_slug'] ?? '') ?>"><?= htmlspecialchars($collection['parent_name']) ?></a>
      </li>
      <?php endif; ?>
      <li class="breadcrumb-separator" aria-hidden="true">
        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </li>
      <li class="breadcrumb-item active" aria-current="page">
        <?= htmlspecialchars($collName) ?>
      </li>
    </ol>
  </div>
</nav>

<!-- ==========================================
     2. COLLECTION HEADER (TITLE, BANNER & QUICK PILLS)
     ========================================== -->
<section class="category-header-section">
  <div class="container">
    <div class="category-header-content">
      <h1 class="category-title"><?= htmlspecialchars($collName) ?></h1>
      <p class="category-description">
        <?= htmlspecialchars($collDesc) ?>
      </p>

      <!-- Quick Filter Pills Row (Sub-collections & Puff Presets) -->
      <div class="category-quick-pills-wrap">
        <div class="category-quick-pills">
          <?php
            $baseCollUrl = \App\Models\Collection::getUrl($collection);
            $sep = (strpos($baseCollUrl, '?') !== false) ? '&' : '?';
          ?>
          <a href="<?= $baseCollUrl ?>" class="quick-pill-btn <?= ($activePuff === 'all' || empty($activePuff)) ? 'active' : '' ?>" data-filter="all" data-url="<?= $baseCollUrl ?>">
            All <?= htmlspecialchars($collName) ?>
          </a>

          <a href="<?= $baseCollUrl . $sep ?>puffs=bestseller" class="quick-pill-btn <?= ($activePuff === 'bestseller') ? 'active' : '' ?>" data-filter="bestseller" data-url="<?= $baseCollUrl . $sep ?>puffs=bestseller">
            🔥 Best Sellers
          </a>

          <a href="<?= $baseCollUrl . $sep ?>puffs=5000" class="quick-pill-btn <?= ($activePuff === '5000' || $activePuff === '5k') ? 'active' : '' ?>" data-filter="5000" data-url="<?= $baseCollUrl . $sep ?>puffs=5000">
            5,000+ Puffs
          </a>

          <a href="<?= $baseCollUrl . $sep ?>puffs=10000" class="quick-pill-btn <?= ($activePuff === '10000' || $activePuff === '10k') ? 'active' : '' ?>" data-filter="10000" data-url="<?= $baseCollUrl . $sep ?>puffs=10000">
            10,000+ Puffs
          </a>

          <a href="<?= $baseCollUrl . $sep ?>puffs=15000" class="quick-pill-btn <?= ($activePuff === '15000' || $activePuff === '15k') ? 'active' : '' ?>" data-filter="15000" data-url="<?= $baseCollUrl . $sep ?>puffs=15000">
            15,000+ Puffs
          </a>

          <a href="<?= $baseCollUrl . $sep ?>puffs=20000" class="quick-pill-btn <?= ($activePuff === '20000' || $activePuff === '20k') ? 'active' : '' ?>" data-filter="20000" data-url="<?= $baseCollUrl . $sep ?>puffs=20000">
            20,000+ Puffs
          </a>

          <a href="<?= $baseCollUrl . $sep ?>puffs=30000" class="quick-pill-btn <?= ($activePuff === '30000' || $activePuff === '30k') ? 'active' : '' ?>" data-filter="30000" data-url="<?= $baseCollUrl . $sep ?>puffs=30000">
            30,000+ Puffs
          </a>

          <a href="<?= $baseCollUrl . $sep ?>puffs=50000" class="quick-pill-btn <?= ($activePuff === '50000' || $activePuff === '50k') ? 'active' : '' ?>" data-filter="50000" data-url="<?= $baseCollUrl . $sep ?>puffs=50000">
            50,000+ Puffs
          </a>

          <a href="<?= $baseCollUrl . $sep ?>puffs=zero-nic" class="quick-pill-btn <?= ($activePuff === 'zero-nic') ? 'active' : '' ?>" data-filter="zero-nic" data-url="<?= $baseCollUrl . $sep ?>puffs=zero-nic">
            🍃 Zero Nicotine
          </a>

          <?php if (!empty($childCollections)): ?>
            <?php foreach ($childCollections as $child): 
              $childName = $child['name'] ?? '';
              // Avoid duplicate puff tier pill
              if (preg_match('/(\d+)[,\d]*\s*puff/i', $childName)) continue;
              $childUrl = \App\Models\Collection::getUrl($child);
              $isChildActive = ($currentSlug === $child['slug']);
            ?>
              <a href="<?= $childUrl ?>" class="quick-pill-btn sub-cat-pill <?= $isChildActive ? 'active' : '' ?>">
                <?= htmlspecialchars($childName) ?>
                <?php if (!empty($child['products_count'])): ?>
                  <span class="pill-count">(<?= $child['products_count'] ?>)</span>
                <?php endif; ?>
              </a>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================
     3. MAIN COLLECTION CONTENT (SIDEBAR + PRODUCTS)
     ========================================== -->
<section class="category-main-section">
  <div class="container">
    <div class="category-layout-grid">

      <!-- ========================================
           LEFT COLUMN: FILTER SIDEBAR ("Shop By")
           ======================================== -->
      <aside class="category-sidebar-col" id="categorySidebarCol">
        <div class="sidebar-heading-row">
          <h2 class="sidebar-title">Shop By</h2>
          <button type="button" class="mobile-filter-close-btn" id="mobileFilterCloseBtn" aria-label="Close filters">
            &times;
          </button>
        </div>

        <div class="filter-card">

          <!-- 1. All Collections Accordion -->
          <div class="filter-accordion-group open">
            <button type="button" class="filter-group-header" aria-expanded="true">
              <span class="filter-group-title">Collections</span>
              <span class="filter-chevron" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </span>
            </button>
            <div class="filter-group-body">
              <ul class="filter-options-list">
                <li>
                  <label class="filter-option-label" onclick="window.location.href='<?= url('collections') ?>';">
                    <input type="radio" name="filterCollection" class="filter-radio" <?= ($currentSlug === 'all') ? 'checked' : '' ?>>
                    <span class="filter-radio-custom"></span>
                    <span class="option-name">Shop All Vapes</span>
                  </label>
                </li>
                <?php foreach ($allCollections as $cat): 
                  if (!empty($cat['parent_id'])) continue; // Show top level in primary list
                  $isActive = ($currentSlug === $cat['slug']);
                  $catUrl = \App\Models\Collection::getUrl($cat);
                ?>
                <li>
                  <label class="filter-option-label" onclick="window.location.href='<?= htmlspecialchars($catUrl) ?>';">
                    <input type="radio" name="filterCollection" class="filter-radio" <?= $isActive ? 'checked' : '' ?>>
                    <span class="filter-radio-custom"></span>
                    <span class="option-name"><?= htmlspecialchars($cat['name']) ?></span>
                    <?php if (isset($cat['products_count'])): ?>
                    <span class="option-count"><?= (int)$cat['products_count'] ?></span>
                    <?php endif; ?>
                  </label>
                </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>

          <!-- 2. Dual Price Range Filter -->
          <div class="filter-accordion-group open">
            <button type="button" class="filter-group-header" aria-expanded="true">
              <span class="filter-group-title">Price Range</span>
              <span class="filter-chevron" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </span>
            </button>
            <div class="filter-group-body">
              <div class="dual-range-wrapper">
                <div class="range-slider-track">
                  <div class="range-slider-fill" id="priceSliderFill" style="left: 0%; width: 100%;"></div>
                </div>
                <input type="range" class="range-input range-min" id="priceRangeMin" min="<?= $priceBounds['min'] ?>" max="<?= $priceBounds['max'] ?>" value="<?= $priceBounds['min'] ?>" step="1">
                <input type="range" class="range-input range-max" id="priceRangeMax" min="<?= $priceBounds['min'] ?>" max="<?= $priceBounds['max'] ?>" value="<?= $priceBounds['max'] ?>" step="1">
              </div>
              <div class="price-inputs-row">
                <div class="price-field-wrap">
                  <span class="currency-symbol">$</span>
                  <input type="number" id="priceInputMin" class="price-number-input" value="<?= $priceBounds['min'] ?>" min="<?= $priceBounds['min'] ?>" max="<?= $priceBounds['max'] ?>">
                </div>
                <span class="price-divider">to</span>
                <div class="price-field-wrap">
                  <span class="currency-symbol">$</span>
                  <input type="number" id="priceInputMax" class="price-number-input" value="<?= $priceBounds['max'] ?>" min="<?= $priceBounds['min'] ?>" max="<?= $priceBounds['max'] ?>">
                </div>
              </div>
              <button type="button" class="btn-filter-apply" id="btnApplyPriceFilter">Apply Price</button>
            </div>
          </div>

          <!-- 3. Brands Filter -->
          <?php if (!empty($brands)): ?>
          <div class="filter-accordion-group open">
            <button type="button" class="filter-group-header" aria-expanded="true">
              <span class="filter-group-title">Brand</span>
              <span class="filter-chevron" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </span>
            </button>
            <div class="filter-group-body">
              <ul class="filter-options-list">
                <?php foreach ($brands as $b): 
                  $bSlug = $b['slug'] ?? '';
                  $bCount = $b['products_count'] ?? 0;
                  $isBrandChecked = (!empty($activeFilters['brand']) && ($activeFilters['brand'] === $bSlug || $activeFilters['brand'] === $b['name']));
                ?>
                <li>
                  <label class="filter-option-label">
                    <input type="checkbox" class="brand-filter-checkbox" data-brand="<?= htmlspecialchars(strtolower($b['name'])) ?>" data-brand-slug="<?= htmlspecialchars($bSlug) ?>" <?= $isBrandChecked ? 'checked' : '' ?>>
                    <span class="filter-check-custom"></span>
                    <span class="option-name"><?= htmlspecialchars($b['name']) ?></span>
                    <span class="option-count"><?= (int)$bCount ?></span>
                  </label>
                </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
          <?php endif; ?>

          <!-- 4. Puff Count Brackets -->
          <div class="filter-accordion-group open">
            <button type="button" class="filter-group-header" aria-expanded="true">
              <span class="filter-group-title">Puff Count</span>
              <span class="filter-chevron" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </span>
            </button>
            <div class="filter-group-body">
              <ul class="filter-options-list">
                <li><label class="filter-option-label"><input type="checkbox" class="puff-filter-checkbox" data-min-puffs="5000" data-max-puffs="9999"><span class="filter-check-custom"></span><span class="option-name">5,000 – 9,999 Puffs</span></label></li>
                <li><label class="filter-option-label"><input type="checkbox" class="puff-filter-checkbox" data-min-puffs="10000" data-max-puffs="14999"><span class="filter-check-custom"></span><span class="option-name">10,000 – 14,999 Puffs</span></label></li>
                <li><label class="filter-option-label"><input type="checkbox" class="puff-filter-checkbox" data-min-puffs="15000" data-max-puffs="19999"><span class="filter-check-custom"></span><span class="option-name">15,000 – 19,999 Puffs</span></label></li>
                <li><label class="filter-option-label"><input type="checkbox" class="puff-filter-checkbox" data-min-puffs="20000" data-max-puffs="29999"><span class="filter-check-custom"></span><span class="option-name">20,000 – 29,999 Puffs</span></label></li>
                <li><label class="filter-option-label"><input type="checkbox" class="puff-filter-checkbox" data-min-puffs="30000" data-max-puffs="999999"><span class="filter-check-custom"></span><span class="option-name">30,000+ Puffs</span></label></li>
              </ul>
            </div>
          </div>

          <!-- 5. Nicotine Strength -->
          <div class="filter-accordion-group">
            <button type="button" class="filter-group-header" aria-expanded="false">
              <span class="filter-group-title">Nicotine Strength</span>
              <span class="filter-chevron" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </span>
            </button>
            <div class="filter-group-body">
              <ul class="filter-options-list">
                <li><label class="filter-option-label"><input type="checkbox" class="nic-filter-checkbox" data-nic="0"><span class="filter-check-custom"></span><span class="option-name">0% (Zero Nicotine)</span></label></li>
                <li><label class="filter-option-label"><input type="checkbox" class="nic-filter-checkbox" data-nic="20"><span class="filter-check-custom"></span><span class="option-name">2% (20mg/mL)</span></label></li>
                <li><label class="filter-option-label"><input type="checkbox" class="nic-filter-checkbox" data-nic="30"><span class="filter-check-custom"></span><span class="option-name">3% (30mg/mL)</span></label></li>
                <li><label class="filter-option-label"><input type="checkbox" class="nic-filter-checkbox" data-nic="50"><span class="filter-check-custom"></span><span class="option-name">5% (50mg/mL Nic Salt)</span></label></li>
              </ul>
            </div>
          </div>

          <!-- 6. Stock Availability -->
          <div class="filter-accordion-group">
            <button type="button" class="filter-group-header" aria-expanded="false">
              <span class="filter-group-title">Availability</span>
              <span class="filter-chevron" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </span>
            </button>
            <div class="filter-group-body">
              <ul class="filter-options-list">
                <li>
                  <label class="filter-option-label">
                    <input type="checkbox" id="inStockCheckbox">
                    <span class="filter-check-custom"></span>
                    <span class="option-name">In Stock Only</span>
                  </label>
                </li>
              </ul>
            </div>
          </div>

          <!-- Reset All Filters Button -->
          <div style="padding: 16px 20px; border-top: 1px solid #edf2f7; text-align: center;">
            <button type="button" id="btnResetAllFilters" style="background: none; border: none; color: #fa5a1e; font-size: 13px; font-weight: 700; cursor: pointer; text-decoration: underline;">
              Clear All Filters
            </button>
          </div>

        </div>
      </aside>

      <!-- ========================================
           RIGHT COLUMN: TOOLBAR & PRODUCT CARDS
           ======================================== -->
      <main class="category-products-col">

        <!-- Toolbar (Results count, Grid/List view switcher, Sort dropdown, Mobile filter button) -->
        <div class="category-toolbar">
          <div class="toolbar-left">
            <span class="toolbar-results-count" id="toolbarResultsCount">
              Showing <strong id="visibleCountText"><?= $totalCount ?></strong> products
            </span>
            
            <!-- Grid / List Switcher -->
            <div class="view-mode-switcher">
              <button type="button" class="view-btn grid-view active" id="btnGridView" aria-label="Grid View" title="Grid View">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2">
                  <rect x="3" y="3" width="7" height="7"></rect>
                  <rect x="14" y="3" width="7" height="7"></rect>
                  <rect x="14" y="14" width="7" height="7"></rect>
                  <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
              </button>
              <button type="button" class="view-btn list-view" id="btnListView" aria-label="List View" title="List View">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2">
                  <line x1="8" y1="6" x2="21" y2="6"></line>
                  <line x1="8" y1="12" x2="21" y2="12"></line>
                  <line x1="8" y1="18" x2="21" y2="18"></line>
                  <line x1="3" y1="6" x2="3.01" y2="6"></line>
                  <line x1="3" y1="12" x2="3.01" y2="12"></line>
                  <line x1="3" y1="18" x2="3.01" y2="18"></line>
                </svg>
              </button>
            </div>

            <!-- Mobile Filter Trigger Button -->
            <button type="button" class="mobile-filter-trigger-btn" id="mobileFilterTriggerBtn">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="4" y1="21" x2="4" y2="14"></line>
                <line x1="4" y1="10" x2="4" y2="3"></line>
                <line x1="12" y1="21" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12" y2="3"></line>
                <line x1="20" y1="21" x2="20" y2="16"></line>
                <line x1="20" y1="12" x2="20" y2="3"></line>
                <line x1="1" y1="14" x2="7" y2="14"></line>
                <line x1="9" y1="8" x2="15" y2="8"></line>
                <line x1="17" y1="16" x2="23" y2="16"></line>
              </svg>
              <span>Filters</span>
            </button>
          </div>

          <!-- Sort Dropdown -->
          <div class="toolbar-right">
            <div class="sort-select-wrapper">
              <select id="categorySortSelect" class="sort-select">
                <option value="position" <?= ($currentSort === 'position') ? 'selected' : '' ?>>Featured / Position</option>
                <option value="bestseller" <?= ($currentSort === 'bestseller') ? 'selected' : '' ?>>Best Selling</option>
                <option value="price-asc" <?= ($currentSort === 'price-asc') ? 'selected' : '' ?>>Price: Low to High</option>
                <option value="price-desc" <?= ($currentSort === 'price-desc') ? 'selected' : '' ?>>Price: High to Low</option>
                <option value="name" <?= ($currentSort === 'name') ? 'selected' : '' ?>>Product Name (A-Z)</option>
                <option value="newest" <?= ($currentSort === 'newest') ? 'selected' : '' ?>>Newest Arrivals</option>
              </select>
              <button type="button" class="sort-direction-btn" id="sortDirectionBtn" aria-label="Toggle sort direction">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M7 15l5 5 5-5M7 9l5-5 5 5"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- 4-Column Product Grid -->
        <div class="category-product-grid" id="categoryProductGrid">
          <?php if (!empty($products)): ?>
            <?php foreach ($products as $p): 
              $pId    = $p['id'];
              $pSlug  = !empty($p['slug']) ? $p['slug'] : $pId;
              $pTitle = !empty($p['title']) ? $p['title'] : ($p['name'] ?? 'Vape Product');
              $pUrl   = url('product/' . urlencode($pSlug));
              
              // Clean image url with proper fallback
              $pImg = !empty($p['featured_image']) ? $p['featured_image'] : 'assets/images/vape-image.webp';
              if (!preg_match('~^(https?://|//|#)~i', $pImg)) {
                  $pImg = url($pImg);
              }

              $basePrice    = (float)($p['base_price'] ?? 0);
              $comparePrice = (float)($p['compare_price'] ?? 0);
              $hasDiscount  = ($comparePrice > $basePrice);
              $discountPct  = $hasDiscount ? round((($comparePrice - $basePrice) / $comparePrice) * 100) : 0;

              // Parse puff count number for data attributes
              $puffsNum = 0;
              if (!empty($p['spec_puffs']) && preg_match('/\d+/', str_replace(',', '', $p['spec_puffs']), $m)) {
                  $puffsNum = (int)$m[0];
              } elseif (preg_match('/(\d+)\s*k?\s*puffs?/i', $pTitle, $m)) {
                  $puffsNum = (int)$m[1];
                  if (stripos($m[0], 'k') !== false && $puffsNum < 1000) {
                      $puffsNum *= 1000;
                  }
              }

              // Parse nicotine value for data attributes
              $nicNum = 50; // default standard 50mg / 5%
              if (!empty($p['spec_nicotine']) && preg_match('/\d+/', $p['spec_nicotine'], $m)) {
                  $nicNum = (int)$m[0];
              }

              // Category classification for quick pill filters
              $pillCat = 'all';
              if (!empty($p['is_bestseller'])) {
                  $pillCat = 'bestseller';
              } elseif ($puffsNum >= 50000) {
                  $pillCat = '50k';
              } elseif ($puffsNum >= 30000) {
                  $pillCat = '30k';
              } elseif ($puffsNum >= 20000) {
                  $pillCat = '20k';
              } elseif ($puffsNum >= 15000) {
                  $pillCat = '15k';
              } elseif ($puffsNum >= 10000) {
                  $pillCat = '10k';
              } elseif ($puffsNum >= 5000) {
                  $pillCat = '5k';
              }
              if ($nicNum === 0 || stripos($pTitle, 'zero nic') !== false) {
                  $pillCat = 'zero-nic';
              }

              $brandName = !empty($p['brand']) ? $p['brand'] : '';
              $brandSlug = !empty($p['brand_slug']) ? $p['brand_slug'] : strtolower(str_replace(' ', '-', $brandName));
            ?>
            <div class="category-product-card" 
                 data-id="<?= $pId ?>" 
                 data-category="<?= htmlspecialchars($pillCat) ?>" 
                 data-brand="<?= htmlspecialchars(strtolower($brandName)) ?>"
                 data-brand-slug="<?= htmlspecialchars($brandSlug) ?>"
                 data-price="<?= $basePrice ?>"
                 data-puffs="<?= $puffsNum ?>"
                 data-nicotine="<?= $nicNum ?>"
                 data-bestseller="<?= !empty($p['is_bestseller']) ? '1' : '0' ?>"
                 data-instock="<?= ($p['stock_quantity'] > 0 && ($p['stock_status'] ?? '') !== 'Out of Stock') ? 'true' : 'false' ?>">
              
              <!-- Card Media & Badges -->
              <div class="card-media-box">
                <a href="<?= htmlspecialchars($pUrl) ?>" class="card-img-link">
                  <img src="<?= htmlspecialchars($pImg) ?>" 
                       alt="<?= htmlspecialchars($pTitle) ?>" 
                       class="card-product-img" 
                       loading="lazy"
                       onerror="this.onerror=null;this.src='<?= url('assets/images/vape-image.webp') ?>';">
                </a>

                <button type="button" class="btn-card-add-cart" data-id="<?= $pId ?>" data-title="<?= htmlspecialchars($pTitle) ?>" data-price="<?= $basePrice ?>">
                  ADD TO CART
                </button>
              </div>

              <!-- Card Info Box -->
              <div class="card-info-box">
                <?php if (!empty($brandName)): ?>
                  <div class="card-brand-tag"><?= htmlspecialchars($brandName) ?></div>
                <?php endif; ?>

                <h3 class="card-product-title">
                  <a href="<?= htmlspecialchars($pUrl) ?>" title="<?= htmlspecialchars($pTitle) ?>">
                    <?= htmlspecialchars($pTitle) ?>
                  </a>
                </h3>

                <!-- Rating & Reviews -->
                <div class="card-rating-row">
                  <span class="card-stars">★★★★★</span>
                  <span class="card-reviews-count">(<?= !empty($p['reviews_count']) ? (int)$p['reviews_count'] : 24 ?>)</span>
                </div>

                <!-- Puff Count Pill -->
                <?php if ($puffsNum > 0): ?>
                  <div class="card-puff-badge">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    <?= number_format($puffsNum) ?> Puffs
                  </div>
                <?php endif; ?>

                <!-- Price Row -->
                <div class="card-price-row">
                  <span class="card-current-price">$<?= number_format($basePrice, 2) ?></span>
                  <?php if ($hasDiscount): ?>
                    <span class="card-old-price">$<?= number_format($comparePrice, 2) ?></span>
                  <?php endif; ?>
                </div>
              </div>

            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <!-- Empty State (Shown when filters match 0 products) -->
        <div id="noProductsFoundBox" style="display: <?= empty($products) ? 'block' : 'none' ?>; text-align: center; padding: 60px 20px; background: #fff; border-radius: 12px; border: 1px dashed #cbd5e1; margin-top: 20px;">
          <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="#94a3b8" stroke-width="1.5" style="margin-bottom: 16px;">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            <line x1="8" y1="11" x2="14" y2="11"></line>
          </svg>
          <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">No Products Found</h3>
          <p style="font-size: 14px; color: #64748b; margin: 0 0 20px 0;">There are no products matching your selected filter criteria.</p>
          <button type="button" onclick="document.getElementById('btnResetAllFilters').click();" class="quick-pill-btn active" style="padding: 10px 24px; font-size: 13px;">
            Reset Filters
          </button>
        </div>

        <!-- Pagination Bar -->
        <div class="category-pagination-bar" id="categoryPaginationBar" style="display: <?= ($totalCount > 16) ? 'flex' : 'none' ?>;">
          <div class="pagination-info-wrap">
            <span class="pagination-info-text" id="paginationInfoText">
              Showing <strong>1-<?= min(16, $totalCount) ?></strong> of <strong><?= $totalCount ?></strong> products
            </span>
          </div>

          <div class="pagination-controls-group">
            <button type="button" class="pagination-btn pagination-arrow-btn" id="paginationPrevBtn" aria-label="Previous Page" title="Previous Page">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
              </svg>
            </button>
            <div class="pagination-pages-wrap" id="paginationPagesWrap">
              <!-- Dynamically generated page buttons by category.js -->
            </div>
            <button type="button" class="pagination-btn pagination-arrow-btn" id="paginationNextBtn" aria-label="Next Page" title="Next Page">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
              </svg>
            </button>
          </div>
        </div>

      </main>

    </div>

    <!-- ========================================
         CATEGORY BOTTOM DESCRIPTION (READ MORE / READ LESS)
         ======================================== -->
    <?php 
      $longDesc = !empty($collection['long_description']) ? $collection['long_description'] : '';
    ?>
    <?php if (!empty($longDesc)): ?>
    <style>
      .category-bottom-text-wrap.expanded { max-height: 5000px !important; }
      .category-bottom-text { font-size: 0.925rem; color: #475569; line-height: 1.75; }
      .category-bottom-text h1,
      .category-bottom-text h2,
      .category-bottom-text h3,
      .category-bottom-text h4,
      .category-bottom-text h5,
      .category-bottom-text h6 {
        color: #0b2545 !important;
      }
      .category-bottom-text h1 { font-size: 1.55rem; font-weight: 800; margin: 18px 0 8px; line-height: 1.25; }
      .category-bottom-text h2 { font-size: 1.35rem; font-weight: 800; margin: 16px 0 8px; line-height: 1.3; }
      .category-bottom-text h3 { font-size: 1.18rem; font-weight: 700; margin: 14px 0 6px; line-height: 1.35; }
      .category-bottom-text h4 { font-size: 1.05rem; font-weight: 700; margin: 12px 0 6px; line-height: 1.35; }
      .category-bottom-text h5 { font-size: 0.95rem; font-weight: 700; margin: 10px 0 4px; line-height: 1.4; }
      .category-bottom-text h6 { font-size: 0.88rem; font-weight: 700; margin: 10px 0 4px; line-height: 1.4; text-transform: uppercase; }
      .category-bottom-text p,
      .category-bottom-text li,
      .category-bottom-text div {
        color: #475569 !important;
      }
      .category-bottom-text span:not(.rte-highlight):not(mark) {
        color: inherit !important;
      }
      .category-bottom-text p { margin: 0 0 10px; }
      .category-bottom-text ul, .category-bottom-text ol { margin: 0 0 12px 22px; padding-left: 0; }
      .category-bottom-text li { margin-bottom: 4px; }
      .category-bottom-text strong, .category-bottom-text b { color: #0f172a; font-weight: 700; }
      .category-bottom-text em, .category-bottom-text i { font-style: italic; }
      .category-bottom-text u { text-decoration: underline; }
      .category-bottom-text a { color: #fa5a1e !important; text-decoration: underline; font-weight: 600; }
      .category-bottom-text mark, .category-bottom-text .rte-highlight { background-color: #fef08a !important; padding: 1px 4px; border-radius: 3px; color: #0b2545 !important; }
    </style>
    <section class="category-bottom-description">
      <div class="category-bottom-text-wrap" id="categoryBottomTextWrap">
        <div class="category-bottom-text">
          <?= $longDesc ?>
        </div>
      </div>
      <button type="button" class="btn-read-more-toggle" id="btnReadMoreToggle" aria-expanded="false">
        <span class="read-more-text">Read More</span>
        <svg class="read-more-chevron" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </button>
    </section>
    <?php endif; ?>

    <!-- ========================================
         COLLECTION FAQS ACCORDION SECTION
         ======================================== -->
    <?php if (!empty($collectionFaqs)): ?>
    <section class="category-faqs-section" id="collectionFaqsSection">
      <h2 class="category-faqs-title">Frequently Asked Questions</h2>
      <div class="category-faqs-list">
        <?php foreach ($collectionFaqs as $idx => $faq): ?>
        <div class="category-faq-item <?= ($idx === 0) ? 'open' : '' ?>">
          <h3 class="category-faq-heading">
            <button type="button" class="category-faq-question-btn" aria-expanded="<?= ($idx === 0) ? 'true' : 'false' ?>">
              <span class="faq-q-text"><?= htmlspecialchars($faq['question']) ?></span>
              <span class="faq-q-chevron" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </span>
            </button>
          </h3>
          <div class="category-faq-answer-collapse">
            <div class="category-faq-answer-inner">
              <p><?= nl2br(htmlspecialchars($faq['answer'])) ?></p>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

    <!-- ========================================
         RELATED CATEGORIES / COLLECTIONS SECTION
         ======================================== -->
    <?php if (!empty($relatedCollections)): ?>
    <section class="related-categories-section">
      <h2 class="related-categories-title">Explore Related Collections</h2>
      <div class="related-categories-grid">
        <?php foreach ($relatedCollections as $otherCol): 
          if (($otherCol['slug'] ?? '') === $currentSlug) continue;
        ?>
        <a href="<?= \App\Models\Collection::getUrl($otherCol) ?>" class="related-category-card">
          <span class="related-cat-name"><?= htmlspecialchars($otherCol['name']) ?></span>
          <span class="related-cat-arrow" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </span>
        </a>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

  </div>
</section>

<!-- Mobile Filter Backdrop -->
<div class="mobile-filter-backdrop" id="mobileFilterBackdrop"></div>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
