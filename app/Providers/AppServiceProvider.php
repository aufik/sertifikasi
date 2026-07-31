<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Repositories\Eloquent\ProductRepository;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
    }

    public function boot(): void
    {
        date_default_timezone_set('Asia/Jakarta');

        RateLimiter::for('api', fn (Request $r) => Limit::perMinute(60)->by($r->user()?->id ?: $r->ip()));
        RateLimiter::for('mutations', fn (Request $r) => Limit::perMinute(10)->by($r->user()?->id ?: $r->ip()));
        RateLimiter::for('health', fn (Request $r) => Limit::perMinute(120)->by($r->ip()));
    }
}
