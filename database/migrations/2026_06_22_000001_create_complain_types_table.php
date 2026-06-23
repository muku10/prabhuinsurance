<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complain_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
        });

        DB::table('complain_types')->insert([
            ['name' => 'Claim delays'],
            ['name' => 'Policy disputes'],
            ['name' => 'Premium billing'],
            ['name' => 'Service quality'],
            ['name' => 'Documentation'],
            ['name' => 'Other'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('complain_types');
    }
};
