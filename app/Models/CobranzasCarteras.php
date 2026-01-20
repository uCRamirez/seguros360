<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CobranzasCarteras extends BaseModel
{
    use HasFactory;

    protected $table = 'cobranzas_carteras';

    protected $default = ['xid'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = [];

    protected $appends = ['xid'];

    protected $filterable = ['xid'];

    protected $casts = [];

    /**
     * Una cartera pertenece a un cliente
     */
    public function cliente()
    {
        return $this->belongsTo(CobranzasClientes::class,'identificador_cliente','identificador');
    }
}
