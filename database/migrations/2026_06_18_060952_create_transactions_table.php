<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_log_id')->constrained('import_logs')->cascadeOnDelete();
            $table->foreignId('state_id')->nullable()->constrained('provinces', 'province_id')->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained('districts', 'district_id')->nullOnDelete();
            $table->foreignId('static_policies_id')->nullable()->constrained('policies', 'policy_id')->nullOnDelete();
            $table->foreignId('static_sub_policies_id')->nullable()->constrained('policies', 'policy_id')->nullOnDelete();
            $table->string('fiscal_year');
            $table->unsignedTinyInteger('month');
            $table->unsignedInteger('number_of_issued_policy')->default(0);
            $table->unsignedInteger('as_on_issued_policy')->default(0);
            $table->decimal('gross_premium_income', 18, 2)->default(0);
            $table->decimal('sum_insured', 18, 2)->default(0);
            $table->unsignedInteger('number_of_gross_claim')->default(0);
            $table->decimal('amount_of_gross_claim', 18, 2)->default(0);
            $table->unsignedInteger('number_of_gross_claim_paid')->default(0);
            $table->decimal('amount_of_gross_claim_paid', 18, 2)->default(0);
            $table->unsignedInteger('number_of_outstanding_claim')->default(0);
            $table->decimal('amount_of_outstanding_claim', 18, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['fiscal_year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
