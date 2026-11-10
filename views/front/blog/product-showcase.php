<?php
/**
 * Blog Inline Product Showcase Partial Template
 * Renders 1 Product (Spotlight) or 2 Products (Side-by-side Comparison)
 * With Live Variation Selection & Instant One-Click Add to Cart
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
  $variants = !empty($prod['variants']) && is_array($prod['variants']) ? $prod['variants'] : [];
  $hasVariants = !empty($variants);
  $defaultVariantId = $hasVariants ? ($variants[0]['id'] ?? '') : '';
?>
  <!-- Single Product Spotlight Showcase -->
  <aside class="blog-inline-showcase single-showcase-wrap my-xl" aria-label="Featured Product Recommendation" data-showcase-container>
    <div class="showcase-card single-showcase-card" data-product-card="<?= $prod['id'] ?>">
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
                data-original-img="<?= htmlspecialchars($prodImg) ?>"
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
            <span class="showcase-stock-tag <?= $isInStock ? 'in-stock' : 'out-of-stock' ?>" data-stock-badge>
              <span class="stock-dot"></span> <span class="stock-text"><?= htmlspecialchars($stockStatus) ?></span>
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

          <!-- VARIATION SELECTOR (If product has variants) -->
          <?php if ($hasVariants): ?>
            <div class="showcase-variant-picker-wrap">
              <div class="showcase-variant-picker-label">
                <span class="label-heading">Choose Flavor / Option:</span>
                <span class="showcase-selected-variant-name" data-variant-name-display>
                  <?= htmlspecialchars($variants[0]['variant_name'] ?? '') ?>
                </span>
              </div>
              <div class="showcase-variant-select-box">
                <select 
                  class="showcase-variant-select" 
                  data-prod-id="<?= $prod['id'] ?>"
                  aria-label="Select Product Option"
                >
                  <?php foreach ($variants as $vIdx => $v): 
                    $vStock = (int)($v['stock_quantity'] ?? 0);
                    $isVOutOfStock = ($vStock <= 0);
                    $vPrice = (float)($v['price'] ?? $basePrice);
                    $vImg = !empty($v['full_image_url']) ? $v['full_image_url'] : (!empty($v['image_url']) ? url($v['image_url']) : '');
                  ?>
                    <option 
                      value="<?= $v['id'] ?>" 
                      data-price="<?= number_format($vPrice, 2, '.', '') ?>"
                      data-stock="<?= $vStock ?>"
                      data-img="<?= htmlspecialchars($vImg) ?>"
                      data-name="<?= htmlspecialchars($v['variant_name']) ?>"
                      <?= ($vIdx === 0) ? 'selected' : '' ?>
                      <?= $isVOutOfStock ? 'class="opt-out-of-stock"' : '' ?>
                    >
                      <?= htmlspecialchars($v['variant_name']) ?> — $<?= number_format($vPrice, 2) ?><?= $isVOutOfStock ? ' (Out of Stock)' : '' ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <div class="showcase-select-chevron">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <!-- BOTTOM ACTION BAR: PRICE & ADD TO CART -->
          <div class="showcase-bottom-action-bar">
            <div class="showcase-price-box">
              <span class="showcase-current-price" data-price-display>$<?= number_format($basePrice, 2) ?></span>
              <?php if ($hasDiscount): ?>
                <span class="showcase-compare-price">$<?= number_format($comparePrice, 2) ?></span>
                <span class="showcase-save-badge">Save $<?= number_format($comparePrice - $basePrice, 2) ?></span>
              <?php endif; ?>
            </div>

            <div class="showcase-purchase-controls">
              <!-- Quantity Stepper -->
              <div class="showcase-qty-stepper">
                <button type="button" class="btn-qty-step btn-qty-minus" aria-label="Decrease quantity">−</button>
                <input type="number" class="showcase-qty-input" value="1" min="1" max="99" readonly aria-label="Quantity">
                <button type="button" class="btn-qty-step btn-qty-plus" aria-label="Increase quantity">+</button>
              </div>

              <!-- Direct Add to Cart Button -->
              <button 
                type="button" 
                class="btn-showcase-add-cart" 
                data-product-id="<?= $prod['id'] ?>"
                data-variant-id="<?= $defaultVariantId ?>"
                <?= (!$isInStock) ? 'disabled' : '' ?>
              >
                <svg class="cart-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                <span class="btn-cart-text"><?= $isInStock ? 'Add to Cart' : 'Out of Stock' ?></span>
              </button>

              <!-- Quick Details Link Button -->
              <a href="<?= htmlspecialchars($prodUrl) ?>" class="btn-showcase-view-link" title="View Full Product Specifications">
                <span>Details</span>
                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
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
  <aside class="blog-inline-showcase comparison-showcase-wrap my-xl" aria-label="Head-to-Head Product Comparison" data-showcase-container>
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
          $variants = !empty($prod['variants']) && is_array($prod['variants']) ? $prod['variants'] : [];
          $hasVariants = !empty($variants);
          $defaultVariantId = $hasVariants ? ($variants[0]['id'] ?? '') : '';
        ?>
          <div class="showcase-col-card <?= ($idx === 0) ? 'is-winner' : '' ?>" data-product-card="<?= $prod['id'] ?>">
            <div class="showcase-col-top">
              <span class="showcase-option-badge <?= ($idx === 0) ? 'badge-primary' : 'badge-alt' ?>"><?= $optionLabel ?></span>
              <span class="showcase-stock-tag sm <?= $isInStock ? 'in-stock' : 'out-of-stock' ?>" data-stock-badge>
                <span class="stock-dot"></span> <span class="stock-text"><?= htmlspecialchars($stockStatus) ?></span>
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
                    data-original-img="<?= htmlspecialchars($prodImg) ?>"
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

              <!-- VARIANT PICKER FOR COMPARISON CARD -->
              <?php if ($hasVariants): ?>
                <div class="showcase-variant-picker-wrap sm">
                  <div class="showcase-variant-select-box sm">
                    <select class="showcase-variant-select sm" data-prod-id="<?= $prod['id'] ?>" aria-label="Select Option">
                      <?php foreach ($variants as $vIdx => $v): 
                        $vStock = (int)($v['stock_quantity'] ?? 0);
                        $isVOutOfStock = ($vStock <= 0);
                        $vPrice = (float)($v['price'] ?? $basePrice);
                        $vImg = !empty($v['full_image_url']) ? $v['full_image_url'] : (!empty($v['image_url']) ? url($v['image_url']) : '');
                      ?>
                        <option 
                          value="<?= $v['id'] ?>" 
                          data-price="<?= number_format($vPrice, 2, '.', '') ?>"
                          data-stock="<?= $vStock ?>"
                          data-img="<?= htmlspecialchars($vImg) ?>"
                          data-name="<?= htmlspecialchars($v['variant_name']) ?>"
                          <?= ($vIdx === 0) ? 'selected' : '' ?>
                          <?= $isVOutOfStock ? 'class="opt-out-of-stock"' : '' ?>
                        >
                          <?= htmlspecialchars($v['variant_name']) ?> — $<?= number_format($vPrice, 2) ?><?= $isVOutOfStock ? ' (OOS)' : '' ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                    <div class="showcase-select-chevron sm">
                      <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                  </div>
                </div>
              <?php endif; ?>

              <div class="showcase-price-box sm">
                <span class="showcase-current-price" data-price-display>$<?= number_format($basePrice, 2) ?></span>
                <?php if ($hasDiscount): ?>
                  <span class="showcase-compare-price">$<?= number_format($comparePrice, 2) ?></span>
                <?php endif; ?>
              </div>

              <div class="showcase-compare-actions">
                <button 
                  type="button" 
                  class="btn-showcase-add-cart btn-sm" 
                  data-product-id="<?= $prod['id'] ?>"
                  data-variant-id="<?= $defaultVariantId ?>"
                  <?= (!$isInStock) ? 'disabled' : '' ?>
                >
                  <svg class="cart-icon" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                  <span class="btn-cart-text"><?= $isInStock ? 'Add to Cart' : 'Out of Stock' ?></span>
                </button>

                <a href="<?= htmlspecialchars($prodUrl) ?>" class="btn-showcase-view-link sm" title="View Full Details">
                  <span>Details</span>
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </aside>
<?php endif; ?>

<!-- Self-Contained Client-side Controller for Showcase Variations & Instant Cart -->
<script>
(function() {
  if (window.__initBlogShowcaseControls) {
    window.__initBlogShowcaseControls();
    return;
  }

  window.__initBlogShowcaseControls = function() {
    document.querySelectorAll('[data-showcase-container]').forEach(container => {
      if (container.__showcaseBound) return;
      container.__showcaseBound = true;

      // 1. Quantity Stepper Logic
      container.querySelectorAll('.showcase-qty-stepper').forEach(stepper => {
        const input = stepper.querySelector('.showcase-qty-input');
        const btnMinus = stepper.querySelector('.btn-qty-minus');
        const btnPlus = stepper.querySelector('.btn-qty-plus');

        if (btnMinus && input) {
          btnMinus.addEventListener('click', (e) => {
            e.preventDefault();
            let val = parseInt(input.value, 10) || 1;
            if (val > 1) {
              input.value = val - 1;
            }
          });
        }

        if (btnPlus && input) {
          btnPlus.addEventListener('click', (e) => {
            e.preventDefault();
            let val = parseInt(input.value, 10) || 1;
            if (val < 99) {
              input.value = val + 1;
            }
          });
        }
      });

      // 2. Variant Select Change Handler
      container.querySelectorAll('.showcase-variant-select').forEach(select => {
        select.addEventListener('change', () => {
          const card = select.closest('[data-product-card]');
          if (!card) return;

          const opt = select.options[select.selectedIndex];
          if (!opt) return;

          const variantId = opt.value;
          const price = opt.dataset.price;
          const stock = parseInt(opt.dataset.stock, 10);
          const imgUrl = opt.dataset.img;
          const variantName = opt.dataset.name;

          // Update Add-to-Cart Button Variant Binding
          const addBtn = card.querySelector('.btn-showcase-add-cart');
          if (addBtn) {
            addBtn.dataset.variantId = variantId;
          }

          // Update Selected Variant Name Display
          const nameDisplay = card.querySelector('[data-variant-name-display]');
          if (nameDisplay && variantName) {
            nameDisplay.textContent = variantName;
          }

          // Update Dynamic Price Display
          const priceDisplay = card.querySelector('[data-price-display]');
          if (priceDisplay && price) {
            priceDisplay.textContent = '$' + parseFloat(price).toFixed(2);
          }

          // Update Dynamic Image if custom variant image exists
          const imgEl = card.querySelector('.showcase-img, .showcase-col-img');
          if (imgEl) {
            if (imgUrl && imgUrl.trim()) {
              imgEl.src = imgUrl;
            } else if (imgEl.dataset.originalImg) {
              imgEl.src = imgEl.dataset.originalImg;
            }
          }

          // Update Stock Status Badge & Add Button Availability
          const stockBadge = card.querySelector('[data-stock-badge]');
          const stockText = stockBadge ? stockBadge.querySelector('.stock-text') : null;
          const btnText = addBtn ? addBtn.querySelector('.btn-cart-text') : null;

          if (stock <= 0) {
            if (stockBadge) {
              stockBadge.className = 'showcase-stock-tag out-of-stock' + (stockBadge.classList.contains('sm') ? ' sm' : '');
            }
            if (stockText) stockText.textContent = 'Out of Stock';
            if (addBtn) {
              addBtn.disabled = true;
              if (btnText) btnText.textContent = 'Out of Stock';
            }
          } else {
            if (stockBadge) {
              stockBadge.className = 'showcase-stock-tag in-stock' + (stockBadge.classList.contains('sm') ? ' sm' : '');
            }
            if (stockText) stockText.textContent = stock < 15 ? 'Low Stock' : 'In Stock';
            if (addBtn) {
              addBtn.disabled = false;
              if (btnText) btnText.textContent = 'Add to Cart';
            }
          }
        });
      });

      // 3. One-Click Add to Cart Logic
      container.querySelectorAll('.btn-showcase-add-cart').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          if (btn.disabled || btn.classList.contains('is-loading')) return;

          const card = btn.closest('[data-product-card]');
          const productId = parseInt(btn.dataset.productId, 10);
          if (!productId) return;

          let variantId = btn.dataset.variantId ? parseInt(btn.dataset.variantId, 10) : null;
          const select = card ? card.querySelector('.showcase-variant-select') : null;
          if (select && select.value) {
            variantId = parseInt(select.value, 10) || variantId;
          }

          const qtyInput = card ? card.querySelector('.showcase-qty-input') : null;
          const qty = qtyInput ? Math.max(1, parseInt(qtyInput.value, 10) || 1) : 1;

          const btnText = btn.querySelector('.btn-cart-text');
          const originalHtml = btnText ? btnText.textContent : 'Add to Cart';

          btn.classList.add('is-loading');
          if (btnText) btnText.textContent = 'Adding...';

          const itemData = {
            product_id: productId,
            variant_id: variantId,
            quantity: qty
          };

          const handleSuccess = () => {
            btn.classList.remove('is-loading');
            btn.classList.add('is-added');
            if (btnText) btnText.textContent = 'Added! ✓';

            setTimeout(() => {
              btn.classList.remove('is-added');
              if (btnText) btnText.textContent = originalHtml;
            }, 1800);
          };

          const handleFail = (msg) => {
            btn.classList.remove('is-loading');
            if (btnText) btnText.textContent = 'Try Again';
            setTimeout(() => {
              if (btnText) btnText.textContent = originalHtml;
            }, 1800);
          };

          // Use Global Cart Drawer if present
          if (window.InfinityCart && typeof window.InfinityCart.add === 'function') {
            window.InfinityCart.add(itemData, function(res) {
              if (res && res.success) {
                handleSuccess();
              } else {
                handleFail(res && res.message);
              }
            });
          } else {
            // Direct Fetch Fallback
            fetch(window.location.origin + '/vapefinalwebsite/cart/add', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              },
              body: JSON.stringify(itemData)
            })
            .then(r => r.json())
            .then(data => {
              if (data && data.success) {
                handleSuccess();
                if (window.InfinityCart && typeof window.InfinityCart.open === 'function') {
                  window.InfinityCart.open();
                }
              } else {
                handleFail(data && data.message);
              }
            })
            .catch(() => handleFail());
          }
        });
      });
    });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', window.__initBlogShowcaseControls);
  } else {
    window.__initBlogShowcaseControls();
  }
})();
</script>