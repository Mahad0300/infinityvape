/**
 * New Vape - Blog Categories Management JavaScript
 * Handles Add/Edit category modal, auto-slug generation, and dynamic action switching.
 */

window.openCategoryModal = function(cat = null) {
  const modal = document.getElementById('categoryModal');
  const form = document.getElementById('categoryForm');
  const title = document.getElementById('modalTitle');
  const idInput = document.getElementById('catId');
  const nameInput = document.getElementById('catName');
  const slugInput = document.getElementById('catSlug');
  const descInput = document.getElementById('catDesc');
  const submitBtn = document.getElementById('catSubmitBtn');

  if (!modal || !form) return;

  const storeUrl = form.dataset.storeUrl || form.action.replace(/update\/?$/, 'store');
  const updateUrl = form.dataset.updateUrl || form.action.replace(/store\/?$/, 'update');

  if (cat) {
    if (title) title.textContent = 'Edit Category: ' + (cat.name || '');
    form.action = updateUrl;
    if (idInput) idInput.value = cat.id || '';
    if (nameInput) nameInput.value = cat.name || '';
    if (slugInput) slugInput.value = cat.slug || '';
    if (descInput) descInput.value = cat.description || '';
    if (submitBtn) submitBtn.textContent = 'Update Category';
  } else {
    if (title) title.textContent = 'Add Category';
    form.action = storeUrl;
    if (idInput) idInput.value = '';
    if (nameInput) nameInput.value = '';
    if (slugInput) slugInput.value = '';
    if (descInput) descInput.value = '';
    if (submitBtn) submitBtn.textContent = 'Save Category';
  }

  modal.style.display = 'flex';
};

window.closeCategoryModal = function() {
  const modal = document.getElementById('categoryModal');
  if (modal) modal.style.display = 'none';
};

document.addEventListener('DOMContentLoaded', () => {
  const catNameInput = document.getElementById('catName');
  const catSlugInput = document.getElementById('catSlug');
  const catIdInput = document.getElementById('catId');

  if (catNameInput && catSlugInput) {
    catNameInput.addEventListener('input', function() {
      if ((!catIdInput || !catIdInput.value) && !catSlugInput.dataset.manual) {
        catSlugInput.value = this.value.toLowerCase()
          .replace(/[^a-z0-9]+/g, '-')
          .replace(/^-+|-+$/g, '');
      }
    });

    catSlugInput.addEventListener('input', function() {
      this.dataset.manual = 'true';
    });
  }
});
