/**
 * Infinity Vape Shop - Admin Coupons JavaScript
 * Handles creation, editing, active toggles, deletion, and 1-click code copying
 */

document.addEventListener('DOMContentLoaded', () => {
  const modalOverlay = document.getElementById('couponModalOverlay');
  const openAddModalBtn = document.getElementById('btnOpenAddCouponModal');
  const closeBtn = document.getElementById('btnCloseCouponModal');
  const cancelBtn = document.getElementById('btnCancelCouponModal');
  const form = document.getElementById('couponForm');
  const modalTitle = document.getElementById('modalCouponTitle');
  const modalAlert = document.getElementById('modalAlertBox');
  const pageAlert = document.getElementById('couponPageAlert');
  const submitBtn = document.getElementById('btnSubmitCouponForm');
  const submitText = document.getElementById('btnSubmitCouponText');
  const generateBtn = document.getElementById('btnGenerateCode');
  const typeSelect = document.getElementById('formCouponType');
  const valueLabel = document.getElementById('lblCouponValue');
  const valueInput = document.getElementById('formCouponValue');

  // Open Modal Helper
  function openModal(isEdit = false) {
    if (!modalOverlay) return;
    if (modalAlert) {
      modalAlert.style.display = 'none';
      modalAlert.innerHTML = '';
    }
    modalOverlay.style.display = 'flex';
  }

  // Close Modal Helper
  function closeModal() {
    if (!modalOverlay) return;
    modalOverlay.style.display = 'none';
    if (form) form.reset();
    document.getElementById('couponId').value = '';
    updateDiscountTypeUI();
  }

  // Show Page Alert Helper
  function showPageAlert(message, type = 'success') {
    if (!pageAlert) return;
    pageAlert.style.display = 'block';
    if (type === 'success') {
      pageAlert.style.backgroundColor = '#ecfdf5';
      pageAlert.style.color = '#065f46';
      pageAlert.style.border = '1px solid #a7f3d0';
      pageAlert.innerHTML = `<strong>✓ Success:</strong> ${message}`;
    } else {
      pageAlert.style.backgroundColor = '#fef2f2';
      pageAlert.style.color = '#991b1b';
      pageAlert.style.border = '1px solid #fecaca';
      pageAlert.innerHTML = `<strong>✕ Error:</strong> ${message}`;
    }
    setTimeout(() => {
      pageAlert.style.display = 'none';
    }, 4500);
  }

  // Type change UI helper
  function updateDiscountTypeUI() {
    if (!typeSelect || !valueLabel || !valueInput) return;
    const type = typeSelect.value;
    if (type === 'percentage') {
      valueLabel.textContent = 'Discount Value (%) *';
      valueInput.placeholder = 'e.g. 15';
      valueInput.max = '100';
      valueInput.disabled = false;
    } else if (type === 'fixed_amount') {
      valueLabel.textContent = 'Discount Amount ($) *';
      valueInput.placeholder = 'e.g. 10.00';
      valueInput.removeAttribute('max');
      valueInput.disabled = false;
    } else if (type === 'free_shipping') {
      valueLabel.textContent = 'Waived Shipping Cost ($) *';
      valueInput.placeholder = '5.99';
      if (!valueInput.value) valueInput.value = '5.99';
      valueInput.removeAttribute('max');
      valueInput.disabled = false;
    }
  }

  if (typeSelect) {
    typeSelect.addEventListener('change', updateDiscountTypeUI);
  }

  // Open Add Modal
  if (openAddModalBtn) {
    openAddModalBtn.addEventListener('click', () => {
      if (modalTitle) modalTitle.textContent = 'Create New Coupon';
      if (submitText) submitText.textContent = 'Create Coupon';
      if (form) form.reset();
      document.getElementById('couponId').value = '';
      updateDiscountTypeUI();
      openModal(false);
    });
  }

  // Close handlers
  if (closeBtn) closeBtn.addEventListener('click', closeModal);
  if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
  if (modalOverlay) {
    modalOverlay.addEventListener('click', (e) => {
      if (e.target === modalOverlay) closeModal();
    });
  }

  // Code Generator
  if (generateBtn) {
    generateBtn.addEventListener('click', () => {
      const prefixes = ['VAPE', 'SAVE', 'DEAL', 'CLOUD', 'FLAVOR', 'PUFF', 'SUMMER', 'VIP', 'FLASH'];
      const numbers = [10, 15, 20, 25, 30, 50];
      const randomPrefix = prefixes[Math.floor(Math.random() * prefixes.length)];
      const randomNumber = numbers[Math.floor(Math.random() * numbers.length)];
      const randomSuffix = Math.floor(10 + Math.random() * 90);
      const codeInput = document.getElementById('formCouponCode');
      if (codeInput) {
        codeInput.value = `${randomPrefix}${randomNumber}`;
      }
    });
  }

  // Edit Coupon Buttons
  document.querySelectorAll('.btn-edit-coupon').forEach((btn) => {
    btn.addEventListener('click', () => {
      const ds = btn.dataset;
      document.getElementById('couponId').value = ds.id || '';
      document.getElementById('formCouponCode').value = ds.code || '';
      if (typeSelect) typeSelect.value = ds.type || 'percentage';
      if (valueInput) valueInput.value = ds.value || '';
      document.getElementById('formCouponMinOrder').value = ds.min || '';
      document.getElementById('formCouponMaxUses').value = ds.max || '';
      document.getElementById('formCouponStart').value = ds.start || '';
      document.getElementById('formCouponEnd').value = ds.end || '';
      document.getElementById('formCouponActive').checked = ds.active === '1';

      if (modalTitle) modalTitle.textContent = `Edit Coupon: ${ds.code}`;
      if (submitText) submitText.textContent = 'Update Coupon';
      updateDiscountTypeUI();
      openModal(true);
    });
  });

  // Form Submit (Create or Update)
  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      const couponId = document.getElementById('couponId').value;
      const isEdit = Boolean(couponId);
      const url = (window.APP_BASE_URL || '') + (isEdit ? '/admin/coupons/update' : '/admin/coupons/store');

      const payload = {
        id: couponId,
        code: document.getElementById('formCouponCode').value.trim(),
        type: document.getElementById('formCouponType').value,
        value: parseFloat(document.getElementById('formCouponValue').value),
        min_order_amount: parseFloat(document.getElementById('formCouponMinOrder').value) || 0,
        max_uses: parseInt(document.getElementById('formCouponMaxUses').value) || null,
        start_date: document.getElementById('formCouponStart').value || null,
        end_date: document.getElementById('formCouponEnd').value || null,
        is_active: document.getElementById('formCouponActive').checked ? 1 : 0
      };

      if (!submitBtn) return;
      const origText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = `<span>Saving...</span>`;

      try {
        const res = await fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (res.ok && data.success) {
          closeModal();
          showPageAlert(data.message, 'success');
          // Reload page to reflect updated metrics & table
          setTimeout(() => {
            window.location.reload();
          }, 800);
        } else {
          throw new Error(data.message || 'Operation failed.');
        }
      } catch (err) {
        if (modalAlert) {
          modalAlert.style.display = 'block';
          modalAlert.style.backgroundColor = '#fef2f2';
          modalAlert.style.color = '#991b1b';
          modalAlert.style.border = '1px solid #fecaca';
          modalAlert.innerHTML = `<strong>✕ Error:</strong> ${err.message}`;
        }
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = origText;
      }
    });
  }

  // Toggle Active Switch
  document.querySelectorAll('.btn-toggle-coupon').forEach((btn) => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id;
      if (!id) return;

      btn.style.opacity = '0.5';

      try {
        const res = await fetch((window.APP_BASE_URL || '') + '/admin/coupons/toggle', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({ id: id })
        });

        const data = await res.json();
        if (res.ok && data.success) {
          const isActive = data.is_active === 1;
          btn.style.color = isActive ? '#10b981' : '#94a3b8';
          
          const badge = document.getElementById(`badgeStatus-${id}`);
          if (badge) {
            badge.className = `badge-status ${isActive ? 'badge-completed' : 'badge-cancelled'}`;
            badge.textContent = isActive ? 'Active' : 'Inactive';
          }
          showPageAlert(data.message, 'success');
        } else {
          showPageAlert(data.message || 'Could not toggle status', 'error');
        }
      } catch (err) {
        showPageAlert(err.message, 'error');
      } finally {
        btn.style.opacity = '1';
      }
    });
  });

  // Delete Coupon
  document.querySelectorAll('.btn-delete-coupon').forEach((btn) => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id;
      const code = btn.dataset.code || 'this coupon';

      if (!confirm(`Are you sure you want to permanently delete coupon "${code}"? This action cannot be undone.`)) {
        return;
      }

      btn.style.opacity = '0.5';

      try {
        const res = await fetch((window.APP_BASE_URL || '') + '/admin/coupons/delete', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({ id: id })
        });

        const data = await res.json();
        if (res.ok && data.success) {
          showPageAlert(data.message, 'success');
          const row = document.getElementById(`couponRow-${id}`);
          if (row) {
            row.style.transition = 'all 0.3s ease';
            row.style.opacity = '0';
            setTimeout(() => row.remove(), 300);
          }
        } else {
          showPageAlert(data.message || 'Failed to delete coupon', 'error');
        }
      } catch (err) {
        showPageAlert(err.message, 'error');
      } finally {
        btn.style.opacity = '1';
      }
    });
  });

  // 1-Click Copy Code
  document.querySelectorAll('.btn-copy-code').forEach((btn) => {
    btn.addEventListener('click', () => {
      const code = btn.dataset.code;
      if (!code) return;

      navigator.clipboard.writeText(code).then(() => {
        const originalHtml = btn.innerHTML;
        btn.innerHTML = `<span style="font-size:11px; color:#10b981; font-weight:700;">Copied!</span>`;
        setTimeout(() => {
          btn.innerHTML = originalHtml;
        }, 1500);
      }).catch(() => {
        prompt('Copy coupon code:', code);
      });
    });
  });
});

