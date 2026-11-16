document.addEventListener('DOMContentLoaded', function () {
  var grid = document.getElementById('categoryProductGrid');
  if (!grid) return;

  grid.addEventListener('click', function (e) {
    var btn = e.target.closest('.btn-card-add-cart');
    if (!btn) return;

    e.preventDefault();
    var card = btn.closest('.category-product-card');
    var title = btn.dataset.title || 'Vape Product';
    var price = parseFloat(btn.dataset.price) || 0;
    var img = card ? card.querySelector('.card-product-img')?.getAttribute('src') : '';
    var cart = window.InfinityCart || window.NewVapeCart;

    if (cart) {
      cart.add({
        id: btn.dataset.id || title,
        title: title,
        price: price,
        quantity: 1,
        flavor: '',
        image: img
      });
    }

    var origText = btn.textContent;
    btn.classList.add('added');
    btn.textContent = 'ADDED ✓';
    setTimeout(function () {
      btn.classList.remove('added');
      btn.textContent = origText;
    }, 1600);
  });
});
