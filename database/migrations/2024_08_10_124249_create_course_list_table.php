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
        Schema::create('course_list', function (Blueprint $table) {
            $table->id();
            $table->string('course_id')->nullable()->unique();
            $table->text('course_name')->nullable();
            $table->string('cost')->nullable();
            $table->unsignedBigInteger('course_image')->nullable();
            $table->boolean('status')->nullable()->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('center_id')->nullable();
            $table->timestamps();
            
            $table->foreign('center_id')->references('center_id')->on('centers')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_image')->references('id')->on('courses_images')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_list');
    }
};
