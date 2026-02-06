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
        Schema::create('cobranzas_pagos', function (Blueprint $table) {
            $table->id();
            // Identificador de la cartera
            $table->string('identificador_cartera')->index()->nullable();
            $table->foreign('identificador_cartera')
                ->references('identificador')
                ->on('cobranzas_carteras')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->decimal('monto', 15, 2)->default(0)->comment('Monto del pago');
            $table->mediumText('detalle')->nullable()->comment('Descripción del pago');
            $table->dateTime('fecha_banco')->nullable()->comment('Fecha en que el banco registró el pago');
            $table->string('nombre_base', 200)->nullable()->index();
            $table->boolean('activo')->default(true)->comment('1 para activo, 0 para inactivo')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cobranzas_pagos');
    }
};
