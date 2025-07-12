<?php

namespace App\Classes;

use App\Models\Campaign;
use App\Models\Currency;
use App\Models\Lang;
use App\Models\Lead;
use App\Models\LeadLog;
use App\Models\Settings;
use App\Models\StaffMember;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Http;

class Common
{
    public static function getFolderPath($type = null)
    {
        $paths = [
            'companyLogoPath' => 'companies',
            'userImagePath' => 'users',
            'langImagePath' => 'langs',
            'expenseBillPath' => 'expenses',
            'productImagePath' => 'products',
            'campaignImagePath' => 'campaigns',
            'audioFilesPath' => 'audio',
            'websiteImagePath' => 'website',
            'offlineRequestDocumentPath' => 'offline-requests',
            'notesFilePath' => 'lead-notes'
        ];

        return ($type == null) ? $paths : $paths[$type];
    }

    public static function uploadFile($request)
    {
        $folder = $request->folder;

        $folderString = "";

        if ($folder == "user") {
            $folderString = "userImagePath";
        } else if ($folder == "company") {
            $folderString = "companyLogoPath";
        } else if ($folder == "langs") {
            $folderString = "langImagePath";
        } else if ($folder == "expenses") {
            $folderString = "expenseBillPath";
        } else if ($folder == "product") {
            $folderString = "productImagePath";
        } else if ($folder == "website") {
            $folderString = "websiteImagePath";
        } else if ($folder == "offline-requests") {
            $folderString = "offlineRequestDocumentPath";
        } else if ($folder == "leadNotes") {
            $folderString = "notesFilePath";
        }else if ($folder == "campaign") {
            $folderString = "campaignImagePath";
        }

        $folderPath = self::getFolderPath($folderString);
        
        if ($request->hasFile('image') || $request->hasFile('file')) {
            $largeLogo  = $request->hasFile('image') ? $request->file('image') : $request->file('file');

            $fileName   = $folder . '_' . strtolower(Str::random(20)) . '.' . $largeLogo->getClientOriginalExtension();
            $largeLogo->storePubliclyAs($folderPath, $fileName);
        }

        return [
            'file' => $fileName,
            'file_url' => self::getFileUrl($folderPath, $fileName),
        ];
    }

    public static function checkFileExists($folderString, $fileName)
    {
        $folderPath = self::getFolderPath($folderString);

        $fullPath = $folderPath . '/' . $fileName;

        return Storage::exists($fullPath);
    }

    public static function getFileUrl($folderPath, $fileName)
    {
        if (config('filesystems.default') == 's3') {
            $path = $folderPath . '/' . $fileName;

            return Storage::url($path);
        } else {
            $path =  'uploads/' . $folderPath . '/' . $fileName;

            return asset($path);
        }
    }

    public static function moduleInformations()
    {
        $allModules = Module::all();
        $allEnabledModules = Module::allEnabled();
        $installedModules = [];
        $enabledModules = [];

        foreach ($allModules as $key => $allModule) {
            $modulePath = $allModule->getPath();
            $versionFileName = app_type() == 'saas' ? 'superadmin_version.txt' : 'version.txt';
            $version = File::get($modulePath . '/' . $versionFileName);

            $installedModules[] = [
                'verified_name' => $key,
                'current_version' => preg_replace("/\r|\n/", "", $version)
            ];
        }

        foreach ($allEnabledModules as $allEnabledModuleKey => $allEnabledModule) {
            $enabledModules[] = $allEnabledModuleKey;
        }

        return [
            'installed_modules' => $installedModules,
            'enabled_modules' => $enabledModules,
        ];
    }

    public static function getIdFromHash($hash)
    {
        if ($hash != "") {
            $convertedId = Hashids::decode($hash);
            $id = $convertedId[0];

            return $id;
        }

        return $hash;
    }

    public static function getHashFromId($id)
    {
        $id = Hashids::encode($id);

        return $id;
    }

    public static function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    public static function calculateTotalUsers($companyId, $update = false)
    {
        $totalUsers =  StaffMember::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $companyId)
            ->count('id');

        if ($update) {
            DB::table('companies')
                ->where('id', $companyId)
                ->update([
                    'total_users' => $totalUsers
                ]);
        }


        return $totalUsers;
    }

    public static function addWebsiteImageUrl($settingData, $keyName)
    {
        if ($settingData && array_key_exists($keyName, $settingData)) {
            if ($settingData[$keyName] != '') {
                $imagePath = self::getFolderPath('websiteImagePath');

                $settingData[$keyName . '_url'] = Common::getFileUrl($imagePath, $settingData[$keyName]);
            } else {
                $settingData[$keyName] = null;
                $settingData[$keyName . '_url'] = asset('images/website.png');
            }
        }

        return $settingData;
    }

    public static function convertToCollection($data)
    {
        $data = collect($data)->map(function ($item) {
            return (object) $item;
        });

        return $data;
    }

    public static function addCurrencies($company)
    {
        $newCurrency = new Currency();
        $newCurrency->company_id = $company->id;
        $newCurrency->name = 'Dollar';
        $newCurrency->code = 'USD';
        $newCurrency->symbol = '$';
        $newCurrency->position = 'front';
        $newCurrency->is_deletable = false;
        $newCurrency->save();

        $rupeeCurrency = new Currency();
        $rupeeCurrency->company_id = $company->id;
        $rupeeCurrency->name = 'Rupee';
        $rupeeCurrency->code = 'INR';
        $rupeeCurrency->symbol = '₹';
        $rupeeCurrency->position = 'front';
        $rupeeCurrency->is_deletable = false;
        $rupeeCurrency->save();

        $enLang = Lang::where('key', 'en')->first();

        $company->currency_id = $newCurrency->id;
        $company->lang_id = $enLang->id;
        $company->save();

        return $company;
    }

    public static function checkSubscriptionModuleVisibility($itemType)
    {
        $visible = true;

        if (app_type() == 'saas') {
            if ($itemType == 'user') {
                $userCounts = StaffMember::count();
                $company = company();

                $visible = $company && $company->subscriptionPlan && $userCounts < $company->subscriptionPlan->max_users ? true : false;
            }
        }

        return $visible;
    }

    public static function allVisibleSubscriptionModules()
    {
        $visibleSubscriptionModules = [];

        if (self::checkSubscriptionModuleVisibility('user')) {
            $visibleSubscriptionModules[] = 'user';
        }

        return $visibleSubscriptionModules;
    }

    public static function insertInitSettings($company)
    {
        if ((app_type() == 'saas' && $company->is_global == 1) || (app_type() == 'non-saas' && $company->is_global == 0)) {
            $local = new Settings();
            $local->company_id = $company->id;
            $local->setting_type = 'storage';
            $local->name = 'Local';
            $local->name_key = 'local';
            $local->status = true;
            $local->is_global = $company->is_global;
            $local->save();

            $aws = new Settings();
            $aws->company_id = $company->id;
            $aws->setting_type = 'storage';
            $aws->name = 'AWS';
            $aws->name_key = 'aws';
            $aws->credentials = [
                'driver' => 's3',
                'key' => '',
                'secret' => '',
                'region' => '',
                'bucket' => '',

            ];
            $aws->is_global = $company->is_global;
            $aws->save();

            $smtp = new Settings();
            $smtp->company_id = $company->id;
            $smtp->setting_type = 'email';
            $smtp->name = 'SMTP';
            $smtp->name_key = 'smtp';
            $smtp->credentials = [
                'from_name' => '',
                'from_email' => '',
                'host' => '',
                'port' => '',
                'encryption' => '',
                'username' => '',
                'password' => '',

            ];
            $smtp->is_global = $company->is_global;
            $smtp->save();
        }

        if ($company->is_global == 0) {
            $sendMailSettings = new Settings();
            $sendMailSettings->company_id = $company->id;
            $sendMailSettings->setting_type = 'send_mail_settings';
            $sendMailSettings->name = 'Send mail to company';
            $sendMailSettings->name_key = 'company';
            $sendMailSettings->credentials = [];
            $sendMailSettings->save();

            // Create Menu Setting
            $setting = new Settings();
            $setting->company_id = $company->id;
            $setting->setting_type = 'shortcut_menus';
            $setting->name = 'Add Menu';
            $setting->name_key = 'shortcut_menus';
            $setting->credentials = [
                'user',
                'currency',
                'language',
                'role',
            ];
            $setting->status = 1;
            $setting->save();

            // Seed for quotations
            NotificationSeed::seedAllModulesNotifications($company->id);
        }
    }

    public static function recalculateCampaignLeads($campaignId)
    {
        $totalLeads = Lead::where('campaign_id', $campaignId)->count();
        $notStartedLeads = Lead::where('campaign_id', $campaignId)
            ->where('leads.started', '=', 0)
            ->count();

        $campaign = Campaign::find($campaignId);
        $campaign->total_leads = $totalLeads;
        $campaign->remaining_leads = $notStartedLeads;
        $campaign->save();
    }

    public static function saveLeadData(array $data)
    {
        $user = user();

        // \Log::info('saveLead', ['data' => $data]);

        // 1) IDs hasheados
        $callLogId = isset($data['call_log_id'])
            ? Common::getIdFromHash($data['call_log_id'])
            : null;
        $leadId = isset($data['x_lead_id'])
            ? Common::getIdFromHash($data['x_lead_id'])
            : (isset($data['xid'])
                ? Common::getIdFromHash($data['xid'])
                : null);

        // 2) Cargar modelos
        $lead = Lead::findOrFail($leadId);
        $log  = $callLogId
            ? LeadLog::where('id', $callLogId)
                    ->where('user_id', $user->id)
                    ->first()
            : null;

        // 3) Actualizar registro de llamada
        if ($log && isset($data['call_time'])) {
            $log->time_taken = (int)$data['call_time'] - (int)$log->started_on;
            $log->save();
        }

        // 4) Recalcular tiempo total
        $totalTime = LeadLog::where('lead_id', $lead->id)
                            ->where('log_type', 'call_log')
                            ->sum('time_taken');

        // 5) Mapear campos personales
        $lead->cedula          = $data['cedula']              ?? $lead->cedula;
        $lead->nombre          = $data['nombre']              ?? $lead->nombre;
        $lead->segundo_nombre  = $data['segundo_nombre']      ?? $lead->segundo_nombre;
        $lead->apellido1       = $data['apellido1']           ?? $lead->apellido1;
        $lead->apellido2       = $data['apellido2']           ?? $lead->apellido2;
        $lead->estadoCivil_id  = $data['estado_civil']['id']  ?? $lead->estadoCivil_id;
        $lead->tipo_plan       = $data['tipo_plan']           ?? $lead->tipo_plan;
        $lead->fechaVencimiento= $data['fechaVencimiento']    ?? $lead->fechaVencimiento;
        $lead->tipo_tarjeta    = $data['tipo_tarjeta']        ?? $lead->tipo_tarjeta;
        $lead->emisor          = $data['emisor']              ?? $lead->emisor;
        $lead->ultimos_digitos = $data['ultimos_digitos']     ?? $lead->ultimos_digitos;
        $lead->mes_carga       = $data['mes_carga']           ?? $lead->mes_carga;
        $lead->anno_carga      = $data['anno_carga']          ?? $lead->anno_carga;
        $lead->foco_venta      = $data['foco_venta']          ?? $lead->foco_venta;
        $lead->hijos           = $data['hijos']               ?? $lead->hijos;
        $lead->fechaNacimiento = $data['fechaNacimiento']     ?? $lead->fechaNacimiento;
        $lead->edad            = $data['edad']                ?? $lead->edad;
        $lead->genero          = $data['genero']              ?? $lead->genero;
        $lead->nacionalidad    = $data['nacionalidad']        ?? $lead->nacionalidad;
        $lead->tel1            = $data['tel1']                ?? $lead->tel1;
        $lead->tel2            = $data['tel2']                ?? $lead->tel2;
        $lead->tel3            = $data['tel3']                ?? $lead->tel3;
        $lead->tel4            = $data['tel4']                ?? $lead->tel4;
        $lead->tel5            = $data['tel5']                ?? $lead->tel5;
        $lead->tel6            = $data['tel6']                ?? $lead->tel6;
        $lead->email           = $data['email']               ?? $lead->email;
        $lead->provincia_voto  = $data['provincia_voto']      ?? $lead->provincia_voto;
        $lead->provincia       = $data['provincia']           ?? $lead->provincia;
        $lead->canton          = $data['canton']              ?? $lead->canton;
        $lead->distrito        = $data['distrito']            ?? $lead->distrito;
        $lead->nombreBase      = $data['nombreBase']          ?? $lead->nombreBase;
        $lead->tarjeta         = $data['tarjeta']             ?? $lead->tarjeta;

        // 6) Campos adicionales y relaciones
        if (isset($data['lead_data'])) {
            $lead->lead_data = $data['lead_data'];
        }
        if (isset($data['reference_number'])) {
            $lead->reference_number = $data['reference_number'];
        }
        if (isset($data['lead_status'])) {
            $lead->lead_status_id = $data['lead_status']['id'];
            //\Log::info('lead_status', ['data' => $data['lead_status']]);
        }

        if (isset($data['assign_to']['id'])) {
            $lead->assign_to = $data['assign_to']['id'];
        }

        // 7) Campos de sistema
        $lead->last_action_by = $user->id;
        $lead->time_taken     = $totalTime;

        // 8) Guardar Lead
        //\Log::info('saveLead', ['data' => $data]);

        $lead->save();

        // 9) (Opcional) Generar JSON extra
        //self::generateAndSaveLeadData($lead->id);

        // 10) Actualizar campaña
        if ($campaign = Campaign::find($lead->campaign_id)) {
            $campaign->last_action_by = $user->id;
            $campaign->save();
        }

        return $lead;
    }



    public static function takeLeadAction($actionType, $leadId, $campaignId)
    {
        $user = user();
        $lead = null;

        if ($actionType == 'next') {
            // Check if next lead exists or not
            $lead = Lead::where('id', '>', $leadId)
                ->where('assign_to', $user->id)
                ->where('campaign_id', $campaignId)
                ->orderBy('id', 'asc')
                ->first();

            if (!$lead) {
                // Getting next not started lead
                $lead = Lead::where('id', '>', $leadId)
                    ->where('campaign_id', $campaignId)
                    ->where('started', 0)
                    ->orderBy('id', 'asc')
                    ->first();

                if ($lead) {
                    // It is new Lead so saving first actioner
                    $lead->assign_to = $user->id;
                    $lead->first_action_by = $user->id;
                    $lead->last_action_by = $user->id;
                    $lead->started = 1;
                    $lead->save();

                    // Calculating Lead Counts
                    Common::recalculateCampaignLeads($campaignId);
                }
            }
        } else if ($actionType == 'previous') {
            $lead = Lead::where('id', '<', $leadId)
                ->where('assign_to', $user->id)
                ->where('campaign_id', $campaignId)
                ->orderBy('id', 'desc')
                ->first();
        }

        return $lead;
    }

    public static function updateBookingStatus($bookingType, $leadId, $bookingStatus)
    {
        $lead = null;

        if ($bookingType == 'lead_follow_up' || $bookingType == 'salesman_bookings') {
            $lead = Lead::find($leadId);
            $logId = $bookingType == 'lead_follow_up' ? $lead->lead_follow_up_id : $lead->salesman_booking_id;
            if ($bookingType == 'lead_follow_up') {
                $lead->lead_follow_up_id = null;
            } else {
                $lead->salesman_booking_id = null;
            }
            $lead->save();

            if (!is_null($logId)) {
                $leadLog = LeadLog::find($logId);
                $leadLog->booking_status = $bookingStatus;
                $leadLog->save();
            }
        }


        return $lead;
    }

    public static function getFieldKeyByName($filedName)
    {
        return str_replace(' ', '_', strtolower($filedName));
    }

    public static function generateAndSaveLeadData($leadId)
    {
        $lead = Lead::withoutGlobalScope(CompanyScope::class)->find($leadId);

        $newLeadDataJson = [];
        foreach ($lead->lead_data as $allLeadDataObject) {
            $convertedKey = self::getFieldKeyByName($allLeadDataObject['field_name']);

            $newLeadDataJson[$convertedKey] = $allLeadDataObject['field_value'];
        }

        $lead->lead_data_json = $newLeadDataJson;
        $lead->save();
    }

    public static function sendMailOrMessage($subdomain, $authToken, $formData, $type = 'email')
    {

        $url = $type == 'email'
            ? 'https://' . $subdomain . '.ucontactcloud.com/IntegraChannels/resources/Forms/SendMailv2'
            : 'https://' . $subdomain . '.ucontactcloud.com/Integra/resources/SMS/SendSMSv2';

        // Your basic auth key (replace with your actual key)
        // $authKey = base64_encode('your-username:your-password');
        $authKey = $authToken;

        // Send the POST request with headers and form data
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $authKey
        ])->asForm()->post($url, $formData);

        return $response;
    }
}
