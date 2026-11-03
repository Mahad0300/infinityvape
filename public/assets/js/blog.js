/**
 * BLOG PAGE JAVASCRIPT (blog.js)
 * Handles search filter, category filtering, clear filter, and pagination.
 */

document.addEventListener('DOMContentLoaded', () => {
  'use strict';

  const searchInput = document.getElementById('blogSearchInput');
  const searchForm = document.getElementById('blogSearchForm');
  const categoryItems = document.querySelectorAll('.blog-categories-list .category-list-item');
  const activeFilterBar = document.getElementById('activeFilterBar');
  const currentFilterLabel = document.getElementById('currentFilterLabel');
  const btnClearFilter = document.getElementById('btnClearFilter');
  const blogPosts = document.querySelectorAll('.blog-post-item');
  const articlesCount = document.getElementById('blogArticlesCount');

  let currentCategory = 'all';
  let searchQuery = '';

  function filterPosts() {
    let visibleCount = 0;
    const q = searchQuery.trim().toLowerCase();

    blogPosts.forEach((post) => {
      const categories = (post.dataset.category || '').toLowerCase().split(' ');
      const title = (post.dataset.title || post.textContent || '').toLowerCase();

      const matchesCat = (currentCategory === 'all') || categories.includes(currentCategory);
      const matchesSearch = !q || title.includes(q);

      if (matchesCat && matchesSearch) {
        post.style.display = '';
        visibleCount++;
      } else {
        post.style.display = 'none';
      }
    });

    // Update filter status bar
    if (activeFilterBar && currentFilterLabel) {
      if (currentCategory !== 'all' || q) {
        activeFilterBar.style.display = 'flex';
        let labelText = '';
        if (currentCategory !== 'all') {
          const activeItem = document.querySelector(`.category-list-item[data-cat="${currentCategory}"] .category-name`);
          labelText += activeItem ? activeItem.textContent : currentCategory;
        }
        if (q) {
          labelText += (labelText ? ' & ' : '') + `Search: "${q}"`;
        }
        currentFilterLabel.textContent = labelText;
      } else {
        activeFilterBar.style.display = 'none';
      }
    }

    if (articlesCount) {
      articlesCount.textContent = `${visibleCount} guide(s) found`;
    }
  }

  // 1. Search Bar Interactions
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      searchQuery = e.target.value;
      filterPosts();
    });
  }

  if (searchForm) {
    searchForm.addEventListener('submit', (e) => {
      e.preventDefault();
      filterPosts();
    });
  }

  // 2. Category List Filtering
  categoryItems.forEach((item) => {
    item.addEventListener('click', (e) => {
      e.preventDefault();
      categoryItems.forEach((c) => c.classList.remove('active'));
      item.classList.add('active');

      currentCategory = item.dataset.cat || 'all';
      filterPosts();

      // Smooth scroll to articles if on mobile
      const mainCol = document.querySelector('.blog-main-col');
      if (window.innerWidth < 992 && mainCol) {
        mainCol.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // 3. Clear Filter Button
  if (btnClearFilter) {
    btnClearFilter.addEventListener('click', () => {
      currentCategory = 'all';
      searchQuery = '';
      if (searchInput) searchInput.value = '';

      categoryItems.forEach((c) => {
        if (c.dataset.cat === 'all') {
          c.classList.add('active');
        } else {
          c.classList.remove('active');
        }
      });

      filterPosts();
    });
  }

  // 4. Pagination
  const pageButtons = document.querySelectorAll('.blog-num-btn');
  const btnPrev = document.getElementById('btnBlogPrev');
  const btnNext = document.getElementById('btnBlogNext');

  pageButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
      pageButtons.forEach((b) => b.classList.remove('active'));
      btn.classList.add('active');

      const pageNum = parseInt(btn.dataset.page, 10);
      if (btnPrev) btnPrev.classList.toggle('disabled', pageNum <= 1);
      if (btnNext) btnNext.classList.toggle('disabled', pageNum >= 3);

      const gridSection = document.querySelector('.blog-articles-grid-section');
      if (gridSection) {
        gridSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  if (btnPrev) {
    btnPrev.addEventListener('click', () => {
      const activeBtn = document.querySelector('.blog-num-btn.active');
      const curPage = parseInt(activeBtn?.dataset.page || '1', 10);
      if (curPage > 1) {
        const targetBtn = document.querySelector(`.blog-num-btn[data-page="${curPage - 1}"]`);
        if (targetBtn) targetBtn.click();
      }
    });
  }

  if (btnNext) {
    btnNext.addEventListener('click', () => {
      const activeBtn = document.querySelector('.blog-num-btn.active');
      const curPage = parseInt(activeBtn?.dataset.page || '1', 10);
      if (curPage < 3) {
        const targetBtn = document.querySelector(`.blog-num-btn[data-page="${curPage + 1}"]`);
        if (targetBtn) targetBtn.click();
      }
    });
  }
});
