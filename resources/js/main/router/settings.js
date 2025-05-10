import CompanyEdit from "../views/settings/company/Edit.vue";
import ProfileEdit from "../views/settings/profile/Edit.vue";
import PasswordSetting from "../views/settings/password-settings/Edit.vue";
import Langs from "../views/settings/translations/langs/index.vue";
import Roles from "../views/settings/roles/index.vue";
import Currencies from "../views/settings/currency/index.vue";
import CommonAdminSettings from "./common/adminSettings";
import LeadStatus from "../views/settings/lead-status/index.vue";
import Audit from "../views/settings/audit-report/index.vue";
import MessageProvider from '../views/settings/message-providers/index.vue';
import EmailProvider from '../views/settings/email-providers/index.vue';

export default [
    {
        path: "/admin/settings/",
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "company",
                component: CompanyEdit,
                name: "admin.settings.company.index",
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: (route) => "company",
                    permission: "companies_edit",
                },
            },
            {
                path: "profile",
                component: ProfileEdit,
                name: "admin.settings.profile.index",
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: (route) => "profile",
                },
            },
            {
                path: "langs",
                component: Langs,
                name: "admin.settings.langs.index",
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: (route) => "translations",
                    permission: "translations_view",
                },
            },
            {
                path: "roles",
                component: Roles,
                name: "admin.settings.roles.index",
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: (route) => "roles",
                    permission: "roles_view",
                },
            },
            {
                path: "currencies",
                component: Currencies,
                name: "admin.settings.currencies.index",
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: (route) => "currencies",
                    permission: "currencies_view",
                },
            },
            {
                path: "lead-status",
                component: LeadStatus,
                name: "admin.settings.lead-status.index",
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: (route) => "lead_status",
                },
            },
            {
                path: "audits",
                component: Audit,
                name: "admin.settings.audits.index",
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: (route) => "audits",
                },
            },
            {
                path: "password-settings",
                component: PasswordSetting,
                name: "admin.settings.password-settings.index",
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: (route) => "password_settings",
                },
            },

            {
                path: 'message_providers',
                component: MessageProvider,
                name: 'admin.settings.message_providers.index',
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: route => "message_providers",
                    permission: "message_provider_edit"
                }
            },

            {
                path: 'email_providers',
                component: EmailProvider,
                name: 'admin.settings.email_providers.index',
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: route => "email_providers",
                    permission: "message_provider_edit"
                }
            },

            ...CommonAdminSettings,
        ],
    },
];
