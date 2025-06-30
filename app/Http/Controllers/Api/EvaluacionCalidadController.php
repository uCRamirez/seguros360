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
use App\Models\AccionCalidad;
use App\Models\Venta;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Arr;   
use Illuminate\Support\Facades\Http;
use App\Notifications\SendLeadMail;
use Illuminate\Support\Facades\Notification;
use App\Models\PlantillaCalidad;
use App\Models\MotivoCancelacion;


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
        // \Log::info('$data', $data);

        if ($data['accion'] === 'add') {
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
                $evaluacion->cerrado_por = $this->getIdFromHash($data['cerrado_por']);

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

                $aux = AccionCalidad::where('id',$accion_calidad_id)->first();

                if ($aux->tipo === 'accion') {
                    $users = is_string($aux->users_ids)
                        ? json_decode($aux->users_ids, true)
                        : $aux->users_ids;
                    $this->envioMailCalidad($evaluacion, $estado, $data, $users);
                }


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

        return $this->updateEvaluacionCalidad($data);

    }

    public function updateEvaluacionCalidad(array $data)
    {
        // \Log::info('updateEvaluacionCalidad', $data);
        DB::beginTransaction();

        try {
            $evaluacion = EvaluacionCalidad::where('idVenta', $data['idVenta'])->first();
            if (! $evaluacion) {
                throw new ApiException('Evaluación no encontrada');
            }

            $evaluacion->plantilla_id                 = $data['plantilla_id'];
            $evaluacion->variables                    = $data['variables'];
            $evaluacion->duracion                     = $data['duracion'];
            $evaluacion->minuto_precio                = $data['minuto_precio'];
            $evaluacion->cierre_venta                 = $data['cierre_venta'];
            $evaluacion->cerrado_por                  = $this->getIdFromHash($data['cerrado_por']);
            $evaluacion->accion_calidad_id            = $this->getIdFromHash($data['accion_calidad_id']);
            $evaluacion->oportunidades                = $data['oportunidades'];
            $evaluacion->comentarios_oportunidades    = $data['comentarios_oportunidades'];
            $evaluacion->comentarios                  = $data['comentarios'];
            $evaluacion->numero_poliza                = $data['numero_poliza'];
            $evaluacion->save();

            $estado = EstadoCalidadVenta::where('idVenta', $data['idVenta'])->first();
            if (!$estado) {
                $estado = new EstadoCalidadVenta();
                $estado->idVenta = $data['idVenta'];
            }
            $estado->evaluacion_id           = $evaluacion->id;
            $estado->estado                  = $data['estado'];
            $estado->nota_estado             = $data['nota_estado'];
            $estado->numero_certificado      = $data['numero_certificado'];
            $estado->motivo_cancelacion_id   = $this->getIdFromHash($data['motivo_cancelacion_id']);
            $estado->cancelado_por_supervision = $data['cancelado_por_supervision'];
            $estado->reasignado_a             = $data['reasignado_a'];
            $estado->save();

            if($data['estado'] === 'REASIGNADA' && $data['reasignado_a']){
                Venta::where('idVenta', $data['idVenta'])->update([
                    'calidad'    => 1,
                    'estadoVenta'=> $data['estadoVenta'],
                    'user_id' => $data['reasignado_a'],
                ]);
            }else{
                Venta::where('idVenta', $data['idVenta'])->update([
                    'calidad'    => 1,
                    'estadoVenta'=> $data['estadoVenta'],
                ]);
            }

            DB::commit();

            $aux = AccionCalidad::find($evaluacion->accion_calidad_id);
            if ($aux->tipo === 'accion') {
                $users = is_string($aux->users_ids)
                    ? json_decode($aux->users_ids, true)
                    : $aux->users_ids;
                $this->envioMailCalidad($evaluacion, $estado, $data, $users);
            }

            return response()->json([
                'success'       => true,
                'evaluacion_id' => $evaluacion->id,
                'estado_id'     => $estado->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ApiException('Error al actualizar: ' . $e->getMessage());
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

   protected function generarTemplatecalidad(EvaluacionCalidad $evaluacion, EstadoCalidadVenta $estado, array $data)
    {
        $plantilla = PlantillaCalidad::find($evaluacion->plantilla_id);
        $accion    = AccionCalidad::find($evaluacion->accion_calidad_id);

        $summary  = "# Resumen de Evaluación de Calidad ". ($data['accion'] === 'edit' ? 'Actualizada ' : '') . "ID #{$evaluacion->id}\n\n";
        $summary .= "- **Venta ID:** {$data['idVenta']}\n";
        $summary .= "- **Plantilla:** {$plantilla->nombre}\n";
        $summary .= "\n## Variables evaluadas\n";
        // Cabecera
        $summary .= "| Nombre   | Tipo       | Descripción                   |     Peso    | Marcada |\n";
        $summary .= "| -------- | ---------- | ----------------------------- | ----------- | ------- |\n";
        // Filas
        foreach ($data['variables'] as $var) {
            $summary .= sprintf(
                "| %s | %s | %s | %s | %s |\n",
                $var['nombre'],
                $var['tipo'] == 'critica'? 'Crítica':'No crítica',
                $var['descripcion'],
                $var['tipo'] == 'critica'? 'N/A' :$var['nota_maxima'],
                $var['marcada'] ? 'Sí' : 'No'
            );
        }
        $summary .= "\n";

        $summary .= "- **NOTA:** {$data['nota_estado']}\n\n";

        $summary .= "- **Duración de la llamada:** {$data['duracion']} minutos\n";
        $summary .= "- **Mención precio en minuto:** {$data['minuto_precio']}\n";
        $summary .= "- **Cierre de venta:** " . ($data['cierre_venta'] ? 'Sí' : 'No') . "\n";
        $summary .= "- **Estado de la venta:** {$data['estadoVenta']}\n";
        $summary .= "- **Estado de llamada:** " . str_replace('_', ' ', $estado->estado) . "\n\n";

        // Información adicional según estado
        switch ($estado->estado) {
            case 'CERTIFICADA':
                $summary .= "- **Número de póliza:** {$data['numero_poliza']}\n\n";
                break;
            case 'RELLAMADA_CERTIFICADA':
                $summary .= "- **Número de certificado:** {$data['numero_certificado']}\n\n";
                break;
            case 'CANCELADA_CALIDAD':
                $motivo = MotivoCancelacion::find($estado->motivo_cancelacion_id);
                $summary .= "- **Motivo cancelación:** {$motivo->motivo}\n\n";
                break;
            case 'REASIGNADA':
                $us = User::find($estado->reasignado_a);
                $summary .= "- **Reasignada a:** {$us->name}\n\n";
                break;
        }

        // Oportunidades de mejora
        if (!empty($data['oportunidades'])) {

            $ops = AccionCalidad::whereIn('id', $data['oportunidades'])->get();
            $summary .= "## Oportunidades de mejora identificadas\n";

            foreach ($ops as $op) {
                $summary .= " - {$op->nombre}" . ($op->descripcion ? ": {$op->descripcion}" : '') . "\n";
            }

            if (!empty($data['comentarios_oportunidades'])) {
                $summary .= "\n## Comentarios sobre las opciones de mejora\n{$data['comentarios_oportunidades']}\n";
            }

            $summary .= "\n";
        }

        // Comentarios generales
        if (!empty($data['comentarios'])) {
            $summary .= "## Comentarios generales\n{$data['comentarios']}\n";
        }

        return $summary;
    }


    protected function envioMailCalidad($evaluacion, $estado, $data, array $users_ids)
    {
        // 1) Recuperar solo emails válidos
        $toList = User::whereIn('id', $users_ids)
                    ->pluck('email')
                    ->filter()      // quita null/strings vacíos
                    ->all();        // convierte a array simple

        // 2) Si no hay destinatarios, salir
        if (empty($toList)) {
            \Log::warning('envioMailCalidad: acción sin destinatarios válidos.');
            return;
        }

        // ya tienes $evaluacion y $estado cargados
        $summary = $this->generarTemplatecalidad($evaluacion, $estado, $data);


        // 3) Disparar la notificación vía mail, usando tu Notification
        Notification::route('mail', $toList)
            ->notify(new SendLeadMail(
                'Detalle de calidad realizada',
                $summary
            ));
    }
  
}
