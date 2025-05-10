<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use OwenIt\Auditing\Contracts\Auditable;

class MessageTemplate extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'message_templates';

    protected $default = ['xid', 'name', 'message', 'code'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['message_provider_id'];

    protected $appends = ['xid', 'x_company_id', 'x_message_provider_id'];

    protected $filterable = ['name'];

    protected $hashableGetterFunctions = [
        'getXCompanyIdAttribute' => 'company_id',
        'getXMessageProviderIdAttribute' => 'message_provider_id',
    ];

    protected $casts = [
        'company_id' => Hash::class . ':hash',
        'status' => 'integer',
        'sharable' => 'integer',
        'message_provider_id' => Hash::class . ':hash'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function messageProvider()
    {
        return $this->belongsTo(MessageProvider::class, 'message_provider_id', 'id');
    }
}