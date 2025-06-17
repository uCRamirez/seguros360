<?php

namespace App\Http\Controllers\Api;

use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\EvaluacionCalidad\IndexRequest;
use App\Http\Requests\Api\EvaluacionCalidad\StoreRequest;
use App\Http\Requests\Api\EvaluacionCalidad\UpdateRequest;
use App\Http\Requests\Api\EvaluacionCalidad\DeleteRequest;
use App\Models\EvaluacionCalidad;
use App\Models\EstadoCalidadVenta;
use App\Models\Venta;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Examyou\RestAPI\Exceptions\ApiException;

class EvaluacionCalidadController extends ApiBaseController
{
    protected $model = EvaluacionCalidad::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function save(StoreRequest $request)
    {
        $user = user();         
        $data = $request->all();
        \Log::info('$data', $data);
        
        DB::beginTransaction();

        try {
            // 1. Guardar en evaluaciones_calidad
            $evaluacion = new EvaluacionCalidad();
            $evaluacion->idVenta = $data['idVenta'];
            $evaluacion->plantilla_id = $data['plantilla_id'];
            $evaluacion->variables = $data['variables'];
            // $evaluacion->fecha_calidad = $data['fecha_calidad'];
            $evaluacion->duracion = $data['duracion'];
            $evaluacion->minuto_precio = $data['minuto_precio'];
            $evaluacion->cierre_venta = $data['cierre_venta'];
            $evaluacion->cerrado_por = $data['cerrado_por'];

            $accion_calidad_id = $this->getIdFromHash($data['accion_calidad_id']);
            $evaluacion->accion_calidad_id = $accion_calidad_id;

            $evaluacion->oportunidades = $data['oportunidades'];
            $evaluacion->comentarios_oportunidades = $data['comentarios_oportunidades'];
            $evaluacion->comentarios = $data['comentarios'];
            $evaluacion->numero_poliza = $data['numero_poliza'];
            $evaluacion->creado_por = $user->id;
            $evaluacion->save();

            // 2. Guardar en estados_calidad_venta
            $estado = new EstadoCalidadVenta();
            $estado->idVenta = $data['idVenta'];
            $estado->evaluacion_id = $evaluacion->id;
            $estado->estado = $data['estado'];
            // $estado->fecha_estado = $data['fecha_estado'];
            $estado->nota_estado = $data['nota_estado'];
            $estado->numero_certificado = $data['numero_certificado'];

            $motivo_cancelacion_id = $this->getIdFromHash($data['motivo_cancelacion_id']);
            $estado->motivo_cancelacion_id = $motivo_cancelacion_id;

            $estado->cancelado_por_supervision = $data['cancelado_por_supervision'];
            $estado->reasignado_a = $data['reasignado_a'];
            $estado->save();

            Venta::where('idVenta', $data['idVenta'])->update([
                'calidad' => 1,
                'estadoVenta' => $data['estadoVenta'],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'evaluacion_id' => $evaluacion->id,
                'estado_id' => $estado->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ApiException('Error al guardar: ' . $e->getMessage());
        }
    }


    public function getEvaluacionEstado($xid)
    {
        $idVenta = trim($xid, '{}');
        // \Log::info('idVenta', ['idVenta' => $idVenta]);

        $evaluacion = EvaluacionCalidad::where('idVenta', $idVenta)->first();
        // \Log::info('evaluacion', ['evaluacion' => $evaluacion]);

        $estado = EstadoCalidadVenta::where('idVenta', $idVenta)->first();
        // \Log::info('estado', ['estado' => $estado]);

        return response()->json([
            'success' => true,
            'evaluacion_calidad' => $evaluacion,
            'estado_calidad_venta' => $estado,
        ]);
    }

    
    public function deleteCalidad($xid)
    {
        $idVenta = trim($xid, '{}');

        DB::beginTransaction();

        try {
            $venta = Venta::where('idVenta', $idVenta)->first();

            if (!$venta) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ], 404);
            }

            $venta->calidad = 0;
            $venta->save();

            $evaluacion = EvaluacionCalidad::where('idVenta', $idVenta)->first();
            $estado = EstadoCalidadVenta::where('idVenta', $idVenta)->first();

            if ($evaluacion) {
                $evaluacion->delete();
            }
            if ($estado) {
                $estado->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ApiException('Error al eliminar: ' . $e->getMessage());
        }
    }


  
}
