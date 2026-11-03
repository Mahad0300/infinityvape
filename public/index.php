<?php
/**
 * Infinity Vape Shop - Public Front Controller
 * Custom MVC Architecture
 */

define('ROOT_PATH', dirname(__DIR__));

// Register Autoloader & Helpers
require_once ROOT_PATH . '/app/Core/Autoloader.php';

// Clean up legacy singular terms-and-condition file if present
$oldTermsFile = ROOT_PATH . '/views/front/page/terms-and-condition.php';
if (file_exists($oldTermsFile)) {
    @unlink($oldTermsFile);
}

// Sync menu items and pages URL to terms-and-conditions & faqs
try {
    $db = \App\Core\Database::getConnection();
    if ($db) {
        $db->exec("UPDATE menu_items SET link_value = 'page/terms-and-conditions' WHERE link_value = 'page/terms-and-condition' OR link_value = '/page/terms-and-condition' OR link_value = 'terms-and-condition'");
        $db->exec("UPDATE pages SET slug = 'terms-and-conditions', custom_url_path = '/page/terms-and-conditions' WHERE slug = 'terms-and-condition'");
        $db->exec("UPDATE menu_items SET link_value = 'page/faqs' WHERE link_value = 'page/faq' OR link_value = '/page/faq' OR link_value = 'faq'");
        $db->exec("UPDATE pages SET slug = 'faqs', custom_url_path = '/page/faqs' WHERE slug = 'faq'");
    }
} catch (\Exception $e) {}

// Instantiate and run the MVC Application
$app = new \App\Core\App();
$app->run();
