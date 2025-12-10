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
        Schema::create('cbt_exams', function (Blueprint $table) {
            $table->id('examId');
            $table->string('examName')->nullable();
            $table->text('details')->nullable();
            $table->string('examDate')->nullable();
            $table->string('examTime')->nullable();
            $table->boolean('isShuffle')->default('0')->nullable();
            $table->boolean('isRandom')->default('0')->nullable();
            $table->string('canRetake')->default('0')->nullable();
            $table->boolean('canSeeResult')->default('0')->nullable();
            $table->string('status')->nullable();
            $table->string('courseId')->nullable();
            $table->string('cohortId')->nullable();
            $table->string('timeAllowed');
            $table->unsignedBigInteger('addedBy')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('courseId')->references('course_id')->on('course_list')->onDelete('cascade');
            $table->foreign('cohortId')->references('cohort_id')->on('cohorts')->onDelete('cascade');
            $table->foreign('addedBy')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
