/**
 * New Vape - Navigation & Menu Builder JavaScript
 * Handles hierarchical tree rendering, 3-level nesting, drag-and-drop, modals, and saving
 */

document.addEventListener('DOMContentLoaded', () => {
  const treeContainer = document.getElementById('menuItemsTree');
  const modal = document.getElementById('menuItemModal');
  const openModalBtn = document.getElementById('openAddItemModalBtn');
  const closeModalBtn = document.getElementById('closeItemModalBtn');
  const cancelModalBtn = document.getElementById('cancelItemModalBtn');
  const saveItemBtn = document.getElementById('saveItemModalBtn');
  const saveMenuBtn = document.getElementById('saveMenuBtn');
  const modalTitle = document.getElementById('itemModalTitle');
  const toast = document.getElementById('builderToast');

  // Form elements
  const editingIdInput = document.getElementById('editingItemId');
  const nameInput = document.getElementById('itemNameInput');
  const linkTypeSelect = document.getElementById('itemLinkTypeSelect');
  const customUrlInput = document.getElementById('itemCustomUrlInput');
  const collectionSelect = document.getElementById('itemCollectionSelect');
  const brandSelect = document.getElementById('itemBrandSelect');
  const pageSelect = document.getElementById('itemPageSelect');
  const parentSelect = document.getElementById('itemParentSelect');

  const groupCustomUrl = document.getElementById('groupCustomUrl');
  const groupCollection = document.getElementById('groupCollectionSelect');
  const groupBrand = document.getElementById('groupBrandSelect');
  const groupPage = document.getElementById('groupPageSelect');
  const groupPromoBanner = document.getElementById('groupPromoBanner');

  const promoLinkInput = document.getElementById('itemPromoLinkInput');
  const promoTextInput = document.getElementById('itemPromoTextInput');
  const promoImageUrlInput = document.getElementById('itemPromoImageUrlInput');
  const promoFileInput = document.getElementById('promoImageFileInput');
  const promoPreviewWrap = document.getElementById('promoImagePreviewWrap');
  const promoPreviewImg = document.getElementById('promoImagePreview');

  // Menu Settings elements
  const menuId = document.getElementById('menuIdInput') ? document.getElementById('menuIdInput').value : 1;
  const menuTitleInput = document.getElementById('menuTitleInput');
  const menuHandleInput = document.getElementById('menuHandleInput');
  const pageTitleHeading = document.getElementById('pageTitleHeading');

  // Flat list of items
  let rawItems = window.initialMenuItems || [];
  let nextTempId = 10000;

  // Convert raw items into hierarchical tree
  function buildTreeFromFlat(items) {
    const idMap = {};
    const tree = [];

    // Clone items
    items.forEach(it => {
      idMap[it.id] = {
        ...it,
        id: it.id,
        title: it.title || it.name || 'Menu Item',
        subtitle: it.subtitle || (it.link_type !== 'custom_url' ? it.link_type : ''),
        link_type: it.link_type || 'custom_url',
        link_value: it.link_value || '#',
        image_url: it.image_url || '',
        entity_id: it.entity_id || null,
        parent_id: it.parent_id ? parseInt(it.parent_id, 10) : null,
        children: []
      };
    });

    items.forEach(it => {
      const node = idMap[it.id];
      if (node.parent_id && idMap[node.parent_id]) {
        idMap[node.parent_id].children.push(node);
      } else {
        tree.push(node);
      }
    });

    return tree;
  }

  let menuTree = buildTreeFromFlat(rawItems);

  // Flatten tree back to list with level metadata for rendering and parent options
  function flattenTreeWithLevels(nodes, level = 1, parentId = null) {
    let result = [];
    nodes.forEach(node => {
      node.level = level;
      node.parent_id = parentId;
      result.push(node);
      if (node.children && node.children.length > 0) {
        result = result.concat(flattenTreeWithLevels(node.children, level + 1, node.id));
      }
    });
    return result;
  }

  // Render the Tree
  function renderTree() {
    if (!treeContainer) return;
    treeContainer.innerHTML = '';

    const flat = flattenTreeWithLevels(menuTree);

    if (flat.length === 0) {
      treeContainer.innerHTML = `
        <div style="text-align: center; padding: 36px 16px; color: #94a3b8; font-size: 14px;">
          No menu items added yet. Click "+ Add menu item" below to get started.
        </div>
      `;
      return;
    }

    flat.forEach((item, index) => {
      const nodeEl = document.createElement('div');
      nodeEl.className = `menu-item-node level-${item.level}`;
      nodeEl.dataset.id = item.id;
      nodeEl.dataset.index = index;

      // Tree connector guides for nested items
      let connectorHtml = '';
      if (item.level === 2) {
        connectorHtml = '<div class="tree-connector-guide"></div>';
      } else if (item.level === 3) {
        connectorHtml = '<div class="tree-connector-guide"></div><div class="tree-vertical-line" style="left: -62px;"></div>';
      }

      // Format subtitle / type badge display text (exact match to reference screenshot)
      let subtext = '';
      if (item.link_type === 'promo_banner') {
        subtext = 'promo_banner';
      } else if (item.link_type === 'mega_menu_column') {
        subtext = 'mega_menu_column';
      } else if (item.link_type === 'collection') {
        subtext = 'collection';
      } else if (item.link_type === 'brand') {
        subtext = 'brand';
      } else if (item.link_type === 'page') {
        subtext = 'page';
      } else if (item.link_type === 'text_block') {
        subtext = 'text_block';
      } else if (item.link_type === 'custom_url') {
        subtext = item.link_value && item.link_value !== '#' ? item.link_value : '';
      } else {
        subtext = item.subtitle || '';
      }

      nodeEl.innerHTML = `
        ${connectorHtml}
        <div class="menu-item-box" draggable="true">
          <div class="menu-item-left">
            <span class="menu-item-drag-handle" title="Drag to reorder or nest">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                <circle cx="9" cy="6" r="1.5"></circle>
                <circle cx="15" cy="6" r="1.5"></circle>
                <circle cx="9" cy="12" r="1.5"></circle>
                <circle cx="15" cy="12" r="1.5"></circle>
                <circle cx="9" cy="18" r="1.5"></circle>
                <circle cx="15" cy="18" r="1.5"></circle>
              </svg>
            </span>
            <div class="menu-item-content">
              <span class="menu-item-title">${escapeHtml(item.title)}</span>
              ${subtext ? `<span class="menu-item-subtext">${escapeHtml(subtext)}</span>` : ''}
            </div>
          </div>
          <div class="menu-item-actions">
            <button type="button" class="btn-item-action edit-item" title="Edit Item" data-id="${item.id}">
              <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
              </svg>
            </button>
            <button type="button" class="btn-item-action delete-item" title="Delete Item" data-id="${item.id}">
              <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
              </svg>
            </button>
          </div>
        </div>
      `;

      // Event handlers for action buttons
      nodeEl.querySelector('.edit-item').addEventListener('click', () => openEditModal(item.id));
      nodeEl.querySelector('.delete-item').addEventListener('click', () => deleteItem(item.id));

      // Drag and drop handlers
      const box = nodeEl.querySelector('.menu-item-box');
      box.addEventListener('dragstart', (e) => handleDragStart(e, item.id));
      box.addEventListener('dragover', (e) => handleDragOver(e, item.id));
      box.addEventListener('dragleave', handleDragLeave);
      box.addEventListener('drop', (e) => handleDrop(e, item.id));
      box.addEventListener('dragend', handleDragEnd);

      treeContainer.appendChild(nodeEl);
    });
  }

  // Drag and Drop Logic
  let draggedItemId = null;

  function handleDragStart(e, id) {
    draggedItemId = id;
    e.currentTarget.classList.add('is-dragging');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', id);
  }

  function handleDragOver(e, targetId) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    const box = e.currentTarget;
    box.style.borderColor = '#5c56e8';
  }

  function handleDragLeave(e) {
    e.currentTarget.style.borderColor = '#e2e8f0';
  }

  function handleDrop(e, targetId) {
    e.preventDefault();
    e.currentTarget.style.borderColor = '#e2e8f0';

    if (draggedItemId === targetId) return;

    // Determine drop position: before or nest under
    const rect = e.currentTarget.getBoundingClientRect();
    const offsetY = e.clientY - rect.top;
    const isBottomHalf = offsetY > rect.height / 2;

    reorderItems(draggedItemId, targetId, isBottomHalf);
  }

  function handleDragEnd(e) {
    e.currentTarget.classList.remove('is-dragging');
    document.querySelectorAll('.menu-item-box').forEach(b => (b.style.borderColor = '#e2e8f0'));
  }

  function findItemAndRemove(tree, id) {
    for (let i = 0; i < tree.length; i++) {
      if (tree[i].id == id) {
        return tree.splice(i, 1)[0];
      }
      if (tree[i].children && tree[i].children.length > 0) {
        const found = findItemAndRemove(tree[i].children, id);
        if (found) return found;
      }
    }
    return null;
  }

  function reorderItems(draggedId, targetId, isAfter) {
    const item = findItemAndRemove(menuTree, draggedId);
    if (!item) return;

    // Helper to insert near target
    function insertNear(tree) {
      for (let i = 0; i < tree.length; i++) {
        if (tree[i].id == targetId) {
          const insertIdx = isAfter ? i + 1 : i;
          tree.splice(insertIdx, 0, item);
          return true;
        }
        if (tree[i].children && tree[i].children.length > 0) {
          if (insertNear(tree[i].children)) return true;
        }
      }
      return false;
    }

    insertNear(menuTree);
    renderTree();
  }

  // Populate Parent Item Select in Modal
  function updateParentSelect(excludeId = null) {
    if (!parentSelect) return;
    parentSelect.innerHTML = '<option value="">None (Top Level Item)</option>';

    const flat = flattenTreeWithLevels(menuTree);
    flat.forEach(item => {
      // Allow only level 1 and level 2 items as parents (max 3 levels)
      if (item.level < 3 && item.id != excludeId) {
        const prefix = item.level === 1 ? '↳ ' : '    ↳↳ ';
        const opt = document.createElement('option');
        opt.value = item.id;
        opt.textContent = `${prefix}${item.title}`;
        parentSelect.appendChild(opt);
      }
    });
  }

  // Link Type Selector Change
  function updateLinkTypeView() {
    const val = linkTypeSelect ? linkTypeSelect.value : 'custom_url';

    if (groupCustomUrl) groupCustomUrl.style.display = val === 'custom_url' ? 'block' : 'none';
    if (groupCollection) groupCollection.style.display = val === 'collection' ? 'block' : 'none';
    if (groupBrand) groupBrand.style.display = val === 'brand' ? 'block' : 'none';
    if (groupPage) groupPage.style.display = val === 'page' ? 'block' : 'none';
    if (groupPromoBanner) groupPromoBanner.style.display = val === 'promo_banner' ? 'block' : 'none';
  }

  if (linkTypeSelect) {
    linkTypeSelect.addEventListener('change', updateLinkTypeView);
  }

  // Auto-fill Name when selecting entity from dropdown
  if (collectionSelect) {
    collectionSelect.addEventListener('change', () => {
      const selected = collectionSelect.options[collectionSelect.selectedIndex];
      if (selected && selected.value) {
        if (!nameInput.value.trim()) nameInput.value = selected.dataset.title || selected.text;
      }
    });
  }

  if (brandSelect) {
    brandSelect.addEventListener('change', () => {
      const selected = brandSelect.options[brandSelect.selectedIndex];
      if (selected && selected.value) {
        if (!nameInput.value.trim()) nameInput.value = selected.dataset.title || selected.text;
      }
    });
  }

  if (pageSelect) {
    pageSelect.addEventListener('change', () => {
      const selected = pageSelect.options[pageSelect.selectedIndex];
      if (selected && selected.value) {
        nameInput.value = selected.dataset.title || selected.text.trim();
      }
    });
  }

  // Promo Banner File Upload Listener
  if (promoFileInput) {
    promoFileInput.addEventListener('change', async () => {
      if (!promoFileInput.files || !promoFileInput.files[0]) return;
      const file = promoFileInput.files[0];
      const formData = new FormData();
      formData.append('banner_image', file);

      try {
        const res = await fetch(window.uploadBannerEndpoint, {
          method: 'POST',
          body: formData
        });
        const data = await res.json();
        if (data.success && data.url) {
          if (promoImageUrlInput) promoImageUrlInput.value = data.url;
          if (promoPreviewImg && promoPreviewWrap) {
            promoPreviewImg.src = data.url;
            promoPreviewWrap.style.display = 'block';
          }
        } else {
          alert(data.message || 'Image upload failed.');
        }
      } catch (err) {
        console.error('Promo banner upload error:', err);
      }
    });
  }

  if (promoImageUrlInput) {
    promoImageUrlInput.addEventListener('input', () => {
      const url = promoImageUrlInput.value.trim();
      if (url && promoPreviewImg && promoPreviewWrap) {
        promoPreviewImg.src = url;
        promoPreviewWrap.style.display = 'block';
      } else if (promoPreviewWrap) {
        promoPreviewWrap.style.display = 'none';
      }
    });
  }

  // Modal Open / Close
  function openAddModal() {
    if (modalTitle) modalTitle.textContent = 'Add Menu Item';
    if (editingIdInput) editingIdInput.value = '';
    if (nameInput) nameInput.value = '';
    if (linkTypeSelect) linkTypeSelect.value = 'custom_url';
    if (customUrlInput) customUrlInput.value = '/';
    if (collectionSelect) collectionSelect.value = '';
    if (brandSelect) brandSelect.value = '';
    if (pageSelect) pageSelect.value = '';
    if (promoLinkInput) promoLinkInput.value = '';
    if (promoTextInput) promoTextInput.value = '';
    if (promoImageUrlInput) promoImageUrlInput.value = '';
    if (promoPreviewWrap) promoPreviewWrap.style.display = 'none';
    if (saveItemBtn) saveItemBtn.textContent = 'Add Item';

    updateParentSelect();
    updateLinkTypeView();

    if (modal) modal.classList.add('is-active');
    if (nameInput) nameInput.focus();
  }

  function findItemById(tree, id) {
    for (const node of tree) {
      if (node.id == id) return node;
      if (node.children && node.children.length > 0) {
        const found = findItemById(node.children, id);
        if (found) return found;
      }
    }
    return null;
  }

  function openEditModal(id) {
    const item = findItemById(menuTree, id);
    if (!item) return;

    if (modalTitle) modalTitle.textContent = 'Edit Menu Item';
    if (editingIdInput) editingIdInput.value = item.id;
    if (nameInput) nameInput.value = item.title;
    if (linkTypeSelect) linkTypeSelect.value = item.link_type;

    updateParentSelect(item.id);
    if (parentSelect) parentSelect.value = item.parent_id || '';

    updateLinkTypeView();

    if (item.link_type === 'promo_banner') {
      if (promoLinkInput) promoLinkInput.value = item.link_value || '';
      if (promoTextInput) promoTextInput.value = item.subtitle || '';
      if (promoImageUrlInput) promoImageUrlInput.value = item.image_url || '';
      if (item.image_url && promoPreviewImg && promoPreviewWrap) {
        promoPreviewImg.src = item.image_url;
        promoPreviewWrap.style.display = 'block';
      } else if (promoPreviewWrap) {
        promoPreviewWrap.style.display = 'none';
      }
    } else if (item.link_type === 'custom_url') {
      if (customUrlInput) customUrlInput.value = item.link_value || '';
    } else if (item.link_type === 'collection' && collectionSelect) {
      collectionSelect.value = item.entity_id || '';
    } else if (item.link_type === 'brand' && brandSelect) {
      brandSelect.value = item.entity_id || '';
    } else if (item.link_type === 'page' && pageSelect) {
      if (item.entity_id) {
        pageSelect.value = item.entity_id;
      } else if (item.link_value) {
        const slug = item.link_value.replace(/^pages?\//, '').replace(/^\//, '');
        for (let i = 0; i < pageSelect.options.length; i++) {
          if (pageSelect.options[i].dataset.slug === slug) {
            pageSelect.selectedIndex = i;
            break;
          }
        }
      }
    }

    if (saveItemBtn) saveItemBtn.textContent = 'Update Item';
    if (modal) modal.classList.add('is-active');
    if (nameInput) nameInput.focus();
  }

  function closeModal() {
    if (modal) modal.classList.remove('is-active');
  }

  document.querySelectorAll('.open-add-item-modal-btn').forEach(btn => {
    btn.addEventListener('click', openAddModal);
  });
  if (openModalBtn) openModalBtn.addEventListener('click', openAddModal);
  if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
  if (cancelModalBtn) cancelModalBtn.addEventListener('click', closeModal);

  if (modal) {
    modal.addEventListener('click', (e) => {
      if (e.target === modal) closeModal();
    });
  }

  // Save Item from Modal
  if (saveItemBtn) {
    saveItemBtn.addEventListener('click', () => {
      const name = nameInput.value.trim();
      if (!name) {
        alert('Please enter a name for the menu item.');
        nameInput.focus();
        return;
      }

      const editingId = editingIdInput.value;
      const linkType = linkTypeSelect.value;
      const parentId = parentSelect.value ? parseInt(parentSelect.value, 10) : null;

      let linkValue = '#';
      let entityId = null;
      let subtitle = linkType !== 'custom_url' ? linkType : '';
      let imageUrl = '';

      if (linkType === 'promo_banner') {
        linkValue = promoLinkInput ? promoLinkInput.value.trim() : '#';
        subtitle = promoTextInput && promoTextInput.value.trim() ? promoTextInput.value.trim() : 'promo_banner';
        imageUrl = promoImageUrlInput ? promoImageUrlInput.value.trim() : '';
      } else if (linkType === 'custom_url') {
        linkValue = customUrlInput.value.trim() || '#';
      } else if (linkType === 'collection') {
        const opt = collectionSelect.options[collectionSelect.selectedIndex];
        if (opt && opt.value) {
          entityId = opt.value;
          linkValue = `/collection/${opt.dataset.slug || ''}`;
        }
      } else if (linkType === 'brand') {
        const opt = brandSelect.options[brandSelect.selectedIndex];
        if (opt && opt.value) {
          entityId = opt.value;
          linkValue = `/collection/all?brand=${opt.dataset.slug || ''}`;
        }
      } else if (linkType === 'page') {
        const opt = pageSelect.options[pageSelect.selectedIndex];
        if (opt && opt.value) {
          entityId = opt.value;
          const cleanSlug = (opt.dataset.slug || '').replace(/^pages?\//, '').replace(/^\//, '');
          linkValue = `page/${cleanSlug}`;
        }
      }

      if (editingId) {
        // Edit existing
        const existing = findItemAndRemove(menuTree, editingId);
        if (existing) {
          existing.title = name;
          existing.link_type = linkType;
          existing.link_value = linkValue;
          existing.entity_id = entityId;
          existing.subtitle = subtitle;
          existing.image_url = imageUrl;
          existing.parent_id = parentId;

          // Re-insert into correct parent
          if (parentId) {
            const parent = findItemById(menuTree, parentId);
            if (parent) {
              if (!parent.children) parent.children = [];
              parent.children.push(existing);
            } else {
              menuTree.push(existing);
            }
          } else {
            menuTree.push(existing);
          }
        }
      } else {
        // Add new item
        const newItem = {
          id: 'temp_' + nextTempId++,
          title: name,
          subtitle: subtitle,
          link_type: linkType,
          link_value: linkValue,
          entity_id: entityId,
          image_url: imageUrl,
          parent_id: parentId,
          children: []
        };

        if (parentId) {
          const parent = findItemById(menuTree, parentId);
          if (parent) {
            if (!parent.children) parent.children = [];
            parent.children.push(newItem);
          } else {
            menuTree.push(newItem);
          }
        } else {
          menuTree.push(newItem);
        }
      }

      closeModal();
      renderTree();
    });
  }

  // Delete Item
  async function deleteItem(id) {
    if (!confirm('Are you sure you want to delete this menu item?')) {
      return;
    }

    const isTemp = String(id).startsWith('temp_') || isNaN(Number(id));
    if (isTemp) {
      findItemAndRemove(menuTree, id);
      renderTree();
      showToast('✓ Item removed');
      return;
    }

    try {
      const res = await fetch(window.deleteItemEndpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ item_id: parseInt(id, 10) })
      });

      const data = await res.json();
      if (data.success) {
        findItemAndRemove(menuTree, id);
        renderTree();
        showToast('✓ Menu item deleted successfully!');
      } else {
        alert(data.message || 'Failed to delete menu item.');
      }
    } catch (err) {
      console.error('Delete error:', err);
      // Fallback: remove from local tree anyway
      findItemAndRemove(menuTree, id);
      renderTree();
      showToast('Item removed locally. Click "Save Menu" to apply.');
    }
  }

  // Save entire Menu
  if (saveMenuBtn) {
    saveMenuBtn.addEventListener('click', async () => {
      const title = menuTitleInput ? menuTitleInput.value.trim() : '';
      const handle = menuHandleInput ? menuHandleInput.value.trim() : '';
      const subtitle = document.getElementById('menuSubtitleInput') ? document.getElementById('menuSubtitleInput').value : 'Secondary Menu';

      if (!title || !handle) {
        alert('Please provide a menu title and location handle.');
        return;
      }

      saveMenuBtn.disabled = true;
      saveMenuBtn.innerHTML = `
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" class="spin">
          <circle cx="12" cy="12" r="10" stroke-opacity="0.3"></circle>
          <path d="M12 2a10 10 0 0 1 10 10"></path>
        </svg>
        <span>Saving...</span>
      `;

      try {
        const payload = {
          id: menuId,
          title: title,
          handle: handle,
          subtitle: subtitle,
          items: menuTree
        };

        const res = await fetch(window.saveMenuEndpoint, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (data.success) {
          if (pageTitleHeading) pageTitleHeading.textContent = title;
          showToast('✓ Menu saved successfully!');
        } else {
          alert(data.message || 'Error saving menu.');
        }
      } catch (err) {
        console.error('Error saving menu:', err);
        alert('An unexpected error occurred while saving.');
      } finally {
        saveMenuBtn.disabled = false;
        saveMenuBtn.innerHTML = `
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
            <polyline points="17 21 17 13 7 13 7 21"></polyline>
            <polyline points="7 3 7 8 15 8"></polyline>
          </svg>
          <span>Save Menu</span>
        `;
      }
    });
  }

  function showToast(msg) {
    if (!toast) return;
    toast.textContent = msg;
    toast.style.display = 'block';
    toast.style.opacity = '1';
    setTimeout(() => {
      toast.style.opacity = '0';
      setTimeout(() => (toast.style.display = 'none'), 300);
    }, 2500);
  }

  function escapeHtml(text) {
    if (!text) return '';
    return text
      .toString()
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // Initial render
  renderTree();
});
