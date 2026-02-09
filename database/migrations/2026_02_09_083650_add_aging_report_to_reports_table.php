<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert the Aging Report
        DB::table('reports')->insert([
            'name' => 'تقرير أعمار الديون',
            'code' => 'aging_report',
            'source_name' => 'Alarabia_AGING_SUMMARY',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('reports')->where('code', 'aging_report')->delete();
    }
};
