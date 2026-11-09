<?php
namespace App\Controllers\Admin;

use App\Models\Analytics;

class AnalyticsController extends AdminController
{
    public function index()
    {
        $range = trim($_GET['range'] ?? '30d');
        $from = trim($_GET['from'] ?? '');
        $to = trim($_GET['to'] ?? '');

        if ($range === 'custom' && !empty($from) && !empty($to)) {
            // Use custom dates
            $from = date('Y-m-d', strtotime($from));
            $to = date('Y-m-d', strtotime($to));
        } else {
            $to = date('Y-m-d');
            switch ($range) {
                case 'today':
                    $from = $to;
                    break;
                case 'yesterday':
                    $from = date('Y-m-d', strtotime('-1 day'));
                    $to = $from;
                    break;
                case '7d':
                    $from = date('Y-m-d', strtotime('-6 days'));
                    break;
                case '90d':
                    $from = date('Y-m-d', strtotime('-89 days'));
                    break;
                case '30d':
                default:
                    $range = '30d';
                    $from = date('Y-m-d', strtotime('-29 days'));
                    break;
            }
        }

        $analytics = Analytics::getAnalyticsData($from, $to);

        // If AJAX request, return dynamic JSON payload
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || 
                  isset($_GET['ajax']) || 
                  (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'range'   => $range,
                'from'    => $from,
                'to'      => $to,
                'data'    => $analytics
            ]);
            exit;
        }

        $this->render('admin.analytics.index', [
            'page_title'         => 'Analytics & Reports',
            'current_admin_page' => 'analytics',
            'page_script'        => 'analytics.js',
            'range'              => $range,
            'from'               => $from,
            'to'                 => $to,
            'analytics'          => $analytics
        ]);
    }
}

