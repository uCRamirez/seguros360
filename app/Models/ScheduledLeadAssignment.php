<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class ScheduledLeadAssignment extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable, HasFactory;

    protected $table = 'scheduled_lead_assignments';

    protected $default = ['xid', 'campaign_id', 'agent_id', 'lead_ids', 'scheduled_at'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = [];

    protected $appends = ['xid'];

    protected $filterable = [
        'campaign_id',
        'agent_id',
        'scheduled_at',
    ];

    protected $casts = [
        'lead_ids' => 'array',
        'scheduled_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}
