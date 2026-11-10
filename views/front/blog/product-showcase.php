<?php
/**
 * Blog Inline Product Showcase Partial Template
 * Renders 1 Product (Spotlight) or 2 Products (Side-by-side Comparison)
 *
 * @var array $products
 */
$products = $products ?? [];
if (empty($products)) return;
$count = count($products);
?>

<?php if ($count === 1): 
  $prod = $products[0];
  $slug = !empty($prod['slug']) ? $prod['slug'] : $prod['id'];
  $prodUrl = url('product/' . urlencode($slug));
  $prodImg = !empty($prod['featured_image']) ? $prod['featured_image'] : 'assets/images/no-image.svg';
  if (!preg_match('~^(https?://|//|#)~i', $prodImg)) {
    $prodImg = url($prodImg);
  }
  $basePrice = (float)($prod['base_price'] ?? 0);
  $comparePrice = (float)($prod['compare_price'] ?? 0);
  $hasDiscount = ($comparePrice > $basePrice);
  $discountPct = $hasDiscount ? round((($comparePrice - $basePrice) / $comparePrice) * 100) : 0;
  $stockStatus = !empty($prod['stock_status']) ? $prod['stock_status'] : 'In Stock';
  $isInStock = (strtolower($stockStatus) !== 'out of stock');
?>
  <!-- Single Product Spotlight Showcase -->
  <div class="blog-inline-showcase single-showcase-wrap my-xl">
    <div class="showcase-header-bar">
      <span class="showcase-badge-pill">
        <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor" style="margin-right: 4px;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
        Featured Product Recommendation
      </span>
    </div>
    <div class="showcase-card single-showcase-card">
      <div class="showcase-media-col">
        <a href="<?= htmlspecialchars($prodUrl) ?>" class="showcase-img-link" aria-label="<?= htmlspecialchars($prod['name']) ?>">
          <img 
            src="<?= htmlspecialchars($prodImg) ?>" 
            alt="<?= htmlspecialchars($prod['name']) ?>" 
            class="showcase-img"
            loading="lazy"
            onerror="this.onerror=null;this.src='<?= url('assets/images/no-image.svg') ?>';"
          >
        </a>
      </div>
      <div class="showcase-info-col">
        <?php if (!empty($prod['brand_name'])): ?>
          <span class="showcase-brand-label"><?= htmlspecialchars($prod['brand_name']) ?></span>
        <?php endif; ?>
        <h3 class="showcase-product-title">
          <a href="<?= htmlspecialchars($prodUrl) ?>"><?= htmlspecialchars($prod['name']) ?></a>
        </h3>
        
        <div class="showcase-stock-row">
          <span class="showcase-stock-tag <?= $isInStock ? 'in-stock' : 'out-of-stock' ?>">
            <span class="stock-dot"></span> <?= htmlspecialchars($stockStatus) ?>
          </span>
        </div>

        <div class="showcase-price-box">
          <span class="showcase-current-price">$<?= number_format($basePrice, 2) ?></span>
          <?php if ($hasDiscount): ?>
            <span class="showcase-compare-price">$<?= number_format($comparePrice, 2) ?></span>
            <span class="showcase-save-badge">Save <?= $discountPct ?>%</span>
          <?php endif; ?>
        </div>

        <div class="showcase-cta-wrap">
          <a href="<?= htmlspecialchars($prodUrl) ?>" class="btn-showcase-buy">
            <span>View Product &amp; Shop</span>
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </a>
        </div>
      </div>
    </div>
  </div>

<?php else: ?>
  <!-- 2-Product Comparison Showcase (Max 2) -->
  <div class="blog-inline-showcase comparison-showcase-wrap my-xl">
    <div class="showcase-header-bar d-flex justify-between align-center">
      <span class="showcase-badge-pill">
        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" style="margin-right: 4px;"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
        Head-to-Head Comparison &bull; Top Picks
      </span>
      <span class="showcase-sub-tag">2-Device Showcase</span>
    </div>
    
    <div class="showcase-grid-two">
      <?php foreach ($products as $idx => $prod): 
        $slug = !empty($prod['slug']) ? $prod['slug'] : $prod['id'];
        $prodUrl = url('product/' . urlencode($slug));
        $prodImg = !empty($prod['featured_image']) ? $prod['featured_image'] : 'assets/images/no-image.svg';
        if (!preg_match('~^(https?://|//|#)~i', $prodImg)) {
          $prodImg = url($prodImg);
        }
        $basePrice = (float)($prod['base_price'] ?? 0);
        $comparePrice = (float)($prod['compare_price'] ?? 0);
        $hasDiscount = ($comparePrice > $basePrice);
        $discountPct = $hasDiscount ? round((($comparePrice - $basePrice) / $comparePrice) * 100) : 0;
        $stockStatus = !empty($prod['stock_status']) ? $prod['stock_status'] : 'In Stock';
        $isInStock = (strtolower($stockStatus) !== 'out of stock');
        $label = ($idx === 0) ? 'Option 1' : 'Option 2';
      ?>
        <div class="showcase-col-card">
          <div class="showcase-col-top">
            <span class="showcase-option-badge"><?= $label ?></span>
            <span class="showcase-stock-tag sm <?= $isInStock ? 'in-stock' : 'out-of-stock' ?>">
              <span class="stock-dot"></span> <?= htmlspecialchars($stockStatus) ?>
            </span>
          </div>

          <div class="showcase-col-media">
            <a href="<?= htmlspecialchars($prodUrl) ?>" aria-label="<?= htmlspecialchars($prod['name']) ?>">
              <img 
                src="<?= htmlspecialchars($prodImg) ?>" 
                alt="<?= htmlspecialchars($prod['name']) ?>" 
                class="showcase-col-img"
                loading="lazy"
                onerror="this.onerror=null;this.src='<?= url('assets/images/no-image.svg') ?>';"
              >
            </a>
          </div>

          <div class="showcase-col-info">
            <?php if (!empty($prod['brand_name'])): ?>
              <span class="showcase-brand-label"><?= htmlspecialchars($prod['brand_name']) ?></span>
            <?php endif; ?>
            <h4 class="showcase-col-title">
              <a href="<?= htmlspecialchars($prodUrl) ?>"><?= htmlspecialchars($prod['name']) ?></a>
            </h4>

            <div class="showcase-price-box">
              <span class="showcase-current-price">$<?= number_format($basePrice, 2) ?></span>
              <?php if ($hasDiscount): ?>
                <span class="showcase-compare-price">$<?= number_format($comparePrice, 2) ?></span>
              <?php endif; ?>
            </div>

            <a href="<?= htmlspecialchars($prodUrl) ?>" class="btn-showcase-buy btn-sm">
              <span>View Product</span>
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>
