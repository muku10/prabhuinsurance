<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Month master table removed; BS month names are handled in code.
    }

    public function down(): void
    {
        // No table to drop.
    }
};
