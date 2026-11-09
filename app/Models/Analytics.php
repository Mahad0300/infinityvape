<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Analytics extends Model
{
    /**
     * Automatic storefront visitor tracking
     */
    public static function trackCurrentRequest($url = null)
    {
        // Only track GET requests
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
            return;
        }

        $url = $url ?: (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $url = '/' . trim($url, '/');

        // Ignore admin portal, api endpoints, and static asset files
        if (str_starts_with($url, '/admin') || 
            str_starts_with($url, '/api') || 
            preg_match('#\.(css|js|png|jpg|jpeg|webp|svg|ico|gif|woff2?|map|json|txt)$#i', $url)) {
            return;
        }

        try {
            $db = self::getDB();
            if (!$db) return;

            $ip = self::getClientIp();
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

            // Ignore SEO search crawlers and AI bots from polluting customer analytics
            if (preg_match('/(AhrefsBot|Googlebot|bingbot|Baiduspider|YandexBot|DuckDuckBot|Slurp|facebookexternalhit|Twitterbot|LinkedInBot|Bytespider|ClaudeBot|GPTBot|ChatGPT-User|CCBot|PerplexityBot|PetalBot|SemrushBot|DotBot|MJ12bot|bot|crawler|spider)/i', $ua)) {
                return;
            }

            $ref = $_SERVER['HTTP_REFERER'] ?? null;
            if (empty($ref)) {
                if (!empty($_GET['utm_source'])) {
                    $ref = 'utm:' . trim((string)$_GET['utm_source']) . (!empty($_GET['utm_medium']) ? '/' . trim((string)$_GET['utm_medium']) : '');
                } elseif (!empty($_GET['ref'])) {
                    $ref = 'ref:' . trim((string)$_GET['ref']);
                } else {
                    $ref = 'direct';
                }
            }

            $device = self::parseDevice($ua);
            $browser = self::parseBrowser($ua);
            $geo = self::detectGeo($ip);

            $stmt = $db->prepare("INSERT INTO `visitors` (`ip`, `country`, `country_code`, `city`, `device`, `browser`, `url`, `referrer`, `created_at`) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $ip,
                $geo['country'],
                $geo['country_code'],
                $geo['city'],
                $device,
                $browser,
                $url,
                substr($ref, 0, 255)
            ]);
        } catch (\Throwable $e) {
            error_log("Analytics::trackCurrentRequest error: " . $e->getMessage());
        }
    }

    /**
     * Record micro-telemetry events (cart_add, checkout_start, order_complete, click, dwell)
     */
    public static function recordEvent($url, $type, $name, $value = 0)
    {
        if (!in_array($type, ['cart_add', 'checkout_start', 'order_complete', 'click', 'dwell'], true)) {
            return false;
        }

        $url = '/' . trim((string) parse_url($url, PHP_URL_PATH), '/');
        if (str_starts_with($url, '/admin') || str_starts_with($url, '/api')) {
            return false;
        }

        try {
            $db = self::getDB();
            if (!$db) return false;

            $ip = self::getClientIp();
            $stmt = $db->prepare("INSERT INTO `analytics_events` (`visitor_ip`, `url`, `event_type`, `event_name`, `event_value`, `created_at`) 
                                  VALUES (?, ?, ?, ?, ?, NOW())");
            return $stmt->execute([
                $ip,
                $url,
                $type,
                substr($name, 0, 100),
                max(0, (int)$value)
            ]);
        } catch (\Throwable $e) {
            error_log("Analytics::recordEvent error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get Complete Dynamic Analytics for the given Date Range
     */
    public static function getAnalyticsData($from, $to)
    {
        $db = self::getDB();
        if (!$db) return [];

        $fromSql = $from . ' 00:00:00';
        $toSql   = $to . ' 23:59:59';
        $isSingleDay = ($from === $to);

        // Previous period for comparison delta
        $daysDiff = max(1, (int) round((strtotime($to) - strtotime($from)) / 86400));
        $prevFromSql = date('Y-m-d 00:00:00', strtotime($fromSql . " -{$daysDiff} days"));
        $prevToSql   = date('Y-m-d 23:59:59', strtotime($fromSql . ' -1 second'));

        // -----------------------------------------------------------------
        // 1. TOTAL SALES OVER TIME (Orders)
        // -----------------------------------------------------------------
        $salesStmt = $db->prepare("SELECT COALESCE(SUM(total_amount), 0) as total, COUNT(*) as orders_count 
                                   FROM orders 
                                   WHERE created_at BETWEEN ? AND ? AND (payment_status IS NULL OR payment_status != 'failed')");
        $salesStmt->execute([$fromSql, $toSql]);
        $currentSales = $salesStmt->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0, 'orders_count' => 0];
        $totalSalesVal = (float)$currentSales['total'];
        $ordersCount = (int)$currentSales['orders_count'];

        // Prev sales for delta
        $prevSalesStmt = $db->prepare("SELECT COALESCE(SUM(total_amount), 0) as total, COUNT(*) as orders_count 
                                       FROM orders 
                                       WHERE created_at BETWEEN ? AND ? AND (payment_status IS NULL OR payment_status != 'failed')");
        $prevSalesStmt->execute([$prevFromSql, $prevToSql]);
        $prevSales = $prevSalesStmt->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0, 'orders_count' => 0];
        $rawPrevSales = (float)$prevSales['total'];
        if ($rawPrevSales > 0) {
            $salesDelta = round((($totalSalesVal - $rawPrevSales) / $rawPrevSales) * 100, 1);
        } else {
            $salesDelta = $totalSalesVal > 0 ? 100.0 : 0.0;
        }

        // Average Order Value (AOV)
        $aov = $ordersCount > 0 ? ($totalSalesVal / $ordersCount) : 0.00;

        // Sales curve breakdown
        $salesLabels = [];
        $salesSeries = [];

        if ($isSingleDay) {
            // Hourly breakdown (12 am to 11 pm)
            $stmt = $db->prepare("SELECT HOUR(created_at) as h, COALESCE(SUM(total_amount), 0) as total 
                                   FROM orders 
                                   WHERE created_at BETWEEN ? AND ? AND (payment_status IS NULL OR payment_status != 'failed')
                                   GROUP BY HOUR(created_at) ORDER BY h ASC");
            $stmt->execute([$fromSql, $toSql]);
            $byHour = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $byHour[(int)$r['h']] = (float)$r['total'];
            }
            for ($h = 0; $h < 24; $h++) {
                $label = date('g a', strtotime("today {$h}:00"));
                $salesLabels[] = $label;
                $salesSeries[] = (float)($byHour[$h] ?? 0);
            }
        } else {
            // Daily breakdown
            $stmt = $db->prepare("SELECT DATE(created_at) as d, COALESCE(SUM(total_amount), 0) as total 
                                   FROM orders 
                                   WHERE created_at BETWEEN ? AND ? AND (payment_status IS NULL OR payment_status != 'failed')
                                   GROUP BY DATE(created_at) ORDER BY d ASC");
            $stmt->execute([$fromSql, $toSql]);
            $byDay = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $byDay[$r['d']] = (float)$r['total'];
            }
            $curr = strtotime($from);
            $end = strtotime($to);
            while ($curr <= $end) {
                $dStr = date('Y-m-d', $curr);
                $salesLabels[] = date('M j', $curr);
                $salesSeries[] = (float)($byDay[$dStr] ?? 0);
                $curr += 86400;
            }
        }

        // -----------------------------------------------------------------
        // 2. SESSIONS / CLICKS OVER TIME (Visitors)
        // -----------------------------------------------------------------
        $sessStmt = $db->prepare("SELECT COUNT(*) as total_sessions, COUNT(DISTINCT ip) as unique_visitors 
                                  FROM visitors 
                                  WHERE created_at BETWEEN ? AND ?");
        $sessStmt->execute([$fromSql, $toSql]);
        $currentSess = $sessStmt->fetch(PDO::FETCH_ASSOC) ?: ['total_sessions' => 0, 'unique_visitors' => 0];
        $totalSessions = (int)$currentSess['total_sessions'];
        $uniqueVisitors = (int)$currentSess['unique_visitors'];

        // Prev sessions for delta
        $prevSessStmt = $db->prepare("SELECT COUNT(*) as total_sessions FROM visitors WHERE created_at BETWEEN ? AND ?");
        $prevSessStmt->execute([$prevFromSql, $prevToSql]);
        $rawPrevSessions = (int)$prevSessStmt->fetchColumn();
        if ($rawPrevSessions > 0) {
            $sessionsDelta = round((($totalSessions - $rawPrevSessions) / $rawPrevSessions) * 100, 1);
        } else {
            $sessionsDelta = $totalSessions > 0 ? 100.0 : 0.0;
        }

        $sessionsLabels = [];
        $sessionsSeries = [];

        if ($isSingleDay) {
            $stmt = $db->prepare("SELECT HOUR(created_at) as h, COUNT(*) as cnt 
                                   FROM visitors 
                                   WHERE created_at BETWEEN ? AND ? 
                                   GROUP BY HOUR(created_at) ORDER BY h ASC");
            $stmt->execute([$fromSql, $toSql]);
            $byHour = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $byHour[(int)$r['h']] = (int)$r['cnt'];
            }
            for ($h = 0; $h < 24; $h++) {
                $label = date('g a', strtotime("today {$h}:00"));
                $sessionsLabels[] = $label;
                $sessionsSeries[] = (int)($byHour[$h] ?? 0);
            }
        } else {
            $stmt = $db->prepare("SELECT DATE(created_at) as d, COUNT(*) as cnt 
                                   FROM visitors 
                                   WHERE created_at BETWEEN ? AND ? 
                                   GROUP BY DATE(created_at) ORDER BY d ASC");
            $stmt->execute([$fromSql, $toSql]);
            $byDay = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $byDay[$r['d']] = (int)$r['cnt'];
            }
            $curr = strtotime($from);
            $end = strtotime($to);
            while ($curr <= $end) {
                $dStr = date('Y-m-d', $curr);
                $sessionsLabels[] = date('M j', $curr);
                $sessionsSeries[] = (int)($byDay[$dStr] ?? 0);
                $curr += 86400;
            }
        }

        // -----------------------------------------------------------------
        // 3. CONVERSION RATE BREAKDOWN (E-Commerce 4-Stage Funnel)
        // -----------------------------------------------------------------
        // Stage 1: Total Sessions
        $funnelSessions = max(1, $totalSessions);

        // Stage 2: Added to cart
        $cartStmt = $db->prepare("SELECT COUNT(*) FROM analytics_events WHERE event_type = 'cart_add' AND created_at BETWEEN ? AND ?");
        $cartStmt->execute([$fromSql, $toSql]);
        $addedToCart = (int)$cartStmt->fetchColumn();

        // Stage 3: Reached checkout
        $chkStmt = $db->prepare("SELECT COUNT(*) FROM analytics_events WHERE event_type = 'checkout_start' AND created_at BETWEEN ? AND ?");
        $chkStmt->execute([$fromSql, $toSql]);
        $reachedCheckout = (int)$chkStmt->fetchColumn();

        // Stage 4: Completed checkout (Orders)
        $completedCheckout = $ordersCount;

        $cartPct = round(($addedToCart / $funnelSessions) * 100, 2);
        $checkoutPct = round(($reachedCheckout / $funnelSessions) * 100, 2);
        $conversionRate = round(($completedCheckout / $funnelSessions) * 100, 2);

        $funnel = [
            'conversion_rate' => $conversionRate . '%',
            'raw_rate' => $conversionRate,
            'stages' => [
                'sessions' => [
                    'label' => 'Sessions',
                    'count' => $funnelSessions,
                    'pct' => '100%'
                ],
                'cart' => [
                    'label' => 'Added to cart',
                    'count' => $addedToCart,
                    'pct' => $cartPct . '%'
                ],
                'checkout' => [
                    'label' => 'Reached checkout',
                    'count' => $reachedCheckout,
                    'pct' => $checkoutPct . '%'
                ],
                'completed' => [
                    'label' => 'Completed checkout',
                    'count' => $completedCheckout,
                    'pct' => $conversionRate . '%'
                ]
            ]
        ];

        // -----------------------------------------------------------------
        // 4. TOTAL SALES BY PRODUCT (Matrix Table)
        // -----------------------------------------------------------------
        $prodSalesStmt = $db->prepare("
            SELECT oi.product_id, 
                   oi.product_title, 
                   p.featured_image,
                   p.base_price,
                   p.slug,
                   SUM(oi.quantity) as units_sold, 
                   SUM(oi.quantity * oi.price_at_purchase) as total_revenue
            FROM order_items oi
            JOIN orders o ON o.id = oi.order_id
            LEFT JOIN products p ON p.id = oi.product_id
            WHERE o.created_at BETWEEN ? AND ? AND (o.payment_status IS NULL OR o.payment_status != 'failed')
            GROUP BY oi.product_id, oi.product_title, p.featured_image, p.base_price, p.slug
            ORDER BY total_revenue DESC
            LIMIT 10
        ");
        $prodSalesStmt->execute([$fromSql, $toSql]);
        $topProducts = $prodSalesStmt->fetchAll(PDO::FETCH_ASSOC);

        // -----------------------------------------------------------------
        // 5. SESSIONS BY BROWSER / SEARCH ENGINE REFERRER
        // -----------------------------------------------------------------
        $refStmt = $db->prepare("SELECT referrer, COUNT(*) as cnt FROM visitors WHERE created_at BETWEEN ? AND ? GROUP BY referrer ORDER BY cnt DESC");
        $refStmt->execute([$fromSql, $toSql]);
        $rawRefs = $refStmt->fetchAll(PDO::FETCH_ASSOC);

        $browserSources = [
            'Direct / Bookmark' => 0,
            'Google'            => 0,
            'Bing'              => 0,
            'Yahoo'             => 0,
            'DuckDuckGo'        => 0,
            'Other Browsers'    => 0,
        ];

        foreach ($rawRefs as $r) {
            $ref = strtolower(trim((string)$r['referrer']));
            $c = (int)$r['cnt'];
            if (empty($ref) || $ref === 'direct' || str_starts_with($ref, 'direct')) {
                $browserSources['Direct / Bookmark'] += $c;
            } elseif (str_contains($ref, 'google')) {
                $browserSources['Google'] += $c;
            } elseif (str_contains($ref, 'bing')) {
                $browserSources['Bing'] += $c;
            } elseif (str_contains($ref, 'yahoo')) {
                $browserSources['Yahoo'] += $c;
            } elseif (str_contains($ref, 'duckduckgo')) {
                $browserSources['DuckDuckGo'] += $c;
            } elseif (!str_contains($ref, 'instagram') && !str_contains($ref, 'facebook') && !str_contains($ref, 'tiktok') && !str_contains($ref, 'reddit') && !str_contains($ref, 'twitter')) {
                $browserSources['Other Browsers'] += $c;
            }
        }

        $maxBrSource = max(1, max($browserSources));
        $browserReferrers = [];
        foreach ($browserSources as $name => $cnt) {
            $pct = round(($cnt / $funnelSessions) * 100, 1);
            $browserReferrers[] = [
                'name'    => $name,
                'count'   => $cnt,
                'pct'     => $pct . '%',
                'bar_pct' => round(($cnt / $maxBrSource) * 100) . '%'
            ];
        }

        // -----------------------------------------------------------------
        // 6. SESSIONS BY SOCIAL REFERRER
        // -----------------------------------------------------------------
        $socialSources = [
            'Instagram' => 0,
            'Facebook'  => 0,
            'TikTok'    => 0,
            'Reddit'    => 0,
            'YouTube'   => 0,
            'X / Twitter' => 0
        ];

        foreach ($rawRefs as $r) {
            $ref = strtolower(trim((string)$r['referrer']));
            $c = (int)$r['cnt'];
            if (str_contains($ref, 'instagram')) $socialSources['Instagram'] += $c;
            elseif (str_contains($ref, 'facebook') || str_contains($ref, 'fb.com')) $socialSources['Facebook'] += $c;
            elseif (str_contains($ref, 'tiktok')) $socialSources['TikTok'] += $c;
            elseif (str_contains($ref, 'reddit')) $socialSources['Reddit'] += $c;
            elseif (str_contains($ref, 'youtube')) $socialSources['YouTube'] += $c;
            elseif (str_contains($ref, 'twitter') || str_contains($ref, 't.co') || str_contains($ref, 'x.com')) $socialSources['X / Twitter'] += $c;
        }

        $maxSocial = max(1, max($socialSources));
        $socialReferrers = [];
        foreach ($socialSources as $name => $cnt) {
            $pct = round(($cnt / $funnelSessions) * 100, 1);
            $socialReferrers[] = [
                'name'    => $name,
                'count'   => $cnt,
                'pct'     => $pct . '%',
                'bar_pct' => round(($cnt / $maxSocial) * 100) . '%'
            ];
        }

        // -----------------------------------------------------------------
        // 7. SESSIONS BY LANDING PAGE
        // -----------------------------------------------------------------
        $pageStmt = $db->prepare("SELECT url, COUNT(*) as sessions, COUNT(DISTINCT ip) as uniques 
                                  FROM visitors 
                                  WHERE created_at BETWEEN ? AND ? 
                                  GROUP BY url 
                                  ORDER BY sessions DESC 
                                  LIMIT 8");
        $pageStmt->execute([$fromSql, $toSql]);
        $landingPages = $pageStmt->fetchAll(PDO::FETCH_ASSOC);

        // -----------------------------------------------------------------
        // 8. SESSIONS BY DEVICE TYPE (Desktop vs Mobile vs Tablet)
        // -----------------------------------------------------------------
        $devStmt = $db->prepare("SELECT device, COUNT(*) as cnt FROM visitors WHERE created_at BETWEEN ? AND ? GROUP BY device");
        $devStmt->execute([$fromSql, $toSql]);
        $devCounts = ['mobile' => 0, 'desktop' => 0, 'tablet' => 0];
        foreach ($devStmt->fetchAll(PDO::FETCH_ASSOC) as $d) {
            $k = strtolower($d['device']);
            if (isset($devCounts[$k])) {
                $devCounts[$k] = (int)$d['cnt'];
            }
        }
        $totalDev = max(1, array_sum($devCounts));
        $mPct = round(($devCounts['mobile'] / $totalDev) * 100, 1);
        $dPct = round(($devCounts['desktop'] / $totalDev) * 100, 1);
        $tPct = max(0, 100 - $mPct - $dPct);

        $devices = [
            ['name' => 'Mobile', 'count' => $devCounts['mobile'], 'pct' => $mPct . '%', 'raw_pct' => $mPct, 'color' => '#3b82f6'],
            ['name' => 'Desktop', 'count' => $devCounts['desktop'], 'pct' => $dPct . '%', 'raw_pct' => $dPct, 'color' => '#10b981'],
            ['name' => 'Tablet', 'count' => $devCounts['tablet'], 'pct' => $tPct . '%', 'raw_pct' => $tPct, 'color' => '#f59e0b']
        ];

        // -----------------------------------------------------------------
        // 9. SESSIONS BY LOCATION (Countries)
        // -----------------------------------------------------------------
        $geoStmt = $db->prepare("SELECT country, country_code, COUNT(*) as cnt 
                                 FROM visitors 
                                 WHERE created_at BETWEEN ? AND ? AND country IS NOT NULL AND country != ''
                                 GROUP BY country, country_code 
                                 ORDER BY cnt DESC 
                                 LIMIT 8");
        $geoStmt->execute([$fromSql, $toSql]);
        $geoRows = $geoStmt->fetchAll(PDO::FETCH_ASSOC);
        $maxGeo = 0;
        foreach ($geoRows as $g) {
            if ((int)$g['cnt'] > $maxGeo) $maxGeo = (int)$g['cnt'];
        }
        $maxGeo = max(1, $maxGeo);

        $locations = [];
        foreach ($geoRows as $g) {
            $code = strtoupper((string)($g['country_code'] ?? 'US'));
            $name = $g['country'] ?: 'United States';
            $cnt = (int)$g['cnt'];
            $locations[] = [
                'country' => $name,
                'country_code' => $code,
                'flag' => self::countryToFlag($code),
                'count' => $cnt,
                'pct' => round(($cnt / $funnelSessions) * 100, 1) . '%',
                'bar_pct' => round(($cnt / $maxGeo) * 100) . '%'
            ];
        }

        return [
            'range' => [
                'from' => $from,
                'to'   => $to,
                'label' => $isSingleDay ? date('d M Y', strtotime($from)) : (date('d M', strtotime($from)) . ' - ' . date('d M Y', strtotime($to))),
                'is_single_day' => $isSingleDay
            ],
            'sales' => [
                'total_raw' => $totalSalesVal,
                'total_formatted' => '$' . number_format($totalSalesVal, 2),
                'delta' => $salesDelta,
                'chart' => [
                    'labels' => $salesLabels,
                    'series' => $salesSeries
                ]
            ],
            'sessions' => [
                'total' => $totalSessions,
                'uniques' => $uniqueVisitors,
                'delta' => $sessionsDelta,
                'chart' => [
                    'labels' => $sessionsLabels,
                    'series' => $sessionsSeries
                ]
            ],
            'orders' => [
                'count' => $ordersCount,
                'aov' => '$' . number_format($aov, 2)
            ],
            'funnel' => $funnel,
            'top_products' => $topProducts,
            'browser_referrers' => $browserReferrers,
            'social_referrers' => $socialReferrers,
            'landing_pages' => $landingPages,
            'devices' => $devices,
            'locations' => $locations
        ];
    }

    /**
     * Parse User-Agent for Device
     */
    public static function parseDevice($ua)
    {
        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $ua)) {
            return 'tablet';
        }
        if (preg_match('/(iphone|ipod|blackberry|android|palm|windows\s+phone)/i', $ua)) {
            return 'mobile';
        }
        return 'desktop';
    }

    /**
     * Parse User-Agent for Browser
     */
    public static function parseBrowser($ua)
    {
        if (preg_match('/Edg/i', $ua)) return 'Edge';
        if (preg_match('/Chrome/i', $ua)) return 'Chrome';
        if (preg_match('/Safari/i', $ua) && !preg_match('/Chrome/i', $ua)) return 'Safari';
        if (preg_match('/Firefox/i', $ua)) return 'Firefox';
        if (preg_match('/Opera|OPR/i', $ua)) return 'Opera';
        return 'Other';
    }

    /**
     * Detect Geo location based on headers
     */
    public static function detectGeo($ip)
    {
        $country = $_SERVER['HTTP_CF_IPCOUNTRY'] ?? $_SERVER['GEOIP_COUNTRY_NAME'] ?? null;
        $countryCode = $_SERVER['HTTP_CF_IPCOUNTRY'] ?? $_SERVER['GEOIP_COUNTRY_CODE'] ?? null;
        $city = $_SERVER['GEOIP_CITY'] ?? null;

        if (empty($country)) {
            $country = 'United States';
            $countryCode = 'US';
            $city = 'Dallas';
        }

        return [
            'country' => $country,
            'country_code' => $countryCode ?: 'US',
            'city' => $city
        ];
    }

    /**
     * Convert 2-letter ISO country code to Emoji Flag
     */
    public static function countryToFlag($code)
    {
        if (empty($code) || strlen($code) !== 2) return '🌐';
        $code = strtoupper($code);
        $first = ord($code[0]) - ord('A') + 0x1F1E6;
        $second = ord($code[1]) - ord('A') + 0x1F1E6;
        if (function_exists('mb_chr')) {
            return mb_chr($first, 'UTF-8') . mb_chr($second, 'UTF-8');
        }
        return '🌐';
    }

    /**
     * Get real client IP address
     */
    public static function getClientIp()
    {
        $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        foreach ($keys as $k) {
            if (!empty($_SERVER[$k])) {
                $ips = explode(',', $_SERVER[$k]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}
