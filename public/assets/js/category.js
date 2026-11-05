/**
 * COLLECTION / CATEGORY DETAIL PAGE JAVASCRIPT (category.js)
 * Fully interactive e-commerce catalog engine:
 * - Dynamic sidebar filter accordion
 * - Dual price range slider
 * - Brand checkboxes filtering
 * - Puff count range filtering
 * - Nicotine strength filtering
 * - Stock availability filtering
 * - Quick filter pills
 * - Sorting (price asc/desc, name, newest, bestseller)
 * - Grid / List view mode switcher
 * - Dynamic pagination & results count
 * - Mobile filter drawer
 * - Add to Cart action with Cart counter integration
 * - Clear all filters
 */

document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  /* ==========================================================================
     1. ACCORDION GROUPS (Shop By Filters)
     ========================================================================== */
  const accordionHeaders = document.querySelectorAll('.filter-group-header');

  accordionHeaders.forEach(function (headerBtn) {
    headerBtn.addEventListener('click', function () {
      const parentGroup = this.closest('.filter-accordion-group');
      if (!parentGroup) return;

      const isCurrentlyOpen = parentGroup.classList.contains('open');

      if (isCurrentlyOpen) {
        parentGroup.classList.remove('open');
        this.setAttribute('aria-expanded', 'false');
      } else {
        parentGroup.classList.add('open');
        this.setAttribute('aria-expanded', 'true');
      }
    });
  });

  // Auto-scroll list to ensure active selection (Collection or Brand) is in view
  const activeRadios = document.querySelectorAll('.filter-accordion-group .filter-scrollable-list input[type="radio"]:checked');
  activeRadios.forEach(function (activeRadio) {
    const activeLi = activeRadio.closest('li');
    if (activeLi) {
      setTimeout(() => {
        activeLi.scrollIntoView({ block: 'nearest' });
      }, 50);
    }
  });

  /* ==========================================================================
     2. DUAL PRICE RANGE SLIDER
     ========================================================================== */
  const rangeMin = document.getElementById('priceRangeMin');
  const rangeMax = document.getElementById('priceRangeMax');
  const inputMin = document.getElementById('priceInputMin');
  const inputMax = document.getElementById('priceInputMax');
  const sliderFill = document.getElementById('priceSliderFill');
  const btnApplyPrice = document.getElementById('btnApplyPriceFilter');

  function updateSliderFill() {
    if (!rangeMin || !rangeMax || !sliderFill) return;

    let minVal = parseFloat(rangeMin.value);
    let maxVal = parseFloat(rangeMax.value);
    const minLimit = parseFloat(rangeMin.min) || 0;
    const maxLimit = parseFloat(rangeMax.max) || 100;
    const totalSpan = maxLimit - minLimit;

    if (totalSpan <= 0) return;

    if (minVal > maxVal - 1) {
      if (document.activeElement === rangeMin) {
        minVal = Math.max(minLimit, maxVal - 1);
        rangeMin.value = minVal;
      } else {
        maxVal = Math.min(maxLimit, minVal + 1);
        rangeMax.value = maxVal;
      }
    }

    const leftPercent = ((minVal - minLimit) / totalSpan) * 100;
    const rightPercent = ((maxLimit - maxVal) / totalSpan) * 100;

    sliderFill.style.left = leftPercent + '%';
    sliderFill.style.width = Math.max(0, 100 - leftPercent - rightPercent) + '%';

    if (inputMin) inputMin.value = Math.round(minVal);
    if (inputMax) inputMax.value = Math.round(maxVal);
  }

  if (rangeMin && rangeMax) {
    rangeMin.addEventListener('input', updateSliderFill);
    rangeMax.addEventListener('input', updateSliderFill);
    rangeMin.addEventListener('change', applyAllFilters);
    rangeMax.addEventListener('change', applyAllFilters);

    if (inputMin) {
      inputMin.addEventListener('change', function () {
        let val = parseFloat(this.value) || 0;
        if (val < parseFloat(rangeMin.min)) val = parseFloat(rangeMin.min);
        if (val > parseFloat(rangeMax.value) - 1) val = parseFloat(rangeMax.value) - 1;
        rangeMin.value = val;
        this.value = Math.round(val);
        updateSliderFill();
      });
    }

    if (inputMax) {
      inputMax.addEventListener('change', function () {
        let val = parseFloat(this.value) || 100;
        if (val > parseFloat(rangeMax.max)) val = parseFloat(rangeMax.max);
        if (val < parseFloat(rangeMin.value) + 1) val = parseFloat(rangeMin.value) + 1;
        rangeMax.value = val;
        this.value = Math.round(val);
        updateSliderFill();
      });
    }

    updateSliderFill();
  }

  /* ==========================================================================
     3. MASTER FILTER ENGINE
     ========================================================================== */
  const productCards = Array.from(document.querySelectorAll('.category-product-card'));
  const noProductsBox = document.getElementById('noProductsFoundBox');
  const visibleCountText = document.getElementById('visibleCountText');
  let activeQuickFilter = 'all';

  function applyAllFilters() {
    // 1. Get selected brands
    const checkedBrandBoxes = Array.from(document.querySelectorAll('.brand-filter-checkbox:checked'));
    const selectedBrands = checkedBrandBoxes.map(cb => (cb.dataset.brandSlug || cb.dataset.brand || '').toLowerCase());

    // 2. Get price limits
    const curMinPrice = rangeMin ? parseFloat(rangeMin.value) : 0;
    const curMaxPrice = rangeMax ? parseFloat(rangeMax.value) : 999999;

    // 3. Get selected puff ranges
    const checkedPuffBoxes = Array.from(document.querySelectorAll('.puff-filter-checkbox:checked'));
    const puffRanges = checkedPuffBoxes.map(cb => ({
      min: parseInt(cb.dataset.minPuffs, 10) || 0,
      max: parseInt(cb.dataset.maxPuffs, 10) || 999999
    }));

    // 4. Get selected nicotine strengths
    const checkedNicBoxes = Array.from(document.querySelectorAll('.nic-filter-checkbox:checked'));
    const selectedNics = checkedNicBoxes.map(cb => parseInt(cb.dataset.nic, 10));

    // 5. In stock only
    const inStockCheckbox = document.getElementById('inStockCheckbox');
    const requireInStock = inStockCheckbox ? inStockCheckbox.checked : false;

    // Evaluate each card
    productCards.forEach(function (card) {
      let isVisible = true;

      // Quick pill filter
      if (activeQuickFilter && activeQuickFilter !== 'all') {
        const cardPuffs = parseInt(card.dataset.puffs, 10) || 0;
        const cardNic = parseInt(card.dataset.nicotine, 10) || 0;
        const cardIsBestseller = card.dataset.bestseller === '1';

        if (activeQuickFilter === 'bestseller') {
          if (!cardIsBestseller) isVisible = false;
        } else if (activeQuickFilter === 'zero-nic') {
          if (cardNic !== 0) isVisible = false;
        } else if (activeQuickFilter === '5000' || activeQuickFilter === '5k') {
          if (cardPuffs < 5000 || cardPuffs >= 10000) isVisible = false;
        } else if (activeQuickFilter === '10000' || activeQuickFilter === '10k') {
          if (cardPuffs < 10000 || cardPuffs >= 15000) isVisible = false;
        } else if (activeQuickFilter === '15000' || activeQuickFilter === '15k') {
          if (cardPuffs < 15000 || cardPuffs >= 20000) isVisible = false;
        } else if (activeQuickFilter === '20000' || activeQuickFilter === '20k') {
          if (cardPuffs < 20000 || cardPuffs >= 30000) isVisible = false;
        } else if (activeQuickFilter === '30000' || activeQuickFilter === '30k') {
          if (cardPuffs < 30000 || cardPuffs >= 50000) isVisible = false;
        } else if (activeQuickFilter === '50000' || activeQuickFilter === '50k') {
          if (cardPuffs < 50000) isVisible = false;
        } else {
          const cardCat = card.dataset.category || '';
          if (cardCat !== activeQuickFilter) isVisible = false;
        }
      }

      // Brand filter
      if (isVisible && selectedBrands.length > 0) {
        const cardBrand = (card.dataset.brand || '').toLowerCase();
        const cardBrandSlug = (card.dataset.brandSlug || '').toLowerCase();
        const matchesBrand = selectedBrands.some(b => b === cardBrand || b === cardBrandSlug);
        if (!matchesBrand) isVisible = false;
      }

      // Price filter
      if (isVisible) {
        const price = parseFloat(card.dataset.price) || 0;
        if (price < curMinPrice || price > curMaxPrice) {
          isVisible = false;
        }
      }

      // Puff range filter
      if (isVisible && puffRanges.length > 0) {
        const puffs = parseInt(card.dataset.puffs, 10) || 0;
        const matchesPuff = puffRanges.some(r => puffs >= r.min && puffs <= r.max);
        if (!matchesPuff) isVisible = false;
      }

      // Nicotine strength filter
      if (isVisible && selectedNics.length > 0) {
        const nic = parseInt(card.dataset.nicotine, 10);
        const matchesNic = selectedNics.includes(nic);
        if (!matchesNic) isVisible = false;
      }

      // In stock filter
      if (isVisible && requireInStock) {
        if (card.dataset.instock !== 'true') isVisible = false;
      }

      card.dataset.filteredOut = isVisible ? 'false' : 'true';
    });

    currentPage = 1;
    renderPagination();
  }

  // Price Apply Button
  if (btnApplyPrice) {
    btnApplyPrice.addEventListener('click', function () {
      applyAllFilters();
      const origText = btnApplyPrice.textContent;
      btnApplyPrice.textContent = 'Applied ✓';
      setTimeout(() => { btnApplyPrice.textContent = origText; }, 900);
    });
  }

  // Brand checkboxes
  document.querySelectorAll('.brand-filter-checkbox').forEach(cb => {
    cb.addEventListener('change', applyAllFilters);
  });

  // Puff checkboxes
  document.querySelectorAll('.puff-filter-checkbox').forEach(cb => {
    cb.addEventListener('change', applyAllFilters);
  });

  // Nicotine checkboxes
  document.querySelectorAll('.nic-filter-checkbox').forEach(cb => {
    cb.addEventListener('change', applyAllFilters);
  });

  // In Stock checkbox
  const inStockCheckbox = document.getElementById('inStockCheckbox');
  if (inStockCheckbox) {
    inStockCheckbox.addEventListener('change', applyAllFilters);
  }

  // Quick Filter Pills with URL sync
  const quickPillBtns = document.querySelectorAll('.quick-pill-btn[data-filter]');
  quickPillBtns.forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      quickPillBtns.forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      activeQuickFilter = this.dataset.filter || 'all';

      // Update URL query parameters cleanly
      try {
        const url = new URL(window.location.href);
        if (activeQuickFilter === 'all') {
          url.searchParams.delete('puffs');
          url.searchParams.delete('puff');
          url.searchParams.delete('pill');
        } else {
          url.searchParams.set('puffs', activeQuickFilter);
        }
        if (window.history && window.history.pushState) {
          window.history.pushState({ puffs: activeQuickFilter }, '', url.toString());
        }
      } catch (err) {
        console.warn('URL pushState notice:', err);
      }

      applyAllFilters();
    });
  });

  // Handle browser back & forward navigation
  window.addEventListener('popstate', function () {
    const params = new URLSearchParams(window.location.search);
    const p = (params.get('puffs') || params.get('puff') || 'all').toLowerCase().trim();
    activeQuickFilter = (p === '5k' ? '5000' : (p === '10k' ? '10000' : (p === '15k' ? '15000' : (p === '20k' ? '20000' : (p === '30k' ? '30000' : (p === '50k' ? '50000' : p))))));

    quickPillBtns.forEach(b => {
      const f = b.dataset.filter;
      if (f === activeQuickFilter || (f === 'all' && (activeQuickFilter === 'all' || !activeQuickFilter))) {
        b.classList.add('active');
      } else {
        b.classList.remove('active');
      }
    });

    applyAllFilters();
  });

  // Reset All Filters
  const btnResetAll = document.getElementById('btnResetAllFilters');
  if (btnResetAll) {
    btnResetAll.addEventListener('click', function () {
      document.querySelectorAll('.brand-filter-checkbox').forEach(cb => { cb.checked = false; });
      document.querySelectorAll('.puff-filter-checkbox').forEach(cb => { cb.checked = false; });
      document.querySelectorAll('.nic-filter-checkbox').forEach(cb => { cb.checked = false; });
      if (inStockCheckbox) inStockCheckbox.checked = false;

      if (rangeMin && rangeMax) {
        rangeMin.value = rangeMin.min;
        rangeMax.value = rangeMax.max;
        updateSliderFill();
      }

      quickPillBtns.forEach(b => b.classList.remove('active'));
      const allPill = document.querySelector('.quick-pill-btn[data-filter="all"]');
      if (allPill) allPill.classList.add('active');
      activeQuickFilter = 'all';

      try {
        const url = new URL(window.location.href);
        url.searchParams.delete('puffs');
        url.searchParams.delete('puff');
        url.searchParams.delete('pill');
        if (window.history && window.history.pushState) {
          window.history.pushState({ puffs: 'all' }, '', url.toString());
        }
      } catch (err) { }

      applyAllFilters();
    });
  }

  /* ==========================================================================
     4. GRID / LIST VIEW SWITCHER
     ========================================================================== */
  const btnGridView = document.getElementById('btnGridView');
  const btnListView = document.getElementById('btnListView');
  const productGrid = document.getElementById('categoryProductGrid');

  if (btnGridView && btnListView && productGrid) {
    btnGridView.addEventListener('click', function () {
      btnGridView.classList.add('active');
      btnListView.classList.remove('active');
      productGrid.classList.remove('list-view');
      localStorage.setItem('vape_collection_view', 'grid');
    });

    btnListView.addEventListener('click', function () {
      btnListView.classList.add('active');
      btnGridView.classList.remove('active');
      productGrid.classList.add('list-view');
      localStorage.setItem('vape_collection_view', 'list');
    });

    const savedView = localStorage.getItem('vape_collection_view');
    if (savedView === 'list') {
      btnListView.click();
    }
  }

  /* ==========================================================================
     5. SORT DROPDOWN & DIRECTION
     ========================================================================== */
  const sortSelect = document.getElementById('categorySortSelect');
  const sortDirectionBtn = document.getElementById('sortDirectionBtn');
  let isAscending = true;

  function sortProducts() {
    if (!productGrid) return;
    const cards = Array.from(productGrid.querySelectorAll('.category-product-card'));
    const sortVal = sortSelect ? sortSelect.value : 'position';

    cards.sort(function (a, b) {
      if (sortVal === 'price-asc') {
        const priceA = parseFloat(a.dataset.price) || 0;
        const priceB = parseFloat(b.dataset.price) || 0;
        return isAscending ? priceA - priceB : priceB - priceA;
      } else if (sortVal === 'price-desc') {
        const priceA = parseFloat(a.dataset.price) || 0;
        const priceB = parseFloat(b.dataset.price) || 0;
        return isAscending ? priceB - priceA : priceA - priceB;
      } else if (sortVal === 'name') {
        const titleA = (a.querySelector('.card-product-title')?.textContent || '').trim().toLowerCase();
        const titleB = (b.querySelector('.card-product-title')?.textContent || '').trim().toLowerCase();
        return isAscending ? titleA.localeCompare(titleB) : titleB.localeCompare(titleA);
      } else if (sortVal === 'newest') {
        const idA = parseInt(a.dataset.id, 10) || 0;
        const idB = parseInt(b.dataset.id, 10) || 0;
        return isAscending ? idB - idA : idA - idB;
      } else if (sortVal === 'bestseller') {
        const isHotA = a.dataset.category === 'bestseller' ? 1 : 0;
        const isHotB = b.dataset.category === 'bestseller' ? 1 : 0;
        return isAscending ? isHotB - isHotA : isHotA - isHotB;
      }
      return 0;
    });

    cards.forEach(card => productGrid.appendChild(card));
    renderPagination();
  }

  if (sortSelect) {
    sortSelect.addEventListener('change', sortProducts);
  }

  if (sortDirectionBtn) {
    sortDirectionBtn.addEventListener('click', function () {
      isAscending = !isAscending;
      this.style.transform = isAscending ? 'rotate(0deg)' : 'rotate(180deg)';
      sortProducts();
    });
  }

  /* ==========================================================================
     6. MOBILE FILTER DRAWER & BACKDROP
     ========================================================================== */
  const mobileTriggerBtn = document.getElementById('mobileFilterTriggerBtn');
  const mobileCloseBtn = document.getElementById('mobileFilterCloseBtn');
  const sidebarCol = document.getElementById('categorySidebarCol');
  const filterBackdrop = document.getElementById('mobileFilterBackdrop');

  function openMobileFilters() {
    if (sidebarCol) sidebarCol.classList.add('mobile-open');
    if (filterBackdrop) filterBackdrop.classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  function closeMobileFilters() {
    if (sidebarCol) sidebarCol.classList.remove('mobile-open');
    if (filterBackdrop) filterBackdrop.classList.remove('show');
    document.body.style.overflow = '';
  }

  if (mobileTriggerBtn) mobileTriggerBtn.addEventListener('click', openMobileFilters);
  if (mobileCloseBtn) mobileCloseBtn.addEventListener('click', closeMobileFilters);
  if (filterBackdrop) filterBackdrop.addEventListener('click', closeMobileFilters);

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && sidebarCol && sidebarCol.classList.contains('mobile-open')) {
      closeMobileFilters();
    }
  });

  /* ==========================================================================
     7. PAGINATION & RESULTS COUNT
     ========================================================================== */
  let currentPage = 1;
  const itemsPerPage = 16;
  const paginationBar = document.getElementById('categoryPaginationBar');
  const paginationPagesWrap = document.getElementById('paginationPagesWrap');
  const paginationPrevBtn = document.getElementById('paginationPrevBtn');
  const paginationNextBtn = document.getElementById('paginationNextBtn');
  const paginationInfoText = document.getElementById('paginationInfoText');

  function renderPagination() {
    const allCards = Array.from(document.querySelectorAll('.category-product-card'));
    const eligibleCards = allCards.filter(card => card.dataset.filteredOut !== 'true');

    const totalEligible = eligibleCards.length;
    const totalPages = Math.ceil(totalEligible / itemsPerPage) || 1;

    if (visibleCountText) {
      visibleCountText.textContent = totalEligible;
    }

    if (noProductsBox) {
      noProductsBox.style.display = totalEligible === 0 ? 'block' : 'none';
    }

    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    const startIdx = (currentPage - 1) * itemsPerPage;
    const endIdx = Math.min(startIdx + itemsPerPage, totalEligible);

    // Hide all
    allCards.forEach(card => { card.style.display = 'none'; });

    // Show slice
    eligibleCards.forEach((card, idx) => {
      if (idx >= startIdx && idx < endIdx) {
        card.style.display = '';
      }
    });

    // Pagination info & buttons
    if (paginationBar) {
      paginationBar.style.display = totalEligible > itemsPerPage ? 'flex' : 'none';
    }

    if (paginationInfoText) {
      paginationInfoText.innerHTML = totalEligible > 0 ? `Showing <strong>${startIdx + 1}–${endIdx}</strong> of <strong>${totalEligible}</strong> products` : 'Showing <strong>0</strong> products';
    }

    if (paginationPagesWrap) {
      paginationPagesWrap.innerHTML = '';
      for (let p = 1; p <= totalPages; p++) {
        const pageBtn = document.createElement('button');
        pageBtn.type = 'button';
        pageBtn.className = 'pagination-page-btn' + (p === currentPage ? ' active' : '');
        pageBtn.textContent = p;
        pageBtn.dataset.page = p;
        pageBtn.addEventListener('click', function () {
          currentPage = p;
          renderPagination();
          scrollToProductGrid();
        });
        paginationPagesWrap.appendChild(pageBtn);
      }
    }

    if (paginationPrevBtn) {
      if (currentPage <= 1) {
        paginationPrevBtn.classList.add('disabled');
        paginationPrevBtn.disabled = true;
      } else {
        paginationPrevBtn.classList.remove('disabled');
        paginationPrevBtn.disabled = false;
      }
    }

    if (paginationNextBtn) {
      if (currentPage >= totalPages) {
        paginationNextBtn.classList.add('disabled');
        paginationNextBtn.disabled = true;
      } else {
        paginationNextBtn.classList.remove('disabled');
        paginationNextBtn.disabled = false;
      }
    }
  }

  if (paginationPrevBtn) {
    paginationPrevBtn.addEventListener('click', function () {
      if (currentPage > 1) {
        currentPage--;
        renderPagination();
        scrollToProductGrid();
      }
    });
  }

  if (paginationNextBtn) {
    paginationNextBtn.addEventListener('click', function () {
      const eligibleCards = Array.from(document.querySelectorAll('.category-product-card')).filter(c => c.dataset.filteredOut !== 'true');
      const totalPages = Math.ceil(eligibleCards.length / itemsPerPage);
      if (currentPage < totalPages) {
        currentPage++;
        renderPagination();
        scrollToProductGrid();
      }
    });
  }

  function scrollToProductGrid() {
    const grid = document.getElementById('categoryProductGrid');
    if (grid) {
      grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  // Initialize filter state from current URL
  const initialParams = new URLSearchParams(window.location.search);
  const initialPuffParam = (initialParams.get('puffs') || initialParams.get('puff') || initialParams.get('pill') || '').toLowerCase().trim();
  if (initialPuffParam) {
    if (initialPuffParam === '5k' || initialPuffParam === '5000') activeQuickFilter = '5000';
    else if (initialPuffParam === '10k' || initialPuffParam === '10000') activeQuickFilter = '10000';
    else if (initialPuffParam === '15k' || initialPuffParam === '15000') activeQuickFilter = '15000';
    else if (initialPuffParam === '20k' || initialPuffParam === '20000') activeQuickFilter = '20000';
    else if (initialPuffParam === '30k' || initialPuffParam === '30000') activeQuickFilter = '30000';
    else if (initialPuffParam === '50k' || initialPuffParam === '50000') activeQuickFilter = '50000';
    else activeQuickFilter = initialPuffParam;

    quickPillBtns.forEach(b => {
      const f = b.dataset.filter;
      if (f === activeQuickFilter || (f === 'all' && (activeQuickFilter === 'all' || !activeQuickFilter))) {
        b.classList.add('active');
      } else {
        b.classList.remove('active');
      }
    });
  }

  // Initial render with all filters applied
  applyAllFilters();

  /* ==========================================================================
     8. ADD TO CART INTERACTION
     ========================================================================== */
  if (productGrid) {
    productGrid.addEventListener('click', function (e) {
      const btn = e.target.closest('.btn-card-add-cart');
      if (!btn) return;

      e.preventDefault();
      e.stopPropagation();

      const card = btn.closest('.category-product-card');
      const title = btn.dataset.title || (card ? card.querySelector('.card-product-title a')?.textContent.trim() : 'Vape Device');
      const price = parseFloat(btn.dataset.price) || 14.99;
      const img = card ? card.querySelector('.card-product-img')?.getAttribute('src') : 'assets/images/no-image.svg';
      const id = btn.dataset.id || title.toLowerCase().replace(/[^a-z0-9]+/g, '-');

      const cartInstance = window.InfinityCart || window.NewVapeCart;
      if (cartInstance) {
        cartInstance.add({
          id: id,
          title: title,
          price: price,
          quantity: 1,
          flavor: '',
          image: img
        });
      }

      const origText = btn.textContent;
      btn.classList.add('added');
      btn.textContent = 'ADDED ✓';

      setTimeout(() => {
        btn.classList.remove('added');
        btn.textContent = origText;
      }, 1600);
    });
  }

  /* ==========================================================================
     9. COLLECTION FAQS ACCORDION
     ========================================================================== */
  const faqsSection = document.getElementById('collectionFaqsSection');
  if (faqsSection) {
    faqsSection.addEventListener('click', function (e) {
      const btn = e.target.closest('.category-faq-question-btn');
      if (!btn) return;

      const item = btn.closest('.category-faq-item');
      if (!item) return;

      const isOpen = item.classList.contains('open');
      item.classList.toggle('open');
      btn.setAttribute('aria-expanded', !isOpen);
    });
  }

  /* ==========================================================================
     10. CATEGORY BOTTOM DESCRIPTION READ MORE / READ LESS TOGGLE
     ========================================================================== */
  const btnReadMore = document.getElementById('btnReadMoreToggle');
  const textWrap = document.getElementById('categoryBottomTextWrap');
  if (btnReadMore && textWrap) {
    btnReadMore.addEventListener('click', function () {
      const isExpanded = textWrap.classList.contains('expanded');
      textWrap.classList.toggle('expanded');
      btnReadMore.classList.toggle('expanded');
      const textSpan = btnReadMore.querySelector('.read-more-text');
      if (textSpan) {
        textSpan.textContent = isExpanded ? 'Read More' : 'Read Less';
      }
      btnReadMore.setAttribute('aria-expanded', !isExpanded);
    });
  }

});

