<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_highlights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_highlight_import_id')->constrained()->cascadeOnDelete();
            $table->string('fiscal_year');
            $table->unsignedTinyInteger('quarter');
            $table->decimal('solvency_ratio', 18, 4)->nullable();
            $table->decimal('return_on_equity', 18, 4)->nullable();
            $table->decimal('earnings_per_share', 18, 4)->nullable();
            $table->decimal('net_worth', 22, 4)->nullable();
            $table->decimal('net_profit_margin', 18, 4)->nullable();
            $table->decimal('liquidity_ratio', 18, 4)->nullable();
            $table->decimal('investment_yield', 18, 4)->nullable();
            $table->timestamps();

            $table->index(['fiscal_year', 'quarter']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_highlights');
    }
};
