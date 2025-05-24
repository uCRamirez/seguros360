<?php

namespace App\Http\Controllers\Api;

use App\Classes\Common;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Lead\AssignUserRequest;
use App\Http\Requests\Api\Lead\IndexRequest;
use App\Http\Requests\Api\Lead\StoreRequest;
use App\Http\Requests\Api\Lead\UpdateRequest;
use App\Http\Requests\Api\Lead\DeleteRequest;
use App\Http\Requests\Api\Lead\CreateLeadRequest;
use App\Http\Requests\Api\Lead\CreateBookingRequest;
use App\Http\Requests\Api\Lead\MakeCallRequest;
use App\Http\Requests\Api\Lead\SendEmailRequest;
use App\Http\Requests\Api\Lead\SendMessageRequest;
use App\Http\Requests\Api\Lead\StartFollowRequest;
use App\Models\Campaign;
use App\Models\CampaignUser;
use App\Models\EmailProvider;
use App\Models\Lead;
use App\Models\LeadAux;
use App\Models\User;
use App\Models\LeadLog;
use App\Models\MessageProvider;
use App\Models\Salesman;
use App\Models\Settings;
use App\Models\MessageTemplate;
use App\Notifications\SendLeadMail;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request; 

class LeadController extends ApiBaseController
{
    protected $model = Lead::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    protected function modifyIndex($query)
    {
        $request = request();
        $user = user();

        // Extra Filter For campaign Type
        $query = $query->join('campaigns', 'campaigns.id', '=', 'leads.campaign_id');

        if ($request->has('xid') && $request->xid != "") {
            $campaignId = $this->getIdFromHash($request->xid);
            $query = $query->where('campaign_id', $campaignId)
                ->where('started', 0);

            return $query;
        }

        if ($user->ability('admin', 'view_completed_campaigns')) {
            // Filter By Campaign Status
            if ($request->has('campaign_status') && $request->campaign_status == "completed") {
                $query = $query->where('status', '=', 'completed');
            } else {
                $query = $query->where('status', '!=', 'completed');
            }
        } else {
            $query = $query->where('status', '!=', 'completed');
        }

        // Extra Filters
        if ($request->has('lead_field_name') && $request->lead_field_name != "" && $request->has('lead_field_value') && $request->lead_field_value != "") {
            $filterStringOne = 'field_name":"' . $request->lead_field_name . '","field_value":"' . $request->lead_field_value;
            $filterStringTwo = "field_name':'" . $request->lead_field_name . "','field_value':'" . $request->lead_field_value;
            $query = $query->where('lead_data', 'like', '%' . $filterStringOne . '%')
                ->orWhere('lead_data', 'like', '%' . $filterStringTwo . '%');
        }



        // Started Filter
        $started = $request->has('started') && $request->started == 'not_started' ? 0 : 1;
        $query = $query->where('leads.started', $started);

        // If user either not admin or have leads_view_all permissions
        // then lead last_action_by must be logged in user
        // and only leads started will be visible
        if ($user->ability('admin', 'leads_view_all')) {
            if ($request->has('assign_to') && $request->assign_to != '') {
                $assignUser = $this->getIdFromHash($request->assign_to);
                $query = $query->where('leads.assign_to', $assignUser);
            }
        } else {
            $query = $query->where('leads.assign_to', $user->id);
        }

        return $query;
    }

    public function createLead(CreateLeadRequest $request)
    {

        $user = user();
        // \Log::info('createLead', ['data' => $request]);


        if (!$user->ability('admin', 'leads_create')) {
            throw new ApiException("Not Allowed");
        }

        $xCampaignId = $request->campaign_id;
        $campaignId = $this->getIdFromHash($xCampaignId);
        $loggedUser = user();
        $campaign = Campaign::find($campaignId);

        // Calculating Lead Data Hash
        $leadHashString = "";
        $leadDatas = $request->lead_data;
        foreach ($leadDatas as $leadData) {
            $leadHashString .= strtolower($leadData['field_value']);
        }

        $userId = $this->getIdFromHash($request->assign_to);
        $lead = new Lead();
        $lead->campaign_id = $campaignId;

        if ($request->has('assign_to') && $request->assign_to != '') {
            $userId = $this->getIdFromHash($request->assign_to);

            // TODO - check if this is the user of that campaign
            $lead->assign_to = $userId;
        }

        // Reference Prefix
        if ($campaign->allow_reference_prefix) {
            $lead->reference_number = $campaign->reference_prefix . Carbon::now()->timestamp;
        }

        $lead->lead_data = $request->lead_data;
        $lead->created_by = $loggedUser->id;
        $lead->lead_hash = md5($leadHashString . $campaignId);

        // 5) Mapear campos personales
        $lead->cedula          = $request['cedula']              ?? $lead->cedula;
        $lead->nombre          = $request['nombre']              ?? $lead->nombre;
        $lead->apellido1       = $request['apellido1']           ?? $lead->apellido1;
        $lead->apellido2       = $request['apellido2']           ?? $lead->apellido2;
        $lead->tel1            = $request['tel1']                ?? $lead->tel1;
        $lead->tel2            = $request['tel2']                ?? $lead->tel2;
        $lead->tel3            = $request['tel3']                ?? $lead->tel3;
        $lead->tel4            = $request['tel4']                ?? $lead->tel4;
        $lead->tel5            = $request['tel5']                ?? $lead->tel5;
        $lead->tel6            = $request['tel6']                ?? $lead->tel6;
        $lead->email           = $request['email']               ?? $lead->email;

        $lead->save();

        // Saving Lead Data JSON
        //Common::generateAndSaveLeadData($lead->id);

        // Calculating Lead Counts
        Common::recalculateCampaignLeads($campaignId);

        return ApiResponse::make('Success', []);
    }

    public function createLeadCallLog($leadXId)
    {
        $id = $this->getIdFromHash($leadXId);
        $loggedUser = user();

        $lead = Lead::find($id);

        // Recalculate Time Taken in Lead
        // And insert it in lead
        $recalculateLeadTime = LeadLog::where('lead_id', $lead->id)
            // ->where('user_id', '=', $loggedUser->id)
            ->where('log_type', '=', 'call_log')
            ->sum('time_taken');
        $lead->time_taken = $recalculateLeadTime;
        $lead->started = 1;
        $lead->save();

        $campaign = Campaign::find($lead->campaign_id);
        if ($campaign) {
            if ($campaign->started_on == null) {
                $campaign->started_on = Carbon::now();
                $campaign->status = 'started';
            }
            $campaign->last_action_by = $loggedUser->id;
            $campaign->save();
        }

        // Calculating Lead Counts
        Common::recalculateCampaignLeads($campaign->id);

        // TODO - Check if any other user is not attending this lead
        $callLog = new LeadLog();
        $callLog->lead_id = $lead->id;
        $callLog->log_type = 'call_log';
        $callLog->user_id = $loggedUser->id;
        $callLog->started_on = (int)$lead->time_taken;
        $callLog->time_taken = 0;
        $callLog->date_time = Carbon::now();
        $callLog->save();

        $leadNumber = Lead::where('id', '<=', $lead->id)
            ->where('campaign_id', $lead->campaign_id)
            ->count();


        return ApiResponse::make('Success', [
            'call_log' => $callLog,
            'lead_number' => $leadNumber
        ]);
    }

    public function createBooking(CreateBookingRequest $request)
    {
        $request = request();
        $bookingType = $request->booking_type;
        $leadXId = $request->lead_id;
        $id = $this->getIdFromHash($leadXId);

        $lead = Lead::find($id);

        // TODO - Check if any other user is not attending this lead

        $bookingId = $bookingType == 'lead_follow_up' ? $lead->lead_follow_up_id : $lead->salesman_booking_id;

        if (!is_null($bookingId)) {
            $booking = LeadLog::where('log_type', $bookingType)
                ->where('id', $bookingId)
                ->first();
        }


        if (is_null($bookingId) || (!is_null($bookingId) && !$booking)) {
            $booking = new LeadLog();
            $booking->lead_id = $lead->id;
            $booking->log_type = $bookingType;
        }

        $booking->date_time = $request->date_time;
        $booking->user_id = $request->user_id;
        $booking->notes = $request->notes;
        $booking->save();

        if ($bookingType == 'lead_follow_up') {
            $lead->lead_follow_up_id = $booking->id;
        } else {
            $lead->salesman_booking_id = $booking->id;
        }
        $lead->save();

        $bookingData = LeadLog::select('id', 'date_time', 'user_id')
            ->with(['user' => function ($query) {
                $query->select('id', 'name');
            }])
            ->find($booking->id);

        return ApiResponse::make('Success', [
            'booking' => $bookingData
        ]);
    }

    public function leadCampaignMembers()
    {
        $request = request();
        $bookingType = $request->booking_type;
        $leadXId = $request->lead_id;
        $id = $this->getIdFromHash($leadXId);

        $lead = Lead::select('id', 'campaign_id')->find($id);

        // TODO - Check if any other user is not attending this lead

        $users = [];

        if ($bookingType == 'lead_follow_up') {
            $users = CampaignUser::select('users.id', 'users.name')
                ->join('users', 'users.id', '=', 'campaign_users.user_id')
                ->where('campaign_users.campaign_id', $lead->campaign_id)
                ->get();
        } else if ($bookingType == 'salesman_bookings') {
            $users = Salesman::select('id', 'name')->get();
        }

        return ApiResponse::make('Success', [
            'users' => $users
        ]);
    }

    public function startFollowUp(StartFollowRequest $request)
    {
        $request = request();
        $bookingType = $request->log_type;
        $leadXId = $request->x_lead_id;
        $id = $this->getIdFromHash($leadXId);

        $lead = Lead::find($id);

        // TODO - Check if any other user is not attending this lead

        $booking = LeadLog::where('log_type', $bookingType)
            ->where('lead_id', $lead->id)
            ->first();

        if (!$booking) {
            $booking = new LeadLog();
            $booking->lead_id = $lead->id;
            $booking->log_type = $bookingType;
        }

        $booking->date_time = $request->date_time;
        $booking->user_id = $request->user_id;
        $booking->notes = $request->notes;
        $booking->save();

        $bookingData = LeadLog::select('id', 'date_time', 'user_id')
            ->with(['user' => function ($query) {
                $query->select('id', 'name');
            }])
            ->find($booking->id);

        return ApiResponse::make('Success', []);
    }

    public function leadCampaignStats()
    {
        $request = request();
        $user = user();

        // Total Active/Completed Campaign Counts
        $totalActiveCampaign = Campaign::where('campaigns.status', '!=', 'completed');
        $totalCompletedCampaign = Campaign::where('campaigns.status', '=', 'completed');

        // Total Leads
        $totalLeads = Lead::join('campaigns', 'campaigns.id', '=', 'leads.campaign_id');
        $callMade = Lead::join('campaigns', 'campaigns.id', '=', 'leads.campaign_id')
            ->where('started', 1);
        $callNotMade = Lead::join('campaigns', 'campaigns.id', '=', 'leads.campaign_id')
            ->where('started', 0);
        $totalDuration = Lead::join('campaigns', 'campaigns.id', '=', 'leads.campaign_id');

        if (!$user->ability('admin', 'leads_view_all') || ($request->has('user_id') && $request->user_id != '')) {
            $userId = $user->ability('admin', 'leads_view_all') ? $this->getIdFromHash($request->user_id) : $user->id;

            $totalActiveCampaign = $totalActiveCampaign->join('campaign_users', 'campaign_users.campaign_id', '=', 'campaigns.id')
                ->where('campaign_users.user_id', $userId);
            $totalCompletedCampaign = $totalCompletedCampaign->join('campaign_users', 'campaign_users.campaign_id', '=', 'campaigns.id')
                ->where('campaign_users.user_id', $userId);

            $totalLeads = $totalLeads->where('leads.assign_to', $userId);
            $callMade = $callMade->where('leads.assign_to', $userId);
            $callNotMade = $callNotMade->where('leads.assign_to', $userId);
            $totalDuration = $totalDuration->where('leads.assign_to', $userId);
        }

        if ($request->has('campaign_status') && $request->campaign_status == 'completed') {
            $totalLeads = $totalLeads->where('campaigns.status', '=', 'completed');
            $callMade = $callMade->where('campaigns.status', '=', 'completed');
            $callNotMade = $callNotMade->where('campaigns.status', '=', 'completed');
            $totalDuration = $totalDuration->where('campaigns.status', '=', 'completed');
        } else {
            $totalLeads = $totalLeads->where('campaigns.status', '!=', 'completed');
            $callMade = $callMade->where('campaigns.status', '!=', 'completed');
            $callNotMade = $callNotMade->where('campaigns.status', '!=', 'completed');
            $totalDuration = $totalDuration->where('campaigns.status', '!=', 'completed');
        }

        if ($request->has('campaign_id') && $request->campaign_id != '') {
            $campaignId = $this->getIdFromHash($request->campaign_id);
            $totalLeads = $totalLeads->where('campaigns.id', $campaignId);
            $callMade = $callMade->where('campaigns.id', $campaignId);
            $callNotMade = $callNotMade->where('campaigns.id', $campaignId);
            $totalDuration = $totalDuration->where('campaigns.id', $campaignId);
        }


        $totalActiveCampaign = $totalActiveCampaign->count();
        $totalCompletedCampaign = $totalCompletedCampaign->count();
        $totalLeads = $totalLeads->count();
        $callMade = $callMade->count();
        $callNotMade = $callNotMade->count();
        $totalDuration = $totalDuration->sum('time_taken');


        return ApiResponse::make('Success', [
            'total_active_campaign' => $totalActiveCampaign,
            'total_completed_campaign' => $totalCompletedCampaign,
            'total_leads' => $totalLeads,
            'call_made' => $callMade,
            'call_not_made' => $callNotMade,
            'total_duration' => $totalDuration,
        ]);
    }

    public function sendEmail(SendEmailRequest $request)
    {
        $user = user();
        $email = $request->email;
        $subject = $request->subject;
        $message = $request->message;
        $success = false;
        $xLeadId = $request->lead_id;
        $leadId = $this->getIdFromHash($xLeadId);
        $xProviderId = $request->email_provider_id;
        $providerId =  $this->getIdFromHash($xProviderId);
        $isSchedule = $request->is_schedule;
        $provider = EmailProvider::find($providerId);

        $bodyObject = [
            'campaign' => $request->campaign,
            'to'    => $email,
            'subject'    => $subject,
            'body'    => $message,
            'attachments'    => [],
        ];

        $formData = [
            'body' => json_encode($bodyObject),
            'agent' => $user->ucontact_user,
        ];

        // $mailSetting = Settings::withoutGlobalScope(CompanyScope::class)->where('setting_type', 'email')
        //     ->where('name_key', 'smtp')
        //     ->first();

        if ($isSchedule == true) {

            // TODO - insert the code for schedule sending message

            // TODO - insert message
            $leadLog = new LeadLog();
            $leadLog->lead_id = $leadId;
            $leadLog->log_type = 'email';
            $leadLog->user_id = $user->id;
            $leadLog->date_time = $request->date_time;
            $leadLog->email = $email;
            $leadLog->subject = $subject;
            $leadLog->message = $message;
            $leadLog->notes = json_encode($formData);
            $leadLog->is_schedule = 1;
            $leadLog->auth_token = $provider->auth_token;
            $leadLog->subdomain = $provider->subdomain;
            $leadLog->save();

            $success = true;
        } else {
            $response = Common::sendMailOrMessage($provider->subdomain, $provider->auth_token, $formData, 'email');

            // Check the response
            if ($response->successful()) {

                $bodyResponse = json_decode($response->body(), true);

                // TODO - Save the response id

                // $formData['code'] = $code;
                // $formData['message'] = $message;
                // $formData['guid'] = $bodyResponse && isset($bodyResponse['id']) ? $bodyResponse['id'] : '';

                // TODO - insert message
                $leadLog = new LeadLog();
                $leadLog->lead_id = $leadId;
                $leadLog->log_type = 'email';
                $leadLog->user_id = $user->id;
                $leadLog->date_time = Carbon::now();
                $leadLog->email = $email;
                $leadLog->subject = $subject;
                $leadLog->message = $message;
                $leadLog->notes = json_encode($formData);
                $leadLog->auth_token = $provider->auth_token;
                $leadLog->subdomain = $provider->subdomain;
                $leadLog->save();

                $success = true;
            } else {
                $success = false;
            }
        }

        return ApiResponse::make('Success', [
            'success' => $success,
        ]);
    }

    public function sendMessage(SendMessageRequest $request)
    {
        $user = user();
        $phone = $request->full_number;
        $message = $request->message;
        $code = $request->code;
        $success = false;
        $xLeadId = $request->lead_id;
        $leadId = $this->getIdFromHash($xLeadId);
        $xProviderId = $request->provider_id;
        $providerId =  $this->getIdFromHash($xProviderId);
        $isSchedule = $request->is_schedule;
        $provider = MessageProvider::find($providerId);

        // Prepare the form data (x-www-form-urlencoded)
        $formData = [
            'destination' => $phone,
            'message' => $code,
            'agent' => $user->ucontact_user,
            'campaign' => $provider->campaign,
            'source' => $provider->source,
        ];

        if ($isSchedule == true) {

            // TODO - insert the code for schedule sending message

            // TODO - insert message
            $leadLog = new LeadLog();
            $leadLog->lead_id = $leadId;
            $leadLog->log_type = 'message';
            $leadLog->user_id = $user->id;
            $leadLog->date_time = $request->date_time;
            $leadLog->phone = $phone;
            $leadLog->message = $request->message;
            $leadLog->notes = json_encode($formData);
            $leadLog->is_schedule = 1;
            $leadLog->auth_token = $provider->auth_token;
            $leadLog->subdomain = $provider->subdomain;
            $leadLog->save();

            $success = true;
        } else {
            $response = Common::sendMailOrMessage($provider->subdomain, $provider->auth_token, $formData, 'message');

            // Check the response
            if ($response->successful()) {

                $bodyResponse = json_decode($response->body(), true);
                $formData['code'] = $code;
                $formData['message'] = $message;
                $formData['guid'] = $bodyResponse && isset($bodyResponse['id']) ? $bodyResponse['id'] : '';

                // TODO - insert message
                $leadLog = new LeadLog();
                $leadLog->lead_id = $leadId;
                $leadLog->log_type = 'message';
                $leadLog->user_id = $user->id;
                $leadLog->date_time = Carbon::now();
                $leadLog->phone = $phone;
                $leadLog->message = $request->message;
                $leadLog->notes = json_encode($formData);
                $leadLog->auth_token = $provider->auth_token;
                $leadLog->subdomain = $provider->subdomain;
                $leadLog->save();

                $success = true;
            } else {
                $success = false;
            }
        }

        return ApiResponse::make('Success', [
            'success' => $success,
        ]);
    }

    public function makeCallRequest(MakeCallRequest $request)
    {
        $success = true;

        return ApiResponse::make('Success', [
            'success' => $success,
        ]);
    }

    public function getUphoneCampaigns()
    {
        $user = user();


        $url = 'https://desarrollocr.ucontactcloud.com/IntegraChannels/resources/AgentResources/GetCampaigns';

        // Your basic auth key (replace with your actual key)
        // $authKey = base64_encode($user->ucontact_user . ':' . $user->ucontact_password);
        $authKey = 'dUNBbG9uc28tcHJ1ZWJhczoxYjFjMzE0My1mNzg4LTRlYzYtYTk0YS0zZmNhYzdiYjhmOTM=';

        // Prepare the form data (x-www-form-urlencoded)
        $formData = [
            'agent' => $user->ucontact_user,
            // 'agent' => 'uCAlonso',
            'channel' => '',
        ];

        // Send the POST request with headers and form data
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $authKey
        ])->asForm()->post($url, $formData);

        $allCampaigns = [];
        $allEmailCampaigns = [];

        // Check the response
        if ($response->status() == 200) {
            $bodyResponse = json_decode($response->body(), true);

            // Use array_filter to find the array with channel value "telephony"
            $telephonyCampaign = array_filter($bodyResponse, function ($item) {
                return $item['channel'] === 'telephony';
            });
            $emailCampaigns = array_filter($bodyResponse, function ($item) {
                return $item['channel'] === 'email';
            });

            // array_filter returns an array with matched elements, so reset to get the first one
            $telephonyCampaign = reset($telephonyCampaign);
            $emailCampaigns = reset($emailCampaigns);

            // Output the result
            if ($telephonyCampaign && isset($telephonyCampaign['campaigns'])) {
                $allCampaigns = $telephonyCampaign['campaigns'];
            }
            if ($emailCampaigns && isset($emailCampaigns['campaigns'])) {
                $allEmailCampaigns = $emailCampaigns['campaigns'];
            }
        }
        return ApiResponse::make('Success', [
            'campaigns' => $allCampaigns,
            'email_campaigns' => $allEmailCampaigns,
        ]);
    }

    // public function getUphoneDataByLeadId($id){
    //     $featchId = $this->getIdFromHash($id);
    //     $leadId = Lead::find($featchId);
    //     $uphoneData = LeadLog::where('lead_id', $leadId->id)->with(['lead','campaignData'])->get();
    //     return ApiResponse::make('uphoneData', [$uphoneData]);

    // }

    public function saveAssignUser(AssignUserRequest $request)
    {
        $leadId = $this->getIdFromHash($request->lead_id);
        $campaignUser = Lead::find($leadId);

        if ($request->has('assign_to') && $request->assign_to != '') {
            $userId = $this->getIdFromHash($request->assign_to);
            $campaignUser->assign_to = $userId;
        }
        $campaignUser->save();

        return ApiResponse::make('Success', []);
    }

    public function addMultipleUser(AssignUserRequest $request)
    {
        foreach ($request->lead_ids as $leadIds) {
            $leadId = $this->getIdFromHash($leadIds);
            $multUserLeadIds = Lead::find($leadId);

            if ($request->has('assign_to') && $request->assign_to != '') {
                $userId = $this->getIdFromHash($request->assign_to);
                $multUserLeadIds->assign_to = $userId;
            }

            $multUserLeadIds->save();
        }
        return ApiResponse::make('Success', []);
    }

    // PARA OBTENER EL CODIGO DEL CLIENTE QUE SE LLAMA
    public function findLeadByPhone(Request $request)
    {
        $phone = $request->input('phone');

        if (!$phone) {
            throw new ApiException("Phone number is required");
        }

        // Usamos LIKE para buscar en la columna 'lead_data_json' una cadena que contenga
        // "phone":"<número>".
        $lead = Lead::where('lead_data_json', 'like', '%"phone":"' . $phone . '"%')->first();

        if ($lead) {
            return ApiResponse::make('Lead found', [
                'x_lead_id' => $lead->xid,
                'lead' => $lead,
            ]);
        }

        return ApiResponse::make('Lead not found', [], 404);
    }
    // PARA OBTENER EL CODIGO VALIDANDO DE CUAL CAMPANA ESTA ENTRANDO LA LLAMADA
    public function findLeadByPhoneCampaign(Request $request)
    {
        $phone = $request->input('phone');
        $campaignName = $request->input('campaign');

        if (!$phone) {
            throw new ApiException("Phone number is required");
        }
        if (!$campaignName) {
            throw new ApiException("Campaign is required");
        }

        $lead = Lead::join('campaigns', 'campaigns.id', '=', 'leads.campaign_id')
            ->where('campaigns.name', $campaignName)
            ->whereRaw("LOCATE(?, lead_data_json) > 0", [$phone])
            ->select('leads.*')
            ->first(); // Usa first() para obtener un solo registro

        if ($lead) {
            return ApiResponse::make('Lead found', [
                'x_lead_id' => $lead->xid, // Ahora $lead es una instancia de Lead
                'lead'      => $lead,
        ]);
        } else {
            // Si no se encontró el lead, buscamos la campaña por nombre
            $campaign = Campaign::where('name', $campaignName)->first();
            if ($campaign) {
                return ApiResponse::make('Campaign found', [
                    'x_campaign_id' => $campaign->xid,
                ]);
            } else {
                return ApiResponse::make('Campaign not found', [], 404);
            }
        }
    }

}
