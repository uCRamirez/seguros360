<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class EstadoCalidadVenta extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable, HasFactory;

    protected $table = 'estados_calidad_venta';

    protected $default = ['xid', 'estado', 'fecha_estado'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = [
        'evaluacion_id', 'motivo_cancelacion_id', 'reasignado_a',
    ];

    protected $appends = ['xid', 'xEvaluacionId', 'xMotivoCancelacionId', 'xReasignadoA'];

    protected $filterable = [
        'estado',
        'fecha_estado',
        'nota_estado',
    ];

    protected $hashableGetterFunctions = [
        'getXVentaIdAttribute'         => 'idVenta',
        'getXEvaluacionIdAttribute'     => 'evaluacion_id',
        'getXMotivoCancelacionIdAttribute' => 'motivo_cancelacion_id',
        'getXReasignadoAAttribute'      => 'reasignado_a',
    ];

    protected $casts = [
        'idVenta'           => Hash::class . ':hash',
        'evaluacion_id'         => Hash::class . ':hash',
        'motivo_cancelacion_id' => Hash::class . ':hash',
        'reasignado_a'          => Hash::class . ':hash',
        'fecha_estado'          => 'datetime',
        'nota_estado'           => 'integer', 
        'cancelado_por_supervision' => 'boolean',
    ];

    public function evaluacion()
    {
        return $this->belongsTo(EvaluacionCalidad::class, 'evaluacion_id', 'id');
    }

    public function motivoCancelacion()
    {
        return $this->belongsTo(MotivoCancelacion::class, 'motivo_cancelacion_id', 'id');
    }

    public function reasignadoA()
    {
        return $this->belongsTo(User::class, 'reasignado_a', 'id');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'idVenta', 'id');
    }
}
