import NotesTypification from '../views/notes-typifications/index.vue';

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
        ]

    }
]
