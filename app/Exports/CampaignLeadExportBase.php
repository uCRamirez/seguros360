<?php

namespace App\Exports;

use App\Classes\Common;
use App\Models\Campaign;
use App\Models\Lead;
use Maatwebsite\Excel\Concerns\FromArray;

class CampaignLeadExportBase implements FromArray
{

    private $campaign;
    private $base;
    private $state;

    public function __construct($campaignId,$base,$state)
    {
        $this->campaign = Campaign::with('form')->where('id', $campaignId)->first();
        $this->base = $base;
        $this->state = $state;
    }

    public function array(): array
    {
        $headerFields = ['Nombre base','Cedula','Nombre','Segundo Nombre','Primer Apellido','Segundo Apellido','Tipo Plan', 'Fecha Vencimiento', 'Tipo Tarjeta','Emisor','Ultimos Digitos','Mes Carga','Año Carga','Foco Venta','Hijos','Fecha Nacimiento','Edad','Genero','Nacionalidad','Tel1','Tel2','Tel3','Tel4','Tel5','Tel6','Email','Provincia Voto','Campaign','Lead Status'];
        $allExcelData = [];

        $allExcelData[] = $headerFields;

        // trabajados
        $allLeads = Lead::select('nombreBase','cedula','nombre','segundo_nombre','apellido1','apellido2','tipo_plan', 'fechaVencimiento','tipo_tarjeta','emisor','ultimos_digitos','mes_carga','anno_carga','foco_venta','hijos','fechaNacimiento','edad','genero','nacionalidad','tel1','tel2','tel3','tel4','tel5','tel6','email','provincia_voto','lead_status_id')
            ->with(['leadStatus'])
            ->where('campaign_id', $this->campaign->id)
            ->where('nombreBase', $this->base)
            ->where('started',$this->state)
            ->get();

        // \Log::info('array', ['allLeads' => $allLeads]);


        foreach ($allLeads as $allLead) {
            $leadStatus = $allLead->leadStatus && $allLead->leadStatus->name ? $allLead->leadStatus->name : '';
            $hijos = $allLead->hijos === 1 ? 'si' : 'no' ;
            $dataResult = [$allLead->nombreBase,$allLead->cedula,$allLead->nombre,$allLead->segundo_nombre,$allLead->apellido1,$allLead->apellido2,$allLead->tipo_plan,$allLead->fechaVencimiento,$allLead->tipo_tarjeta,$allLead->emisor,$allLead->ultimos_digitos,$allLead->mes_carga,$allLead->anno_carga,$allLead->foco_venta,$hijos,$allLead->fechaNacimiento,$allLead->edad,$allLead->genero,$allLead->nacionalidad,$allLead->tel1,$allLead->tel2,$allLead->tel3,$allLead->tel4,$allLead->tel5,$allLead->tel6,$allLead->email,$allLead->provincia_voto,$this->campaign->name,$leadStatus];

            $allExcelData[] = $dataResult;
        }


        return $allExcelData;
    }
}
