<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class MotivoCancelacion extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable, HasFactory;

    protected $table = 'motivos_cancelacion';

    protected $default = ['xid', 'motivo'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = [];

    protected $appends = ['xid'];

    protected $filterable = [
        'motivo',
    ];

    public function estados()
    {
        return $this->hasMany(EstadoCalidadVenta::class, 'motivo_cancelacion_id', 'id');
    }
}
