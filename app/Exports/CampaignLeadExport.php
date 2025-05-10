<?php

namespace App\Exports;

use App\Classes\Common;
use App\Models\Campaign;
use App\Models\Lead;
use Maatwebsite\Excel\Concerns\FromArray;

class CampaignLeadExport implements FromArray
{

    private $campaign;

    public function __construct($campaignId)
    {
        $this->campaign = Campaign::with('form')->where('id', $campaignId)->first();
    }

    public function array(): array
    {
        $headerFields = ['Reference No', 'Campaign Name', 'Lead Status'];
        $otherHeaderFieldsKeys = [];
        $allExcelData = [];

        if ($this->campaign->form_id && $this->campaign->form && $this->campaign->form->form_fields) {
            foreach ($this->campaign->form->form_fields as $allFormFields) {
                $headerFields[] = $allFormFields['name'];
                $otherHeaderFieldsKeys[] = Common::getFieldKeyByName($allFormFields['name']);
            }
        }

        $allExcelData[] = $headerFields;

        $allLeads = Lead::select('reference_number', 'lead_status_id', 'lead_data_json')
            ->with(['leadStatus'])
            ->where('campaign_id', $this->campaign->id)
            ->get();

        foreach ($allLeads as $allLead) {
            $leadStatus = $allLead->leadStatus && $allLead->leadStatus->name ? $allLead->leadStatus->name : '';
            $dataResult = [$allLead->reference_number, $this->campaign->name, $leadStatus];

            foreach ($otherHeaderFieldsKeys as $otherHeaderFieldsKey) {
                $dataResult[] = isset($allLead->lead_data_json[$otherHeaderFieldsKey]) ? $allLead->lead_data_json[$otherHeaderFieldsKey] : '';
            }

            $allExcelData[] = $dataResult;
        }


        return $allExcelData;
    }
}
