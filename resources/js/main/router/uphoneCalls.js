import Category from '../views/uphone-calls/index.vue';

export default [
    {
        path: '/',
        component: () => import('../../common/layouts/Admin.vue'),
        children: [
            {
                path: '/admin/uphone-calls',
                component: Category,
                name: 'admin.uphone_calls.index',
                meta: {
                    requireAuth: true,
                    menuParent: "uphone_calls",
                    menuKey: route => "uphone_calls",
                    permission: "uphone_calls_view",
                }
            },
        ]

    }
]
