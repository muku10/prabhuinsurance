<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complains', function (Blueprint $table) {
            $table->decimal('average_resolution_time', 10, 2)->nullable()->after('pending_num');
        });
    }

    public function down(): void
    {
        Schema::table('complains', function (Blueprint $table) {
            $table->dropColumn('average_resolution_time');
        });
    }
};