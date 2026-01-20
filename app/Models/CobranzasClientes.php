<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CobranzasClientes extends BaseModel
{
    use HasFactory;

    protected $table = 'cobranzas_clientes';

    protected $default = ['xid'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = [];

    protected $appends = ['xid'];

    protected $filterable = ['xid'];

    protected $casts = [];

    /**
     * Un cliente puede tener muchas carteras
     */
    public function carteras()
    {
        return $this->hasMany(CobranzasCarteras::class, 'identificador_cliente', 'identificador');
    }
}
