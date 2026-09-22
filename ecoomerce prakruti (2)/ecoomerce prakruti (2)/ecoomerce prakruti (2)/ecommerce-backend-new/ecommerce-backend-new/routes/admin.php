<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ConsultationRequestController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ShippingController;
use App\Models\ShippingMethod;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::bind('shipping', fn ($value) => ShippingMethod::findOrFail($value));
Route::bind('customer', fn ($value) => User::findOrFail($value));
Route::bind('user', fn ($value) => User::findOrFail($value));

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::middleware('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('permission:dashboard.view')->name('dashboard');
    Route::get('/search', [\App\Http\Controllers\Admin\GlobalSearchController::class, 'search'])->name('search');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Products & Variations
    Route::get('/products-bulk/create', [ProductController::class, 'bulkCreate'])->middleware('permission:products.bulk_manage')->name('products.bulkCreate');
    Route::post('/products-bulk/store', [ProductController::class, 'bulkStore'])->middleware('permission:products.bulk_manage')->name('products.bulkStore');
    Route::get('/products-import', [ProductController::class, 'importForm'])->middleware('permission:products.import')->name('products.import');
    Route::post('/products-import', [ProductController::class, 'importStore'])->middleware('permission:products.import')->name('products.importStore');
    Route::patch('/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->middleware('permission:products.edit')->name('products.toggleStatus');
    Route::patch('/products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->middleware('permission:products.edit')->name('products.toggleFeatured');
    
    Route::get('/products/{product}/variations', [\App\Http\Controllers\Admin\ProductVariationController::class, 'index'])->middleware('permission:products.view')->name('products.variations.index');
    Route::post('/products/{product}/variations', [\App\Http\Controllers\Admin\ProductVariationController::class, 'store'])->middleware('permission:products.edit')->name('products.variations.store');
    Route::put('/products/{product}/variations/{variation}', [\App\Http\Controllers\Admin\ProductVariationController::class, 'update'])->middleware('permission:products.edit')->name('products.variations.update');
    Route::delete('/products/{product}/variations/{variation}', [\App\Http\Controllers\Admin\ProductVariationController::class, 'destroy'])->middleware('permission:products.edit')->name('products.variations.destroy');

    Route::resource('products', ProductController::class)->middleware('permission:products.view');

    // Categories
    Route::resource('categories', CategoryController::class)->except(['show'])->middleware('permission:categories.view');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->middleware('permission:orders.view')->name('orders.index');
    Route::post('/orders/bulk-action', [OrderController::class, 'bulkAction'])->middleware('permission:orders.edit')->name('orders.bulkAction');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->middleware('permission:orders.view')->name('orders.invoice');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->middleware('permission:orders.view')->name('orders.show');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->middleware('permission:orders.edit')->name('orders.update');
    Route::post('/orders/{order}/verify-payment', [OrderController::class, 'verifyPaymentManually'])->middleware('permission:orders.edit')->name('orders.verifyPayment');

    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->middleware('permission:customers.view')->name('customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->middleware('permission:customers.view')->name('customers.show');

    Route::resource('shipping', ShippingController::class)->except(['show'])->middleware('permission:shipping.view');

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->middleware('permission:payments.view')->name('payments.index');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->middleware('permission:payments.view')->name('payments.show');


    // Inquiries
    Route::get('/inquiries', [InquiryController::class, 'index'])->middleware('permission:inquiries.view')->name('inquiries.index');
    Route::get('/inquiries/{inquiry}', [InquiryController::class, 'show'])->middleware('permission:inquiries.view')->name('inquiries.show');
    Route::put('/inquiries/{inquiry}', [InquiryController::class, 'update'])->middleware('permission:inquiries.manage')->name('inquiries.update');

    Route::get('/consultations', [ConsultationRequestController::class, 'index'])->name('consultations.index');
    Route::get('/consultations/{consultation}', [ConsultationRequestController::class, 'show'])->name('consultations.show');
    Route::put('/consultations/{consultation}', [ConsultationRequestController::class, 'update'])->name('consultations.update');

    // Frontend Reviews / Testimonials
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::patch('/reviews/product/{review}', [ReviewController::class, 'updateProductReview'])->name('reviews.product.update');
    Route::patch('/reviews/testimonial/{testimonial}', [ReviewController::class, 'updateTestimonial'])->name('reviews.testimonial.update');

    // Reports
    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->middleware('permission:settings.view')->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->middleware('permission:settings.manage')->name('settings.update');


    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('notifications.destroy');

    // System Queue & Failed Jobs (Super Admin Restricted)
    Route::get('/system-health', [\App\Http\Controllers\Admin\SystemHealthController::class, 'index'])->name('system-health.index');
    Route::get('/system/failed-jobs', [\App\Http\Controllers\Admin\FailedJobController::class, 'index'])->name('system.failed-jobs.index');
    Route::post('/system/failed-jobs/retry-all', [\App\Http\Controllers\Admin\FailedJobController::class, 'retryAll'])->name('system.failed-jobs.retry-all');
    Route::post('/system/failed-jobs/{id}/retry', [\App\Http\Controllers\Admin\FailedJobController::class, 'retry'])->name('system.failed-jobs.retry');
    Route::delete('/system/failed-jobs/{id}', [\App\Http\Controllers\Admin\FailedJobController::class, 'destroy'])->name('system.failed-jobs.destroy');

    // System Backups (Super Admin Restricted)
    Route::get('/system/backups', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('system.backups.index');
    Route::post('/system/backups', [\App\Http\Controllers\Admin\BackupController::class, 'create'])->name('system.backups.create');
    Route::get('/system/backups/{filename}/download', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('system.backups.download');
    Route::delete('/system/backups/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('system.backups.destroy');
});
