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
        Schema::table('educational_details', function (Blueprint $table) {
            $table->dropColumn('grade');
        });
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->string('grade')->nullable();
            $table->timestamps();
        });

        Schema::table('educational_details', function (Blueprint $table) {
            $table->unsignedBigInteger('grade')->nullable();
            $table->foreign('grade')->references('id')->on('grades')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
