<?php

namespace App\Models;

use Trebol\Entrust\Contracts\EntrustPermissionInterface;
use Trebol\Entrust\Traits\EntrustPermissionTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Permission extends BaseModel implements EntrustPermissionInterface, Auditable
{
    use \OwenIt\Auditing\Auditable;
	
	use EntrustPermissionTrait;

	protected $table = 'permissions';

	protected $default = ['xid'];

	protected $guarded = ['id', 'created_at', 'updated_at'];

	protected $hidden = ['id', 'pivot'];

	protected $appends = ['xid'];
}
