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
        // Schema::table('course_material', function (Blueprint $table) {
        //     $table->softDeletes();
        // });


        // Schema::table('course_list', function (Blueprint $table) {
        //     $table->softDeletes();
        // });


        // Schema::table('centers', function (Blueprint $table) {
        //     $table->softDeletes();
        // });



        // Schema::table('grades', function (Blueprint $table) {
        //     $table->softDeletes();
        // });


        Schema::table('profile_image', function (Blueprint $table) {
            $table->softDeletes();
        });


        Schema::table('qualifications', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            //
        });
    }
};
