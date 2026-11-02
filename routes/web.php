<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AppearanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatSettingsController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoginOtpController;
use App\Http\Controllers\PageController;
use App\Http\Middleware\EnsureActiveAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'show'])->name('home');
Route::post('/api/chat', [ChatController::class, 'store'])->middleware('throttle:chat')->name('chat.store');
Route::delete('/api/chat', [ChatController::class, 'destroy'])->middleware('throttle:chat')->name('chat.clear');
Route::get('/{page}', [PageController::class, 'show'])
    ->whereIn('page', array_values(array_diff(array_keys(config('site.pages')), ['home'])))
    ->name('page');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.send');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/login/otp', [LoginOtpController::class, 'show'])->name('login.otp');
    Route::post('/login/otp/request', [LoginOtpController::class, 'store'])->middleware('throttle:5,1')->name('login.otp.send');
    Route::post('/login/otp', [LoginOtpController::class, 'verify'])->middleware('throttle:otp-verify')->name('login.otp.verify');
});

Route::middleware(['auth', EnsureActiveAdmin::class])->group(function () {
    Route::view('/admin', 'admin.dashboard')->name('admin.dashboard');
    Route::view('/admin/appearance', 'admin.appearance')->name('admin.appearance');
    Route::put('/admin/appearance', [AppearanceController::class, 'update'])->name('admin.appearance.update');
    Route::view('/admin/themes', 'admin.themes')->name('admin.themes');
    Route::put('/admin/themes', [AppearanceController::class, 'updateLayout'])->name('admin.themes.update');

    Route::get('/admin/pages', [PageController::class, 'index'])->name('admin.pages');
    Route::get('/admin/pages/{page}', [PageController::class, 'edit'])->name('admin.pages.edit');
    Route::put('/admin/pages/{page}', [PageController::class, 'update'])->name('admin.pages.update');

    Route::get('/admin/messages', [ContactController::class, 'index'])->name('admin.messages');
    Route::get('/admin/messages/{message}', [ContactController::class, 'show'])->name('admin.messages.show');
    Route::delete('/admin/messages/{message}', [ContactController::class, 'destroy'])->name('admin.messages.destroy');

    Route::middleware('can:super-admin')->group(function () {
        Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users');
        Route::post('/admin/users', [AdminUserController::class, 'store'])->middleware('throttle:10,1')->name('admin.users.store');
        Route::put('/admin/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

        Route::get('/admin/chat', [ChatSettingsController::class, 'edit'])->name('admin.chat');
        Route::put('/admin/chat', [ChatSettingsController::class, 'update'])->middleware('throttle:5,1')->name('admin.chat.update');
        Route::post('/admin/chat/test', [ChatSettingsController::class, 'test'])->middleware('throttle:5,1')->name('admin.chat.test');
        Route::delete('/admin/chat', [ChatSettingsController::class, 'destroy'])->name('admin.chat.destroy');

        Route::get('/admin/content/{group?}', [ContentController::class, 'edit'])->name('admin.content');
        Route::put('/admin/content/{group}', [ContentController::class, 'update'])->name('admin.content.update');

        Route::get('/admin/items/{type}', [ItemController::class, 'index'])->name('admin.items.index');
        Route::get('/admin/items/{type}/create', [ItemController::class, 'create'])->name('admin.items.create');
        Route::post('/admin/items/{type}', [ItemController::class, 'store'])->name('admin.items.store');
        Route::get('/admin/items/{type}/{item}/edit', [ItemController::class, 'edit'])->name('admin.items.edit');
        Route::put('/admin/items/{type}/{item}', [ItemController::class, 'update'])->name('admin.items.update');
        Route::delete('/admin/items/{type}/{item}', [ItemController::class, 'destroy'])->name('admin.items.destroy');

        Route::view('/admin/account', 'admin.account')->name('admin.account');
        Route::put('/admin/account/profile', [AccountController::class, 'updateProfile'])->name('admin.account.profile');
        Route::put('/admin/account/password', [AccountController::class, 'updatePassword'])->name('admin.account.password');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
