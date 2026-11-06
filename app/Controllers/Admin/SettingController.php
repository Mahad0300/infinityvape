<?php
namespace App\Controllers\Admin;

use App\Core\Database;

class SettingController extends AdminController
{
    /**
     * Get all settings grouped or as key-value pairs
     */
    protected function getSettings(): array
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT `key`, `value` FROM settings");
        $rows = $stmt->fetchAll();

        $settings = [];
        foreach ($rows as $r) {
            $settings[$r['key']] = $r['value'];
        }

        return $settings;
    }

    /**
     * Display Settings Page
     */
    public function index()
    {
        $settings = $this->getSettings();

        $this->render('admin.settings.index', [
            'page_title'         => 'Store & Checkout Settings',
            'current_admin_page' => 'settings',
            'settings'           => $settings
        ]);
    }

    /**
     * Save / Update Settings
     */
    public function save()
    {
        $db = Database::getInstance()->getConnection();

        $allowedKeys = [
            'test_checkout_enabled'   => 'checkout',
            'tax_rate_percent'        => 'checkout',
            'free_shipping_threshold' => 'shipping',
            'standard_shipping_rate'  => 'shipping',
            'express_shipping_rate'   => 'shipping',
            'site_name'               => 'general',
            'site_email'              => 'general',
            'site_phone'              => 'general',
            'min_legal_age'           => 'compliance'
        ];

        foreach ($allowedKeys as $key => $group) {
            $val = $_POST[$key] ?? null;

            // Handle checkbox for test_checkout_enabled
            if ($key === 'test_checkout_enabled') {
                $val = isset($_POST['test_checkout_enabled']) ? '1' : '0';
            }

            if ($val !== null) {
                $stmt = $db->prepare("INSERT INTO settings (`key`, `value`, `group_name`) 
                                      VALUES (:key, :val, :grp) 
                                      ON DUPLICATE KEY UPDATE `value` = :val2, `group_name` = :grp2");
                $stmt->execute([
                    ':key'  => $key,
                    ':val'  => trim((string)$val),
                    ':grp'  => $group,
                    ':val2' => trim((string)$val),
                    ':grp2' => $group
                ]);
            }
        }

        if (function_exists('set_flash')) {
            set_flash('success', 'Settings have been updated successfully.');
        }

        $this->redirect('admin/settings');
    }
}

