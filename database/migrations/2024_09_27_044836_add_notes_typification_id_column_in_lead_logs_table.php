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
        Schema::table('lead_logs', function (Blueprint $table) {
            $table->bigInteger('notes_typification_id_1')->unsigned()->nullable()->default(null);
            $table->foreign('notes_typification_id_1')->references('id')->on('notes_typifications')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('notes_typification_id_2')->unsigned()->nullable()->default(null);
            $table->foreign('notes_typification_id_2')->references('id')->on('notes_typifications')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('notes_typification_id_3')->unsigned()->nullable()->default(null);
            $table->foreign('notes_typification_id_3')->references('id')->on('notes_typifications')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead_logs', function (Blueprint $table) {
            $table->dropForeign('lead_logs_notes_typification_id_1_foreign');
            $table->dropForeign('lead_logs_notes_typification_id_2_foreign');
            $table->dropForeign('lead_logs_notes_typification_id_3_foreign');

            $table->dropColumn('notes_typification_id_1');
            $table->dropColumn('notes_typification_id_2');
            $table->dropColumn('notes_typification_id_3');
        });
    }
};
