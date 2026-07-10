<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branch_network', function (Blueprint $table) {
            if (! Schema::hasColumn('branch_network', 'fiscal_year')) {
                $table->string('fiscal_year')->nullable()->after('district_id');
            }

            if (! Schema::hasColumn('branch_network', 'month')) {
                $table->unsignedTinyInteger('month')->nullable()->after('fiscal_year');
            }

            if (! Schema::hasColumn('branch_network', 'inactive_fiscal_year')) {
                $table->string('inactive_fiscal_year')->nullable()->after('status');
            }

            if (! Schema::hasColumn('branch_network', 'inactive_month')) {
                $table->unsignedTinyInteger('inactive_month')->nullable()->after('inactive_fiscal_year');
            }
        });
    }

    public function down(): void
    {
        Schema::table('branch_network', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('branch_network', 'inactive_month') ? 'inactive_month' : null,
                Schema::hasColumn('branch_network', 'inactive_fiscal_year') ? 'inactive_fiscal_year' : null,
                Schema::hasColumn('branch_network', 'month') ? 'month' : null,
                Schema::hasColumn('branch_network', 'fiscal_year') ? 'fiscal_year' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
