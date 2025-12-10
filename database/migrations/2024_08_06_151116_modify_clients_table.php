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
        Schema::table('users', function (Blueprint $table) {
            // $table->dropColumn('status');
            // $table->dropColumn('client_id');
            // $table->string('status')->default('profile_created')->nullable();
            // // $table->string('client_id')->nullable();
            // $table->string('title')->nullable();
            // $table->string('date_of_birth')->nullable();
            // $table->text('address')->nullable();
            // $table->string('nationality')->nullable();
            // $table->string('country')->nullable();
            $table->string('client_id')->nullable();
            $table->foreign('client_id')->references('client_id')->on('clients')->onDelete('cascade');
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
