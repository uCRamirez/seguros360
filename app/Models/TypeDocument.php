<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TypeDocument extends BaseModel 
{
    use HasFactory;

    protected $table = 'type_documents';

    protected $default = ['xid', 'name'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    // protected $hidden = [];

    protected $appends = ['xid'];

    protected $filterable = ['name'];

    // protected $casts = [];

}
