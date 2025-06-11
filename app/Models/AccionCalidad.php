<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class AccionCalidad extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable, HasFactory;

    protected $table = 'acciones_calidad';

    protected $default = ['xid', 'nombre'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = [];

    protected $appends = ['xid'];

    protected $filterable = [
        'nombre',
    ];

    public function evaluaciones()
    {
        return $this->hasMany(EvaluacionCalidad::class, 'accion_calidad_id', 'id');
    }
}
