<?php

namespace App\Imports;

use App\Classes\Common;
use App\Models\Campaign;
use App\Models\CampaignUser;
use App\Models\Lead;
use App\Models\StaffMember;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToArray;
use Illuminate\Support\Str;

class LeadImport implements ToArray, WithHeadingRow
{
    public $importLeadFields = "";
    public $allFormFields = "";
    public $campaignId = "";

    public function __construct($importLeadFields, $allFormFields, $campaignId)
    {
        $this->importLeadFields = $importLeadFields;
        $this->allFormFields = $allFormFields;
        $this->campaignId = $campaignId;
    }

    public function array(array $leads)
    {
        $campaign = Campaign::with(['campaignUsers'])->withoutGlobalScope(CompanyScope::class)->find($this->campaignId);
        $campaignUsers = CampaignUser::join('users', 'users.id', '=', 'campaign_users.user_id')
            ->where('campaign_users.campaign_id', $campaign->id)
            ->orderBy('users.name')
            ->pluck('user_id')
            ->toArray();

        if ($campaign->lead_distribution_method == 'random') {
            shuffle($campaignUsers);
        }

        DB::transaction(function () use ($leads, $campaign, $campaignUsers) {

            $agentKeyExists = $leads && $leads[0] && isset($leads[0]['agent']) ? true : false;
            $leadsNotAssignedAgent = count($leads);

            $assinedUserIndex = 0;
            $counter = 1;
            foreach ($leads as $lead) {
                // Calculating Lead Data Hash
                $leadHashString = "";
                $newLeadData = [];
                foreach ($this->allFormFields as $allFormField) {
                    $fieldName = $allFormField['name'];
                    $headerColumnName = array_search($fieldName, $this->importLeadFields);

                    if ($headerColumnName === false) {
                        $fieldValue = "";
                    } else {
                        $headerColumnSlug = Str::slug($headerColumnName, '_');
                        $fieldValue = $lead[$headerColumnSlug] ? $lead[$headerColumnSlug] : '';
                    }

                    $newLeadData[] = [
                        'id' => strtolower(Str::random(12)),
                        'field_name' => $fieldName,
                        'field_value' => $fieldValue,
                    ];

                    $leadHashString .= strtolower($fieldValue);
                }

                $leadHash = md5($leadHashString . $campaign->id);

                // Check if lead hash exists or not
                $leadHashCount = Lead::withoutGlobalScope(CompanyScope::class)
                    ->where('campaign_id', $campaign->id)
                    ->where('lead_hash', $leadHash)
                    ->count();

                // Agent/Assign To
                $agentName = $lead && isset($lead['agent']) ? $lead['agent'] : null;
                $agentId = null;
                if ($agentName != null) {
                    $agent = CampaignUser::join('users', 'users.id', '=', 'campaign_users.user_id')
                        ->join('campaigns', 'campaigns.id', 'campaign_users.campaign_id')
                        ->where('campaign_users.campaign_id', $campaign->id)
                        ->where('users.name', $agentName)
                        ->first();

                    if ($agent) {
                        $agentId = $agent->user_id;
                    }
                }

                if ($leadHashCount == 0) {

                    // Saving Lead
                    $lead = new Lead();
                    $lead->campaign_id = $campaign->id;

                    // Reference Prefix
                    if ($campaign->allow_reference_prefix) {
                        $timerCounter = Carbon::now()->timestamp + $counter;
                        $lead->reference_number = $campaign->reference_prefix . $timerCounter;
                    }



                    $lead->lead_data = $newLeadData;
                    $lead->created_by = user() ?  user()->id : null;
                    $lead->lead_hash = $leadHash;

                    if ($agentId == null) {
                        $agentId = $campaignUsers[$assinedUserIndex];

                        $assinedUserIndex = $assinedUserIndex < count($campaignUsers) - 1 ? $assinedUserIndex + 1 : 0;
                    }
                    $lead->assign_to = $agentId;
                    $lead->save();

                    $counter = $counter + 1;

                    // Saving Lead Data JSON
                    Common::generateAndSaveLeadData($lead->id);
                }
            }

            // Calculating Lead Counts
            Common::recalculateCampaignLeads($campaign->id);
        });
    }
}
