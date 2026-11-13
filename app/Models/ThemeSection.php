<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class ThemeSection extends Model
{
    /**
     * Get a setting by key
     */
    public static function getSetting($key, $default = null)
    {
        $db = self::getDB();
        if (!$db) return $default;

        try {
            $stmt = $db->prepare("SELECT `value` FROM settings WHERE `key` = ? LIMIT 1");
            $stmt->execute([$key]);
            $val = $stmt->fetchColumn();
            return ($val !== false && $val !== null) ? $val : $default;
        } catch (\Exception $e) {
            error_log("ThemeSection::getSetting error: " . $e->getMessage());
            return $default;
        }
    }

    /**
     * Set or update a setting
     */
    public static function setSetting($key, $value, $group = 'storefront')
    {
        $db = self::getDB();
        if (!$db) return false;

        try {
            $stmt = $db->prepare("
                INSERT INTO settings (`key`, `value`, `group_name`) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `group_name` = VALUES(`group_name`)
            ");
            return $stmt->execute([$key, $value, $group]);
        } catch (\Exception $e) {
            error_log("ThemeSection::setSetting error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get Announcement & Warning Bars configuration
     */
    public static function getAnnouncementBars()
    {
        $raw = self::getSetting('theme_announcement_bars');
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        // Return sensible default values
        return [
            'fda_warning_active' => 1,
            'fda_warning_text'   => 'WARNING: This product contains nicotine. Nicotine is an addictive chemical.',
            'promo_bar_active'   => 1,
            'promo_bar_text'     => 'Free shipping on orders over $150',
            'promo_bar_link'     => ''
        ];
    }

    /**
     * Save Announcement & Warning Bars configuration
     */
    public static function saveAnnouncementBars(array $data)
    {
        $clean = [
            'fda_warning_active' => !empty($data['fda_warning_active']) ? 1 : 0,
            'fda_warning_text'   => trim($data['fda_warning_text'] ?? 'WARNING: This product contains nicotine. Nicotine is an addictive chemical.'),
            'promo_bar_active'   => !empty($data['promo_bar_active']) ? 1 : 0,
            'promo_bar_text'     => trim($data['promo_bar_text'] ?? 'Free shipping on orders over $150'),
            'promo_bar_link'     => trim($data['promo_bar_link'] ?? '')
        ];

        return self::setSetting('theme_announcement_bars', json_encode($clean, JSON_UNESCAPED_UNICODE), 'storefront');
    }

    /**
     * Get Hero Banner Slides
     */
    public static function getHeroSlides()
    {
        $raw = self::getSetting('theme_hero_slides');
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && !empty($decoded)) {
                return $decoded;
            }
        }

        // Default initial slide
        return [
            [
                'id'          => 1,
                'badge_text'  => 'NEW FLAVORS',
                'title'       => 'Every Flavor. In Stock.',
                'subtitle'    => 'The full Foger Switch Pro lineup is here.',
                'button_text' => 'Find Your Flavor',
                'button_url'  => 'category?b=foger',
                'image_url'   => 'assets/images/foger-switch-pro-new-pod-flavors-d-banner.webp',
                'is_active'   => 1
            ]
        ];
    }

    /**
     * Save Hero Banner Slides
     */
    public static function saveHeroSlides(array $slides)
    {
        $clean = [];
        foreach ($slides as $idx => $s) {
            $title = trim($s['title'] ?? '');
            if (empty($title)) continue;

            $clean[] = [
                'id'          => $idx + 1,
                'badge_text'  => trim($s['badge_text'] ?? 'NEW ARRIVALS'),
                'title'       => $title,
                'subtitle'    => trim($s['subtitle'] ?? ''),
                'button_text' => trim($s['button_text'] ?? 'Shop Now'),
                'button_url'  => trim($s['button_url'] ?? 'category'),
                'image_url'   => trim($s['image_url'] ?? ''),
                'is_active'   => isset($s['is_active']) ? (int)$s['is_active'] : 1
            ];
        }

        if (empty($clean)) {
            // keep at least 1 default
            $clean = self::getHeroSlides();
        }

        return self::setSetting('theme_hero_slides', json_encode($clean, JSON_UNESCAPED_UNICODE), 'storefront');
    }

    /**
     * Get Twin Promo Banners
     */
    public static function getTwinBanners()
    {
        $raw = self::getSetting('theme_twin_banners');
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && !empty($decoded)) {
                return $decoded;
            }
        }

        // Default twin banners
        return [
            [
                'id'          => 1,
                'title'       => 'Foger Switch Pro &mdash; Up to 30,000 Puffs',
                'subtitle'    => 'Pod system with power bank & OLED display.',
                'button_text' => 'Shop Foger Switch Pro',
                'button_url'  => 'category?b=foger',
                'image_url'   => 'assets/images/twin-banner.webp',
                'is_active'   => 1
            ],
            [
                'id'          => 2,
                'title'       => 'Entire Adjust Lineup',
                'subtitle'    => 'Tune your cool. Tune your flavor. Only $11.99.',
                'button_text' => 'Shop Adjust Series',
                'button_url'  => 'category?b=adjust',
                'image_url'   => 'assets/images/twin-banner.webp',
                'is_active'   => 1
            ]
        ];
    }

    /**
     * Save Twin Promo Banners
     */
    public static function saveTwinBanners(array $banners)
    {
        $clean = [];
        for ($i = 0; $i < 2; $i++) {
            $b = $banners[$i] ?? [];
            $clean[] = [
                'id'          => $i + 1,
                'title'       => trim($b['title'] ?? ''),
                'subtitle'    => trim($b['subtitle'] ?? ''),
                'button_text' => trim($b['button_text'] ?? 'Shop Now'),
                'button_url'  => trim($b['button_url'] ?? 'category'),
                'image_url'   => trim($b['image_url'] ?? ''),
                'is_active'   => isset($b['is_active']) ? (int)$b['is_active'] : 1
            ];
        }

        return self::setSetting('theme_twin_banners', json_encode($clean, JSON_UNESCAPED_UNICODE), 'storefront');
    }

    /**
     * Get SEO / Trusted Vape Store Content configuration
     */
    public static function getSeoContent()
    {
        $raw = self::getSetting('theme_seo_trusted_content');
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                if (empty($decoded['content'])) {
                    // Backwards compatibility with previous paragraph fields
                    $parts = [];
                    if (!empty($decoded['paragraph_1'])) $parts[] = '<p>' . $decoded['paragraph_1'] . '</p>';
                    if (!empty($decoded['paragraph_2'])) $parts[] = '<p>' . $decoded['paragraph_2'] . '</p>';
                    if (!empty($decoded['paragraph_3'])) $parts[] = '<p>' . $decoded['paragraph_3'] . '</p>';
                    if (!empty($decoded['paragraph_4'])) $parts[] = '<p>' . $decoded['paragraph_4'] . '</p>';
                    if (!empty($decoded['disclaimer_text'])) $parts[] = '<p class="seo-content-disclaimer">' . $decoded['disclaimer_text'] . '</p>';
                    if (!empty($decoded['compliance_text'])) $parts[] = '<p class="seo-content-compliance">' . $decoded['compliance_text'] . '</p>';
                    $decoded['content'] = !empty($parts) ? implode("\n\n", $parts) : '';
                }
                return $decoded;
            }
        }

        // Default single rich text content from homepage
        $defaultContent = '<p>We are a trusted online vape shop where you can buy vapes online with confidence. We stock a wide selection of <a href="category?cat=disposables">disposable vapes</a>, <a href="category?cat=e-liquids">e-liquids</a>, and <a href="category?cat=nicotine-pouches">nicotine pouches</a>, all from brands you know and love. Every product sold on our store is 100% authentic and ships fast from within the United States.</p>

<p>How much does a vape cost? Disposable vapes start at $8.99, nicotine pouches at $6.99, and most high-puff devices stay under $25. We back that up with a price match guarantee: find a lower price at another online vape store and we will match it. No coupon hunting needed, the price you see is already the low one.</p>

<p>Looking for high-puff devices? Browse our collection of <a href="category">5,000 to 50,000+ puff disposable vapes</a> from top brands like Geek Bar, Lost Mary, RAZ, and more. We also carry vape juices in nicotine salt, freebase, TFN, and zero-nicotine options, plus vape pod systems and starter kits for every experience level.</p>

<p>So what makes us the best online vape store? Ask the people who shop here: our customers rate us 4.7 out of 5 stars across more than 1,700 reviews. Orders ship the same day, every purchase is covered by a 14-day return policy, and our support team actually answers when you need help with an order, a device, or a flavor recommendation.</p>

<p class="seo-content-disclaimer">All products on this site are intended for adults of legal smoking age only. These products have not been evaluated by the Food and Drug Administration and are not intended to diagnose, treat, prevent, or cure any disease or condition. Keep all products out of the reach of children and pets.</p>

<p class="seo-content-compliance">Our store is fully PACT Act compliant. We verify every order meets federal and state regulations so you can shop our online vape store worry-free.</p>';

        return [
            'is_active' => 1,
            'title'     => 'Your Trusted Online Vape Store',
            'content'   => $defaultContent
        ];
    }

    /**
     * Save SEO / Trusted Vape Store Content configuration
     */
    public static function saveSeoContent(array $data)
    {
        $clean = [
            'is_active' => !empty($data['is_active']) ? 1 : 0,
            'title'     => trim($data['title'] ?? 'Your Trusted Online Vape Store'),
            'content'   => trim($data['content'] ?? '')
        ];

        return self::setSetting('theme_seo_trusted_content', json_encode($clean, JSON_UNESCAPED_UNICODE), 'storefront');
    }
}
