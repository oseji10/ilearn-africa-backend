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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('othernames')->nullable();
            $table->string('gender')->nullable();
            $table->string('marital_status')->nullable();
            $table->unsignedBigInteger('state_of_origin')->nullable(); // Foreign key column
            $table->unsignedBigInteger('state_of_residence')->nullable(); // Foreign key column
            $table->unsignedBigInteger('qualification')->nullable(); // Foreign key column
            $table->timestamps();

            $table->foreign('state_of_origin')->references('id')->on('states')->onDelete('cascade');
            $table->foreign('state_of_residence')->references('id')->on('states')->onDelete('cascade');
            $table->foreign('qualification')->references('id')->on('qualifications')->onDelete('cascade');
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
