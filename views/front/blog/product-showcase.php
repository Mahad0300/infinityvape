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
  $shortDesc = !empty($prod['short_desc']) ? strip_tags($prod['short_desc']) : '';
  if (strlen($shortDesc) > 160) {
    $shortDesc = substr($shortDesc, 0, 157) . '...';
  }
  $specPuffs = !empty($prod['spec_puffs']) ? $prod['spec_puffs'] : '';
  $specNicotine = !empty($prod['spec_nicotine']) ? $prod['spec_nicotine'] : '';
?>
  <!-- Single Product Spotlight Showcase -->
  <aside class="blog-inline-showcase single-showcase-wrap my-xl" aria-label="Featured Product Recommendation">
    <div class="showcase-card single-showcase-card">
      <!-- Top Accent Bar -->
      <div class="showcase-card-header-bar">
        <div class="showcase-header-left">
          <span class="showcase-badge-pill">
            <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
            Featured Product Recommendation
          </span>
          <span class="showcase-editor-pick-tag">Top Pick</span>
        </div>
        <div class="showcase-header-right">
          <span class="showcase-guarantee-text">
            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            100% Authentic Device
          </span>
        </div>
      </div>

      <div class="showcase-card-inner">
        <!-- Media Column -->
        <div class="showcase-media-col">
          <a href="<?= htmlspecialchars($prodUrl) ?>" class="showcase-img-link" aria-label="<?= htmlspecialchars($prod['name']) ?>">
            <div class="showcase-img-frame">
              <img 
                src="<?= htmlspecialchars($prodImg) ?>" 
                alt="<?= htmlspecialchars($prod['name']) ?>" 
                class="showcase-img"
                loading="lazy"
                onerror="this.onerror=null;this.src='<?= url('assets/images/no-image.svg') ?>';"
              >
              <?php if ($hasDiscount): ?>
                <span class="showcase-floating-discount">-<?= $discountPct ?>% OFF</span>
              <?php endif; ?>
            </div>
          </a>
        </div>

        <!-- Info Column -->
        <div class="showcase-info-col">
          <div class="showcase-meta-top">
            <?php if (!empty($prod['brand_name'])): ?>
              <span class="showcase-brand-chip"><?= htmlspecialchars($prod['brand_name']) ?></span>
            <?php endif; ?>
            
            <div class="showcase-rating-snippet">
              <span class="stars-gold">★★★★★</span>
              <span class="rating-num">4.9</span>
            </div>
          </div>

          <h3 class="showcase-product-title">
            <a href="<?= htmlspecialchars($prodUrl) ?>"><?= htmlspecialchars($prod['name']) ?></a>
          </h3>

          <div class="showcase-chips-row">
            <span class="showcase-stock-tag <?= $isInStock ? 'in-stock' : 'out-of-stock' ?>">
              <span class="stock-dot"></span> <?= htmlspecialchars($stockStatus) ?>
            </span>
            <?php if (!empty($specPuffs)): ?>
              <span class="showcase-spec-chip">
                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.59 4.59A2 2 0 1 1 11 8H2m10.59 11.41A2 2 0 1 0 14 16H2m15.73-8.27A2.5 2.5 0 1 1 19.5 12H2"></path></svg>
                <?= htmlspecialchars($specPuffs) ?> Puffs
              </span>
            <?php endif; ?>
            <?php if (!empty($specNicotine)): ?>
              <span class="showcase-spec-chip">
                <?= htmlspecialchars($specNicotine) ?>
              </span>
            <?php endif; ?>
          </div>

          <?php if (!empty($shortDesc)): ?>
            <p class="showcase-short-excerpt"><?= htmlspecialchars($shortDesc) ?></p>
          <?php endif; ?>

          <div class="showcase-bottom-action-bar">
            <div class="showcase-price-box">
              <span class="showcase-current-price">$<?= number_format($basePrice, 2) ?></span>
              <?php if ($hasDiscount): ?>
                <span class="showcase-compare-price">$<?= number_format($comparePrice, 2) ?></span>
                <span class="showcase-save-badge">Save $<?= number_format($comparePrice - $basePrice, 2) ?></span>
              <?php endif; ?>
            </div>

            <div class="showcase-cta-wrap">
              <a href="<?= htmlspecialchars($prodUrl) ?>" class="btn-showcase-buy">
                <span>View Product &amp; Shop</span>
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
              </a>
            </div>
          </div>

          <div class="showcase-trust-strip">
            <span>🔒 Secure Checkout</span>
            <span>⚡ Same-Day Dispatch</span>
            <span>📦 Discreet Shipping</span>
          </div>
        </div>
      </div>
    </div>
  </aside>

<?php else: ?>
  <!-- 2-Product Comparison Showcase (Max 2) -->
  <aside class="blog-inline-showcase comparison-showcase-wrap my-xl" aria-label="Head-to-Head Product Comparison">
    <div class="showcase-card comparison-main-card">
      <div class="showcase-card-header-bar">
        <div class="showcase-header-left">
          <span class="showcase-badge-pill comparison-badge">
            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="16 3 21 3 21 8"></polyline><line x1="4" y1="20" x2="21" y2="3"></line><polyline points="21 16 21 21 16 21"></polyline><line x1="15" y1="15" x2="21" y2="21"></line><line x1="4" y1="4" x2="9" y2="9"></line></svg>
            Head-to-Head Device Comparison
          </span>
          <span class="showcase-editor-pick-tag">Editorial Top 2</span>
        </div>
        <div class="showcase-header-right">
          <span class="showcase-guarantee-text">
            Side-by-Side Review
          </span>
        </div>
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
          $optionLabel = ($idx === 0) ? 'Option 1 • Top Pick' : 'Option 2 • Alternative';
          $specPuffs = !empty($prod['spec_puffs']) ? $prod['spec_puffs'] : '';
        ?>
          <div class="showcase-col-card <?= ($idx === 0) ? 'is-winner' : '' ?>">
            <div class="showcase-col-top">
              <span class="showcase-option-badge <?= ($idx === 0) ? 'badge-primary' : 'badge-alt' ?>"><?= $optionLabel ?></span>
              <span class="showcase-stock-tag sm <?= $isInStock ? 'in-stock' : 'out-of-stock' ?>">
                <span class="stock-dot"></span> <?= htmlspecialchars($stockStatus) ?>
              </span>
            </div>

            <div class="showcase-col-media">
              <a href="<?= htmlspecialchars($prodUrl) ?>" aria-label="<?= htmlspecialchars($prod['name']) ?>">
                <div class="showcase-img-frame sm">
                  <img 
                    src="<?= htmlspecialchars($prodImg) ?>" 
                    alt="<?= htmlspecialchars($prod['name']) ?>" 
                    class="showcase-col-img"
                    loading="lazy"
                    onerror="this.onerror=null;this.src='<?= url('assets/images/no-image.svg') ?>';"
                  >
                  <?php if ($hasDiscount): ?>
                    <span class="showcase-floating-discount sm">-<?= $discountPct ?>%</span>
                  <?php endif; ?>
                </div>
              </a>
            </div>

            <div class="showcase-col-info">
              <div class="showcase-meta-top sm">
                <?php if (!empty($prod['brand_name'])): ?>
                  <span class="showcase-brand-chip sm"><?= htmlspecialchars($prod['brand_name']) ?></span>
                <?php endif; ?>
                <div class="showcase-rating-snippet sm">
                  <span class="stars-gold">★★★★★</span>
                </div>
              </div>

              <h4 class="showcase-col-title">
                <a href="<?= htmlspecialchars($prodUrl) ?>"><?= htmlspecialchars($prod['name']) ?></a>
              </h4>

              <?php if (!empty($specPuffs)): ?>
                <div class="showcase-spec-pills-row">
                  <span class="showcase-spec-chip sm">
                    <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.59 4.59A2 2 0 1 1 11 8H2m10.59 11.41A2 2 0 1 0 14 16H2m15.73-8.27A2.5 2.5 0 1 1 19.5 12H2"></path></svg>
                    <?= htmlspecialchars($specPuffs) ?> Puffs
                  </span>
                </div>
              <?php endif; ?>

              <div class="showcase-price-box sm">
                <span class="showcase-current-price">$<?= number_format($basePrice, 2) ?></span>
                <?php if ($hasDiscount): ?>
                  <span class="showcase-compare-price">$<?= number_format($comparePrice, 2) ?></span>
                <?php endif; ?>
              </div>

              <a href="<?= htmlspecialchars($prodUrl) ?>" class="btn-showcase-buy btn-sm">
                <span>View Product &amp; Shop</span>
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </aside>
<?php endif; ?>