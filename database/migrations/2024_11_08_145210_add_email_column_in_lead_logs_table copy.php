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
            $table->string('subject')->nullable()->default(null);
            $table->string('email')->nullable()->default(null);
            $table->string('auth_token')->nullable()->default(null);
            $table->string('subdomain')->nullable()->default(null);
            $table->integer('attempt_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead_logs', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->dropColumn('auth_token');
            $table->dropColumn('subdomain');
            $table->dropColumn('subject');
            $table->dropColumn('attempt_count');
        });
    }
};
