<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use OwenIt\Auditing\Contracts\Auditable;

class NotesTypification extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'notes_typifications';

    protected $default = ['xid', 'name', 'parent_id', 'x_parent_id'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id','parent_id'];

    protected $appends = ['xid','x_parent_id'];

    protected $filterable = ['id', 'name','parent_id'];

    protected $hashableGetterFunctions = [
        'getXParentIdAttribute' => 'parent_id',
    ];

    protected $casts = [
        'parent_id' => Hash::class . ':hash',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function subTypifications()
    {
        return $this->hasMany(NotesTypification::class, 'parent_id', 'id');
    }

    public function childs()
    {
        return $this->subcategories()->with('childs');
    }

}