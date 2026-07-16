<?php

namespace Tests\Feature;

use App\Models\ImportLog;
use App\Models\PaidClaim;
use App\Models\User;
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

    public function test_portfolio_claim_rows_are_aggregated_before_rendering(): void
    {
        $user = User::factory()->create();
        $import = ImportLog::query()->create([
            'date' => now()->toDateString(),
            'user_id' => $user->user_id,
            'upload_type' => 'paid_claim',
            'file_name' => 'paid.xlsx',
            'fiscal_year' => '2082-83',
            'month' => 4,
            'status' => 'completed',
        ]);
        PaidClaim::query()->insert([
            ['import_log_id' => $import->id, 'fiscal_year' => '2082-83', 'month' => 4, 'province' => 'Bagmati', 'district' => 'Kathmandu', 'class' => null, 'total_paid_amount' => 100, 'turnaround_days' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['import_log_id' => $import->id, 'fiscal_year' => '2082-83', 'month' => 4, 'province' => 'Bagmati', 'district' => 'Kathmandu', 'class' => null, 'total_paid_amount' => 200, 'turnaround_days' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $rows = collect(app(PublicDashboardData::class)->toArray()['portfolioClaimRows']);
        $paid = $rows->firstWhere('type', 'paid');

        $this->assertSame(2, $paid['count']);
        $this->assertSame(300.0, $paid['amount']);
        $this->assertSame(6, $paid['turnaround_days']);
    }

    public function test_cached_dashboard_payload_contains_only_scalar_fiscal_years(): void
    {
        $data = app(PublicDashboardData::class)->toArray();

        $this->assertIsArray($data['fiscalYears']);
        $this->assertContainsOnly('string', $data['fiscalYears']);
        $this->assertIsArray($data['provinces']);
        $this->assertIsArray($data['outstandingClaims']);
        $this->assertIsArray($data['portfolioClaimRows']);
    }
}
