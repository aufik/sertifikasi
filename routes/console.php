<?php

declare(strict_types=1);

use App\Models\ProductAudit;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('product:log-report {--limit=10 : Number of rows to display} {--event= : Filter by business event} {--user= : Filter by user email} {--json : Print JSON evidence output}', function (): int {
    $limit = max(1, min((int) $this->option('limit'), 50));
    $event = $this->option('event');
    $userEmail = $this->option('user');
    $logPath = storage_path('logs/product.log');

    $query = ProductAudit::query()
        ->when(is_string($event) && $event !== '', fn ($q) => $q->where('event', $event))
        ->when(is_string($userEmail) && $userEmail !== '', fn ($q) => $q->where('user_email', $userEmail))
        ->latest()
        ->take($limit);

    $auditRows = $query->get()->map(function (ProductAudit $audit): array {
        /** @var array<string, mixed>|null $payload */
        $payload = $audit->payload;
        $productName = is_array($payload) && isset($payload['name']) ? (string) $payload['name'] : '-';
        $sku = is_array($payload) && isset($payload['sku']) ? (string) $payload['sku'] : '-';

        return [
            'time' => $audit->created_at?->format('Y-m-d H:i:s') ?? '-',
            'event' => $audit->event,
            'product' => $productName,
            'sku' => $sku,
            'user' => $audit->user_name ?? '-',
            'email' => $audit->user_email ?? '-',
            'ip' => $audit->ip_address ?? '-',
            'method' => $audit->method ?? '-',
            'path' => $audit->path ?? '-',
            'request_id' => $audit->request_id ?? '-',
        ];
    });

    $lines = File::exists($logPath) ? (file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: []) : [];
    $lastLines = array_slice($lines, -$limit);

    if ((bool) $this->option('json')) {
        $this->line(json_encode([
            'generated_at' => now()->toIso8601String(),
            'filters' => ['event' => $event, 'user' => $userEmail, 'limit' => $limit],
            'audit_trail' => $auditRows->toArray(),
            'file_log_tail' => $lastLines,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}');

        return self::SUCCESS;
    }

    $this->info('Product Management Service - Logging Report');
    $this->line('Generated at : '.now()->format('Y-m-d H:i:s T'));
    $this->line('Log file     : '.$logPath);
    $this->line('Event filter : '.(is_string($event) && $event !== '' ? $event : '-'));
    $this->line('User filter  : '.(is_string($userEmail) && $userEmail !== '' ? $userEmail : '-'));
    $this->newLine();

    $this->info('Recent Business Audit Trail (Database: product_audits)');
    if ($auditRows->isEmpty()) {
        $this->warn('No audit records found. Create/update/delete/restock/restore/purge a product to generate business audit rows.');
    } else {
        $this->table(['Time', 'Event', 'Product', 'SKU', 'User', 'Email', 'IP', 'Method', 'Path', 'Request ID'], $auditRows->toArray());
    }

    $this->newLine();
    $this->info('Recent Technical Product Log Lines (File: storage/logs/product.log)');
    if (! File::exists($logPath)) {
        $this->warn('Product log file does not exist yet. Run product actions or API requests first.');

        return self::SUCCESS;
    }

    foreach ($lastLines as $line) {
        $this->line($line);
    }

    return self::SUCCESS;
})->purpose('Display product business audit trail and product.log entries for assessor demonstration');
