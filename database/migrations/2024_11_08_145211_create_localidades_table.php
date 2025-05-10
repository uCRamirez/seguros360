<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('localidades', function (Blueprint $table) {
            $table->increments('id');
            $table->string('provincia', 100)->nullable();
            $table->string('canton', 100)->nullable();
            $table->string('distrito', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('localidades');
    }
};
