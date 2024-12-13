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
        Schema::create('cbt_exams_results', function (Blueprint $table) {
            $table->id('resultId');
            $table->string('clientId')->nullable();
            $table->unsignedBigInteger('examId')->nullable();
            $table->string('courseId')->nullable();
            $table->unsignedBigInteger('questionId')->nullable();
            $table->unsignedBigInteger('optionSelected')->nullable();
            $table->string('score')->nullable();
            $table->timestamps();

            $table->foreign('clientId')->references('client_id')->on('clients')->onDelete('cascade');
            $table->foreign('examId')->references('examId')->on('cbt_exams')->onDelete('cascade');
            $table->foreign('courseId')->references('course_id')->on('course_list')->onDelete('cascade');
            $table->foreign('questionId')->references('questionId')->on('cbt_questions')->onDelete('cascade');
            $table->foreign('optionSelected')->references('optionId')->on('cbt_question_options')->onDelete('cascade');
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
