<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite cannot remove these constrained legacy columns without
            // rebuilding the table. They are harmless in the test schema.
            return;
        }

        Schema::table('premium', function (Blueprint $table) {
            $table->dropIndex('transactions_import_batch_token_index');
            $table->dropForeign('transactions_static_policies_id_foreign');
            $table->dropForeign('transactions_static_sub_policies_id_foreign');

            $table->dropColumn($this->legacyColumns());
        });
    }

    public function down(): void
    {
        Schema::table('premium', function (Blueprint $table) {
            $table->uuid('import_batch_token')->nullable()->after('import_log_id');
            $table->index('import_batch_token');
            $table->foreignId('static_policies_id')->nullable()->after('district_id')->constrained('policies', 'policy_id')->nullOnDelete();
            $table->foreignId('static_sub_policies_id')->nullable()->after('static_policies_id')->constrained('policies', 'policy_id')->nullOnDelete();
            $table->unsignedInteger('number_of_issued_policy')->default(0)->after('month');
            $table->unsignedInteger('as_on_issued_policy')->default(0)->after('number_of_issued_policy');
            $table->unsignedInteger('number_of_gross_claim')->default(0)->after('sum_insured');
            $table->decimal('amount_of_gross_claim', 18, 2)->default(0)->after('number_of_gross_claim');
            $table->unsignedInteger('number_of_gross_claim_paid')->default(0)->after('amount_of_gross_claim');
            $table->decimal('amount_of_gross_claim_paid', 18, 2)->default(0)->after('number_of_gross_claim_paid');
            $table->unsignedInteger('number_of_outstanding_claim')->default(0)->after('amount_of_gross_claim_paid');
            $table->decimal('amount_of_outstanding_claim', 18, 2)->default(0)->after('number_of_outstanding_claim');
        });
    }

    private function legacyColumns(): array
    {
        return [
            'import_batch_token',
            'static_policies_id',
            'static_sub_policies_id',
            'number_of_issued_policy',
            'as_on_issued_policy',
            'number_of_gross_claim',
            'amount_of_gross_claim',
            'number_of_gross_claim_paid',
            'amount_of_gross_claim_paid',
            'number_of_outstanding_claim',
            'amount_of_outstanding_claim',
        ];
    }
};
