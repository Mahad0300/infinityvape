<?php
$query       = $query ?? '';
$products    = $products ?? [];
$collections = $collections ?? [];
$page_title  = $page_title ?? 'Search | Infinity Vape Shop';
$page_js     = $page_js ?? 'search';

include dirname(__DIR__) . '/includes/header.php';
?>

<nav class="category-breadcrumb-nav" aria-label="Breadcrumb">
  <div class="container">
    <ol class="breadcrumb-list">
      <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
      <li class="breadcrumb-separator" aria-hidden="true">
        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
      </li>
      <li class="breadcrumb-item active" aria-current="page">Search</li>
    </ol>
  </div>
</nav>

<section class="store-search-section">
  <div class="container">
    <?php if ($query === ''): ?>
      <h1 class="category-title">Search</h1>
      <p class="category-description">Type a product name or a category, then press Enter.</p>
    <?php else: ?>
      <div class="store-search-heading">
        <h1 class="category-title">Results for “<?= e($query) ?>”</h1>
        <p class="store-search-count">
          <?= count($collections) ?> <?= count($collections) === 1 ? 'category' : 'categories' ?>
          and <?= count($products) ?> <?= count($products) === 1 ? 'product' : 'products' ?>
        </p>
      </div>

      <?php if (!empty($collections)): ?>
        <h2 class="store-search-label">Categories</h2>
        <div class="store-search-categories">
          <?php foreach ($collections as $collection):
            $collUrl = \App\Models\Collection::getUrl($collection);
          ?>
            <a class="store-search-category" href="<?= e($collUrl) ?>">
              <?= e($collection['name'] ?? '') ?>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($products)): ?>
        <h2 class="store-search-label">Products</h2>
        <div class="category-product-grid" id="categoryProductGrid">
          <?php foreach ($products as $p):
            $pId = $p['id'];
            $pSlug = !empty($p['slug']) ? $p['slug'] : $pId;
            $pTitle = !empty($p['title']) ? $p['title'] : ($p['name'] ?? 'Vape Product');
            $pUrl = url('product/' . urlencode($pSlug));
            $pImg = !empty($p['featured_image']) && $p['featured_image'] !== 'assets/images/vape-image.webp'
                ? $p['featured_image']
                : 'assets/images/no-image.svg';
            if (!preg_match('~^(https?://|//|#)~i', $pImg)) {
                $pImg = url($pImg);
            }
            $basePrice = (float)($p['base_price'] ?? 0);
            $comparePrice = (float)($p['compare_price'] ?? 0);
            $hasDiscount = ($comparePrice > $basePrice);
            $discountPct = $hasDiscount ? round((($comparePrice - $basePrice) / $comparePrice) * 100) : 0;
            $brandName = !empty($p['brand']) ? $p['brand'] : '';
            $cardRevCount = isset($p['reviews_count']) ? (int)$p['reviews_count'] : 0;
            $cardRating = !empty($p['rating_avg']) ? (int)round($p['rating_avg']) : ($cardRevCount > 0 ? 5 : 0);
            $cardStars = ($cardRevCount > 0)
                ? (str_repeat('★', $cardRating) . str_repeat('☆', max(0, 5 - $cardRating)))
                : '☆☆☆☆☆';
          ?>
            <div class="category-product-card">
              <div class="card-media-box">
                <?php if ($hasDiscount && $discountPct > 0): ?>
                  <div class="card-badges-wrap">
                    <span class="badge-discount-tag">-<?= (int)$discountPct ?>%</span>
                  </div>
                <?php endif; ?>
                <a href="<?= e($pUrl) ?>" class="card-img-link">
                  <img src="<?= e($pImg) ?>" alt="<?= e($pTitle) ?>" class="card-product-img" loading="lazy" onerror="this.onerror=null;this.src='<?= url('assets/images/no-image.svg') ?>';">
                </a>
                <button type="button" class="btn-card-add-cart" data-id="<?= (int)$pId ?>" data-title="<?= e($pTitle) ?>" data-price="<?= e($basePrice) ?>">ADD TO CART</button>
              </div>
              <div class="card-info-box">
                <?php if ($brandName !== ''): ?>
                  <div class="card-brand-tag"><?= e($brandName) ?></div>
                <?php endif; ?>
                <h3 class="card-product-title">
                  <a href="<?= e($pUrl) ?>"><?= e($pTitle) ?></a>
                </h3>
                <div class="card-rating-row">
                  <span class="card-stars" style="<?= ($cardRevCount === 0) ? 'color: #cbd5e1;' : '' ?>"><?= $cardStars ?></span>
                  <span class="card-reviews-count">(<?= $cardRevCount ?>)</span>
                </div>
                <div class="card-price-row">
                  <span class="card-current-price">$<?= number_format($basePrice, 2) ?></span>
                  <?php if ($hasDiscount): ?>
                    <span class="card-old-price">$<?= number_format($comparePrice, 2) ?></span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if (empty($collections) && empty($products)): ?>
        <div class="store-search-empty">
          <h2>No results for “<?= e($query) ?>”</h2>
          <p>Try a product name or a category like Disposable, E-Liquids, or Nicotine Pouches.</p>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
