<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\Campaign;
use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class BaseHistorico extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;


    protected $table = 'bases_historico';

    protected $appends = ['x_campaign_id','x_user_id'];

    protected $fillable = [
        'campaign_id',
        'nombreBase',
        'user_id',
        'cantidadRegistros',
        'etapa',
        'estado',
    ];


    protected $hidden = [];

    protected $casts = [
        'campaign_id' => Hash::class . ':hash',
    ];


    protected $attributes = [
        'etapa' => 'nueva',
        'estado' => true, 
    ];

    protected $filterable = [
        'campaign_id',
        'nombreBase',
        'user_id',
        'cantidadRegistros',
        'etapa',
        'estado',
        'created_at',
    ];

    protected $hashableGetterFunctions = [
        'getXCampaignIdAttribute' => 'campaign_id',
        'getXUserIdAttribute' => 'user_id',
    ];

    /**
     * El método "booted" del modelo.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
    }

    // RELACIONES

    /**
     * Obtiene la campaña a la que pertenece la entrada del histórico.
     */
    public function campaign()
    {
        // Asegúrate de que App\Models\Campaign exista o ajusta el namespace.
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    /**
     * Obtiene el usuario asociado con la entrada del histórico.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}