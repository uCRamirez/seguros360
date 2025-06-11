<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class EvaluacionCalidad extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable, HasFactory;

    protected $table = 'evaluaciones_calidad';

    protected $default = ['xid', 'fecha_calidad', 'duracion', 'minuto_precio', 'cierre_venta'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = [
        'venta_id', 'plantilla_id', 'cerrado_por', 'accion_calidad_id', 'creado_por',
    ];

    protected $appends = [
        'xid', 'xVentaId', 'xPlantillaId', 'xCerradoPor', 'xAccionCalidadId', 'xCreadoPor',
    ];

    protected $filterable = [
        'fecha_calidad',
        'duracion',
        'cierre_venta',
        'numero_poliza',
    ];

    protected $hashableGetterFunctions = [
        'getXVentaIdAttribute'         => 'venta_id',
        'getXPlantillaIdAttribute'     => 'plantilla_id',
        'getXCerradoPorAttribute'      => 'cerrado_por',
        'getXAccionCalidadIdAttribute' => 'accion_calidad_id',
        'getXCreadoPorAttribute'       => 'creado_por',
    ];

    protected $casts = [
        'venta_id'           => Hash::class . ':hash',
        'plantilla_id'       => Hash::class . ':hash',
        'cerrado_por'        => Hash::class . ':hash',
        'accion_calidad_id'  => Hash::class . ':hash',
        'creado_por'         => Hash::class . ':hash',
        'fecha_calidad'      => 'datetime',
        'duracion'           => 'integer',
        'minuto_precio'      => 'integer',
        'cierre_venta'       => 'boolean',
        'oportunidades'      => 'array',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id', 'id');
    }

    public function plantilla()
    {
        return $this->belongsTo(PlantillaCalidad::class, 'plantilla_id', 'id');
    }

    public function cerradoPor()
    {
        return $this->belongsTo(User::class, 'cerrado_por', 'id');
    }

    public function accionCalidad()
    {
        return $this->belongsTo(AccionCalidad::class, 'accion_calidad_id', 'id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por', 'id');
    }

    public function estados()
    {
        return $this->hasMany(EstadoCalidadVenta::class, 'evaluacion_id', 'id');
    }
}
