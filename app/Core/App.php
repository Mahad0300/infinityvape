<?php
namespace App\Core;

class App
{
    protected $routes = [];

    public function __construct()
    {
        $this->routes = config('routes', []);
    }

    public function run()
    {
        session_init();
        $uri = $this->resolveUri();

        // Automatic Storefront Visitor Tracking
        if (class_exists('\\App\\Models\\Analytics')) {
            \App\Models\Analytics::trackCurrentRequest('/' . ltrim($uri, '/'));
        }

        // Check exact route match
        if (isset($this->routes[$uri])) {
            return $this->dispatch($this->routes[$uri]);
        }

        // Check with / without trailing slash
        $trimmedUri = rtrim($uri, '/');
        if (isset($this->routes[$trimmedUri])) {
            return $this->dispatch($this->routes[$trimmedUri]);
        }

        // Check if URI ends with .php and stripped version exists
        if (substr($uri, -4) === '.php') {
            $noPhp = substr($uri, 0, -4);
            if (isset($this->routes[$noPhp])) {
                return $this->dispatch($this->routes[$noPhp]);
            }
        }

        // Dynamic route matching for collections (/collections/{slug} or /collection/{slug})
        if (preg_match('#^collections?/(.+)$#', $trimmedUri, $matches)) {
            $_GET['slug'] = basename($matches[1]);
            return $this->dispatch([\App\Controllers\CollectionController::class, 'show']);
        }

        // Dynamic route matching for brands (/brands/{slug} or /brand/{slug})
        if (preg_match('#^brands?/(.+)$#', $trimmedUri, $matches)) {
            $_GET['slug'] = basename($matches[1]);
            return $this->dispatch([\App\Controllers\BrandController::class, 'show']);
        }

        // Dynamic route matching for product (/products/{slug} or /product/{slug})
        if (preg_match('#^products?/(.+)$#', $trimmedUri, $matches)) {
            $_GET['slug'] = basename($matches[1]);
            return $this->dispatch([\App\Controllers\ProductController::class, 'index']);
        }

        // Dynamic route matching for category (/category/{slug})
        if (preg_match('#^category/(.+)$#', $trimmedUri, $matches)) {
            $_GET['cat'] = basename($matches[1]);
            return $this->dispatch([\App\Controllers\CategoryController::class, 'index']);
        }

        // Dynamic route matching for blog posts (/blog/{slug} or /blog/category/{slug})
        if (preg_match('#^blog/(.+)$#', $trimmedUri, $matches)) {
            $blogSlug = basename($matches[1]);
            if (strpos($trimmedUri, 'blog/category/') === 0) {
                $_GET['cat'] = $blogSlug;
                return $this->dispatch([\App\Controllers\BlogController::class, 'category']);
            }
            if (class_exists('\\App\\Models\\BlogPost')) {
                $checkCat = \App\Models\BlogPost::findCategory($blogSlug);
                if ($checkCat) {
                    $_GET['cat'] = $checkCat['slug'];
                    return $this->dispatch([\App\Controllers\BlogController::class, 'category']);
                }
            }
            // 301 Permanent Redirect to clean root URL
            redirect($blogSlug, 301);
            return;
        }

        // Dynamic route matching for static pages (/pages/{slug} or /page/{slug})
        if (preg_match('#^pages?/(.+)$#', $trimmedUri, $matches)) {
            $slug = basename($matches[1]);
            if (isset($this->routes[$slug])) {
                return $this->dispatch($this->routes[$slug]);
            }
            $viewPath = dirname(dirname(__DIR__)) . '/views/front/page/' . $slug . '.php';
            if (file_exists($viewPath)) {
                return View::render('front.page.' . $slug);
            }
        }

        // Dynamic Hierarchical & Clean Collection Routes:
        // Case 1: Child under Parent: /{parent_slug}/{child_slug} (e.g. /disposable-vapes/5000-puff-disposable-vape)
        // Case 2: Top-level Collection: /{slug} (e.g. /disposable-vapes)
        $uriSegments = explode('/', $trimmedUri);
        $segCount = count($uriSegments);

        if ($segCount === 2) {
            $possibleParent = $uriSegments[0];
            $possibleChild  = $uriSegments[1];

            if (class_exists('\\App\\Models\\Collection') && !empty($possibleChild)) {
                $matchedCol = \App\Models\Collection::findBySlug($possibleChild);
                if ($matchedCol) {
                    $_GET['slug'] = $possibleChild;
                    $_GET['parent_slug'] = $possibleParent;
                    return $this->dispatch([\App\Controllers\CollectionController::class, 'show']);
                }
            }
        } elseif ($segCount === 1 && !empty($trimmedUri)) {
            // 1. Check Product Collection
            if (class_exists('\\App\\Models\\Collection')) {
                $matchedCol = \App\Models\Collection::findBySlug($trimmedUri);
                if ($matchedCol) {
                    $_GET['slug'] = $trimmedUri;
                    return $this->dispatch([\App\Controllers\CollectionController::class, 'show']);
                }
            }

            // 2. Check Blog Category (e.g. /comparisons, /vaping-guides)
            if (class_exists('\\App\\Models\\BlogPost')) {
                $matchedBlogCat = \App\Models\BlogPost::findCategory($trimmedUri);
                if ($matchedBlogCat) {
                    $_GET['cat'] = $matchedBlogCat['slug'];
                    return $this->dispatch([\App\Controllers\BlogController::class, 'category']);
                }
            }

            // 3. Check Blog Post (e.g. /how-to-fix-burnt-disposable-vape-causes-solutions)
            if (class_exists('\\App\\Models\\BlogPost')) {
                $matchedPost = \App\Models\BlogPost::find($trimmedUri);
                if ($matchedPost) {
                    $_GET['slug'] = $matchedPost['slug'];
                    return $this->dispatch([\App\Controllers\BlogController::class, 'detail']);
                }
            }

            // 4. Check Brand (e.g. /geek-bar, /lost-mary)
            if (class_exists('\\App\\Models\\Brand')) {
                $matchedBrand = \App\Models\Brand::findBySlug($trimmedUri);
                if ($matchedBrand) {
                    $_GET['slug'] = $trimmedUri;
                    return $this->dispatch([\App\Controllers\BrandController::class, 'show']);
                }
            }
        }

        // 404 Not Found
        http_response_code(404);
        View::render('errors.404', ['uri' => $uri]);
        return;
    }

    protected function resolveUri()
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

        // Strip query string
        $pos = strpos($requestUri, '?');
        if ($pos !== false) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        // Remove base directory (useful when hosted in /vapefinalwebsite or /vapefinalwebsite/public)
        $baseDir = dirname($scriptName);
        if ($baseDir !== '/' && $baseDir !== '\\' && strpos($requestUri, $baseDir) === 0) {
            $uri = substr($requestUri, strlen($baseDir));
        } else {
            $uri = $requestUri;
        }

        $uri = trim($uri, '/');

        // If uri is public or starts with public/
        if ($uri === 'public') {
            $uri = '';
        } elseif (strpos($uri, 'public/') === 0) {
            $uri = substr($uri, 7);
        }

        return $uri;
    }

    protected function dispatch($handler)
    {
        if (is_callable($handler)) {
            return call_user_func($handler);
        }

        if (is_array($handler)) {
            list($controllerClass, $method) = $handler;
            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                if (method_exists($controller, $method)) {
                    return $controller->$method();
                }
            }
        }

        http_response_code(500);
        echo "<h1>500 - Route Handler Error</h1>";
    }
}
