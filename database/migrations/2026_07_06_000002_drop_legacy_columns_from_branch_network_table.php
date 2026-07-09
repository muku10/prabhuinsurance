<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $indexName = collect(DB::select('SHOW INDEX FROM branch_network'))
            ->firstWhere('Column_name', 'fiscal_year')?->Key_name;
        $foreignName = collect(DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'branch_network'
                AND COLUMN_NAME = 'import_log_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
        "))->first()?->CONSTRAINT_NAME;

        if ($foreignName) {
            DB::statement("ALTER TABLE branch_network DROP FOREIGN KEY `{$foreignName}`");
        }

        if ($indexName) {
            DB::statement("ALTER TABLE branch_network DROP INDEX `{$indexName}`");
        }

        Schema::table('branch_network', function (Blueprint $table) {
            if (Schema::hasColumn('branch_network', 'import_log_id')) {
                $table->dropColumn('import_log_id');
            }

            $columns = array_values(array_filter([
                Schema::hasColumn('branch_network', 'fiscal_year') ? 'fiscal_year' : null,
                Schema::hasColumn('branch_network', 'year') ? 'year' : null,
                Schema::hasColumn('branch_network', 'month') ? 'month' : null,
                Schema::hasColumn('branch_network', 'number_of_branch') ? 'number_of_branch' : null,
                Schema::hasColumn('branch_network', 'number_of_agents') ? 'number_of_agents' : null,
                Schema::hasColumn('branch_network', 'number_of_surveyors') ? 'number_of_surveyors' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }

    public function down(): void
    {
        Schema::table('branch_network', function (Blueprint $table) {
            $table->foreignId('import_log_id')->nullable()->after('id')->constrained('import_logs')->nullOnDelete();
            $table->string('fiscal_year')->nullable()->after('district_id');
            $table->integer('year')->nullable()->after('fiscal_year');
            $table->integer('month')->nullable()->after('year');
            $table->integer('number_of_branch')->default(0)->after('month');
            $table->unsignedInteger('number_of_agents')->default(0)->after('number_of_branch');
            $table->unsignedInteger('number_of_surveyors')->default(0)->after('number_of_agents');
            $table->index(['fiscal_year', 'month']);
        });
    }
};
