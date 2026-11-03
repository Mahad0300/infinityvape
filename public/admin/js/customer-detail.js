/**
 * New Vape - Customer Detail JavaScript
 * Handles customer notes, asynchronous saving, and feedback
 */

document.addEventListener('DOMContentLoaded', () => {
  const noteForm = document.getElementById('customerNoteForm');
  if (noteForm) {
    noteForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const textarea = noteForm.querySelector('textarea[name="admin_notes"]');
      const customerIdInput = noteForm.querySelector('input[name="customer_id"]');
      const submitBtn = document.getElementById('saveNoteBtn');
      const statusMsg = document.getElementById('noteStatusMsg');

      if (!customerIdInput || !customerIdInput.value) return;

      const customerId = customerIdInput.value;
      const notes = textarea ? textarea.value : '';

      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving Note...';
      }

      try {
        const tokenInput = noteForm.querySelector('input[name="_token"]');
        const formData = new FormData();
        formData.append('customer_id', customerId);
        formData.append('admin_notes', notes);
        if (tokenInput && tokenInput.value) {
          formData.append('_token', tokenInput.value);
        }

        const targetUrl = window.location.pathname.replace(/\/admin\/customer-detail.*$/, '/admin/customer/update-note');

        const response = await fetch(targetUrl, {
          method: 'POST',
          body: formData
        });

        const data = await response.json();

        if (submitBtn) {
          submitBtn.textContent = '✓ Note Saved!';
          setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Save Note';
          }, 2000);
        }

        if (statusMsg) {
          statusMsg.style.display = 'block';
          statusMsg.className = 'mb-sm text-sm text-success';
          statusMsg.textContent = data.message || 'Note updated successfully.';
          setTimeout(() => {
            statusMsg.style.display = 'none';
          }, 3000);
        }
      } catch (err) {
        console.error('Error saving note:', err);
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Save Note';
        }
        if (statusMsg) {
          statusMsg.style.display = 'block';
          statusMsg.className = 'mb-sm text-sm text-danger';
          statusMsg.textContent = 'Could not save note. Please try again.';
        }
      }
    });
  }
});
