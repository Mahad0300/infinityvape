/**
 * New Vape - Blogs Index Page Script
 * Search filtering & status toggles
 */
document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('blogSearchInput');
  const statusFilter = document.getElementById('blogStatusFilter');

  function applyFilter() {
    const q = searchInput ? encodeURIComponent(searchInput.value.trim()) : '';
    const s = statusFilter ? encodeURIComponent(statusFilter.value) : '';
    let target = window.location.pathname + '?';
    if (q) target += 'q=' + q + '&';
    if (s && s !== 'all') target += 'status=' + s + '&';
    window.location.href = target.replace(/[?&]$/, '');
  }

  if (searchInput) {
    searchInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        applyFilter();
      }
    });
  }

  if (statusFilter) {
    statusFilter.addEventListener('change', () => {
      applyFilter();
    });
  }
});
