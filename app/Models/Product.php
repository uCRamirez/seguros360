<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Classes\common;
use App\Scopes\CompanyScope;
use OwenIt\Auditing\Contracts\Auditable;

class Product extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'products';

    protected $default = ['xid', 'name','coverage','description','nombreBase', 'product_type', 'image_url', 'price', 'tax_rate', 'tax_label','status'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['category_id'];

    protected $appends = ['xid', 'image_url','x_category_id','x_campaign_id'];

    protected $filterable = ['name','coverage', 'internal_code','category_id','campaign_id','nombreBase','status'];

    protected $hashableGetterFunctions = [
        'getXCategoryIdAttribute' => 'category_id',
        'getXCampaignIdAttribute' => 'campaign_id',
    ];

    protected $casts = [
        'category_id' => Hash::class . ':hash',
        'campaign_id' => Hash::class . ':hash',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function categories()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function campaigns()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }

    public function getImageUrlAttribute()
    {
        $productImagePath = Common::getFolderPath('productImagePath');

        return $this->image == null ? asset('images/product.png') : Common::getFileUrl($productImagePath, $this->image);
    }
}
