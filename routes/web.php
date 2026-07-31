<?php

declare(strict_types=1);

use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ProductPageController;
use App\Http\Controllers\Web\UserActivityLogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', '/dashboard');
Route::get('/login', fn () => Inertia::render('Auth/Login'))->name('login');
Route::post('/login', function (Request $request) {
    if (Auth::attempt($request->only('email', 'password'))) {
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    return back()->withErrors(['email' => 'Invalid credentials.']);
});
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
})->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/logs/user-activity', UserActivityLogController::class)->name('logs.user-activity');
    Route::get('/operations/restock', fn () => Inertia::render('Operations/ProductRestock'))->name('operations.restock');
    Route::get('/operations/delete', fn () => Inertia::render('Operations/ProductDelete'))->name('operations.delete');
    Route::get('/operations/restore', fn () => Inertia::render('Operations/ProductRestore'))->name('operations.restore');
    Route::get('/operations/purge', fn () => Inertia::render('Operations/ProductPurge'))->name('operations.purge');
    Route::resource('products', ProductPageController::class)->except(['restore']);
});
