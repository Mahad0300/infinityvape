/**
 * GLOBAL JAVASCRIPT
 * Handles header interactions, search bar, mobile navigation drawer, and global UI components.
 */

document.addEventListener('DOMContentLoaded', () => {
  // Mobile Navigation Drawer Toggle
  const mobileToggle = document.getElementById('mobileNavToggle');
  const mobileDrawer = document.getElementById('mobileNavDrawer');
  const mobileBackdrop = document.getElementById('mobileNavBackdrop');
  const mobileClose = document.getElementById('mobileNavClose');

  function openMobileNav() {
    if (mobileDrawer && mobileBackdrop) {
      mobileDrawer.classList.add('active');
      mobileBackdrop.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeMobileNav() {
    if (mobileDrawer && mobileBackdrop) {
      mobileDrawer.classList.remove('active');
      mobileBackdrop.classList.remove('active');
      document.body.style.overflow = '';
    }
  }

  if (mobileToggle) {
    mobileToggle.addEventListener('click', openMobileNav);
  }

  if (mobileClose) {
    mobileClose.addEventListener('click', closeMobileNav);
  }

  if (mobileBackdrop) {
    mobileBackdrop.addEventListener('click', closeMobileNav);
  }

  // Mobile Navigation Accordion / Dropdown Toggle
  const mobileDropdownBtns = document.querySelectorAll('.mobile-dropdown-btn');
  mobileDropdownBtns.forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const parent = btn.closest('.mobile-nav-item');
      if (!parent) return;

      const isOpen = parent.classList.contains('open');

      // Close other open dropdowns for accordion effect
      document.querySelectorAll('.mobile-nav-item.has-dropdown').forEach((item) => {
        if (item !== parent) {
          item.classList.remove('open');
          const otherBtn = item.querySelector('.mobile-dropdown-btn');
          if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
        }
      });

      if (isOpen) {
        parent.classList.remove('open');
        btn.setAttribute('aria-expanded', 'false');
      } else {
        parent.classList.add('open');
        btn.setAttribute('aria-expanded', 'true');
      }
    });
  });

  // Global Search Form Handling
  const searchForm = document.getElementById('headerSearchForm');
  const searchInput = document.getElementById('headerSearchInput');
  const searchToggle = document.getElementById('headerSearchToggle');
  const headerMain = document.querySelector('.site-header-main');
  const mobileHeaderQuery = window.matchMedia('(max-width: 1144px)');

  function setMobileSearchOpen(open) {
    if (!headerMain || !searchToggle) return;
    headerMain.classList.toggle('is-search-open', open);
    searchToggle.classList.toggle('is-open', open);
    searchToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    searchToggle.setAttribute('aria-label', open ? 'Close search' : 'Open search');
    if (open && searchInput) {
      searchInput.focus();
    }
  }

  if (searchToggle && headerMain) {
    searchToggle.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      setMobileSearchOpen(!headerMain.classList.contains('is-search-open'));
    });

    document.addEventListener('click', (e) => {
      if (!headerMain.classList.contains('is-search-open')) return;
      if (headerMain.contains(e.target)) return;
      if (e.target.closest('.site-nav-bar')) return;
      setMobileSearchOpen(false);
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && headerMain.classList.contains('is-search-open')) {
        setMobileSearchOpen(false);
      }
    });

    const onHeaderModeChange = (event) => {
      if (!event.matches) setMobileSearchOpen(false);
    };
    if (typeof mobileHeaderQuery.addEventListener === 'function') {
      mobileHeaderQuery.addEventListener('change', onHeaderModeChange);
    } else if (typeof mobileHeaderQuery.addListener === 'function') {
      mobileHeaderQuery.addListener(onHeaderModeChange);
    }
  }

  const searchSuggest = document.getElementById('headerSearchSuggest');
  let suggestTimer = null;
  let suggestRequest = null;
  let suggestItems = [];

  function hideSuggestions() {
    if (!searchSuggest) return;
    searchSuggest.hidden = true;
    searchSuggest.innerHTML = '';
    suggestItems = [];
    if (searchInput) searchInput.setAttribute('aria-expanded', 'false');
  }

  function setActiveSuggestion(index) {
    suggestItems.forEach((item, i) => {
      item.classList.toggle('is-active', i === index);
    });
    if (suggestItems[index]) suggestItems[index].scrollIntoView({ block: 'nearest' });
  }

  function renderSuggestions(data, query) {
    if (!searchSuggest) return;
    searchSuggest.innerHTML = '';
    suggestItems = [];

    const collections = Array.isArray(data.collections) ? data.collections : [];
    const products = Array.isArray(data.products) ? data.products : [];

    if (!collections.length && !products.length) {
      const empty = document.createElement('p');
      empty.className = 'header-search-suggest-empty';
      empty.textContent = 'No matching products or categories.';
      searchSuggest.appendChild(empty);
    } else {
      if (collections.length) {
        const label = document.createElement('p');
        label.className = 'header-search-suggest-label';
        label.textContent = 'Categories';
        searchSuggest.appendChild(label);
        collections.forEach((collection) => {
          const link = document.createElement('a');
          link.className = 'header-search-suggest-item';
          link.href = collection.url || '#';
          const title = document.createElement('span');
          title.className = 'header-search-suggest-title';
          title.textContent = collection.name || '';
          link.appendChild(title);
          searchSuggest.appendChild(link);
        });
      }

      if (products.length) {
        const label = document.createElement('p');
        label.className = 'header-search-suggest-label';
        label.textContent = 'Products';
        searchSuggest.appendChild(label);
        products.forEach((product) => {
          const link = document.createElement('a');
          link.className = 'header-search-suggest-item';
          link.href = product.url || '#';
          const img = document.createElement('img');
          img.className = 'header-search-suggest-thumb';
          img.alt = (function (url, fallback) {
            var file = decodeURIComponent(String(url || '').split('/').pop().split('?')[0]).replace(/\.[a-z0-9]+$/i, '');
            if (!file || /^(prod|brand|brand_hdr|brand_promo|col|blog|var|banner|deal)_\d+/i.test(file)) {
              return fallback || '';
            }
            return file.trim();
          })(product.image, product.title || '');
          img.src = product.image || '';
          const title = document.createElement('span');
          title.className = 'header-search-suggest-title';
          title.textContent = product.title || '';
          const price = document.createElement('span');
          price.className = 'header-search-suggest-price';
          price.textContent = '$' + (product.price || '0.00');
          link.appendChild(img);
          link.appendChild(title);
          link.appendChild(price);
          searchSuggest.appendChild(link);
        });
      }

      const all = document.createElement('a');
      all.className = 'header-search-suggest-all';
      all.href = (searchForm ? searchForm.action : 'search') + '?q=' + encodeURIComponent(query);
      all.textContent = 'View all results';
      searchSuggest.appendChild(all);
    }

    suggestItems = Array.from(searchSuggest.querySelectorAll('a'));
    searchSuggest.hidden = false;
    if (searchInput) searchInput.setAttribute('aria-expanded', 'true');
  }

  function requestSuggestions(query) {
    if (!searchForm || !searchSuggest) return;
    const endpoint = searchForm.dataset.suggestUrl;
    if (!endpoint) return;
    if (suggestRequest) suggestRequest.abort();
    suggestRequest = new AbortController();
    fetch(endpoint + '?q=' + encodeURIComponent(query), {
      signal: suggestRequest.signal,
      headers: { 'Accept': 'application/json' }
    })
      .then((response) => response.ok ? response.json() : null)
      .then((data) => {
        if (!data || searchInput.value.trim() !== query) return;
        renderSuggestions(data, query);
      })
      .catch((error) => {
        if (error.name !== 'AbortError') hideSuggestions();
      });
  }

  if (searchForm && searchInput) {
    searchInput.addEventListener('input', () => {
      const query = searchInput.value.trim();
      clearTimeout(suggestTimer);
      if (query.length < 2) {
        hideSuggestions();
        return;
      }
      suggestTimer = setTimeout(() => requestSuggestions(query), 180);
    });

    searchInput.addEventListener('keydown', (e) => {
      if (searchSuggest && !searchSuggest.hidden && suggestItems.length && (e.key === 'ArrowDown' || e.key === 'ArrowUp')) {
        e.preventDefault();
        const current = suggestItems.findIndex((item) => item.classList.contains('is-active'));
        const next = e.key === 'ArrowDown'
          ? (current + 1) % suggestItems.length
          : (current <= 0 ? suggestItems.length - 1 : current - 1);
        setActiveSuggestion(next);
      } else if (e.key === 'Enter' && searchSuggest && !searchSuggest.hidden) {
        const active = suggestItems.find((item) => item.classList.contains('is-active'));
        if (active) {
          e.preventDefault();
          window.location.href = active.href;
        }
      } else if (e.key === 'Escape' && searchSuggest && !searchSuggest.hidden) {
        e.stopPropagation();
        hideSuggestions();
      }
    });

    searchForm.addEventListener('submit', (e) => {
      const query = searchInput.value.trim();
      if (!query) {
        e.preventDefault();
        searchInput.focus();
      }
    });

    document.addEventListener('click', (e) => {
      if (!searchSuggest || searchSuggest.hidden) return;
      if (e.target.closest('#headerSearchForm') || e.target.closest('#headerSearchSuggest')) return;
      hideSuggestions();
    });
  }

  // Hide the header while scrolling down, and slide it back on a small scroll up.
  const headerShell = document.getElementById('siteHeaderShell');
  const headerSpacer = document.getElementById('siteHeaderSpacer');
  if (headerShell && headerSpacer) {
    let lastScrollY = window.scrollY;
    let scrollAccum = 0;
    let revealQueued = false;
    const hideAfter = 8;
    const showAfter = 4;

    function headerNaturalTop() {
      if (!headerShell.classList.contains('is-fixed')) {
        const top = headerShell.getBoundingClientRect().top + window.scrollY;
        headerShell.dataset.naturalTop = String(top);
        return top;
      }
      return Number(headerShell.dataset.naturalTop || 0);
    }

    function headerShouldStay() {
      const active = document.activeElement;
      if (active && headerShell.contains(active) && (active.tagName === 'INPUT' || active.tagName === 'TEXTAREA')) return true;
      const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
      if (finePointer && headerShell.matches(':hover')) return true;
      if (document.body.classList.contains('cart-drawer-open')) return true;
      const drawer = document.getElementById('mobileNavDrawer');
      return !!(drawer && drawer.classList.contains('active'));
    }

    function releaseHeader() {
      headerShell.classList.remove('is-fixed', 'is-header-hidden');
      headerSpacer.style.height = '0px';
      revealQueued = false;
    }

    function pinHeader(hidden) {
      const height = headerShell.offsetHeight;
      headerSpacer.style.height = height + 'px';
      headerShell.classList.add('is-fixed');
      headerShell.classList.toggle('is-header-hidden', hidden);
    }

    function revealHeader() {
      if (!headerShell.classList.contains('is-fixed')) {
        pinHeader(true);
        revealQueued = true;
        requestAnimationFrame(() => {
          requestAnimationFrame(() => {
            if (revealQueued) headerShell.classList.remove('is-header-hidden');
            revealQueued = false;
          });
        });
        return;
      }
      headerShell.classList.remove('is-header-hidden');
    }

    function onHeaderScroll() {
      const y = Math.max(window.scrollY, 0);
      const delta = y - lastScrollY;
      lastScrollY = y;
      if (delta === 0) return;

      const naturalTop = headerNaturalTop();
      const shellHeight = headerShell.offsetHeight;

      if (y <= naturalTop + 2) {
        scrollAccum = 0;
        releaseHeader();
        return;
      }

      if (headerShouldStay()) {
        scrollAccum = 0;
        if (y >= naturalTop + shellHeight && !headerShell.classList.contains('is-fixed')) {
          pinHeader(false);
        } else if (headerShell.classList.contains('is-header-hidden')) {
          headerShell.classList.remove('is-header-hidden');
        }
        return;
      }

      if (Math.sign(delta) !== Math.sign(scrollAccum)) scrollAccum = 0;
      scrollAccum += delta;

      const headerIsOffscreen = y >= naturalTop + shellHeight - 1;
      if (!headerShell.classList.contains('is-fixed')) {
        if (headerIsOffscreen && scrollAccum <= -showAfter) {
          scrollAccum = 0;
          revealHeader();
        }
        return;
      }

      if (scrollAccum >= hideAfter) {
        scrollAccum = 0;
        headerShell.classList.add('is-header-hidden');
      } else if (scrollAccum <= -showAfter) {
        scrollAccum = 0;
        revealHeader();
      }
    }

    window.addEventListener('scroll', onHeaderScroll, { passive: true });
    window.addEventListener('resize', () => {
      if (headerShell.classList.contains('is-fixed')) {
        headerSpacer.style.height = headerShell.offsetHeight + 'px';
      }
    });
    if (typeof ResizeObserver === 'function') {
      const headerResize = new ResizeObserver(() => {
        if (headerShell.classList.contains('is-fixed')) {
          headerSpacer.style.height = headerShell.offsetHeight + 'px';
        }
      });
      headerResize.observe(headerShell);
    }
  }
});
