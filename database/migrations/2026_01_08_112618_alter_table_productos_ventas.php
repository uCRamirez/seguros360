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
        Schema::table('productos_ventas', function (Blueprint $table) {
            $table->decimal('precio_digitado', 12, 2)->default(0.00)->comment('Si hay precio digitado, es el que se usa para el total')->after('precio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos_ventas', function (Blueprint $table) {
            $table->dropColumn('precio_digitado');
        });
    }
};
