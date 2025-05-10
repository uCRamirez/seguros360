<?php

namespace App\Models;
use App\Casts\Hash;

use App\Models\BaseModel;

class UphoneCalls extends BaseModel
{

    protected $table = 'uphone_calls';

    protected $default = ['xid', 'campaign', 'date', 'direction', 'duration', 'guid', 'discriptions', 'numbers', 'response_data'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['lead_id','campaign_id'];

    protected $appends = ['xid', 'x_lead_id','x_campaign_id'];

    protected $filterable = ['campaign', 'client_id', 'direction', 'duration', 'guid', 'discriptions', 'number', 'response_data'];

    protected $hashableGetterFunctions = [
		'getXLeadIdAttribute' => 'lead_id',
        'getXCampaignIdAttribute' => 'campaign_id'

	];

    protected $casts = [
		'response_data' => 'json'
	];

    public function campaigns()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }

    public function leadData()
    {
        return $this->belongsTo(Lead::class, 'lead_id', 'id');
    }

    // public function firstActioner()
    // {
    //     return $this->belongsTo(User::class, 'first_action_by', 'id');
    // }

    // public function lastActioner()
    // {
    //     return $this->belongsTo(User::class, 'last_action_by', 'id');
    // }

}