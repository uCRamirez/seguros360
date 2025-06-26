<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar migraciones.
     */
    public function up(): void
    {
        Schema::create('productos_ventas', function (Blueprint $table) {
            // PK
            $table->bigIncrements('id');

            // FK al encabezado de venta y producto
            $table->unsignedBigInteger('idVenta')->nullable();
            $table->unsignedBigInteger('idProducto')->nullable();

            // Datos de la venta
            $table->integer('cantidadProducto')->nullable();
            $table->integer('precio')->nullable();

            // Índices
            $table->index('idVenta', 'productos_ventas_ventas_FK');
            $table->index('idProducto', 'productos_ventas_products_FK');

            // Restricciones de llave foránea
            $table->foreign('idVenta', 'productos_ventas_ventas_FK')
                  ->references('idVenta')->on('ventas')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('idProducto', 'productos_ventas_products_FK')
                  ->references('id')->on('products')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Revertir migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos_ventas');
    }
};
