<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            // 1) Añadir la columna (ajusta el posicionamiento con ->after() si lo necesitas)
            $table->unsignedBigInteger('plantilla_calidad_id')
                  ->nullable()
                  ->after('company_id');

            // 2) Crear la llave foránea
            $table->foreign('plantilla_calidad_id')
                  ->references('id')
                  ->on('plantillas_calidad')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            // 1) Eliminar la restricción primero
            $table->dropForeign(['plantilla_calidad_id']);
            // 2) Luego eliminar la columna
            $table->dropColumn('plantilla_calidad_id');
        });
    }
};
