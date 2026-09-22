<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\LoginController as AdminLoginController;

/*
|--------------------------------------------------------------------------
| Backend Entry Route
|--------------------------------------------------------------------------
*/

Route::get('/', [AdminLoginController::class, 'showLoginForm'])
    ->name('home');

Route::get('/products', [AdminLoginController::class, 'redirectToLogin'])
    ->name('products.index');

Route::get('/products/{slug}', [AdminLoginController::class, 'redirectToLogin'])
    ->name('products.show');

Route::get('/cart', [AdminLoginController::class, 'redirectToLogin'])
    ->name('cart.index');

Route::post('/cart/add/{product}', [AdminLoginController::class, 'redirectToLogin'])
    ->name('cart.add');

Route::post('/cart/buy-now/{product}', [AdminLoginController::class, 'redirectToLogin'])
    ->name('cart.buyNow');

Route::post('/cart/remove/{product}', [AdminLoginController::class, 'redirectToLogin'])
    ->name('cart.remove');

Route::get('/category/{slug}', [AdminLoginController::class, 'redirectToLogin'])
    ->name('categories.show');

Route::get('/page/{slug}', [AdminLoginController::class, 'redirectToLogin'])
    ->name('pages.show');

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

Route::get('/register', [AdminLoginController::class, 'redirectToLogin'])->name('register');
Route::post('/register', [AdminLoginController::class, 'redirectToLogin'])->name('register.post');
Route::get('/login', [AdminLoginController::class, 'redirectToLogin'])->name('login');
Route::post('/login', [AdminLoginController::class, 'redirectToLogin'])->name('login.post');
Route::get('/my-account', [AdminLoginController::class, 'redirectToLogin'])->name('account');
Route::post('/logout', [AdminLoginController::class, 'redirectToLogin'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->group(base_path('routes/admin.php'));

