<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AppearanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// Public pages: which sections each one shows is managed in Admin → Site pages.
Route::get('/', [PageController::class, 'show'])->name('home');
Route::get('/{page}', [PageController::class, 'show'])
    ->whereIn('page', array_values(array_diff(array_keys(config('site.pages')), ['home'])))
    ->name('page');

// Contact form of the public site: saved for the admin, sender gets a thank-you email.
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.send');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::view('/admin', 'admin.dashboard')->name('admin.dashboard');
    Route::view('/admin/appearance', 'admin.appearance')->name('admin.appearance');
    Route::put('/admin/appearance', [AppearanceController::class, 'update'])->name('admin.appearance.update');
    Route::view('/admin/themes', 'admin.themes')->name('admin.themes');
    Route::put('/admin/themes', [AppearanceController::class, 'updateLayout'])->name('admin.themes.update');

    // Page texts, photo and CV (settings)
    Route::get('/admin/content/{group?}', [ContentController::class, 'edit'])->name('admin.content');
    Route::put('/admin/content/{group}', [ContentController::class, 'update'])->name('admin.content.update');

    // Repeatable content: projects, experiences, education, services, skills, ...
    Route::get('/admin/items/{type}', [ItemController::class, 'index'])->name('admin.items.index');
    Route::get('/admin/items/{type}/create', [ItemController::class, 'create'])->name('admin.items.create');
    Route::post('/admin/items/{type}', [ItemController::class, 'store'])->name('admin.items.store');
    Route::get('/admin/items/{type}/{item}/edit', [ItemController::class, 'edit'])->name('admin.items.edit');
    Route::put('/admin/items/{type}/{item}', [ItemController::class, 'update'])->name('admin.items.update');
    Route::delete('/admin/items/{type}/{item}', [ItemController::class, 'destroy'])->name('admin.items.destroy');

    // Pages: menu name, browser title and sections of each public page
    Route::get('/admin/pages', [PageController::class, 'index'])->name('admin.pages');
    Route::get('/admin/pages/{page}', [PageController::class, 'edit'])->name('admin.pages.edit');
    Route::put('/admin/pages/{page}', [PageController::class, 'update'])->name('admin.pages.update');

    // Messages received through the contact form
    Route::get('/admin/messages', [ContactController::class, 'index'])->name('admin.messages');
    Route::get('/admin/messages/{message}', [ContactController::class, 'show'])->name('admin.messages.show');
    Route::delete('/admin/messages/{message}', [ContactController::class, 'destroy'])->name('admin.messages.destroy');

    Route::view('/admin/account', 'admin.account')->name('admin.account');
    Route::put('/admin/account/profile', [AccountController::class, 'updateProfile'])->name('admin.account.profile');
    Route::put('/admin/account/password', [AccountController::class, 'updatePassword'])->name('admin.account.password');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
