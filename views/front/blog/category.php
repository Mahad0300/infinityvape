<?php
$category = $category ?? [];
$category_name = $category['name'] ?? 'Blog Category';
$category_slug = $category['slug'] ?? '';

$categoryResult = $categoryResult ?? ['data' => [], 'total' => 0, 'current_page' => 1, 'last_page' => 1];
$posts = $posts ?? ($categoryResult['data'] ?? []);
$current_page = (int) ($categoryResult['current_page'] ?? 1);
$last_page = (int) ($categoryResult['last_page'] ?? 1);

$categories = $categories ?? [];
$recentPosts = $recentPosts ?? [];
$popularPosts = $popularPosts ?? [];
$archiveMonths = $archiveMonths ?? [];
$totalPostsCount = $totalPostsCount ?? array_sum(array_column($categories, 'post_count'));

$page_title = htmlspecialchars($category_name) . ' | Infinity Vape Shop Blog';
$page_js = 'blog';

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
        <?= htmlspecialchars($category_name) ?>
      </li>
    </ol>
  </div>
</nav>

<!-- ==========================================
     2. CATEGORY ARCHIVE MAIN LAYOUT
     ========================================== -->
<section class="tag-layout-section">
  <div class="container">
    <div class="blog-layout-grid">
      
      <!-- ========================================
           LEFT COLUMN: CATEGORY TITLE & ARTICLES
           ======================================== -->
      <div class="tag-main-col">
        
        <!-- Category Page H1 Heading -->
        <div class="tag-page-header">
          <h1 class="tag-page-title"><?= htmlspecialchars($category_name) ?></h1>
          <?php if (!empty($category['description'])): ?>
            <p class="tag-page-desc" style="color: #64748b; font-size: 15px; margin-top: 8px; line-height: 1.5;">
              <?= htmlspecialchars($category['description']) ?>
            </p>
          <?php endif; ?>
        </div>

        <!-- Horizontal Articles List -->
        <div class="tag-posts-list" id="tagPostsList">
          <?php if (!empty($posts)): ?>
            <?php
            foreach ($posts as $tPost):
              $tSlug = !empty($tPost['slug']) ? $tPost['slug'] : $tPost['id'];
              $tUrl = url(urlencode($tSlug));
              $tImg = !empty($tPost['featured_image_url']) ? $tPost['featured_image_url'] : 'assets/images/blog-1.jpg';
              if (!preg_match('~^(https?://|//|#)~i', $tImg)) {
                $tImg = url($tImg);
              }
              $tCatName = !empty($tPost['category_name']) ? $tPost['category_name'] : $category_name;
              $tCatSlug = !empty($tPost['category_slug']) ? $tPost['category_slug'] : $category_slug;
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
                    <span class="meta-author"><?= htmlspecialchars(!empty($tPost['author_name']) ? $tPost['author_name'] : 'Infinity Vape Shop') ?></span>
                    <span class="meta-separator">|</span>
                    <span class="meta-date"><?= htmlspecialchars(function_exists('blog_time_display') ? blog_time_display($tPost['published_at'] ?? '') : date('M d, Y', strtotime($tPost['published_at'] ?? 'now'))) ?></span>
                  </div>
                  <p class="tag-post-excerpt">
                    <?= htmlspecialchars(mb_strimwidth($tPost['excerpt'] ?? '', 0, 180, '...')) ?>
                  </p>
                  <div class="tag-post-badges" style="display: flex; align-items: center; justify-content: space-between; margin-top: 14px;">
                    <a href="<?= url($category_slug) ?>" class="tag-badge-pill"><?= htmlspecialchars($category_name) ?></a>
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
              <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="#94a3b8" stroke-width="1.5" style="margin-bottom: 12px;">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
              </svg>
              <h3 style="font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 6px;">No Articles Found</h3>
              <p>There are no articles published under "<strong><?= htmlspecialchars($category_name) ?></strong>" yet.</p>
              <a href="<?= url('blog') ?>" class="tag-badge-pill" style="margin-top: 15px; display: inline-block;">Back to Blog Home</a>
            </div>
          <?php endif; ?>
        </div>

        <!-- ==============================================
             PAGINATION
             ============================================== -->
        <?php if ($last_page > 1): ?>
        <nav class="tag-pagination-nav" aria-label="Category Articles Pagination" style="margin-top: 30px;">
          <ul class="tag-pagination-list">
            <li class="page-item">
              <a href="<?= url($category_slug) ?>?page=<?= max(1, $current_page - 1) ?>" class="page-link prev-btn <?= $current_page <= 1 ? 'disabled' : '' ?>" aria-label="Previous Page">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5">
                  <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
              </a>
            </li>
            <?php for ($p = 1; $p <= $last_page; $p++): ?>
              <li class="page-item">
                <a href="<?= url($category_slug) ?>?page=<?= $p ?>" class="page-link page-num-btn <?= $current_page === $p ? 'active' : '' ?>"><?= $p ?></a>
              </li>
            <?php endfor; ?>
            <li class="page-item">
              <a href="<?= url($category_slug) ?>?page=<?= min($last_page, $current_page + 1) ?>" class="page-link next-btn <?= $current_page >= $last_page ? 'disabled' : '' ?>" aria-label="Next Page">
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
           RIGHT COLUMN: SIDEBAR (4 KEY WIDGETS)
           ======================================== -->
      <aside class="blog-sidebar-col" aria-label="Blog Sidebar">
        
        <!-- SEARCH BAR -->
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

        <!-- 1. CATEGORIES CARD (ACTIVE HIGHLIGHT) -->
        <div class="blog-widget blog-card-widget blog-categories-widget">
          <h3 class="blog-widget-title">Categories</h3>
          <ul class="blog-categories-list" id="blogCategoriesList">
            <li class="category-list-item">
              <a href="<?= url('blog') ?>" class="category-link">
                <span class="category-name">All Articles</span>
                <span class="category-count">(<?= (int) $totalPostsCount ?>)</span>
              </a>
            </li>
            <?php
            foreach ($categories as $cat):
              $isActiveCat = (!empty($category_slug) && $category_slug === ($cat['slug'] ?? ''));
              ?>
              <li class="category-list-item <?= $isActiveCat ? 'active' : '' ?>">
                <a href="<?= url($cat['slug']) ?>" class="category-link">
                  <span class="category-name"><?= htmlspecialchars($cat['name']) ?></span>
                  <span class="category-count">(<?= (int) ($cat['post_count'] ?? 0) ?>)</span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>

        <!-- 2. RECENT POSTS CARD -->
        <?php if (!empty($recentPosts)): ?>
        <div class="blog-widget blog-card-widget blog-recent-posts-widget">
          <h3 class="blog-widget-title">Recent Posts</h3>
          <ul class="recent-posts-list">
            <?php
            foreach ($recentPosts as $rp):
              $rpSlug = !empty($rp['slug']) ? $rp['slug'] : $rp['id'];
              $rpUrl = url(urlencode($rpSlug));
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

        <!-- 3. POPULAR POSTS CARD -->
        <?php if (!empty($popularPosts)): ?>
        <div class="blog-widget blog-card-widget blog-popular-posts-widget">
          <h3 class="blog-widget-title">Popular Posts</h3>
          <ul class="popular-posts-list">
            <?php
            foreach ($popularPosts as $pop):
              $popSlug = !empty($pop['slug']) ? $pop['slug'] : $pop['id'];
              $popUrl = url(urlencode($popSlug));
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

        <!-- 4. ARCHIVE CARD -->
        <div class="blog-widget blog-card-widget blog-archive-widget">
          <h3 class="blog-widget-title">Archive</h3>
          <ul class="archive-list">
            <?php if (!empty($archiveMonths)): ?>
              <?php foreach ($archiveMonths as $arch): ?>
                <li class="archive-item">
                  <a href="<?= url('blog?archive=' . urlencode($arch['ym'])) ?>" class="archive-link"><?= htmlspecialchars($arch['label']) ?> (<?= (int) $arch['count'] ?>)</a>
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

