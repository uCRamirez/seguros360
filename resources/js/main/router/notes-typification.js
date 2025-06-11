import NotesTypification from '../views/notes-typifications/index.vue';
import TemplatesCalidad from '../views/quality-templates/index.vue';
import TemplatesAcciones from '../views/acciones/index.vue';
import TemplatesMotivos from '../views/motivos-cancelacion/index.vue';

export default [
    {
        path: '/',
        component: () => import('../../common/layouts/Admin.vue'),
        children: [
            {
                path: '/admin/notes-typifications',
                component: NotesTypification,
                name: 'admin.notes_typifications.index',
                meta: {
                    requireAuth: true,
                    menuParent: "notes_typifications",
                    menuKey: route => "notes_typifications",
                    permission: "notes_typifications_view",
                }
            },
            {
                path: '/admin/templates',
                component: TemplatesCalidad,
                name: 'admin.templates.index',
                meta: {
                    requireAuth: true,
                    menuParent: "quality_templates",
                    menuKey: route => "quality_templates",
                    permission: "quality_view",
                }
            },
            {
                path: '/admin/actions',
                component: TemplatesAcciones,
                name: 'admin.templates_acciones.index',
                meta: {
                    requireAuth: true,
                    menuParent: "templates_acciones",
                    menuKey: route => "templates_acciones",
                    permission: "acciones_calidad_view",
                }
            },
            {
                path: '/admin/reasons-cancellation',
                component: TemplatesMotivos,
                name: 'admin.templates_motivos.index',
                meta: {
                    requireAuth: true,
                    menuParent: "templates_motivos",
                    menuKey: route => "templates_motivos",
                    permission: "motivos_calidad_view",
                }
            },
        ]

    }
]
