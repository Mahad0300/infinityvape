<?php
$page_title = 'Navigation';
$current_admin_page = 'navigation';
$page_script = 'navigation.js';
include dirname(__DIR__) . '/includes/header.php';

$menus = $menus ?? \App\Models\Menu::all();
?>

<!-- Title & Action Bar -->
<div class="page-title-bar">
  <div class="page-heading-group">
    <h1 style="font-size: 26px; font-weight: 800; color: #0f172a;">Navigation</h1>
  </div>
  <div class="page-actions-group">
    <button type="button" class="btn-purple-primary" id="openAddMenuModalBtn">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
      </svg>
      <span>Add Menu</span>
    </button>
  </div>
</div>

<!-- Menus Table Card -->
<div class="admin-card">
  <div class="admin-card-body p-0">
    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th style="width: 45%;">Title</th>
            <th style="width: 25%; text-align: center;">Location (Handle)</th>
            <th style="width: 15%; text-align: center;">Items Count</th>
            <th style="width: 15%; text-align: right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($menus)): ?>
            <?php foreach ($menus as $m): ?>
              <tr>
                <!-- Title & Subtitle -->
                <td>
                  <a href="<?= url('admin/navigation/builder') ?>?id=<?= (int)$m['id'] ?>" class="nav-table-title">
                    <?= htmlspecialchars($m['name']) ?>
                  </a>
                  <span class="nav-table-subtitle"><?= htmlspecialchars(!empty($m['subtitle']) ? $m['subtitle'] : 'Secondary Menu') ?></span>
                </td>

                <!-- Location Handle -->
                <td style="text-align: center;">
                  <span class="handle-badge"><?= htmlspecialchars($m['location']) ?></span>
                </td>

                <!-- Items Count -->
                <td style="text-align: center; color: #64748b;">
                  <?= !empty($m['items_count']) ? (int)$m['items_count'] : '-' ?>
                </td>

                <!-- Actions -->
                <td style="text-align: right;">
                  <div class="nav-actions-cell">
                    <a href="<?= url('admin/navigation/builder') ?>?id=<?= (int)$m['id'] ?>" class="btn-nav-action" title="Edit Menu">
                      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                      </svg>
                    </a>

                    <form action="<?= url('admin/navigation/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this menu and all its items?');">
                      <?= csrf_field() ?>
                      <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                      <button type="submit" class="btn-nav-action action-delete" title="Delete Menu">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <polyline points="3 6 5 6 21 6"></polyline>
                          <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" class="text-center p-xl text-muted">
                No menus created yet. Click "+ Add Menu" to create your first navigation menu.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Add Menu Modal -->
<div class="menu-modal-overlay" id="addMenuModal">
  <div class="menu-modal-content">
    <div class="menu-modal-header">
      <h3 class="menu-modal-title">Create New Menu</h3>
      <button type="button" class="menu-modal-close-btn" id="closeAddMenuModalBtn">&times;</button>
    </div>
    <form action="<?= url('admin/navigation/create') ?>" method="POST" id="createMenuForm">
      <?= csrf_field() ?>
      <div class="menu-modal-body">
        <div class="admin-form-group">
          <label class="admin-form-label font-medium">Menu Title <span class="text-danger">*</span></label>
          <input type="text" name="name" id="newMenuName" class="admin-form-input" placeholder="e.g. Header Navigation or Sidebar Menu" required autofocus>
        </div>

        <div class="admin-form-group">
          <label class="admin-form-label font-medium">Handle / Location</label>
          <input type="text" name="location" id="newMenuLocation" class="admin-form-input" placeholder="e.g. main_menu or mobile_menu">
          <p class="menu-settings-help">Unique handle used in theme templates to render this menu.</p>
        </div>

        <div class="admin-form-group">
          <label class="admin-form-label font-medium">Subtitle / Note</label>
          <input type="text" name="subtitle" class="admin-form-input" value="Secondary Menu" placeholder="e.g. Secondary Menu">
        </div>
      </div>
      <div class="menu-modal-footer">
        <button type="button" class="btn-modal-cancel" id="cancelAddMenuModalBtn">Cancel</button>
        <button type="submit" class="btn-purple-primary">Create Menu</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('addMenuModal');
  const openBtn = document.getElementById('openAddMenuModalBtn');
  const closeBtn = document.getElementById('closeAddMenuModalBtn');
  const cancelBtn = document.getElementById('cancelAddMenuModalBtn');
  const nameInput = document.getElementById('newMenuName');
  const locInput = document.getElementById('newMenuLocation');

  function openModal() {
    if (modal) modal.classList.add('is-active');
    if (nameInput) nameInput.focus();
  }

  function closeModal() {
    if (modal) modal.classList.remove('is-active');
  }

  if (openBtn) openBtn.addEventListener('click', openModal);
  if (closeBtn) closeBtn.addEventListener('click', closeModal);
  if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

  if (modal) {
    modal.addEventListener('click', (e) => {
      if (e.target === modal) closeModal();
    });
  }

  // Auto generate handle from name
  if (nameInput && locInput) {
    nameInput.addEventListener('input', () => {
      if (!locInput.dataset.manual) {
        locInput.value = nameInput.value.toLowerCase().trim().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
      }
    });
    locInput.addEventListener('input', () => {
      locInput.dataset.manual = 'true';
    });
  }
});
</script>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>
