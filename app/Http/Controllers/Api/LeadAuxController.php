<?php

namespace App\Http\Controllers\Api;

use App\Classes\Common;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\LeadAux\IndexRequest;
use App\Http\Requests\Api\LeadAux\StoreRequest;
use App\Http\Requests\Api\LeadAux\UpdateRequest;
use App\Http\Requests\Api\LeadAux\DeleteRequest;
use App\Models\LeadAux;
use App\Models\Company;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;

class LeadAuxController extends ApiBaseController
{
    protected $model = LeadAux::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function pushData(Request $request)
    {
        $leadsArrayJson = $request->input('data');
        // \Log::info('pushData', ['leadsArrayJson' => $leadsArrayJson]);

        $fileName = $request->input('file');
        // \Log::info('pushData', ['fileName' => $fileName]);

        $campaign_id = $request->input('campaign_id');
        // \Log::info('pushData', ['campaign_id' => $campaign_id]);

        $myId = $request->input('myId');
        // \Log::info('pushData', ['myId' => $myId]);

        $company_id = $this->getIdFromHash($request->input('company_id'));
        // \Log::info('pushData', ['company_id' => $company_id]);

        $etapa = $request->input('etapa');
        // \Log::info('pushData', ['etapa' => $etapa]);

        if (is_string($leadsArrayJson)) {
            $leadsArray = json_decode($leadsArrayJson, true);
        } else {
            $leadsArray = $leadsArrayJson;
        }

        if (!is_array($leadsArray)) {
            return response()->json(['error' => 'Invalid base format'], 400);
        }

        // Extraer nombre base sin extensión
        $nombreBase = pathinfo($fileName, PATHINFO_FILENAME);
        $cantidadRegistros  = count($leadsArray);
        foreach ($leadsArray as $leadData) {
            \App\Models\LeadAux::create([
                'cedula'           => $leadData['cedula'] ?? null,
                'nombre'           => $leadData['nombre'] ?? null,
                'apellido1'        => $leadData['apellido1'] ?? null,
                'apellido2'        => $leadData['apellido2'] ?? null,
                'tel1'             => $leadData['tel1'] ?? null,
                'tel2'             => $leadData['tel2'] ?? null,
                'email'            => $leadData['email'] ?? null,
                'edad'             => $leadData['edad'] ?? null,
                'fechaNacimiento'  => $leadData['fechaNacimiento'] ?? null,
                'agente'           => $leadData['agente'] ?? null,
                'nombreBase'       => $nombreBase,
                'campaign_id'      => $campaign_id,
                'company_id'       => $company_id,
                'created_by'       => $myId, 
            ]);
        }


        $sql = "
            CALL crm_seguros.sincronizarInformacionBaseLeads(
                {$campaign_id},
                '" . addslashes($nombreBase) . "',
                {$myId},
                {$cantidadRegistros},
                '" . addslashes($etapa) . "'
            )
        ";
        DB::statement($sql);


        return response()->json(['message' => 'Leads insertados correctamente']);
    }


}
