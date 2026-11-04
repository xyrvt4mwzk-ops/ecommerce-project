<?php

use App\Http\Controllers\Admin\AffiliateManagementController as AdminAffiliateManagementController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\Affiliate\AffiliateController;
use App\Http\Controllers\Affiliate\PayoutController as AffiliatePayoutController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Buyer\LibraryController;
use App\Http\Controllers\Buyer\OrderController as BuyerOrderController;
use App\Http\Controllers\Buyer\WishlistController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContentReportController;
use App\Http\Controllers\Creator\ModerationController as CreatorModerationController;
use App\Http\Controllers\Creator\ProductController as CreatorProductController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaywayController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseIntentController;
use App\Http\Controllers\Reviewer\ModerationController;
use App\Http\Controllers\RoleSelectionController;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;

Route::aliasMiddleware('role', CheckRole::class);
Route::aliasMiddleware('permission', CheckPermission::class);

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/search', [ProductController::class, 'index'])->name('search');
Route::get('/categories', [MarketplaceController::class, 'categories'])->name('categories.index');
Route::get('/categories/{category}', [MarketplaceController::class, 'category'])->name('categories.show');
Route::get('/creators', [MarketplaceController::class, 'creators'])->name('creators.index');
Route::get('/creators/{creator}', [MarketplaceController::class, 'creator'])->name('creators.show');
Route::get('/about', [MarketplaceController::class, 'about'])->name('about');
Route::get('/help', [MarketplaceController::class, 'help'])->name('help');
Route::get('/ref/{code}', [AffiliateController::class, 'capture'])->name('affiliate.capture');
Route::post('/payments/payway/callback', [PaywayController::class, 'callback'])->name('payments.payway.callback');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/role-selection', [RoleSelectionController::class, 'create'])->name('role.select');
    Route::post('/role-selection', [RoleSelectionController::class, 'store'])->name('role.store');

    Route::get('/buyer/dashboard', [DashboardController::class, 'buyer'])
        ->middleware('role:buyer')
        ->name('buyer.dashboard');

    Route::get('/buyer/library', [LibraryController::class, 'index'])
        ->middleware(['role:buyer', 'permission:buyer.library.view'])
        ->name('buyer.library.index');

    Route::get('/buyer/wishlist', [WishlistController::class, 'index'])->middleware(['role:buyer', 'permission:buyer.wishlist.manage'])->name('buyer.wishlist.index');
    Route::post('/buyer/wishlist/{product}', [WishlistController::class, 'store'])->middleware(['role:buyer', 'permission:buyer.wishlist.manage'])->name('buyer.wishlist.store');
    Route::delete('/buyer/wishlist/{product}', [WishlistController::class, 'destroy'])->middleware(['role:buyer', 'permission:buyer.wishlist.manage'])->name('buyer.wishlist.destroy');

    Route::get('/buyer/orders', [BuyerOrderController::class, 'index'])
        ->middleware(['role:buyer', 'permission:buyer.orders.view'])
        ->name('buyer.orders.index');

    Route::get('/buyer/orders/{order}', [BuyerOrderController::class, 'show'])
        ->middleware(['role:buyer', 'permission:buyer.orders.view'])
        ->name('buyer.orders.show');

    Route::get('/purchase/{product}/continue', [PurchaseIntentController::class, 'show'])
        ->name('purchase.resume');
    Route::post('/purchase/{product}/coupon', [PurchaseIntentController::class, 'applyCoupon'])
        ->name('purchase.coupon');

    Route::middleware(['role:buyer', 'permission:buyer.cart.manage'])->group(function () {
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/coupon', [CartController::class, 'coupon'])->name('cart.coupon');
        Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
        Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');
    });

    Route::post('/products/{product}/reviews', [ProductReviewController::class, 'store'])
        ->middleware(['role:buyer', 'permission:buyer.reviews.manage'])
        ->name('products.reviews.store');
    Route::delete('/reviews/{review}', [ProductReviewController::class, 'destroy'])
        ->middleware(['role:buyer', 'permission:buyer.reviews.manage'])
        ->name('reviews.destroy');
    Route::post('/products/{product}/reports', [ContentReportController::class, 'store'])
        ->name('content-reports.store');

    Route::get('/creator/dashboard', [DashboardController::class, 'creator'])
        ->middleware('role:creator')
        ->name('creator.dashboard');

    Route::resource('/creator/products', CreatorProductController::class)
        ->except(['show'])
        ->names('creator.products')
        ->middleware(['role:creator', 'permission:creator.products.manage']);
    Route::post('/creator/reviews/{review}/response', [ProductReviewController::class, 'respond'])
        ->middleware(['role:creator', 'permission:creator.reviews.respond'])
        ->name('creator.reviews.respond');
    Route::post('/creator/content-reports/{report}/counter-notice', [ContentReportController::class, 'counterNotice'])
        ->middleware(['role:creator', 'permission:creator.moderation.respond'])
        ->name('creator.content-reports.counter');
    Route::get('/creator/content-reports', [CreatorModerationController::class, 'index'])
        ->middleware(['role:creator', 'permission:creator.moderation.respond'])
        ->name('creator.content-reports.index');

    Route::get('/affiliate/dashboard', [AffiliateController::class, 'index'])
        ->middleware('role:affiliate')
        ->name('affiliate.dashboard');

    Route::get('/affiliate/products', [AffiliateController::class, 'products'])
        ->middleware(['role:affiliate', 'permission:affiliate.dashboard'])
        ->name('affiliate.products.index');

    Route::post('/affiliate/products/{product}/referral', [AffiliateController::class, 'storeReferral'])
        ->middleware(['role:affiliate', 'permission:affiliate.dashboard'])
        ->name('affiliate.products.referral.store');
    Route::get('/affiliate/payouts', [AffiliatePayoutController::class, 'index'])
        ->middleware(['role:affiliate', 'permission:affiliate.payouts.manage'])
        ->name('affiliate.payouts.index');
    Route::post('/affiliate/payouts', [AffiliatePayoutController::class, 'store'])
        ->middleware(['role:affiliate', 'permission:affiliate.payouts.manage'])
        ->name('affiliate.payouts.store');

    Route::get('/reviewer/dashboard', [DashboardController::class, 'reviewer'])
        ->middleware('role:content-reviewer')
        ->name('reviewer.dashboard');
    Route::middleware(['role:content-reviewer', 'permission:reviewer.moderation.manage'])
        ->prefix('reviewer')->name('reviewer.')->group(function () {
            Route::get('/moderation', [ModerationController::class, 'index'])->name('moderation.index');
            Route::get('/moderation/{report}', [ModerationController::class, 'show'])->name('moderation.show');
            Route::post('/moderation/{report}/takedown', [ModerationController::class, 'takeDown'])->name('moderation.takedown');
            Route::post('/moderation/{report}/restore', [ModerationController::class, 'restore'])->name('moderation.restore');
            Route::post('/moderation/{report}/uphold', [ModerationController::class, 'uphold'])->name('moderation.uphold');
            Route::post('/reviews/{review}/hide', [ModerationController::class, 'hideReview'])->name('reviews.hide');
            Route::post('/reviews/{review}/restore', [ModerationController::class, 'restoreReview'])->name('reviews.restore');
        });

    Route::post('/checkout/cart/khqr', [CheckoutController::class, 'startCartKhqrCheckout'])->middleware(['role:buyer', 'permission:buyer.checkout.create'])->name('checkout.cart.khqr');
    Route::post('/checkout/cart/start', [CheckoutController::class, 'startCartCheckout'])->middleware(['role:buyer', 'permission:buyer.checkout.create'])->name('checkout.cart.start');
    Route::post('/checkout/{product}/khqr', [CheckoutController::class, 'startKhqrCheckout'])
        ->middleware(['role:buyer', 'permission:buyer.checkout.create'])
        ->name('checkout.khqr');
    Route::post('/checkout/{product}/start', [CheckoutController::class, 'startProductCheckout'])
        ->middleware(['role:buyer', 'permission:buyer.checkout.create'])
        ->name('checkout.product.start');

    Route::get('/checkout/{order}/success', [CheckoutController::class, 'success'])
        ->name('checkout.success');

    Route::get('/download/{license}', [DownloadController::class, 'download'])
        ->name('download.license');

    Route::get('/payments/khqr/{payment}', [PaymentController::class, 'khqr'])->name('payments.khqr.show');
    Route::get('/payments/{payment}/status', [PaymentController::class, 'status'])->name('payments.status');
    Route::post('/payments/{payment}/retry', [PaymentController::class, 'retry'])->name('payments.retry');
    Route::post('/payments/{payment}/cancel', [PaymentController::class, 'cancel'])->name('payments.cancel');
    Route::get('/payments/payway/{payment}/complete', [PaywayController::class, 'complete'])->name('payments.payway.complete');
    Route::get('/payments/payway/{payment}/cancel', [PaywayController::class, 'cancelReturn'])->name('payments.payway.cancel-return');

    Route::middleware('role:super-admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::middleware('permission:admin.products.manage')->group(function () {
            Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
            Route::put('/products/{product}/status', [AdminProductController::class, 'updateStatus'])->name('products.status');
        });
        Route::resource('coupons', AdminCouponController::class)
            ->except('show')
            ->middleware('permission:admin.coupons.manage');
        Route::middleware('permission:admin.affiliates.manage')->group(function () {
            Route::get('/affiliates', [AdminAffiliateManagementController::class, 'index'])->name('affiliates.index');
            Route::put('/affiliates/products/{product}/rate', [AdminAffiliateManagementController::class, 'updateRate'])->name('affiliates.products.rate');
            Route::put('/affiliates/{affiliate}', [AdminAffiliateManagementController::class, 'updateAffiliate'])->name('affiliates.update');
            Route::delete('/affiliates/{affiliate}', [AdminAffiliateManagementController::class, 'destroy'])->name('affiliates.destroy');
            Route::put('/affiliate-commissions/{commission}', [AdminAffiliateManagementController::class, 'updateCommission'])->name('affiliate-commissions.update');
            Route::put('/affiliate-payouts/{payout}', [AdminAffiliateManagementController::class, 'updatePayout'])->name('affiliate-payouts.update');
        });
        Route::middleware('permission:admin.orders.view')->group(function () {
            Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        });
        Route::middleware('permission:admin.access.manage')->group(function () {
            Route::get('/users', [UserRoleController::class, 'index'])->name('users.index');
            Route::get('/roles', [AdminRoleController::class, 'index'])->name('roles.index');
            Route::post('/roles', [AdminRoleController::class, 'store'])->name('roles.store');
            Route::post('/users/{user}/roles', [UserRoleController::class, 'assignRole'])->name('users.roles.assign');
        });
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('guest')->group(function () {
    Route::get('auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('google.callback');
});

require __DIR__.'/auth.php';
