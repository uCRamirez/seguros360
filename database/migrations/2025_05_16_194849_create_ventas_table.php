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
        Schema::create('ventas', function (Blueprint $table) {
            // Primary key
            $table->bigIncrements('idVenta');

            // Foreign key columns
            $table->unsignedBigInteger('idNota');
            $table->unsignedBigInteger('idLead');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('idProducto');

            // Other columns
            $table->string('telVenta', 191);
            $table->string('estadoVenta', 191);
            $table->string('tarjeta', 191);
            $table->integer('cantidadProducto')->default(0);
            $table->bigInteger('aplicaBeneficiarios');
            $table->integer('cantidadBeneficiarios')->default(0);
            $table->json('beneficiarios');
            $table->decimal('montoTotal', 8, 2);
            $table->boolean('calidad')->default(false);

            // Indexes for foreign keys
            $table->index('idNota', 'ventas_lead_logs_FK');
            $table->index('idLead', 'ventas_leads_FK');
            $table->index('user_id', 'ventas_users_FK');
            $table->index('idProducto', 'ventas_products_FK');

            // Foreign key constraints
            $table->foreign('idNota', 'ventas_lead_logs_FK')
                  ->references('id')->on('lead_logs')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('idLead', 'ventas_leads_FK')
                  ->references('id')->on('leads')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('user_id', 'ventas_users_FK')
                  ->references('id')->on('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('idProducto', 'ventas_products_FK')
                  ->references('id')->on('products')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};