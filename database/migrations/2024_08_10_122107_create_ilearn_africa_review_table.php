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
        // Schema::create('ilearn_africa_review', function (Blueprint $table) {
            // $table->id();
            // $table->string('client_id')->nullable();
            // $table->string('admission_number')->nullable();
            // $table->string('is_documents_reviewed')->nullable();
            // $table->string('is_application_approved')->nullable();
            // $table->timestamps();
            // $table->softDeletes();
            // $table->string('created_by')->nullable();
            
            // $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('client_id')->references('client_id')->on('clients')->onDelete('cascade');
            // $table->foreign('admission_number')->references('admission_number')->on('admissions')->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ilearn_africa_review');
    }
};
