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
        Schema::create('cbt_question_options', function (Blueprint $table) {
            $table->id('optionId');
            $table->unsignedBigInteger('questionId');
            $table->string('optionName')->nullable();
            $table->text('optionDetail')->nullable();
            $table->boolean('isCorrect')->default('0')->nullable();
            $table->timestamps();

            $table->foreign('questionId')->references('questionId')->on('cbt_questions')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_options');
    }
};
