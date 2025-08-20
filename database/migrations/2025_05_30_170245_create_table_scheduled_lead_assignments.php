<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('scheduled_lead_assignments', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('campaign_id');
			$table->unsignedBigInteger('agent_id');
			$table->json('lead_ids');
			$table->dateTime('scheduled_at');
			$table->timestamps();

			$table->index('campaign_id');
			$table->index('agent_id');
			$table->index('scheduled_at');
		});
    }

    public function down()
    {
        Schema::dropIfExists('scheduled_lead_assignments');
    }
};