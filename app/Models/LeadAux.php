<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class LeadAux extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    use HasFactory;

    protected $table = 'leads_aux';

    protected $default = [];

    protected $guarded = [];

    protected $hidden = [];

    protected $appends = [];

    protected $filterable = [
        'campaign_id',
        'estadoCivil_id',
        'lead_status_id',
        'lead_status',
        'cedula',
        'nombre',
        'apellido1',
        'apellido2',
        'email',
        'tel1',
        'tel2',
        'tel3',
        'tel4',
        'tel5',
        'tel6',
        'provincia',
        'canton',
        'distrito',
        'hijos',
        'fechaNacimiento',
        'edad',
        'nacionalidad',
        'nombreBase',
        'tarjeta',
        'assign_to',
    ];
    

    protected $hashableGetterFunctions = [
    
    ];

    protected $casts = [
    
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

}