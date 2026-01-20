<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CobranzasPagos extends BaseModel
{
    use HasFactory;

    protected $table = 'cobranzas_pagos';

    protected $default = ['xid'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = [];

    protected $appends = ['xid'];

    protected $filterable = ['xid'];

    protected $casts = [];

    /**
     * Un pago pertenece a una cartera
     */
    public function cartera()
    {
        return $this->belongsTo(CobranzasCarteras::class,'identificador_cartera','identificador');
    }
}
