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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('client_id')->nullable();
            $table->string('payment_for')->nullable();
            $table->string('payment_gateway')->nullable();
            $table->string('amount')->nullable();
            $table->string('course_id')->nullable();
            $table->string('transaction_reference')->nullable()->unique();
            $table->string('other_reference')->nullable();
            $table->string('payment_method')->nullable();
           $table->boolean('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('course_id')->references('course_id')->on('course_list')->onDelete('cascade');
            $table->foreign('client_id')->references('client_id')->on('clients')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
