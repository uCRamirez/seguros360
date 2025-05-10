import Category from '../views/categories/index.vue';

export default [
    {
        path: '/',
        component: () => import('../../common/layouts/Admin.vue'),
        children: [
            {
                path: '/admin/categories',
                component: Category,
                name: 'admin.categories.index',
                meta: {
                    requireAuth: true,
                    menuParent: "categories",
                    menuKey: route => "categories",
                    permission: "categories_view",
                }
            },
        ]

    }
]
