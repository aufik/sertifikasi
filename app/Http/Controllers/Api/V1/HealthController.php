<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class HealthController extends BaseController
{
    public function __invoke()
    {
        $checks = ['database' => 'ok', 'cache' => 'ok', 'disk' => is_writable(storage_path()) ? 'ok' : 'failed'];
        try {
            DB::connection()->getPdo();
        } catch (\Throwable) {
            $checks['database'] = 'failed';
        }
        try {
            Cache::put('health_check', true, 5);
        } catch (\Throwable) {
            $checks['cache'] = 'failed';
        }

        return $this->successResponse(['status' => in_array('failed', $checks, true) ? 'degraded' : 'ok', 'checks' => $checks]);
    }
}
