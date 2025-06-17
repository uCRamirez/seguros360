<?php

namespace App\Http\Controllers\Api;

use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\VariableCalidad\IndexRequest;
use App\Http\Requests\Api\VariableCalidad\StoreRequest;
use App\Http\Requests\Api\VariableCalidad\UpdateRequest;
use App\Http\Requests\Api\VariableCalidad\DeleteRequest;
use App\Models\VariableCalidad;
use App\Models\PlantillaCalidad;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;


class VariableCalidadController extends ApiBaseController
{
    protected $model = VariableCalidad::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    /**
     * Guarda en bloque (crea o actualiza según 'accion').
     * Espera payload con:
     *   variables: [ { plantilla_id, tipo, nombre, descripcion, nota_maxima, [id] } ]
     *   accion: 'add' | 'edit'
     */
    public function save(StoreRequest $request)
    {
        $items   = $request['variables'] ?? [];
        $items_eliminar   = $request['variables_eliminar'] ?? [];

        $accion  = $request['accion']    ?? 'add';

        $results = [];

        if ($accion === 'add') {
            foreach ($items as $item) {
                // crea nuevo
                $results[] = VariableCalidad::create([
                    'plantilla_id' => $item['plantilla_id'],
                    'tipo'         => $item['tipo'],
                    'nombre'       => $item['nombre'],
                    'descripcion'  => $item['descripcion'],
                    'nota_maxima'  => $item['nota_maxima'],
                ]);
            }
            return response()->json([
                'success' => true,
                'data'    => $results,
            ], 201);
        }else{
            return $this->updateVariables($items, $items_eliminar);
        }


        
    }

    /**
     * Actualiza un solo registro.  
     * Espera payload con { id, plantilla_id, tipo, nombre, descripcion, nota_maxima }.
     */
    protected function updateVariables(array $items, array $items_eliminar)
    {
        $results = [];

        DB::transaction(function() use ($items, $items_eliminar, &$results) {
            // Crear/actualizar
            foreach ($items as $item) {
                $fields = Arr::only($item, [
                    'plantilla_id', 'tipo', 'nombre', 'descripcion', 'nota_maxima'
                ]);

                if (!empty($item['id'])) {
                    $variable = VariableCalidad::findOrFail($item['id']);
                    $variable->update($fields);
                    $results[] = $variable->fresh();
                } else {
                    $results[] = VariableCalidad::create($fields);
                }
            }

            if (!empty($items_eliminar)) {
                foreach ($items_eliminar as $id) {
                    VariableCalidad::destroy($id);
                }
            }
        });

        return response()->json([
            'success' => true,
            'data'    => $results,
        ], 200);
    }
  
}
