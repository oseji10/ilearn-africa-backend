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
        Schema::create('cbt_exams_retake', function (Blueprint $table) {
            $table->id('retakeId');
            $table->string('clientId')->nullable();
            $table->unsignedBigInteger('examId')->nullable();
            $table->string('retake_count')->nullable();
            $table->unsignedBigInteger('permittedBy')->nullable();
            $table->timestamps();

            // $table->foreign('clientId')->references('client_id')->on('clients')->onDelete('cascade');
            $table->foreign('examId')->references('examId')->on('cbt_exams')->onDelete('cascade');
            $table->foreign('permittedBy')->references('id')->on('users')->onDelete('cascade');
           
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
