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
        Schema::create('calidad_venta', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('idVenta');
            $table->enum('estadoCalidad', [""]);
            $table->integer('notaFinal');
            $table->dateTime('fechaCalidad');
            $table->bigInteger('user_id');
            $table->json('variableCriticas');
            $table->json('variableNoCriticas');
            $table->longText('comentarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calidad_venta');
    }
};
