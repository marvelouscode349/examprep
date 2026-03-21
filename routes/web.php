<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;

Route::prefix('admin')->middleware([
    'admin'])->group(function () {
    Route::get('/',                                  [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users',                             [AdminController::class, 'users'])->name('admin.users');
    Route::post('/users/{user}/premium',             [AdminController::class, 'makeUserPremium'])->name('admin.users.premium');
    Route::post('/users/{user}/ban',                 [AdminController::class, 'banUser'])->name('admin.users.ban');
    Route::get('/discount-codes',                    [AdminController::class, 'discountCodes'])->name('admin.codes');
    Route::post('/discount-codes',                   [AdminController::class, 'createDiscountCode'])->name('admin.codes.create');
    Route::post('/discount-codes/{code}/toggle',     [AdminController::class, 'toggleDiscountCode'])->name('admin.codes.toggle');
    Route::delete('/discount-codes/{code}',          [AdminController::class, 'deleteDiscountCode'])->name('admin.codes.delete');
    Route::get('/discount-codes/generate',           [AdminController::class, 'generateDiscountCode'])->name('admin.codes.generate');
    Route::get('/revenue',                           [AdminController::class, 'revenue'])->name('admin.revenue');
    Route::get('/marketers',                         [AdminController::class, 'marketers'])->name('admin.marketers');
    Route::post('/marketers',                        [AdminController::class, 'createMarketer'])->name('admin.marketers.create');
    Route::post('/marketers/{marketer}/toggle',      [AdminController::class, 'toggleMarketer'])->name('admin.marketers.toggle');
    Route::post('/marketers/{marketer}/pay',         [AdminController::class, 'payMarketer'])->name('admin.marketers.pay');
});

// Admin login — outside middleware group
Route::get('/admin/login',  [AdminController::class, 'loginPage'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'loginSubmit'])->name('admin.login.submit');
Route::post('/admin/logout',[AdminController::class, 'logout'])->name('admin.logout');

// Serve the frontend for all non-API routes
Route::get('/', function () {
    return file_get_contents(public_path('index.html'));
});

// Route::get('/{any}', function () {
//     return file_get_contents(public_path('index.html'));
// })->where('any', '^(?!api).*$');