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
        // Schema::disableForeignKeyConstraints(); 
        // Schema::table('payments', function (Blueprint $table) {
        //     $table->dropColumn('other_reference');
        // });
        // Schema::enableForeignKeyConstraints(); 

        Schema::table('payments', function (Blueprint $table) {
            $table->string('other_reference')->unique()->nullable();
            
        });

        Schema::table('proof_of_payment', function (Blueprint $table) {
            $table->string('other_reference')->nullable();
            $table->foreign('other_reference')->references('other_reference')->on('payments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};
