<?php
$page_title = 'Blogs | Vape Store Admin';
$current_admin_page = 'blogs';
$page_script = 'blogs.js';
include dirname(__DIR__) . '/includes/header.php';
?>

<!-- Title & Action Bar (Matching The Perfect Vape Admin) -->
<div class="page-title-bar">
  <div class="page-heading-group">
    <h1>Blogs</h1>
    <p class="page-subtitle">Publish vaping guides, product reviews, and industry compliance updates.</p>
  </div>
  <div class="page-actions-group">
    <!-- Search Input -->
    <div class="table-search-box" style="margin: 0; min-width: 240px;">
      <input type="text" id="blogSearchInput" class="admin-form-input table-search-input" placeholder="Search blogs..." value="<?= e($search ?? '') ?>">
      <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="table-search-icon">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
      </svg>
    </div>

    <!-- Status Dropdown -->
    <div class="table-status-filter-wrap">
      <select id="blogStatusFilter" class="admin-form-select admin-filter-select" style="height: 40px;">
        <option value="all" <?= ($status ?? 'all') === 'all' ? 'selected' : '' ?>>All status</option>
        <option value="visible" <?= ($status ?? '') === 'visible' || ($status ?? '') === 'active' ? 'selected' : '' ?>>Visible</option>
        <option value="hidden" <?= ($status ?? '') === 'hidden' || ($status ?? '') === 'inactive' ? 'selected' : '' ?>>Hidden</option>
      </select>
    </div>

    <!-- Add Blog Button -->
    <a href="<?= url('admin/blogs/add') ?>" class="btn-admin btn-admin-primary" style="text-decoration: none; padding: 0 18px; height: 40px; display: inline-flex; align-items: center; gap: 8px; border-radius: 8px;">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
      </svg>
      <span>Add Blog</span>
    </a>
  </div>
</div>

<!-- Flash Notifications -->
<?php if (!empty($success)): ?>
<div class="admin-alert admin-alert-success mb-lg">
  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
    <polyline points="22 4 12 14.01 9 11.01"></polyline>
  </svg>
  <span><?= e($success) ?></span>
</div>
<?php endif; ?>

<?php if (!empty($error)): ?>
<div class="admin-alert admin-alert-danger mb-lg">
  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
    <circle cx="12" cy="12" r="10"></circle>
    <line x1="12" y1="8" x2="12" y2="12"></line>
    <line x1="12" y1="16" x2="12.01" y2="16"></line>
  </svg>
  <span><?= e($error) ?></span>
</div>
<?php endif; ?>

<!-- Blog Posts Container -->
<div id="blogs-list-container">
  <?php if (empty($posts)): ?>
    <div class="admin-card" style="padding: 60px 20px; text-align: center;">
      <div style="width: 56px; height: 56px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; color: #64748b;">
        <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
          <polyline points="14 2 14 8 20 8"></polyline>
          <line x1="16" y1="13" x2="8" y2="13"></line>
          <line x1="16" y1="17" x2="8" y2="17"></line>
          <polyline points="10 9 9 9 8 9"></polyline>
        </svg>
      </div>
      <h3 style="font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 6px;">No blog posts found. Create your first one!</h3>
      <p style="color: #64748b; font-size: 14px; max-width: 440px; margin: 0 auto 20px auto;">Publish guides, comparisons and news to boost organic search rankings and educate your vape customers.</p>
      <a href="<?= url('admin/blogs/add') ?>" class="btn-admin btn-admin-primary" style="display: inline-flex; align-items: center; gap: 8px;">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>Add First Blog</span>
      </a>
    </div>
  <?php else: ?>
    <!-- Blog Cards Grid (3 Columns) -->
    <div class="admin-blogs-grid">
      <?php foreach ($posts as $b): 
        $statusText = $b['is_active'] ? 'Visible' : 'Hidden';
        $statusClass = $b['is_active'] ? 'badge-status-active' : 'badge-status-inactive';
        $cover = !empty($b['featured_image_url']) ? $b['featured_image_url'] : 'assets/images/blog-1.jpg';
        if (!preg_match('~^(https?://|//|#)~i', $cover)) {
            $cover = url($cover);
        }
        $pubDate = !empty($b['published_at']) ? date('M d, Y', strtotime($b['published_at'])) : date('M d, Y');
        $catName = $b['category_name'] ?? 'General Guides';
        $catList = array_filter(array_map('trim', explode(',', $catName)));
        if (empty($catList)) $catList = ['General Guides'];
        $firstCat = $catList[0];
        $remainingCats = array_slice($catList, 1);
      ?>
      <div class="admin-blog-card">
        <div class="admin-blog-card-media">
          <img src="<?= e($cover) ?>" alt="<?= e($b['title']) ?>" class="admin-blog-cover-img" onerror="this.onerror=null;this.src='<?= url('assets/images/no-image.svg') ?>';">
          <span class="admin-blog-status-badge <?= $statusClass ?>"><?= $statusText ?></span>
        </div>
        <div class="admin-blog-card-body">
          <div class="admin-blog-meta-row">
            <div class="admin-blog-categories-wrap">
              <span class="admin-blog-category-tag" title="<?= e($firstCat) ?>"><?= e($firstCat) ?></span>
              <?php if (!empty($remainingCats)): ?>
                <span class="admin-blog-category-tag admin-blog-cat-more" title="<?= e(implode(', ', $remainingCats)) ?>">+<?= count($remainingCats) ?></span>
              <?php endif; ?>
            </div>
            <span class="admin-blog-date"><?= $pubDate ?></span>
          </div>
          <a href="<?= url('admin/blogs/edit?id=' . $b['id']) ?>" class="admin-blog-title-link">
            <h3 class="admin-blog-title"><?= e($b['title']) ?></h3>
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="admin-blog-arrow">
              <line x1="7" y1="17" x2="17" y2="7"></line>
              <polyline points="7 7 17 7 17 17"></polyline>
            </svg>
          </a>
          <p class="admin-blog-excerpt"><?= e(mb_strimwidth($b['excerpt'] ?? '', 0, 115, '...')) ?></p>
          <div class="admin-blog-card-footer">
            <span class="admin-blog-readtime" title="Published: <?= e(!empty($b['published_at']) ? date('M d, Y h:i A', strtotime($b['published_at'])) : '') ?>">
              <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: -1px; margin-right: 3px;">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
              <?= e(blog_time_display($b['published_at'] ?? '')) ?>
            </span>
            <div class="admin-blog-actions">
              <a href="<?= url('admin/blogs/edit?id=' . $b['id']) ?>" class="btn-action-icon edit-btn" title="Edit Post">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 20h9"></path>
                  <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                </svg>
              </a>
              <form action="<?= url('admin/blogs/delete') ?>" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this blog post?');">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= $b['id'] ?>">
                <button type="submit" class="btn-action-icon delete-btn" title="Delete Post">
                  <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                  </svg>
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if (!empty($pagination) && $pagination['last_page'] > 1): ?>
      <div class="admin-pagination-bar" style="margin-top: 28px; display: flex; align-items: center; justify-content: space-between;">
        <span style="font-size: 13.5px; color: #64748b;">
          Showing <?= (($pagination['current_page'] - 1) * $pagination['per_page']) + 1 ?> to <?= min($pagination['total'], $pagination['current_page'] * $pagination['per_page']) ?> of <?= $pagination['total'] ?> posts
        </span>
        <div style="display: flex; gap: 6px;">
          <?php for ($p = 1; $p <= $pagination['last_page']; $p++): ?>
            <a href="<?= url('admin/blogs?page=' . $p . (!empty($search) ? '&q=' . urlencode($search) : '') . (!empty($status) ? '&status=' . urlencode($status) : '')) ?>" 
               class="btn-admin <?= $p === (int)$pagination['current_page'] ? 'btn-admin-primary' : 'btn-admin-outline' ?>" 
               style="min-width: 38px; height: 38px; padding: 0 12px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; font-weight: 600;">
              <?= $p ?>
            </a>
          <?php endfor; ?>
        </div>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</div>

<style>
/* Modern 3-Column Blogs Grid Matching The Perfect Vape Admin */
.admin-blogs-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
  gap: 22px;
}
.admin-blog-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: all 0.22s ease-in-out;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}
.admin-blog-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 24px -6px rgba(15, 23, 42, 0.08), 0 4px 8px -2px rgba(15, 23, 42, 0.04);
  border-color: #cbd5e1;
}
.admin-blog-card-media {
  position: relative;
  width: 100%;
  height: 190px;
  background: #f8fafc;
  overflow: hidden;
}
.admin-blog-cover-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}
.admin-blog-card:hover .admin-blog-cover-img {
  transform: scale(1.03);
}
.admin-blog-status-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  padding: 4px 10px;
  border-radius: 9999px;
  font-size: 11.5px;
  font-weight: 700;
  letter-spacing: 0.02em;
  backdrop-filter: blur(6px);
}
.badge-status-active {
  background: rgba(16, 185, 129, 0.9);
  color: #ffffff;
}
.badge-status-inactive {
  background: rgba(100, 116, 139, 0.85);
  color: #ffffff;
}
.admin-blog-card-body {
  padding: 18px 20px 20px;
  display: flex;
  flex-direction: column;
  flex: 1;
}
.admin-blog-meta-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  margin-bottom: 12px;
  min-height: 24px;
}
.admin-blog-categories-wrap {
  display: flex;
  align-items: center;
  gap: 6px;
  flex: 1;
  min-width: 0;
}
.admin-blog-category-tag {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #5c56e8;
  background: #f3f0ff;
  padding: 3px 8px;
  border-radius: 6px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 175px;
  display: inline-block;
}
.admin-blog-category-tag.admin-blog-cat-more {
  background: #ede9fe;
  color: #4f46e5;
  font-weight: 800;
  padding: 3px 6px;
  border-radius: 6px;
  cursor: pointer;
  flex-shrink: 0;
  max-width: none;
  transition: all 0.15s ease;
}
.admin-blog-category-tag.admin-blog-cat-more:hover {
  background: #ddd6fe;
  color: #3730a3;
}
.admin-blog-date {
  font-size: 12px;
  color: #94a3b8;
  font-weight: 500;
  white-space: nowrap;
  flex-shrink: 0;
}
.admin-blog-title-link {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 8px;
  text-decoration: none;
  margin-bottom: 8px;
}
.admin-blog-title {
  font-size: 16px;
  font-weight: 700;
  color: #0f172a;
  line-height: 1.35;
  margin: 0;
  transition: color 0.15s ease;
}
.admin-blog-title-link:hover .admin-blog-title {
  color: #5c56e8;
}
.admin-blog-arrow {
  flex-shrink: 0;
  color: #94a3b8;
  margin-top: 3px;
  transition: transform 0.15s ease, color 0.15s ease;
}
.admin-blog-title-link:hover .admin-blog-arrow {
  color: #5c56e8;
  transform: translate(2px, -2px);
}
.admin-blog-excerpt {
  font-size: 13.5px;
  color: #64748b;
  line-height: 1.5;
  margin: 0 0 16px 0;
  flex: 1;
}
.admin-blog-card-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-top: 1px solid #f1f5f9;
  padding-top: 14px;
}
.admin-blog-readtime {
  font-size: 12px;
  color: #94a3b8;
  font-weight: 500;
}
.admin-blog-actions {
  display: flex;
  gap: 6px;
}
.btn-action-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #e2e8f0;
  background: #ffffff;
  color: #64748b;
  cursor: pointer;
  transition: all 0.15s ease;
}
.btn-action-icon:hover {
  background: #f8fafc;
  color: #0f172a;
  border-color: #cbd5e1;
}
.btn-action-icon.delete-btn:hover {
  background: #fef2f2;
  color: #ef4444;
  border-color: #fecaca;
}
</style>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
