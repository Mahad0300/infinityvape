/**
 * New Vape - Admin Core JavaScript (Global)
 * Handles mobile sidebar, SweetAlert-style confirmation dialogs, toast notifications, and universal table pagination
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Mobile Sidebar Toggle & Backdrop Drawer
  const sidebar = document.getElementById('adminSidebar');
  const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
  const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
  const drawerBackdrop = document.getElementById('adminDrawerBackdrop');

  function openSidebar() {
    if (sidebar && drawerBackdrop) {
      sidebar.classList.add('open');
      drawerBackdrop.classList.add('open');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeSidebar() {
    if (sidebar && drawerBackdrop) {
      sidebar.classList.remove('open');
      drawerBackdrop.classList.remove('open');
      document.body.style.overflow = '';
    }
  }

  if (sidebarToggleBtn) sidebarToggleBtn.addEventListener('click', openSidebar);
  if (sidebarCloseBtn) sidebarCloseBtn.addEventListener('click', closeSidebar);
  if (drawerBackdrop) drawerBackdrop.addEventListener('click', closeSidebar);

  // Close on ESC
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      if (sidebar && sidebar.classList.contains('open')) closeSidebar();
      const activeSwal = document.querySelector('.admin-swal-overlay.active');
      if (activeSwal) closeSwal(activeSwal);
      const activeModal = document.querySelector('.admin-modal-overlay.active');
      if (activeModal) closeModal(activeModal);
    }
  });

  // 2. SweetAlert-Style Custom Confirmation Dialog Engine
  let swalResolve = null;

  function createSwalDOM() {
    let overlay = document.getElementById('adminSwalOverlay');
    if (overlay) return overlay;

    overlay = document.createElement('div');
    overlay.id = 'adminSwalOverlay';
    overlay.className = 'admin-swal-overlay';
    overlay.innerHTML = `
      <div class="admin-swal-box">
        <div class="admin-swal-icon-wrap danger" id="adminSwalIcon">
          <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="3 6 5 6 21 6"></polyline>
            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            <line x1="10" y1="11" x2="10" y2="17"></line>
            <line x1="14" y1="11" x2="14" y2="17"></line>
          </svg>
        </div>
        <h3 class="admin-swal-title" id="adminSwalTitle">Are you sure?</h3>
        <p class="admin-swal-text" id="adminSwalText">This record will be permanently deleted. This action cannot be undone.</p>
        <div class="admin-swal-actions">
          <button type="button" class="admin-swal-btn admin-swal-btn-cancel" id="adminSwalCancelBtn">Cancel</button>
          <button type="button" class="admin-swal-btn admin-swal-btn-confirm-danger" id="adminSwalConfirmBtn">Yes, Delete</button>
        </div>
      </div>
    `;
    document.body.appendChild(overlay);

    const cancelBtn = overlay.querySelector('#adminSwalCancelBtn');
    const confirmBtn = overlay.querySelector('#adminSwalConfirmBtn');

    cancelBtn.addEventListener('click', () => {
      closeSwal(overlay);
      if (swalResolve) swalResolve(false);
    });

    confirmBtn.addEventListener('click', () => {
      closeSwal(overlay);
      if (swalResolve) swalResolve(true);
    });

    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) {
        closeSwal(overlay);
        if (swalResolve) swalResolve(false);
      }
    });

    return overlay;
  }

  function closeSwal(overlay) {
    if (overlay) {
      overlay.classList.remove('active');
      document.body.style.overflow = '';
    }
  }

  window.adminConfirm = function({
    title = 'Are you sure?',
    text = 'This record will be permanently deleted. This action cannot be undone.',
    confirmButtonText = 'Yes, Delete',
    cancelButtonText = 'Cancel',
    type = 'danger'
  } = {}) {
    return new Promise((resolve) => {
      swalResolve = resolve;
      const overlay = createSwalDOM();
      const titleEl = overlay.querySelector('#adminSwalTitle');
      const textEl = overlay.querySelector('#adminSwalText');
      const confirmBtn = overlay.querySelector('#adminSwalConfirmBtn');
      const cancelBtn = overlay.querySelector('#adminSwalCancelBtn');
      const iconWrap = overlay.querySelector('#adminSwalIcon');

      if (titleEl) titleEl.textContent = title;
      if (textEl) textEl.textContent = text;
      if (confirmBtn) {
        confirmBtn.textContent = confirmButtonText;
        confirmBtn.className = `admin-swal-btn ${type === 'danger' ? 'admin-swal-btn-confirm-danger' : 'admin-swal-btn-confirm-primary'}`;
      }
      if (cancelBtn) cancelBtn.textContent = cancelButtonText;

      if (iconWrap) {
        iconWrap.className = `admin-swal-icon-wrap ${type}`;
        if (type === 'danger') {
          iconWrap.innerHTML = `
            <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="3 6 5 6 21 6"></polyline>
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
              <line x1="10" y1="11" x2="10" y2="17"></line>
              <line x1="14" y1="11" x2="14" y2="17"></line>
            </svg>
          `;
        } else if (type === 'warning') {
          iconWrap.innerHTML = `
            <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
              <line x1="12" y1="9" x2="12" y2="13"></line>
              <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
          `;
        } else {
          iconWrap.innerHTML = `
            <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
          `;
        }
      }

      overlay.classList.add('active');
      document.body.style.overflow = 'hidden';
    });
  };

  // 3. Toast Notification System
  function getToastContainer() {
    let container = document.getElementById('adminToastContainer');
    if (!container) {
      container = document.createElement('div');
      container.id = 'adminToastContainer';
      container.className = 'admin-toast-container';
      document.body.appendChild(container);
    }
    return container;
  }

  window.adminToast = function(message, type = 'success') {
    const container = getToastContainer();
    const toast = document.createElement('div');
    toast.className = `admin-toast toast-${type}`;
    
    let iconSvg = '';
    if (type === 'success') {
      iconSvg = `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>`;
    } else if (type === 'danger') {
      iconSvg = `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>`;
    } else {
      iconSvg = `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#0284c7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`;
    }

    toast.innerHTML = `
      ${iconSvg}
      <span>${message}</span>
    `;

    container.appendChild(toast);
    void toast.offsetWidth; // trigger reflow
    toast.classList.add('show');

    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 250);
    }, 3000);
  };

  // 4. Delete Action Confirmation Interceptor (SweetAlert-style dialog replacing browser alert)
  document.addEventListener('click', async (e) => {
    const deleteBtn = e.target.closest('.btn-delete-row, [data-confirm-delete]');
    if (deleteBtn) {
      e.preventDefault();
      const itemName = deleteBtn.getAttribute('data-item-name') || 
                       deleteBtn.closest('tr')?.querySelector('.table-product-title, .table-cell-title, td:nth-child(2)')?.textContent?.trim() || 
                       'this record';
      const confirmed = await window.adminConfirm({
        title: 'Delete Item?',
        text: `Are you sure you want to delete ${itemName}? This action cannot be undone.`,
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        type: 'danger'
      });

      if (confirmed) {
        const form = deleteBtn.closest('form');
        if (form) {
          form.onsubmit = null;
          HTMLFormElement.prototype.submit.call(form);
          return;
        }

        const row = deleteBtn.closest('tr');
        if (row) {
          row.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
          row.style.opacity = '0';
          row.style.transform = 'translateX(10px)';
          setTimeout(() => {
            row.remove();
            window.adminToast('Record deleted successfully!', 'success');
          }, 250);
        } else {
          window.adminToast('Record deleted successfully!', 'success');
        }
      }
    }
  });

  // 5. Table Real-Time Filter & Client-Side Pagination Engine
  const adminTable = document.querySelector('.admin-table');
  const searchInput = document.getElementById('adminTableSearch');
  const statusSelect = document.getElementById('adminStatusFilter');
  const perPageSelect = document.getElementById('adminPerPageSelect');
  const paginationBar = document.querySelector('.table-pagination-bar');

  if (adminTable && (searchInput || statusSelect || perPageSelect || paginationBar)) {
    let currentPage = 1;
    let perPage = 10;
    const tableBody = adminTable.querySelector('tbody');
    
    function applyTablePagination() {
      if (!tableBody) return;
      const allRows = Array.from(tableBody.querySelectorAll('tr:not(.variant-subrow)'));
      const searchQuery = searchInput ? searchInput.value.toLowerCase().trim() : '';
      const statusFilter = statusSelect ? statusSelect.value.toLowerCase().trim() : 'all';
      perPage = perPageSelect ? parseInt(perPageSelect.value, 10) || 10 : 10;

      // Filter rows
      const matchingRows = allRows.filter(row => {
        const text = row.textContent.toLowerCase();
        const matchesSearch = !searchQuery || text.includes(searchQuery);
        
        let matchesStatus = true;
        if (statusFilter && statusFilter !== 'all') {
          const badges = Array.from(row.querySelectorAll('.badge-status'));
          if (badges.length > 0) {
            matchesStatus = badges.some(b => b.textContent.toLowerCase().includes(statusFilter));
          } else {
            matchesStatus = text.includes(statusFilter);
          }
        }

        return matchesSearch && matchesStatus;
      });

      const totalMatching = matchingRows.length;
      const totalPages = Math.max(1, Math.ceil(totalMatching / perPage));

      if (currentPage > totalPages) currentPage = totalPages;
      if (currentPage < 1) currentPage = 1;

      const startIndex = (currentPage - 1) * perPage;
      const endIndex = Math.min(startIndex + perPage, totalMatching);

      allRows.forEach(row => row.classList.add('d-none'));
      for (let i = startIndex; i < endIndex; i++) {
        if (matchingRows[i]) {
          matchingRows[i].classList.remove('d-none');
        }
      }

      if (paginationBar) {
        const startSpan = paginationBar.querySelector('.pagination-start');
        const endSpan = paginationBar.querySelector('.pagination-end');
        const totalSpan = paginationBar.querySelector('.pagination-total');
        const prevBtn = paginationBar.querySelector('.prev-page');
        const nextBtn = paginationBar.querySelector('.next-page');
        const pagesContainer = paginationBar.querySelector('.pagination-pages');

        if (startSpan) startSpan.textContent = totalMatching > 0 ? startIndex + 1 : 0;
        if (endSpan) endSpan.textContent = endIndex;
        if (totalSpan) totalSpan.textContent = totalMatching;

        if (prevBtn) prevBtn.disabled = (currentPage <= 1);
        if (nextBtn) nextBtn.disabled = (currentPage >= totalPages);

        if (pagesContainer) {
          pagesContainer.innerHTML = '';
          for (let p = 1; p <= totalPages; p++) {
            const pageBtn = document.createElement('button');
            pageBtn.type = 'button';
            pageBtn.className = `page-num ${p === currentPage ? 'active' : ''}`;
            pageBtn.textContent = p;
            pageBtn.addEventListener('click', () => {
              currentPage = p;
              applyTablePagination();
            });
            pagesContainer.appendChild(pageBtn);
          }
        }
      }
    }

    if (searchInput) {
      searchInput.addEventListener('input', () => {
        currentPage = 1;
        applyTablePagination();
      });
    }

    if (statusSelect) {
      statusSelect.addEventListener('change', () => {
        currentPage = 1;
        applyTablePagination();
      });
    }

    if (perPageSelect) {
      perPageSelect.addEventListener('change', () => {
        currentPage = 1;
        applyTablePagination();
      });
    }

    if (paginationBar) {
      const prevBtn = paginationBar.querySelector('.prev-page');
      const nextBtn = paginationBar.querySelector('.next-page');

      if (prevBtn) {
        prevBtn.addEventListener('click', () => {
          if (currentPage > 1) {
            currentPage--;
            applyTablePagination();
          }
        });
      }

      if (nextBtn) {
        nextBtn.addEventListener('click', () => {
          currentPage++;
          applyTablePagination();
        });
      }
    }

    applyTablePagination();
  }

  // 6. Generic Modals Open / Close Logic (for custom dialogs e.g. Bulk Edit, Address)
  function openModal(modalEl) {
    if (modalEl) {
      modalEl.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeModal(modalEl) {
    if (modalEl) {
      modalEl.classList.remove('active');
      document.body.style.overflow = '';
    }
  }

  window.adminOpenModal = openModal;
  window.adminCloseModal = closeModal;

  document.addEventListener('click', (e) => {
    const trigger = e.target.closest('[data-modal-target]');
    if (trigger) {
      const targetSelector = trigger.getAttribute('data-modal-target');
      const targetModal = document.querySelector(targetSelector);
      if (targetModal) openModal(targetModal);
    }

    const closeBtn = e.target.closest('.btn-close-modal');
    if (closeBtn) {
      const modal = closeBtn.closest('.admin-modal-overlay');
      if (modal) closeModal(modal);
    }

    if (e.target.classList.contains('admin-modal-overlay')) {
      closeModal(e.target);
    }
  });

  // 7. Generic Form Submit Notifications
  const adminForms = document.querySelectorAll('.admin-ajax-form');
  adminForms.forEach(form => {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const submitBtn = this.querySelector('button[type="submit"]');
      if (submitBtn) {
        const origText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Saving...';
        setTimeout(() => {
          submitBtn.disabled = false;
          submitBtn.innerHTML = 'Saved Successfully!';
          window.adminToast('Changes saved successfully!', 'success');
          setTimeout(() => {
            submitBtn.innerHTML = origText;
            const parentModal = form.closest('.admin-modal-overlay');
            if (parentModal) closeModal(parentModal);
          }, 1000);
        }, 600);
      }
    });
  });
});
