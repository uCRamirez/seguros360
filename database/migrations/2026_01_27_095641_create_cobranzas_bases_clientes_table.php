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
        Schema::create('cobranzas_bases_clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_base');
            $table->integer('registros');
            $table->unsignedBigInteger('user_id')->nullable()->index()->comment('Usuario que subio la base');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->dateTime('fecha_subida');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cobranzas_bases_clientes');
    }
};
