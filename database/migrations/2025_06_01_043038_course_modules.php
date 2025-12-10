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
        Schema::create('course_list_modules', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('client_id');

            $table->string('course_id')->nullable();
            $table->string('modules')->nullable();
            

            $table->timestamps();

            
            // $table->foreign('client_id')->references('client_id')->on('clients')->onDelete('cascade');
            
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
