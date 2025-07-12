<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1ª pasada: agregar todas las columnas nuevas
        Schema::table('leads', function (Blueprint $table) {
            $table->string('segundo_nombre', 200)->nullable()->after('nombre');
            $table->string('tipo_plan', 200)->nullable()->after('estadoCivil_id');
            $table->dateTime('fechaVencimiento')->nullable()->after('tipo_plan');
            $table->string('tipo_tarjeta', 200)->nullable()->after('fechaVencimiento');
            $table->string('emisor', 200)->nullable()->after('tipo_tarjeta');
            $table->string('ultimos_digitos', 10)->nullable()->after('emisor');
            $table->string('mes_carga', 10)->nullable()->after('ultimos_digitos');
            $table->year('anno_carga')->nullable()->after('mes_carga');
            $table->string('foco_venta', 200)->nullable()->after('anno_carga');

            // nota: 'hijos' ya existe, así que NO lo agregamos aquí
            $table->string('genero', 100)->nullable()->after('edad');
            $table->string('provincia_voto', 200)->nullable()->after('email');
            $table->string('etapa')->nullable()->default('Nueva')->after('provincia_voto');
        });

        // 2ª pasada: modificar la columna hijos
        Schema::table('leads', function (Blueprint $table) {
            $table->boolean('hijos')
                  ->default(false)
                  ->after('foco_venta')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'segundo_nombre',
                'tipo_plan',
                'fechaVencimiento',
                'tipo_tarjeta',
                'emisor',
                'ultimos_digitos',
                'mes_carga',
                'anno_carga',
                'foco_venta',
                'genero',
                'provincia_voto',
                'etapa',
            ]);
            $table->dropColumn('hijos');
        });
    }
};
