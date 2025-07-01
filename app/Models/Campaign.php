<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Classes\common;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class Campaign extends BaseModel  implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    use HasFactory;

    protected $table = 'campaigns';

    protected $default = ['xid', 'name','image_url'];

    protected $guarded = ['id', 'status', 'started_on', 'completed_on', 'completed_by', 'created_by', 'updated_by', 'last_action_by', 'created_at', 'updated_at'];

    protected $hidden = ['company_id','plantilla_calidad_id', 'form_id', 'email_template_id', 'created_by', 'updated_by', 'last_actioner', 'completed_by'];

    protected $appends = ['xid', 'x_company_id', 'image_url', 'x_plantilla_calidad_id','x_form_id', 'x_email_template_id', 'x_created_by', 'x_updated_by', 'x_last_action_by', 'x_completed_by', 'upcoming_lead_action', 'managed_data'];

    protected $filterable = ['name', 'active'];

    protected $hashableGetterFunctions = [
        'getXCompanyIdAttribute' => 'company_id',
        'getXPlantillaCalidadIdAttribute' => 'plantilla_calidad_id',
        'getXFormIdAttribute' => 'form_id',
        'getXEmailTemplateIdAttribute' => 'email_template_id',
        'getXCreatedByAttribute' => 'created_by',
        'getXUpdatedByAttribute' => 'updated_by',
        'getXLastActionByAttribute' => 'last_action_by',
        'getXCompletedByAttribute' => 'completed_by',
    ];

    protected $casts = [
        'company_id' => Hash::class . ':hash',
        'plantilla_calidad_id' => Hash::class . ':hash',
        'form_id' => Hash::class . ':hash',
        'email_template_id' => Hash::class . ':hash',
        'created_by' => Hash::class . ':hash',
        'updated_by' => Hash::class . ':hash',
        'last_action_by' => Hash::class . ':hash',
        'completed_by' => Hash::class . ':hash',
        'allow_reference_prefix' => 'integer',
        'detail_fields' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function getUpcomingLeadActionAttribute()
    {
        $data = $this->getManagedDataAttribute();

        $totalLeads = $data['total_leads'];
        $remainingLeads = $data['remaining_leads'];

        if ($remainingLeads == 0 && $totalLeads == 0) {
            return 'start_and_new_lead';
        } else if ($remainingLeads == 0) {
            return 'new_lead';
        } else if ($remainingLeads == $totalLeads) {
            return 'start';
        } else {
            return 'resume';
        }
    }

    public function getManagedDataAttribute()
    {
        $user = user();
        $userId = $user->id;

        $totalLeads = Lead::where('leads.assign_to', $userId)
            ->where('leads.campaign_id', $this->id)
            ->count();
        $startedLeads = Lead::where('leads.assign_to', $userId)
            ->where('leads.campaign_id', $this->id)
            ->where('leads.started', 1)
            ->count();
        $remainingLeads = Lead::where('leads.assign_to', $userId)
            ->where('leads.campaign_id', $this->id)
            ->where('leads.started', 0)
            ->count();
        $notHavingNotes = Lead::where('leads.assign_to', $userId)
            ->where('leads.campaign_id', $this->id)
            ->where('leads.notes_count', '<=', 0)
            ->count();

        $havingNotes = Lead::where('leads.assign_to', $userId)
        ->where('leads.campaign_id', $this->id)
        ->where('leads.notes_count', '>', 0)
        ->count();

        return [
            'total_leads' => $totalLeads,
            'remaining_leads' => $remainingLeads,
            'started_leads' => $startedLeads,
            'not_having_notes' => $notHavingNotes,
            'having_notes' => $havingNotes
        ];
    }

    public function campaignUsers()
    {
        return $this->hasMany(CampaignUser::class, 'campaign_id', 'id');
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function plantilla_calidad()
    {
        return $this->belongsTo(PlantillaCalidad::class);
    }

    public function emailTemplate()
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    public function lastActioner()
    {
        return $this->belongsTo(User::class, 'last_action_by', 'id');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by', 'id');
    }

    public function getImageUrlAttribute()
    {
        $campaignImagePath = Common::getFolderPath('campaignImagePath');

        return $this->image == null ? asset('images/product.png') : Common::getFileUrl($campaignImagePath, $this->image);
    }
    
}