<?php
/**
 * Blog Inline Product Showcase Partial Template
 * Ultra-Compact Editorial Product Showcase & Comparison Card
 * Styled identically to the Admin Compact Card with live Variant Selector & 1-Click Cart
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
  $specPuffs = !empty($prod['spec_puffs']) ? $prod['spec_puffs'] : '';
  $variants = !empty($prod['variants']) && is_array($prod['variants']) ? $prod['variants'] : [];
  $hasVariants = !empty($variants);
  $defaultVariantId = $hasVariants ? ($variants[0]['id'] ?? '') : '';
?>
  <!-- Single Product Compact Showcase -->
  <aside class="blog-inline-showcase single-compact-wrap my-lg" aria-label="Featured Product" data-showcase-container>
    <div class="showcase-compact-card single-card" data-product-card="<?= $prod['id'] ?>">
      <!-- Thumbnail Column -->
      <a href="<?= htmlspecialchars($prodUrl) ?>" class="compact-thumb-link" aria-label="<?= htmlspecialchars($prod['name']) ?>">
        <div class="compact-thumb-frame">
          <img 
            src="<?= htmlspecialchars($prodImg) ?>" 
            alt="<?= htmlspecialchars($prod['name']) ?>" 
            class="compact-thumb-img"
            loading="lazy"
            onerror="this.onerror=null;this.src='<?= url('assets/images/no-image.svg') ?>';"
            data-original-img="<?= htmlspecialchars($prodImg) ?>"
          >
          <?php if ($hasDiscount): ?>
            <span class="compact-discount-pill">-<?= $discountPct ?>%</span>
          <?php endif; ?>
        </div>
      </a>

      <!-- Middle Info Column -->
      <div class="compact-info-col">
        <!-- Product Title -->
        <h4 class="compact-title-text">
          <a href="<?= htmlspecialchars($prodUrl) ?>"><?= htmlspecialchars($prod['name']) ?></a>
        </h4>

        <!-- Price & Variant Selection Row -->
        <div class="compact-sub-row">
          <div class="compact-price-wrap">
            <span class="compact-current-price" data-price-display>$<?= number_format($basePrice, 2) ?></span>
            <?php if ($hasDiscount): ?>
              <span class="compact-compare-price">$<?= number_format($comparePrice, 2) ?></span>
            <?php endif; ?>
          </div>

          <?php if ($hasVariants): ?>
            <div class="compact-variant-picker-box">
              <select 
                class="showcase-variant-select compact-variant-select" 
                data-prod-id="<?= $prod['id'] ?>"
                aria-label="Select Flavor or Option"
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
                    <?= htmlspecialchars($v['variant_name']) ?><?= ($vPrice != $basePrice) ? ' — $' . number_format($vPrice, 2) : '' ?><?= $isVOutOfStock ? ' (OOS)' : '' ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="compact-select-chevron">
                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Right Action Column -->
      <div class="compact-actions-col">
        <!-- Quantity Stepper -->
        <div class="showcase-qty-stepper compact-qty-stepper">
          <button type="button" class="btn-qty-step btn-qty-minus" aria-label="Decrease quantity">−</button>
          <input type="number" class="showcase-qty-input" value="1" min="1" max="99" readonly aria-label="Quantity">
          <button type="button" class="btn-qty-step btn-qty-plus" aria-label="Increase quantity">+</button>
        </div>

        <!-- Add to Cart Button -->
        <button 
          type="button" 
          class="btn-showcase-add-cart compact-btn-add" 
          data-product-id="<?= $prod['id'] ?>"
          data-variant-id="<?= $defaultVariantId ?>"
          <?= (!$isInStock) ? 'disabled' : '' ?>
        >
          <svg class="cart-icon" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
          <span class="btn-cart-text"><?= $isInStock ? 'Add to Cart' : 'Out of Stock' ?></span>
        </button>

        <!-- View Details Button -->
        <a href="<?= htmlspecialchars($prodUrl) ?>" class="btn-compact-view" title="View Full Product Page">
          <span>Details</span>
          <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </a>
      </div>
    </div>
  </aside>

<?php else: ?>
  <!-- 2-Product Compact Comparison Showcase -->
  <aside class="blog-inline-showcase comparison-compact-wrap my-lg" aria-label="Product Comparison" data-showcase-container>
    <div class="compact-comparison-grid">
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
        $specPuffs = !empty($prod['spec_puffs']) ? $prod['spec_puffs'] : '';
        $variants = !empty($prod['variants']) && is_array($prod['variants']) ? $prod['variants'] : [];
        $hasVariants = !empty($variants);
        $defaultVariantId = $hasVariants ? ($variants[0]['id'] ?? '') : '';
      ?>
        <div class="showcase-compact-card comparison-card <?= ($idx === 0) ? 'is-winner' : '' ?>" data-product-card="<?= $prod['id'] ?>">
          <!-- Thumbnail Column -->
          <a href="<?= htmlspecialchars($prodUrl) ?>" class="compact-thumb-link" aria-label="<?= htmlspecialchars($prod['name']) ?>">
            <div class="compact-thumb-frame">
              <img 
                src="<?= htmlspecialchars($prodImg) ?>" 
                alt="<?= htmlspecialchars($prod['name']) ?>" 
                class="compact-thumb-img"
                loading="lazy"
                onerror="this.onerror=null;this.src='<?= url('assets/images/no-image.svg') ?>';"
                data-original-img="<?= htmlspecialchars($prodImg) ?>"
              >
              <?php if ($hasDiscount): ?>
                <span class="compact-discount-pill">-<?= $discountPct ?>%</span>
              <?php endif; ?>
            </div>
          </a>

          <!-- Middle Info Column -->
          <div class="compact-info-col">
            <!-- Product Title -->
            <h4 class="compact-title-text">
              <a href="<?= htmlspecialchars($prodUrl) ?>"><?= htmlspecialchars($prod['name']) ?></a>
            </h4>

            <!-- Price & Variant Row -->
            <div class="compact-sub-row">
              <div class="compact-price-wrap">
                <span class="compact-current-price" data-price-display>$<?= number_format($basePrice, 2) ?></span>
                <?php if ($hasDiscount): ?>
                  <span class="compact-compare-price">$<?= number_format($comparePrice, 2) ?></span>
                <?php endif; ?>
              </div>

              <?php if ($hasVariants): ?>
                <div class="compact-variant-picker-box">
                  <select 
                    class="showcase-variant-select compact-variant-select" 
                    data-prod-id="<?= $prod['id'] ?>"
                    aria-label="Select Flavor or Option"
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
                        <?= htmlspecialchars($v['variant_name']) ?><?= ($vPrice != $basePrice) ? ' — $' . number_format($vPrice, 2) : '' ?><?= $isVOutOfStock ? ' (OOS)' : '' ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <div class="compact-select-chevron">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                  </div>
                </div>
              <?php endif; ?>
            </div>

            <!-- Action Controls for Comparison Card -->
            <div class="compact-card-bottom-actions">
              <button 
                type="button" 
                class="btn-showcase-add-cart compact-btn-add" 
                data-product-id="<?= $prod['id'] ?>"
                data-variant-id="<?= $defaultVariantId ?>"
                <?= (!$isInStock) ? 'disabled' : '' ?>
              >
                <svg class="cart-icon" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                <span class="btn-cart-text"><?= $isInStock ? 'Add to Cart' : 'Out of Stock' ?></span>
              </button>

              <a href="<?= htmlspecialchars($prodUrl) ?>" class="btn-compact-view" title="View Full Details">
                <span>Details</span>
                <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
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

          // Update Dynamic Price Display
          const priceDisplay = card.querySelector('[data-price-display]');
          if (priceDisplay && price) {
            priceDisplay.textContent = '$' + parseFloat(price).toFixed(2);
          }

          // Update Dynamic Image if custom variant image exists
          const imgEl = card.querySelector('.compact-thumb-img, .showcase-img');
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
              stockBadge.className = 'compact-stock-badge out-of-stock';
            }
            if (stockText) stockText.textContent = 'Out of Stock';
            if (addBtn) {
              addBtn.disabled = true;
              if (btnText) btnText.textContent = 'Out of Stock';
            }
          } else {
            if (stockBadge) {
              stockBadge.className = 'compact-stock-badge in-stock';
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
