<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\LeadLog\IndexRequest;
use App\Http\Requests\Api\LeadLog\StoreRequest;
use App\Http\Requests\Api\LeadLog\UpdateRequest;
use App\Http\Requests\Api\LeadLog\DeleteRequest;
use App\Models\LeadLog;
use App\Models\Lead;
use Carbon\Carbon;
use Examyou\RestAPI\Exceptions\ApiException;
use App\Models\CampaignUser;

class LeadLogController extends ApiBaseController
{
    protected $model = LeadLog::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    protected function modifyIndex($query)
    {
        $user = user();
        $request = request();
        //  \Log::info('modifyIndex', ['$user' => $user]);


        $usersIds = CampaignUser::where('user_id', $user->id)
            ->pluck('user_id');

         \Log::info('modifyIndex', ['$usersIds' => $usersIds]);

        $query = $query->join('leads', 'leads.id', '=', 'lead_logs.lead_id')
            ->join('campaigns', 'campaigns.id', 'leads.campaign_id');

        if (!$user->ability('admin', 'leads_view_all')) {
            if ($request->has('log_type') && $request->log_type == 'salesman_bookings') {
                $query = $query->where('lead_logs.created_by_id', $user->id);
                // \Log::info('modifyIndex', ['data' => 1]);
            }
            else if ($request->has('log_type') && $request->log_type == 'message') {
                // \Log::info('modifyIndex', ['data' => 2]);
                $query = $query->where('lead_logs.log_type', 'message');
            }
            else if (
                $request->has('log_type') && $request->has('page_name') &&
                $request->page_name == 'lead_action' &&
                $request->has('lead_id') && $request->lead_id != "" &&
                ($request->log_type == 'call_log' || $request->log_type == 'notes' || $request->log_type == 'all')
            ) {
                // \Log::info('modifyIndex', ['data' => 3]);
                // When request comes from TakeLeadActionPage
            } else {
                // \Log::info('modifyIndex', ['data' => 4]);
                $query = $query->where('lead_logs.user_id', $user->id);
            }
        }else{
            if ($user->ability($user->role->name, 'leads_view_all')) {
                $campaignIds = CampaignUser::where('user_id', $user->id)
                                    ->pluck('campaign_id');
                $teammateIds = CampaignUser::whereIn('campaign_id', $campaignIds)
                                    ->distinct()
                                    ->pluck('user_id');
                $allUserIds = $teammateIds->push($user->id)->unique();
                \Log::info('modifyIndex', ['allUserIds' => $allUserIds]);
                $query = $query->whereIn('lead_logs.user_id', $allUserIds);
            }
        }

        // For Lead Follow Up showing
        // only not actioned leads
        if ($request->has('log_type') && $request->log_type == 'lead_follow_up') {
            $query = $query->where('booking_status', '!=', 'actioned');
        }

        // Extra Filters
        if ($user->ability('admin', 'view_completed_campaigns')) {
            if ($request->has('campaign_status') && $request->campaign_status == "completed") {
                $query = $query->where('campaigns.status', '=', 'completed');
            } else {
                $query = $query->where('campaigns.status', '!=', 'completed');
            }
        } else {
            $query = $query->where('campaigns.status', '!=', 'completed');
        }

        if ($request->has('lead_id') && $request->lead_id != "") {
            $leadId = $this->getIdFromHash($request->lead_id);

            $query = $query->where('lead_logs.lead_id', $leadId);
        }

        if ($request->has('campaign_id') && $request->campaign_id != "") {
            $campaignId = $this->getIdFromHash($request->campaign_id);

            $query = $query->where('leads.campaign_id', $campaignId);
        }

        if ($request->has('log_type') && $request->log_type == 'call_log') {
            $query = $query->where('lead_logs.time_taken', '>', 0);
        }

        // Dates Filters
        if ($request->has('dates') && $request->dates != "") {
            $dates = explode(',', $request->dates);
            $startDate = $dates[0];
            $endDate = $dates[1];

            $query = $query->whereRaw('lead_logs.date_time >= ?', [$startDate])
                ->whereRaw('lead_logs.date_time <= ?', [$endDate]);
        }

        \Log::info('modifyIndex SQL', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);

        return $query;
    }

    public function storing($leadLog)
    {
        $loggedUser = user();
        $request = request();
        // \Log::info('storing', ['data' => $leadLog]);


        if ($request->log_type == '' || $request->log_type != 'notes') {
            throw new ApiException("Not Allowed");
        }

        $leadLog->user_id = $loggedUser->id;
        $leadLog->date_time = Carbon::now();

        return $leadLog;
    }

    public function stored($leadLog)
    {
        // \Log::info('stored', ['data' => $leadLog]);
        $notesCount = LeadLog::where('lead_id', $leadLog->lead_id)
        ->where('log_type', 'notes')
        ->count();
        $lead = Lead::find($leadLog->lead_id);
        $lead->notes_count = $notesCount;
        $lead->save();

    }

    public function updating($leadLog)
    {
        $request = request();
        // \Log::info('updating', ['data' => $leadLog]);
        if ($request->has('user_id') || $request->has('date_time') || $request->log_type == '' || $request->log_type != 'notes') {
            throw new ApiException("Not Allowed");
        }

        return $leadLog;
    }
}