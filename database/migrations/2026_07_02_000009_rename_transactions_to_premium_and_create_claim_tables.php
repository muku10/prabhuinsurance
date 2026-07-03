<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transactions') && ! Schema::hasTable('premium')) {
            Schema::rename('transactions', 'premium');
        }

        if (Schema::hasTable('premium')) {
            Schema::table('premium', function (Blueprint $table) {
                if (! Schema::hasColumn('premium', 'department')) {
                    $table->string('department')->nullable()->after('month');
                }

                if (! Schema::hasColumn('premium', 'class')) {
                    $table->string('class')->nullable()->after('department');
                }

                if (! Schema::hasColumn('premium', 'fresh_policy')) {
                    $table->unsignedInteger('fresh_policy')->default(0)->after('class');
                }

                if (! Schema::hasColumn('premium', 'renewal_policy')) {
                    $table->unsignedInteger('renewal_policy')->default(0)->after('fresh_policy');
                }

                if (! Schema::hasColumn('premium', 'endrosement_policy')) {
                    $table->unsignedInteger('endrosement_policy')->default(0)->after('renewal_policy');
                }
            });
        }

        Schema::create('intimation_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_log_id')->constrained('import_logs')->cascadeOnDelete();
            $table->string('fiscal_year')->nullable();
            $table->unsignedTinyInteger('month');
            $table->string('province')->nullable();
            $table->string('district')->nullable();
            $table->string('branch')->nullable();
            $table->string('department')->nullable();
            $table->string('class')->nullable();
            $table->decimal('estimated_loss', 18, 2)->default(0);
            $table->string('status')->nullable();
            $table->timestamps();

            $table->index(['fiscal_year', 'month']);
        });

        Schema::create('paid_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_log_id')->constrained('import_logs')->cascadeOnDelete();
            $table->string('fiscal_year')->nullable();
            $table->unsignedTinyInteger('month');
            $table->string('department')->nullable();
            $table->string('class')->nullable();
            $table->string('province')->nullable();
            $table->string('district')->nullable();
            $table->string('branch_name')->nullable();
            $table->decimal('total_paid_amount', 18, 2)->default(0);
            $table->unsignedInteger('turnaround_days')->default(0);
            $table->timestamps();

            $table->index(['fiscal_year', 'month']);
        });

        Schema::create('withdrawal_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_log_id')->constrained('import_logs')->cascadeOnDelete();
            $table->string('fiscal_year')->nullable();
            $table->unsignedTinyInteger('month');
            $table->string('province')->nullable();
            $table->string('district')->nullable();
            $table->string('branch')->nullable();
            $table->string('department')->nullable();
            $table->string('class')->nullable();
            $table->timestamps();

            $table->index(['fiscal_year', 'month']);
        });

        Schema::create('outstanding_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_log_id')->constrained('import_logs')->cascadeOnDelete();
            $table->string('fiscal_year')->nullable();
            $table->string('development_year')->nullable();
            $table->string('province')->nullable();
            $table->string('district')->nullable();
            $table->string('branch')->nullable();
            $table->string('department')->nullable();
            $table->string('class')->nullable();
            $table->decimal('amount', 18, 2)->default(0);
            $table->timestamps();

            $table->index(['fiscal_year', 'development_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outstanding_claims');
        Schema::dropIfExists('withdrawal_claims');
        Schema::dropIfExists('paid_claims');
        Schema::dropIfExists('intimation_claims');

        if (Schema::hasTable('premium') && ! Schema::hasTable('transactions')) {
            Schema::rename('premium', 'transactions');
        }
    }
};