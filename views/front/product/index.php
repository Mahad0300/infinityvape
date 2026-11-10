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

// Resolve Parent Category for Product Breadcrumbs:
// Pattern: Home > (parent category) > Product Title
// If child category (e.g. 5,000+ Puffs), resolve its parent category (e.g. Disposable)
// If already top-level parent category (e.g. Disposable), use directly
$parentCategoryName = $product['parent_category_name'] ?? null;
$parentCategorySlug = $product['parent_category_slug'] ?? null;

if (empty($parentCategoryName) && class_exists('\App\Models\Collection')) {
  $collId = $product['collection_id'] ?? null;
  $rawSlug = $product['category_slug'] ?? ($product['category'] ?? '');
  $coll = null;
  if ($collId) {
    $coll = \App\Models\Collection::find($collId);
  } elseif (!empty($rawSlug)) {
    $coll = \App\Models\Collection::findBySlug($rawSlug);
  }
  if ($coll) {
    if (!empty($coll['parent_id'])) {
      $parentColl = \App\Models\Collection::find($coll['parent_id']);
      if ($parentColl) {
        $parentCategoryName = $parentColl['name'];
        $parentCategorySlug = $parentColl['slug'];
      }
    }
    if (empty($parentCategoryName)) {
      $parentCategoryName = $coll['name'];
      $parentCategorySlug = $coll['slug'];
    }
  }
}

if (empty($parentCategoryName)) {
  $parentCategoryName = $categoryName;
  $parentCategorySlug = $categorySlug;
}

$parentCategoryUrl = class_exists('\App\Models\Collection') 
  ? \App\Models\Collection::getUrl($parentCategorySlug) 
  : url('collection/' . urlencode($parentCategorySlug));
$sku = !empty($product['sku']) ? $product['sku'] : ('SKU-' . ($product['id'] ?? '100'));
$stockStatus = !empty($product['stock_status']) ? ucfirst(str_replace('_', ' ', $product['stock_status'])) : (!empty($product['status']) ? ucfirst($product['status']) : 'In Stock');

// Pricing & Discount calculation
$basePrice = (float) ($product['base_price'] ?? $product['price'] ?? 0);
$comparePrice = !empty($product['compare_price']) ? (float) $product['compare_price'] : 0;
$hasDiscount = ($comparePrice > $basePrice);
$discountPct = $hasDiscount ? round((($comparePrice - $basePrice) / $comparePrice) * 100) : 0;

$page_title = $page_title ?? (!empty($product['seo_title']) ? trim($product['seo_title']) : $productTitle);
$meta_description = $meta_description ?? (!empty($product['seo_description']) ? trim($product['seo_description']) : (!empty($product['short_desc']) ? trim(preg_replace('/\s+/', ' ', strip_tags($product['short_desc']))) : (!empty($product['long_desc']) ? mb_substr(trim(preg_replace('/\s+/', ' ', strip_tags($product['long_desc']))), 0, 160) : '')));
$canonical_url = $canonical_url ?? url('product/' . urlencode(!empty($product['slug']) ? $product['slug'] : $product['id']));
$page_js = 'product';

include dirname(__DIR__) . '/includes/header.php';
?>

<!-- ==========================================
     1. BREADCRUMB NAVIGATION
     ========================================== -->
<nav class="product-breadcrumb-nav" aria-label="Breadcrumb">
  <div class="container">
    <ol class="breadcrumb-list" itemscope itemtype="https://schema.org/BreadcrumbList">
      <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
        <a href="<?= url() ?>" itemprop="item">
          <span itemprop="name">Home</span>
        </a>
        <meta itemprop="position" content="1">
      </li>
      <li class="breadcrumb-separator" aria-hidden="true">
        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </li>
      <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
        <a href="<?= htmlspecialchars($parentCategoryUrl) ?>" itemprop="item">
          <span itemprop="name"><?= htmlspecialchars($parentCategoryName) ?></span>
        </a>
        <meta itemprop="position" content="2">
      </li>
      <li class="breadcrumb-separator" aria-hidden="true">
        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </li>
      <li class="breadcrumb-item active" aria-current="page" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
        <span itemprop="name"><?= htmlspecialchars($productTitle) ?></span>
        <meta itemprop="position" content="3">
      </li>
    </ol>
  </div>
</nav>

<?php if (!empty($isDraftPreview)): ?>
<div class="draft-preview-banner">
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
            $galleryList[] = !empty($product['featured_image']) && $product['featured_image'] !== 'assets/images/vape-image.webp'
              ? $product['featured_image'] 
              : (!empty($product['img']) && $product['img'] !== 'assets/images/vape-image.webp' ? $product['img'] : 'assets/images/no-image.svg');
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
                data-default-src="<?= asset($mainImgSrc) ?>" 
                alt="<?= htmlspecialchars($productTitle) ?>" 
                class="product-main-img"
                onerror="this.onerror=null;this.src='<?= asset('assets/images/no-image.svg') ?>';"
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
                  <img src="<?= asset($tImg) ?>" alt="<?= htmlspecialchars($productTitle) ?> thumbnail <?= $tIdx + 1 ?>" onerror="this.onerror=null;this.src='<?= asset('assets/images/no-image.svg') ?>';">
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
        <div class="product-price-section" data-base-compare="<?= $comparePrice ?>" data-base-price="<?= $basePrice ?>">
          <span class="product-price-current">$<?= number_format($basePrice, 2) ?></span>
          <?php if ($hasDiscount): ?>
            <span class="product-price-old">$<?= number_format($comparePrice, 2) ?></span>
          <?php endif; ?>
          <?php if ($hasDiscount && $discountPct > 0): ?>
            <span class="product-discount-pill">SAVE <?= $discountPct ?>%</span>
          <?php endif; ?>
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
              'index' => $i,
              'name' => $optionLabels[$i],
              'values' => $optionValuesMap[$i]
            ];
          }
        }

        // Resolve default in-stock variant combination
        $defaultVariant = null;
        foreach ($productVariants as $pv) {
          $st = isset($pv['stock_quantity']) ? (int) $pv['stock_quantity'] : 0;
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
              data-variant-id="<?= (int)$variant['id'] ?>"
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
          <?php
          $jsonVariants = array_map(function($var) use ($comparePrice) {
            if (!isset($var['compare_price']) || $var['compare_price'] === null || $var['compare_price'] === '') {
              $var['compare_price'] = $comparePrice > 0 ? $comparePrice : null;
            }
            return $var;
          }, $productVariants);
          ?>
          <script id="productVariantsJson" type="application/json">
            <?= json_encode($jsonVariants, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
          </script>

          <!-- Dynamic Option Groups (Option 1: Flavor custom dropdown, Option 2: Strength custom dropdown, etc.) -->
          <?php
          foreach ($parsedOptions as $opt):
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

            <div class="custom-variant-select" data-option-index="<?= $currOptIdx ?>" tabindex="0" role="combobox" aria-expanded="false">
              <!-- Custom Trigger Box -->
              <div class="custom-variant-trigger">
                <span class="custom-variant-current-val"><?= htmlspecialchars($selectedOptVal) ?></span>
                <div class="custom-variant-chevron" aria-hidden="true">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </div>
              </div>

              <!-- Custom Floating Options Menu (Styled perfectly with Navy, White & Checkmark) -->
              <div class="custom-variant-menu" role="listbox">
                <?php
                foreach ($opt['values'] as $vIdx => $val):
                  $isOutStock = false;
                  if ($currOptIdx === 0) {
                    $hasAny = false;
                    foreach ($productVariants as $pv) {
                      $pParts = array_map('trim', explode(' / ', $pv['variant_name']));
                      if (($pParts[0] ?? $pv['variant_name']) === $val && (int) ($pv['stock_quantity'] ?? 0) > 0) {
                        $hasAny = true;
                        break;
                      }
                    }
                    $isOutStock = !$hasAny;
                  } elseif ($currOptIdx === 1 && !empty($defaultParts[0])) {
                    $hasStock = false;
                    foreach ($productVariants as $pv) {
                      $pParts = array_map('trim', explode(' / ', $pv['variant_name']));
                      if (($pParts[0] ?? '') === $defaultParts[0] && ($pParts[1] ?? '') === $val && (int) ($pv['stock_quantity'] ?? 0) > 0) {
                        $hasStock = true;
                        break;
                      }
                    }
                    $isOutStock = !$hasStock;
                  }
                  $isActive = ($val === $selectedOptVal);
                  ?>
                  <div 
                    class="custom-variant-item <?= $isActive ? 'is-selected' : '' ?> <?= $isOutStock ? 'is-disabled' : '' ?>" 
                    data-value="<?= htmlspecialchars($val) ?>"
                    role="option"
                    aria-selected="<?= $isActive ? 'true' : 'false' ?>"
                  >
                    <span class="item-text"><?= htmlspecialchars($val) ?></span>
                    <?php if ($isOutStock): ?>
                      <span class="item-badge-out">Out of stock</span>
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
              </div>

              <!-- Native select preserved in background for cart-drawer & fallback -->
              <select 
                id="variant-select-<?= $currOptIdx ?>" 
                class="variant-dropdown-select d-none" 
                style="display: none;"
                data-option-index="<?= $currOptIdx ?>"
                aria-hidden="true"
                tabindex="-1"
              >
                <?php foreach ($opt['values'] as $val): ?>
                  <option value="<?= htmlspecialchars($val) ?>" <?= ($val === $selectedOptVal) ? 'selected' : '' ?> data-original-text="<?= htmlspecialchars($val) ?>">
                    <?= htmlspecialchars($val) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Actions Row: Quantity Selector + Add to Cart -->
        <div class="product-purchase-row" data-in-stock="<?= $isOutOfStock ? '0' : '1' ?>">
          
          <!-- Quantity Counter -->
          <div class="product-qty-selector">
            <button type="button" class="qty-btn" id="qtyMinus" aria-label="Decrease quantity" <?= $isOutOfStock ? 'disabled' : '' ?>>&minus;</button>
            <input type="number" id="productQuantity" class="qty-input" value="1" min="1" max="99" readonly <?= $isOutOfStock ? 'disabled' : '' ?>>
            <button type="button" class="qty-btn" id="qtyPlus" aria-label="Increase quantity" <?= $isOutOfStock ? 'disabled' : '' ?>>&plus;</button>
          </div>

          <!-- Add to Cart (Gold/Yellow Button) -->
          <button 
            type="button" 
            class="btn-product-add-cart <?= $isOutOfStock ? 'is-disabled' : '' ?>" 
            id="btnProductAddToCart"
            data-product-id="<?= (int)$product['id'] ?>"
            data-variant-id="<?= !empty($defaultVariant['id']) ? (int)$defaultVariant['id'] : '' ?>"
            <?= $isOutOfStock ? 'disabled' : '' ?>
          >
            <?= $isOutOfStock ? 'Out of Stock' : 'Add to cart' ?>
          </button>
        </div>

        <!-- Buy It Now Button (Black Solid Button) -->
        <button 
          type="button" 
          class="btn-product-buy-now <?= $isOutOfStock ? 'is-disabled' : '' ?>" 
          id="btnProductBuyNow"
          data-product-id="<?= (int)$product['id'] ?>"
          data-variant-id="<?= !empty($defaultVariant['id']) ? (int)$defaultVariant['id'] : '' ?>"
          <?= $isOutOfStock ? 'disabled' : '' ?>
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

        <!-- Frequently Bought Together Section (Cross-Sell Bundle) -->
        <?php 
        $fbtList = $fbtProducts ?? \App\Models\Product::getFrequentlyBoughtProducts($product);
        ?>
        <?php if (!empty($fbtList)): ?>
        <div class="fbt-bundle-container" id="fbtBundleContainer">
          <h3 class="fbt-bundle-title">Frequently Bought Together:</h3>

          <div class="fbt-items-grid">
            <?php foreach ($fbtList as $fIdx => $fbt): ?>
              <?php
              $fbtId = (int)$fbt['id'];
              $fbtTitle = $fbt['title'] ?? $fbt['name'] ?? 'Product';
              $fbtSlug = $fbt['slug'] ?? $fbtId;
              $fbtImg = $fbt['img'] ?? 'assets/images/no-image.svg';
              $hasVariants = !empty($fbt['variants']);
              $fbtOptions = $fbt['parsed_options'] ?? [];
              $isFbtOutOfStock = !empty($fbt['is_out_of_stock']);
              $defaultVariant = $hasVariants ? ($fbt['first_in_stock_variant'] ?? $fbt['variants'][0]) : null;
              $defaultParts = $defaultVariant ? array_map('trim', explode(' / ', $defaultVariant['variant_name'])) : [];
              $defaultPrice = $defaultVariant ? (float)($defaultVariant['price'] ?? $fbt['base_price']) : (float)$fbt['base_price'];
              $isDefaultVarOutOfStock = $hasVariants 
                  ? ((int)($defaultVariant['stock_quantity'] ?? 0) <= 0) 
                  : $isFbtOutOfStock;
              ?>
              <div class="fbt-item-card <?= $isDefaultVarOutOfStock ? 'is-out-of-stock' : '' ?>" 
                   data-product-id="<?= $fbtId ?>" 
                   data-product-title="<?= htmlspecialchars($fbtTitle, ENT_QUOTES, 'UTF-8') ?>" 
                   data-base-price="<?= $defaultPrice ?>" 
                   data-current-price="<?= $defaultPrice ?>" 
                   data-product-img="<?= asset($fbtImg) ?>" 
                   data-out-of-stock="<?= $isDefaultVarOutOfStock ? '1' : '0' ?>" 
                   data-parent-out-of-stock="<?= $isFbtOutOfStock ? '1' : '0' ?>" 
                   data-has-variants="<?= $hasVariants ? '1' : '0' ?>"
                   data-selected-variant-id="<?= $defaultVariant ? ($defaultVariant['id'] ?? '') : '' ?>"
                   data-selected-variant-name="<?= $defaultVariant ? htmlspecialchars($defaultVariant['variant_name'] ?? '', ENT_QUOTES, 'UTF-8') : '' ?>"
                   data-variants='<?= htmlspecialchars(json_encode($fbt['variants'] ?? []), ENT_QUOTES, 'UTF-8') ?>'>
                <div class="fbt-item-header">
                  <label class="fbt-custom-checkbox-wrap" title="<?= $isDefaultVarOutOfStock ? 'This product is out of stock' : '' ?>">
                    <input type="checkbox" class="fbt-item-checkbox" data-product-id="<?= $fbtId ?>" <?= $isDefaultVarOutOfStock ? 'disabled' : '' ?>>
                    <span class="fbt-checkbox-custom">
                      <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                      </svg>
                    </span>
                  </label>

                  <a href="<?= url('product/' . urlencode($fbtSlug)) ?>" class="fbt-item-img-link" title="<?= htmlspecialchars($fbtTitle) ?>">
                    <img src="<?= asset($fbtImg) ?>" alt="<?= htmlspecialchars($fbtTitle) ?>" class="fbt-item-img" onerror="this.onerror=null;this.src='<?= asset('assets/images/no-image.svg') ?>';">
                  </a>

                  <div class="fbt-item-meta">
                    <a href="<?= url('product/' . urlencode($fbtSlug)) ?>" class="fbt-item-title" title="<?= htmlspecialchars($fbtTitle) ?>">
                      <?= htmlspecialchars($fbtTitle) ?>
                    </a>
                    <div class="fbt-item-price-row">
                      <span class="fbt-price-display"><?= $hasVariants && $defaultPrice > 0 ? '$' . number_format($defaultPrice, 2) : $fbt['price_display'] ?></span>
                      <?php if ($isDefaultVarOutOfStock): ?>
                        <span class="fbt-badge-out">Out of stock</span>
                      <?php endif; ?>
                    </div>

                    <?php if ($hasVariants && !empty($fbtOptions)): ?>
                      <button type="button" class="fbt-toggle-options-btn" data-target="fbt-options-<?= $fbtId ?>" aria-expanded="false">
                        <span>OPTIONS</span>
                        <svg class="fbt-toggle-chevron" viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                          <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                      </button>
                    <?php endif; ?>
                  </div>
                </div>

                <?php if ($hasVariants && !empty($fbtOptions)): ?>
                  <!-- Options Popover/Card with Custom Dropdowns -->
                  <div class="fbt-options-popover" id="fbt-options-<?= $fbtId ?>" style="display: none;">
                    <?php foreach ($fbtOptions as $optGroup): ?>
                      <?php 
                      $currPartVal = $defaultParts[$optGroup['index']] ?? ($optGroup['values'][0] ?? '');
                      ?>
                      <div class="fbt-opt-group" data-opt-index="<?= $optGroup['index'] ?>">
                        <div class="fbt-opt-label">
                          <?= htmlspecialchars(strtoupper($optGroup['name'])) ?>: <span class="req">*</span>
                        </div>
                        <div class="custom-variant-select fbt-custom-select" data-option-index="<?= $optGroup['index'] ?>" tabindex="0" role="combobox" aria-expanded="false">
                          <div class="custom-variant-trigger">
                            <span class="custom-variant-current-val"><?= htmlspecialchars($currPartVal) ?></span>
                            <div class="custom-variant-chevron" aria-hidden="true">
                              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                              </svg>
                            </div>
                          </div>

                          <div class="custom-variant-menu" role="listbox">
                            <?php foreach ($optGroup['values'] as $vIdx => $val): ?>
                              <?php
                              $valHasStock = false;
                              if (!empty($fbt['variants'])) {
                                foreach ($fbt['variants'] as $fv) {
                                  $fParts = array_map('trim', explode(' / ', $fv['variant_name']));
                                  $partVal = $fParts[$optGroup['index']] ?? ($optGroup['index'] === 0 ? $fv['variant_name'] : '');
                                  if ($partVal === $val && (int)($fv['stock_quantity'] ?? 0) > 0) {
                                    $valHasStock = true;
                                    break;
                                  }
                                }
                              }
                              $isOptOutOfStock = !$valHasStock;
                              $isActive = ($val === $currPartVal);
                              ?>
                              <div 
                                class="custom-variant-item <?= $isActive ? 'is-selected' : '' ?> <?= $isOptOutOfStock ? 'is-disabled' : '' ?>" 
                                data-value="<?= htmlspecialchars($val) ?>"
                                role="option"
                                aria-selected="<?= $isActive ? 'true' : 'false' ?>"
                              >
                                <span class="item-text"><?= htmlspecialchars($val) ?></span>
                                <?php if ($isOptOutOfStock): ?>
                                  <span class="item-badge-out">Out of stock</span>
                                <?php endif; ?>
                              </div>
                            <?php endforeach; ?>
                          </div>

                          <!-- Hidden select for holding value -->
                          <select class="fbt-dropdown-select d-none" style="display: none;" data-option-index="<?= $optGroup['index'] ?>">
                            <?php foreach ($optGroup['values'] as $vIdx => $val): ?>
                              <option value="<?= htmlspecialchars($val) ?>" <?= ($val === $currPartVal) ? 'selected' : '' ?>><?= htmlspecialchars($val) ?></option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                      </div>
                    <?php endforeach; ?>

                    <!-- Quantity Counter inside options -->
                    <div class="fbt-opt-qty-group">
                      <span class="fbt-opt-qty-label">QUANTITY:</span>
                      <div class="fbt-qty-box">
                        <button type="button" class="fbt-qty-control btn-minus" aria-label="Decrease quantity">&minus;</button>
                        <input type="number" class="fbt-qty-num" value="1" min="1" max="99" readonly>
                        <button type="button" class="fbt-qty-control btn-plus" aria-label="Increase quantity">&plus;</button>
                      </div>
                    </div>
                  </div>
                <?php else: ?>
                  <input type="hidden" class="fbt-qty-num" value="1">
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>

          <!-- Add Selected to Cart Action Bar (Hidden by default until customer checks an item) -->
          <div class="fbt-action-footer" style="display: none;">
            <button type="button" class="btn-fbt-add-all" id="btnFbtAddToCart">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
              </svg>
              <span class="btn-fbt-label">Add selected to cart</span>
              <span class="fbt-btn-dot">&bull;</span>
              <span class="fbt-btn-total-price">$0.00</span>
            </button>
          </div>
        </div>
        <?php endif; ?>

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
                <a href="<?= url(urlencode($post['slug'] ?? '')) ?>" class="related-post-link">
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
          $relImg = !empty($rel['featured_image']) && $rel['featured_image'] !== 'assets/images/vape-image.webp'
            ? $rel['featured_image'] 
            : (!empty($rel['img']) && $rel['img'] !== 'assets/images/vape-image.webp' ? $rel['img'] : 'assets/images/no-image.svg');
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
            <?php if ($relHasDiscount && $relDiscPct > 0): ?>
              <div class="product-badges">
                <span class="badge badge-discount">-<?= $relDiscPct ?>%</span>
              </div>
            <?php endif; ?>
            <a href="<?= htmlspecialchars($relUrl) ?>" class="product-img-wrap">
              <img src="<?= htmlspecialchars($relImg) ?>" 
                   alt="<?= htmlspecialchars($relTitle) ?>" 
                   class="product-img" 
                   loading="lazy"
                   onerror="this.onerror=null;this.src='<?= url('assets/images/no-image.svg') ?>';">
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
