<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Trebol\Entrust\Contracts\EntrustRoleInterface;
use Trebol\Entrust\Traits\EntrustRoleTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Role extends BaseModel implements EntrustRoleInterface, Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    use EntrustRoleTrait;

    protected  $table = 'roles';

    protected $default = ['xid', 'id', 'name', 'display_name'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = [];

    protected $appends = ['xid'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }
}
