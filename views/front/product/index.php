<?php

/**
 * PRODUCT DETAILS PAGE (product.php)
 * Dynamic product details view with gallery, specs, flavor selector, buy box, and related products.
 */

// Fallback if accessed directly or via include without controller context
if (!isset($product) || empty($product)) {
  $identifier = $_GET['slug'] ?? ($_GET['p'] ?? ($_GET['id'] ?? null));
  if ($identifier) {
    $product = \App\Models\Product::find($identifier);
  }
  if (!$product) {
    $product = \App\Models\Product::find(1);
  }
}

$productTitle = $product['title'] ?? ($product['name'] ?? 'Product Details');
$brandName = $product['brand'] ?? 'Vape Brand';
$brandSlug = $product['brand_slug'] ?? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $brandName)));
$categoryName = $product['category'] ?? 'Disposables';
$categorySlug = $product['category_slug'] ?? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $categoryName)));
$sku = !empty($product['sku']) ? $product['sku'] : ('SKU-' . ($product['id'] ?? '100'));
$stockStatus = !empty($product['stock_status']) ? ucfirst(str_replace('_', ' ', $product['stock_status'])) : (!empty($product['status']) ? ucfirst($product['status']) : 'In Stock');

// Pricing & Discount calculation
$basePrice = (float) ($product['base_price'] ?? $product['price'] ?? 0);
$comparePrice = !empty($product['compare_price']) ? (float) $product['compare_price'] : 0;
$hasDiscount = ($comparePrice > $basePrice);
$discountPct = $hasDiscount ? round((($comparePrice - $basePrice) / $comparePrice) * 100) : 0;

$page_title       = $page_title ?? ($productTitle . ' | Infinity Vape Shop');
$meta_description = $meta_description ?? (!empty($product['meta_desc']) ? $product['meta_desc'] : (!empty($product['short_description']) ? $product['short_description'] : ''));
$canonical_url    = $canonical_url ?? url('product/' . urlencode(!empty($product['slug']) ? $product['slug'] : $product['id']));
$page_js          = 'product';

include dirname(__DIR__) . '/includes/header.php';
?>

<style>
/* Product Image Gallery & Layout (50% / 50% Grid) */
.product-main-grid {
  display: grid !important;
  grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
  gap: 44px !important;
  align-items: start !important;
}

.product-gallery-col {
  width: 100% !important;
  min-width: 0 !important;
  max-width: 100% !important;
  margin: 0 !important;
}

.product-gallery-sticky {
  position: sticky !important;
  top: 24px !important;
  width: 100% !important;
  min-width: 0 !important;
  max-width: 100% !important;
}

.product-main-image-card {
  position: relative !important;
  width: 100% !important;
  max-width: 100% !important;
  margin: 0 auto !important;
  aspect-ratio: 1 / 1 !important;
  background-color: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 16px !important;
  padding: 24px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  overflow: hidden !important;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04) !important;
}

/* Main Large Image Hover Navigation Buttons (< and >) */
.main-image-nav-btn {
  position: absolute !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
  width: 44px !important;
  height: 44px !important;
  border-radius: 50% !important;
  background: rgba(255, 255, 255, 0.95) !important;
  border: 1px solid #cbd5e1 !important;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12) !important;
  color: #0f172a !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  cursor: pointer !important;
  z-index: 10 !important;
  opacity: 0 !important;
  transition: all 0.25s ease !important;
  backdrop-filter: blur(4px) !important;
}

.product-main-image-card:hover .main-image-nav-btn {
  opacity: 1 !important;
}

.main-image-nav-btn:hover {
  background: #0f172a !important;
  color: #ffffff !important;
  border-color: #0f172a !important;
  transform: translateY(-50%) scale(1.08) !important;
  box-shadow: 0 6px 18px rgba(15, 23, 42, 0.25) !important;
}

.main-image-nav-btn.prev {
  left: 16px !important;
}

.main-image-nav-btn.next {
  right: 16px !important;
}

/* Horizontal Scrollable Thumbnails Container */
.product-thumbnails-wrap {
  position: relative !important;
  width: 100% !important;
  max-width: 100% !important;
  margin: 16px auto 0 !important;
  padding: 0 !important;
  box-sizing: border-box !important;
  display: flex !important;
  align-items: center !important;
  transition: padding 0.15s ease !important;
}

.product-thumbnails-wrap.has-scroll {
  padding: 0 38px !important; /* Space for arrow buttons ONLY when scroll is needed */
}

.product-thumbnails-track {
  display: flex !important;
  align-items: center !important;
  justify-content: flex-start !important;
  gap: 10px !important;
  width: 100% !important;
  max-width: 100% !important;
  overflow-x: auto !important;
  overflow-y: hidden !important;
  padding: 4px 2px !important;
  scroll-behavior: smooth !important;
  -webkit-overflow-scrolling: touch !important;
  /* Scrollbar completely hidden */
  scrollbar-width: none !important;
  -ms-overflow-style: none !important;
}

.product-thumbnails-track::-webkit-scrollbar {
  display: none !important;
  width: 0 !important;
  height: 0 !important;
}

.product-thumb-btn {
  width: 76px !important;
  height: 76px !important;
  flex: 0 0 76px !important;
  background-color: #ffffff !important;
  border: 2px solid #e2e8f0 !important;
  border-radius: 10px !important;
  padding: 6px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  cursor: pointer !important;
  transition: all 0.2s ease !important;
  overflow: hidden !important;
}

.product-thumb-btn img {
  max-width: 100% !important;
  max-height: 100% !important;
  object-fit: contain !important;
}

.product-thumb-btn:hover {
  border-color: #94a3b8 !important;
  transform: translateY(-2px) !important;
}

.product-thumb-btn.active {
  border-color: #f25c27 !important;
  box-shadow: 0 0 0 2px rgba(242, 92, 39, 0.25) !important;
  background-color: #ffffff !important;
}

/* Thumbnail Navigation Arrows (Visible ONLY when thumbnails exceed container width) */
.thumb-scroll-btn {
  display: none !important;
  position: absolute !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
  width: 32px !important;
  height: 32px !important;
  border-radius: 50% !important;
  background: #ffffff !important;
  border: 1px solid #cbd5e1 !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12) !important;
  color: #0f172a !important;
  align-items: center !important;
  justify-content: center !important;
  cursor: pointer !important;
  z-index: 5 !important;
  pointer-events: auto !important;
  transition: all 0.2s ease !important;
}

.product-thumbnails-wrap.has-scroll .thumb-scroll-btn {
  display: flex !important;
}

.thumb-scroll-btn:hover {
  background: #0f172a !important;
  color: #ffffff !important;
  border-color: #0f172a !important;
  box-shadow: 0 4px 12px rgba(15, 23, 42, 0.25) !important;
  transform: translateY(-50%) scale(1.08) !important;
}

.thumb-scroll-btn.thumb-scroll-prev {
  left: 0 !important;
}

.thumb-scroll-btn.thumb-scroll-next {
  right: 0 !important;
}

.thumb-scroll-btn:disabled,
.thumb-scroll-btn.is-disabled {
  opacity: 0.35 !important;
  cursor: not-allowed !important;
  pointer-events: none !important;
  background: #f8fafc !important;
  border-color: #e2e8f0 !important;
  color: #94a3b8 !important;
}

@media (max-width: 991px) {
  .product-main-grid {
    grid-template-columns: 1fr !important;
    gap: 36px !important;
  }
  .main-image-nav-btn {
    opacity: 0.9 !important;
  }
}

/* Modern eCommerce Variant Options & Badges */
.product-variants-wrapper {
  margin-top: 6px;
  margin-bottom: 22px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.variant-option-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.variant-option-header {
  display: flex;
  align-items: baseline;
  gap: 6px;
  font-size: 0.92rem;
  line-height: 1.25;
}

.variant-option-label {
  font-weight: 700;
  color: #082142;
  letter-spacing: -0.01em;
}

.variant-selected-value {
  font-weight: 600;
  color: #475569;
}

.variant-badges-list {
  display: flex;
  flex-wrap: wrap;
  gap: 8px 10px;
}

.variant-badge-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 8px 16px;
  min-height: 38px;
  border-radius: 6px;
  border: 1.5px solid #cbd5e1;
  background-color: #ffffff;
  color: #1e293b;
  font-size: 0.88rem;
  font-weight: 600;
  font-family: inherit;
  cursor: pointer;
  outline: none;
  -webkit-tap-highlight-color: transparent;
  user-select: none;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
  transition: background-color 0.12s ease, border-color 0.12s ease, color 0.12s ease;
}

.variant-badge-btn:hover {
  border-color: #082142;
  color: #082142;
}

/* Active Selected Badge State (Rock Solid, Zero Layout Shift) */
.variant-badge-btn.active {
  background-color: #082142 !important;
  border-color: #082142 !important;
  color: #ffffff !important;
  font-weight: 600 !important;
  box-shadow: 0 2px 6px rgba(8, 33, 66, 0.2) !important;
}

/* Out of Stock & Disabled Pill Style */
.variant-badge-btn:disabled,
.variant-badge-btn.is-disabled,
.variant-badge-btn.is-out-of-stock {
  opacity: 0.38 !important;
  background-color: #f8fafc !important;
  border-color: #bec4cb !important;
  color: #25292e !important;
  text-decoration: line-through !important;
  cursor: not-allowed !important;
  pointer-events: none !important;
  box-shadow: none !important;
  user-select: none !important;
}

.variant-badge-btn:disabled:hover,
.variant-badge-btn.is-disabled:hover,
.variant-badge-btn.is-out-of-stock:hover {
  border-color: #e2e8f0 !important;
  color: #94a3b8 !important;
  background-color: #f8fafc !important;
}

/* Out of Stock Add to Cart & Buy Now Buttons */
.btn-product-add-cart:disabled,
.btn-product-add-cart.is-disabled,
.btn-product-buy-now:disabled,
.btn-product-buy-now.is-disabled {
  opacity: 0.55 !important;
  cursor: not-allowed !important;
  pointer-events: none !important;
  box-shadow: none !important;
}

.btn-product-add-cart:disabled,
.btn-product-add-cart.is-disabled {
  background-color: #cbd5e1 !important;
  color: #64748b !important;
  border-color: #cbd5e1 !important;
}

.btn-product-buy-now:disabled,
.btn-product-buy-now.is-disabled {
  background-color: #94a3b8 !important;
  color: #f1f5f9 !important;
}
</style>

<!-- ==========================================
     1. BREADCRUMB NAVIGATION
     ========================================== -->
<nav class="product-breadcrumb-nav" aria-label="Breadcrumb">
  <div class="container">
    <ol class="breadcrumb-list">
      <li class="breadcrumb-item">
        <a href="<?= url() ?>">Home</a>
      </li>
      <li class="breadcrumb-separator" aria-hidden="true">
        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </li>
      <li class="breadcrumb-item">
        <a href="<?= class_exists('\\App\\Models\\Collection') ? \App\Models\Collection::getUrl($categorySlug) : url('collection/' . urlencode($categorySlug)) ?>"><?= htmlspecialchars($categoryName) ?></a>
      </li>
      <li class="breadcrumb-separator" aria-hidden="true">
        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </li>
      <li class="breadcrumb-item active" aria-current="page">
        <?= htmlspecialchars($productTitle) ?>
      </li>
    </ol>
  </div>
</nav>

<?php if (!empty($isDraftPreview)): ?>
<div style="background: #fffbeb; border-bottom: 1px solid #fef3c7; color: #92400e; padding: 10px 16px; text-align: center; font-size: 13.5px; font-weight: 600;">
  ⚠️ <strong>Draft Preview Mode:</strong> This product is currently in Draft status and is completely hidden from customers on the public storefront.
</div>
<?php endif; ?>

<!-- ==========================================
     2. PRODUCT MAIN SECTION (GALLERY & BUY BOX)
     ========================================== -->
<section class="product-main-section">
  <div class="container">
    <div class="product-main-grid">

      <!-- ========================================
           LEFT COLUMN: IMAGE GALLERY
           ======================================== -->
      <div class="product-gallery-col">
        <div class="product-gallery-sticky">
          
          <?php
          $galleryList = [];
          if (!empty($productImages)) {
            foreach ($productImages as $pImg) {
              $galleryList[] = $pImg['image_url'];
            }
          }
          if (empty($galleryList)) {
            $galleryList[] = !empty($product['featured_image']) ? $product['featured_image'] : (!empty($product['img']) ? $product['img'] : 'assets/images/vape-image.webp');
          }
          $mainImgSrc = $galleryList[0];
          ?>
          <!-- Main Large Image Card -->
          <div class="product-main-image-card">
            <?php if (count($galleryList) > 1): ?>
            <button type="button" class="main-image-nav-btn prev" id="mainImgPrev" aria-label="Previous image">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </button>
            <?php endif; ?>

            <div class="main-image-viewport">
              <img 
                id="productMainImage" 
                src="<?= asset($mainImgSrc) ?>" 
                alt="<?= htmlspecialchars($productTitle) ?>" 
                class="product-main-img"
                onerror="this.onerror=null;this.src='<?= asset('assets/images/vape-image.webp') ?>';"
              >
            </div>

            <?php if (count($galleryList) > 1): ?>
            <button type="button" class="main-image-nav-btn next" id="mainImgNext" aria-label="Next image">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>
            <?php endif; ?>
          </div>

          <!-- Thumbnails Row (Scrollable Gallery with Responsive Dynamic Buttons) -->
          <?php if (count($galleryList) > 1): ?>
          <?php $hasInitialScroll = count($galleryList) > 5; ?>
          <div class="product-thumbnails-wrap <?= $hasInitialScroll ? 'has-scroll' : '' ?>" id="productThumbnailsWrap">
            <button type="button" class="thumb-scroll-btn thumb-scroll-prev" id="thumbScrollPrev" aria-label="Previous thumbnails">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </button>
            <div class="product-thumbnails-track" id="productThumbnailsTrack">
              <?php foreach ($galleryList as $tIdx => $tImg): ?>
                <button type="button" class="product-thumb-btn <?= $tIdx === 0 ? 'active' : '' ?>" data-index="<?= $tIdx ?>" data-img="<?= asset($tImg) ?>" aria-label="View product image <?= $tIdx + 1 ?>">
                  <img src="<?= asset($tImg) ?>" alt="<?= htmlspecialchars($productTitle) ?> thumbnail <?= $tIdx + 1 ?>" onerror="this.onerror=null;this.src='<?= asset('assets/images/vape-image.webp') ?>';">
                </button>
              <?php endforeach; ?>
            </div>
            <button type="button" class="thumb-scroll-btn thumb-scroll-next" id="thumbScrollNext" aria-label="Next thumbnails">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>
          </div>
          <?php endif; ?>

        </div>
      </div>

      <!-- ========================================
           RIGHT COLUMN: PRODUCT DETAILS & BUY BOX
           ======================================== -->
      <div class="product-info-col">
        
        <!-- Brand Name -->
        <div class="product-brand-tag">
          <a href="<?= url('collection/' . urlencode($categorySlug) . '?brand=' . urlencode(strtolower($brandName))) ?>">
            <?= htmlspecialchars(strtoupper($brandName)) ?>
          </a>
        </div>

        <!-- Product Title -->
        <h1 class="product-details-title">
          <?= htmlspecialchars($productTitle) ?>
        </h1>

        <!-- SKU & Star Rating Row -->
        <?php
        $reviewCount = !empty($productReviews) ? count($productReviews) : 0;
        $avgRating = 5;
        if ($reviewCount > 0) {
          $sumRating = 0;
          foreach ($productReviews as $pr) {
            $sumRating += (int) ($pr['rating'] ?? 5);
          }
          $avgRating = (int) round($sumRating / $reviewCount);
        }
        ?>
        <div class="product-meta-row">
          <span class="product-sku">SKU: <?= htmlspecialchars($sku) ?></span>
          <div class="product-rating-wrap">
            <div class="rating-stars" aria-label="<?= $avgRating ?> out of 5 stars">
              <?= $reviewCount > 0 ? (str_repeat('★', $avgRating) . str_repeat('☆', 5 - $avgRating)) : '☆☆☆☆☆' ?>
            </div>
            <a href="#tab-reviews" class="rating-count"><?= $reviewCount ?> <?= $reviewCount === 1 ? 'Review' : 'Reviews' ?></a>
          </div>
        </div>

        <!-- Pricing Row -->
        <div class="product-price-section">
          <?php if ($hasDiscount): ?>
            <span class="product-price-old">$<?= number_format($comparePrice, 2) ?></span>
          <?php endif; ?>
          <span class="product-price-current">$<?= number_format($basePrice, 2) ?></span>
        </div>

        <!-- Stock Status Strip -->
        <?php
        $isOutOfStock = strtolower(str_replace(' ', '_', $product['stock_status'] ?? '')) === 'out_of_stock' || (isset($product['stock_quantity']) && (int) $product['stock_quantity'] <= 0);
        ?>
        <div class="product-stock-status <?= $isOutOfStock ? 'out-of-stock' : 'in-stock' ?>">
          <span class="stock-dot"></span>
          <span class="stock-text">
            <strong><?= $isOutOfStock ? 'Out of Stock' : 'In Stock' ?></strong>
          </span>
        </div>

        <!-- Short Description Paragraph -->
        <?php if (!empty(trim(strip_tags($product['short_desc'] ?? '')))): ?>
        <p class="product-short-desc">
          <?= nl2br(htmlspecialchars(trim($product['short_desc']))) ?>
        </p>
        <?php endif; ?>

        <!-- Dynamic Spec / Feature Boxes Grid -->
        <?php if (!empty($productHighlights)): ?>
        <div class="product-spec-boxes-grid">
          <?php foreach ($productHighlights as $highlight): ?>
          <div class="spec-box">
            <div class="spec-icon">
              <?php
              $icon = strtolower($highlight['icon_type'] ?? $highlight['label'] ?? '');
              if (strpos($icon, 'puff') !== false):
                ?>
              <!-- Puffs Vapor Icon -->
              <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 3v3m6.366 1.634l-2.12 2.12M21 12h-3m-1.634 6.366l-2.12-2.12M12 21v-3m-6.366-1.634l2.12-2.12M3 12h3m1.634-6.366l2.12 2.12"></path>
              </svg>
              <?php elseif (strpos($icon, 'nic') !== false || strpos($icon, 'strength') !== false): ?>
              <!-- Nicotine Icon -->
              <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9"></circle>
                <path d="M12 3a9 9 0 0 0 0 18z" fill="currentColor"></path>
              </svg>
              <?php elseif (strpos($icon, 'batt') !== false): ?>
              <!-- Battery Icon -->
              <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="7" width="16" height="10" rx="2" ry="2"></rect>
                <line x1="22" y1="11" x2="22" y2="13"></line>
                <line x1="6" y1="11" x2="6" y2="13"></line>
                <line x1="10" y1="11" x2="10" y2="13"></line>
              </svg>
              <?php else: ?>
              <!-- Capacity / Flask Icon -->
              <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10 2v5l-4.5 9A4 4 0 0 0 9 21h6a4 4 0 0 0 3.5-5L14 7V2"></path>
                <line x1="8.5" y1="14" x2="15.5" y2="14"></line>
              </svg>
              <?php endif; ?>
            </div>
            <div class="spec-details">
              <span class="spec-label"><?= htmlspecialchars($highlight['label']) ?></span>
              <span class="spec-value"><?= htmlspecialchars($highlight['value']) ?></span>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Dynamic Multi-Option Variant Badges / Selectors -->
        <?php if (!empty($productVariants)): ?>
        <?php
        // 1. Resolve configured option names from database (e.g. ["Flavor", "Nicotine Strength"])
        $configuredOptionNames = [];
        if (!empty($product['option_names'])) {
            if (is_array($product['option_names'])) {
                $configuredOptionNames = $product['option_names'];
            } else {
                $decoded = json_decode($product['option_names'], true);
                if (is_array($decoded)) {
                    $configuredOptionNames = $decoded;
                }
            }
        }

        // 2. Detect number of dimensions/parts in variant names (e.g. "Banana Ice / 20mg")
        $maxParts = 1;
        foreach ($productVariants as $pv) {
            $parts = array_map('trim', explode(' / ', $pv['variant_name']));
            if (count($parts) > $maxParts) {
                $maxParts = count($parts);
            }
        }

        // 3. Assign human-friendly labels for each option dimension
        $optionLabels = [];
        for ($i = 0; $i < $maxParts; $i++) {
            if (!empty($configuredOptionNames[$i])) {
                $optionLabels[$i] = $configuredOptionNames[$i];
            } else {
                if ($i === 0) {
                    $optionLabels[$i] = 'Flavor';
                } elseif ($i === 1) {
                    $optionLabels[$i] = 'Strength';
                } elseif ($i === 2) {
                    $optionLabels[$i] = 'Size';
                } else {
                    $optionLabels[$i] = 'Option ' . ($i + 1);
                }
            }
        }

        // 4. Collect unique values for each option level while preserving order
        $optionValuesMap = array_fill(0, $maxParts, []);
        foreach ($productVariants as $pv) {
            $parts = array_map('trim', explode(' / ', $pv['variant_name']));
            for ($i = 0; $i < $maxParts; $i++) {
                $val = $parts[$i] ?? ($i === 0 ? $pv['variant_name'] : '');
                if ($val !== '' && !in_array($val, $optionValuesMap[$i])) {
                    $optionValuesMap[$i][] = $val;
                }
            }
        }

        $parsedOptions = [];
        for ($i = 0; $i < $maxParts; $i++) {
            if (!empty($optionValuesMap[$i])) {
                $parsedOptions[] = [
                    'index'  => $i,
                    'name'   => $optionLabels[$i],
                    'values' => $optionValuesMap[$i]
                ];
            }
        }

        // Resolve default in-stock variant combination
        $defaultVariant = null;
        foreach ($productVariants as $pv) {
            $st = isset($pv['stock_quantity']) ? (int)$pv['stock_quantity'] : 0;
            if ($st > 0) {
                $defaultVariant = $pv;
                break;
            }
        }
        if (!$defaultVariant && !empty($productVariants)) {
            $defaultVariant = $productVariants[0];
        }

        $defaultParts = [];
        if ($defaultVariant) {
            $defaultParts = array_map('trim', explode(' / ', $defaultVariant['variant_name']));
        }
        ?>

        <div class="product-variants-wrapper" id="productVariantsWrapper">
          <!-- Hidden fallback select ensuring 100% backward compatibility with cart-drawer.js and checkout -->
          <select id="flavorSelect" class="product-flavor-select" style="display: none;" aria-hidden="true" tabindex="-1">
            <?php
            foreach ($productVariants as $idx => $variant):
              $vPrice = !empty($variant['price']) ? (float) $variant['price'] : $basePrice;
              $vCompare = !empty($variant['compare_price']) ? (float) $variant['compare_price'] : $comparePrice;
              $vSku = !empty($variant['sku']) ? $variant['sku'] : $sku;
              $vStock = isset($variant['stock_quantity']) ? (int) $variant['stock_quantity'] : 0;
              $vImage = !empty($variant['image_url']) ? asset($variant['image_url']) : '';
              ?>
            <option 
              value="<?= htmlspecialchars($variant['variant_name']) ?>" 
              data-price="<?= number_format($vPrice, 2, '.', '') ?>"
              data-compare-price="<?= $vCompare ? number_format($vCompare, 2, '.', '') : '' ?>"
              data-sku="<?= htmlspecialchars($vSku) ?>"
              data-stock="<?= $vStock ?>"
              data-image="<?= htmlspecialchars($vImage) ?>"
              <?= $idx === 0 ? 'selected' : '' ?>
            >
              <?= htmlspecialchars($variant['variant_name']) ?>
            </option>
            <?php endforeach; ?>
          </select>

          <!-- Store JSON data for instant client-side switching -->
          <script id="productVariantsJson" type="application/json">
            <?= json_encode($productVariants, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
          </script>

          <!-- Dynamic Option Groups (Option 1: Flavor badges, Option 2: Strength badges, etc.) -->
          <?php foreach ($parsedOptions as $opt): 
            $currOptIdx = $opt['index'];
            $selectedOptVal = $defaultParts[$currOptIdx] ?? ($opt['values'][0] ?? '');
          ?>
          <div class="variant-option-group" data-option-index="<?= $currOptIdx ?>">
            <div class="variant-option-header">
              <span class="variant-option-label"><?= htmlspecialchars($opt['name']) ?>:</span>
              <span class="variant-selected-value" id="selectedVal-<?= $currOptIdx ?>">
                <?= htmlspecialchars($selectedOptVal) ?>
              </span>
            </div>
            <div class="variant-badges-list" role="radiogroup" aria-label="<?= htmlspecialchars($opt['name']) ?>">
              <?php foreach ($opt['values'] as $vIdx => $val): 
                $isOutStock = false;
                if ($currOptIdx === 0) {
                    // Check if this Option 0 value has ANY stock in any variant
                    $hasAny = false;
                    foreach ($productVariants as $pv) {
                        $pParts = array_map('trim', explode(' / ', $pv['variant_name']));
                        if (($pParts[0] ?? $pv['variant_name']) === $val && (int)($pv['stock_quantity'] ?? 0) > 0) {
                            $hasAny = true;
                            break;
                        }
                    }
                    $isOutStock = !$hasAny;
                } elseif ($currOptIdx === 1 && !empty($defaultParts[0])) {
                    // Check if combination of default Option 0 + this Option 1 is in stock
                    $hasStock = false;
                    foreach ($productVariants as $pv) {
                        $pParts = array_map('trim', explode(' / ', $pv['variant_name']));
                        if (($pParts[0] ?? '') === $defaultParts[0] && ($pParts[1] ?? '') === $val && (int)($pv['stock_quantity'] ?? 0) > 0) {
                            $hasStock = true;
                            break;
                        }
                    }
                    $isOutStock = !$hasStock;
                }
                $isActive = ($val === $selectedOptVal) && !$isOutStock;
              ?>
              <button 
                type="button" 
                class="variant-badge-btn <?= $isActive ? 'active' : '' ?> <?= $isOutStock ? 'is-out-of-stock is-disabled' : '' ?>" 
                data-option-index="<?= $currOptIdx ?>" 
                data-value="<?= htmlspecialchars($val) ?>"
                aria-pressed="<?= $isActive ? 'true' : 'false' ?>"
                <?= $isOutStock ? 'disabled aria-disabled="true" title="Out of stock"' : '' ?>
              >
                <?= htmlspecialchars($val) ?>
              </button>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Actions Row: Quantity Selector + Add to Cart -->
        <div class="product-purchase-row" data-in-stock="<?= $isOutOfStock ? '0' : '1' ?>">
          
          <!-- Quantity Counter -->
          <div class="product-qty-selector" style="<?= $isOutOfStock ? 'opacity: 0.5; pointer-events: none;' : '' ?>">
            <button type="button" class="qty-btn" id="qtyMinus" aria-label="Decrease quantity" <?= $isOutOfStock ? 'disabled' : '' ?>>&minus;</button>
            <input type="number" id="productQuantity" class="qty-input" value="1" min="1" max="99" readonly <?= $isOutOfStock ? 'disabled' : '' ?>>
            <button type="button" class="qty-btn" id="qtyPlus" aria-label="Increase quantity" <?= $isOutOfStock ? 'disabled' : '' ?>>&plus;</button>
          </div>

          <!-- Add to Cart (Gold/Yellow Button) -->
          <button 
            type="button" 
            class="btn-product-add-cart <?= $isOutOfStock ? 'is-disabled' : '' ?>" 
            id="btnProductAddToCart"
            <?= $isOutOfStock ? 'disabled style="opacity: 0.6; cursor: not-allowed;"' : '' ?>
          >
            <?= $isOutOfStock ? 'Out of Stock' : 'Add to cart' ?>
          </button>
        </div>

        <!-- Buy It Now Button (Black Solid Button) -->
        <button 
          type="button" 
          class="btn-product-buy-now <?= $isOutOfStock ? 'is-disabled' : '' ?>" 
          id="btnProductBuyNow"
          <?= $isOutOfStock ? 'disabled style="opacity: 0.6; cursor: not-allowed;"' : '' ?>
        >
          Buy it now
        </button>

        <!-- Limited-Time Bundle Promotional Banner (Dynamic per product) -->
        <?php if (!empty($product['bundle_banner_active']) && !empty($product['bundle_banner_img'])): ?>
        <div class="product-promo-banner-card">
          <a href="<?= !empty($product['bundle_banner_link']) ? (preg_match('/^https?:\/\//i', $product['bundle_banner_link']) ? htmlspecialchars($product['bundle_banner_link']) : url(htmlspecialchars(ltrim($product['bundle_banner_link'], '/')))) : 'javascript:void(0);' ?>" class="product-promo-link">
            <img 
              src="<?= asset($product['bundle_banner_img']) ?>" 
              alt="<?= htmlspecialchars($productTitle) ?> Promotional Deal" 
              class="product-promo-img"
              onerror="this.onerror=null;this.src='<?= asset('assets/images/limited-time.webp') ?>';"
            >
          </a>
        </div>
        <?php endif; ?>

        <!-- Trust / Features Bar -->
        <div class="product-trust-features-strip">
          
          <div class="trust-feature-item">
            <div class="trust-feature-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
            </div>
            <div class="trust-feature-text">
              <span class="trust-feature-title">Fast Shipping</span>
              <span class="trust-feature-desc">We ship same day</span>
            </div>
          </div>

          <div class="trust-feature-item">
            <div class="trust-feature-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                <polyline points="9 12 11 14 15 10"></polyline>
              </svg>
            </div>
            <div class="trust-feature-text">
              <span class="trust-feature-title">Secure Checkout</span>
              <span class="trust-feature-desc">SSL encrypted payments</span>
            </div>
          </div>

          <div class="trust-feature-item">
            <div class="trust-feature-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                <line x1="7" y1="7" x2="7.01" y2="7"></line>
              </svg>
            </div>
            <div class="trust-feature-text">
              <span class="trust-feature-title">Lower Prices</span>
              <span class="trust-feature-desc">Price match guarantee</span>
            </div>
          </div>

          <div class="trust-feature-item">
            <div class="trust-feature-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="9 12 11 14 15 10"></polyline>
              </svg>
            </div>
            <div class="trust-feature-text">
              <span class="trust-feature-title">Authentic Products</span>
              <span class="trust-feature-desc">100% genuine brands</span>
            </div>
          </div>

        </div>

      </div>

    </div>
  </div>
</section>

<!-- ==========================================
     3. PRODUCT TABS SECTION
     ========================================== -->
<?php
$hasDesc = !empty(trim(strip_tags($product['long_desc'] ?? '')));
$hasMoreInfo = !empty($productSpecs);
$hasReviews = true;  // Customers can submit reviews and read approved reviews
$hasFaqs = !empty($productFaqs);
$hasRelatedPosts = !empty(trim(strip_tags($product['related_posts_content'] ?? ''))) || !empty($relatedPosts);

$tabs = [];
if ($hasDesc) {
  $tabs['tab-description'] = 'Description';
}
if ($hasMoreInfo) {
  $tabs['tab-more-info'] = 'More Information';
}
if ($hasReviews) {
  $tabs['tab-reviews'] = 'Reviews (' . $reviewCount . ')';
}
if ($hasFaqs) {
  $tabs['tab-faqs'] = 'FAQs';
}
if ($hasRelatedPosts) {
  $tabs['tab-related-posts'] = 'Related Posts';
}

$firstTabId = !empty($tabs) ? array_key_first($tabs) : null;
?>

<?php if (!empty($tabs)): ?>
<style>
/* Rich text styling for Description (Tab 1) & Related Posts (Tab 5) */
.tab-description-content,
.related-posts-rich-text {
  font-size: 15px;
  line-height: 1.7;
  color: #1e293b;
}
.tab-description-content h1, .related-posts-rich-text h1,
.tab-description-content h2, .related-posts-rich-text h2,
.tab-description-content h3, .related-posts-rich-text h3,
.tab-description-content h4, .related-posts-rich-text h4,
.tab-description-content h5, .related-posts-rich-text h5,
.tab-description-content h6, .related-posts-rich-text h6 {
  color: #082142 !important;
  font-family: 'Outfit', sans-serif;
  margin-top: 1.25em;
  margin-bottom: 0.5em;
  font-weight: 700;
  line-height: 1.35;
}
.tab-description-content h1, .related-posts-rich-text h1 { font-size: 1.6rem; }
.tab-description-content h2, .related-posts-rich-text h2 { font-size: 1.4rem; }
.tab-description-content h3, .related-posts-rich-text h3 { font-size: 1.22rem; }
.tab-description-content h4, .related-posts-rich-text h4 { font-size: 1.1rem; }
.tab-description-content h5, .related-posts-rich-text h5 { font-size: 1rem; }
.tab-description-content h6, .related-posts-rich-text h6 { font-size: 0.9rem; text-transform: uppercase; }

.tab-description-content p,
.related-posts-rich-text p {
  margin-bottom: 1em;
  color: #1e293b !important;
}
.tab-description-content a,
.related-posts-rich-text a {
  color: #f25c27 !important;
  text-decoration: underline;
  font-weight: 600;
  transition: color 0.15s ease;
}
.tab-description-content a:hover,
.related-posts-rich-text a:hover {
  color: #db4915 !important;
}
.tab-description-content mark, .tab-description-content .rte-highlight,
.related-posts-rich-text mark, .related-posts-rich-text .rte-highlight {
  background-color: #fef08a !important;
  color: #082142 !important;
  padding: 1px 4px;
  border-radius: 2px;
}
.tab-description-content ul, .related-posts-rich-text ul,
.tab-description-content ol, .related-posts-rich-text ol {
  margin: 0 0 1em 1.5em;
  padding-left: 0;
  color: #1e293b !important;
}
.tab-description-content li, .related-posts-rich-text li {
  margin-bottom: 0.35em;
  color: #1e293b !important;
}
.tab-description-content span:not(.rte-highlight):not(mark),
.related-posts-rich-text span:not(.rte-highlight):not(mark) {
  color: inherit !important;
}
</style>
<section class="product-tabs-section" id="productTabsSection">
  <div class="container">
    
    <!-- Tab Navigation Header Strip -->
    <div class="product-tabs-nav-wrap">
      <ul class="product-tabs-nav" role="tablist">
        <?php foreach ($tabs as $tId => $tLabel): ?>
        <li class="product-tab-item" role="presentation">
          <button type="button" class="product-tab-btn <?= $tId === $firstTabId ? 'active' : '' ?>" role="tab" aria-selected="<?= $tId === $firstTabId ? 'true' : 'false' ?>" data-tab="<?= $tId ?>" id="btn-<?= $tId ?>">
            <?= htmlspecialchars($tLabel) ?>
          </button>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- Tab Panels Container -->
    <div class="product-tabs-content">

      <!-- TAB 1: DESCRIPTION -->
      <?php if ($hasDesc): ?>
      <div class="product-tab-pane <?= $firstTabId === 'tab-description' ? 'active' : '' ?>" id="tab-description" role="tabpanel" aria-labelledby="btn-tab-description">
        <div class="tab-description-content">
          <?= $product['long_desc'] ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- TAB 2: MORE INFORMATION -->
      <?php if ($hasMoreInfo): ?>
      <div class="product-tab-pane <?= $firstTabId === 'tab-more-info' ? 'active' : '' ?>" id="tab-more-info" role="tabpanel" aria-labelledby="btn-tab-more-info">
        <div class="tab-specs-table-wrap">
          <table class="tab-specs-table">
            <tbody>
              <?php foreach ($productSpecs as $specKey => $specVal): ?>
              <tr>
                <th scope="row"><?= htmlspecialchars($specKey) ?></th>
                <td><?= htmlspecialchars($specVal) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php endif; ?>

      <!-- TAB 3: REVIEWS -->
      <?php if ($hasReviews): ?>
      <div class="product-tab-pane <?= $firstTabId === 'tab-reviews' ? 'active' : '' ?>" id="tab-reviews" role="tabpanel" aria-labelledby="btn-tab-reviews">
        <div class="tab-reviews-container">

          <!-- Reviews List -->
          <div class="reviews-list">
            <?php if (!empty($productReviews)): ?>
              <?php foreach ($productReviews as $rev): ?>
              <div class="review-item">
                <div class="review-stars-row" aria-label="<?= (int) ($rev['rating'] ?? 5) ?> stars">
                  <?= str_repeat('★', (int) ($rev['rating'] ?? 5)) ?>
                </div>
                <h4 class="review-item-title"><?= htmlspecialchars($rev['title']) ?></h4>
                <p class="review-item-body"><?= htmlspecialchars($rev['comment']) ?></p>
                <div class="review-meta"><?= htmlspecialchars($rev['customer_name']) ?> &bull; <?= htmlspecialchars($rev['created_at']) ?></div>
              </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p>No customer reviews yet. Be the first to write a review!</p>
            <?php endif; ?>
          </div>

          <!-- Reviews Bottom Bar (Count + Show dropdown) -->
          <div class="reviews-pagination-bar">
            <span class="reviews-count-text"><?= !empty($productReviews) ? count($productReviews) : 0 ?> Item(s)</span>
            <div class="reviews-show-select-wrap">
              <label for="reviewsShowSelect">Show</label>
              <select id="reviewsShowSelect" class="reviews-show-select">
                <option value="10" selected>10</option>
                <option value="20">20</option>
                <option value="50">50</option>
              </select>
            </div>
          </div>

          <!-- Write a Review Section -->
          <div class="write-review-section">
            <h3 class="write-review-heading">Write a Review</h3>

            <div id="reviewAlertBox" style="display:none; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 0.9rem; font-weight: 500;"></div>

            <form class="write-review-form" id="writeReviewForm" action="javascript:void(0);">
              <input type="hidden" name="product_id" id="reviewProductId" value="<?= (int) ($product['id'] ?? 1) ?>">
              
              <!-- Star Rating Input -->
              <div class="form-group-rating">
                <label class="rating-input-label">Your Rating <span class="required-asterisk">*</span></label>
                <div class="interactive-star-rating" id="reviewStarRating" aria-label="Select rating">
                  <span class="star-rating-btn active" data-rating="1" title="1 star">&#9733;</span>
                  <span class="star-rating-btn active" data-rating="2" title="2 stars">&#9733;</span>
                  <span class="star-rating-btn active" data-rating="3" title="3 stars">&#9733;</span>
                  <span class="star-rating-btn active" data-rating="4" title="4 stars">&#9733;</span>
                  <span class="star-rating-btn active" data-rating="5" title="5 stars">&#9733;</span>
                </div>
                <input type="hidden" name="rating" id="selectedRating" value="5">
              </div>

              <!-- Nickname Field -->
              <div class="form-group-field">
                <label for="reviewNickname" class="field-label">Nickname <span class="required-asterisk">*</span></label>
                <input type="text" id="reviewNickname" class="review-input" placeholder="e.g. John D." required>
              </div>

              <!-- Review Title Field -->
              <div class="form-group-field">
                <label for="reviewTitle" class="field-label">Review Title <span class="required-asterisk">*</span></label>
                <input type="text" id="reviewTitle" class="review-input" placeholder="Summarize your experience" required>
              </div>

              <!-- Review Body Field -->
              <div class="form-group-field">
                <label for="reviewBody" class="field-label">Your Review <span class="required-asterisk">*</span></label>
                <textarea id="reviewBody" class="review-textarea" rows="4" placeholder="What did you like or dislike?" required></textarea>
              </div>

              <!-- Submit Button -->
              <button type="submit" class="btn-submit-review" id="btnSubmitReview">
                Submit Review
              </button>

            </form>
          </div>

        </div>
      </div>
      <?php endif; ?>

      <!-- TAB 4: FAQS -->
      <?php if ($hasFaqs): ?>
      <div class="product-tab-pane <?= $firstTabId === 'tab-faqs' ? 'active' : '' ?>" id="tab-faqs" role="tabpanel" aria-labelledby="btn-tab-faqs">
        <div class="faqs-accordion-list">
          <?php foreach ($productFaqs as $idx => $faq): ?>
          <div class="faq-accordion-card">
            <h3 class="faq-accordion-heading">
              <button type="button" class="faq-header-btn" aria-expanded="false">
                <span class="faq-question"><?= htmlspecialchars($faq['question']) ?></span>
                <span class="faq-chevron-icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </span>
              </button>
            </h3>
            <div class="faq-body-collapse">
              <div class="faq-body-inner">
                <?= htmlspecialchars($faq['answer']) ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- TAB 5: RELATED POSTS -->
      <?php if ($hasRelatedPosts): ?>
      <div class="product-tab-pane <?= $firstTabId === 'tab-related-posts' ? 'active' : '' ?>" id="tab-related-posts" role="tabpanel" aria-labelledby="btn-tab-related-posts">
        <div class="tab-related-posts-content">
          <?php if (!empty($product['related_posts_content'])): ?>
            <div class="related-posts-rich-text">
              <?= $product['related_posts_content'] ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($relatedPosts)): ?>
            <ul class="related-posts-link-list" style="<?= !empty($product['related_posts_content']) ? 'margin-top: 20px;' : '' ?>">
              <?php foreach ($relatedPosts as $post): ?>
              <li>
                <a href="<?= url('blog/' . urlencode($post['slug'] ?? '')) ?>" class="related-post-link">
                  <?= htmlspecialchars($post['title']) ?>
                </a>
              </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>

    </div>

  </div>
</section>
<?php endif; ?>

<!-- ==========================================
     4. YOU MAY ALSO LIKE SECTION
     (Related Best Sellers Showcase)
     ========================================== -->
<section class="products-showcase-section you-may-also-like-section">
  <div class="container">
    
    <!-- Section Header (Left-aligned Heading, No Navigation Arrows, No View All Link) -->
    <div class="products-section-header you-may-also-like-header">
      <div class="products-header-left">
        <h2 class="products-section-title">You May Also Like</h2>
      </div>
    </div>

    <!-- Products Grid (Dynamic Related Products) -->
    <div class="products-grid you-may-also-like-grid">
      <?php if (!empty($relatedProducts)): ?>
        <?php
        foreach ($relatedProducts as $rel):
          $relSlug = !empty($rel['slug']) ? $rel['slug'] : $rel['id'];
          $relTitle = !empty($rel['title']) ? $rel['title'] : ($rel['name'] ?? 'Vape Device');
          $relUrl = url('product/' . urlencode($relSlug));
          $relImg = !empty($rel['featured_image']) ? $rel['featured_image'] : (!empty($rel['img']) ? $rel['img'] : 'assets/images/vape-image.webp');
          if (!preg_match('~^(https?://|//|#)~i', $relImg)) {
            $relImg = url($relImg);
          }
          $relBasePrice = (float) ($rel['base_price'] ?? $rel['price'] ?? 19.99);
          $relCompPrice = (float) ($rel['compare_price'] ?? 0);
          $relHasDiscount = ($relCompPrice > $relBasePrice);
          $relDiscPct = $relHasDiscount ? round((($relCompPrice - $relBasePrice) / $relCompPrice) * 100) : 0;
          ?>
        <div class="product-card">
          <div class="product-card-media">
            <a href="<?= htmlspecialchars($relUrl) ?>" class="product-img-wrap">
              <img src="<?= htmlspecialchars($relImg) ?>" 
                   alt="<?= htmlspecialchars($relTitle) ?>" 
                   class="product-img" 
                   loading="lazy"
                   onerror="this.onerror=null;this.src='<?= url('assets/images/vape-image.webp') ?>';">
            </a>
            <div class="product-action-wrap">
              <button type="button" class="btn-add-to-cart" data-id="<?= $rel['id'] ?>" data-title="<?= htmlspecialchars($relTitle) ?>" data-price="<?= $relBasePrice ?>" data-image="<?= htmlspecialchars($relImg) ?>">
                ADD TO CART
              </button>
            </div>
          </div>
          <div class="product-card-info">
            <h3 class="product-title">
              <a href="<?= htmlspecialchars($relUrl) ?>" title="<?= htmlspecialchars($relTitle) ?>">
                <?= htmlspecialchars($relTitle) ?>
              </a>
            </h3>
            <div class="product-price-row">
              <span class="current-price">$<?= number_format($relBasePrice, 2) ?></span>
              <?php if ($relHasDiscount): ?>
                <span class="old-price">$<?= number_format($relCompPrice, 2) ?></span>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="grid-column: 1 / -1; text-align: center; color: #718096; padding: 24px 0;">No related products found.</p>
      <?php endif; ?>
    </div>

  </div>
</section>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
