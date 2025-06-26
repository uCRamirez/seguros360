<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use OwenIt\Auditing\Contracts\Auditable;

class Venta extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ventas';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'idVenta';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The data type of the primary key.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Disable timestamps if your table does not have created_at/updated_at.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Default attributes returned by the model.
     *
     * @var array
     */
    protected $default = [
        'idVenta',
        'idNota',
        'idLead',
        'user_id',
        'telVenta',
        'estadoVenta',
        'tarjeta',
        // 'idProducto',
        // 'cantidadProducto',
        'aplicaBeneficiarios',
        'cantidadBeneficiarios',
        'beneficiarios',
        'aplicaBeneficiariosAsist',
        'cantidadBeneficiariosAsist',
        'beneficiariosAsist',
        'montoTotal',
        'calidad',
        'xid',
        'x_id_nota',
        'x_id_producto',
        'x_user_id',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['idVenta'];

    /**
     * Hidden attributes for arrays/JSON.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Appended attributes for arrays/JSON.
     *
     * @var array
     */
    protected $appends = ['xid', 'x_id_nota','x_user_id'];//,'','x_id_producto'

    /**
     * Attributes available for filtering.
     *
     * @var array
     */
    protected $filterable = ['idVenta', 'idNota','calidad','estadoVenta','user_id'];//, 'idProducto'

    /**
     * Map accessor methods to fields for hashing.
     *
     * @var array
     */
    protected $hashableGetterFunctions = [
        'getXIdNotaAttribute' => 'idNota',
        // 'getXIdProductoAttribute' => 'idProducto',
        'getXUserIdAttribute' => 'user_id',

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'idNota' => Hash::class . ':hash',
        // 'idProducto' => Hash::class . ':hash',
        'user_id' => Hash::class . ':hash',
    ];

    /**
     * The model's boot method.
     */
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CompanyScope);
    }

    // public function producto() 
    // { 
    //     return $this->hasOne(Product::class, 'idProducto', 'id')->withoutGlobalScopes();

    // }

    public function user() 
    { 
        return $this->belongsTo(User::class, 'user_id', 'id')->withoutGlobalScopes();
    }

    public function productos()
    {
        return $this
            ->hasMany(ProductosVenta::class, 'idVenta', 'idVenta')
            ->withoutGlobalScopes();
    }


    // public function product() 
    // { 
    //     return $this->belongsTo(Product::class, 'idProducto', 'id')->withoutGlobalScopes();
    // }

}
