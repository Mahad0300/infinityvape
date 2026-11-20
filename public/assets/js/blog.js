/**
 * BLOG PAGE JAVASCRIPT (blog.js)
 * Sidebar search filters the Categories list only. Post titles stay unchanged.
 */

document.addEventListener('DOMContentLoaded', () => {
  'use strict';

  const searchInput = document.getElementById('blogSearchInput');
  const searchForm = document.getElementById('blogSearchForm');
  const categoryItems = document.querySelectorAll('.blog-categories-list .category-list-item');

  let searchQuery = '';

  function filterCategories() {
    const q = searchQuery.trim().toLowerCase();

    categoryItems.forEach((item) => {
      const nameEl = item.querySelector('.category-name');
      const name = (nameEl ? nameEl.textContent : '').trim().toLowerCase();
      const alwaysVisible = name === 'all articles';
      item.style.display = (!q || alwaysVisible || name.includes(q)) ? '' : 'none';
    });
  }

  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      searchQuery = e.target.value;
      filterCategories();
    });
  }

  if (searchForm) {
    searchForm.addEventListener('submit', (e) => {
      e.preventDefault();
      filterCategories();
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
