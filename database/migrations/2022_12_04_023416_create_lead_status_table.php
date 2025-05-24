<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
    //     Schema::create('lead_status', function (Blueprint $table) {
    //         $table->id();
    //         $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
    //         $table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');
    //         $table->string('name');
    //         $table->string('color', 20)->default('1f87e8');
    //         $table->string('type');
    //         $table->timestamps();
    //     });

    //     Schema::table('leads', function (Blueprint $table) {
    //         $table->bigInteger('lead_status_id')->unsigned()->nullable()->default(null);
    //         $table->foreign('lead_status_id')->references('id')->on('lead_status')->onUpdate('cascade')->onDelete('set null');
    //     });
    // }
    public function up(): void
    {
        Schema::create('lead_status', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->string('color', 20)->default('#1f87e8');
            $table->string('type');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1) Suelta la FK company_id en lead_status
        Schema::table('lead_status', function (Blueprint $table) {
            if (Schema::hasColumn('lead_status', 'company_id')) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            }
        });

        // 2) Ahora sí elimina la tabla
        Schema::dropIfExists('lead_status');
    }



};
