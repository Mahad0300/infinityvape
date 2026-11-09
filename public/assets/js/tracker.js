/*!
 * E-Commerce Customer Analytics Tracker
 * Non-blocking, passive event listener for Add to Cart, Checkout, and Dwell time
 */
(function () {
  'use strict';

  if (window.__VAPE_TRACKER_LOADED) return;
  window.__VAPE_TRACKER_LOADED = true;

  var path = window.location.pathname || '/';
  if (path.indexOf('/admin') !== -1) return;

  var endpoint = 'api/analytics/event';
  if (window.APP_BASE_URL) {
    endpoint = window.APP_BASE_URL.replace(/\/$/, '') + '/api/analytics/event';
  } else {
    var baseTag = document.querySelector('base');
    if (baseTag && baseTag.getAttribute('href')) {
      endpoint = baseTag.getAttribute('href').replace(/\/$/, '') + '/api/analytics/event';
    } else {
      var origin = window.location.origin;
      var appPath = window.location.pathname.split('/')[1] === 'vapefinalwebsite' ? '/vapefinalwebsite' : '';
      endpoint = origin + appPath + '/api/analytics/event';
    }
  }

  var queue = [];
  var flushTimer = null;

  function flushEvents() {
    if (flushTimer) {
      clearTimeout(flushTimer);
      flushTimer = null;
    }
    if (!queue.length) return;

    var payload = JSON.stringify({ events: queue });
    queue = [];

    // Attempt Beacon API first
    if (typeof navigator.sendBeacon === 'function') {
      try {
        var blob = new Blob([payload], { type: 'application/json' });
        var sent = navigator.sendBeacon(endpoint, blob);
        if (sent) return;
      } catch (e) {}
    }

    // Modern fetch keepalive fallback
    if (typeof fetch === 'function') {
      try {
        fetch(endpoint, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: payload,
          keepalive: true,
          credentials: 'same-origin'
        }).catch(function () {});
        return;
      } catch (e) {}
    }

    // XHR fallback
    try {
      var xhr = new XMLHttpRequest();
      xhr.open('POST', endpoint, true);
      xhr.setRequestHeader('Content-Type', 'application/json');
      xhr.send(payload);
    } catch (e) {}
  }

  function queueEvent(type, name, value) {
    queue.push({
      url: path,
      type: type,
      name: name,
      value: typeof value === 'number' ? value : 1
    });

    if (queue.length >= 3) {
      flushEvents();
    } else if (!flushTimer) {
      flushTimer = setTimeout(flushEvents, 800);
    }
  }

  // 1. Automatic Checkout Reached Trigger
  if (path.indexOf('/checkout') !== -1) {
    queueEvent('checkout_start', 'Reached Checkout Page', 1);
  }

  // 2. Global Event Delegation for Storefront CTAs & Cart
  document.addEventListener('click', function (e) {
    var target = e.target.closest('button, a, input[type="submit"]');
    if (!target) return;

    var text = (target.textContent || target.value || target.getAttribute('aria-label') || '').trim();
    var idLower = (target.id || '').toLowerCase();
    var clsLower = (typeof target.className === 'string' ? target.className : '').toLowerCase();
    var textLower = text.toLowerCase();

    // Add to Cart Buttons (PDP, Quick Add, Cards, FBT bundles)
    if (
      clsLower.indexOf('btn-add-to-cart') !== -1 ||
      clsLower.indexOf('btn-product-add-cart') !== -1 ||
      clsLower.indexOf('add-to-cart') !== -1 ||
      clsLower.indexOf('add-cart') !== -1 ||
      idLower.indexOf('addtocart') !== -1 ||
      textLower.indexOf('add to cart') !== -1 ||
      textLower.indexOf('add selected to cart') !== -1
    ) {
      queueEvent('cart_add', text || 'Add to Cart', 1);
    }

    // Checkout Buttons (Cart Drawer, Cart page)
    if (
      clsLower.indexOf('btn-checkout') !== -1 ||
      idLower.indexOf('checkout') !== -1 ||
      textLower.indexOf('checkout') !== -1
    ) {
      queueEvent('checkout_start', text || 'Initiate Checkout', 1);
    }
  }, { passive: true });

  // Flush on page unload
  window.addEventListener('visibilitychange', function () {
    if (document.visibilityState === 'hidden') {
      flushEvents();
    }
  });
})();

