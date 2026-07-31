<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DashboardPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_open_dashboard(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/dashboard')
            ->assertOk();
    }

    public function test_authenticated_user_can_open_user_activity_logs(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/logs/user-activity')
            ->assertOk();
    }
}
