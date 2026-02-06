<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use App\Casts\Hash;
use OwenIt\Auditing\Contracts\Auditable;

class CobranzasBasesPagos extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'cobranzas_bases_pagos';

    protected $default = ['xid', 'nombre_base'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = [];

    protected $appends = ['xid','x_user_id'];

    protected $filterable = ['nombre_base', 'user_id'];

    protected $hashableGetterFunctions = [
        'getXUserIdAttribute' => 'user_id',
    ];

    protected $casts = [
        'user_id' => Hash::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();
    }
}