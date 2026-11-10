<?php
$post = $post ?? [];
$relatedPosts = $relatedPosts ?? [];
$categories = $categories ?? [];
$recentPosts = $recentPosts ?? [];
$popularPosts = $popularPosts ?? [];
$allTags = $allTags ?? [];

$postTitle = !empty($post['title']) ? $post['title'] : 'Blog Article';
$postSlug = !empty($post['slug']) ? $post['slug'] : '';
$postCatName = !empty($post['category_name']) ? $post['category_name'] : 'Vaping Guides';
$postCatSlug = !empty($post['category_slug']) ? $post['category_slug'] : 'vaping-guides';
$postAuthor = !empty($post['author_name']) ? $post['author_name'] : 'Infinity Vape Shop';
$postDate = !empty($post['published_at']) ? date('M d, Y', strtotime($post['published_at'])) : date('M d, Y');
$postReadTime = !empty($post['read_time']) ? $post['read_time'] : '5 min read';
$postImg = !empty($post['featured_image_url']) ? $post['featured_image_url'] : 'assets/images/no-image.svg';
if (!preg_match('~^(https?://|//|#)~i', $postImg)) {
  $postImg = url($postImg);
}

if (session_status() === PHP_SESSION_NONE) {
  @session_start();
}

// Resolve active category for breadcrumb navigation (especially for multi-category posts)
$activeCategory = null;
$postCategories = !empty($post['categories']) ? $post['categories'] : [];

// 1. Candidate slug from ?cat= or ?from=
$candidateSlug = !empty($_GET['cat']) ? trim($_GET['cat']) : (!empty($_GET['from']) ? trim($_GET['from']) : null);

// 2. Candidate slug from HTTP Referer (e.g. came from /nicotine-pouches-guide or /vaping-guides)
if (!$candidateSlug && !empty($_SERVER['HTTP_REFERER'])) {
  $refPath = trim((string)parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH), '/');
  if (!empty($refPath)) {
    $parts = explode('/', $refPath);
    $candidateSlug = end($parts);
  }
}

// 3. Candidate slug from Session (last category visited in current session)
if (!$candidateSlug && !empty($_SESSION['last_blog_category'])) {
  $candidateSlug = $_SESSION['last_blog_category'];
}

// Match candidate against post's categories
if (!empty($candidateSlug) && !empty($postCategories)) {
  foreach ($postCategories as $c) {
    if (strtolower($c['slug']) === strtolower($candidateSlug)) {
      $activeCategory = $c;
      break;
    }
  }
}

// Fallback to post's primary category
if (!$activeCategory) {
  if (!empty($postCategories)) {
    $activeCategory = $postCategories[0];
  } else {
    $activeCategory = [
      'name' => $postCatName,
      'slug' => $postCatSlug
    ];
  }
}

$breadcrumbCatName = $activeCategory['name'] ?? $postCatName;
$breadcrumbCatSlug = $activeCategory['slug'] ?? $postCatSlug;

$page_title = (!empty($post['meta_title']) ? $post['meta_title'] : $postTitle) . ' | Infinity Vape Shop Blog';
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
      <?php if (!empty($breadcrumbCatName)): ?>
      <li class="breadcrumb-separator" aria-hidden="true">
        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </li>
      <li class="breadcrumb-item">
        <a href="<?= url($breadcrumbCatSlug) ?>">
          <?= htmlspecialchars($breadcrumbCatName) ?>
        </a>
      </li>
      <?php endif; ?>
      <li class="breadcrumb-separator" aria-hidden="true">
        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </li>
      <li class="breadcrumb-item active" aria-current="page">
        <?= htmlspecialchars($postTitle) ?>
      </li>
    </ol>
  </div>
</nav>

<!-- ==========================================
     2. BLOG DETAIL MAIN LAYOUT (ARTICLE + SIDEBAR)
     ========================================== -->
<section class="blog-detail-section">
  <div class="container">
    <div class="blog-layout-grid">
      
      <!-- ========================================
           LEFT COLUMN: FULL BLOG ARTICLE
           ======================================== -->
      <article class="blog-detail-article">
        
        <!-- Post Header -->
        <header class="blog-article-header">
          <h1 class="blog-article-title"><?= htmlspecialchars($postTitle) ?></h1>
          
          <!-- Category Badges -->
          <div class="blog-article-categories" style="display: flex; gap: 8px; flex-wrap: wrap;">
            <?php if (!empty($post['categories']) && is_array($post['categories'])): ?>
              <?php foreach ($post['categories'] as $catItem): ?>
                <a href="<?= url($catItem['slug']) ?>" class="detail-cat-badge"><?= htmlspecialchars($catItem['name']) ?></a>
              <?php endforeach; ?>
            <?php else: ?>
              <a href="<?= url($postCatSlug) ?>" class="detail-cat-badge"><?= htmlspecialchars($postCatName) ?></a>
            <?php endif; ?>
          </div>

          <!-- Meta Author & Date Row -->
          <div class="blog-article-meta">
            <span class="meta-author"><?= htmlspecialchars(!empty($post['author_name']) ? $post['author_name'] : 'Infinity Vape Shop') ?></span>
            <span class="meta-separator">|</span>
            <span class="meta-date"><?= htmlspecialchars(blog_time_display($post['published_at'] ?? '')) ?></span>
          </div>
        </header>

        <!-- Featured Hero Image -->
        <div class="blog-article-featured-media">
          <img 
            src="<?= htmlspecialchars($postImg) ?>" 
            alt="<?= htmlspecialchars($postTitle) ?>" 
            class="blog-article-hero-img"
            loading="eager"
            onerror="this.onerror=null;this.src='<?= url('assets/images/no-image.svg') ?>';"
          >
        </div>

        <!-- Article Prose Content -->
        <div class="blog-article-prose">
          <?php if (!empty($post['content'])): 
            $contentHtml = $post['content'];
            $contentHtml = preg_replace_callback(
                '/\[product_showcase\s+ids=["\']([^"\']+)["\']\s*\]/i',
                function ($matches) {
                    $rawIds = explode(',', $matches[1]);
                    $cleanIds = array_slice(array_filter(array_map('intval', $rawIds)), 0, 2);
                    if (empty($cleanIds)) return '';
                    $products = \App\Models\Product::getByIds($cleanIds);
                    if (empty($products)) return '';
                    ob_start();
                    include __DIR__ . '/partials/product_showcase.php';
                    return ob_get_clean();
                },
                $contentHtml
            );
          ?>
            <?= $contentHtml ?>
          <?php elseif (!empty($post['excerpt'])): ?>
            <p class="lead-excerpt" style="font-size: 16px; line-height: 1.8; color: var(--color-text-main);"><?= nl2br(htmlspecialchars($post['excerpt'])) ?></p>
          <?php else: ?>
            <p style="color: #64748b; font-style: italic; padding: 20px 0;">No written content available for this article yet.</p>
          <?php endif; ?>
        </div>

        <!-- ==============================================
             SOCIAL SHARE BUTTONS
             ============================================== -->
        <div class="blog-article-share-bar">
          <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(url(urlencode($postSlug))) ?>" target="_blank" rel="noopener noreferrer" class="btn-social-share share-fb" aria-label="Share on Facebook">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            <span>Share on Facebook</span>
          </a>

          <a href="https://twitter.com/intent/tweet?url=<?= urlencode(url(urlencode($postSlug))) ?>&text=<?= urlencode($postTitle) ?>" target="_blank" rel="noopener noreferrer" class="btn-social-share share-x" aria-label="Share on X">
            <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor">
              <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>
            <span>Share on X</span>
          </a>

          <a href="https://pinterest.com/pin/create/button/?url=<?= urlencode(url(urlencode($postSlug))) ?>&description=<?= urlencode($postTitle) ?>" target="_blank" rel="noopener noreferrer" class="btn-social-share share-pinterest" aria-label="Share on Pinterest">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
              <path d="M12 0a12 12 0 0 0-4.37 23.18c-.05-.98-.1-2.48.02-3.55l1.04-4.42s-.26-.52-.26-1.3c0-1.22.7-2.13 1.59-2.13.75 0 1.11.56 1.11 1.24 0 .76-.48 1.89-.73 2.94-.21.88.44 1.6 1.3 1.6 1.57 0 2.78-1.65 2.78-4.04 0-2.11-1.52-3.59-3.69-3.59-2.51 0-3.99 1.89-3.99 3.84 0 .76.29 1.58.66 2.02.07.09.08.17.06.26l-.25 1.02c-.04.16-.14.2-.32.12-1.18-.55-1.92-2.27-1.92-3.65 0-2.98 2.16-5.71 6.24-5.71 3.28 0 5.82 2.34 5.82 5.46 0 3.26-2.05 5.88-4.9 5.88-.96 0-1.86-.5-2.17-1.09l-.59 2.26c-.22.84-.8 1.89-1.2 2.52A12 12 0 1 0 12 0z"/>
            </svg>
            <span>Share on Pinterest</span>
          </a>
        </div>

      </article>

      <!-- ========================================
           RIGHT COLUMN: SIDEBAR (SAME AS BLOG & TAG)
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
            <li class="category-list-item <?= empty($postCatSlug) ? 'active' : '' ?>">
              <a href="<?= url('blog') ?>" class="category-link">
                <span class="category-name">All Articles</span>
                <span class="category-count">(<?= (int) ($totalPostsCount ?? \App\Models\BlogPost::countActive()) ?>)</span>
              </a>
            </li>
            <?php foreach ($categories as $cat): ?>
              <li class="category-list-item <?= ($postCatSlug === $cat['slug']) ? 'active' : '' ?>">
                <a href="<?= url($cat['slug']) ?>" class="category-link">
                  <span class="category-name"><?= htmlspecialchars($cat['name']) ?></span>
                  <span class="category-count">(<?= (int) ($cat['post_count'] ?? 0) ?>)</span>
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

        <!-- WIDGET 4: FEATURED POSTS CARD -->
        <?php
        $featDetail = null;
        if (!empty($relatedPosts)) {
          $featDetail = isset($relatedPosts[0]) ? $relatedPosts[0] : reset($relatedPosts);
        } elseif (!empty($recentPosts)) {
          $featDetail = isset($recentPosts[0]) ? $recentPosts[0] : reset($recentPosts);
        }
        ?>
        <?php
        if ($featDetail):
          $fdSlug = !empty($featDetail['slug']) ? $featDetail['slug'] : $featDetail['id'];
          $fdUrl = url(urlencode($fdSlug));
          $fdImg = !empty($featDetail['featured_image_url']) ? $featDetail['featured_image_url'] : 'assets/images/blog-1.jpg';
          if (!preg_match('~^(https?://|//|#)~i', $fdImg)) {
            $fdImg = url($fdImg);
          }
          $fdDate = !empty($featDetail['published_at']) ? date('M d, Y', strtotime($featDetail['published_at'])) : date('M d, Y');
          ?>
        <div class="blog-widget blog-card-widget blog-featured-post-widget">
          <h3 class="blog-widget-title">Related Guides</h3>
          <ul class="recent-posts-list">
            <li class="recent-post-item">
              <a href="<?= htmlspecialchars($fdUrl) ?>" class="recent-post-thumb-link" aria-label="<?= htmlspecialchars($featDetail['title']) ?>">
                <img src="<?= htmlspecialchars($fdImg) ?>" alt="<?= htmlspecialchars($featDetail['title']) ?>" class="recent-post-thumb-img" loading="lazy" onerror="this.onerror=null;this.src='<?= url('assets/images/blog-1.jpg') ?>';">
              </a>
              <div class="recent-post-info">
                <h4 class="recent-post-title">
                  <a href="<?= htmlspecialchars($fdUrl) ?>"><?= htmlspecialchars($featDetail['title']) ?></a>
                </h4>
                <span class="recent-post-date"><?= htmlspecialchars($fdDate) ?></span>
              </div>
            </li>
          </ul>
        </div>
        <?php endif; ?>

        <!-- WIDGET 5: POPULAR POSTS CARD -->
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



      </aside>

    </div>
  </div>
</section>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
