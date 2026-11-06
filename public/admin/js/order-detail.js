/**
 * Infinity Vape Shop - Admin Order Detail JavaScript
 * Professional order fulfillment, status transitions, and tracking synchronization
 */

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('orderStatusForm');
  const alertBox = document.getElementById('statusUpdateAlert');
  const submitBtn = document.getElementById('btnSaveOrderTracking');

  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    if (!submitBtn) return;
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
      <span style="display:inline-flex; align-items:center; gap:8px;">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" style="animation: spin 1s linear infinite;">
          <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
          <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"></path>
        </svg>
        Saving Changes...
      </span>
    `;

    // Hide any previous alert
    if (alertBox) {
      alertBox.style.display = 'none';
      alertBox.className = '';
    }

    const formData = new FormData(form);
    const payload = {
      order_id: formData.get('order_id'),
      shipping_status: formData.get('shipping_status'),
      payment_status: formData.get('payment_status'),
      shipping_carrier: formData.get('shipping_carrier'),
      tracking_number: formData.get('tracking_number'),
      admin_notes: formData.get('admin_notes')
    };

    const targetUrl = (window.APP_BASE_URL || '') + '/admin/orders/update-status';

    try {
      const response = await fetch(targetUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload)
      });

      const result = await response.json();

      if (response.ok && result.success) {
        // Show success alert
        if (alertBox) {
          alertBox.style.display = 'block';
          alertBox.style.backgroundColor = '#ecfdf5';
          alertBox.style.color = '#065f46';
          alertBox.style.border = '1px solid #a7f3d0';
          alertBox.innerHTML = `<strong>✓ Success:</strong> ${result.message || 'Order status and tracking updated.'}`;
        }

        // Live update header badges
        const data = result.data;

        // 1. Payment Badge
        const paymentBadge = document.getElementById('badgePaymentHeader');
        if (paymentBadge && data.payment_status) {
          const pStatus = data.payment_status.toLowerCase();
          paymentBadge.className = `badge-status badge-${pStatus === 'paid' ? 'paid' : (pStatus === 'refunded' ? 'refunded' : 'pending')}`;
          paymentBadge.textContent = data.payment_status_label || data.payment_status;
        }

        // 2. Shipping Badge
        const shippingBadge = document.getElementById('badgeShippingHeader');
        if (shippingBadge && data.shipping_status) {
          const sStatus = data.shipping_status.toLowerCase();
          let badgeClass = 'pending';
          if (sStatus === 'delivered') badgeClass = 'delivered';
          else if (['shipped', 'in_transit', 'out_for_delivery', 'processing'].includes(sStatus)) badgeClass = 'shipped';
          else if (sStatus === 'cancelled') badgeClass = 'cancelled';
          else if (sStatus === 'confirmed') badgeClass = 'paid';

          shippingBadge.className = `badge-status badge-${badgeClass}`;
          shippingBadge.textContent = data.shipping_status_label || data.shipping_status;
        }

        // 3. Fulfillment Badges
        const fulfillBadgeHeader = document.getElementById('badgeFulfillmentHeader');
        const fulfillBadgeCard = document.getElementById('cardFulfillmentBadge');
        if (data.fulfillment_status) {
          const isFulfilled = data.fulfillment_status.toLowerCase() === 'fulfilled';
          const fClass = `badge-status badge-${isFulfilled ? 'fulfilled' : 'unfulfilled'}`;
          if (fulfillBadgeHeader) {
            fulfillBadgeHeader.className = fClass;
            fulfillBadgeHeader.textContent = data.fulfillment_status;
          }
          if (fulfillBadgeCard) {
            fulfillBadgeCard.className = fClass;
            fulfillBadgeCard.textContent = data.fulfillment_status;
          }
        }

        // Auto-dismiss alert after 5 seconds
        setTimeout(() => {
          if (alertBox) {
            alertBox.style.display = 'none';
          }
        }, 5000);

      } else {
        throw new Error(result.message || 'Failed to update order status.');
      }
    } catch (err) {
      if (alertBox) {
        alertBox.style.display = 'block';
        alertBox.style.backgroundColor = '#fef2f2';
        alertBox.style.color = '#991b1b';
        alertBox.style.border = '1px solid #fecaca';
        alertBox.innerHTML = `<strong>✕ Error:</strong> ${err.message || 'Failed to update order.'}`;
      }
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    }
  });
});

// Keyframe animation for spinner
if (!document.getElementById('admin-order-detail-style')) {
  const style = document.createElement('style');
  style.id = 'admin-order-detail-style';
  style.textContent = `
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  `;
  document.head.appendChild(style);
}
