<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiBaseController;
use Examyou\RestAPI\ApiResponse;
use App\Models\Campaign;
use App\Models\CampaignUser;
use Illuminate\Support\Facades\DB;



class DataFormController extends ApiBaseController
{

    public function userCampaigns(Request $request){
        $userName = user()->name;

        $campaigns = Campaign::select('campaigns.name')
            ->join('campaign_users', 'campaigns.id', '=', 'campaign_users.campaign_id')
            ->join('users', 'campaign_users.user_id', '=', 'users.id')
            ->where('users.name', $userName)
            ->where('users.status', 'enabled')
            ->get();

        return ApiResponse::make('OK', [
            'user_campaigns' => $campaigns
        ]);
    }

    public function agentScheduled(Request $request)
    {
        $userName = user()->name;
        $schedules = DB::table('CRM_Scheduled')
            ->where('active', 1)
            ->where('agent', $userName)
            ->orderBy('dateSchedule', 'asc')
            ->get();

        return ApiResponse::make('OK', [
            'scheduled' => $schedules
        ]);
    }



}
