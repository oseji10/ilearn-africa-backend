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
        Schema::create('educational_details', function (Blueprint $table) {
            $table->id();
            $table->string('client_id')->nullable(); // Foreign key column
            $table->unsignedBigInteger('qualification_id')->nullable(); // Foreign key column
            $table->string('date_acquired')->nullable();
            $table->string('grade')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('client_id')->on('clients')->onDelete('cascade');
            $table->foreign('qualification_id')->references('id')->on('qualifications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('educationaldetails');
    }
};

