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
        // Schema::table('clients', function (Blueprint $table) {
        //     $table->dropColumn('qualification');
        // });
        Schema::table('educational_details', function (Blueprint $table) {
            
            $table->string('course_studied')->nullable();
            
        });
        // Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
