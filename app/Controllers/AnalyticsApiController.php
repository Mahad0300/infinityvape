<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Analytics;

class AnalyticsApiController extends Controller
{
    /**
     * POST /api/analytics/event
     * Lightweight beacon endpoint for real-time frontend customer interactions:
     * cart additions, checkout initiations, dwell time, CTA clicks.
     */
    public function recordEvent()
    {
        header('Content-Type: application/json');

        // Allow same-origin or localhost
        $origin = $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? '';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        if ($origin && $host && !str_contains($origin, $host) && !str_contains($origin, 'localhost') && !str_contains($origin, '127.0.0.1')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Forbidden']);
            exit;
        }

        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        if (!$data && !empty($_POST)) {
            $data = $_POST;
        }

        if (empty($data)) {
            echo json_encode(['success' => false, 'error' => 'No event payload']);
            exit;
        }

        $events = [];
        if (isset($data['events']) && is_array($data['events'])) {
            $events = $data['events'];
        } elseif (isset($data[0]) && is_array($data[0])) {
            $events = $data;
        } else {
            $events = [$data];
        }

        $recorded = 0;
        foreach ($events as $ev) {
            if (!is_array($ev)) continue;

            $url = trim((string)($ev['url'] ?? $_SERVER['HTTP_REFERER'] ?? '/'));
            $type = trim((string)($ev['type'] ?? $ev['event_type'] ?? 'click'));
            $name = trim((string)($ev['name'] ?? $ev['event_name'] ?? 'interaction'));
            $value = (int)($ev['value'] ?? $ev['event_value'] ?? 0);

            if ($type !== '' && $name !== '') {
                if (Analytics::recordEvent($url, $type, $name, $value)) {
                    $recorded++;
                }
            }
        }

        echo json_encode(['success' => true, 'recorded' => $recorded]);
        exit;
    }
}

