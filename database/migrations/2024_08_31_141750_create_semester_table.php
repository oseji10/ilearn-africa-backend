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
        Schema::create('semester', function (Blueprint $table) {
            $table->id();
            $table->string('semester_id')->nullable()->unique();
            $table->string('semester_name')->nullable();
            $table->timestamps();

            
        });
        
        
        Schema::table('payments', function (Blueprint $table){
            $table->string('semester_id')->nullable();
            $table->foreign('semester_id')->references('semester_id')->on('semester')->onDelete('cascade');
        });

        Schema::table('admissions', function (Blueprint $table){
            $table->string('semester_id')->nullable();
            $table->foreign('semester_id')->references('semester_id')->on('semester')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semester');
    }
};
