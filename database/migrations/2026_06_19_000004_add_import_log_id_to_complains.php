<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complains', function (Blueprint $table) {
            $table->foreignId('import_log_id')->nullable()->after('id')->constrained('import_logs')->nullOnDelete();
            $table->index(['year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::table('complains', function (Blueprint $table) {
            $table->dropIndex(['year', 'month']);
            $table->dropConstrainedForeignId('import_log_id');
        });
    }
};