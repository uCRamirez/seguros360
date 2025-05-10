<?php

namespace App\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;

class Localidades extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'localidades'; // Nombre correcto de la tabla

    protected $guarded = ['Provincia', 'Canton', 'Distrito']; // No hay id, created_at ni updated_at

    protected $fillable = ['Provincia', 'Canton', 'Distrito'];

    protected $casts = [
        'Provincia' => 'string',
        'Canton' => 'string',
        'Distrito' => 'string',
    ];

    public $timestamps = false; // No hay columnas created_at ni updated_at
}
