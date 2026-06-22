<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->foreignId('import_log_id')->nullable()->after('id')->constrained('import_logs')->nullOnDelete();
            $table->string('fiscal_year')->nullable()->after('district_id');
            $table->integer('year')->nullable()->change();
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->renameColumn('number', 'number_of_branch');
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->unsignedInteger('number_of_agents')->default(0)->after('number_of_branch');
            $table->unsignedInteger('number_of_surveyors')->default(0)->after('number_of_agents');
            $table->index(['fiscal_year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropIndex(['fiscal_year', 'month']);
            $table->dropConstrainedForeignId('import_log_id');
            $table->dropColumn(['fiscal_year', 'number_of_agents', 'number_of_surveyors']);
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->renameColumn('number_of_branch', 'number');
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->integer('year')->nullable(false)->change();
        });
    }
};
