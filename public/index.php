<?php
/**
 * Infinity Vape Shop - Public Front Controller
 * Custom MVC Architecture
 */

define('ROOT_PATH', dirname(__DIR__));

// Register Autoloader & Helpers
require_once ROOT_PATH . '/app/Core/Autoloader.php';

// Instantiate and run the MVC Application
$app = new \App\Core\App();
$app->run();
