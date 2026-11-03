<?php
namespace App\Controllers;

use App\Core\Controller;

class CategoryController extends Controller
{
    /**
     * Category route index - automatically redirects to clean /collection/{slug} URL
     */
    public function index()
    {
        $slug = $_GET['cat'] ?? ($_GET['slug'] ?? ($_GET['collection'] ?? ($_GET['c'] ?? '')));
        
        // Remove category query parameters so they don't pollute the target URL
        $queryParams = $_GET;
        unset($queryParams['cat'], $queryParams['slug'], $queryParams['collection'], $queryParams['c']);
        
        $queryString = !empty($queryParams) ? '?' . http_build_query($queryParams) : '';
        
        if (!empty($slug)) {
            $targetUrl = url('collection/' . urlencode($slug)) . $queryString;
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: " . $targetUrl);
            exit;
        }

        // Default fallback if no slug specified
        $targetUrl = url('collection/disposable') . $queryString;
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: " . $targetUrl);
        exit;
    }
}
