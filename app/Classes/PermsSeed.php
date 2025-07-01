<?php

namespace App\Classes;

use App\Models\Permission;
use Nwidart\Modules\Facades\Module;

class PermsSeed
{
    public static $mainPermissionsArray = [

        // Category
        'categories_view' => [
            'name' => 'categories_view',
            'display_name' => 'Category View'
        ],
        'categories_create' => [
            'name' => 'categories_create',
            'display_name' => 'Category Create'
        ],
        'categories_edit' => [
            'name' => 'categories_edit',
            'display_name' => 'Category Edit'
        ],
        'categories_delete' => [
            'name' => 'categories_delete',
            'display_name' => 'Category Delete'
        ],

        // Users
        'users_view' => [
            'name' => 'users_view',
            'display_name' => 'Staff Member View'
        ],
        'users_create' => [
            'name' => 'users_create',
            'display_name' => 'Staff Member Create'
        ],
        'users_edit' => [
            'name' => 'users_edit',
            'display_name' => 'Staff Member Edit'
        ],
        'users_delete' => [
            'name' => 'users_delete',
            'display_name' => 'Staff Member Delete'
        ],

        // Salesmans
        'salesmans_view' => [
            'name' => 'salesmans_view',
            'display_name' => 'Salesman View'
        ],
        'salesmans_create' => [
            'name' => 'salesmans_create',
            'display_name' => 'Salesman Create'
        ],
        'salesmans_edit' => [
            'name' => 'salesmans_edit',
            'display_name' => 'Salesman Edit'
        ],
        'salesmans_delete' => [
            'name' => 'salesmans_delete',
            'display_name' => 'Salesman Delete'
        ],

        // Campaigns
        'campaigns_view' => [
            'name' => 'campaigns_view',
            'display_name' => 'Campaigns View'
        ],
        'campaigns_view_all' => [
            'name' => 'campaigns_view_all',
            'display_name' => 'View All Campaigns'
        ],
        'view_completed_campaigns' => [
            'name' => 'view_completed_campaigns',
            'display_name' => 'View All Completed Campaigns'
        ],
        'campaigns_create' => [
            'name' => 'campaigns_create',
            'display_name' => 'Campaigns Create'
        ],
        'campaigns_edit' => [
            'name' => 'campaigns_edit',
            'display_name' => 'Campaigns Edit'
        ],
        'campaigns_delete' => [
            'name' => 'campaigns_delete',
            'display_name' => 'Campaigns Delete'
        ],
        'bases_view' => [
            'name' => 'bases_view',
            'display_name' => 'Bases View'
        ],
        'campaigns_recycle' => [
            'name' => 'campaigns_recycle',
            'display_name' => 'Campaigns Recycle Delete'
        ],
        'export_leads' => [
            'name' => 'export_leads',
            'display_name' => 'Export Leads'
        ],

        // Leads
        'leads_view_all' => [
            'name' => 'leads_view_all',
            'display_name' => 'View All Leads'
        ],
        'leads_create' => [
            'name' => 'leads_create',
            'display_name' => 'Leads Create'
        ],
        'leads_delete' => [
            'name' => 'leads_delete',
            'display_name' => 'Leads Delete'
        ],

        // email_templates
        'email_templates_view' => [
            'name' => 'email_templates_view',
            'display_name' => 'Email Templates View'
        ],
        'email_templates_view_all' => [
            'name' => 'email_templates_view_all',
            'display_name' => 'View All Email Templates'
        ],
        'email_templates_create' => [
            'name' => 'email_templates_create',
            'display_name' => 'Email Templates Create'
        ],
        'email_templates_edit' => [
            'name' => 'email_templates_edit',
            'display_name' => 'Email Templates Edit'
        ],
        'email_templates_delete' => [
            'name' => 'email_templates_delete',
            'display_name' => 'Email Templates Delete'
        ],

        // Forms
        'formsUCB_view' => [
            'name' => 'formsUCB_view',
            'display_name' => 'formsUCB'
        ],
        'forms_view' => [
            'name' => 'forms_view',
            'display_name' => 'form View'
        ],
        'forms_view_all' => [
            'name' => 'forms_view_all',
            'display_name' => 'View All Forms'
        ],
        'forms_create' => [
            'name' => 'forms_create',
            'display_name' => 'Forms Create'
        ],
        'forms_edit' => [
            'name' => 'forms_edit',
            'display_name' => 'Forms Edit'
        ],
        'forms_delete' => [
            'name' => 'forms_delete',
            'display_name' => 'Forms Delete'
        ],

            // Localidades
        'localidades_view' => [
            'name'         => 'localidades_view',
            'display_name' => 'Localidades View',
        ],
        'localidades_create' => [
            'name'         => 'localidades_create',
            'display_name' => 'Localidades Create',
        ],
        'localidades_edit' => [
            'name'         => 'localidades_edit',
            'display_name' => 'Localidades Edit',
        ],
        'localidades_delete' => [
            'name'         => 'localidades_delete',
            'display_name' => 'Localidades Delete',
        ],


        // Lead Table Fields
        'form_field_names_view' => [
            'name' => 'form_field_names_view',
            'display_name' => 'Lead Table Fields View'
        ],
        'form_field_names_create' => [
            'name' => 'form_field_names_create',
            'display_name' => 'Lead Table Fields Create'
        ],
        'form_field_names_edit' => [
            'name' => 'form_field_names_edit',
            'display_name' => 'Lead Table Fields Edit'
        ],
        'form_field_names_delete' => [
            'name' => 'form_field_names_delete',
            'display_name' => 'Lead Table Fields Delete'
        ],

          // Uphone calls table
        'uphone_calls_view' => [
            'name' => 'uphone_calls_view',
            'display_name' => 'Uphone Calls View'
        ],

         // Expense Category
        'expense_categories_view' => [
            'name' => 'expense_categories_view',
            'display_name' => 'Expense Category View'
        ],
        'expense_categories_create' => [
            'name' => 'expense_categories_create',
            'display_name' => 'Expense Category Create'
        ],
        'expense_categories_edit' => [
            'name' => 'expense_categories_edit',
            'display_name' => 'Expense Category Edit'
        ],
        'expense_categories_delete' => [
            'name' => 'expense_categories_delete',
            'display_name' => 'Expense Category Delete'
        ],

        // Expense
        'expenses_view' => [
            'name' => 'expenses_view',
            'display_name' => 'Expense View'
        ],
        'expenses_create' => [
            'name' => 'expenses_create',
            'display_name' => 'Expense Create'
        ],
        'expenses_edit' => [
            'name' => 'expenses_edit',
            'display_name' => 'Expense Edit'
        ],
        'expenses_delete' => [
            'name' => 'expenses_delete',
            'display_name' => 'Expense Delete'
        ],

        // Message Templates
        'message_templates_view' => [
            'name' => 'message_templates_view',
            'display_name' => 'Message Templates View'
        ],
        'message_templates_view_all' => [
            'name' => 'message_templates_view_all',
            'display_name' => 'View All Message Templates'
        ],
        'message_templates_create' => [
            'name' => 'message_templates_create',
            'display_name' => 'Message Templates Create'
        ],
        'message_templates_edit' => [
            'name' => 'message_templates_edit',
            'display_name' => 'Message Templates Edit'
        ],
        'message_templates_delete' => [
            'name' => 'message_templates_delete',
            'display_name' => 'Message Templates Delete'
        ],

        // Product
        'products_view' => [
            'name' => 'products_view',
            'display_name' => 'Product View'
        ],
        'products_create' => [
            'name' => 'products_create',
            'display_name' => 'Product Create'
        ],
        'products_edit' => [
            'name' => 'products_edit',
            'display_name' => 'Product Edit'
        ],
        'products_delete' => [
            'name' => 'products_delete',
            'display_name' => 'Product Delete'
        ],

        // Currency
        'currencies_view' => [
            'name' => 'currencies_view',
            'display_name' => 'Currency View'
        ],
        'currencies_create' => [
            'name' => 'currencies_create',
            'display_name' => 'Currency Create'
        ],
        'currencies_edit' => [
            'name' => 'currencies_edit',
            'display_name' => 'Currency Edit'
        ],
        'currencies_delete' => [
            'name' => 'currencies_delete',
            'display_name' => 'Currency Delete'
        ],

        // Modules
        'modules_view' => [
            'name' => 'modules_view',
            'display_name' => 'Modules View'
        ],

        // Role
        'roles_view' => [
            'name' => 'roles_view',
            'display_name' => 'Role View'
        ],
        'roles_create' => [
            'name' => 'roles_create',
            'display_name' => 'Role Create'
        ],
        'roles_edit' => [
            'name' => 'roles_edit',
            'display_name' => 'Role Edit'
        ],
        'roles_delete' => [
            'name' => 'roles_delete',
            'display_name' => 'Role Delete'
        ],

        // Company
        'companies_edit' => [
            'name' => 'companies_edit',
            'display_name' => 'Company Edit'
        ],

        // Translation
        'translations_view' => [
            'name' => 'translations_view',
            'display_name' => 'Translation View'
        ],
        'translations_create' => [
            'name' => 'translations_create',
            'display_name' => 'Translation Create'
        ],
        'translations_edit' => [
            'name' => 'translations_edit',
            'display_name' => 'Translation Edit'
        ],
        'translations_delete' => [
            'name' => 'translations_delete',
            'display_name' => 'Translation Delete'
        ],

        // Storage Settings
        'storage_edit' => [
            'name' => 'storage_edit',
            'display_name' => 'Storage Settings Edit'
        ],

        // Email Settings
        'email_edit' => [
            'name' => 'email_edit',
            'display_name' => 'Email Settings Edit'
        ],

          // Message Providers
        'message_providers_view' => [
            'name' => 'message_providers_view',
        'display_name' => 'Message Providers View'
        ],
        'message_providers_create' => [
            'name' => 'message_providers_create',
            'display_name' => 'Message Providers Create'
        ],
        'message_providers_edit' => [
            'name' => 'message_providers_edit',
            'display_name' => 'Message Providers Edit'
        ],
        'message_providers_delete' => [
            'name' => 'message_providers_delete',
            'display_name' => 'Message Providers Delete'
        ],

        //Email Providers
        'email_providers_view' => [
            'name' => 'email_providers_view',
            'display_name' => 'Email Providers View'
        ],
        'email_providers_create' => [
            'name' => 'email_providers_create',
            'display_name' => 'Email Providers Create'
        ],
        'email_providers_edit' => [
            'name' => 'email_providers_edit',
            'display_name' => 'Email Providers Edit'
        ],
        'email_providers_delete' => [
            'name' => 'email_providers_delete',
            'display_name' => 'Email Providers Delete'
        ],

        // Localidades
        'audits_view' => [
            'name'         => 'audits_view',
            'display_name' => 'Audits View',
        ],
        'password_settings_view' => [
            'name'         => 'password_settings_view',
            'display_name' => 'Password Settings View',
        ],
        'password_settings_edit' => [
            'name'         => 'password_settings_edit',
            'display_name' => 'Password Settings Edit',
        ],

        // tipificaciones
        'notes_typifications_view' => [
            'name'         => 'notes_typifications_view',
            'display_name' => 'Notes typifications View',
        ],
        'notes_typifications_add' => [
            'name'         => 'notes_typifications_add',
            'display_name' => 'Notes typifications Add',
        ],
        'notes_typifications_edit' => [
            'name'         => 'notes_typifications_edit',
            'display_name' => 'Notes typifications Edit',
        ],
        'notes_typifications_delete' => [
            'name'         => 'notes_typifications_delete',
            'display_name' => 'Notes typifications Delete',
        ],

        // lead status
        'lead_status_view' => [
            'name'         => 'lead_status_view',
            'display_name' => 'Lead status View',
        ],
        'lead_status_add' => [
            'name'         => 'lead_status_add',
            'display_name' => 'Lead status Add',
        ],
        'lead_status_edit' => [
            'name'         => 'lead_status_edit',
            'display_name' => 'Lead status Edit',
        ],
        'lead_status_delete' => [
            'name'         => 'lead_status_delete',
            'display_name' => 'Lead status Delete',
        ],
        // calidad vistas
        'quality_view' => [
            'name'         => 'quality_view',
            'display_name' => 'Quality View',
        ],
        'quality_add' => [
            'name'         => 'quality_add',
            'display_name' => 'Quality Add',
        ],
        'quality_edit' => [
            'name'         => 'quality_edit',
            'display_name' => 'Quality Edit',
        ],
        'quality_delete' => [
            'name'         => 'quality_delete',
            'display_name' => 'Quality Delete',
        ],

        // ventas
        'sales_view' => [
            'name'         => 'sales_view',
            'display_name' => 'Sales View',
        ],
        'sales_add' => [
            'name'         => 'sales_add',
            'display_name' => 'Sales Add',
        ],
        'sales_edit' => [
            'name'         => 'sales_edit',
            'display_name' => 'Sales Edit',
        ],
        'sales_delete' => [
            'name'         => 'sales_delete',
            'display_name' => 'Sales Delete',
        ],

        // gestiones
        'notes_edit' => [
            'name'         => 'notes_edit',
            'display_name' => 'Notes Edit',
        ],
        'notes_delete' => [
            'name'         => 'notes_delete',
            'display_name' => 'Notes Delete',
        ],

        // plantillas calidad
        'plantillas_calidad_view' => [
            'name' => 'plantillas_calidad_view',
            'display_name' => 'Quality Templates View'
        ],
        'plantillas_calidad_create' => [
            'name' => 'plantillas_calidad_create',
            'display_name' => 'Quality Templates Create'
        ],
        'plantillas_calidad_edit' => [
            'name' => 'plantillas_calidad_edit',
            'display_name' => 'Quality Templates Edit'
        ],
        'plantillas_calidad_delete' => [
            'name' => 'plantillas_calidad_delete',
            'display_name' => 'Quality Templates Delete'
        ],

        // motivos calidad
        'motivos_calidad_view' => [
            'name' => 'motivos_calidad_view',
            'display_name' => 'Quality Reasons View'
        ],
        'motivos_calidad_create' => [
            'name' => 'motivos_calidad_create',
            'display_name' => 'Quality Reasons Create'
        ],
        'motivos_calidad_edit' => [
            'name' => 'motivos_calidad_edit',
            'display_name' => 'Quality Reasons Edit'
        ],
        'motivos_calidad_delete' => [
            'name' => 'motivos_calidad_delete',
            'display_name' => 'Quality Reasons Delete'
        ],

        // accciones calidad
        'acciones_calidad_view' => [
            'name' => 'acciones_calidad_view',
            'display_name' => 'Quality Actions View'
        ],
        'acciones_calidad_create' => [
            'name' => 'acciones_calidad_create',
            'display_name' => 'Quality Actions Create'
        ],
        'acciones_calidad_edit' => [
            'name' => 'acciones_calidad_edit',
            'display_name' => 'Quality Actions Edit'
        ],
        'acciones_calidad_delete' => [
            'name' => 'acciones_calidad_delete',
            'display_name' => 'Quality Actions Delete'
        ],

        // faltante
        'leads_view' => [
            'name' => 'leads_view',
            'display_name' => 'Leads View'
        ],
        'call_logs_view' => [
            'name' => 'call_logs_view',
            'display_name' => 'Call Logs View'
        ],
        'lead_notes_view' => [
            'name' => 'lead_notes_view',
            'display_name' => 'Lead Notes View'
        ],
        'lead_notes_ventas_view' => [
            'name' => 'lead_notes_ventas_view',
            'display_name' => 'Lead Notes Sales View'
        ],

    ];

    public static $eStorePermissions = [];

    public static function getPermissionArray($moduleName)
    {
        return self::$mainPermissionsArray;
    }

    public static function seedPermissions($moduleName = '')
    {
        $permissions = self::getPermissionArray($moduleName);

        foreach ($permissions as $group => $permission) {
            $permissionCount = Permission::where('name', $permission['name'])->count();

            if ($permissionCount == 0) {
                $newPermission = new Permission();
                $newPermission->name = $permission['name'];
                $newPermission->display_name = $permission['display_name'];
                $newPermission->save();
            }
        }
    }

    public static function seedMainPermissions()
    {
        // Main Module
        self::seedPermissions();
    }

    public static function seedAllModulesPermissions()
    {
        // Main Module
        self::seedMainPermissions();

        $allModules = Module::all();
        foreach ($allModules as $allModule) {
            self::seedPermissions($allModule);
        }
    }
}