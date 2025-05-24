<?php

namespace App\Http\Controllers\Api;

use App\Classes\Common;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\LeadAux\IndexRequest;
use App\Http\Requests\Api\LeadAux\StoreRequest;
use App\Http\Requests\Api\LeadAux\UpdateRequest;
use App\Http\Requests\Api\LeadAux\DeleteRequest;
use App\Models\LeadAux;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request; 

class LeadAuxController extends ApiBaseController
{
    protected $model = LeadAux::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function pushData(Request $request)
    {
        \Log::info('pushData RAW', ['request' => $request->all()]);

        // Obtener el array de leads desde la clave "data"
        $leadsArrayJson = $request->input('data');
        $fileName = $request->input('file'); // e.g. "TestBase.csv"

        // Verifica si está codificado como string JSON
        if (is_string($leadsArrayJson)) {
            $leadsArray = json_decode($leadsArrayJson, true);
        } else {
            $leadsArray = $leadsArrayJson;
        }

        if (!is_array($leadsArray)) {
            return response()->json(['error' => 'Formato inválido de datos'], 400);
        }

        // Extraer nombre base sin extensión
        $nombreBase = pathinfo($fileName, PATHINFO_FILENAME);

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
                'nombreBase'       => $nombreBase,
            ]);
        }


        DB::statement('CALL sincronizarInformacion()');

        return response()->json(['message' => 'Leads insertados correctamente']);
    }


}
