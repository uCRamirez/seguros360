<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use OwenIt\Auditing\Contracts\Auditable;

class ExpenseCategory extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'expense_categories';

    protected $default = ['xid', 'name'];

    protected $guarded = ['created_at', 'updated_at'];

    protected $hidden = [];

    protected $appends = ['xid'];

    protected $filterable = ['name'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }
}
