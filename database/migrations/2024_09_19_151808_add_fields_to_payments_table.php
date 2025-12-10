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
        // Schema::disableForeignKeyConstraints(); 
     
    
        Schema::table('payments', function (Blueprint $table) {
            $table->string('cohort_id')->nullable();
            $table->foreign('cohort_id')->references('cohort_id')->on('cohorts')->onDelete('cascade');
        });
        Schema::table('admissions', function (Blueprint $table) {
            $table->string('cohort_id')->nullable();
            $table->foreign('cohort_id')->references('cohort_id')->on('cohorts')->onDelete('cascade');
        });

        //  Schema::enableForeignKeyConstraints(); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    //    
    }
};
