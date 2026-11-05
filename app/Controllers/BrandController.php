<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Brand;
use App\Models\Collection;
use App\Models\Product;

class BrandController extends Controller
{
    /**
     * Display all brands directory page with simple cards
     */
    public function index()
    {
        $brands = Brand::active();

        $page_title       = 'All Brands | Official Vape Brands & Manufacturers';
        $meta_description = 'Explore our verified collection of authentic vape hardware, disposable vapes, and premium e-liquid brands at Infinity Vape Shop.';
        $canonical_url    = url('brands');

        $this->render('front.brand.index', [
            'brands'           => $brands,
            'totalCount'       => count($brands),
            'page_title'       => $page_title,
            'meta_description' => $meta_description,
            'canonical_url'    => $canonical_url,
        ]);
    }

    /**
     * Display a specific brand detail page with products and interactive filters
     */
    public function show($slug = null)
    {
        $slug = $slug ?? ($_GET['slug'] ?? ($_GET['b'] ?? ($_GET['brand'] ?? '')));
        $slug = trim(strtolower($slug));

        $brand = null;
        if (!empty($slug)) {
            $brand = Brand::findBySlug($slug);
            if (!$brand && is_numeric($slug)) {
                $brand = Brand::find($slug);
            }
        }

        if (!$brand) {
            http_response_code(404);
            $this->render('errors.404', ['uri' => 'Brand Not Found: ' . htmlspecialchars($slug)]);
            return;
        }

        // Canonical URL enforcement (redirect /brands/slug to /brand/slug)
        $rawUri = $_SERVER['REQUEST_URI'] ?? '';
        $canonicalUrl = Brand::getUrl($brand);
        $canonicalPath = parse_url($canonicalUrl, PHP_URL_PATH);
        $currentPath   = parse_url($rawUri, PHP_URL_PATH);

        $cleanCanonical = rtrim($canonicalPath, '/');
        $cleanCurrent   = rtrim($currentPath, '/');

        if ($cleanCurrent !== $cleanCanonical) {
            $queryPart = '';
            if (($qPos = strpos($rawUri, '?')) !== false) {
                $qStr = substr($rawUri, $qPos + 1);
                parse_str($qStr, $params);
                unset($params['slug'], $params['brand'], $params['b']);
                if (!empty($params)) {
                    $queryPart = '?' . http_build_query($params);
                }
            }
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: " . $canonicalUrl . $queryPart);
            exit;
        }

        // Extract active filters from GET request
        $activeCollection = $_GET['c'] ?? ($_GET['collection'] ?? ($_GET['cat'] ?? null));
        $minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
        $maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;
        $inStock = !empty($_GET['in_stock']) ? 1 : 0;
        $sort = $_GET['sort'] ?? 'position';
        $search = $_GET['q'] ?? null;
        $activePuff = $_GET['puffs'] ?? ($_GET['puff'] ?? ($_GET['pill'] ?? ($_GET['filter'] ?? 'all')));
        $activePuff = strtolower(trim((string)$activePuff));
        if ($activePuff === '5k') $activePuff = '5000';
        if ($activePuff === '10k') $activePuff = '10000';
        if ($activePuff === '15k') $activePuff = '15000';
        if ($activePuff === '20k') $activePuff = '20000';
        if ($activePuff === '30k') $activePuff = '30000';
        if ($activePuff === '50k') $activePuff = '50000';

        $filters = [
            'collection' => $activeCollection,
            'min_price'  => $minPrice,
            'max_price'  => $maxPrice,
            'in_stock'   => $inStock,
            'sort'       => $sort,
            'q'          => $search,
            'puffs'      => $activePuff
        ];

        // Fetch filtered products belonging to this brand
        $products = Brand::getProducts($brand['id'], $filters);

        // Fetch price bounds for dual-slider
        $priceBounds = Brand::getPriceBounds($brand['id']);

        // Fetch collections associated with this brand
        $brandCollections = Brand::getCollectionsForBrand($brand['id']);

        // Fetch all active collections for the Shop By filter sidebar
        $allCollections = class_exists('\\App\\Models\\Collection') ? Collection::all() : [];

        // Fetch Header Quick Navigation Collections (Checkmarked in admin)
        $headerCollections = Brand::getHeaderCollections($brand);

        // Fetch Related Collections (Checkmarked in admin or fallback)
        $relatedCollections = Brand::getRelatedCollections($brand, 8);

        // Fetch Brand FAQs
        $brandFaqs = Brand::getFaqs($brand['id']);

        // SEO Page Title, Meta Description & Canonical URL
        $pageTitle = !empty($brand['meta_title'])
            ? $brand['meta_title']
            : ($brand['name'] . ' Official Store | Buy ' . $brand['name'] . ' Vapes Online | New Vape');

        $metaDesc = !empty($brand['meta_desc'])
            ? $brand['meta_desc']
            : (!empty($brand['short_description'])
                ? $brand['short_description']
                : 'Shop authentic ' . $brand['name'] . ' disposable vapes, devices, and accessories with fast same-day shipping at New Vape.');

        // Fetch all active brands for the Shop By filter sidebar
        $allBrands = Brand::active();

        $this->render('front.brand.detail', [
            'page_title'         => $pageTitle,
            'meta_description'   => $metaDesc,
            'canonical_url'      => $canonicalUrl,
            'page_js'            => 'category',
            'brand'              => $brand,
            'brands'             => $allBrands,
            'brandFaqs'          => $brandFaqs,
            'allCollections'     => $allCollections,
            'brandCollections'   => $brandCollections,
            'relatedCollections' => $relatedCollections,
            'headerCollections'  => $headerCollections,
            'products'           => $products,
            'totalCount'         => count($products),
            'priceBounds'        => $priceBounds,
            'activeFilters'      => $filters,
            'currentSlug'        => $brand['slug'],
            'currentSort'        => $sort,
            'activePuff'         => $activePuff,
            'activeCollection'   => $activeCollection,
            'quickPill'          => $activePuff
        ]);
    }
}

