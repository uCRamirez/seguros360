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

        $campaign_id = $request->input('campaign_id');;

        $myId = $request->input('myId');

        $company_id = $this->getIdFromHash($request->input('company_id'));

        $etapa = $request->input('etapa');

        if (is_string($leadsArrayJson)) {
            $leadsArray = json_decode($leadsArrayJson, true);
        } else {
            $leadsArray = $leadsArrayJson;
        }

        if (!is_array($leadsArray)) {
            return response()->json(['error' => 'Invalid base format'], 400);
        }

        try {
            $nombreBase = pathinfo($fileName, PATHINFO_FILENAME);
            $cantidadRegistros  = count($leadsArray);
            foreach ($leadsArray as $leadData) {
                $rawNacimiento  = $leadData['fechaNacimiento']  ?? null;
                $rawVencimiento = $leadData['fechaVencimiento'] ?? null;
                $rawHijos       = $leadData['hijos']           ?? null;

                $fechaNacimiento = $rawNacimiento
                    ? Carbon::parse($rawNacimiento)->format('Y-m-d H:i:s')
                    : null;
                $fechaVencimiento = $rawVencimiento
                    ? Carbon::parse($rawVencimiento)->format('Y-m-d H:i:s')
                    : null;

                $edad = $fechaNacimiento
                    ? Carbon::parse($fechaNacimiento)->age
                    : null;

                $hijos = 0;
                if ($rawHijos !== null) {
                    $h = trim($rawHijos);
                    if (preg_match('/^s/iu', $h)) {
                        $hijos = 1;
                    } else {
                        $hijos = 0;
                    }
                }

                \App\Models\LeadAux::create([
                    'cedula'           => $leadData['cedula'] ?? null, # 1
                    'nombre'           => $leadData['nombre'] ?? null, # 2
                    'segundo_nombre'   => $leadData['segundo_nombre'] ?? null, # 3
                    'apellido1'        => $leadData['apellido1'] ?? null, # 4
                    'apellido2'        => $leadData['apellido2'] ?? null, # 5
                    'tel1'             => $leadData['tel1'] ?? null, # 6
                    'tel2'             => $leadData['tel2'] ?? null, # 7
                    'tel3'             => $leadData['tel3'] ?? null, # 8
                    'tel4'             => $leadData['tel4'] ?? null, # 9
                    'tel5'             => $leadData['tel5'] ?? null, # 10
                    'tel6'             => $leadData['tel6'] ?? null, # 11
                    'email'            => $leadData['email'] ?? null, # 12
                    'provincia_voto'   => $leadData['provincia_voto'] ?? null, # 13
                    'hijos'            => $hijos, # 14
                    'estadoCivil'      => $leadData['estadoCivil'] ?? null, # 15
                    'tipo_plan'        => $leadData['tipo_plan'] ?? null, # 16
                    'fechaVencimiento' => $fechaVencimiento, # 17
                    'tarjeta'          => $leadData['tarjeta'] ?? null, # 18
                    'tipo_tarjeta'     => $leadData['tipo_tarjeta'] ?? null, # 19
                    'emisor'           => $leadData['emisor'] ?? null, # 20
                    'ultimos_digitos'  => $leadData['ultimos_digitos'] ?? null, # 21
                    'mes_carga'        => $leadData['mes_carga'] ?? null, # 22
                    'anno_carga'       => $leadData['anno_carga'] ?? null, # 23
                    'foco_venta'       => $leadData['foco_venta'] ?? null, # 24
                    'fechaNacimiento'  => $fechaNacimiento, # 25
                    'edad'             => $edad, # 26 -- esta no cuenta en el excel
                    'genero'           => $leadData['genero'] ?? null, # 27
                    'nacionalidad'     => $leadData['nacionalidad'] ?? null, # 28
                    'agente'           => $leadData['agente'] ?? null, # 29
                    'nombreBase'       => $nombreBase,
                    'campaign_id'      => $campaign_id,
                    'company_id'       => $company_id,
                    'created_by'       => $myId, 
                    'started'          => 0,
                ]);
            }

            $sql = "
                CALL sincronizarInformacionBaseLeads(
                    {$campaign_id},
                    '" . addslashes($nombreBase) . "',
                    {$myId},
                    {$cantidadRegistros},
                    '" . addslashes($etapa) . "'
                )
            ";
            DB::statement($sql);

            return response()->json(['message' => 'Leads insertados correctamente']); 
        } catch (\Throwable $th) {
            DB::rollBack();
            DB::table('leads_aux')->truncate();

            return response()->json([
                'error'   => 'Error al procesar los leads.',
                'details' => $th->getMessage(),
            ], 500);
        }
        
    }

}
