<?php
/**
 * ALL BRANDS DIRECTORY PAGE (views/front/brand/index.php)
 * Responsive, clean showcase of all active brands with real-time search,
 * alphabetical A-Z jump bar, and simple modern brand cards.
 */

$brands = $brands ?? [];
$totalCount = count($brands);

$page_title = $page_title ?? 'Shop All Brands | Official Vape Manufacturers | Infinity Vape Shop';
$meta_description = $meta_description ?? 'Browse all authorized and authentic vape brands, disposable vape manufacturers, and premium e-liquid makers at Infinity Vape Shop.';
$canonical_url = $canonical_url ?? url('brands');

include dirname(__DIR__) . '/includes/header.php';
?>

<!-- ==========================================
     1. BREADCRUMBS
     ========================================== -->
<nav class="category-breadcrumb-nav" aria-label="Breadcrumb">
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
      <li class="breadcrumb-item active" aria-current="page">
        Brands
      </li>
    </ol>
  </div>
</nav>

<!-- ==========================================
     2. ALL BRANDS MAIN SECTION (HEADER & CARDS GRID)
     ========================================== -->
<section class="brands-main-section">
  <div class="container">
    
    <!-- Hero / Header Area -->
    <div class="brands-hero-content">
      <h1 class="brands-page-title">Shop by Brand</h1>
      <p class="brands-page-subtitle">
        Explore our curated lineup of verified hardware, disposable vapes, and premium e-liquid manufacturers. 100% authentic guaranteed.
      </p>

      <!-- Search Controls Wrapper -->
      <div class="brands-controls-wrap">
        <div class="brands-search-box">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" class="brands-search-icon">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <input 
            type="text" 
            id="brandsSearchInput" 
            class="brands-search-input" 
            placeholder="Search brands by name (e.g. Lost Mary, Geek Bar)..." 
            autocomplete="off"
          >
          <button type="button" id="btnResetBrandSearch" class="brands-search-clear-btn" style="display: none;" title="Clear search">&times;</button>
        </div>
      </div>
    </div>

    <!-- Grid Container -->
    <div class="brands-cards-grid" id="brandsCardsGrid">
      <?php if (!empty($brands)): ?>
        <?php foreach ($brands as $b): 
          $bId       = (int)($b['id'] ?? 0);
          $bName     = $b['name'] ?? 'Brand';
          $bSlug     = $b['slug'] ?? '';
          $bUrl      = \App\Models\Brand::getUrl($b);
          $bLogo     = !empty($b['logo_url']) ? asset($b['logo_url']) : '';
          $pCount    = (int)($b['products_count'] ?? 0);
          $firstChar = strtoupper(mb_substr(trim($bName), 0, 1));
          if (!ctype_alpha($firstChar)) {
              $firstChar = '0-9';
          }
        ?>
        <a 
          href="<?= htmlspecialchars($bUrl) ?>" 
          class="brand-directory-card" 
          data-brand-name="<?= htmlspecialchars(strtolower($bName)) ?>" 
          data-letter="<?= htmlspecialchars($firstChar) ?>"
        >
          <!-- Logo Container -->
          <div class="brand-card-logo-wrap">
            <?php if (!empty($bLogo)): ?>
              <img 
                src="<?= htmlspecialchars($bLogo) ?>" 
                alt="<?= htmlspecialchars($bName) ?>" 
                class="brand-card-logo-img" 
                loading="lazy"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
              >
              <div class="brand-card-logo-fallback" style="display: none;">
                <?= htmlspecialchars(mb_substr($bName, 0, 1)) ?>
              </div>
            <?php else: ?>
              <div class="brand-card-logo-fallback">
                <?= htmlspecialchars(mb_substr($bName, 0, 1)) ?>
              </div>
            <?php endif; ?>
          </div>

          <!-- Brand Info -->
          <div class="brand-card-info">
            <h3 class="brand-card-name" title="<?= htmlspecialchars($bName) ?>">
              <?= htmlspecialchars($bName) ?>
            </h3>
            
            <div class="brand-card-meta">
              <span class="brand-card-count">
                <?= $pCount ?> product<?= $pCount === 1 ? '' : 's' ?>
              </span>
            </div>

            <div class="brand-card-cta">
              <span>View Products</span>
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
              </svg>
            </div>
          </div>
        </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Empty State (Shown when search / filter matches 0 brands) -->
    <div id="noBrandsFoundBox" style="display: <?= empty($brands) ? 'block' : 'none' ?>;" class="brands-empty-state">
      <div class="empty-icon-circle">
        <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="#94a3b8" stroke-width="1.8">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          <line x1="8" y1="11" x2="14" y2="11"></line>
        </svg>
      </div>
      <h3>No Brands Found</h3>
      <p>We couldn't find any brands matching your search criteria.</p>
      <button type="button" id="btnResetEmptySearch" class="btn-clear-brand-filters">Clear Filter</button>
    </div>

  </div>
</section>

<!-- ==========================================
     4. STYLES & INTERACTIVE FILTER SCRIPT
     ========================================== -->
<style>
/* All Brands Unified Main Section */
.brands-main-section {
  padding: 20px 0 80px;
  background-color: #ffffff;
}

.brands-hero-content {
  max-width: 960px;
  margin: 0 auto 36px;
  text-align: center;
}

.brands-page-title {
  font-size: 2.35rem;
  font-weight: 800;
  color: #0b2545;
  margin: 0 0 10px;
  letter-spacing: -0.02em;
}

.brands-page-subtitle {
  font-size: 0.95rem;
  line-height: 1.6;
  color: #64748b;
  max-width: 680px;
  margin: 0 auto 24px;
}

/* Search Bar */
.brands-controls-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
  margin-bottom: 16px;
}

.brands-search-box {
  position: relative;
  width: 100%;
  max-width: 520px;
}

.brands-search-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  pointer-events: none;
}

.brands-search-input {
  width: 100%;
  height: 48px;
  padding: 0 42px 0 46px;
  border: 1.5px solid #e2e8f0;
  border-radius: 9999px;
  font-size: 0.925rem;
  color: #0f172a;
  background-color: #f8fafc;
  outline: none;
  transition: all 0.2s ease;
  font-family: inherit;
}

.brands-search-input:focus {
  border-color: #0b2545;
  background-color: #ffffff;
  box-shadow: 0 4px 14px rgba(11, 37, 69, 0.08);
}

.brands-search-clear-btn {
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  font-size: 20px;
  color: #94a3b8;
  cursor: pointer;
  line-height: 1;
  padding: 4px;
}

.brands-search-clear-btn:hover {
  color: #0f172a;
}

.brands-cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
  gap: 22px;
}

/* Simple Brand Card */
.brand-directory-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  background: #ffffff;
  border: 1.5px solid #edf2f7;
  border-radius: 14px;
  padding: 24px 18px 20px;
  text-decoration: none;
  transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
  box-shadow: none;
  position: relative;
  overflow: hidden;
}

.brand-directory-card:hover {
  border-color: #0b2545;
  transform: translateY(-4px);
  box-shadow: 0 10px 24px rgba(11, 37, 69, 0.08);
}

/* Logo Box - No background, no shadow, bigger size */
.brand-card-logo-wrap {
  width: 100%;
  max-width: 170px;
  height: 95px;
  background: transparent;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  margin-bottom: 14px;
  box-shadow: none !important;
  transition: transform 0.2s ease;
}

.brand-directory-card:hover .brand-card-logo-wrap {
  transform: scale(1.06);
  box-shadow: none !important;
}

.brand-card-logo-img {
  max-width: 100%;
  max-height: 85px;
  object-fit: contain;
  display: block;
}

.brand-card-logo-fallback {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: linear-gradient(135deg, #082142 0%, #1e3a8a 100%);
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.85rem;
  font-weight: 800;
  text-transform: uppercase;
}

/* Card Info */
.brand-card-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
}

.brand-card-name {
  font-size: 1.12rem;
  font-weight: 700;
  color: #0b2545;
  margin: 0 0 6px;
  letter-spacing: -0.01em;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
  transition: color 0.15s ease;
}

.brand-directory-card:hover .brand-card-name {
  color: #fa5a1e;
}

.brand-card-meta {
  margin-bottom: 14px;
}

.brand-card-count {
  font-size: 0.78rem;
  font-weight: 600;
  color: #64748b;
  background: #f1f5f9;
  padding: 3px 10px;
  border-radius: 9999px;
  display: inline-block;
}

/* Card CTA */
.brand-card-cta {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 0.82rem;
  font-weight: 700;
  color: #0b2545;
  transition: all 0.2s ease;
}

.brand-card-cta svg {
  transition: transform 0.2s ease;
}

.brand-directory-card:hover .brand-card-cta {
  color: #fa5a1e;
}

.brand-directory-card:hover .brand-card-cta svg {
  transform: translateX(3px);
}

/* Empty State */
.brands-empty-state {
  text-align: center;
  padding: 60px 20px;
  background: #ffffff;
  border-radius: 16px;
  border: 1px dashed #cbd5e1;
  max-width: 520px;
  margin: 20px auto 0;
}

.empty-icon-circle {
  width: 64px;
  height: 64px;
  background: #f8fafc;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 14px;
}

.brands-empty-state h3 {
  font-size: 1.25rem;
  font-weight: 700;
  color: #0b2545;
  margin: 0 0 8px;
}

.brands-empty-state p {
  font-size: 0.9rem;
  color: #64748b;
  margin: 0 0 20px;
}

.btn-clear-brand-filters {
  background-color: #0b2545;
  color: #ffffff;
  padding: 9px 22px;
  border-radius: 9999px;
  font-size: 0.85rem;
  font-weight: 700;
  border: none;
  cursor: pointer;
  transition: background-color 0.15s ease;
}

.btn-clear-brand-filters:hover {
  background-color: #fa5a1e;
}

/* Mobile responsive */
@media (max-width: 768px) {
  .brands-page-title {
    font-size: 1.85rem;
  }
  .brands-cards-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
  }
  .brand-directory-card {
    padding: 18px 12px 14px;
  }
  .brand-card-logo-wrap {
    max-width: 130px;
    height: 75px;
    margin-bottom: 10px;
  }
  .brand-card-logo-img {
    max-height: 65px;
  }
  .brand-card-name {
    font-size: 0.98rem;
  }
}

@media (max-width: 480px) {
  .brands-cards-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  'use strict';

  const searchInput = document.getElementById('brandsSearchInput');
  const clearBtn = document.getElementById('btnResetBrandSearch');
  const cards = document.querySelectorAll('.brand-directory-card');
  const emptyBox = document.getElementById('noBrandsFoundBox');
  const resetEmptyBtn = document.getElementById('btnResetEmptySearch');

  function filterBrands() {
    const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
    let visibleCount = 0;

    cards.forEach(card => {
      const brandName = card.dataset.brandName || '';
      const matchesSearch = (query.length === 0) || brandName.includes(query);

      if (matchesSearch) {
        card.style.display = 'flex';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });

    // Toggle empty state
    if (emptyBox) {
      emptyBox.style.display = (visibleCount === 0) ? 'block' : 'none';
    }

    // Toggle clear button
    if (clearBtn) {
      clearBtn.style.display = (query.length > 0) ? 'block' : 'none';
    }
  }

  // Search input events
  if (searchInput) {
    searchInput.addEventListener('input', filterBrands);
  }

  // Clear button click
  if (clearBtn) {
    clearBtn.addEventListener('click', function() {
      searchInput.value = '';
      filterBrands();
      searchInput.focus();
    });
  }

  // Reset from empty state
  if (resetEmptyBtn) {
    resetEmptyBtn.addEventListener('click', function() {
      if (searchInput) searchInput.value = '';
      filterBrands();
      if (searchInput) searchInput.focus();
    });
  }
});
</script>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>

