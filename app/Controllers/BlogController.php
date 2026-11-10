<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\BlogPost;

class BlogController extends Controller
{
    /**
     * Storefront Blog Index Page
     */
    public function index()
    {
        // 1. Featured Guides (Lead + 3 stack items)
        $featuredGuides = BlogPost::getByCategorySlugs(['vaping-guides', 'industry-news'], 4);

        // 2. Top Product Reviews (3 grid items)
        $topReviews = BlogPost::getByCategorySlugs(['product-reviews'], 3);

        // 3. Flavor Profiles (1 lead card)
        $flavorProfiles = BlogPost::getByCategorySlugs(['flavor-profiles'], 1);

        // 4. Nicotine Pouches & Hemp Guides (4 grid items)
        $hempAndPouches = BlogPost::getByCategorySlugs(['nicotine-pouches-guide', 'eliquid-guide', 'comparisons', 'brand-spotlights'], 4);

        // 5. Latest Posts (6 compact items)
        $latestPosts = BlogPost::latest(6);

        // Sidebar data
        $categories = BlogPost::getCategories();
        $recentPosts = BlogPost::latest(2);
        $popularPosts = BlogPost::latest(3);
        $archiveMonths = BlogPost::getArchiveMonths();

        $totalPostsCount = BlogPost::countActive();

        $this->render('front.blog.index', [
            'page_title'       => 'Infinity Vape Shop Blog | Vaping Guides, Reviews & Industry News',
            'page_js'          => 'blog',
            'featuredGuides'   => $featuredGuides,
            'topReviews'       => $topReviews,
            'flavorProfiles'   => $flavorProfiles,
            'hempAndPouches'   => $hempAndPouches,
            'latestPosts'      => $latestPosts,
            'categories'       => $categories,
            'recentPosts'      => $recentPosts,
            'popularPosts'     => $popularPosts,
            'archiveMonths'    => $archiveMonths,
            'totalPostsCount'  => $totalPostsCount
        ]);
    }

    /**
     * Storefront Blog Category Archive Page
     */
    public function category()
    {
        $catSlug = trim($_GET['cat'] ?? $_GET['slug'] ?? '');
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 12;

        if (empty($catSlug)) {
            redirect('blog');
            return;
        }

        $categoryResult = BlogPost::byCategory($catSlug, $page, $perPage);
        $category = $categoryResult['category'] ?? null;

        if (!$category) {
            redirect('blog');
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        if (!empty($category['slug'])) {
            $_SESSION['last_blog_category'] = $category['slug'];
        }

        // Sidebar data (Categories, Recent Posts, Popular Posts, Archive)
        $categories = BlogPost::getCategories();
        $recentPosts = BlogPost::latest(3);
        $popularPosts = BlogPost::latest(3);
        $archiveMonths = BlogPost::getArchiveMonths();
        $totalPostsCount = BlogPost::countActive();

        $this->render('front.blog.category', [
            'page_title'      => htmlspecialchars($category['name']) . ' | Infinity Vape Shop Blog',
            'page_js'         => 'blog',
            'category'        => $category,
            'categoryResult'  => $categoryResult,
            'posts'           => $categoryResult['data'] ?? [],
            'categories'      => $categories,
            'recentPosts'     => $recentPosts,
            'popularPosts'    => $popularPosts,
            'archiveMonths'   => $archiveMonths,
            'totalPostsCount' => $totalPostsCount
        ]);
    }

    /**
     * Storefront Blog Detail Page
     */
    public function detail()
    {
        $slug = trim($_GET['slug'] ?? $_GET['post'] ?? '');

        if (empty($slug)) {
            // Default to latest post if none specified
            $latest = BlogPost::latest(1);
            if (!empty($latest)) {
                $post = $latest[0];
            } else {
                redirect('blog');
                return;
            }
        } else {
            $post = BlogPost::find($slug);
            if (!$post) {
                // If not found, redirect to blog page
                redirect('blog');
                return;
            }
        }

        // Related posts based on category
        $relatedPosts = [];
        $catSlugs = !empty($post['categories']) ? array_column($post['categories'], 'slug') : (!empty($post['category_slug']) ? [$post['category_slug']] : []);
        if (!empty($catSlugs)) {
            $relatedPosts = BlogPost::getByCategorySlugs($catSlugs, 4);
            // Filter out current post and re-index array
            $relatedPosts = array_values(array_filter($relatedPosts, function($p) use ($post) {
                return $p['id'] != $post['id'];
            }));
        }
        if (empty($relatedPosts)) {
            $relatedPosts = array_values(array_filter(BlogPost::latest(4), function($p) use ($post) {
                return $p['id'] != $post['id'];
            }));
        }

        $categories = BlogPost::getCategories();
        $recentPosts = BlogPost::latest(2);
        $popularPosts = BlogPost::latest(3);
        $totalPostsCount = BlogPost::countActive();

        $this->render('front.blog.detail', [
            'page_title'      => (!empty($post['meta_title']) ? $post['meta_title'] : $post['title']) . ' | Infinity Vape Shop Blog',
            'page_js'         => 'blog',
            'post'            => $post,
            'relatedPosts'    => $relatedPosts,
            'categories'      => $categories,
            'recentPosts'     => $recentPosts,
            'popularPosts'    => $popularPosts,
            'totalPostsCount' => $totalPostsCount
        ]);
    }
}
