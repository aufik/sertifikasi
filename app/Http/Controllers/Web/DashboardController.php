<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAudit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

final class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $totalProducts = Product::query()->count();
        $activeProducts = Product::query()->where('status', ProductStatus::Active->value)->count();
        $draftProducts = Product::query()->where('status', ProductStatus::Draft->value)->count();
        $archivedProducts = Product::query()->where('status', ProductStatus::Archived->value)->count();
        $lowStockCount = Product::query()->where('stock', '<=', 5)->count();
        $totalInventoryValue = (int) Product::query()->sum(DB::raw('price * stock'));

        // Hitung sebaran kategori untuk grafik kecil di dashboard.
        $categoryBreakdown = Category::query()
            ->withCount('products')
            ->get()
            ->map(fn (Category $c) => [
                'name' => $c->name,
                'count' => $c->products_count,
            ]);

        // Ambil aktivitas audit 7 hari terakhir untuk grafik aktivitas harian.
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $audits = ProductAudit::query()
            ->where('created_at', '>=', $startDate)
            ->get();

        $dailyActivity = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayName = $date->format('D');
            $dateStr = $date->format('Y-m-d');

            $dayAudits = $audits->filter(fn (ProductAudit $audit) => $audit->created_at?->format('Y-m-d') === $dateStr);

            $dailyActivity[] = [
                'day' => $dayName,
                'date' => $date->format('d M'),
                'created' => $dayAudits->where('event', 'ProductCreated')->count(),
                'updated' => $dayAudits->where('event', 'ProductUpdated')->count(),
                'deleted' => $dayAudits->where('event', 'ProductDeleted')->count(),
                'restocked' => $dayAudits->where('event', 'ProductRestocked')->count(),
                'restored' => $dayAudits->where('event', 'ProductRestored')->count(),
                'purged' => $dayAudits->where('event', 'ProductPurged')->count(),
                'total' => $dayAudits->count(),
            ];
        }

        // Ambil log bisnis terbaru dari database untuk activity log kecil di dashboard.
        $recentAudits = ProductAudit::query()
            ->latest()
            ->take(8)
            ->get()
            ->map(function (ProductAudit $audit) {
                /** @var array<string, mixed>|null $payload */
                $payload = $audit->payload;
                $productName = is_array($payload) && isset($payload['name']) && is_string($payload['name'])
                    ? $payload['name']
                    : 'Product #'.substr($audit->product_id ?? '', 0, 8);

                $sku = is_array($payload) && isset($payload['sku']) && is_string($payload['sku'])
                    ? $payload['sku']
                    : '-';

                return [
                    'id' => $audit->id,
                    'event' => $audit->event,
                    'product_name' => $productName,
                    'sku' => $sku,
                    'request_id' => $audit->request_id,
                    'user_name' => $audit->user_name,
                    'user_email' => $audit->user_email,
                    'created_at' => $audit->created_at?->diffForHumans() ?? 'Just now',
                    'timestamp' => $audit->created_at?->format('Y-m-d H:i:s') ?? '',
                ];
            });

        // Data user yang sedang login untuk ditampilkan di dashboard.
        /** @var User $user */
        $user = $request->user();

        return Inertia::render('Dashboard/Index', [
            'stats' => [
                'total' => $totalProducts,
                'active' => $activeProducts,
                'draft' => $draftProducts,
                'archived' => $archivedProducts,
                'low_stock' => $lowStockCount,
                'inventory_value' => $totalInventoryValue,
            ],
            'category_breakdown' => $categoryBreakdown,
            'daily_activity' => $dailyActivity,
            'recent_audits' => $recentAudits,
            'user_profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'logged_at' => Carbon::now()->format('d M Y, H:i T'),
            ],
            'system_metrics' => [
                'health' => 'ok',
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'environment' => app()->environment(),
                'uptime_status' => '100% Operational',
                'response_latency' => '< 45ms',
            ],
        ]);
    }
}
