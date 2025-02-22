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
        Schema::create('cbt_master_results', function (Blueprint $table) {
            $table->id('masterId');
            $table->string('clientId')->nullable();
            $table->unsignedBigInteger('examId')->nullable();
            $table->string('total_score')->nullable();
            $table->timestamps();

            $table->foreign('examId')->references('examId')->on('cbt_exams')->onDelete('cascade');
            
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
