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
        Schema::create('clients_extra', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('client_id');

            $table->string('client_id')->nullable();
            $table->string('preferred_mode_of_communication')->nullable();
            $table->string('employment_status')->nullable();
            $table->string('job_title')->nullable();
            $table->string('name_of_organization')->nullable();
            $table->string('years_of_experience')->nullable();
           
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
