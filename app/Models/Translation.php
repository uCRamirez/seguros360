<?php

namespace App\Models;

use App\Casts\Hash;
use OwenIt\Auditing\Contracts\Auditable;

class Translation extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable;
	
	protected  $table = 'translations';

	protected $default = ['xid'];

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $filterable = ['name'];

	protected $hidden = ['lang_id'];

	protected $appends = ['xid', 'x_lang_id'];

	protected $hashableGetterFunctions = [
		'getXLangIdAttribute' => 'lang_id'
	];

	protected $casts = [
		'lang_id' => Hash::class . ':hash'
	];
}
