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
        Schema::create('plantillas_calidad', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->longText('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('variables_calidad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plantilla_id')
                  ->constrained('plantillas_calidad')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->enum('tipo', ['critica', 'no_critica']);
            $table->string('nombre');
            $table->longText('descripcion')->nullable();
            $table->integer('nota_maxima')->nullable()->default(null);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('acciones_calidad', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->longText('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('motivos_cancelacion', function (Blueprint $table) {
            $table->id();
            $table->string('motivo');
            $table->timestamps();
        });

        Schema::create('evaluaciones_calidad', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idVenta');
            $table->foreignId('plantilla_id')
                  ->constrained('plantillas_calidad')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->json('variables')->nullable();
            $table->dateTime('fecha_calidad')->useCurrent();
            $table->integer('duracion');
            $table->integer('minuto_precio');
            $table->boolean('cierre_venta')->default(false);
            $table->foreignId('cerrado_por')->nullable()
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
            $table->foreignId('accion_calidad_id')->nullable()
                  ->constrained('acciones_calidad')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
            $table->json('oportunidades')->nullable();
            $table->text('comentarios_oportunidades')->nullable();
            $table->text('comentarios')->nullable();
            $table->string('numero_poliza', 100)->nullable()->default(null);
            $table->foreignId('creado_por')->nullable()
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
            $table->timestamps();

            $table->foreign('idVenta')
                ->references('idVenta')  
                ->on('ventas')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::create('estados_calidad_venta', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idVenta');
            $table->foreignId('evaluacion_id')
                  ->constrained('evaluaciones_calidad')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->enum('estado', [
                'CERTIFICADA',
                'RELLAMADA_CERTIFICADA',
                'RELLAMADA',
                'CANCELADA_CALIDAD',
                'CANCELADA_SUPERVISION',
                'REASIGNADA'
            ]);
            $table->dateTime('fecha_estado')->useCurrent();
            $table->integer('nota_estado')->nullable();
            $table->string('numero_certificado', 100)->nullable();
            $table->foreignId('motivo_cancelacion_id')->nullable()
                  ->constrained('motivos_cancelacion')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
            $table->boolean('cancelado_por_supervision')->default(false);
            $table->foreignId('reasignado_a')->nullable()
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
            $table->timestamps();

            $table->foreign('idVenta')
                ->references('idVenta')  
                ->on('ventas')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estados_calidad_venta');
        Schema::dropIfExists('evaluaciones_calidad');
        Schema::dropIfExists('motivos_cancelacion');
        Schema::dropIfExists('acciones_calidad');
        Schema::dropIfExists('variables_calidad');
        Schema::dropIfExists('plantillas_calidad');
    }
};

