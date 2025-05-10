<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Casts\Hash;
use App\Scopes\CompanyScope;
use OwenIt\Auditing\Models\Audit as AuditModel;

class Audit extends BaseModel implements \OwenIt\Auditing\Contracts\Audit
{
    use \OwenIt\Auditing\Audit;

    protected $table = 'audits';

    protected $default = ['xid','auditable_type','event','user_type','created_at'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['user_id','company_id'];

    protected $appends = ['xid','x_user_id', 'x_company_id'];

    protected $filterable = ['auditable_id','ip_address','event'];

    public static $auditingGloballyDisabled = false;

    protected $hashableGetterFunctions = [
        'getXCompanyIdAttribute' => 'company_id',
        'getXUserIdAttribute' => 'user_id',
    ];

    protected $casts = [
        'old_values'   => 'json',
        'new_values'   => 'json',
        'company_id' => Hash::class . ':hash',
        'user_id' => Hash::class . ':hash',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function getSerializedDate($date)
    {
        return $this->serializeDate($date);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
