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
        Schema::table('message_templates', function (Blueprint $table) {
            $table->string('code')->nullable()->default(null);
            $table->bigInteger('message_provider_id')->unsigned()->nullable()->default(null);
            $table->foreign('message_provider_id')->references('id')->on('message_providers')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('message_templates', function (Blueprint $table) {
            $table->dropColumn('code');

            $table->dropForeign('message_templates_message_provider_id_foreign');
            $table->dropColumn('message_provider_id');
        });
    }
};
