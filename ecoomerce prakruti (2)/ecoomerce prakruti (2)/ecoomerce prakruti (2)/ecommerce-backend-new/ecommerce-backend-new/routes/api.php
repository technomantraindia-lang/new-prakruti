<?php

use App\Http\Controllers\Api\V1\AddressApiController;
use App\Http\Controllers\Api\V1\CartApiController;
use App\Http\Controllers\Api\V1\CatalogMetaApiController;
use App\Http\Controllers\Api\V1\CategoryApiController;
use App\Http\Controllers\Api\V1\CheckoutApiController;
use App\Http\Controllers\Api\V1\ConsultationRequestApiController;
use App\Http\Controllers\Api\V1\CustomerAuthApiController;
use App\Http\Controllers\Api\V1\CustomerOrderApiController;
use App\Http\Controllers\Api\V1\FamilyPackApiController;
use App\Http\Controllers\Api\V1\InquiryApiController;
use App\Http\Controllers\Api\V1\ProductApiController;
use App\Http\Controllers\Api\V1\ReviewApiController;
use App\Http\Controllers\Api\V1\ShippingTaxApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public & Customer API V1 Routes
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->middleware(['throttle:api', 'api.optional'])->group(function () {

    // Public Product & Catalog APIs
    Route::get('/products', [ProductApiController::class, 'index']);
    Route::get('/products/{product}/reviews', [ReviewApiController::class, 'productReviews']);
    Route::post('/products/{product}/reviews', [ReviewApiController::class, 'storeProductReview']);
    Route::get('/products/{slug}', [ProductApiController::class, 'show']);
    Route::get('/products/{product}/variations', [ProductApiController::class, 'variations']);

    Route::get('/categories', [CategoryApiController::class, 'index']);
    Route::get('/categories/{slug}', [CategoryApiController::class, 'show']);
    Route::get('/brands', [CatalogMetaApiController::class, 'brands']);
    Route::get('/attributes', [CatalogMetaApiController::class, 'attributes']);


    // Public Shipping & Tax APIs
    Route::get('/shipping-methods', [ShippingTaxApiController::class, 'shippingMethods']);
    Route::get('/taxes', [ShippingTaxApiController::class, 'taxes']);

    // Cart APIs
    Route::get('/cart', [CartApiController::class, 'index']);
    Route::post('/cart/items', [CartApiController::class, 'store']);
    Route::put('/cart/items/{id}', [CartApiController::class, 'update']);
    Route::delete('/cart/items/{id}', [CartApiController::class, 'destroy']);
    Route::delete('/cart', [CartApiController::class, 'clear']);


    // Public Inquiry / Contact Form API
    Route::post('/inquiries', [InquiryApiController::class, 'store']);
    Route::post('/consultation-requests', [ConsultationRequestApiController::class, 'store']);
    Route::get('/testimonials', [ReviewApiController::class, 'testimonials']);
    Route::post('/testimonials', [ReviewApiController::class, 'storeTestimonial']);

    // Customer Authentication APIs (Strictly Rate-Limited)
    Route::post('/register', [CustomerAuthApiController::class, 'register'])->middleware('throttle:login');
    Route::post('/login', [CustomerAuthApiController::class, 'login'])->middleware('throttle:login');

    // Authenticated Customer APIs
    Route::middleware(\App\Http\Middleware\AuthenticateApiToken::class)->group(function () {
        Route::post('/logout', [CustomerAuthApiController::class, 'logout']);
        Route::get('/me', [CustomerAuthApiController::class, 'me']);
        Route::put('/me', [CustomerAuthApiController::class, 'updateProfile']);

        // Orders History
        Route::post('/checkout', [CheckoutApiController::class, 'store']);
        Route::post('/orders', [CustomerOrderApiController::class, 'store']);
        Route::get('/orders', [CustomerOrderApiController::class, 'index']);
        Route::get('/orders/{id}', [CustomerOrderApiController::class, 'show']);
        Route::post('/orders/{id}/cancel', [CustomerOrderApiController::class, 'cancel']);

        // Family Pack
        Route::get('/family-packs/latest', [FamilyPackApiController::class, 'latest']);
        Route::post('/family-packs', [FamilyPackApiController::class, 'store']);

        // Customer Addresses
        Route::get('/addresses', [AddressApiController::class, 'index']);
        Route::post('/addresses', [AddressApiController::class, 'store']);
    });
});
