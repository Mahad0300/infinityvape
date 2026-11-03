      </main>

    </div>
  </div>

  <!-- Global Admin Core Script -->
  <script src="<?= asset('admin/js/admin-core.js') ?>"></script>

  <!-- Page-Specific Script -->
  <?php 
    $adminJsRoot = defined('ROOT_PATH') ? ROOT_PATH : dirname(dirname(__DIR__));
    if (!empty($page_script) && file_exists($adminJsRoot . '/public/admin/js/' . $page_script)): 
  ?>
    <script src="<?= asset('admin/js/' . $page_script) ?>?v=<?= filemtime($adminJsRoot . '/public/admin/js/' . $page_script) ?>"></script>
  <?php endif; ?>

</body>
</html>
