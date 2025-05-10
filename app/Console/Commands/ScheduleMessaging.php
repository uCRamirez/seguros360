<?php

namespace App\Console\Commands;

use App\Classes\Common;
use App\Models\LeadLog;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ScheduleMessaging extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'schedule:messaging';

    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'Get schedule lead logs for email and message. and send message based on time.';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public function __construct()

    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle()
    {
        $allLeadLogs = LeadLog::where('date_time', '<', Carbon::now('UTC'))
            ->where('is_schedule', 1)
            ->where(function ($query) {
                $query->where('log_type', 'email')
                    ->orWhere('log_type', 'message');
            })
            ->where('attempt_count', '<=', 3)
            ->get();

        foreach ($allLeadLogs as $allLeadLog) {
            $formData = json_decode($allLeadLog->notes, true);

            $response = Common::sendMailOrMessage($allLeadLog->subdomain, $allLeadLog->auth_token, $formData, $allLeadLog->log_type);

            // Check the response
            if ($response->successful()) {
                $allLeadLog->is_schedule = 0;
            } else {
                $allLeadLog->attempt_count += 1;
            }
            $allLeadLog->save();
        }
    }
}
