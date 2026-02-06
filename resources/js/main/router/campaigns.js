import CampaignIndex from '../views/campaigns/index.vue';
import CallManagerIndex from '../views/call-manager/index.vue';
import CallManagerAction from '../views/formsUCB/Web/WebForms/Correspondencia/views/TakeLeadAction.vue';
// import CallManagerAction_MFisico from '../views/formsUCB/Web/WebForms/MFisico/views/TakeLeadAction.vue';
// Para nuevos formularios se debe de agregar aqui la ruta al nuevo TakeLeadAction (Visual del formulario)

export default [
    {
        path: '/admin/forms/Correspondencia/:id?',
        component: CallManagerAction,
        name: 'admin.formsUCB.Correspondencia',
        props: true,
        meta: {
            requireAuth: true,
            menuParent: "formsUCB",
            menuKey: "Correspondencia",
            permission: "formsUCB_view",
        },
    },
    // {
    //     path: '/admin/formsUCB/MFisico/:id?',
    //     component: CallManagerAction_MFisico,
    //     name: 'admin.formsUCB.MFisico',
    //     props: true,
    //     meta: {
    //         requireAuth: true,
    //         menuParent: "MFisico",
    //         menuKey: "MFisico",
    //     },
    // },
    {
        path: '/',
        component: () => import('../../common/layouts/Admin.vue'),
        children: [
            {
                path: '/admin/call-manager',
                component: CallManagerIndex,
                name: 'admin.call_manager.index',
                meta: {
                    requireAuth: true,
                    menuParent: "call_manager",
                    menuKey: "call_manager",
                }
            },
            {
                path: '/admin/campaigns',
                component: CampaignIndex,
                name: 'admin.campaigns.index',
                meta: {
                    requireAuth: true,
                    menuParent: "campaigns",
                    menuKey: "campaigns",
                    permission: "campaigns_view"
                }
            },
        ]

    },
]
