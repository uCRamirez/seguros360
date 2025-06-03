<?php

namespace App\Http\Controllers\Api;

use App\Classes\Common;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\BaseHistorico\IndexRequest;
use App\Http\Requests\Api\BaseHistorico\StoreRequest;
use App\Http\Requests\Api\BaseHistorico\UpdateRequest;
use App\Http\Requests\Api\BaseHistorico\DeleteRequest;
use App\Models\BaseHistorico;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BaseHistoricoController extends ApiBaseController
{
    protected $model = BaseHistorico::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function getBases($xid)
    {
        $campaignId = $this->getIdFromHash($xid);
        // Log::info('getBases – XID', ['id' => $xid]);
        // Log::info('getBases – XID limpio', ['id' => $campaignId]);
        if (! $campaignId) {
            return response()->json(['error' => 'ID inválido'], 400);
        }

        try {
            $bases = BaseHistorico::with(['campaign', 'user'])
                    ->where('campaign_id', $campaignId)
                    ->get();

            return response()->json($bases);
        } catch (\Throwable $e) {
            \Log::error("Error en getBases: {$e->getMessage()}");
            return response()->json([
                'error'  => 'Error interno al obtener bases',
                'detail' => $e->getMessage()
            ], 500);
        }
    }
}
