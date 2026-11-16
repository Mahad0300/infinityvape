<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Collection;
use App\Models\Product;

class SearchController extends Controller
{
    /**
     * Storefront search: published products by title, active collections by name.
     */
    public function index()
    {
        $query = trim((string)($_GET['q'] ?? ''));
        $query = preg_replace('/\s+/u', ' ', $query);
        if (mb_strlen($query) > 80) {
            $query = mb_substr($query, 0, 80);
        }

        $products = $query !== '' ? Product::searchByTitle($query) : [];
        $collections = $query !== '' ? Collection::searchActiveByName($query) : [];

        $pageTitle = $query !== ''
            ? 'Search results for ' . $query . ' | Infinity Vape Shop'
            : 'Search | Infinity Vape Shop';

        $this->render('front.search.index', [
            'page_title'  => $pageTitle,
            'page_js'     => 'search',
            'query'       => $query,
            'products'    => $products,
            'collections' => $collections,
        ]);
    }

    /**
     * Live header suggestions. Published products by title, active collections by name.
     */
    public function suggest()
    {
        $query = trim((string)($_GET['q'] ?? ''));
        $query = preg_replace('/\s+/u', ' ', $query);
        if (mb_strlen($query) > 80) {
            $query = mb_substr($query, 0, 80);
        }

        if (mb_strlen($query) < 2) {
            $this->json(['collections' => [], 'products' => []]);
        }

        $collections = [];
        foreach (Collection::searchActiveByName($query, 4) as $collection) {
            $collections[] = [
                'name' => $collection['name'] ?? '',
                'url'  => Collection::getUrl($collection),
            ];
        }

        $products = [];
        foreach (Product::searchByTitle($query, 6) as $product) {
            $slug = !empty($product['slug']) ? $product['slug'] : $product['id'];
            $image = $product['featured_image'] ?? '';
            if ($image === '' || $image === 'assets/images/vape-image.webp') {
                $image = 'assets/images/no-image.svg';
            }
            if (!preg_match('~^(https?://|//|#)~i', $image)) {
                $image = url($image);
            }
            $products[] = [
                'title' => !empty($product['title']) ? $product['title'] : ($product['name'] ?? ''),
                'url'   => url('product/' . rawurlencode($slug)),
                'image' => $image,
                'price' => number_format((float)($product['base_price'] ?? 0), 2),
            ];
        }

        $this->json([
            'collections' => $collections,
            'products'    => $products,
        ]);
    }
}
