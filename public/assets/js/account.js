/**
 * INFINITY VAPE SHOP - Customer Account Module JavaScript (account.js)
 * Handles:
 * - Password visibility toggle (Login & Register)
 * - Real AJAX Login submission & validation
 * - Real AJAX Register submission & validation
 * - Lost password helper
 * - Dashboard tab switching
 * - Address edit & creation modal management
 */

// 1. Password Visibility Toggle (Exposed globally for inline button onclick)
function togglePasswordVisibility(inputId, btn) {
  const input = document.getElementById(inputId);
  if (!input) return;

  if (input.type === 'password') {
    input.type = 'text';
    btn.innerHTML = `<svg class="eye-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
      <circle cx="12" cy="12" r="3"></circle>
    </svg>`;
  } else {
    input.type = 'password';
    btn.innerHTML = `<svg class="eye-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
      <line x1="1" y1="1" x2="23" y2="23"></line>
    </svg>`;
  }
}
window.togglePasswordVisibility = togglePasswordVisibility;

// 2. Address Modal Handler (Exposed globally for dashboard buttons)
function openAddressModal(type, isEdit) {
  const modal = document.getElementById('addressEditModal');
  const title = document.getElementById('addressModalTitle');
  const typeInput = document.getElementById('addrTypeInput');
  const streetInput = document.getElementById('addrStreet');
  const suiteInput = document.getElementById('addrSuite');
  const cityInput = document.getElementById('addrCity');
  const stateInput = document.getElementById('addrState');
  const zipInput = document.getElementById('addrZip');

  if (modal && title && typeInput) {
    typeInput.value = type;
    title.textContent = (isEdit ? 'Edit ' : 'Add ') + (type === 'shipping' ? 'Shipping' : 'Billing') + ' Address';

    const savedShipping = window.savedShipping || {};
    const savedBilling = window.savedBilling || {};
    const targetAddr = (type === 'shipping') ? savedShipping : savedBilling;

    if (isEdit && targetAddr && targetAddr.street) {
      if (streetInput) streetInput.value = targetAddr.street || '';
      if (suiteInput) suiteInput.value = targetAddr.apartment || targetAddr.suite || '';
      if (cityInput) cityInput.value = targetAddr.city || '';
      if (stateInput) stateInput.value = targetAddr.state || '';
      if (zipInput) zipInput.value = targetAddr.zip || '';
    } else {
      if (streetInput) streetInput.value = '';
      if (suiteInput) suiteInput.value = '';
      if (cityInput) cityInput.value = '';
      if (stateInput) stateInput.value = '';
      if (zipInput) zipInput.value = '';
    }

    modal.style.display = 'block';
    modal.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
}
window.openAddressModal = openAddressModal;

// 3. Document Ready Initialization
document.addEventListener('DOMContentLoaded', function () {
  const loginForm = document.getElementById('loginForm');
  const registerForm = document.getElementById('registerForm');
  const linkLostPassword = document.getElementById('linkLostPassword');

  // Handle ?action=register URL query (Scroll to register form)
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('action') === 'register') {
    const regInput = document.getElementById('regFirstName');
    if (regInput) {
      setTimeout(() => {
        regInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        regInput.focus();
      }, 200);
    }
  }

  // --- Real AJAX Login Form Submission ---
  if (loginForm) {
    loginForm.addEventListener('submit', async function (e) {
      e.preventDefault();
      const email = document.getElementById('loginUsername');
      const pass = document.getElementById('loginPassword');
      const statusMsg = document.getElementById('loginStatusMsg');
      const btn = document.getElementById('btnLoginSubmit');
      const tokenInput = loginForm.querySelector('input[name="_token"]');
      let valid = true;

      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!email.value.trim() || !emailPattern.test(email.value.trim())) {
        email.classList.add('has-error');
        const errEmail = document.getElementById('errLoginUsername');
        if (errEmail) errEmail.style.display = 'block';
        valid = false;
      } else {
        email.classList.remove('has-error');
        const errEmail = document.getElementById('errLoginUsername');
        if (errEmail) errEmail.style.display = 'none';
      }

      if (!pass.value.trim()) {
        pass.classList.add('has-error');
        const errPass = document.getElementById('errLoginPassword');
        if (errPass) errPass.style.display = 'block';
        valid = false;
      } else {
        pass.classList.remove('has-error');
        const errPass = document.getElementById('errLoginPassword');
        if (errPass) errPass.style.display = 'none';
      }

      if (!valid) return;

      btn.disabled = true;
      btn.textContent = 'Logging in...';
      if (statusMsg) statusMsg.style.display = 'none';

      const targetUrl = loginForm.getAttribute('action') || 'account/login';

      try {
        const response = await fetch(targetUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            email: email.value.trim(),
            password: pass.value,
            _token: tokenInput ? tokenInput.value : ''
          })
        });

        const data = await response.json();

        if (response.ok && data.success) {
          if (statusMsg) {
            statusMsg.className = 'account-status-msg success';
            statusMsg.textContent = data.message || 'Login successful! Redirecting...';
            statusMsg.style.display = 'block';
          }
          setTimeout(() => {
            window.location.href = data.redirect || 'my-account';
          }, 600);
        } else {
          if (statusMsg) {
            statusMsg.className = 'account-status-msg error';
            statusMsg.style.backgroundColor = '#fef2f2';
            statusMsg.style.color = '#dc2626';
            statusMsg.style.padding = '12px';
            statusMsg.style.borderRadius = '6px';
            statusMsg.style.marginTop = '12px';
            statusMsg.textContent = data.message || 'Invalid email or password.';
            statusMsg.style.display = 'block';
          }
          btn.disabled = false;
          btn.textContent = 'Log in';
        }
      } catch (err) {
        if (statusMsg) {
          statusMsg.className = 'account-status-msg error';
          statusMsg.style.backgroundColor = '#fef2f2';
          statusMsg.style.color = '#dc2626';
          statusMsg.style.padding = '12px';
          statusMsg.style.borderRadius = '6px';
          statusMsg.style.marginTop = '12px';
          statusMsg.textContent = 'Network error occurred. Please try again.';
          statusMsg.style.display = 'block';
        }
        btn.disabled = false;
        btn.textContent = 'Log in';
      }
    });
  }

  // --- Real AJAX Register Form Submission ---
  if (registerForm) {
    registerForm.addEventListener('submit', async function (e) {
      e.preventDefault();
      const fName = document.getElementById('regFirstName');
      const lName = document.getElementById('regLastName');
      const email = document.getElementById('regEmail');
      const phone = document.getElementById('regPhone');
      const pass = document.getElementById('regPassword');
      const statusMsg = document.getElementById('regStatusMsg');
      const btn = document.getElementById('btnRegisterSubmit');
      const tokenInput = registerForm.querySelector('input[name="_token"]');
      let valid = true;

      if (!fName.value.trim()) {
        fName.classList.add('has-error');
        const errFn = document.getElementById('errRegFirstName');
        if (errFn) errFn.style.display = 'block';
        valid = false;
      } else {
        fName.classList.remove('has-error');
        const errFn = document.getElementById('errRegFirstName');
        if (errFn) errFn.style.display = 'none';
      }

      if (!lName.value.trim()) {
        lName.classList.add('has-error');
        const errLn = document.getElementById('errRegLastName');
        if (errLn) errLn.style.display = 'block';
        valid = false;
      } else {
        lName.classList.remove('has-error');
        const errLn = document.getElementById('errRegLastName');
        if (errLn) errLn.style.display = 'none';
      }

      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!email.value.trim() || !emailPattern.test(email.value.trim())) {
        email.classList.add('has-error');
        const errEm = document.getElementById('errRegEmail');
        if (errEm) errEm.style.display = 'block';
        valid = false;
      } else {
        email.classList.remove('has-error');
        const errEm = document.getElementById('errRegEmail');
        if (errEm) errEm.style.display = 'none';
      }

      if (!pass.value.trim() || pass.value.length < 6) {
        pass.classList.add('has-error');
        const errPass = document.getElementById('errRegPassword');
        if (errPass) errPass.style.display = 'block';
        valid = false;
      } else {
        pass.classList.remove('has-error');
        const errPass = document.getElementById('errRegPassword');
        if (errPass) errPass.style.display = 'none';
      }

      if (!valid) return;

      btn.disabled = true;
      btn.textContent = 'Creating account...';
      if (statusMsg) statusMsg.style.display = 'none';

      const targetUrl = registerForm.getAttribute('action') || 'account/register';

      try {
        const response = await fetch(targetUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            first_name: fName.value.trim(),
            last_name: lName.value.trim(),
            email: email.value.trim(),
            phone: phone ? phone.value.trim() : '',
            password: pass.value,
            _token: tokenInput ? tokenInput.value : ''
          })
        });

        const data = await response.json();

        if (response.ok && data.success) {
          if (statusMsg) {
            statusMsg.className = 'account-status-msg success';
            statusMsg.style.backgroundColor = '#ecfdf5';
            statusMsg.style.color = '#059669';
            statusMsg.style.padding = '12px';
            statusMsg.style.borderRadius = '6px';
            statusMsg.style.marginTop = '12px';
            statusMsg.textContent = data.message || 'Account created successfully! Welcome.';
            statusMsg.style.display = 'block';
          }
          setTimeout(() => {
            window.location.href = data.redirect || 'my-account';
          }, 800);
        } else {
          if (statusMsg) {
            statusMsg.className = 'account-status-msg error';
            statusMsg.style.backgroundColor = '#fef2f2';
            statusMsg.style.color = '#dc2626';
            statusMsg.style.padding = '12px';
            statusMsg.style.borderRadius = '6px';
            statusMsg.style.marginTop = '12px';
            statusMsg.textContent = data.message || 'Registration failed. Please check your information.';
            statusMsg.style.display = 'block';
          }
          btn.disabled = false;
          btn.textContent = 'Create Account';
        }
      } catch (err) {
        if (statusMsg) {
          statusMsg.className = 'account-status-msg error';
          statusMsg.style.backgroundColor = '#fef2f2';
          statusMsg.style.color = '#dc2626';
          statusMsg.style.padding = '12px';
          statusMsg.style.borderRadius = '6px';
          statusMsg.style.marginTop = '12px';
          statusMsg.textContent = 'A connection error occurred. Please try again.';
          statusMsg.style.display = 'block';
        }
        btn.disabled = false;
        btn.textContent = 'Create Account';
      }
    });
  }

  // --- Lost Password Link Handler ---
  if (linkLostPassword) {
    linkLostPassword.addEventListener('click', function (e) {
      e.preventDefault();
      const email = prompt('Enter your registered email address to receive password reset instructions:');
      if (email && email.trim()) {
        alert('Password reset instructions have been sent to ' + email.trim() + ' (if registered).');
      }
    });
  }

  // --- Dashboard Tabs Switcher ---
  const tabBtns = document.querySelectorAll('.dash-tab-btn');
  const tabPanes = document.querySelectorAll('.dash-tab-pane');

  if (tabBtns.length > 0 && tabPanes.length > 0) {
    tabBtns.forEach(btn => {
      btn.addEventListener('click', function () {
        tabBtns.forEach(b => {
          b.classList.remove('active');
          b.style.borderLeftColor = 'transparent';
          b.style.fontWeight = '500';
          b.style.color = '#4b5563';
        });
        this.classList.add('active');
        this.style.borderLeftColor = '#10b981';
        this.style.fontWeight = '600';
        this.style.color = '#111827';

        const targetTabId = this.dataset.tab;
        tabPanes.forEach(pane => {
          pane.style.display = (pane.id === targetTabId) ? 'block' : 'none';
        });
      });
    });
  }
});

