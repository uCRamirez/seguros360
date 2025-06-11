<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class VariableCalidad extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable, HasFactory;

    protected $table = 'variables_calidad';

    protected $default = ['xid', 'nombre'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['plantilla_id'];

    protected $appends = ['xid', 'xPlantillaId'];

    protected $filterable = [
        'nombre',
        'tipo',
        'nota_maxima',
    ];

    protected $hashableGetterFunctions = [
        'getXPlantillaIdAttribute' => 'plantilla_id',
    ];

    protected $casts = [
        'plantilla_id' => Hash::class . ':hash',
        'nota_maxima'  => 'integer',
    ];

    public function plantilla()
    {
        return $this->belongsTo(PlantillaCalidad::class, 'plantilla_id', 'id');
    }
}
