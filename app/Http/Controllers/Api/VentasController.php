<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Arr;   
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Venta\IndexRequest;
use App\Http\Requests\Api\Venta\StoreRequest;
use App\Http\Requests\Api\Venta\UpdateRequest;
use App\Http\Requests\Api\Venta\DeleteRequest;
use App\Models\Venta;
use App\Models\ProductosVenta;
use App\Models\LeadLog;
use App\Models\Lead;
use Carbon\Carbon;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;

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

        // \Log::info('DATA LLEGO', ['data' => $data]);
        if ($data['accion'] === 'add') {

            DB::beginTransaction();
            try {
                $ventaData = Arr::except($data, ['productos', 'accion']);
                $venta = Venta::create($ventaData);

                $productos = json_decode($data['productos'], true);

                foreach ($productos as $item) {
                    ProductosVenta::create([
                        'idVenta'           => $venta->idVenta,
                        'idProducto'        => $this->getIdFromHash($item['xid']) ?: null,
                        'cantidadProducto'  => $item['product_quantity'],
                        'precio'            => $item['price'],
                        'precio_digitado'   => $item['precio_digitado'] ?? 0,
                    ]);
                }

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                // \Log::error('Error al guardar venta y productos:', ['error' => $e->getMessage()]);
                throw new ApiException('Sale not saved');
            }

            return response()->json([
                'success' => true,
                'data'    => $venta,
            ], 201);
        }

        return $this->updateVenta($data);
    }


    protected function updateVenta(array $data)
    {
        DB::beginTransaction();
        try {
            $idNota = $this->getIdFromHash($data['idNota']);
            $venta  = Venta::where('idNota', $idNota)->firstOrFail();

            $venta->update(Arr::except($data, ['productos', 'accion', 'idNota']));

            if (! empty($data['productos'])) {
                ProductosVenta::where('idVenta', $venta->idVenta)->delete();

                $productos = json_decode($data['productos'], true);
                foreach ($productos as $item) {
                    ProductosVenta::create([
                        'idVenta'           => $venta->idVenta,
                        'idProducto'        => $this->getIdFromHash($item['xid']) ?: null,
                        'cantidadProducto'  => $item['product_quantity'],
                        'precio'            => $item['price'],
                        'precio_digitado'   => $item['precio_digitado'] ?? 0,
                    ]);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            // \Log::error('Error al actualizar venta y productos:', ['error' => $e->getMessage()]);
            throw new ApiException('Sale not updated');
        }

        return response()->json([
            'success' => true,
            'data'    => $venta->load('productos'),
        ], 200);
    }

}
