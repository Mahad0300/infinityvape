<?php

$page_title = 'Infinity Vape Shop Blog | Vaping Guides, Reviews & Industry News';
$page_js    = 'blog';

$featuredGuides  = $featuredGuides ?? [];
$topReviews      = $topReviews ?? [];
$flavorProfiles  = $flavorProfiles ?? [];
$hempAndPouches  = $hempAndPouches ?? [];
$latestPosts     = $latestPosts ?? [];
$categories      = $categories ?? [];
$recentPosts     = $recentPosts ?? [];
$popularPosts    = $popularPosts ?? [];
$archiveMonths   = $archiveMonths ?? [];
$allTags         = $allTags ?? [];
$totalPostsCount = $totalPostsCount ?? 0;
$archiveActive   = !empty($archiveActive);
$archiveLabel    = $archiveLabel ?? 'Archive';
$archiveYm       = $archiveYm ?? '';
$archivePosts    = $archivePosts ?? [];
$archivePage     = (int) ($archivePage ?? 1);
$archiveLastPage = (int) ($archiveLastPage ?? 1);

if (!function_exists('get_blog_card_url')) {
    function get_blog_card_url($post) {
        $slug = !empty($post['slug']) ? $post['slug'] : ($post['id'] ?? '');
        return url(urlencode($slug));
    }
}

if (!function_exists('get_blog_card_img')) {
    function get_blog_card_img($post, $fallback = 'assets/images/blog-1.jpg') {
        $img = !empty($post['featured_image_url']) ? $post['featured_image_url'] : $fallback;
        if (!preg_match('~^(https?://|//|#)~i', $img)) {
            $img = url($img);
        }
        return $img;
    }
}

include dirname(__DIR__) . '/includes/header.php';
?>

<!-- ==========================================
     1. BREADCRUMBS
     ========================================== -->
<nav class="blog-breadcrumb-nav" aria-label="Breadcrumb">
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
      <?php if ($archiveActive): ?>
      <li class="breadcrumb-item">
        <a href="<?= url('blog') ?>">Blog</a>
      </li>
      <li class="breadcrumb-separator" aria-hidden="true">
        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </li>
      <li class="breadcrumb-item active" aria-current="page">
        <?= htmlspecialchars($archiveLabel) ?>
      </li>
      <?php else: ?>
      <li class="breadcrumb-item active" aria-current="page">
        Blog
      </li>
      <?php endif; ?>
    </ol>
  </div>
</nav>

<!-- ==========================================
     2. BLOG HEADER (TITLE & SUBTITLE)
     ========================================== -->
<header class="blog-header-section">
  <div class="container">
    <div class="blog-header-content">
      <?php if ($archiveActive): ?>
      <h1 class="blog-main-title"><?= htmlspecialchars($archiveLabel) ?></h1>
      <p class="blog-main-subtitle">
        <?= $archiveYm !== '' ? 'Articles published in ' . htmlspecialchars($archiveLabel) . '.' : 'This archive month is not available.' ?>
      </p>
      <?php else: ?>
      <h1 class="blog-main-title">Infinity Vape Shop Blog</h1>
      <p class="blog-main-subtitle">
        Your go-to resource for vaping guides, honest product reviews, flavor recommendations, and the latest industry news. Whether you are new to vaping or a seasoned enthusiast, we have got you covered.
      </p>
      <?php endif; ?>
    </div>
  </div>
</header>

<!-- ==========================================
     3. MAIN BLOG LAYOUT (CONTENT + SIDEBAR)
     ========================================== -->
<section class="blog-layout-section">
  <div class="container">
    <div class="blog-layout-grid">
      
      <!-- ========================================
           LEFT COLUMN: MAIN CONTENT
           ======================================== -->
      <main class="blog-main-col">
        <?php if ($archiveActive): ?>
        <div class="tag-posts-list" id="tagPostsList">
          <?php if (!empty($archivePosts)): ?>
            <?php foreach ($archivePosts as $tPost):
              $tSlug = !empty($tPost['slug']) ? $tPost['slug'] : ($tPost['id'] ?? '');
              $tUrl = url(urlencode($tSlug));
              $tImg = !empty($tPost['featured_image_url']) ? $tPost['featured_image_url'] : 'assets/images/blog-1.jpg';
              if (!preg_match('~^(https?://|//|#)~i', $tImg)) {
                $tImg = url($tImg);
              }
              $tCatName = trim(explode(',', (string)($tPost['category_name'] ?? ''))[0]);
              $tCatSlug = trim(explode(',', (string)($tPost['category_slug'] ?? ''))[0]);
            ?>
              <article class="tag-post-card">
                <a href="<?= htmlspecialchars($tUrl) ?>" class="tag-post-thumb-link" aria-label="<?= htmlspecialchars($tPost['title'] ?? '') ?>">
                  <img src="<?= htmlspecialchars($tImg) ?>" alt="<?= htmlspecialchars(image_alt_from_path($tImg, $tPost['title'] ?? '')) ?>" class="tag-post-thumb-img" loading="lazy" onerror="this.onerror=null;this.src='<?= url('assets/images/blog-1.jpg') ?>';">
                </a>
                <div class="tag-post-body">
                  <h2 class="tag-post-title">
                    <a href="<?= htmlspecialchars($tUrl) ?>"><?= htmlspecialchars($tPost['title'] ?? '') ?></a>
                  </h2>
                  <div class="tag-post-meta">
                    <span class="meta-author"><?= htmlspecialchars(!empty($tPost['author_name']) ? $tPost['author_name'] : 'Infinity Vape Shop') ?></span>
                    <span class="meta-separator">|</span>
                    <span class="meta-date"><?= htmlspecialchars(function_exists('blog_time_display') ? blog_time_display($tPost['published_at'] ?? '') : date('M d, Y', strtotime($tPost['published_at'] ?? 'now'))) ?></span>
                  </div>
                  <p class="tag-post-excerpt">
                    <?= htmlspecialchars(mb_strimwidth($tPost['excerpt'] ?? '', 0, 180, '...')) ?>
                  </p>
                  <div class="tag-post-badges" style="display: flex; align-items: center; justify-content: space-between; margin-top: 14px;">
                    <?php if ($tCatName !== '' && $tCatSlug !== ''): ?>
                      <a href="<?= url($tCatSlug) ?>" class="tag-badge-pill"><?= htmlspecialchars($tCatName) ?></a>
                    <?php else: ?>
                      <span></span>
                    <?php endif; ?>
                    <a href="<?= htmlspecialchars($tUrl) ?>" class="btn-read-more">
                      Read Article
                      <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          <?php else: ?>
            <div style="padding: 60px 0; text-align: center; color: #64748b; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0;">
              <h3 style="font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 6px;">No Articles Found</h3>
              <p><?= $archiveYm !== '' ? 'There are no articles published in <strong>' . htmlspecialchars($archiveLabel) . '</strong>.' : 'There are no articles for this archive.' ?></p>
              <a href="<?= url('blog') ?>" class="tag-badge-pill" style="margin-top: 15px; display: inline-block;">Back to Blog Home</a>
            </div>
          <?php endif; ?>
        </div>
        <?php if ($archiveYm !== '' && $archiveLastPage > 1): ?>
        <nav class="tag-pagination-nav" aria-label="Archive Pagination" style="margin-top: 30px;">
          <ul class="tag-pagination-list">
            <?php for ($p = 1; $p <= $archiveLastPage; $p++): ?>
              <li class="page-item">
                <a href="<?= url('blog?archive=' . urlencode($archiveYm) . '&page=' . $p) ?>" class="page-link page-num-btn <?= $archivePage === $p ? 'active' : '' ?>"><?= $p ?></a>
              </li>
            <?php endfor; ?>
          </ul>
        </nav>
        <?php endif; ?>
        <?php else: ?>

        <!-- SECTION 1: LATEST VAPING GUIDES -->
        <?php 
          $leadGuide = !empty($featuredGuides) ? $featuredGuides[0] : null;
          $sideGuides = !empty($featuredGuides) ? array_slice($featuredGuides, 1, 3) : [];
        ?>
        <?php if ($leadGuide): ?>
        <section class="blog-guides-section">
          <div class="blog-section-header">
            <h2 class="blog-section-heading">Latest Vaping Guides</h2>
          </div>

          <div class="blog-featured-grid">
            
            <!-- Left Sub-col: Big Lead Featured Post Card -->
            <?php 
              $leadUrl = get_blog_card_url($leadGuide);
              $leadImg = get_blog_card_img($leadGuide, 'assets/images/blog-1.jpg');
              $leadDate = !empty($leadGuide['published_at']) ? date('M d, Y', strtotime($leadGuide['published_at'])) : date('M d, Y');
            ?>
            <article class="featured-lead-card blog-post-item" data-category="<?= htmlspecialchars(strtolower($leadGuide['category_slug'] ?? 'vaping-guides')) ?>" data-title="<?= htmlspecialchars(strtolower($leadGuide['title'] ?? '')) ?>">
              <a href="<?= htmlspecialchars($leadUrl) ?>" class="featured-media-link">
                <img 
                  src="<?= htmlspecialchars($leadImg) ?>" 
                  alt="<?= htmlspecialchars(image_alt_from_path($leadImg, $leadGuide['title'] ?? '')) ?>" 
                  class="featured-lead-img"
                  loading="eager"
                  onerror="this.onerror=null;this.src='<?= url('assets/images/blog-1.jpg') ?>';"
                >
              </a>
              <div class="featured-lead-content">
                <h3 class="featured-lead-title">
                  <a href="<?= htmlspecialchars($leadUrl) ?>"><?= htmlspecialchars($leadGuide['title'] ?? '') ?></a>
                </h3>
                <div class="blog-meta-row">
                  <span class="meta-author"><?= htmlspecialchars(!empty($leadGuide['author_name']) ? $leadGuide['author_name'] : 'Infinity Vape Shop') ?></span>
                  <span class="meta-separator">|</span>
                  <span class="meta-date"><?= htmlspecialchars(blog_time_display($leadGuide['published_at'] ?? '')) ?></span>
                </div>
                <p class="featured-lead-excerpt">
                  <?= htmlspecialchars(mb_strimwidth($leadGuide['excerpt'] ?? '', 0, 180, '...')) ?>
                </p>
              </div>
            </article>

            <!-- Right Sub-col: 3 Stacked Horizontal Compact Cards -->
            <div class="featured-side-stack">
              <?php foreach ($sideGuides as $sPost): 
                $sUrl = get_blog_card_url($sPost);
                $sImg = get_blog_card_img($sPost, 'assets/images/blog-2.jpg');
                $sDate = !empty($sPost['published_at']) ? date('M d, Y', strtotime($sPost['published_at'])) : date('M d, Y');
              ?>
                <article class="side-post-card blog-post-item" data-category="<?= htmlspecialchars(strtolower($sPost['category_slug'] ?? 'vaping-guides')) ?>" data-title="<?= htmlspecialchars(strtolower($sPost['title'] ?? '')) ?>">
                  <a href="<?= htmlspecialchars($sUrl) ?>" class="side-post-thumb-link">
                    <img 
                      src="<?= htmlspecialchars($sImg) ?>" 
                      alt="<?= htmlspecialchars(image_alt_from_path($sImg, $sPost['title'] ?? '')) ?>" 
                      class="side-post-thumb-img"
                      onerror="this.onerror=null;this.src='<?= url('assets/images/blog-2.jpg') ?>';"
                    >
                  </a>
                  <div class="side-post-content">
                    <h4 class="side-post-title">
                      <a href="<?= htmlspecialchars($sUrl) ?>"><?= htmlspecialchars($sPost['title'] ?? '') ?></a>
                    </h4>
                    <div class="blog-meta-row">
                      <span class="meta-author"><?= htmlspecialchars(!empty($sPost['author_name']) ? $sPost['author_name'] : 'Infinity Vape Shop') ?></span>
                      <span class="meta-separator">|</span>
                      <span class="meta-date"><?= htmlspecialchars(blog_time_display($sPost['published_at'] ?? '')) ?></span>
                    </div>
                  </div>
                </article>
              <?php endforeach; ?>
            </div>

          </div>
        </section>
        <?php endif; ?>

        <!-- SECTION 2: TOP PRODUCT REVIEWS -->
        <?php if (!empty($topReviews)): ?>
        <section class="blog-top-reviews-section">
          <div class="blog-section-header">
            <h2 class="blog-section-heading">Top Product Reviews</h2>
          </div>

          <div class="top-reviews-grid">
            <?php foreach ($topReviews as $rev): 
              $revUrl = get_blog_card_url($rev);
              $revImg = get_blog_card_img($rev, 'assets/images/blog-3.jpg');
              $revDate = !empty($rev['published_at']) ? date('M d, Y', strtotime($rev['published_at'])) : date('M d, Y');
              $catName = !empty($rev['category_name']) ? $rev['category_name'] : 'Product Reviews';
            ?>
              <article class="top-review-card blog-post-item" data-category="<?= htmlspecialchars(strtolower($rev['category_slug'] ?? 'product-reviews')) ?>" data-title="<?= htmlspecialchars(strtolower($rev['title'] ?? '')) ?>">
                <a href="<?= htmlspecialchars($revUrl) ?>" class="top-review-card-link" aria-label="<?= htmlspecialchars($rev['title'] ?? '') ?>">
                  <img 
                    src="<?= htmlspecialchars($revImg) ?>" 
                    alt="<?= htmlspecialchars(image_alt_from_path($revImg, $rev['title'] ?? '')) ?>" 
                    class="top-review-bg-img"
                    loading="lazy"
                    onerror="this.onerror=null;this.src='<?= url('assets/images/blog-3.jpg') ?>';"
                  >
                  <div class="top-review-overlay"></div>
                  <div class="top-review-content">
                    <h3 class="top-review-title"><?= htmlspecialchars($rev['title'] ?? '') ?></h3>
                    <div class="top-review-meta">
                      <span class="meta-author"><?= htmlspecialchars(!empty($rev['author_name']) ? $rev['author_name'] : 'Infinity Vape Shop') ?></span>
                      <span class="meta-separator">|</span>
                      <span class="meta-date"><?= htmlspecialchars(blog_time_display($rev['published_at'] ?? '')) ?></span>
                    </div>
                    <div class="top-review-badges">
                      <span class="review-tag-badge"><?= htmlspecialchars($catName) ?></span>
                    </div>
                  </div>
                </a>
              </article>
            <?php endforeach; ?>
          </div>
        </section>
        <?php endif; ?>

        <!-- SECTION 3: EXPLORE FLAVOR PROFILES -->
        <?php $flavorLead = !empty($flavorProfiles) ? $flavorProfiles[0] : null; ?>
        <?php if ($flavorLead): 
          $fUrl = get_blog_card_url($flavorLead);
          $fImg = get_blog_card_img($flavorLead, 'assets/images/blog-4.jpg');
          $fDate = !empty($flavorLead['published_at']) ? date('M d, Y', strtotime($flavorLead['published_at'])) : date('M d, Y');
          $fCat = !empty($flavorLead['category_name']) ? $flavorLead['category_name'] : 'Flavor Profiles';
        ?>
        <section class="blog-flavor-profiles-section">
          <div class="blog-section-header">
            <h2 class="blog-section-heading">Explore Flavor Profiles</h2>
          </div>

          <article class="flavor-lead-card blog-post-item" data-category="<?= htmlspecialchars(strtolower($flavorLead['category_slug'] ?? 'flavor-profiles')) ?>" data-title="<?= htmlspecialchars(strtolower($flavorLead['title'] ?? '')) ?>">
            <a href="<?= htmlspecialchars($fUrl) ?>" class="flavor-lead-media-link" aria-label="<?= htmlspecialchars($flavorLead['title'] ?? '') ?>">
              <img 
                src="<?= htmlspecialchars($fImg) ?>" 
                alt="<?= htmlspecialchars(image_alt_from_path($fImg, $flavorLead['title'] ?? '')) ?>" 
                class="flavor-lead-img"
                loading="lazy"
                onerror="this.onerror=null;this.src='<?= url('assets/images/blog-4.jpg') ?>';"
              >
            </a>
            <div class="flavor-lead-content">
              <h3 class="flavor-lead-title">
                <a href="<?= htmlspecialchars($fUrl) ?>"><?= htmlspecialchars($flavorLead['title'] ?? '') ?></a>
              </h3>
              <div class="blog-meta-row">
                <span class="meta-author"><?= htmlspecialchars(!empty($flavorLead['author_name']) ? $flavorLead['author_name'] : 'Infinity Vape Shop') ?></span>
                <span class="meta-separator">|</span>
                <span class="meta-date"><?= htmlspecialchars(blog_time_display($flavorLead['published_at'] ?? '')) ?></span>
              </div>
              <p class="flavor-lead-excerpt">
                <?= htmlspecialchars(mb_strimwidth($flavorLead['excerpt'] ?? '', 0, 200, '...')) ?>
              </p>
              <div class="flavor-lead-badges">
                <span class="flavor-tag-badge"><?= htmlspecialchars($fCat) ?></span>
              </div>
            </div>
          </article>
        </section>
        <?php endif; ?>

        <!-- SECTION 4: NICOTINE POUCHES & HEMP GUIDES -->
        <?php if (!empty($hempAndPouches)): ?>
        <section class="blog-articles-grid-section">
          <div class="blog-section-header">
            <h2 class="blog-section-heading">Nicotine Pouches &amp; E-Liquid Guides</h2>
          </div>

          <!-- Active Filter Pill Bar (Hidden initially, visible when search or category is active) -->
          <div class="active-filter-bar" id="activeFilterBar" style="display: none;">
            <span class="filter-status-label">Filtered by: <strong id="currentFilterLabel">All</strong></span>
            <button type="button" class="btn-clear-filter" id="btnClearFilter">Clear filter &times;</button>
          </div>

          <div class="blog-articles-grid" id="blogArticlesGrid">
            <?php foreach ($hempAndPouches as $hp): 
              $hpUrl = get_blog_card_url($hp);
              $hpImg = get_blog_card_img($hp, 'assets/images/twin-banner.webp');
              $hpDate = !empty($hp['published_at']) ? date('M d, Y', strtotime($hp['published_at'])) : date('M d, Y');
              $hpCat = !empty($hp['category_name']) ? $hp['category_name'] : 'Guides';
            ?>
              <article class="blog-article-card blog-post-item" data-category="<?= htmlspecialchars(strtolower($hp['category_slug'] ?? 'nicotine-pouches-guide')) ?>" data-title="<?= htmlspecialchars(strtolower($hp['title'] ?? '')) ?>">
                <a href="<?= htmlspecialchars($hpUrl) ?>" class="article-card-thumb-link">
                  <img src="<?= htmlspecialchars($hpImg) ?>" alt="<?= htmlspecialchars(image_alt_from_path($hpImg, $hp['title'] ?? '')) ?>" class="article-card-img" loading="lazy" onerror="this.onerror=null;this.src='<?= url('assets/images/blog-1.jpg') ?>';">
                </a>
                <div class="article-card-body">
                  <h3 class="article-card-title">
                    <a href="<?= htmlspecialchars($hpUrl) ?>"><?= htmlspecialchars($hp['title'] ?? '') ?></a>
                  </h3>
                  <div class="blog-meta-row">
                    <span class="meta-author"><?= htmlspecialchars(!empty($hp['author_name']) ? $hp['author_name'] : 'Infinity Vape Shop') ?></span>
                    <span class="meta-separator">|</span>
                    <span class="meta-date"><?= htmlspecialchars(blog_time_display($hp['published_at'] ?? '')) ?></span>
                  </div>
                  <p class="article-card-excerpt">
                    <?= htmlspecialchars(mb_strimwidth($hp['excerpt'] ?? '', 0, 160, '...')) ?>
                  </p>
                  <div class="article-badges-row">
                    <span class="flavor-tag-badge"><?= htmlspecialchars($hpCat) ?></span>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        </section>
        <?php endif; ?>

        <!-- SECTION 5: LATEST POSTS -->
        <?php if (!empty($latestPosts)): ?>
        <section class="blog-latest-posts-section">
          <div class="blog-section-header">
            <h2 class="blog-section-heading">Latest Posts</h2>
          </div>

          <div class="latest-posts-grid">
            <?php foreach ($latestPosts as $lp): 
              $lpUrl = get_blog_card_url($lp);
              $lpImg = get_blog_card_img($lp, 'assets/images/blog-3.jpg');
              $lpDate = !empty($lp['published_at']) ? date('M d, Y', strtotime($lp['published_at'])) : date('M d, Y');
            ?>
              <article class="latest-post-card blog-post-item" data-category="<?= htmlspecialchars(strtolower($lp['category_slug'] ?? 'vaping-guides')) ?>" data-title="<?= htmlspecialchars(strtolower($lp['title'] ?? '')) ?>">
                <a href="<?= htmlspecialchars($lpUrl) ?>" class="latest-post-thumb-link">
                  <img src="<?= htmlspecialchars($lpImg) ?>" alt="<?= htmlspecialchars(image_alt_from_path($lpImg, $lp['title'] ?? '')) ?>" class="latest-post-thumb-img" loading="lazy" onerror="this.onerror=null;this.src='<?= url('assets/images/blog-3.jpg') ?>';">
                </a>
                <div class="latest-post-content">
                  <h3 class="latest-post-title">
                    <a href="<?= htmlspecialchars($lpUrl) ?>"><?= htmlspecialchars($lp['title'] ?? '') ?></a>
                  </h3>
                  <div class="latest-post-meta">
                    <span class="meta-author"><?= htmlspecialchars(!empty($lp['author_name']) ? $lp['author_name'] : 'Infinity Vape Shop') ?></span>
                    <span class="meta-date-read">| <?= htmlspecialchars(blog_time_display($lp['published_at'] ?? '')) ?></span>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        </section>
        <?php endif; ?>

        <?php endif; ?>

      </main>

      <!-- ========================================
           RIGHT COLUMN: SIDEBAR
           ======================================== -->
      <aside class="blog-sidebar-col" aria-label="Blog Sidebar">
        
        <!-- WIDGET 1: SEARCH BAR -->
        <div class="blog-widget blog-search-widget">
          <form class="blog-search-form" id="blogSearchForm" action="javascript:void(0);">
            <div class="search-input-wrapper">
              <input 
                type="text" 
                id="blogSearchInput" 
                class="blog-search-input" 
                placeholder="Search categories..." 
                autocomplete="off"
                aria-label="Search posts"
              >
              <button type="submit" class="blog-search-btn" aria-label="Search posts">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="11" cy="11" r="8"></circle>
                  <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
              </button>
            </div>
          </form>
        </div>

        <!-- WIDGET 1: CATEGORIES CARD -->
        <div class="blog-widget blog-card-widget blog-categories-widget">
          <h3 class="blog-widget-title">Categories</h3>
          <ul class="blog-categories-list" id="blogCategoriesList">
            <li class="category-list-item <?= $archiveActive ? '' : 'active' ?>">
              <a href="<?= url('blog') ?>" class="category-link">
                <span class="category-name">All Articles</span>
                <span class="category-count">(<?= (int)$totalPostsCount ?>)</span>
              </a>
            </li>
            <?php foreach ($categories as $cat): ?>
              <li class="category-list-item">
                <a href="<?= url($cat['slug']) ?>" class="category-link">
                  <span class="category-name"><?= htmlspecialchars($cat['name']) ?></span>
                  <span class="category-count">(<?= (int)($cat['post_count'] ?? 0) ?>)</span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>

        <!-- WIDGET 2: RECENT POSTS CARD -->
        <?php if (!empty($recentPosts)): ?>
        <div class="blog-widget blog-card-widget blog-recent-posts-widget">
          <h3 class="blog-widget-title">Recent Posts</h3>
          <ul class="recent-posts-list">
            <?php foreach ($recentPosts as $rp): 
              $rpUrl = get_blog_card_url($rp);
              $rpImg = get_blog_card_img($rp, 'assets/images/blog-1.jpg');
              $rpDate = !empty($rp['published_at']) ? date('M d, Y', strtotime($rp['published_at'])) : date('M d, Y');
            ?>
              <li class="recent-post-item">
                <a href="<?= htmlspecialchars($rpUrl) ?>" class="recent-post-thumb-link" aria-label="<?= htmlspecialchars($rp['title']) ?>">
                  <img src="<?= htmlspecialchars($rpImg) ?>" alt="<?= htmlspecialchars(image_alt_from_path($rpImg, $rp['title'])) ?>" class="recent-post-thumb-img" loading="lazy" onerror="this.onerror=null;this.src='<?= url('assets/images/blog-1.jpg') ?>';">
                </a>
                <div class="recent-post-info">
                  <h4 class="recent-post-title">
                    <a href="<?= htmlspecialchars($rpUrl) ?>"><?= htmlspecialchars($rp['title']) ?></a>
                  </h4>
                  <span class="recent-post-date"><?= htmlspecialchars($rpDate) ?></span>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>

        <!-- WIDGET 3: POPULAR POSTS CARD -->
        <?php if (!empty($popularPosts)): ?>
        <div class="blog-widget blog-card-widget blog-popular-posts-widget">
          <h3 class="blog-widget-title">Popular Posts</h3>
          <ul class="popular-posts-list">
            <?php foreach ($popularPosts as $pop): 
              $popUrl = get_blog_card_url($pop);
              $popImg = get_blog_card_img($pop, 'assets/images/blog-3.jpg');
              $popDate = !empty($pop['published_at']) ? date('M d, Y', strtotime($pop['published_at'])) : date('M d, Y');
            ?>
              <li class="popular-post-item">
                <a href="<?= htmlspecialchars($popUrl) ?>" class="popular-post-thumb-link" aria-label="<?= htmlspecialchars($pop['title']) ?>">
                  <img src="<?= htmlspecialchars($popImg) ?>" alt="<?= htmlspecialchars(image_alt_from_path($popImg, $pop['title'])) ?>" class="popular-post-thumb-img" loading="lazy" onerror="this.onerror=null;this.src='<?= url('assets/images/blog-3.jpg') ?>';">
                </a>
                <div class="popular-post-info">
                  <h4 class="popular-post-title">
                    <a href="<?= htmlspecialchars($popUrl) ?>"><?= htmlspecialchars($pop['title']) ?></a>
                  </h4>
                  <span class="popular-post-date"><?= htmlspecialchars($popDate) ?></span>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>

        <!-- WIDGET 4: ARCHIVE CARD -->
        <div class="blog-widget blog-card-widget blog-archive-widget">
          <h3 class="blog-widget-title">Archive</h3>
          <ul class="archive-list">
            <?php if (!empty($archiveMonths)): ?>
              <?php foreach ($archiveMonths as $arch):
                $isActiveArch = $archiveYm !== '' && $archiveYm === ($arch['ym'] ?? '');
              ?>
                <li class="archive-item">
                  <a href="<?= url('blog?archive=' . urlencode($arch['ym'])) ?>" class="archive-link<?= $isActiveArch ? ' active' : '' ?>"><?= htmlspecialchars($arch['label']) ?> (<?= (int)$arch['count'] ?>)</a>
                </li>
              <?php endforeach; ?>
            <?php else: ?>
              <li class="archive-item"><a href="javascript:void(0);" class="archive-link"><?= date('F Y') ?></a></li>
            <?php endif; ?>
          </ul>
        </div>

      </aside>

    </div>
  </div>
</section>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
