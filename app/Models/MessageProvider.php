<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class MessageProvider extends BaseModel  implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    use HasFactory;

    protected $table = 'message_providers';

    protected $default = ['xid', 'name', 'source', 'auth_token', 'campaign', 'subdomain'];

    protected $guarded = ['id', 'last_action_by', 'created_at', 'updated_at'];

    protected $hidden = ['company_id'];

    protected $appends = ['xid', 'x_company_id'];

    protected $filterable = ['id', 'name', 'source', 'auth_token', 'campaign'];

    protected $hashableGetterFunctions = [
        'getXCompanyIdAttribute' => 'company_id',
    ];

    protected $casts = [
        'company_id' => Hash::class . ':hash',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }
}