<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class PlantillaCalidad extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable, HasFactory;

    protected $table = 'plantillas_calidad';

    protected $default = ['xid', 'nombre','descripcion','activo'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = [];

    protected $appends = ['xid'];

    protected $filterable = [
        'nombre',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function variables()
    {
        return $this->hasMany(VariableCalidad::class, 'plantilla_id', 'id');
    }

    public function evaluaciones()
    {
        return $this->hasMany(EvaluacionCalidad::class, 'plantilla_id', 'id');
    }
}
