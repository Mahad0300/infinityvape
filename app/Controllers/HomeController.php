<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Collection;
use App\Models\BlogPost;
use App\Models\ThemeSection;

class HomeController extends Controller
{
    public function index()
    {
        $brands = Brand::active(12);
        $categories = Collection::getMainCategories(4);
        $newArrivals = Product::newArrivals(10);
        $bestSellers = Product::bestSellers(10);
        $disposableVapes = Product::disposables(16);
        $vapeJuices = Product::vapeJuices(4);
        $nicotinePouches = Product::nicotinePouches(4);
        $latestBlogs = BlogPost::latest(4);
        $heroSlides = ThemeSection::getHeroSlides();
        $twinBanners = ThemeSection::getTwinBanners();
        $seoContent = ThemeSection::getSeoContent();

        $this->render('front.home.index', [
            'page_title' => 'Online Vape Store | Every Flavor. In Stock.',
            'meta_description' => 'Shop top disposable vapes, premium e-liquids, nicotine pouches, and hardware devices at Infinity Vape Shop. Guaranteed authentic with same-day fast shipping.',
            'canonical_url' => url(),
            'page_js' => 'home',
            'brands' => $brands,
            'categories' => $categories,
            'newArrivals' => $newArrivals,
            'bestSellers' => $bestSellers,
            'disposableVapes' => $disposableVapes,
            'vapeJuices' => $vapeJuices,
            'nicotinePouches' => $nicotinePouches,
            'latestBlogs' => $latestBlogs,
            'heroSlides' => $heroSlides,
            'twinBanners' => $twinBanners,
            'seoContent' => $seoContent
        ]);
    }
}
