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
        // Schema::create('admissions', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('client_id')->nullable();
        //     $table->string('admission_number')->nullable();
        //     $table->string('status')->nullable()->default('pending');
        //     $table->string('is_documents_reviewed')->nullable();
        //     $table->string('is_registration_fee_paid')->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();
            
        //     $table->foreign('client_id')->references('client_id')->on('clients')->onDelete('cascade');
        // });

        // Schema::table('clients', function (Blueprint $table) {
        //    $table->string('created_by')->nullable();
        //    $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};
