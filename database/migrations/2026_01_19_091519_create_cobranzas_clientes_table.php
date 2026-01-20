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
        Schema::create('cobranzas_clientes', function (Blueprint $table) {
            $table->id();
            $table->string('identificador')->unique()->comment('Identificador único del cliente, sea documento, código, etc.');

            $table->unsignedBigInteger('tipo_identificador')->index()->nullable();
            $table->foreign('tipo_identificador')
                ->references('id')
                ->on('type_documents')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->string('nombre_completo')->index();

            $table->string('email')->nullable()->index();
            $table->string('email2')->nullable()->index();

            $table->string('tel1')->nullable()->index();
            $table->string('tel2')->nullable()->index();
            $table->string('tel3')->nullable()->index();
            $table->string('tel4')->nullable()->index();
            $table->string('tel5')->nullable()->index();
            $table->string('tel6')->nullable()->index();

            $table->string('provincia', 200)->nullable();
            $table->string('canton', 200)->nullable();
            $table->string('distrito', 200)->nullable();
            $table->mediumText('direccion')->nullable();
            
            $table->string('nombre_base', 200)->nullable()->default('Manual')->index();

            $table->unsignedBigInteger('campaign_id')->nullable()->index();
            $table->foreign('campaign_id')
                ->references('id')
                ->on('campaigns')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->unsignedBigInteger('asignado_a')->nullable()->index();
            $table->foreign('asignado_a')
                ->references('id')
                ->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            
            $table->json('otros')->nullable()->comment('Almacena datos adicionales del cliente en formato JSON');

            $table->boolean('activo')->default(true)->comment('1 para activo, 0 para inactivo')->index();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cobranzas_clientes');
    }
};
