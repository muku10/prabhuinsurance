<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('outstanding_claims', function (Blueprint $table) {
            $table->unsignedTinyInteger('month')->nullable()->after('fiscal_year');
            $table->index(['fiscal_year', 'month']);
        });

        DB::table('outstanding_claims')
            ->whereNull('month')
            ->orderBy('id')
            ->eachById(function ($claim): void {
                $month = DB::table('import_logs')
                    ->where('id', $claim->import_log_id)
                    ->value('month');

                if ($month !== null) {
                    DB::table('outstanding_claims')
                        ->where('id', $claim->id)
                        ->update(['month' => $month]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('outstanding_claims', function (Blueprint $table) {
            $table->dropIndex(['fiscal_year', 'month']);
            $table->dropColumn('month');
        });
    }
};
