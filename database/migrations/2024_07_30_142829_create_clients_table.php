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
            $table->string('client_id')->nullable();
            $table->string('title')->nullable();
            $table->string('firstname')->nullable();
            $table->string('surname')->nullable();
            $table->string('othernames')->nullable();
            $table->string('gender')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('status')->default('profile_created')->nullable();
            
            $table->string('date_of_birth')->nullable();
            $table->text('address')->nullable();
            // $table->string('nationality')->nullable();
            // $table->string('country')->nullable();
            $table->unsignedBigInteger('nationality')->nullable(); // Foreign key column
            $table->unsignedBigInteger('country')->nullable(); // Foreign key column
            $table->unsignedBigInteger('qualification')->nullable(); // Foreign key column
            $table->timestamps();

            $table->foreign('nationality')->references('id')->on('nationality')->onDelete('cascade');
            $table->foreign('country')->references('id')->on('country')->onDelete('cascade');
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
