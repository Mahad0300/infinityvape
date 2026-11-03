<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\BlogPost;

class TagController extends Controller
{
    /**
     * Storefront Tag Archive Page
     */
    public function index()
    {
        $allTags = BlogPost::getAllTags();
        $defaultTag = !empty($allTags) ? $allTags[0] : 'puff-count';

        $tagSlug = trim($_GET['tag'] ?? $defaultTag);
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 12;

        $tagResult = BlogPost::byTag($tagSlug, $page, $perPage);

        // Sidebar data
        $categories = BlogPost::getCategories();
        $recentPosts = BlogPost::latest(2);
        $popularPosts = BlogPost::latest(3);
        $archiveMonths = BlogPost::getArchiveMonths();

        $tagTitle = ucwords(str_replace(['-', '_'], ' ', $tagSlug));

        $this->render('front.tag.index', [
            'page_title'    => $tagTitle . ' | Infinity Vape Shop Blog',
            'page_js'       => 'blog',
            'currentTag'    => $tagSlug,
            'tagTitle'      => $tagTitle,
            'tagResult'     => $tagResult,
            'categories'    => $categories,
            'recentPosts'   => $recentPosts,
            'popularPosts'  => $popularPosts,
            'archiveMonths' => $archiveMonths,
            'allTags'       => $allTags
        ]);
    }
}
