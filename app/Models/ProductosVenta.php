<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use OwenIt\Auditing\Contracts\Auditable;

class ProductosVenta extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    /**
     * La tabla asociada.
     *
     * @var string
     */
    protected $table = 'productos_ventas';

    /**
     * La clave primaria.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * IDs auto-incrementantes.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Desactivar timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Atributos por defecto al serializar.
     *
     * @var array
     */
    protected $default = [
        'id',
        'idVenta',
        'idProducto',
        'cantidadProducto',
        'precio',
        'x_id_venta',
        'x_id_producto',
    ];

    /**
     * Atributos no asignables masivamente.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Atributos añadidos al serializar.
     *
     * @var array
     */
    protected $appends = ['x_id_venta', 'x_id_producto'];

    /**
     * Campos filtrables.
     *
     * @var array
     */
    protected $filterable = ['id', 'idVenta', 'idProducto'];

    /**
     * Funciones getter para hashing.
     *
     * @var array
     */
    protected $hashableGetterFunctions = [
        'getXIdVentaAttribute'   => 'idVenta',
        'getXIdProductoAttribute'=> 'idProducto',
    ];

    /**
     * Casts.
     *
     * @var array
     */
    protected $casts = [
        'idVenta'   => Hash::class . ':hash',
        'idProducto'=> Hash::class . ':hash',
    ];

    /**
     * Boot del modelo.
     */
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CompanyScope);
    }

    /**
     * Relación con Venta.
     */
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'idVenta', 'idVenta')->withoutGlobalScopes();
    }

    /**
     * Relación con Product.
     */
    public function producto()
    {
        return $this->belongsTo(Product::class, 'idProducto', 'id')->withoutGlobalScopes();
    }

}
