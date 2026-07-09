<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branch_network', function (Blueprint $table) {
            $table->unsignedInteger('branch_code')->nullable()->unique()->after('id');
            $table->string('ext_branch_code', 10)->nullable()->unique()->after('branch_code');
            $table->string('branch_name')->nullable()->after('ext_branch_code');
            $table->unsignedInteger('local_level')->nullable()->after('district_id');
            $table->string('address')->nullable()->after('local_level');
            $table->string('display_name')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('branch_network', function (Blueprint $table) {
            $table->dropUnique(['branch_code']);
            $table->dropUnique(['ext_branch_code']);
            $table->dropColumn([
                'branch_code',
                'ext_branch_code',
                'branch_name',
                'local_level',
                'address',
                'display_name',
            ]);
        });
    }
};
