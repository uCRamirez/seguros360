<?php

namespace App\Models;

use App\Casts\Hash;
use App\Classes\Common;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use OwenIt\Auditing\Contracts\Auditable;

class LeadLog extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'lead_logs';

    protected $default = ['xid'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['company_id','campaign_id', 'lead_id', 'user_id', 'notes_typification_id_1', 'notes_typification_id_2', 'notes_typification_id_3','notes_typification_id_4'];

    protected $appends = ['xid', 'x_company_id','x_campaign_id', 'x_lead_id', 'x_user_id', 'x_created_by_id', 'notes_file_url', 'x_notes_typification_id_1', 'x_notes_typification_id_2', 'x_notes_typification_id_3','x_notes_typification_id_4'];

    protected $filterable = ['id', 'log_type', 'lead_id', 'campaign_id', 'user_id', 'isSale'];

    protected $dates = ['date_time'];

    protected $hashableGetterFunctions = [
        'getXCompanyIdAttribute' => 'company_id',
        'getXCampaignIdAttribute' => 'campaign_id',
        'getXLeadIdAttribute' => 'lead_id',
        'getXUserIdAttribute' => 'user_id',
        'getXCreatedByIdAttribute' => 'created_by_id',
        'getXNotesTypificationId1Attribute' => 'notes_typification_id_1',
        'getXNotesTypificationId2Attribute' => 'notes_typification_id_2',
        'getXNotesTypificationId3Attribute' => 'notes_typification_id_3',
        'getXNotesTypificationId4Attribute' => 'notes_typification_id_4',
    ];

    protected $casts = [
        'company_id' => Hash::class . ':hash',
        'campaign_id' => Hash::class . ':hash',
        'lead_id' => Hash::class . ':hash',
        'user_id' => Hash::class . ':hash',
        'notes_typification_id_1' => Hash::class . ':hash',
        'notes_typification_id_2' => Hash::class . ':hash',
        'notes_typification_id_3' => Hash::class . ':hash',
        'notes_typification_id_4' => Hash::class . ':hash',
        'created_by_id' => Hash::class . ':hash',
        'time_taken' => 'integer',
        'date_time' => 'datetime',
        // 'is_sale'   => 'boolean',
        // 'notes' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withoutGlobalScopes();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_id', 'id')->withoutGlobalScopes();
    }

    public function getNotesFileUrlAttribute()
    {
        $notesFilePath = Common::getFolderPath('notesFilePath');

        return $this->notes_file == null ? null : Common::getFileUrl($notesFilePath, $this->notes_file);
    }

    public function isSale()
    {
        return $this->hasOne(Venta::class, 'idNota', 'id')->withoutGlobalScopes();

    }


}
