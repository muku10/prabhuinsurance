<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('branches', 'branch_network');
    }

    public function down(): void
    {
        Schema::rename('branch_network', 'branches');
    }
};