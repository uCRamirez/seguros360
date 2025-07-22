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
        Schema::create('notes_typifications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('campaign_id')->unsigned()->nullable()->default(null);
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->bigInteger('parent_id')->unsigned()->nullable()->default(null);
            $table->boolean('sale')->default(false);
            $table->boolean('schedule')->default(false);
            $table->boolean('no_contact')->default(false);
            $table->boolean('status')->default(true);
			$table->foreign('parent_id')->references('id')->on('notes_typifications')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes_typifications');
    }
};