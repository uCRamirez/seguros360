<?php

namespace App\Http\Controllers\Api;

use App\Classes\Common;
use App\Exports\CampaignLeadExport;
use App\Exports\CampaignLeadExportBase;
use App\Exports\ExportReportInfo;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Campaign\IndexRequest;
use App\Http\Requests\Api\Campaign\StoreRequest;
use App\Http\Requests\Api\Campaign\UpdateRequest;
use App\Http\Requests\Api\Campaign\DeleteRequest;
use App\Http\Requests\Api\Campaign\ExportReport;
use App\Http\Requests\Api\Campaign\CampaignLeadActionRequest;
use App\Http\Requests\Api\Campaign\EmailTemplatesRequest;
use App\Http\Requests\Api\Campaign\RecycleCampaignLeadsRequest;
use App\Http\Requests\Api\Campaign\SkipDeleteLeadRequest;
use App\Http\Requests\Api\Campaign\StopCampaignRequest;
use App\Http\Requests\Api\Campaign\TakeLeadActionRequest;
use App\Http\Requests\Api\Campaign\ImportRequest;
use App\Imports\LeadImport;
use App\Models\Campaign;
use App\Models\PlantillaCalidad;
use App\Models\CampaignUser;
use App\Models\EmailTemplate;
use App\Models\Form;
use App\Models\User;
use App\Models\Lead;
use App\Models\LeadAux;
use App\Models\LeadLog;
use Carbon\Carbon;
use Examyou\RestAPI\ApiResponse;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\Log;
// use Maatwebsite\Excel\Excel as ExcelFormat;

class CampaignController extends ApiBaseController
{
    protected $model = Campaign::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;
    protected $exportReport = ExportReport::class;

    protected function modifyIndex($query)
    {
        $user = user();
        $request = request();
        // \Log::info('modifyIndex - campaign', ['request' => $request]);


        // Checking use is user of camaign
        if (!$user->ability('admin', 'campaigns_view_all')) {
            $query = $query->join('campaign_users', 'campaign_users.campaign_id', '=', 'campaigns.id')
                ->where('campaign_users.user_id', $user->id);
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

        return $query;
    }


    public function exportReportNotes($camps,$star,$end)
    {
        config(['api.cors' => false]);
        $camps = is_array($camps) ? $camps : explode(',', $camps);
        // \Log::info('data', [$camps]);
        // \Log::info('data', [$star]);
        // \Log::info('data', [$end]);

        return Excel::download(new ExportReportInfo($camps, $star, $end), 'invoices.xlsx');
    }


    public function storing($campaign)
    {
        $request = request();
        $loggedUser = user();

        $campaign->created_by = $loggedUser->id;
        $campaign->updated_by = $loggedUser->id;
        $campaign->detail_fields = json_decode($request->detail_fields);

        return $campaign;
    }

    public function updating($campaign)
    {
        $loggedUser = user();
        $request = request();

        $campaign->updated_by = $loggedUser->id;
        $campaign->detail_fields = json_decode($request->detail_fields);

        return $campaign;
    }

    public function stored(Campaign $campaign)
    {
        //\Log::info('stored - campaign', ['campaign' => $campaign]);
        $request = request();
        $allUsers = json_decode($request->user_id);

        foreach ($allUsers as $allUser) {
            $userId = $this->getIdFromHash($allUser);

            $campaignUser = new CampaignUser();
            $campaignUser->user_id = $userId;
            $campaignUser->campaign_id = $campaign->id;
            $campaignUser->save();
        }

        // Saving csv file data
        $this->saveCsvFileData($campaign);
    }

    public function updated(Campaign $campaign)
    {
        // \Log::info('updated - campaign', ['campaign' => $campaign]);
        $request = request();
        $allUsers = json_decode($request->user_id);
        CampaignUser::where('campaign_id', $campaign->id)->delete();

        foreach ($allUsers as $allUser) {
            $userId = $this->getIdFromHash($allUser);

            $campaignUser = new CampaignUser();
            $campaignUser->user_id = $userId;
            $campaignUser->campaign_id = $campaign->id;
            $campaignUser->save();
        }

        // Saving csv file data
        $this->saveCsvFileData($campaign);
    }

    public function saveCsvFileData($campaign)
    {
        $request = request();
        // \Log::info('saveCsvFileData - campaign', ['campaign' => $campaign]);

        // Saving csv file data
        if ($request->hasFile('file')) {
            $formId = $campaign->form_id;
            $form = Form::find($formId);
            $formFields = $form && $form->form_fields ? $form->form_fields : [];

            $importLeadFields = json_decode($request->import_lead_fields, true);

            Excel::import(new LeadImport($importLeadFields, $formFields, $campaign->id), request()->file('file'));
        }
    }

    public function stopCampaign(StopCampaignRequest $request)
    {
        $loggedUser = user();
        $xid = $request->x_campaign_id;
        $id = $this->getIdFromHash($xid);

        $campaign = Campaign::find($id);
        $campaign->status = 'completed';
        $campaign->completed_on = Carbon::now();
        $campaign->completed_by = $loggedUser->id;
        $campaign->save();

        return ApiResponse::make('Success', []);
    }

    public function takeAction(CampaignLeadActionRequest $request)
    {
        $loggedUser = user();
        // \Log::info('request', [$request]);

        $phone = $request->call_phone;
        $xid = $request->x_campaign_id;
        $campaignId = $this->getIdFromHash($xid);

        $campaign = Campaign::with('form')->findOrFail($campaignId);

        // 1) Intento por teléfono exacto dentro de la campaña
        $lead = null;
        if (!empty($phone)) {
            $lead = Lead::where('campaign_id', $campaign->id)
                ->where(function ($q) use ($phone) {
                    $q->where('tel1', $phone)
                    ->orWhere('tel2', $phone)
                    ->orWhere('tel3', $phone)
                    ->orWhere('tel4', $phone)
                    ->orWhere('tel5', $phone)
                    ->orWhere('tel6', $phone);
                })
                ->orderByDesc('id') // si hay varios, toma el más reciente
                ->first();

            // \Log::info('lead_lookup_by_phone', [
            //     'phone'   => $phone,
            //     'lead_id' => optional($lead)->id,
            // ]);
        }

        // 2) Si no existe por teléfono: crear (si hay teléfono) o caer al próximo no iniciado (si no hay teléfono)
        if (!$lead) {
            if (!empty($phone)) {
                $lead = $this->createNewLead($campaign, $phone);
                // \Log::info('lead_created', ['lead_id' => $lead->id, 'phone' => $phone]);
            } 
            // else {
            //     $lead = $this->upcomingNotStartedLead($campaign->id);
            //     \Log::info('lead_upcoming_fallback', ['lead_id' => optional($lead)->id]);
            // }
        }

        // 3) Marcar campaña como iniciada/actualizar actor
        if ($campaign->started_on == null) {
            $campaign->started_on = \Carbon\Carbon::now();
            $campaign->status = 'started';
        }
        $campaign->last_action_by = $loggedUser->id;
        $campaign->save();

        // \Log::info('lead', [$lead]);

        return ApiResponse::make('Success', [
            'x_lead_id' => $lead->xid,
        ]);
    }


    public function takeAction_original(CampaignLeadActionRequest $request)
    {
        $loggedUser = user();
        // \Log::info('request', [$request]);

        $phone = $request->call_phone;
        $xid = $request->x_campaign_id;
        $id = $this->getIdFromHash($xid);

        $campaign = Campaign::with('form')->find($id);
        // $upcomingLeadAction = $campaign->upcoming_lead_action;

        // Next not started lead
        $lead = $this->upcomingNotStartedLead($campaign->id);

        // No Lead found
        // Creating new lead
        if ($lead == null) {
            $lead = $this->createNewLead($campaign,$phone);
        }

        // \Log::info('lead', [$lead]);


        if ($campaign->started_on == null) {
            $campaign->started_on = Carbon::now();
            $campaign->status = 'started';
        }
        $campaign->last_action_by = $loggedUser->id;
        $campaign->save();

        // Calculating Lead Counts
        // Common::recalculateCampaignLeads($campaign->id);

        return ApiResponse::make('Success', [
            'x_lead_id' => $lead->xid,
        ]);
    }

    public function upcomingNotStartedLead($id)
    {
        $loggedUser = user();

        // Taking latest lead
        // which is not-started
        $lead = Lead::where('campaign_id', $id)
            ->where('started', 0)
            // ->whereNull('first_action_by')
            ->where('assign_to', $loggedUser->id)
            ->oldest('id')
            ->first();

        if ($lead) {

            $lead->started = 1;
            $lead->first_action_by = $loggedUser->id;
            $lead->last_action_by = $loggedUser->id;
            $lead->save();

            return $lead;
        } else {
            return null;
        }
    }

    public function createNewLead($campaign, $phone)
    {
        $loggedUser = user();

        $lead = new Lead();
        $lead->campaign_id = $campaign->id;
        $lead->tel1 = $phone ? $phone : null;
        // Reference Prefix
        // if ($campaign->allow_reference_prefix) {
        //     $lead->reference_number = $campaign->reference_prefix . Carbon::now()->timestamp;
        // }

        $lead->started = 1;
        $lead->assign_to = $loggedUser->id;
        $lead->first_action_by = $loggedUser->id;
        $lead->last_action_by = $loggedUser->id;
        $lead->created_by = $loggedUser->id;
        $lead->save();

        // Common::recalculateCampaignLeads($campaign->id);ƒ

        return $lead;
    }

    public function takeLeadAction(TakeLeadActionRequest $request)
    {
        //\Log::info('takeLeadAction', ['request' => $request]);

        $data       = $request->validated();
        $actionType = $data['action_type'] ?? null;
        $currentLead = Common::saveLeadData($data);
        $lead = Common::takeLeadAction(
            $actionType,
            $currentLead->id,
            $currentLead->campaign_id
        );
        return ApiResponse::make('Success', [
            'lead' => $lead ?: null,
        ]);
    }

    public function updateActionedLead(TakeLeadActionRequest $request)
    {
        //\Log::info('updateActionedLead', ['request' => $request]);
        $data = $request->validated();
        $lead = Common::saveLeadData($data);

        return ApiResponse::make('Success', [
            'lead' => $lead ?: null,
        ]);
    }
    

    public function skipAndDeleteLead(SkipDeleteLeadRequest $request)
    {
        $xLeadId = $request->lead_id;
        $leadId = $this->getIdFromHash($xLeadId);

        $lead = Lead::find($leadId);
        $campaignId = $lead->campaign_id;
        $lead->delete();

        // Calculating Lead Counts
        Common::recalculateCampaignLeads($campaignId);

        // Getting next lead
        $lead = Common::takeLeadAction('next', $leadId, $campaignId);

        return ApiResponse::make('Success', [
            'lead' => $lead
        ]);
    }

    public function userCampaigns()
    {
        $user = user();

        $userCampaigns = CampaignUser::select('campaign_id')->where('user_id', $user->id)->get();

        return ApiResponse::make('Success', [
            'user_campaigns' => $userCampaigns
        ]);
    }

    public function campaignEmailTemplates(EmailTemplatesRequest $request)
    {
        $user = user();
        $xLeadId = $request->x_lead_id;
        $leadId = $this->getIdFromHash($xLeadId);

        $lead = Lead::find($leadId);

        $userCampaigns = EmailTemplate::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->where('status', 1);
        })
            ->orWhere('')
            ->get();

        return ApiResponse::make('Success', [
            'user_campaigns' => $userCampaigns
        ]);
    }

    public function campaignExportLead($xCampaignId)
    {
        // Setting this because without this it was giving error
        // Check code in vendor/examyou/rest-api/src/Middleware/ApiMiddleware.php
        // Solution is $response->headers->set
        // headers will be set using above
        // https://stackoverflow.com/questions/74902274/call-to-undefined-method-symfony-component-httpfoundation-binaryfileresponsewi
        config(['api.cors' => false]);

        $campaignId = $this->getIdFromHash($xCampaignId);
        $campaign = Campaign::find($campaignId);

        return Excel::download(new CampaignLeadExport($campaignId), 'invoices.xlsx');
    }

    public function campaignExportLeadBase($id,$base,$state)
    {
        // \Log::info('campaignExportLeadBase', ['base' => $base]);
        config(['api.cors' => false]);

        $campaignId = $this->getIdFromHash($id);
        $campaign = Campaign::find($campaignId);

        return Excel::download(new CampaignLeadExportBase($campaignId,$base,$state), 'invoices.xlsx');
    }

    public function getAllCampaignExcept()
    {
        $request = request();

        $ignoreCampaignId = $this->getIdFromHash($request->xid);
        $campaigns =  Campaign::where('id', '!=', $ignoreCampaignId)->get();

        return ApiResponse::make('success', [
            'campaigns' => $campaigns
        ]);
    }

    public function recycleCampaignLeads(RecycleCampaignLeadsRequest $request)
    {
        $campaignId = $this->getIdFromHash($request->campaign_id);
        $manageNonId = $this->getIdFromHash($request->xid);
        $selectedRowKeys = $request->selectedRowKeys;
        $campaign = Campaign::find($campaignId);

        $assinedUserIndex = 0;

        $campaignUsers = CampaignUser::join('users', 'users.id', '=', 'campaign_users.user_id')
            ->where('campaign_users.campaign_id', $campaignId)
            ->orderBy('users.name')
            ->pluck('user_id')
            ->toArray();

        if ($request->select_lead_type == 'all_non_managed_leads') {
            $nonManageList = Lead::where('started', 0)
                ->where('campaign_id', $manageNonId)
                ->get();

            if ($nonManageList != "") {
                foreach ($nonManageList as $nonManage) {
                    $nonManage->campaign_id = $campaignId;

                    $agentId = $campaignUsers[$assinedUserIndex];
                    $assinedUserIndex = $assinedUserIndex < count($campaignUsers) - 1 ? $assinedUserIndex + 1 : 0;

                    $nonManage->assign_to = $agentId;
                    $nonManage->save();
                }
            }
        } else if ($request->select_lead_type == 'select_non_managed_leads' && $selectedRowKeys != "") {
            foreach ($selectedRowKeys as $xid) {
                $leadId = $this->getIdFromHash($xid);
                $lead = Lead::find($leadId);
                if ($lead != "") {
                    $lead->campaign_id = $campaignId;

                    $agentId = $campaignUsers[$assinedUserIndex];
                    $assinedUserIndex = $assinedUserIndex < count($campaignUsers) - 1 ? $assinedUserIndex + 1 : 0;

                    $lead->assign_to = $agentId;

                    $lead->save();
                }
            }
        }



        Common::recalculateCampaignLeads($campaignId);
        Common::recalculateCampaignLeads($manageNonId);
        return ApiResponse::make('success', []);
    }
    // Traer toda las campañas de telefonia de ucontact
    public function getUContactCampaigns()
    {
        $subdomain = env('UCONTACT_SUB_DOMINIO');
        $authKey   = env('UCONTACT_API_KEY');

        $url = "https://{$subdomain}.ucontactcloud.com/IntegraChannels/resources/webhook/getCRMCampaigns";


        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $authKey
        ])->asForm()->post($url);

        $allCampaigns = [];

        if ($response->status() == 200) {
            $bodyResponse = json_decode($response->body(), true);
            $allCampaigns = $bodyResponse;
        }

        return ApiResponse::make($allCampaigns);
    }

    public function getHomologateCampaigns(Request $request)
    {
        $campaignName = $request->input('campaign');

        if (!$campaignName) {
            throw new ApiException("Campaign is required");
        }

        $campaign = Campaign::select('id', 'name', 'uc_campaigns', 'form')
            ->whereRaw("LOCATE(?, uc_campaigns) > 0", [$campaignName])
            ->first();

        if ($campaign) {
            // Asignamos el alias usando el accesor getXidAttribute()
            $campaign->x_campaign_id = $campaign->xid;
            return ApiResponse::make('Campaigns found', [$campaign]);
        } else {
            return ApiResponse::make('No campaigns found', [], 404);
        }
    }

    public function getUseUContactCampaigns()
    {
        $campaigns = Campaign::select('name', 'uc_campaigns')
            ->whereNotNull('uc_campaigns')
            ->get();

        if ($campaigns->isNotEmpty()) {
            return ApiResponse::make(['campaigns' => $campaigns]);
        } else {
            return ApiResponse::make('No campaigns found', [], 404);
        }
    }

    public function getUsersCamps($xid)
    {
        if (ctype_digit($xid)) {
            $userId = (int) $xid;
            $campaignIds = CampaignUser::where('user_id', $userId)
                ->orderBy('campaign_id')
                ->pluck('campaign_id');
            return response()->json($campaignIds->toArray());
        }
        
        // \Log::info('takeLeadAction', ['idx' => $xid]);
        $campaignId = $this->getIdFromHash($xid);
        // \Log::info('takeLeadAction', ['id' => $campaignId]);
        $users = User::join('campaign_users as cu', 'cu.user_id', '=', 'users.id')
            ->where('cu.campaign_id', $campaignId)
            ->orderBy('users.name')
            ->select('users.id', 'users.name', 'users.user')
            ->get();

        $result = $users->map(function($u) {
            return [
                'xid'  => $u->xid,
                'id'   => $u->id,
                'name' => $u->name,
                'user' => $u->user,
            ];
        });

        return response()->json($result);
    }



}
