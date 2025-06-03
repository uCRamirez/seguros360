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
        Schema::create('bases_historico', function (Blueprint $table) {
            $table->id();
            $table->string('nombreBase');
            $table->bigInteger('user_id')->unsigned()->nullable()->default(null);
            $table->unsignedBigInteger('campaign_id')->nullable();
            
            $table->foreign('campaign_id')
                  ->references('id')
                  ->on('campaigns')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->integer('cantidadRegistros');
            $table->string('etapa')->default('nueva');
            $table->boolean('estado')->default(true);

            $table->timestamps();

            // Definición de la Foreign Key
            $table->foreign('user_id', 'bases_users_FK') // Nombre opcional para la restricción
                ->references('id')->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bases_historico');
    }
};
