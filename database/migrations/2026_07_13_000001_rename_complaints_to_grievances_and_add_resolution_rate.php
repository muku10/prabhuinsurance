<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('complains', 'grievances');
        Schema::rename('complain_types', 'grievance_types');

        Schema::table('grievances', function (Blueprint $table) {
            $table->renameColumn('complain_type', 'grievance_type');
            $table->decimal('resolution_rate', 7, 2)->default(0)->after('pending_num');
        });

        DB::table('grievances')->orderBy('id')->eachById(function ($row) {
            $received = max(0, (int) $row->received_num);
            $resolved = min($received, max(0, (int) $row->resolved_num));
            DB::table('grievances')->where('id', $row->id)->update([
                'resolved_num' => $resolved,
                'pending_num' => $received - $resolved,
                'resolution_rate' => $received > 0 ? round(($resolved / $received) * 100, 2) : 0,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('grievances', function (Blueprint $table) {
            $table->dropColumn('resolution_rate');
            $table->renameColumn('grievance_type', 'complain_type');
        });

        Schema::rename('grievance_types', 'complain_types');
        Schema::rename('grievances', 'complains');
    }
};
