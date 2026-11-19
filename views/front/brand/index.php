<?php
/**
 * ALL BRANDS DIRECTORY PAGE (views/front/brand/index.php)
 * Responsive, clean showcase of all active brands with real-time search,
 * alphabetical A-Z jump bar, and A-Z grouped brand card sections.
 */

$brands = $brands ?? [];
$totalCount = count($brands);

// 1. Alphabetically sort brands by name (A-Z)
usort($brands, function($a, $b) {
    return strcasecmp($a['name'] ?? '', $b['name'] ?? '');
});

// 2. Group brands by first character
$groupedBrands = [];
$activeLetters = [];

foreach ($brands as $b) {
    $bName = trim($b['name'] ?? '');
    if ($bName === '') continue;
    $firstChar = strtoupper(mb_substr($bName, 0, 1));
    if (!ctype_alpha($firstChar)) {
        $firstChar = '#';
    }
    if (!isset($groupedBrands[$firstChar])) {
        $groupedBrands[$firstChar] = [];
    }
    $groupedBrands[$firstChar][] = $b;
    $activeLetters[$firstChar] = true;
}

// Full A-Z list: '#' followed by 'A' through 'Z'
$alphabet = array_merge(['#'], range('A', 'Z'));

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
     2. ALL BRANDS MAIN SECTION (HEADER & GROUPS)
     ========================================== -->
<section class="brands-main-section" id="brandsMainSection">
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
            placeholder="Search brands by name (e.g. Geek Bar, Lost Mary, Smok)..." 
            autocomplete="off"
            aria-label="Search brands"
          >
          <button type="button" id="btnResetBrandSearch" class="brands-search-clear-btn" style="display: none;" title="Clear search">&times;</button>
        </div>
        <div class="brands-total-pill">
          <span><?= $totalCount ?> Brands Available</span>
        </div>
      </div>
    </div>

    <!-- ==========================================
         3. STICKY A-Z ALPHABET JUMP BAR
         ========================================== -->
    <div class="brands-alphabet-bar-wrap" id="brandsAlphabetBar">
      <div class="brands-alphabet-bar-inner">
        <button type="button" class="az-btn az-btn-all is-active" id="btnJumpAll" data-letter="all" title="View all brands">
          All
        </button>
        
        <div class="az-letters-scroll-track" id="azLettersTrack">
          <?php foreach ($alphabet as $letter): 
            $hasBrands = !empty($activeLetters[$letter]);
          ?>
            <?php if ($hasBrands): ?>
              <a 
                href="#brand-letter-<?= $letter ?>" 
                class="az-btn az-btn-letter has-brands" 
                data-letter="<?= $letter ?>"
                title="Jump to Letter <?= $letter ?>"
              >
                <?= $letter ?>
              </a>
            <?php else: ?>
              <span 
                class="az-btn az-btn-letter no-brands" 
                data-letter="<?= $letter ?>" 
                title="No brands starting with <?= $letter ?>"
                aria-disabled="true"
              >
                <?= $letter ?>
              </span>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- ==========================================
         4. ALPHABETICAL BRAND SECTIONS
         ========================================== -->
    <div class="brands-alphabet-directory" id="brandsAlphabetDirectory">
      <?php if (!empty($groupedBrands)): ?>
        <?php foreach ($groupedBrands as $letter => $groupBrands): ?>
          <div 
            class="brand-letter-group" 
            id="brand-letter-<?= $letter ?>" 
            data-group-letter="<?= $letter ?>"
          >
            <!-- Letter Header -->
            <div class="brand-letter-header">
              <h2 class="brand-letter-char"><?= $letter ?></h2>
              <span class="brand-letter-count"><?= count($groupBrands) ?> <?= count($groupBrands) === 1 ? 'Brand' : 'Brands' ?></span>
            </div>

            <!-- Cards Grid For This Letter -->
            <div class="brands-cards-grid">
              <?php foreach ($groupBrands as $b): 
                $bId       = (int)($b['id'] ?? 0);
                $bName     = $b['name'] ?? 'Brand';
                $bSlug     = $b['slug'] ?? '';
                $bUrl      = \App\Models\Brand::getUrl($b);
                $bLogo     = !empty($b['logo_url']) ? asset($b['logo_url']) : asset('assets/images/logo.svg');
                $pCount    = (int)($b['products_count'] ?? 0);
              ?>
              <a 
                href="<?= htmlspecialchars($bUrl) ?>" 
                class="brand-directory-card" 
                data-brand-name="<?= htmlspecialchars(strtolower($bName)) ?>" 
                data-letter="<?= htmlspecialchars($letter) ?>"
              >
                <!-- Logo Container -->
                <div class="brand-card-logo-wrap">
                  <img 
                    src="<?= htmlspecialchars($bLogo) ?>" 
                    alt="<?= htmlspecialchars($bName) ?>" 
                    class="brand-card-logo-img" 
                    loading="lazy"
                    onerror="this.src='<?= asset('assets/images/logo.svg') ?>';"
                  >
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
            </div>
          </div>
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
     5. STYLES & INTERACTIVE FILTER SCRIPT
     ========================================== -->
<style>
/* All Brands Unified Main Section */
.brands-main-section {
  padding: 24px 0 80px;
  background-color: #ffffff;
}

.brands-hero-content {
  max-width: 960px;
  margin: 0 auto 28px;
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
  margin: 0 auto 20px;
}

/* Search Bar & Pill */
.brands-controls-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
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

.brands-total-pill {
  display: inline-flex;
  align-items: center;
  font-size: 0.82rem;
  font-weight: 700;
  color: #0b2545;
  background: #f1f5f9;
  padding: 4px 14px;
  border-radius: 9999px;
  letter-spacing: 0.02em;
}

/* ==========================================
   STICKY A-Z ALPHABET JUMP BAR
   ========================================== */
.brands-alphabet-bar-wrap {
  position: sticky;
  top: 0;
  z-index: 45;
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  padding: 10px 0;
  margin-bottom: 36px;
  border-top: 1px solid #edf2f7;
  border-bottom: 1.5px solid #e2e8f0;
}

.brands-alphabet-bar-inner {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 8px;
}

.az-letters-scroll-track {
  display: flex;
  align-items: center;
  gap: 4px;
  overflow-x: auto;
  scrollbar-width: none;
  -webkit-overflow-scrolling: touch;
  padding: 2px 4px;
}

.az-letters-scroll-track::-webkit-scrollbar {
  display: none;
}

.az-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 32px;
  height: 34px;
  padding: 0 7px;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 700;
  text-decoration: none;
  text-transform: uppercase;
  transition: all 0.18s ease;
  user-select: none;
  border: 1px solid transparent;
}

.az-btn-all {
  background: #0b2545;
  color: #ffffff;
  padding: 0 14px;
  cursor: pointer;
  border: none;
  flex-shrink: 0;
}

.az-btn-all:hover,
.az-btn-all.is-active {
  background: #fa5a1e;
  color: #ffffff;
}

/* Letters with active brands */
.az-btn-letter.has-brands {
  background: #f8fafc;
  color: #0b2545;
  border-color: #e2e8f0;
  cursor: pointer;
}

.az-btn-letter.has-brands:hover {
  background: #0b2545;
  color: #ffffff;
  border-color: #0b2545;
  transform: translateY(-1px);
}

.az-btn-letter.has-brands.is-active {
  background: #fa5a1e !important;
  color: #ffffff !important;
  border-color: #fa5a1e !important;
  box-shadow: 0 3px 10px rgba(250, 90, 30, 0.28);
}

/* Inactive letters without brands */
.az-btn-letter.no-brands {
  color: #cbd5e1;
  background: transparent;
  cursor: default;
  pointer-events: none;
  opacity: 0.55;
}

/* ==========================================
   ALPHABET GROUP SECTIONS
   ========================================== */
.brand-letter-group {
  scroll-margin-top: 76px;
  margin-bottom: 44px;
  transition: opacity 0.2s ease;
}

.brand-letter-group:last-child {
  margin-bottom: 0;
}

.brand-letter-header {
  display: flex;
  align-items: baseline;
  justify-content: space-between;
  padding-bottom: 12px;
  margin-bottom: 22px;
  border-bottom: 1.5px solid #e2e8f0;
  position: relative;
}

.brand-letter-char {
  font-size: 2.25rem;
  font-weight: 800;
  color: #0b2545;
  margin: 0;
  line-height: 1;
  letter-spacing: -0.03em;
}

.brand-letter-count {
  font-size: 0.88rem;
  font-weight: 600;
  color: #64748b;
  letter-spacing: 0.01em;
}



/* ==========================================
   BRAND CARDS GRID
   ========================================== */
.brands-cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
  gap: 20px;
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

/* Logo Box */
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
  margin: 40px auto 0;
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
  .brands-alphabet-bar-inner {
    justify-content: flex-start;
  }
  .az-btn {
    min-width: 29px;
    height: 31px;
    font-size: 0.8rem;
    padding: 0 5px;
  }
  .brand-letter-char {
    font-size: 1.85rem;
  }
  .brand-letter-count {
    font-size: 0.82rem;
  }
  .brands-cards-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }
  .brand-directory-card {
    padding: 16px 10px 14px;
  }
  .brand-card-logo-wrap {
    max-width: 120px;
    height: 70px;
    margin-bottom: 8px;
  }
  .brand-card-logo-img {
    max-height: 60px;
  }
  .brand-card-name {
    font-size: 0.95rem;
  }
}

@media (max-width: 480px) {
  .brand-letter-header {
    padding-bottom: 8px;
    margin-bottom: 16px;
  }
  .brand-letter-char {
    font-size: 1.65rem;
  }
}
</style>

<!-- ==========================================
     6. JAVASCRIPT: JUMP, FILTER & SCROLLSPY
     ========================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  'use strict';

  const searchInput   = document.getElementById('brandsSearchInput');
  const clearBtn      = document.getElementById('btnResetBrandSearch');
  const emptyBox      = document.getElementById('noBrandsFoundBox');
  const resetEmptyBtn = document.getElementById('btnResetEmptySearch');
  const letterGroups  = document.querySelectorAll('.brand-letter-group');
  const allCards      = document.querySelectorAll('.brand-directory-card');
  const azButtons     = document.querySelectorAll('.az-btn-letter.has-brands');
  const allBtn        = document.getElementById('btnJumpAll');
  const alphabetBar   = document.getElementById('brandsAlphabetBar');
  const azTrack       = document.getElementById('azLettersTrack');

  // 1. Smooth scroll to letter anchor on button click
  azButtons.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const targetLetter = this.getAttribute('data-letter');
      const targetEl = document.getElementById('brand-letter-' + targetLetter);

      if (targetEl && targetEl.style.display !== 'none') {
        // Reset any search that might be hiding it
        if (searchInput && searchInput.value.trim().length > 0) {
          // If search was active, check if this letter has cards visible
          const visibleInGroup = targetEl.querySelectorAll('.brand-directory-card:not([style*="display: none"])');
          if (visibleInGroup.length === 0) {
            searchInput.value = '';
            filterBrands();
          }
        }

        targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });

        // Update active class
        setActiveLetterButton(this);
      }
    });
  });

  // "ALL" button click -> scroll smoothly to top of directory
  if (allBtn) {
    allBtn.addEventListener('click', function(e) {
      e.preventDefault();
      if (searchInput && searchInput.value.trim().length > 0) {
        searchInput.value = '';
        filterBrands();
      }
      const dir = document.getElementById('brandsAlphabetDirectory');
      if (dir) {
        dir.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
      setActiveLetterButton(allBtn);
    });
  }

  function setActiveLetterButton(activeEl) {
    document.querySelectorAll('.az-btn').forEach(b => b.classList.remove('is-active'));
    if (activeEl) {
      activeEl.classList.add('is-active');

      // Ensure button is visible within horizontal track on mobile
      if (azTrack && activeEl !== allBtn) {
        const trackRect = azTrack.getBoundingClientRect();
        const btnRect = activeEl.getBoundingClientRect();
        if (btnRect.left < trackRect.left || btnRect.right > trackRect.right) {
          activeEl.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        }
      }
    }
  }

  // 2. Real-time Search Filter
  function filterBrands() {
    const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
    let totalVisible = 0;

    letterGroups.forEach(group => {
      const cardsInGroup = group.querySelectorAll('.brand-directory-card');
      let visibleInGroup = 0;

      cardsInGroup.forEach(card => {
        const brandName = card.dataset.brandName || '';
        const matches = (query.length === 0) || brandName.includes(query);

        if (matches) {
          card.style.display = 'flex';
          visibleInGroup++;
          totalVisible++;
        } else {
          card.style.display = 'none';
        }
      });

      // Show or hide this entire letter section
      group.style.display = (visibleInGroup > 0) ? 'block' : 'none';

      // Update corresponding letter button status in alphabet bar
      const groupLetter = group.getAttribute('data-group-letter');
      const azBtn = document.querySelector(`.az-btn-letter[data-letter="${groupLetter}"]`);
      if (azBtn && azBtn.classList.contains('has-brands')) {
        if (visibleInGroup === 0 && query.length > 0) {
          azBtn.style.opacity = '0.35';
          azBtn.style.pointerEvents = 'none';
        } else {
          azBtn.style.opacity = '';
          azBtn.style.pointerEvents = '';
        }
      }
    });

    // Toggle empty state box
    if (emptyBox) {
      emptyBox.style.display = (totalVisible === 0) ? 'block' : 'none';
    }

    // Toggle clear search button
    if (clearBtn) {
      clearBtn.style.display = (query.length > 0) ? 'block' : 'none';
    }

    if (query.length === 0) {
      setActiveLetterButton(allBtn);
    }
  }

  if (searchInput) {
    searchInput.addEventListener('input', filterBrands);
  }

  if (clearBtn) {
    clearBtn.addEventListener('click', function() {
      searchInput.value = '';
      filterBrands();
      searchInput.focus();
    });
  }

  if (resetEmptyBtn) {
    resetEmptyBtn.addEventListener('click', function() {
      if (searchInput) searchInput.value = '';
      filterBrands();
      if (searchInput) searchInput.focus();
    });
  }

  // 3. Scrollspy: Highlight active letter button as user scrolls down
  let scrollTimeout = null;
  window.addEventListener('scroll', function() {
    if (scrollTimeout) return;
    scrollTimeout = setTimeout(function() {
      scrollTimeout = null;
      if (searchInput && searchInput.value.trim().length > 0) return;

      const scrollPos = window.scrollY + 120;
      let currentActiveGroup = null;

      letterGroups.forEach(group => {
        if (group.style.display === 'none') return;
        const top = group.offsetTop;
        const height = group.offsetHeight;
        if (scrollPos >= top && scrollPos < top + height) {
          currentActiveGroup = group;
        }
      });

      if (currentActiveGroup) {
        const letter = currentActiveGroup.getAttribute('data-group-letter');
        const activeBtn = document.querySelector(`.az-btn-letter[data-letter="${letter}"]`);
        if (activeBtn) setActiveLetterButton(activeBtn);
      } else if (window.scrollY < 200 && allBtn) {
        setActiveLetterButton(allBtn);
      }
    }, 60);
  }, { passive: true });
});
</script>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
