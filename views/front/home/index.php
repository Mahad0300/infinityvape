<?php
/**
 * HOMEPAGE (index.php)
 */

$page_title = 'Online Vape Store | Every Flavor. In Stock.';
$page_js    = 'home';

include dirname(__DIR__) . '/includes/header.php';
?>

<?php 
$activeSlides = array_filter($heroSlides ?? [], function($s) { return !empty($s['is_active']); });
if (!empty($activeSlides)): 
?>
<!-- ==========================================
     HERO BANNER SECTION
     ========================================== -->
<section class="hero-banner-section">
  <div class="hero-banner-container">
    <div class="hero-slider-wrapper" id="heroSliderWrapper">
      <div class="hero-slider-slides">
        <?php 
        $slideIdx = 0;
        foreach ($activeSlides as $slide):
            $isFirst = ($slideIdx === 0);
            $imgSrc = !empty($slide['image_url']) ? $slide['image_url'] : 'assets/images/no-image.svg';
            if (!preg_match('~^(https?://|//|#)~i', $imgSrc)) {
                $imgSrc = url($imgSrc);
            }
            $btnUrl = !empty($slide['button_url']) ? (preg_match('~^(https?://|//|#)~i', $slide['button_url']) ? $slide['button_url'] : url($slide['button_url'])) : url('category');
        ?>
        <div class="hero-banner-card hero-slide-item <?= $isFirst ? 'active' : '' ?>" data-slide="<?= $slideIdx ?>">
          <!-- Background Banner Image -->
          <img 
            src="<?= htmlspecialchars($imgSrc) ?>" 
            alt="<?= htmlspecialchars($slide['title']) ?>" 
            class="hero-banner-bg"
            onerror="this.onerror=null;this.src='<?= url('assets/images/no-image.svg') ?>';"
          >

          <!-- Left Text & CTA Content -->
          <div class="hero-banner-content">
            <?php if (!empty($slide['badge_text'])): ?>
            <div class="hero-pill-badge">
              <span class="hero-pill-dot"></span>
              <?= htmlspecialchars($slide['badge_text']) ?>
            </div>
            <?php endif; ?>
            
            <h1 class="hero-title"><?= htmlspecialchars($slide['title']) ?></h1>
            
            <?php if (!empty($slide['subtitle'])): ?>
            <p class="hero-subtitle"><?= htmlspecialchars($slide['subtitle']) ?></p>
            <?php endif; ?>
            
            <a href="<?= htmlspecialchars($btnUrl) ?>" class="hero-cta-btn">
              <?= htmlspecialchars(!empty($slide['button_text']) ? $slide['button_text'] : 'Shop Now') ?>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </a>
          </div>
        </div>
        <?php $slideIdx++; endforeach; ?>
      </div>

      <?php if (count($activeSlides) > 1): ?>
      <!-- Slider Arrows -->
      <button class="hero-slider-nav-btn prev" id="heroPrevBtn" aria-label="Previous Slide">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
      </button>
      <button class="hero-slider-nav-btn next" id="heroNextBtn" aria-label="Next Slide">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </button>

      <!-- Slider Dots -->
      <div class="hero-slider-dots" id="heroSliderDots">
        <?php for ($i = 0; $i < count($activeSlides); $i++): ?>
        <button class="hero-slider-dot <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>" aria-label="Slide <?= $i + 1 ?>"></button>
        <?php endfor; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ==========================================
     TRUST / FEATURES BAR SECTION
     ========================================== -->
<section class="features-bar-section">
  <div class="container">
    <div class="features-bar-card">
      
      <!-- Feature 1: Fast Shipping -->
      <div class="feature-item">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
        </div>
        <div class="feature-text">
          <h4 class="feature-title">Fast Shipping</h4>
          <p class="feature-desc">We ship same day</p>
        </div>
      </div>

      <div class="feature-divider"></div>

      <!-- Feature 2: Secure Checkout -->
      <div class="feature-item">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            <polyline points="9 12 11 14 15 10"></polyline>
          </svg>
        </div>
        <div class="feature-text">
          <h4 class="feature-title">Secure Checkout</h4>
          <p class="feature-desc">SSL encrypted payments</p>
        </div>
      </div>

      <div class="feature-divider"></div>

      <!-- Feature 3: Lower Prices -->
      <div class="feature-item">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
            <line x1="7" y1="7" x2="7.01" y2="7"></line>
          </svg>
        </div>
        <div class="feature-text">
          <h4 class="feature-title">Lower Prices</h4>
          <p class="feature-desc">Price match guarantee</p>
        </div>
      </div>

      <div class="feature-divider"></div>

      <!-- Feature 4: Authentic Products -->
      <div class="feature-item">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="9 12 11 14 15 10"></polyline>
          </svg>
        </div>
        <div class="feature-text">
          <h4 class="feature-title">Authentic Products</h4>
          <p class="feature-desc">100% genuine brands</p>
        </div>
      </div>

      <div class="feature-divider"></div>

      <!-- Feature 5: Great Customer Service -->
      <div class="feature-item">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 18v-6a9 9 0 0 1 18 0v6"></path>
            <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path>
          </svg>
        </div>
        <div class="feature-text">
          <h4 class="feature-title">Great Customer Service</h4>
          <p class="feature-desc">Support when you need it</p>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ==========================================
     BROWSE CATEGORIES SECTION
     ========================================== -->
<section class="browse-categories-section">
  <div class="container">
    
    <!-- Section Header -->
    <div class="section-header">
      <h2 class="section-title">Browse Our Vape Categories</h2>
      <p class="section-subtitle">Find disposable vapes, e-liquids, nicotine pouches and more.</p>
    </div>

    <!-- Category Cards Grid -->
    <div class="categories-grid">
      <?php $categories = $categories ?? []; ?>
      <?php if (!empty($categories)): ?>
        <?php foreach ($categories as $cat): 
          $catUrl = url('collection/' . urlencode($cat['slug']));
          $imgUrl = !empty($cat['header_image_url']) ? $cat['header_image_url'] : 'assets/images/no-image.svg';
          if (!preg_match('~^(https?://|//|#)~i', $imgUrl)) {
              $imgUrl = url($imgUrl);
          }
        ?>
          <a href="<?= htmlspecialchars($catUrl) ?>" class="category-card">
            <div class="category-card-media">
              <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" class="category-card-img" onerror="this.onerror=null;this.src='<?= url('assets/images/no-image.svg') ?>';">
            </div>
            <div class="category-card-info">
              <h3 class="category-card-title"><?= htmlspecialchars($cat['name']) ?></h3>
              <?php if (!empty($cat['short_description'])): ?>
                <p class="category-card-desc"><?= htmlspecialchars($cat['short_description']) ?></p>
              <?php endif; ?>
            </div>
          </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>
</section>

<!-- ==========================================
     SHOP BY BRAND SECTION
     ========================================== -->
<section class="shop-by-brand-section">
  <div class="container">
    <div class="brand-showcase-card">
      
      <!-- Section Header -->
      <div class="section-header">
        <h2 class="section-title">Shop by Brand</h2>
        <p class="section-subtitle">We carry the top brands you know and love</p>
      </div>

      <!-- Brands 4x3 Grid -->
      <div class="brands-grid">
        <?php $brands = $brands ?? []; ?>
        <?php if (!empty($brands)): ?>
          <?php foreach ($brands as $b): ?>
            <?php
              $bSlug = $b['slug'] ?? '';
              $bName = $b['name'] ?? '';
              $bDesc = $b['description'] ?? 'Official Verified Vendor';
              $bLogo = !empty($b['logo_url']) ? $b['logo_url'] : 'assets/images/no-image.svg';
              if (!preg_match('~^(https?://|//|#)~i', $bLogo)) {
                  $bLogo = url($bLogo);
              }
            ?>
            <a href="<?= url('category?b=' . urlencode($bSlug)) ?>" class="brand-item-card">
              <div class="brand-logo-wrap">
                <img src="<?= htmlspecialchars($bLogo) ?>" alt="<?= htmlspecialchars($bName) ?>" class="brand-logo-img" onerror="this.onerror=null;this.src='<?= url('assets/images/no-image.svg') ?>';">
              </div>
              <div class="brand-item-info">
                <h3 class="brand-item-name"><?= htmlspecialchars($bName) ?></h3>
                <p class="brand-item-desc"><?= htmlspecialchars($bDesc) ?></p>
              </div>
            </a>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>

<!-- ==========================================
     NEW ARRIVALS: LATEST PRODUCTS SECTION
     ========================================== -->
<section class="products-showcase-section">
  <div class="container">
    
    <!-- Section Header with Title & Navigation Controls -->
    <div class="products-section-header">
      <div class="products-header-left">
        <h2 class="products-section-title">New Arrivals: Latest Products</h2>
        <p class="products-section-subtitle">Our newest products, shipped same day.</p>
      </div>
      <div class="products-header-right">
        <a href="<?= url('collection/new-arrivals') ?>" class="view-all-link">
          View All New Arrivals
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </a>
        <div class="slider-nav-arrows">
          <button type="button" class="slider-arrow-btn prev-btn" aria-label="Previous">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
          </button>
          <button type="button" class="slider-arrow-btn next-btn" aria-label="Next">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </button>
        </div>
      </div>
    </div>

            <!-- Products Grid (6 Columns) -->
    <div class="products-grid">
      <?php $newArrivals = $newArrivals ?? []; ?>
      <?php if (!empty($newArrivals)): ?>
        <?php foreach ($newArrivals as $prod): ?>
          <?php
            $pId = $prod['id'];
            $pSlug = !empty($prod['slug']) ? $prod['slug'] : $pId;
            $pTitle = $prod['title'] ?? $prod['name'] ?? '';
            $pPrice = (float)($prod['base_price'] ?? 0);
            $pCompare = !empty($prod['compare_price']) ? (float)$prod['compare_price'] : 0;
            $pImg = !empty($prod['featured_image']) ? $prod['featured_image'] : 'assets/images/no-image.svg';
            if (!preg_match('~^(https?://|//|#)~i', $pImg)) {
                $pImg = url($pImg);
            }
          ?>
          <div class="product-card">
            <div class="product-card-media">
              <a href="<?= url('product/' . urlencode($pSlug)) ?>" class="product-img-wrap">
                <img src="<?= htmlspecialchars($pImg) ?>" alt="<?= htmlspecialchars($pTitle) ?>" class="product-img" onerror="this.onerror=null;this.src='<?= url('assets/images/no-image.svg') ?>';">
              </a>
              <div class="product-action-wrap">
                <button type="button" class="btn-add-to-cart" data-id="<?= $pId ?>" data-title="<?= htmlspecialchars($pTitle) ?>" data-price="<?= number_format($pPrice, 2, '.', '') ?>" data-image="<?= htmlspecialchars($pImg) ?>">ADD TO CART</button>
              </div>
            </div>
            <div class="product-card-info">
              <h3 class="product-title">
                <a href="<?= url('product/' . urlencode($pSlug)) ?>"><?= htmlspecialchars($pTitle) ?></a>
              </h3>
              <div class="product-price-row">
                <span class="current-price">$<?= number_format($pPrice, 2) ?></span>
                <?php if ($pCompare > $pPrice): ?>
                  <span class="old-price">$<?= number_format($pCompare, 2) ?></span>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ==========================================
     BEST SELLING VAPES & E-LIQUIDS SECTION
     ========================================== -->
<section class="products-showcase-section">
  <div class="container">
    
    <!-- Section Header with Title & Navigation Controls -->
    <div class="products-section-header">
      <div class="products-header-left">
        <h2 class="products-section-title">Best Selling Vapes &amp; E-Liquids</h2>
        <p class="products-section-subtitle">Most popular vapes and e-liquids chosen by our customers.</p>
      </div>
      <div class="products-header-right">
        <a href="<?= url('collection/best-sellers') ?>" class="view-all-link">
          View All Best Sellers
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </a>
        <div class="slider-nav-arrows">
          <button type="button" class="slider-arrow-btn prev-btn" aria-label="Previous">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
          </button>
          <button type="button" class="slider-arrow-btn next-btn" aria-label="Next">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </button>
        </div>
      </div>
    </div>

            <!-- Products Carousel Track (10 Products) -->
    <div class="products-grid">
      <?php $bestSellers = $bestSellers ?? []; ?>
      <?php if (!empty($bestSellers)): ?>
        <?php foreach ($bestSellers as $prod): ?>
          <?php
            $pId = $prod['id'];
            $pSlug = !empty($prod['slug']) ? $prod['slug'] : $pId;
            $pTitle = $prod['title'] ?? $prod['name'] ?? '';
            $pPrice = (float)($prod['base_price'] ?? 0);
            $pCompare = !empty($prod['compare_price']) ? (float)$prod['compare_price'] : 0;
            $pImg = !empty($prod['featured_image']) ? $prod['featured_image'] : 'assets/images/no-image.svg';
            if (!preg_match('~^(https?://|//|#)~i', $pImg)) {
                $pImg = url($pImg);
            }
            $ratingVal = round((float)($prod['rating_avg'] ?? 5));
            $reviewsCount = (int)($prod['reviews_count'] ?? 0);
          ?>
          <div class="product-card">
            <div class="product-card-media">
              <a href="<?= url('product/' . urlencode($pSlug)) ?>" class="product-img-wrap">
                <img src="<?= htmlspecialchars($pImg) ?>" alt="<?= htmlspecialchars($pTitle) ?>" class="product-img" onerror="this.onerror=null;this.src='<?= url('assets/images/no-image.svg') ?>';">
              </a>
              <div class="product-action-wrap">
                <button type="button" class="btn-add-to-cart" data-id="<?= $pId ?>" data-title="<?= htmlspecialchars($pTitle) ?>" data-price="<?= number_format($pPrice, 2, '.', '') ?>" data-image="<?= htmlspecialchars($pImg) ?>">ADD TO CART</button>
              </div>
            </div>
            <div class="product-card-info">
              <h3 class="product-title">
                <a href="<?= url('product/' . urlencode($pSlug)) ?>"><?= htmlspecialchars($pTitle) ?></a>
              </h3>
              <?php if ($reviewsCount > 0): ?>
                <div class="product-rating">
                  <div class="rating-stars">
                    <?php for ($s = 1; $s <= 5; $s++): ?>
                      <svg class="star-icon" viewBox="0 0 24 24" fill="<?= $s <= $ratingVal ? 'currentColor' : '#e2e8f0' ?>"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    <?php endfor; ?>
                  </div>
                  <span class="rating-count">(<?= $reviewsCount ?>)</span>
                </div>
              <?php endif; ?>
              <div class="product-price-row">
                <span class="current-price">$<?= number_format($pPrice, 2) ?></span>
                <?php if ($pCompare > $pPrice): ?>
                  <span class="old-price">$<?= number_format($pCompare, 2) ?></span>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>



<!-- ==========================================
     TWIN PROMO BANNERS SECTION
     ========================================== -->
<!-- ==========================================
     TWIN PROMO BANNERS SECTION
     ========================================== -->
<?php 
$activeTwins = array_filter($twinBanners ?? [], function($b) { return !empty($b['is_active']); });
if (!empty($activeTwins)): 
?>
<section class="twin-banners-section">
  <div class="container">
    <div class="twin-banners-grid">
      
      <?php foreach ($activeTwins as $card): 
        $cardImg = !empty($card['image_url']) ? $card['image_url'] : 'assets/images/no-image.svg';
        if (!preg_match('~^(https?://|//|#)~i', $cardImg)) {
            $cardImg = url($cardImg);
        }
        $cardBtnUrl = !empty($card['button_url']) ? (preg_match('~^(https?://|//|#)~i', $card['button_url']) ? $card['button_url'] : url($card['button_url'])) : url('category');
      ?>
      <div class="promo-banner-card">
        <img src="<?= htmlspecialchars($cardImg) ?>" alt="<?= htmlspecialchars(strip_tags($card['title'])) ?>" class="promo-banner-bg" onerror="this.onerror=null;this.src='<?= url('assets/images/no-image.svg') ?>';">
        <div class="promo-banner-content">
          <h3 class="promo-banner-title"><?= $card['title'] ?></h3>
          <?php if (!empty($card['subtitle'])): ?>
          <p class="promo-banner-subtitle"><?= $card['subtitle'] ?></p>
          <?php endif; ?>
          <a href="<?= htmlspecialchars($cardBtnUrl) ?>" class="promo-banner-btn">
            <?= htmlspecialchars(!empty($card['button_text']) ? $card['button_text'] : 'Shop Now') ?>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </a>
        </div>
      </div>
      <?php endforeach; ?>

    </div>
  </div>
</section>
<?php endif; ?>

<!-- ==========================================
     SHOP DISPOSABLE VAPES (4x4 GRID - 16 CARDS)
     ========================================== -->
<section class="disposables-grid-section">
  <div class="container">
    
    <!-- Section Header -->
    <div class="products-section-header">
      <div class="products-header-left">
        <h2 class="products-section-title">Shop Disposable Vapes</h2>
        <p class="products-section-subtitle">Pre-filled disposable vape devices with high puff counts.</p>
      </div>
      <div class="products-header-right">
        <a href="<?= url('collection/disposable') ?>" class="view-all-link">
          Shop All Disposables
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </a>
      </div>
    </div>

        <!-- 4 Columns Grid (16 Cards) -->
    <div class="disposables-grid">
      <?php $disposableVapes = $disposableVapes ?? []; ?>
      <?php if (!empty($disposableVapes)): ?>
        <?php foreach ($disposableVapes as $prod): ?>
          <?php
            $pId = $prod['id'];
            $pSlug = !empty($prod['slug']) ? $prod['slug'] : $pId;
            $pTitle = $prod['title'] ?? $prod['name'] ?? '';
            $pPrice = (float)($prod['base_price'] ?? 0);
            $pCompare = !empty($prod['compare_price']) ? (float)$prod['compare_price'] : 0;
            $pImg = !empty($prod['featured_image']) ? $prod['featured_image'] : 'assets/images/no-image.svg';
            if (!preg_match('~^(https?://|//|#)~i', $pImg)) {
                $pImg = url($pImg);
            }
            $ratingVal = round((float)($prod['rating_avg'] ?? 5));
            $reviewsCount = (int)($prod['reviews_count'] ?? 0);
            $isUsa = (!empty($prod['tags']) && stripos($prod['tags'], 'USA') !== false);
          ?>
          <div class="product-card">
            <div class="product-card-media">
              <?php if ($isUsa): ?>
                <div class="product-badges">
                  <span class="badge badge-usa">
                    <svg viewBox="0 0 16 12" width="12" height="9" style="border-radius:1px;"><rect width="16" height="12" fill="#b22234"/><rect y="1.85" width="16" height="1.85" fill="#fff"/><rect y="5.54" width="16" height="1.85" fill="#fff"/><rect y="9.23" width="16" height="1.85" fill="#fff"/><rect width="6.4" height="6.46" fill="#3c3b6e"/></svg>
                    USA
                  </span>
                </div>
              <?php endif; ?>
              <a href="<?= url('product/' . urlencode($pSlug)) ?>" class="product-img-wrap">
                <img src="<?= htmlspecialchars($pImg) ?>" alt="<?= htmlspecialchars($pTitle) ?>" class="product-img" onerror="this.onerror=null;this.src='<?= url('assets/images/no-image.svg') ?>';">
              </a>
              <div class="product-action-wrap">
                <button type="button" class="btn-add-to-cart" data-id="<?= $pId ?>" data-title="<?= htmlspecialchars($pTitle) ?>" data-price="<?= number_format($pPrice, 2, '.', '') ?>" data-image="<?= htmlspecialchars($pImg) ?>">ADD TO CART</button>
              </div>
            </div>
            <div class="product-card-info">
              <h3 class="product-title">
                <a href="<?= url('product/' . urlencode($pSlug)) ?>"><?= htmlspecialchars($pTitle) ?></a>
              </h3>
              <?php if ($reviewsCount > 0): ?>
                <div class="product-rating">
                  <div class="rating-stars">
                    <?php for ($s = 1; $s <= 5; $s++): ?>
                      <svg class="star-icon" viewBox="0 0 24 24" fill="<?= $s <= $ratingVal ? 'currentColor' : '#e2e8f0' ?>"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    <?php endfor; ?>
                  </div>
                  <span class="rating-count">(<?= $reviewsCount ?>)</span>
                </div>
              <?php endif; ?>
              <div class="product-price-row">
                <span class="current-price">$<?= number_format($pPrice, 2) ?></span>
                <?php if ($pCompare > $pPrice): ?>
                  <span class="old-price">$<?= number_format($pCompare, 2) ?></span>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ==========================================
     SHOP VAPE JUICES (1 ROW - 4 CARDS)
     ========================================== -->
<section class="disposables-grid-section vape-juices-section">
  <div class="container">
    
    <!-- Section Header -->
    <div class="products-section-header">
      <div class="products-header-left">
        <h2 class="products-section-title">Shop Vape Juices</h2>
        <p class="products-section-subtitle">E-liquids in nicotine salt, freebase and zero-nic options.</p>
      </div>
      <div class="products-header-right">
        <a href="<?= url('collection/e-liquids') ?>" class="view-all-link">
          Shop All Vape Juices
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </a>
      </div>
    </div>

    <!-- 4 Columns Grid (4 Cards) -->
    <div class="disposables-grid">
      <?php $vapeJuices = $vapeJuices ?? []; ?>
      <?php if (!empty($vapeJuices)): ?>
        <?php foreach ($vapeJuices as $prod): ?>
          <?php
            $pId = $prod['id'];
            $pSlug = !empty($prod['slug']) ? $prod['slug'] : $pId;
            $pTitle = $prod['title'] ?? $prod['name'] ?? '';
            $pPrice = (float)($prod['base_price'] ?? 0);
            $pCompare = !empty($prod['compare_price']) ? (float)$prod['compare_price'] : 0;
            $pImg = !empty($prod['featured_image']) ? $prod['featured_image'] : 'assets/images/no-image.svg';
            if (!preg_match('~^(https?://|//|#)~i', $pImg)) {
                $pImg = url($pImg);
            }
            $ratingVal = round((float)($prod['rating_avg'] ?? 5));
            $reviewsCount = (int)($prod['reviews_count'] ?? 0);
          ?>
          <div class="product-card">
            <div class="product-card-media">
              <a href="<?= url('product/' . urlencode($pSlug)) ?>" class="product-img-wrap">
                <img src="<?= htmlspecialchars($pImg) ?>" alt="<?= htmlspecialchars($pTitle) ?>" class="product-img juice-product-img" onerror="this.onerror=null;this.src='<?= url('assets/images/no-image.svg') ?>';">
              </a>
              <div class="product-action-wrap">
                <button type="button" class="btn-add-to-cart" data-id="<?= $pId ?>" data-title="<?= htmlspecialchars($pTitle) ?>" data-price="<?= number_format($pPrice, 2, '.', '') ?>" data-image="<?= htmlspecialchars($pImg) ?>">ADD TO CART</button>
              </div>
            </div>
            <div class="product-card-info">
              <h3 class="product-title">
                <a href="<?= url('product/' . urlencode($pSlug)) ?>"><?= htmlspecialchars($pTitle) ?></a>
              </h3>
              <?php if ($reviewsCount > 0): ?>
                <div class="product-rating">
                  <div class="rating-stars">
                    <?php for ($s = 1; $s <= 5; $s++): ?>
                      <svg class="star-icon" viewBox="0 0 24 24" fill="<?= $s <= $ratingVal ? 'currentColor' : '#e2e8f0' ?>"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    <?php endfor; ?>
                  </div>
                  <span class="rating-count">(<?= $reviewsCount ?>)</span>
                </div>
              <?php endif; ?>
              <div class="product-price-row">
                <span class="current-price">$<?= number_format($pPrice, 2) ?></span>
                <?php if ($pCompare > $pPrice): ?>
                  <span class="old-price">$<?= number_format($pCompare, 2) ?></span>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ==========================================
     SHOP NICOTINE POUCHES (1 ROW - 4 CARDS)
     ========================================== -->
<section class="disposables-grid-section pouches-section">
  <div class="container">
    
    <!-- Section Header -->
    <div class="products-section-header">
      <div class="products-header-left">
        <h2 class="products-section-title">Shop Nicotine Pouches</h2>
        <p class="products-section-subtitle">Tobacco-free nicotine pouches from ZYN, VELO and more.</p>
      </div>
      <div class="products-header-right">
        <a href="<?= url('collection/nicotine-pouches') ?>" class="view-all-link">
          Shop All Pouches
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </a>
      </div>
    </div>

    <!-- 4 Columns Grid (4 Cards) -->
    <div class="disposables-grid">
      <?php $nicotinePouches = $nicotinePouches ?? []; ?>
      <?php if (!empty($nicotinePouches)): ?>
        <?php foreach ($nicotinePouches as $prod): ?>
          <?php
            $pId = $prod['id'];
            $pSlug = !empty($prod['slug']) ? $prod['slug'] : $pId;
            $pTitle = $prod['title'] ?? $prod['name'] ?? '';
            $pPrice = (float)($prod['base_price'] ?? 0);
            $pCompare = !empty($prod['compare_price']) ? (float)$prod['compare_price'] : 0;
            $pImg = !empty($prod['featured_image']) ? $prod['featured_image'] : 'assets/images/no-image.svg';
            if (!preg_match('~^(https?://|//|#)~i', $pImg)) {
                $pImg = url($pImg);
            }
            $ratingVal = round((float)($prod['rating_avg'] ?? 5));
            $reviewsCount = (int)($prod['reviews_count'] ?? 0);
          ?>
          <div class="product-card">
            <div class="product-card-media">
              <a href="<?= url('product/' . urlencode($pSlug)) ?>" class="product-img-wrap">
                <img src="<?= htmlspecialchars($pImg) ?>" alt="<?= htmlspecialchars($pTitle) ?>" class="product-img pouch-product-img" onerror="this.onerror=null;this.src='<?= url('assets/images/no-image.svg') ?>';">
              </a>
              <div class="product-action-wrap">
                <button type="button" class="btn-add-to-cart" data-id="<?= $pId ?>" data-title="<?= htmlspecialchars($pTitle) ?>" data-price="<?= number_format($pPrice, 2, '.', '') ?>" data-image="<?= htmlspecialchars($pImg) ?>">ADD TO CART</button>
              </div>
            </div>
            <div class="product-card-info">
              <h3 class="product-title">
                <a href="<?= url('product/' . urlencode($pSlug)) ?>"><?= htmlspecialchars($pTitle) ?></a>
              </h3>
              <?php if ($reviewsCount > 0): ?>
                <div class="product-rating">
                  <div class="rating-stars">
                    <?php for ($s = 1; $s <= 5; $s++): ?>
                      <svg class="star-icon" viewBox="0 0 24 24" fill="<?= $s <= $ratingVal ? 'currentColor' : '#e2e8f0' ?>"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    <?php endfor; ?>
                  </div>
                  <span class="rating-count">(<?= $reviewsCount ?>)</span>
                </div>
              <?php endif; ?>
              <div class="product-price-row">
                <span class="current-price">$<?= number_format($pPrice, 2) ?></span>
                <?php if ($pCompare > $pPrice): ?>
                  <span class="old-price">$<?= number_format($pCompare, 2) ?></span>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ==========================================
     SHOP HEMP & THC PRODUCTS (1 ROW - 4 CARDS)
     ========================================== -->
<section class="disposables-grid-section hemp-thc-section">
  <div class="container">
    
    <!-- Section Header -->
    <div class="products-section-header">
      <div class="products-header-left">
        <h2 class="products-section-title">Shop Hemp &amp; THC Products</h2>
        <p class="products-section-subtitle">THCA vapes, THC-P disposables, hemp prerolls &amp; more.</p>
      </div>
      <div class="products-header-right">
        <a href="<?= url('collection/lifestyle') ?>" class="view-all-link">
          Shop All Lifestyle
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </a>
      </div>
    </div>

    <!-- 4 Columns Grid (4 Cards) -->
    <div class="disposables-grid">
<?php $hempThc = $hempThc ?? []; ?>
      <?php if (!empty($hempThc)): ?>
        <?php foreach ($hempThc as $prod): ?>
          <?php
            $pId = $prod['id'];
            $pSlug = !empty($prod['slug']) ? $prod['slug'] : $pId;
            $pTitle = $prod['title'] ?? $prod['name'] ?? '';
            $pPrice = (float)($prod['base_price'] ?? 0);
            $pCompare = !empty($prod['compare_price']) ? (float)$prod['compare_price'] : 0;
            $pImg = !empty($prod['featured_image']) ? $prod['featured_image'] : 'assets/images/no-image.svg';
            if (!preg_match('~^(https?://|//|#)~i', $pImg)) {
                $pImg = url($pImg);
            }
          ?>
          <div class="product-card">
            <div class="product-card-media">
              <a href="<?= url('product/' . urlencode($pSlug)) ?>" class="product-img-wrap">
                <img src="<?= htmlspecialchars($pImg) ?>" alt="<?= htmlspecialchars($pTitle) ?>" class="product-img hemp-product-img" onerror="this.onerror=null;this.src='<?= url('assets/images/no-image.svg') ?>';">
              </a>
              <div class="product-action-wrap">
                <button type="button" class="btn-add-to-cart" data-id="<?= $pId ?>" data-title="<?= htmlspecialchars($pTitle) ?>" data-price="<?= number_format($pPrice, 2, '.', '') ?>" data-image="<?= htmlspecialchars($pImg) ?>">ADD TO CART</button>
              </div>
            </div>
            <div class="product-card-info">
              <h3 class="product-title">
                <a href="<?= url('product/' . urlencode($pSlug)) ?>"><?= htmlspecialchars($pTitle) ?></a>
              </h3>
              <div class="product-price-row">
                <span class="current-price">$<?= number_format($pPrice, 2) ?></span>
                <?php if ($pCompare > $pPrice): ?>
                  <span class="old-price">$<?= number_format($pCompare, 2) ?></span>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>


<!-- ==========================================
     SEO TRUSTED VAPE STORE CONTENT SECTION
     ========================================== -->
<?php if (!isset($seoContent['is_active']) || !empty($seoContent['is_active'])): ?>
<section class="store-seo-content-section">
  <div class="container">
    <div class="seo-content-wrapper">
      <?php if (!empty($seoContent['title'])): ?>
      <h2 class="seo-content-title"><?= htmlspecialchars($seoContent['title']) ?></h2>
      <?php endif; ?>

      <div class="seo-content-body">
        <?= $seoContent['content'] ?? '' ?>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>


<!-- ==========================================
     FROM OUR BLOG SECTION
     ========================================== -->
<section class="blog-showcase-section">
  <div class="container">
    
    <!-- Section Header with Title & Navigation Controls -->
    <div class="products-section-header">
      <div class="products-header-left">
        <h2 class="products-section-title">From Our Blog</h2>
        <p class="products-section-subtitle">Expert guides, reviews, and the latest vaping news.</p>
      </div>
      <div class="products-header-right">
        <a href="<?= url('blog') ?>" class="view-all-link">
          Read All Posts
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </a>
        <div class="slider-nav-arrows">
          <button type="button" class="slider-arrow-btn prev-btn blog-prev-btn" aria-label="Previous">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
          </button>
          <button type="button" class="slider-arrow-btn next-btn blog-next-btn" aria-label="Next">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Blog Cards Grid / Slider Track -->
    <div class="blog-cards-grid">
      <?php $latestBlogs = $latestBlogs ?? []; ?>
      <?php if (!empty($latestBlogs)): ?>
        <?php foreach ($latestBlogs as $blog): 
          $blogSlug = !empty($blog['slug']) ? $blog['slug'] : $blog['id'];
          $blogUrl = url('blog-detail?slug=' . urlencode($blogSlug));
          $blogImg = !empty($blog['featured_image_url']) ? $blog['featured_image_url'] : 'assets/images/no-image.svg';
          if (!preg_match('~^(https?://|//|#)~i', $blogImg)) {
              $blogImg = url($blogImg);
          }
          $blogDate = blog_time_display($blog['published_at'] ?? '');
          $blogCat = !empty($blog['category_name']) ? $blog['category_name'] : 'Guide';
        ?>
          <article class="blog-card">
            <a href="<?= htmlspecialchars($blogUrl) ?>" class="blog-card-thumb-link">
              <div class="blog-card-media">
                <img src="<?= htmlspecialchars($blogImg) ?>" alt="<?= htmlspecialchars($blog['title']) ?>" class="blog-card-img" onerror="this.onerror=null;this.src='<?= url('assets/images/no-image.svg') ?>';">
              </div>
            </a>
            <div class="blog-card-content">
              <span class="blog-card-date"><?= htmlspecialchars($blogDate) ?></span>
              <h3 class="blog-card-title">
                <a href="<?= htmlspecialchars($blogUrl) ?>"><?= htmlspecialchars($blog['title']) ?></a>
              </h3>
              <p class="blog-card-excerpt"><?= htmlspecialchars(mb_strimwidth($blog['excerpt'] ?? '', 0, 125, '...')) ?></p>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>
</section>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
