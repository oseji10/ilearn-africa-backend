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
        Schema::create('cohorts_clients', function (Blueprint $table) {
            $table->id();
            $table->string('cohort_id')->nullable();
            $table->string('client_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('cohort_id')->references('cohort_id')->on('cohorts')->onDelete('cascade');
            $table->foreign('client_id')->references('client_id')->on('clients')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cohorts_clients');
    }
};
