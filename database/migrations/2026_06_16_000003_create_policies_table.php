<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('policies', function (Blueprint $table) {
            $table->id('policy_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('policy_name');
            $table->string('policy_name_np')->nullable(); // Nepali name
            $table->string('code', 10)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->foreign('parent_id')->references('policy_id')->on('policies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};