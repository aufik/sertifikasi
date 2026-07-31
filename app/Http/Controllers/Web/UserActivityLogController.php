<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ProductAudit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class UserActivityLogController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $search = trim((string) $request->query('search', ''));
        $event = trim((string) $request->query('event', ''));

        $logs = ProductAudit::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($innerQuery) use ($search): void {
                    $innerQuery
                        ->where('user_name', 'like', "%{$search}%")
                        ->orWhere('user_email', 'like', "%{$search}%")
                        ->orWhere('event', 'like', "%{$search}%")
                        ->orWhere('request_id', 'like', "%{$search}%")
                        ->orWhere('path', 'like', "%{$search}%");
                });
            })
            ->when($event !== '', fn ($query) => $query->where('event', $event))
            ->latest()
            ->paginate(20)
            ->withQueryString()
            ->through(function (ProductAudit $audit): array {
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
                    'user_name' => $audit->user_name,
                    'user_email' => $audit->user_email,
                    'method' => $audit->method,
                    'path' => $audit->path,
                    'ip_address' => $audit->ip_address,
                    'request_id' => $audit->request_id,
                    'created_at' => $audit->created_at?->format('Y-m-d H:i:s') ?? '',
                ];
            });

        $events = ProductAudit::query()
            ->select('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event')
            ->values();

        return Inertia::render('Logs/UserActivity', [
            'logs' => $logs,
            'events' => $events,
            'filters' => [
                'search' => $search,
                'event' => $event,
            ],
        ]);
    }
}
