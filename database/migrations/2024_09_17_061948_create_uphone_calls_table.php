<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\UphoneCalls;
use App\Models\Campaign;
use App\Models\Lead;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('uphone_calls', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('user_id')->unsigned()->nullable()->default(null);
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('lead_id')->unsigned()->nullable()->default(null);
            $table->foreign('lead_id')->references('id')->on('leads')->onUpdate('cascade')->onDelete('set null');
            $table->bigInteger('campaign_id')->unsigned()->nullable()->default(null);
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onUpdate('cascade')->onDelete('set null');
            $table->string('campaign')->nullable()->default(null);
            $table->string('client_id')->nullable()->default(null);
            $table->string('date')->nullable()->default(null);
            $table->string('direction')->nullable()->default(null);
            $table->string('duration')->nullable()->default(null);
            $table->string('guid')->nullable()->default(null);
            $table->string('discriptions')->nullable()->default(null);
            $table->string('number')->nullable()->default(null);
            $table->longText('response_data')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uphone_calls');
    }
};
