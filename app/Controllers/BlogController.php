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
        $flavorProfiles = BlogPost::getByCategorySlugs(['flavors-eliquids', 'flavor-profiles'], 1);

        // 4. Nicotine Pouches & Hemp Guides (4 grid items)
        $hempAndPouches = BlogPost::getByCategorySlugs(['nicotine-pouches', 'hemp-cbd'], 4);

        // 5. Latest Posts (6 compact items)
        $latestPosts = BlogPost::latest(6);

        // Sidebar data
        $categories = BlogPost::getCategories();
        $recentPosts = BlogPost::latest(2);
        $popularPosts = BlogPost::latest(3);
        $archiveMonths = BlogPost::getArchiveMonths();
        $allTags = BlogPost::getAllTags();

        $totalPostsCount = array_sum(array_column($categories, 'post_count')) ?: count($latestPosts);

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
            'allTags'          => $allTags,
            'totalPostsCount'  => $totalPostsCount
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
        if (!empty($post['category_slug'])) {
            $relatedPosts = BlogPost::getByCategorySlugs([$post['category_slug']], 4);
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
        $allTags = BlogPost::getAllTags();

        $this->render('front.blog.detail', [
            'page_title'   => (!empty($post['meta_title']) ? $post['meta_title'] : $post['title']) . ' | Infinity Vape Shop Blog',
            'page_js'      => 'blog',
            'post'         => $post,
            'relatedPosts' => $relatedPosts,
            'categories'   => $categories,
            'recentPosts'  => $recentPosts,
            'popularPosts' => $popularPosts,
            'allTags'      => $allTags
        ]);
    }
}
