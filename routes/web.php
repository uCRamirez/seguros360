<?php

use Examyou\RestAPI\Facades\ApiRoute;
use Illuminate\Support\Facades\Route;

// Admin Routes
ApiRoute::group(['namespace' => 'App\Http\Controllers\Api'], function () {
    ApiRoute::get('all-langs', ['as' => 'api.extra.all-langs', 'uses' => 'AuthController@allEnabledLangs']);
    ApiRoute::get('lang-trans', ['as' => 'api.extra.lang-trans', 'uses' => 'AuthController@langTrans']);
    ApiRoute::post('change-theme-mode', ['as' => 'api.extra.change-theme-mode', 'uses' => 'AuthController@changeThemeMode']);
    ApiRoute::get('all-users', ['as' => 'api.extra.all-users', 'uses' => 'AuthController@allUsers']);

    // Check visibility of module according to subscription plan
    ApiRoute::post('check-subscription-module-visibility', ['as' => 'api.extra.check-subscription-module-visibility', 'uses' => 'AuthController@checkSubscriptionModuleVisibility']);
    ApiRoute::post('visible-subscription-modules', ['as' => 'api.extra.visible-subscription-modules', 'uses' => 'AuthController@visibleSubscriptionModules']);

    ApiRoute::group(['middleware' => ['api.auth.check']], function () {
        ApiRoute::post('dashboard', ['as' => 'api.extra.dashboard', 'uses' => 'AuthController@dashboard']);
        ApiRoute::post('upload-file', ['as' => 'api.extra.upload-file', 'uses' => 'AuthController@uploadFile']);
        ApiRoute::post('profile', ['as' => 'api.extra.profile', 'uses' => 'AuthController@profile']);
        ApiRoute::post('user', ['as' => 'api.extra.user', 'uses' => 'AuthController@user']);
        ApiRoute::get('timezones', ['as' => 'api.extra.user', 'uses' => 'AuthController@getAllTimezones']);

        // This route index page not require permissions
        ApiRoute::resource('lead-status', 'LeadStatusController', ['as' => 'api', 'only' => ['index']]);
    });

    // Routes Accessable to thouse user who have permissions realted to route
    ApiRoute::group(['middleware' => ['api.permission.check', 'api.auth.check', 'license-expire']], function () {
        $options = [
            'as' => 'api'
        ];

        // Imports
        ApiRoute::post('users/import', ['as' => 'api.users.import', 'uses' => 'UsersController@import']);
        ApiRoute::post('products/import', ['as' => 'api.products.import', 'uses' => 'ProductController@import']);


        // Update Status
        ApiRoute::post('email-templates/update-status', ['as' => 'api.email-templates.update-status', 'uses' => 'EmailTemplateController@updateStatus']);
        ApiRoute::post('forms/update-status', ['as' => 'api.forms.update-status', 'uses' => 'FormController@updateStatus']);
        ApiRoute::post('form-field-names/update-status', ['as' => 'api.form-field-name.update-status', 'uses' => 'FormFieldNameController@updateStatus']);

        // Lead Follow Up
        ApiRoute::post('lead-follow-ups/take-action', ['as' => 'api.lead-follow-ups.take-action', 'uses' => 'LeadFollowUpController@takeFollowUpAction']);
        ApiRoute::post('lead-follow-ups/delete', ['as' => 'api.lead-follow-ups.delete', 'uses' => 'LeadFollowUpController@delete']);
        ApiRoute::resource('lead-follow-ups', 'LeadFollowUpController', ['as' => 'api', 'only' => ['index']]);

        // Salesman Booking
        ApiRoute::post('salesman-bookings', ['as' => 'api.salesman-bookings.delete', 'uses' => 'SalesmanBookingController@delete']);
        ApiRoute::resource('salesman-bookings', 'SalesmanBookingController', ['as' => 'api', 'only' => ['index']]);

        // All Forms
        ApiRoute::get('forms/all', ['as' => 'api.forms.all', 'uses' => 'FormController@allForms']);
        ApiRoute::resource('forms', 'FormController', $options);
        ApiRoute::resource('audits', 'AuditController', $options);
        ApiRoute::get('audits-data', ['as' => 'api.audits-data', 'uses' => 'AuditController@auditData']);
        ApiRoute::post('audit/download', ['as' => 'api.audit.download.index', 'uses' => 'AuditController@auditDownload']);

        // Call Manager
        ApiRoute::resource('call-managers', 'CallManagerController', ['as' => 'api', 'only' => ['index']]);

        ApiRoute::post('leads/start-follow-up', ['as' => 'api.leads.start-follow-up', 'uses' => 'LeadController@startFollowUp']);

        ApiRoute::post('leads/uphone-campaigns', ['as' => 'api.leads.get-uphone-campaigns', 'uses' => 'LeadController@getUphoneCampaigns']);
        ApiRoute::post('leads/make-call-request', ['as' => 'api.leads.make-call-request', 'uses' => 'LeadController@makeCallRequest']);
        ApiRoute::post('leads/send-message', ['as' => 'api.leads.send-message', 'uses' => 'LeadController@sendMessage']);
        ApiRoute::post('leads/send-email', ['as' => 'api.leads.send-email', 'uses' => 'LeadController@sendEmail']);
        ApiRoute::get('leads/campaign-stats', ['as' => 'api.leads.campaign-stats', 'uses' => 'LeadController@leadCampaignStats']);
        ApiRoute::get('leads/campaign-members', ['as' => 'api.leads.bookings.lead-campaign-members', 'uses' => 'LeadController@leadCampaignMembers']);
        ApiRoute::post('leads/create-booking', ['as' => 'api.leads.bookings.create', 'uses' => 'LeadController@createBooking']);
        ApiRoute::get('leads/create-call-log/{id}', ['as' => 'api.leads.create-call-log', 'uses' => 'LeadController@createLeadCallLog']);
        ApiRoute::post('leads/create-lead', ['as' => 'api.leads.create-lead', 'uses' => 'LeadController@createLead']);
        ApiRoute::resource('leads', 'LeadController', ['as' => 'api', 'only' => ['index', 'show']]);

        ApiRoute::post('campaigns/recycle-campaign-leads', ['as' => 'api.move-leads', 'uses' => 'CampaignController@recycleCampaignLeads']);
        ApiRoute::post('campaigns/campaign-lists-except', ['as' => 'api.campaign-lists', 'uses' => 'CampaignController@getAllCampaignExcept']);
        ApiRoute::get('campaigns/email-templates', ['as' => 'api.campaigns.email-templates', 'uses' => 'CampaignController@campaignEmailTemplates']);
        ApiRoute::get('campaigns/user-campaigns', ['as' => 'api.campaigns.user-campaigns', 'uses' => 'CampaignController@userCampaigns']);
        ApiRoute::post('campaigns/skip-delete-lead', ['as' => 'api.campaigns.skip-delete-lead', 'uses' => 'CampaignController@skipAndDeleteLead']);
        ApiRoute::post('campaigns/export-leads/{id}', ['as' => 'api.campaigns.export-leads', 'uses' => 'CampaignController@campaignExportLead']);
        ApiRoute::post('campaigns/update-actioned-lead', ['as' => 'api.campaigns.update-actioned-lead', 'uses' => 'CampaignController@updateActionedLead']);
        ApiRoute::post('campaigns/take-lead-action', ['as' => 'api.campaigns.take-lead-action', 'uses' => 'CampaignController@takeLeadAction']);
        ApiRoute::post('campaigns/take-action', ['as' => 'api.campaigns.take-action', 'uses' => 'CampaignController@takeAction']);
        ApiRoute::post('campaigns/stop', ['as' => 'api.campaigns.stop', 'uses' => 'CampaignController@stopCampaign']);
        // usuarios por campaña
        ApiRoute::get('campaigns/{xid}/users', [
            'as'   => 'api.campaigns.users',
            'uses' => 'CampaignController@getUsersCamps'
        ]);

        ApiRoute::resource('campaigns', 'CampaignController', $options);
        //////////////////////////////////////////////////////////////////////////////////

        // Localidades
        ApiRoute::resource('localidades', 'LocalidadesController', ['as' => 'api', 'only' => ['index']]);
        // ventas
        ApiRoute::post('ventas/save', ['as' => 'api.ventas.save', 'uses' => 'VentasController@save']);
        ApiRoute::resource('ventas', 'VentasController', ['as' => 'api', 'only' => ['index']]);

        // columnas
        ApiRoute::get('columns/{table}', ['as' => 'api.leads.columns', 'uses' => 'ColumnController@allColumns']);
        // csv leads aux
        ApiRoute::post('leadcsv/push', ['as' => 'api.leadcsv.push', 'uses' => 'LeadAuxController@pushData']);
        // bases
        ApiRoute::get('bases/{xid}', ['as' => 'api.campaigns.bases', 'uses' => 'BaseHistoricoController@getBases']);
        


        ApiRoute::post('campaigns/uc-campaigns', ['as' => 'api.campaigns.uc-campaigns', 'uses' => 'CampaignController@getUContactCampaigns']);
        ApiRoute::post('campaigns/use-uc-campaigns', ['as' => 'api.campaigns.use-uc-campaigns', 'uses' => 'CampaignController@getUseUContactCampaigns']);

        //////////////////////////////////////////////////////////////////////////////////

        ApiRoute::get('email-templates/all', ['as' => 'api.email-templates.all', 'uses' => 'EmailTemplateController@allEmailTemplates']);
        ApiRoute::resource('email-templates', 'EmailTemplateController', $options);

        ApiRoute::get('message-templates/all', ['as' => 'api.message-templates.all', 'uses' => 'MessageTemplateController@allMessageTemplates']);
        ApiRoute::resource('message-templates', 'MessageTemplateController', $options);

        ApiRoute::get('form-field-names/all', ['as' => 'api.form-field-names.all', 'uses' => 'FormFieldNameController@allFormFieldNames']);
        ApiRoute::resource('form-field-names', 'FormFieldNameController', $options);

        ApiRoute::get('message-providers/all', ['as' => 'api.message-providers.all', 'uses' => 'MessageProviderController@allProviders']);
        ApiRoute::resource('message-providers', 'MessageProviderController', $options);

        ApiRoute::resource('lead-logs', 'LeadLogController', $options);

        ApiRoute::resource('uphones', 'UphoneCallsController', ['as' => 'api', 'only' => ['index']]);
        ApiRoute::resource('uphone-calls', 'UphoneCallsController',  ['as' => 'api', 'only' => ['index']]);
        ApiRoute::post('uphone-calls/save', ['as' => 'api.uphones-calls.save', 'uses' => 'UphoneCallsController@saveUphoneCalls']);
        //////////////////////////////////////////////////////////////////////////////////

        ApiRoute::post('leads/find-by-phone', ['as' => 'api.leads.find-by-phone','uses' => 'LeadController@findLeadByPhone']);        
        ApiRoute::post('leads/find-by-phone-campaign', ['as' => 'api.leads.find-by-phone-campaign','uses' => 'LeadController@findLeadByPhoneCampaign']);  
        ApiRoute::post('campaigns/find-campaigns-homologate', ['as' => 'api.campaigns.find-campaigns-homologate', 'uses' => 'CampaignController@getHomologateCampaigns']);


        //////////////////////////////////////////////////////////////////////////////////


        // Create Menu Update
        ApiRoute::post('companies/update-create-menu', ['as' => 'api.companies.update-create-menu', 'uses' => 'CompanyController@updateCreateMenu']);

        ApiRoute::resource('salesmans', 'SalesmanController', $options);
        ApiRoute::resource('expense-categories', 'ExpenseCategoryController', $options);
        ApiRoute::resource('expenses', 'ExpenseController', $options);
        ApiRoute::resource('products', 'ProductController', $options);
        ApiRoute::resource('users', 'UsersController', $options);
        ApiRoute::resource('companies', 'CompanyController', ['as' => 'api', 'only' => ['update']]);
        ApiRoute::resource('permissions', 'PermissionController', ['as' => 'api', 'only' => ['index']]);
        ApiRoute::resource('roles', 'RolesController', $options);
        ApiRoute::resource('lead-status', 'LeadStatusController', ['as' => 'api', 'except' => ['index']]);
        ApiRoute::resource('categories', 'CategoryController', $options);
        ApiRoute::resource('password-settings', 'PasswordSettingController',  ['as' => 'api', 'only' => ['update', 'index']]);
        ApiRoute::post('assign-users', ['as' => 'api.assign-users', 'uses' => 'LeadController@saveAssignUser']);
        ApiRoute::post('assign-multiple-users', ['as' => 'api.assign-multiple-users', 'uses' => 'LeadController@addMultipleUser']);
        ApiRoute::resource('notes-typifications', 'NotesTypificationController', $options);
        ApiRoute::post('multiple-typifications', ['as' => 'api.multiple-typifications', 'uses' => 'NotesTypificationController@addMultipleTypification']);
        ApiRoute::resource('message-templates', 'MessageTemplateController', $options);
        ApiRoute::post('message-templates/update-status', ['as' => 'api.message-templates.update-status', 'uses' => 'MessageTemplateController@updateStatus']);
        ApiRoute::post('notes-typifications/import', ['as' => 'api.notes-typifications.import', 'uses' => 'NotesTypificationController@import']);
        ApiRoute::resource('email-providers', 'EmailProviderController', $options);

        // templates de calidad
        ApiRoute::resource('plantillas-calidad', 'PlantillaCalidadController', $options);
        // acciones de calidad
        ApiRoute::resource('acciones-calidad', 'AccionCalidadController', $options);
        // motivos cancelacion
        ApiRoute::resource('motivos-calidad', 'MotivoCancelacionController', $options);
        // variables de los templates de calidad
        ApiRoute::post('variables/save', ['as' => 'api.variables.save', 'uses' => 'VariableCalidadController@save']);

        // Routes data form uCB
        ApiRoute::get('dataForm/user-campaigns', ['as' => 'api.dataForm.user-campaigns', 'uses' => 'DataFormController@userCampaigns']);
        ApiRoute::get('dataForm/user-scheduled', ['as' => 'api.dataForm.user-scheduled', 'uses' => 'DataFormController@agentScheduled']);
        // ApiRoute::get('dataForm/tipification-campaign', ['as' => 'api.dataForm.tipification-campaign', 'uses' => 'DataFormController@tipificationCampaign']);


    });
});