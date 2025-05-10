<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads_history', function (Blueprint $table) {
            // Charset & Collation
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();

            // Personal data
            $table->string('cedula', 200)->nullable();
            $table->string('nombre', 200)->nullable();
            $table->string('apellido1', 200)->nullable();
            $table->string('apellido2', 200)->nullable();
            
            // Estado civil: entero sin auto_increment
            $table->unsignedBigInteger('estadoCivil_id')->nullable();
            $table->foreign('estadoCivil_id')
                  ->references('id')
                  ->on('lead_status')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->integer('hijos')->nullable();
            $table->dateTime('fechaNacimiento')->nullable();
            $table->integer('edad')->nullable();
            $table->string('nacionalidad', 200)->nullable();

            // Contact data
            $table->string('tel1', 100)->nullable();
            $table->string('tel2', 100)->nullable();
            $table->string('tel3', 100)->nullable();
            $table->string('tel4', 100)->nullable();
            $table->string('tel5', 100)->nullable();
            $table->string('tel6', 100)->nullable();
            $table->string('email', 200)->nullable();

            // Location & source
            $table->string('provincia', 200)->nullable();
            $table->string('canton', 200)->nullable();
            $table->string('distrito', 200)->nullable();
            $table->string('nombreBase', 200)->nullable();
            $table->string('tarjeta', 200)->nullable();

            // Relations
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')
                  ->references('id')
                  ->on('companies')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->string('reference_number', 191)->nullable();

            $table->unsignedBigInteger('campaign_id')->nullable();
            $table->foreign('campaign_id')
                  ->references('id')
                  ->on('campaigns')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            // Lead data
            $table->longText('lead_data')->nullable();
            $table->longText('lead_data_json')->nullable();

            $table->boolean('started')->default(false);

            // Legacy status
            $table->unsignedBigInteger('lead_status')->nullable();

            $table->unsignedInteger('time_taken')->default(0);

            $table->unsignedBigInteger('lead_follow_up_id')->nullable();
            $table->unsignedBigInteger('salesman_booking_id')->nullable();

            // Action tracking
            $table->unsignedBigInteger('first_action_by')->nullable();
            $table->foreign('first_action_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->unsignedBigInteger('last_action_by')->nullable();
            $table->foreign('last_action_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->string('lead_hash', 191)->nullable();

            $table->integer('notes_count')->default(0);

            // New status reference
            $table->unsignedBigInteger('lead_status_id')->nullable();
            $table->foreign('lead_status_id')
                  ->references('id')
                  ->on('lead_status')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            // Assignment
            $table->unsignedBigInteger('assign_to')->nullable();
            $table->foreign('assign_to')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads_history');
    }
};
