import EmailTemplateIndex from '../views/messaging/email-templates/index.vue';
import MessageTemplate from '../views/messaging/message-templates/index.vue';

export default [
    {
        path: '/',
        component: () => import('../../common/layouts/Admin.vue'),
        children: [
            {
                path: '/admin/email-templates',
                component: EmailTemplateIndex,
                name: 'admin.email_templates.index',
                meta: {
                    requireAuth: true,
                    menuParent: "messaging",
                    menuKey: "email_templates",
                    permission: "email_templates_view"
                }
            },
            {
                path: '/admin/message-template',
                component: MessageTemplate,
                name: 'admin.message_templates.index',
                meta: {
                    requireAuth: true,
                    menuParent: "messaging",
                    menuKey: "message_templates",
                    permission: "message_templates_view"
                }
            },
        ]

    }
]
