<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScheduledLeadAssignment;
use App\Models\Lead;
use Carbon\Carbon;

class RunScheduledAssignments extends Command
{
    protected $signature = 'assignments:run';
    protected $description = 'Assign scheduled leads to agents';

    public function handle()
    {
        $now = Carbon::now();

        $tasks = ScheduledLeadAssignment::where('scheduled_at', '<=', $now)->get();

        foreach ($tasks as $task) {
            $leadIds = $task->lead_ids;

            Lead::whereIn('id', $leadIds)
                ->where('campaign_id', $task->campaign_id)
                ->update(['assign_to' => $task->agent_id]);

            $task->delete();
        }

        $this->info("Scheduled assignments processed.");
    }
}
