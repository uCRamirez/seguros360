<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Arr;   
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Venta\IndexRequest;
use App\Http\Requests\Api\Venta\StoreRequest;
use App\Http\Requests\Api\Venta\UpdateRequest;
use App\Http\Requests\Api\Venta\DeleteRequest;
use App\Models\Venta;
use App\Models\LeadLog;
use App\Models\Lead;
use Carbon\Carbon;
use Examyou\RestAPI\Exceptions\ApiException;


class VentasController extends ApiBaseController
{
    protected $model = Venta::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function save(StoreRequest $request)
    {
        $data = $request->validated();

        if ($data['accion'] === 'add') {
            //\Log::info('saveVenta - add', ['data' => $data]);

            try {
                $venta = Venta::create($data);
            } catch (\Throwable $e) {
                throw new ApiException('Sale not saved');
            }

            return response()->json([
                'success' => true,
                'data'    => $venta,
            ], 201);
        } else {
            return $this->updateVenta($data);
        }
    }


    protected function updateVenta(array $data)
    {
        // \Log::info('updateVenta - edit', ['data' => $data]);
        try {
            $idNota = $this->getIdFromHash($data['idNota']);
            $venta  = Venta::where('idNota', $idNota)->firstOrFail();
            $venta->update(Arr::except($data, ['accion', 'idNota']));
        } catch (\Throwable $e) {
            throw new ApiException('Sale not updated');
        }

        return response()->json([
            'success' => true,
            'data'    => $venta,
        ], 200);
    }
}
