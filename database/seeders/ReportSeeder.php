<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Report;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Report::create([
            'name' => 'Sales Report 2024',
            'code' => 'sales_report',
            'source_name' => 'sales_data', // Assuming a table named 'sales_data' in SQL Server
        ]);

    }
}
