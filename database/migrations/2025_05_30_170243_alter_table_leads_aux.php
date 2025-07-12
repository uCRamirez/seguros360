<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1) Quitar FK (si existe) y renombrar
        try {
            Schema::table('leads_aux', function (Blueprint $table) {
                $table->dropForeign(['estadoCivil_id']);
                $table->renameColumn('estadoCivil_id', 'estadoCivil');
            });
        } catch (\Exception $e) {
            // Si la FK no existía, simplemente renombramos
            Schema::table('leads_aux', function (Blueprint $table) {
                $table->renameColumn('estadoCivil_id', 'estadoCivil');
            });
        }

        // IMPORTANTE: para usar ->change() asegúrate de tener doctrine/dbal instalado
        Schema::table('leads_aux', function (Blueprint $table) {
            // 2) Cambiar tipo a VARCHAR(200)
            $table->string('estadoCivil', 200)
                  ->nullable()
                  ->change();
        });

        // 3) Añadir nuevas columnas y ajustes
        Schema::table('leads_aux', function (Blueprint $table) {
            $table->string('segundo_nombre', 200)
                  ->nullable()
                  ->after('nombre');

            $table->string('tipo_plan', 200)
                  ->nullable()
                  ->after('estadoCivil');

            $table->dateTime('fechaVencimiento')
                  ->nullable()
                  ->after('tipo_plan');

            $table->string('tipo_tarjeta', 200)
                  ->nullable()
                  ->after('fechaVencimiento');

            $table->string('emisor', 200)
                  ->nullable()
                  ->after('tipo_tarjeta');

            $table->string('ultimos_digitos', 10)
                  ->nullable()
                  ->after('emisor');

            $table->string('mes_carga', 10)
                  ->nullable()
                  ->after('ultimos_digitos');

            $table->year('anno_carga')
                  ->nullable()
                  ->after('mes_carga');

            $table->string('foco_venta', 200)
                  ->nullable()
                  ->after('anno_carga');

            $table->boolean('hijos')
                  ->nullable()
                  ->default(false)
                  ->change();  

            $table->string('genero', 100)
                  ->nullable()
                  ->after('edad');

            $table->string('provincia_voto', 200)
                  ->nullable()
                  ->after('email');
        });
    }

    public function down()
    {
        Schema::table('leads_aux', function (Blueprint $table) {
            // 1) Revertir cambios en 'estadoCivil'
            $table->string('estadoCivil', 200)->change();
            $table->renameColumn('estadoCivil', 'estadoCivil_id');
            $table->foreign('estadoCivil_id')
                  ->references('id')
                  ->on('estados_civiles');

            // 2) Eliminar columnas añadidas
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
            ]);

            // 3) Revertir tipo de 'hijos' a integer
            $table->integer('hijos')
                  ->nullable()
                  ->change();
        });
    }
};
