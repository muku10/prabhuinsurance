<?php

namespace Tests\Feature;

use App\Http\Controllers\ImportLogController;
use App\Models\ImportLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use ReflectionMethod;
use Tests\TestCase;

class OutstandingClaimImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_outstanding_claim_uses_upload_month_when_sheet_has_no_month_column(): void
    {
        $importLog = ImportLog::create([
            'date' => now()->toDateString(),
            'user_id' => User::factory()->create()->getKey(),
            'upload_type' => 'outstanding_claim',
            'file_name' => 'test.xlsx',
            'fiscal_year' => '2082-83',
            'month' => 4,
            'status' => 'pending',
        ]);
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->fromArray([
            ['Province', 'District', 'Branch', 'Department', 'Class', 'Amount', 'Development Year'],
            ['Bagmati', 'Kathmandu', 'Head Office', 'Motor', '1', '1000', '< 1'],
        ]);

        $method = new ReflectionMethod(ImportLogController::class, 'buildOutstandingClaimRowsFromUpload');
        $rows = $method->invoke(app(ImportLogController::class), $spreadsheet, $importLog);

        $this->assertSame(4, $rows[0]['month']);
    }
}
