<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('import_logs', function (Blueprint $table) {
            $table->string('upload_type')->nullable()->after('user_id');
            $table->index(['upload_type', 'date']);
        });
    }

    public function down(): void
    {
        Schema::table('import_logs', function (Blueprint $table) {
            $table->dropIndex(['upload_type', 'date']);
            $table->dropColumn('upload_type');
        });
    }
};