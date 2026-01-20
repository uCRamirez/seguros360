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
        Schema::create('cobranzas_carteras', function (Blueprint $table) {
            $table->id();
            
            // Identificador único de la cartera
            $table->string('identificador')->unique()->comment('Identificador único de la cartera');

            // Identificador del cliente asociado a la cartera
            $table->string('identificador_cliente')->index()->nullable();
            $table->foreign('identificador_cliente')
                ->references('identificador')
                ->on('cobranzas_clientes')
                ->onDelete('set null')
                ->onUpdate('cascade');
            
            // Proyecto asociado a la cartera
            $table->unsignedBigInteger('campaign_id')->nullable()->index();
            $table->foreign('campaign_id')
                ->references('id')
                ->on('campaigns')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->string('nombre_base', 200)->nullable()->index();

            $table->decimal('total_deuda', 15, 2)->default(0)->comment('Monto total de la deuda en la cartera');
            $table->integer('cantidad_pagos')->default(0)->comment('Cantidad total de pagos realizados en la cartera');
            $table->decimal('monto_pagado', 15, 2)->default(0)->comment('Monto total pagado de la deuda');
            $table->string('intereses')->nullable()->comment('Intereses asociados a la deuda');

            $table->decimal('monto_pagado_interno', 15, 2)->default(0)->comment('Monto total pagado de la deuda, control interno');
            $table->string('dias_mora')->nullable()->comment('Días de mora asociados a la deuda');
            $table->string('clasificacion_cartera')->nullable();

            $table->dateTime('fecha_deuda')->nullable()->comment('Fecha en que se registro la deuda en la entidad financiera');
            $table->dateTime('fecha_carga')->nullable()->comment('Fecha de carga de la cartera');

            $table->boolean('estado_judicial')->nullable()->comment('1 Si, 0 No')->index();

            $table->boolean('completada')->default(false)->comment('1 para completada, 0 para incompleta')->index();

            $table->boolean('activo')->default(true)->comment('1 para activo, 0 para inactivo')->index();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cobranzas_carteras');
    }
};
