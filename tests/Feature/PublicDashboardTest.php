<?php

namespace Tests\Feature;

use App\Services\PublicDashboardData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_dashboard_can_be_rendered(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_cached_dashboard_payload_contains_only_scalar_fiscal_years(): void
    {
        $data = app(PublicDashboardData::class)->toArray();

        $this->assertIsArray($data['fiscalYears']);
        $this->assertContainsOnly('string', $data['fiscalYears']);
        $this->assertIsArray($data['provinces']);
        $this->assertIsArray($data['outstandingClaims']);
    }
}
