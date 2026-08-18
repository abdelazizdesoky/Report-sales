<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bind a user to a node of the sales hierarchy that already exists in
     * SQL Server (BI_ACTIVE_CUSTOMERS), instead of rebuilding it locally.
     *
     * hierarchy_level: salesman | supervisor | sales_manager
     * hierarchy_value: EMP_CODE for a salesman, the name for the other two.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('hierarchy_level', 20)->nullable()->after('region');
            $table->string('hierarchy_value', 150)->nullable()->after('hierarchy_level');

            $table->index(['hierarchy_level', 'hierarchy_value']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['hierarchy_level', 'hierarchy_value']);
            $table->dropColumn(['hierarchy_level', 'hierarchy_value']);
        });
    }
};
