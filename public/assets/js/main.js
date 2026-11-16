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
  const mobileHeaderQuery = window.matchMedia('(max-width: 991px)');

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

  if (searchForm && searchInput) {
    searchForm.addEventListener('submit', (e) => {
      const query = searchInput.value.trim();
      if (!query) {
        e.preventDefault();
        searchInput.focus();
      }
    });
  }
});
