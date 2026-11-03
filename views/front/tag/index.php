<?php
$allTags       = $allTags ?? [];
$defaultTag    = !empty($allTags) ? $allTags[0] : 'puff-count';
$tag_slug      = isset($currentTag) && !empty($currentTag) ? htmlspecialchars(trim($currentTag)) : (isset($_GET['tag']) ? htmlspecialchars(trim($_GET['tag'])) : $defaultTag);
$tag_title     = $tagTitle ?? ucwords(str_replace(['-', '_'], ' ', $tag_slug));

$tagResult     = $tagResult ?? ['data' => [], 'total' => 0, 'current_page' => 1, 'last_page' => 1];
$tagPosts      = $tagResult['data'] ?? [];
$current_page  = (int)($tagResult['current_page'] ?? 1);
$last_page     = (int)($tagResult['last_page'] ?? 1);

$categories    = $categories ?? [];
$recentPosts   = $recentPosts ?? [];
$popularPosts  = $popularPosts ?? [];
$archiveMonths = $archiveMonths ?? [];

$page_title = $tag_title . ' | Infinity Vape Shop Blog';
$page_js    = 'blog';

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
      <li class="breadcrumb-item">
        <a href="<?= url('blog') ?>">Blog</a>
      </li>
      <li class="breadcrumb-separator" aria-hidden="true">
        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </li>
      <li class="breadcrumb-item active" aria-current="page">
        <?= htmlspecialchars($tag_title) ?>
      </li>
    </ol>
  </div>
</nav>

<!-- ==========================================
     2. TAG PAGE MAIN LAYOUT (CONTENT + SIDEBAR)
     ========================================== -->
<section class="tag-layout-section">
  <div class="container">
    <div class="blog-layout-grid">
      
      <!-- ========================================
           LEFT COLUMN: TAG TITLE & ARTICLE CARDS
           ======================================== -->
      <div class="tag-main-col">
        
        <!-- Tag Page H1 Heading -->
        <div class="tag-page-header">
          <h1 class="tag-page-title"><?= htmlspecialchars($tag_title) ?></h1>
        </div>

        <!-- Tag Horizontal Posts List -->
        <div class="tag-posts-list" id="tagPostsList">
          <?php if (!empty($tagPosts)): ?>
            <?php foreach ($tagPosts as $tPost): 
              $tSlug = !empty($tPost['slug']) ? $tPost['slug'] : $tPost['id'];
              $tUrl = url('blog-detail?slug=' . urlencode($tSlug));
              $tImg = !empty($tPost['featured_image_url']) ? $tPost['featured_image_url'] : 'assets/images/blog-1.jpg';
              if (!preg_match('~^(https?://|//|#)~i', $tImg)) {
                  $tImg = url($tImg);
              }
              $tDate = !empty($tPost['published_at']) ? date('M d, Y', strtotime($tPost['published_at'])) : date('M d, Y');
              $tCatName = !empty($tPost['category_name']) ? $tPost['category_name'] : 'Vaping Guides';
              $tCatSlug = !empty($tPost['category_slug']) ? $tPost['category_slug'] : 'vaping-guides';
            ?>
              <article class="tag-post-card">
                <a href="<?= htmlspecialchars($tUrl) ?>" class="tag-post-thumb-link" aria-label="<?= htmlspecialchars($tPost['title']) ?>">
                  <img src="<?= htmlspecialchars($tImg) ?>" alt="<?= htmlspecialchars($tPost['title']) ?>" class="tag-post-thumb-img" loading="lazy" onerror="this.onerror=null;this.src='<?= url('assets/images/blog-1.jpg') ?>';">
                </a>
                <div class="tag-post-body">
                  <h2 class="tag-post-title">
                    <a href="<?= htmlspecialchars($tUrl) ?>"><?= htmlspecialchars($tPost['title']) ?></a>
                  </h2>
                  <div class="tag-post-meta">
                    <span class="meta-author">New Vape</span>
                    <span class="meta-separator">|</span>
                    <span class="meta-date"><?= htmlspecialchars(blog_time_display($tPost['published_at'] ?? '')) ?></span>
                  </div>
                  <p class="tag-post-excerpt">
                    <?= htmlspecialchars(mb_strimwidth($tPost['excerpt'] ?? '', 0, 180, '...')) ?>
                  </p>
                  <div class="tag-post-badges">
                    <a href="<?= url('blog?cat=' . urlencode($tCatSlug)) ?>" class="tag-badge-pill"><?= htmlspecialchars($tCatName) ?></a>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          <?php else: ?><!--  -->
            <div style="padding: 40px 0; text-align: center; color: #64748b;">
              <p>No articles found for tag "<strong><?= htmlspecialchars($tag_title) ?></strong>".</p>
              <a href="<?= url('blog') ?>" class="tag-badge-pill" style="margin-top: 15px; display: inline-block;">Back to All Blogs</a>
            </div>
          <?php endif; ?>
        </div>

        <!-- ==============================================
             PAGINATION
             ============================================== -->
        <?php if ($last_page > 1): ?>
        <nav class="tag-pagination-nav" aria-label="Tag Articles Pagination">
          <ul class="tag-pagination-list">
            <li class="page-item">
              <a href="<?= url('tag?tag=' . urlencode($tag_slug) . '&page=' . max(1, $current_page - 1)) ?>" class="page-link prev-btn <?= $current_page <= 1 ? 'disabled' : '' ?>" id="tagPrevBtn" aria-label="Previous Page">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5">
                  <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
              </a>
            </li>
            <?php for ($p = 1; $p <= $last_page; $p++): ?>
              <li class="page-item">
                <a href="<?= url('tag?tag=' . urlencode($tag_slug) . '&page=' . $p) ?>" class="page-link page-num-btn <?= $current_page === $p ? 'active' : '' ?>"><?= $p ?></a>
              </li>
            <?php endfor; ?>
            <li class="page-item">
              <a href="<?= url('tag?tag=' . urlencode($tag_slug) . '&page=' . min($last_page, $current_page + 1)) ?>" class="page-link next-btn <?= $current_page >= $last_page ? 'disabled' : '' ?>" id="tagNextBtn" aria-label="Next Page">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5">
                  <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
              </a>
            </li>
          </ul>
        </nav>
        <?php endif; ?>

      </div>

      <!-- ========================================
           RIGHT COLUMN: SIDEBAR
           ======================================== -->
      <aside class="blog-sidebar-col" aria-label="Blog Sidebar">
        
        <!-- WIDGET 1: SEARCH BAR -->
        <div class="blog-widget blog-search-widget">
          <form class="blog-search-form" id="blogSearchForm" action="<?= url('blog') ?>" method="GET">
            <div class="search-input-wrapper">
              <input 
                type="text" 
                name="search"
                id="blogSearchInput" 
                class="blog-search-input" 
                placeholder="Search posts here..." 
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

        <!-- WIDGET 2: CATEGORIES CARD -->
        <div class="blog-widget blog-card-widget blog-categories-widget">
          <h3 class="blog-widget-title">Categories</h3>
          <ul class="blog-categories-list" id="blogCategoriesList">
            <li class="category-list-item">
              <a href="<?= url('blog') ?>" class="category-link">
                <span class="category-name">All Articles</span>
              </a>
            </li>
            <?php foreach ($categories as $cat): ?>
              <li class="category-list-item">
                <a href="<?= url('blog?cat=' . urlencode($cat['slug'])) ?>" class="category-link">
                  <span class="category-name"><?= htmlspecialchars($cat['name']) ?></span>
                  <span class="category-count">(<?= (int)($cat['post_count'] ?? 0) ?>)</span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>

        <!-- WIDGET 3: RECENT POSTS CARD -->
        <?php if (!empty($recentPosts)): ?>
        <div class="blog-widget blog-card-widget blog-recent-posts-widget">
          <h3 class="blog-widget-title">Recent Posts</h3>
          <ul class="recent-posts-list">
            <?php foreach ($recentPosts as $rp): 
              $rpSlug = !empty($rp['slug']) ? $rp['slug'] : $rp['id'];
              $rpUrl = url('blog-detail?slug=' . urlencode($rpSlug));
              $rpImg = !empty($rp['featured_image_url']) ? $rp['featured_image_url'] : 'assets/images/blog-1.jpg';
              if (!preg_match('~^(https?://|//|#)~i', $rpImg)) {
                  $rpImg = url($rpImg);
              }
              $rpDate = !empty($rp['published_at']) ? date('M d, Y', strtotime($rp['published_at'])) : date('M d, Y');
            ?>
              <li class="recent-post-item">
                <a href="<?= htmlspecialchars($rpUrl) ?>" class="recent-post-thumb-link" aria-label="<?= htmlspecialchars($rp['title']) ?>">
                  <img src="<?= htmlspecialchars($rpImg) ?>" alt="<?= htmlspecialchars($rp['title']) ?>" class="recent-post-thumb-img" loading="lazy" onerror="this.onerror=null;this.src='<?= url('assets/images/blog-1.jpg') ?>';">
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

        <!-- WIDGET 4: POPULAR POSTS CARD -->
        <?php if (!empty($popularPosts)): ?>
        <div class="blog-widget blog-card-widget blog-popular-posts-widget">
          <h3 class="blog-widget-title">Popular Posts</h3>
          <ul class="popular-posts-list">
            <?php foreach ($popularPosts as $pop): 
              $popSlug = !empty($pop['slug']) ? $pop['slug'] : $pop['id'];
              $popUrl = url('blog-detail?slug=' . urlencode($popSlug));
              $popImg = !empty($pop['featured_image_url']) ? $pop['featured_image_url'] : 'assets/images/blog-3.jpg';
              if (!preg_match('~^(https?://|//|#)~i', $popImg)) {
                  $popImg = url($popImg);
              }
              $popDate = !empty($pop['published_at']) ? date('M d, Y', strtotime($pop['published_at'])) : date('M d, Y');
            ?>
              <li class="popular-post-item">
                <a href="<?= htmlspecialchars($popUrl) ?>" class="popular-post-thumb-link" aria-label="<?= htmlspecialchars($pop['title']) ?>">
                  <img src="<?= htmlspecialchars($popImg) ?>" alt="<?= htmlspecialchars($pop['title']) ?>" class="popular-post-thumb-img" loading="lazy" onerror="this.onerror=null;this.src='<?= url('assets/images/blog-3.jpg') ?>';">
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

        <!-- WIDGET 5: ARCHIVE CARD -->
        <div class="blog-widget blog-card-widget blog-archive-widget">
          <h3 class="blog-widget-title">Archive</h3>
          <ul class="archive-list">
            <?php if (!empty($archiveMonths)): ?>
              <?php foreach ($archiveMonths as $arch): ?>
                <li class="archive-item">
                  <a href="<?= url('blog?archive=' . urlencode($arch['ym'])) ?>" class="archive-link"><?= htmlspecialchars($arch['label']) ?> (<?= (int)$arch['count'] ?>)</a>
                </li>
              <?php endforeach; ?>
            <?php else: ?>
              <li class="archive-item"><a href="javascript:void(0);" class="archive-link"><?= date('F Y') ?></a></li>
            <?php endif; ?>
          </ul>
        </div>

        <!-- WIDGET 6: TAGS CARD -->
        <div class="blog-widget blog-card-widget blog-tags-widget">
          <h3 class="blog-widget-title">Tags</h3>
          <div class="blog-tags-cloud">
            <?php if (!empty($allTags)): ?>
              <?php foreach ($allTags as $tg): ?>
                <a href="<?= url('tag?tag=' . urlencode(strtolower(str_replace(' ', '-', $tg)))) ?>" class="blog-tag-pill <?= (strtolower(str_replace(' ', '-', $tg)) === strtolower(str_replace(' ', '-', $tag_slug))) ? 'active' : '' ?>"><?= htmlspecialchars($tg) ?></a>
              <?php endforeach; ?>
            <?php else: ?>
              <a href="<?= url('tag?tag=puff-count') ?>" class="blog-tag-pill">Puff Count</a>
              <a href="<?= url('tag?tag=new-releases') ?>" class="blog-tag-pill">New Releases</a>
              <a href="<?= url('tag?tag=product-reviews') ?>" class="blog-tag-pill">Product Reviews</a>
            <?php endif; ?>
          </div>
        </div>

      </aside>

    </div>
  </div>
</section>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
