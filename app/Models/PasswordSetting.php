<?php

namespace App\Models;

use App\Classes\Common;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use Vinkla\Hashids\Facades\Hashids;
use OwenIt\Auditing\Contracts\Auditable;

class PasswordSetting extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'password_settings';

    protected $default = ['xid'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id'];

    protected $appends = ['xid'];

    protected $filterable = [];

    protected $casts = [
       'lower_case' => 'integer',
       'upper_case' => 'integer',
       'special_character' => 'integer',
       'number' => 'integer',
       'enable_audit_log' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

}
