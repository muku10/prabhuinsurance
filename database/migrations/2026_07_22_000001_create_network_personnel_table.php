<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('network_personnel', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['agent', 'surveyor']);
            $table->string('fiscal_year');
            $table->unsignedTinyInteger('month');
            $table->unsignedInteger('number');
            $table->timestamps();

            $table->unique(['type', 'fiscal_year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('network_personnel');
    }
};
