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
use App\Models\Campaign;
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
use App\Services\AzureSmtpToken;
use App\Services\GraphMailService;
use Illuminate\Support\Facades\Config;
use Illuminate\Mail\Markdown;
use App\Models\Product;
use Carbon\Carbon;


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
            $evaluacion = EvaluacionCalidad::where('id', $data['idVenta'])->first();
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

            $estado = EstadoCalidadVenta::where('evaluacion_id',$evaluacion->id)->first();
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
                Venta::where('idVenta', $evaluacion->idVenta)->update([
                    'calidad'    => 1,
                    'estadoVenta'=> $data['estadoVenta'],
                    'user_id' => $data['reasignado_a'],
                ]);
            }else{
                Venta::where('idVenta', $evaluacion->idVenta)->update([
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
        $contenido  = trim($xid, '{}');
        $comentario = null;
        $isNota     = false;

        if (strpos($contenido, ',') !== false) {
            // Es nota
            list($idNota, $comentario) = explode(',', $contenido, 2);
            $isNota = true;

            $idVenta = Venta::where('idNota', $idNota)->value('idVenta');

            $evaluacion = EvaluacionCalidad::where('idVenta', $idVenta)
                            ->latest()
                            ->first();

            $estado = EstadoCalidadVenta::where('idVenta', $idVenta)
                            ->latest()
                            ->first();

            if ($evaluacion) {
                $evaluacion->load('accionCalidad', 'creador');
            }

        } else {
            // No es nota, tomar todos
            $idVenta = $contenido;

            $evaluacion = EvaluacionCalidad::where('idVenta', $idVenta)
                            ->with(['accionCalidad', 'creador'])
                            ->get();

            $estado = EstadoCalidadVenta::where('idVenta', $idVenta)->get();
        }

        return response()->json([
            'success'               => true,
            'evaluacion_calidad'    => $evaluacion,
            'estado_calidad_venta'  => $estado,
        ]);
    }


    
    public function deleteCalidad($xid)
    {
        $id = trim($xid, '{}');
        DB::beginTransaction();

        try {
            $evaluacion = EvaluacionCalidad::find($id);
            if (!$evaluacion) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Evaluación no encontrada'
                ], 404);
            }
            $idVenta = $evaluacion->idVenta;
            $evaluacion->delete();

            $total = EvaluacionCalidad::where('idVenta', $idVenta)->count();

            if ($total === 0) {
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
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Evaluación eliminada correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ApiException('Error al eliminar: ' . $e->getMessage());
        }
    }


    protected function generarTemplatecalidad(EvaluacionCalidad $evaluacion, EstadoCalidadVenta $estado, array $data)
    {
        // \Log::info('EvaluacionCalidad', [$evaluacion]);
        // \Log::info('EstadoCalidadVenta', [$estado]);
        // \Log::info('data', [$data]);

        $campaign = Campaign::find($data['campaign_id'] ?? null);
        $user = User::find($data['venta']['user_id'] ?? null);
        $headerLeft = [
            'Proyecto'          => $campaign->name ?? '—',
            'Colaborador (TO)'  =>  $user->name   ?? '—',
            'Fecha Venta'       => isset($data['fecha_venta'])
                                    ? Carbon::parse($data['fecha_venta'])->format('n/j/Y')
                                    : (optional($evaluacion->created_at)?->format('n/j/Y') ?? '—'),
            'Nota obtenida'     => $data['nota_estado'] ?? ($estado->nota_estado ?? 0),
        ];

        $motivo = MotivoCancelacion::find($estado->motivo_cancelacion_id);

        // Encabezado derecho (como en la imagen)
        $headerRight = [
            'Motivo'                     => $motivo->motivo              ?? '—',
            'Nombre cliente'             => ($data['lead']['nombre'] ?? '') . ' ' . ($data['lead']['apellido1'] ?? '') . ' ' . ($data['lead']['apellido2'] ?? '') ,
            'ID'                         => $data['lead']['cedula']          ?? ($data['lead']['id'] ?? '—'),
            'Monto venta'           => $data['venta']['montoTotal'] ?? '—',
            'Seguro colocado' => !empty($data['venta']['productos'])
                                    ? collect($data['venta']['productos'])->map(function ($p) {
                                        $producto = Product::find($p['idProducto']); // busca el producto en BD
                                        return "{$producto->name} - {$producto->coverage}";
                                    })->implode("\n")
                                    : '—',
            'Tiene cierre de Supervisor' => isset($data['cierre_venta'])
                                                ? ($data['cierre_venta'] ? 'SI' : 'NO')
                                                : '—',
            'Teléfono'                   => $data['venta']['telVenta'] ?? '—',
        ];

        $incumplimientos = [];
        if (!empty($evaluacion->variables)) {
            // Normalizar $vars a arreglo de arreglos
            $vars = $evaluacion->variables;
            // Si viene como string JSON -> decodificar
            if (is_string($vars)) {
                $decoded = json_decode($vars, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $vars = $decoded;
                }
            }
            // Si viene como objeto stdClass -> a arreglo
            if (is_object($vars)) {
                $vars = json_decode(json_encode($vars), true);
            }
            // Asegurar que es un arreglo indexado
            if (!is_array($vars)) {
                $vars = [];
            }
            $incumplimientos = collect($vars)
                ->map(function ($v) {
                    $nombre = trim(
                        (string) data_get($v, 'nombre', '')
                        . ' - '
                        . (string) data_get($v, 'descripcion', '')
                    );
                    return [
                        'nombre'  => $nombre === '-' ? '' : $nombre,
                        'marcada' => (bool) data_get($v, 'marcada', false),
                    ];
                })
                ->values()
                ->all();
        }

        return [
            // títulos
            'titulo'            => 'DETALLE DE CALIDAD REALIZADA',
            // encabezados
            'header_left'       => $headerLeft,
            'header_right'      => $headerRight,
            // secciones grandes
            'incumplimientos'   => $incumplimientos,                 // solo se listan los true en la vista
            'comentarios'       => $data['comentarios'] ?? ($estado->comentarios ?? ''), // "Detalle de la escucha"
            'mejoras'           => $data['comentarios_oportunidades']     ?? '',     // "Cuál era la forma correcta"
        ];
    }


    protected function envioMailCalidad($evaluacion, $estado, $data, array $users_ids)
    {
        $user = user();
        $users_ids[] = $user->id;

        $toList = User::whereIn('id', $users_ids)
                    ->pluck('email')
                    ->filter()      
                    ->all();        

        if (empty($toList)) {
            \Log::warning('envioMailCalidad: acción sin destinatarios válidos.');
            return;
        }
        
        $summary = $this->generarTemplatecalidad($evaluacion, $estado, $data);

        $systemMail = env('AZURE_MAIL_USERNAME');

        $markdown = app(Markdown::class);
        $html = $markdown->render('mail.lead_mail', ['content' => $summary])->toHtml();

        app(GraphMailService::class)->sendMail(
            $systemMail,
            $toList,
            'Detalle de calidad realizada',
            $html
        );

    }
  
}
