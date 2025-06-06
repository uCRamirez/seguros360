import LeadIndex from '../views/leads-calls/leads/index.vue';
import CallLogs from '../views/leads-calls/call-logs/index.vue';
import LeadNotes from '../views/leads-calls/notes/index.vue';
import LeadNotesVentas from '../views/leads-calls/venta/index.vue';
import LeadNotesCalidad from '../views/leads-calls/calidad/index.vue';

export default [
    {
        path: '/',
        component: () => import('../../common/layouts/Admin.vue'),
        children: [
            {
                path: '/admin/leads',
                component: LeadIndex,
                name: 'admin.leads.index',
                meta: {
                    requireAuth: true,
                    menuParent: "leads",
                    menuKey: "leads",
                    permission: "leads_view",
                }
            },
            {
                path: '/admin/call-logs',
                component: CallLogs,
                name: 'admin.call_logs.index',
                meta: {
                    requireAuth: true,
                    menuParent: "leads",
                    menuKey: "call_logs",
                    permission: "call_logs_view",
                }
            },
            {
                path: '/admin/notes',
                component: LeadNotes,
                name: 'admin.lead_notes.index',
                meta: {
                    requireAuth: true,
                    menuParent: "leads",
                    menuKey: "lead_notes",
                    permission: "lead_notes_view",
                }
            },
            {
                path: '/admin/ventas',
                component: LeadNotesVentas,
                name: 'admin.lead_notes_ventas.index',
                meta: {
                    requireAuth: true,
                    // menuParent: "leads",
                    menuKey: "lead_notes_ventas",
                    permission: "lead_notes_ventas_view",
                }
            },
            {
                path: '/admin/calidad',
                component: LeadNotesCalidad,
                name: 'admin.lead_notes_calidad.index',
                meta: {
                    requireAuth: true,
                    // menuParent: "leads",
                    menuKey: "lead_notes_calidad",
                    permission: "lead_notes_calidad_view",
                }
            },
        ]

    }
]
