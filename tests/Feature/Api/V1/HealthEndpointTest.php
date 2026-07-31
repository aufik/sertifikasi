<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use Tests\TestCase;

final class HealthEndpointTest extends TestCase
{
    public function test_health_endpoint_returns_standard_response(): void
    {
        $this->getJson('/api/v1/health')
            ->assertOk()
            ->assertHeader('X-Request-ID')
            ->assertHeader('X-Response-Time-Ms')
            ->assertJsonPath('meta.success', true)
            ->assertJsonPath('data.status', 'ok');
    }
}
