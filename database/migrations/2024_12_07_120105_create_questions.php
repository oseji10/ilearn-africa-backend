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
        Schema::create('cbt_questions', function (Blueprint $table) {
            $table->id('questionId');
            $table->unsignedBigInteger('questionCategoryId')->nullable();
            $table->text('question')->nullable();
            $table->integer('score')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('questionCategoryId')->references('questionCategoryId')->on('cbt_questions_category')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
