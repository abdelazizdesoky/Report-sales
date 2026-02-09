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
        Schema::create('manager_salesman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manager_id')->constrained('users')->onDelete('cascade');
            $table->string('salesman_name'); // اسم المندوب من SQL Server
            $table->timestamps();
            
            // منع التكرار
            $table->unique(['manager_id', 'salesman_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manager_salesman');
    }
};
