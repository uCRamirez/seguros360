<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;

class CampaignUser extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'campaign_users';

    protected $default = ['xid'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'campaign_id', 'user_id'];

    protected $appends = ['xid', 'x_campaign_id', 'x_user_id'];

    protected $filterable = ['name'];

    protected $hashableGetterFunctions = [
        'getXCampaignIdAttribute' => 'campaign_id',
        'getXUserIdAttribute' => 'user_id',
    ];

    protected $casts = [
        'campaign_id' => Hash::class . ':hash',
        'user_id' => Hash::class . ':hash',
    ];

    protected static function boot()
    {
        parent::boot();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
