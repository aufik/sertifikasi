<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

final class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        if (app()->environment('local', 'testing')) {
            return null;
        }

        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'appName' => config('app.name'),
            'auth' => ['user' => $request->user() ? ['id' => $request->user()->id, 'name' => $request->user()->name, 'email' => $request->user()->email, 'role' => $request->user()->role] : null],
            'flash' => ['success' => $request->session()->get('success'), 'error' => $request->session()->get('error')],
            'requestId' => $request->headers->get('X-Request-ID'),
        ]);
    }
}
