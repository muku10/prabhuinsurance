<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('import_logs')
            ->where('upload_type', 'irms')
            ->update([
                'upload_type' => 'premium',
                'file_name' => DB::raw("REPLACE(file_name, 'uploads/irms/', 'uploads/premium/')"),
            ]);

        $disk = Storage::disk('public');

        if ($disk->exists('uploads/irms')) {
            $files = $disk->allFiles('uploads/irms');

            foreach ($files as $file) {
                $newPath = str_replace('uploads/irms/', 'uploads/premium/', $file);
                $disk->move($file, $newPath);
            }

            $disk->deleteDirectory('uploads/irms');
        }
    }

    public function down(): void
    {
        DB::table('import_logs')
            ->where('upload_type', 'premium')
            ->update([
                'upload_type' => 'irms',
                'file_name' => DB::raw("REPLACE(file_name, 'uploads/premium/', 'uploads/irms/')"),
            ]);

        $disk = Storage::disk('public');

        if ($disk->exists('uploads/premium')) {
            $files = $disk->allFiles('uploads/premium');

            foreach ($files as $file) {
                $newPath = str_replace('uploads/premium/', 'uploads/irms/', $file);
                $disk->move($file, $newPath);
            }

            $disk->deleteDirectory('uploads/premium');
        }
    }
};
