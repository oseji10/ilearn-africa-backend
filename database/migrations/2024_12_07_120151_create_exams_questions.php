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
        Schema::create('cbt_exams_questions', function (Blueprint $table) {
            $table->id('examQuestionId');
            $table->unsignedBigInteger('examId')->nullable();
            $table->unsignedBigInteger('questionId')->nullable();
            $table->string('score')->nullable();
            $table->timestamps();

            $table->foreign('examId')->references('examId')->on('cbt_exams')->onDelete('cascade');
            $table->foreign('questionId')->references('questionId')->on('cbt_questions')->onDelete('cascade');
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams_results');
    }
};
