<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =============================================
// WEB ROUTES (Frontend)
// =============================================
$routes->get('/', 'Web\HomeController::index');
$routes->get('/produk', 'Web\HomeController::products');
$routes->get('/produk/(:segment)', 'Web\HomeController::productDetail/$1');
$routes->get('/kategori/(:segment)', 'Web\HomeController::category/$1');
$routes->get('/search', 'Web\HomeController::search');

// Auth Pages
$routes->get('/login', 'Web\AuthWebController::login');
$routes->post('/login', 'Web\AuthWebController::doLogin');
$routes->get('/register', 'Web\AuthWebController::register');
$routes->post('/register', 'Web\AuthWebController::doRegister');
$routes->get('/logout', 'Web\AuthWebController::logout');
$routes->get('/verify/(:segment)', 'Web\AuthWebController::verify/$1');

// Customer Dashboard
$routes->group('customer', ['filter' => 'sessionauth:customer,admin,superuser'], function ($routes) {
    $routes->get('dashboard', 'Web\CustomerController::dashboard');
    $routes->get('profile', 'Web\CustomerController::profile');
    $routes->post('profile/update', 'Web\CustomerController::updateProfile');
    $routes->match(['get', 'post'], 'beli/(:num)', 'Web\CustomerController::beli/$1');
    $routes->get('cart', 'Web\CustomerController::cart');
    $routes->post('cart/add', 'Web\CustomerController::addToCart');
    $routes->post('cart/update', 'Web\CustomerController::updateCart');
    $routes->post('cart/remove', 'Web\CustomerController::removeFromCart');
    $routes->get('checkout', 'Web\CustomerController::checkout');
    $routes->post('checkout/process', 'Web\CustomerController::processCheckout');
    $routes->get('orders', 'Web\CustomerController::orders');
    $routes->get('orders/(:num)', 'Web\CustomerController::orderDetail/$1');
    $routes->get('invoice/(:num)', 'Web\CustomerController::invoice/$1');
});

// Admin Dashboard
$routes->group('admin', ['filter' => 'sessionauth:admin,superuser'], function ($routes) {
    $routes->get('/', 'Web\AdminController::dashboard');
    $routes->get('dashboard', 'Web\AdminController::dashboard');

    // Products
    $routes->get('products', 'Web\AdminController::products');
    $routes->get('products/create', 'Web\AdminController::createProduct');
    $routes->post('products/store', 'Web\AdminController::storeProduct');
    $routes->get('products/edit/(:num)', 'Web\AdminController::editProduct/$1');
    $routes->post('products/update/(:num)', 'Web\AdminController::updateProduct/$1');
    $routes->post('products/delete/(:num)', 'Web\AdminController::deleteProduct/$1');

    // Categories
    $routes->get('categories', 'Web\AdminController::categories');
    $routes->post('categories/store', 'Web\AdminController::storeCategory');
    $routes->post('categories/update/(:num)', 'Web\AdminController::updateCategory/$1');
    $routes->post('categories/delete/(:num)', 'Web\AdminController::deleteCategory/$1');

    // Orders
    $routes->get('orders', 'Web\AdminController::orders');
    $routes->get('orders/(:num)', 'Web\AdminController::orderDetail/$1');
    $routes->post('orders/(:num)/status', 'Web\AdminController::updateOrderStatus/$1');
    $routes->post('orders/(:num)/resi', 'Web\AdminController::updateResi/$1');

    // Reports
    $routes->get('reports', 'Web\AdminController::reports');
    $routes->get('reports/sales', 'Web\AdminController::salesReport');
    $routes->get('reports/profit', 'Web\AdminController::profitReport');
    $routes->get('reports/stock', 'Web\AdminController::stockReport');
});

// Superuser
$routes->group('superuser', ['filter' => 'sessionauth:superuser'], function ($routes) {
    $routes->get('/', 'Web\SuperuserController::dashboard');
    $routes->get('dashboard', 'Web\SuperuserController::dashboard');
    $routes->get('admins', 'Web\SuperuserController::admins');
    $routes->post('admins/store', 'Web\SuperuserController::storeAdmin');
    $routes->post('admins/update/(:num)', 'Web\SuperuserController::updateAdmin/$1');
    $routes->post('admins/delete/(:num)', 'Web\SuperuserController::deleteAdmin/$1');
    $routes->get('customers', 'Web\SuperuserController::customers');
    $routes->post('customers/toggle/(:num)', 'Web\SuperuserController::toggleCustomer/$1');
    $routes->get('settings', 'Web\SuperuserController::settings');
});

// =============================================
// API ROUTES (REST API)
// =============================================
$routes->group('api', function ($routes) {
    // Auth
    $routes->post('register', 'Api\AuthController::register');
    $routes->post('login', 'Api\AuthController::login');
    $routes->get('verify/(:segment)', 'Api\AuthController::verify/$1');
    $routes->post('resend-verification', 'Api\AuthController::resendVerification');
    $routes->get('confirm-password/(:segment)', 'Api\AuthController::confirmPassword/$1');

    // Protected routes
    $routes->group('', ['filter' => 'jwt'], function ($routes) {
        $routes->get('profile', 'Api\AuthController::profile');
        $routes->post('logout', 'Api\AuthController::logout');
        $routes->put('profile', 'Api\AuthController::updateProfile');

        // Products (public read, admin write)
        $routes->get('products', 'Api\ProductController::index');
        $routes->get('products/(:num)', 'Api\ProductController::show/$1');

        // Categories
        $routes->get('categories', 'Api\CategoryController::index');
        $routes->get('categories/(:num)', 'Api\CategoryController::show/$1');

        // Customer Orders
        $routes->post('orders', 'Api\OrderController::create');
        $routes->get('promos/available', 'Api\PromoController::getAvailablePromos');
        $routes->post('promos/validate', 'Api\PromoController::validatePromo');
        $routes->get('orders/user', 'Api\OrderController::userOrders');
        $routes->get('orders/(:num)', 'Api\OrderController::show/$1');
        $routes->post('orders/(:num)/pay', 'Api\OrderController::pay/$1');
        $routes->post('orders/(:num)/cancel', 'Api\OrderController::cancel/$1');

        // Admin/Superuser Protected Routes
        $routes->group('', ['filter' => 'role:admin,superuser'], function ($routes) {
            $routes->post('products', 'Api\ProductController::create');
            $routes->post('products/(:num)', 'Api\ProductController::update/$1');
            $routes->delete('products/(:num)', 'Api\ProductController::delete/$1');

            $routes->post('categories', 'Api\CategoryController::create');
            $routes->put('categories/(:num)', 'Api\CategoryController::update/$1');
            $routes->delete('categories/(:num)', 'Api\CategoryController::delete/$1');

            $routes->get('orders', 'Api\OrderController::index');
            $routes->put('orders/(:num)/status', 'Api\OrderController::updateStatus/$1');

            // Reports
            $routes->get('reports/profit', 'Api\ReportController::profit');
            $routes->get('reports/sales', 'Api\ReportController::sales');
            $routes->get('reports/stock', 'Api\ReportController::stock');

            // Promos / Vouchers
            $routes->get('promos', 'Api\PromoController::index');
            $routes->post('promos', 'Api\PromoController::create');
            $routes->put('promos/(:num)', 'Api\PromoController::update/$1');
            $routes->delete('promos/(:num)', 'Api\PromoController::delete/$1');

            // Customers
            $routes->get('customers', 'Api\UserController::index');
            $routes->put('customers/(:num)/toggle', 'Api\UserController::update/$1');

            // Finance Advanced
            $routes->group('finance', function ($routes) {
                $routes->get('growth', 'Api\FinanceController::growth');
                $routes->get('chart/daily', 'Api\FinanceController::dailyChart');
                $routes->get('chart/monthly', 'Api\FinanceController::monthlyChart');
                $routes->get('export/pdf', 'Api\FinanceController::exportPdf');
                $routes->get('export/excel', 'Api\FinanceController::exportExcel');
            });
        });

        // Superuser-only Routes
        $routes->group('', ['filter' => 'role:superuser'], function ($routes) {
            $routes->get('users', 'Api\UserController::index');
            $routes->post('users', 'Api\UserController::create');
            $routes->put('users/(:num)', 'Api\UserController::update/$1');
            $routes->delete('users/(:num)', 'Api\UserController::delete/$1');
        });
    });

    // Public product/category routes (no auth needed)
    $routes->get('public/products', 'Api\ProductController::index');
    $routes->get('public/products/(:num)', 'Api\ProductController::show/$1');
    $routes->get('public/categories', 'Api\CategoryController::index');
    $routes->get('public/products/search', 'Api\ProductController::search');
});
