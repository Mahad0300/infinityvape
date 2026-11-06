<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Collection;
use App\Models\Product;

class CollectionController extends Controller
{
    /**
     * Display all collections directory or primary collection
     */
    public function index()
    {
        $slug = $_GET['slug'] ?? ($_GET['cat'] ?? ($_GET['collection'] ?? ($_GET['c'] ?? 'all')));
        return $this->show($slug);
    }

    /**
     * Display a specific collection detail page with products and interactive filters
     */
    public function show($slug = null)
    {
        $slug = $slug ?? ($_GET['slug'] ?? ($_GET['cat'] ?? ($_GET['collection'] ?? ($_GET['c'] ?? 'all'))));
        $slug = trim(strtolower((string)$slug));
        if ($slug === 'collections' || $slug === 'collection' || empty($slug)) {
            $slug = 'all';
        }

        // Handle 'all' or 'shop'
        $isAll = ($slug === 'all' || $slug === 'shop' || empty($slug));
        $collection = null;

        if (!$isAll) {
            $collection = Collection::findBySlug($slug);
            if (!$collection && is_numeric($slug)) {
                $collection = Collection::find($slug);
            }
        }

        if (!$collection && !$isAll) {
            http_response_code(404);
            $this->render('errors.404', ['uri' => 'Collection Not Found: ' . htmlspecialchars($slug)]);
            return;
        }

        // 301 Permanent Redirect: Redirect legacy URLs or non-canonical single-segment URLs to clean hierarchical SEO URL
        $rawUri = $_SERVER['REQUEST_URI'] ?? '';
        if (!$isAll && $collection) {
            $canonicalUrl = Collection::getUrl($collection);
            $canonicalPath = parse_url($canonicalUrl, PHP_URL_PATH);
            $currentPath   = parse_url($rawUri, PHP_URL_PATH);

            $cleanCanonical = rtrim($canonicalPath, '/');
            $cleanCurrent   = rtrim($currentPath, '/');

            if ($cleanCurrent !== $cleanCanonical || strpos($rawUri, '?cat=') !== false || strpos($rawUri, '?collection=') !== false || strpos($rawUri, '?c=') !== false) {
                $queryPart = '';
                if (($qPos = strpos($rawUri, '?')) !== false) {
                    $qStr = substr($rawUri, $qPos + 1);
                    parse_str($qStr, $params);
                    unset($params['slug'], $params['cat'], $params['collection'], $params['c']);
                    if (!empty($params)) {
                        $queryPart = '?' . http_build_query($params);
                    }
                }
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: " . $canonicalUrl . $queryPart);
                exit;
            }
        }

        if (!$collection) {
            $collection = [
                'id' => 0,
                'name' => 'Shop All Vapes',
                'slug' => 'all',
                'short_description' => 'Explore our full selection of disposable vapes, premium e-liquids, nicotine pouches, and hardware devices from verified authentic brands.',
                'meta_title' => 'Shop All Vapes, E-Liquids & Disposables | Infinity Vape Shop',
                'meta_desc' => 'Browse our complete catalog of authentic disposable vapes, e-juices, nicotine pouches, and starter kits with fast same-day shipping.'
            ];
        }

        // Get all active collections for the Shop By filter sidebar
        $allCollections = Collection::all();

        // Target slug for product querying
        $filterSlug = ($collection['slug'] === 'all') ? null : $collection['slug'];

        // Extract active filters from GET request
        $activeBrand = $_GET['b'] ?? ($_GET['brand'] ?? null);
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
            'brand'     => $activeBrand,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'in_stock'  => $inStock,
            'sort'      => $sort,
            'q'         => $search
        ];

        // Fetch filtered products
        $products = Product::getCollectionProducts($filterSlug, $filters);

        // Fetch brands present in this collection with product counts
        $brands = Product::getBrandsForCollection($filterSlug);

        // Fetch min & max price bounds for the dual range slider
        $priceBounds = Product::getPriceBounds($filterSlug);

        // Sub-collections / child categories for quick pills if available
        $childCollections = [];
        if (!empty($collection['id'])) {
            $childCollections = array_filter($allCollections, function($c) use ($collection) {
                return !empty($c['parent_id']) && (int)$c['parent_id'] === (int)$collection['id'];
            });
        }

        // Fetch Collection FAQs
        $collectionFaqs = !empty($collection['id']) ? Collection::getFaqs($collection['id']) : [];

        // Fetch Related Collections (Admin cross-sell configured or fallback)
        $relatedCollections = Collection::getRelatedCollections($collection, 8);

        // Fetch Header Quick Navigation Collections (Checkmarked by admin)
        $headerCollections = Collection::getHeaderCollections($collection);

        // SEO Page Title, Meta Description & Canonical URL
        $pageTitle = !empty($collection['meta_title']) 
            ? $collection['meta_title'] 
            : ($collection['name'] . ' - Shop All Top Brands & Puffs | Infinity Vape Shop');

        $metaDesc = !empty($collection['meta_desc']) 
            ? $collection['meta_desc'] 
            : (!empty($collection['short_description']) 
                ? $collection['short_description'] 
                : 'Shop ' . ($collection['name'] ?? 'all vapes') . ' at Infinity Vape Shop. Discover authentic disposable vapes, premium e-liquids, and nicotine pouches.');

        $canonicalUrl = !empty($collection['id']) ? Collection::getUrl($collection) : url('collections');

        $this->render('front.collection.detail', [
            'page_title'         => $pageTitle,
            'meta_description'   => $metaDesc,
            'canonical_url'      => $canonicalUrl,
            'page_js'            => 'category',
            'collection'         => $collection,
            'collectionFaqs'     => $collectionFaqs,
            'allCollections'     => $allCollections,
            'relatedCollections' => $relatedCollections,
            'headerCollections'  => $headerCollections,
            'childCollections'   => $childCollections,
            'brands'             => $brands,
            'products'           => $products,
            'totalCount'         => count($products),
            'priceBounds'        => $priceBounds,
            'activeFilters'      => $filters,
            'currentSlug'        => $collection['slug'] ?? 'disposables',
            'currentSort'        => $sort,
            'activePuff'         => $activePuff,
            'quickPill'          => $activePuff
        ]);
    }
}
