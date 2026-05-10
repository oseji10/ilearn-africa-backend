<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('clients_extra')) {
            Schema::table('clients_extra', function (Blueprint $table) {
                
                $table->string('department')->nullable();
                $table->text('notes')->nullable();
                
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('client_extra');
    }
};