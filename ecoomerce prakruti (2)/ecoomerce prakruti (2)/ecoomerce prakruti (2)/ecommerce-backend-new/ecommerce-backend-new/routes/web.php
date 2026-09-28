<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CategoryController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\LoginController as FrontendLoginController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\RegisterController;

/*
|--------------------------------------------------------------------------
| Backend entry point
|--------------------------------------------------------------------------
|
| The customer storefront is served by the separate React application on
| port 5173. The Laravel application is the admin backend, so its root URL
| must never open the legacy storefront view.
|
*/
Route::get('/', fn () => redirect()->route('admin.login'))->name('backend.home');

/* Legacy storefront routes kept available for existing links. */

Route::get('/storefront', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/buy-now/{product}', [CartController::class, 'buyNow'])->name('cart.buyNow');
Route::post('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/page/{slug}', [PageController::class, 'show'])->name('pages.show');

Route::get('/media/{path}', function (string $path) {
    $relativePath = ltrim($path, '/');
    if (str_contains($relativePath, '..')) {
        abort(404);
    }

    $absolutePath = storage_path('app/public/' . $relativePath);
    $resolvedPath = realpath($absolutePath);
    $storageRoot = realpath(storage_path('app/public'));

    if (! $resolvedPath || ! $storageRoot || ! str_starts_with($resolvedPath, $storageRoot) || ! is_file($resolvedPath)) {
        abort(404);
    }

    return response()->file($resolvedPath);
})->where('path', '.*')->name('media.file');

/*
|--------------------------------------------------------------------------
| Removed Storefront Routes
|--------------------------------------------------------------------------
|
| This Laravel app now serves the backend/admin interface only.
|
*/

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
Route::get('/login', [FrontendLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [FrontendLoginController::class, 'login'])->name('login.post');
Route::get('/my-account', fn () => view('frontend.account'))->name('account');
Route::post('/logout', [FrontendLoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->group(base_path('routes/admin.php'));

