<?php
/**
 * Application Routes Map
 * Maps URL paths to [ControllerClass, ActionMethod]
 */

return [
    // Front Store Routes
    ''                     => [\App\Controllers\HomeController::class, 'index'],
    'index.php'            => [\App\Controllers\HomeController::class, 'index'],
    'home'                 => [\App\Controllers\HomeController::class, 'index'],
    'category'             => [\App\Controllers\CategoryController::class, 'index'],
    'category.php'         => [\App\Controllers\CategoryController::class, 'index'],
    'shop'                 => [\App\Controllers\CategoryController::class, 'index'],
    'shop.php'             => [\App\Controllers\CategoryController::class, 'index'],
    'collections'          => [\App\Controllers\CollectionController::class, 'index'],
    'collections.php'      => [\App\Controllers\CollectionController::class, 'index'],
    'collection'           => [\App\Controllers\CollectionController::class, 'show'],
    'collection.php'       => [\App\Controllers\CollectionController::class, 'show'],
    'brand'                => [\App\Controllers\BrandController::class, 'show'],
    'brand.php'            => [\App\Controllers\BrandController::class, 'show'],
    'brands'               => [\App\Controllers\BrandController::class, 'index'],
    'brands.php'           => [\App\Controllers\BrandController::class, 'index'],
    'product'              => [\App\Controllers\ProductController::class, 'index'],
    'product.php'          => [\App\Controllers\ProductController::class, 'index'],

    // Cart API Routes
    'cart/items'           => [\App\Controllers\CartController::class, 'items'],
    'cart/items.php'       => [\App\Controllers\CartController::class, 'items'],
    'cart/add'             => [\App\Controllers\CartController::class, 'add'],
    'cart/add.php'         => [\App\Controllers\CartController::class, 'add'],
    'cart/add-multiple'    => [\App\Controllers\CartController::class, 'addMultiple'],
    'cart/add-multiple.php'=> [\App\Controllers\CartController::class, 'addMultiple'],
    'cart/update'          => [\App\Controllers\CartController::class, 'update'],
    'cart/update.php'      => [\App\Controllers\CartController::class, 'update'],
    'cart/remove'          => [\App\Controllers\CartController::class, 'remove'],
    'cart/remove.php'      => [\App\Controllers\CartController::class, 'remove'],
    'cart/clear'           => [\App\Controllers\CartController::class, 'clear'],
    'cart/clear.php'       => [\App\Controllers\CartController::class, 'clear'],

    'checkout'             => [\App\Controllers\CheckoutController::class, 'index'],
    'checkout.php'         => [\App\Controllers\CheckoutController::class, 'index'],
    'checkout/process'     => [\App\Controllers\CheckoutController::class, 'process'],
    'checkout/apply-coupon'=> [\App\Controllers\CheckoutController::class, 'applyCoupon'],
    'checkout/remove-coupon'=>[\App\Controllers\CheckoutController::class, 'removeCoupon'],
    'my-account'           => [\App\Controllers\AccountController::class, 'index'],
    'my-account.php'       => [\App\Controllers\AccountController::class, 'index'],
    'account/login'        => [\App\Controllers\AccountController::class, 'login'],
    'account/register'     => [\App\Controllers\AccountController::class, 'register'],
    'account/logout'       => [\App\Controllers\AccountController::class, 'logout'],
    'account/update-profile' => [\App\Controllers\AccountController::class, 'updateProfile'],
    'account/save-address' => [\App\Controllers\AccountController::class, 'saveAddress'],
    'blog'                 => [\App\Controllers\BlogController::class, 'index'],
    'blog.php'             => [\App\Controllers\BlogController::class, 'index'],
    'blog/category'        => [\App\Controllers\BlogController::class, 'category'],
    'blog-category'        => [\App\Controllers\BlogController::class, 'category'],
    'blog-detail'          => [\App\Controllers\BlogController::class, 'detail'],
    'blog-detail.php'      => [\App\Controllers\BlogController::class, 'detail'],

    // Legal, Policy & Informational Pages
    'page/shipping-policy'     => [\App\Controllers\PageController::class, 'shippingPolicy'],
    'shipping-policy'          => [\App\Controllers\PageController::class, 'shippingPolicy'],

    'page/refund-policy'       => [\App\Controllers\PageController::class, 'refundPolicy'],
    'refund-policy'            => [\App\Controllers\PageController::class, 'refundPolicy'],

    'page/fda-disclaimer'      => [\App\Controllers\PageController::class, 'fdaDisclaimer'],
    'fda-disclaimer'           => [\App\Controllers\PageController::class, 'fdaDisclaimer'],

    'page/terms-and-conditions' => [\App\Controllers\PageController::class, 'terms'],
    'terms-and-conditions'      => [\App\Controllers\PageController::class, 'terms'],

    'page/privacy-policy'      => [\App\Controllers\PageController::class, 'privacy'],
    'privacy-policy'           => [\App\Controllers\PageController::class, 'privacy'],

    'page/age-verification'    => [\App\Controllers\PageController::class, 'fdaDisclaimer'],

    'page/contact-us'          => [\App\Controllers\PageController::class, 'contact'],
    'contact-us'               => [\App\Controllers\PageController::class, 'contact'],

    'page/faqs'                 => [\App\Controllers\PageController::class, 'faqs'],
    'faqs'                      => [\App\Controllers\PageController::class, 'faqs'],

    'page/about-us'            => [\App\Controllers\PageController::class, 'about'],
    'about-us'                 => [\App\Controllers\PageController::class, 'about'],

    'page/track-order'         => [\App\Controllers\PageController::class, 'trackOrder'],
    'track-order'              => [\App\Controllers\PageController::class, 'trackOrder'],

    // Admin Auth Routes
    'admin/login'          => [\App\Controllers\Admin\AuthController::class, 'login'],
    'admin/login.php'      => [\App\Controllers\Admin\AuthController::class, 'login'],
    'admin/logout'         => [\App\Controllers\Admin\AuthController::class, 'logout'],
    'admin/logout.php'     => [\App\Controllers\Admin\AuthController::class, 'logout'],

    // Admin Protected Routes
    'admin'                => [\App\Controllers\Admin\DashboardController::class, 'index'],
    'admin/'               => [\App\Controllers\Admin\DashboardController::class, 'index'],
    'admin/index.php'      => [\App\Controllers\Admin\DashboardController::class, 'index'],
    'admin/dashboard'      => [\App\Controllers\Admin\DashboardController::class, 'index'],
    'admin/products'       => [\App\Controllers\Admin\ProductController::class, 'index'],
    'admin/products.php'   => [\App\Controllers\Admin\ProductController::class, 'index'],
    'admin/add-product'    => [\App\Controllers\Admin\ProductController::class, 'create'],
    'admin/add-product.php'=> [\App\Controllers\Admin\ProductController::class, 'create'],
    'admin/edit-product'   => [\App\Controllers\Admin\ProductController::class, 'edit'],
    'admin/edit-product.php'=>[\App\Controllers\Admin\ProductController::class, 'edit'],
    'admin/delete-product' => [\App\Controllers\Admin\ProductController::class, 'delete'],
    'admin/delete-product.php'=>[\App\Controllers\Admin\ProductController::class, 'delete'],
    'admin/upload-variant-image' => [\App\Controllers\Admin\ProductController::class, 'uploadVariantImage'],
    'admin/inventory'      => [\App\Controllers\Admin\InventoryController::class, 'index'],
    'admin/inventory.php'  => [\App\Controllers\Admin\InventoryController::class, 'index'],
    'admin/inventory/update'=>[\App\Controllers\Admin\InventoryController::class, 'update'],
    'admin/inventory-update'=>[\App\Controllers\Admin\InventoryController::class, 'update'],
    'admin/collections'    => [\App\Controllers\Admin\CollectionController::class, 'index'],
    'admin/collections.php'=> [\App\Controllers\Admin\CollectionController::class, 'index'],
    'admin/add-collection' => [\App\Controllers\Admin\CollectionController::class, 'create'],
    'admin/add-collection.php'=>[\App\Controllers\Admin\CollectionController::class, 'create'],
    'admin/edit-collection'=> [\App\Controllers\Admin\CollectionController::class, 'edit'],
    'admin/edit-collection.php'=>[\App\Controllers\Admin\CollectionController::class, 'edit'],
    'admin/delete-collection'=> [\App\Controllers\Admin\CollectionController::class, 'delete'],
    'admin/delete-collection.php'=>[\App\Controllers\Admin\CollectionController::class, 'delete'],
    'admin/brands'         => [\App\Controllers\Admin\BrandController::class, 'index'],
    'admin/brands.php'     => [\App\Controllers\Admin\BrandController::class, 'index'],
    'admin/add-brand'      => [\App\Controllers\Admin\BrandController::class, 'create'],
    'admin/add-brand.php'  => [\App\Controllers\Admin\BrandController::class, 'create'],
    'admin/edit-brand'     => [\App\Controllers\Admin\BrandController::class, 'edit'],
    'admin/edit-brand.php' => [\App\Controllers\Admin\BrandController::class, 'edit'],
    'admin/delete-brand'   => [\App\Controllers\Admin\BrandController::class, 'delete'],
    'admin/delete-brand.php'=>[\App\Controllers\Admin\BrandController::class, 'delete'],
    'admin/orders'         => [\App\Controllers\Admin\OrderController::class, 'index'],
    'admin/orders.php'     => [\App\Controllers\Admin\OrderController::class, 'index'],
    'admin/order-detail'   => [\App\Controllers\Admin\OrderController::class, 'detail'],
    'admin/order-detail.php'=>[\App\Controllers\Admin\OrderController::class, 'detail'],
    'admin/orders/update-status' => [\App\Controllers\Admin\OrderController::class, 'updateStatus'],
    'admin/orders/update'  => [\App\Controllers\Admin\OrderController::class, 'updateStatus'],
    'admin/order/update-status' => [\App\Controllers\Admin\OrderController::class, 'updateStatus'],
    'admin/customers'      => [\App\Controllers\Admin\CustomerController::class, 'index'],
    'admin/customers.php'  => [\App\Controllers\Admin\CustomerController::class, 'index'],
    'admin/customer-detail'=> [\App\Controllers\Admin\CustomerController::class, 'detail'],
    'admin/customer-detail.php'=> [\App\Controllers\Admin\CustomerController::class, 'detail'],
    'admin/customer/update-note' => [\App\Controllers\Admin\CustomerController::class, 'updateNote'],
    'admin/customer-update-note' => [\App\Controllers\Admin\CustomerController::class, 'updateNote'],
    'admin/navigation'     => [\App\Controllers\Admin\NavigationController::class, 'index'],
    'admin/navigation.php' => [\App\Controllers\Admin\NavigationController::class, 'index'],
    'admin/navigation/builder' => [\App\Controllers\Admin\NavigationController::class, 'builder'],
    'admin/navigation-builder' => [\App\Controllers\Admin\NavigationController::class, 'builder'],
    'admin/navigation/edit'    => [\App\Controllers\Admin\NavigationController::class, 'builder'],
    'admin/navigation-edit'    => [\App\Controllers\Admin\NavigationController::class, 'builder'],
    'admin/navigation/create'  => [\App\Controllers\Admin\NavigationController::class, 'create'],
    'admin/navigation/save'    => [\App\Controllers\Admin\NavigationController::class, 'save'],
    'admin/navigation/delete'  => [\App\Controllers\Admin\NavigationController::class, 'delete'],
    'admin/navigation/delete-item' => [\App\Controllers\Admin\NavigationController::class, 'deleteItem'],
    'admin/navigation/upload-banner' => [\App\Controllers\Admin\NavigationController::class, 'uploadBanner'],

    // Admin Blogs CRUD
    'admin/blogs'                  => [\App\Controllers\Admin\BlogController::class, 'index'],
    'admin/blogs.php'              => [\App\Controllers\Admin\BlogController::class, 'index'],
    'admin/blogs/create'           => [\App\Controllers\Admin\BlogController::class, 'create'],
    'admin/blogs/add'              => [\App\Controllers\Admin\BlogController::class, 'create'],
    'admin/add-blog'               => [\App\Controllers\Admin\BlogController::class, 'create'],
    'admin/blogs/store'            => [\App\Controllers\Admin\BlogController::class, 'store'],
    'admin/blogs/edit'             => [\App\Controllers\Admin\BlogController::class, 'edit'],
    'admin/edit-blog'              => [\App\Controllers\Admin\BlogController::class, 'edit'],
    'admin/blogs/update'           => [\App\Controllers\Admin\BlogController::class, 'update'],
    'admin/blogs/delete'           => [\App\Controllers\Admin\BlogController::class, 'delete'],

    // Admin Blog Categories CRUD
    'admin/blog-categories'        => [\App\Controllers\Admin\BlogController::class, 'categories'],
    'admin/blog-categories.php'    => [\App\Controllers\Admin\BlogController::class, 'categories'],
    'admin/blog-categories/store'  => [\App\Controllers\Admin\BlogController::class, 'storeCategory'],
    'admin/blog-categories/update' => [\App\Controllers\Admin\BlogController::class, 'updateCategory'],
    'admin/blog-categories/delete' => [\App\Controllers\Admin\BlogController::class, 'deleteCategory'],

    // Admin UI / Theme Sections Management
    'admin/theme-sections'                    => [\App\Controllers\Admin\ThemeSectionController::class, 'index'],
    'admin/theme-sections.php'                => [\App\Controllers\Admin\ThemeSectionController::class, 'index'],
    'admin/ui-sections'                       => [\App\Controllers\Admin\ThemeSectionController::class, 'index'],
    'admin/ui-sections.php'                   => [\App\Controllers\Admin\ThemeSectionController::class, 'index'],
    'admin/theme-sections/save-announcements' => [\App\Controllers\Admin\ThemeSectionController::class, 'saveAnnouncements'],
    'admin/theme-sections/save-hero'          => [\App\Controllers\Admin\ThemeSectionController::class, 'saveHero'],
    'admin/theme-sections/save-twin-banners'  => [\App\Controllers\Admin\ThemeSectionController::class, 'saveTwinBanners'],
    'admin/theme-sections/save-seo-content'   => [\App\Controllers\Admin\ThemeSectionController::class, 'saveSeoContent'],

    // Customer Reviews API
    'api/reviews/submit'                      => [\App\Controllers\ProductController::class, 'submitReview'],

    // Admin Customer Reviews CRUD
    'admin/reviews'                           => [\App\Controllers\Admin\ReviewController::class, 'index'],
    'admin/reviews.php'                       => [\App\Controllers\Admin\ReviewController::class, 'index'],
    'admin/reviews/store'                     => [\App\Controllers\Admin\ReviewController::class, 'store'],
    'admin/reviews/update'                    => [\App\Controllers\Admin\ReviewController::class, 'update'],
    'admin/reviews/status'                    => [\App\Controllers\Admin\ReviewController::class, 'updateStatus'],
    'admin/reviews/delete'                    => [\App\Controllers\Admin\ReviewController::class, 'delete'],

    // Admin Coupons & Discounts CRUD
    'admin/coupons'                           => [\App\Controllers\Admin\CouponController::class, 'index'],
    'admin/coupons.php'                       => [\App\Controllers\Admin\CouponController::class, 'index'],
    'admin/coupons/store'                     => [\App\Controllers\Admin\CouponController::class, 'store'],
    'admin/coupons/update'                    => [\App\Controllers\Admin\CouponController::class, 'update'],
    'admin/coupons/toggle'                    => [\App\Controllers\Admin\CouponController::class, 'toggle'],
    'admin/coupons/delete'                    => [\App\Controllers\Admin\CouponController::class, 'delete'],

    // Admin Store & Checkout Settings
    'admin/settings'                          => [\App\Controllers\Admin\SettingController::class, 'index'],
    'admin/settings.php'                      => [\App\Controllers\Admin\SettingController::class, 'index'],
    'admin/settings/save'                     => [\App\Controllers\Admin\SettingController::class, 'save'],

    // Admin E-Commerce Analytics & Reports
    'admin/analytics'                         => [\App\Controllers\Admin\AnalyticsController::class, 'index'],
    'admin/analytics.php'                     => [\App\Controllers\Admin\AnalyticsController::class, 'index'],

    // Front-End Telemetry Beacon API
    'api/analytics/event'                     => [\App\Controllers\AnalyticsApiController::class, 'recordEvent'],
];