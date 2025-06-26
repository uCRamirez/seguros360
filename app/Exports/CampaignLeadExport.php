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
        $headerFields = ['Cedula','Nombre','Primer Apellido', 'Segundo Apellido','Tel1','Tel2','Tel3','Tel4','Tel5','Tel6','Email', 'Campaign', 'Lead Status'];
        $allExcelData = [];

        $allExcelData[] = $headerFields;

        $allLeads = Lead::select('cedula','nombre','apellido1','apellido2','tel1','tel2','tel3','tel4','tel5','tel6','email', 'lead_status_id', 'lead_data_json')
            ->with(['leadStatus'])
            ->where('campaign_id', $this->campaign->id)
            ->get();

        foreach ($allLeads as $allLead) {
            $leadStatus = $allLead->leadStatus && $allLead->leadStatus->name ? $allLead->leadStatus->name : '';
            $dataResult = [$allLead->cedula,$allLead->nombre,$allLead->apellido1,$allLead->apellido2,$allLead->tel1,$allLead->tel2,$allLead->tel3,$allLead->tel4,$allLead->tel5,$allLead->tel6,$allLead->email, $this->campaign->name, $leadStatus];



            $allExcelData[] = $dataResult;
        }


        return $allExcelData;
    }
}
