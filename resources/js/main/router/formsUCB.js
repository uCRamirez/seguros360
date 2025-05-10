import FormIndex from '../views/formsUCB/forms/index.vue';

// Importación dinámica de formularios en "WebForms/{CRM}/*.vue"
const formFiles = import.meta.glob('../views/formsUCB/Web/WebForms/*/*.vue');

const formRoutes = Object.keys(formFiles).map((path) => {
    const parts = path.split("/");
    const crmName = parts[parts.length - 2]; // Ej: CRM

    return {
        // path: crmName === "CRM"
        //     ? `/admin/formsUCB/${crmName}/:id?` // <-- solo CRM lleva :id
        //     : `/admin/formsUCB/${crmName}`,
        path: `/admin/formsUCB/${crmName}/:id?`, // <-- ruta
        name: `admin.formsUCB.${crmName}`, // <-- nombre siempre es este
        component: FormIndex,
        props: crmName === "CRM", // permite pasar :id como prop
        meta: {
            requireAuth: true,
            menuParent: "formsUCB",
            menuKey: crmName,
            permission: "forms_view_uCB",
            formPath: formFiles[path],
        },
    };
});




//  Ruta base de Forms UCB
const baseFormRoute = {
    path: "/admin/formsUCB",
    component: FormIndex,
    name: "admin.formsUCB.index",
    meta: {
        requireAuth: true,
        menuParent: "formsUCB",
        menuKey: "formsUCB",
        permission: "forms_view_uCB",
    }
};

export default [
    {
        path: '/',
        component: () => import('../../common/layouts/Admin.vue'),
        children: [baseFormRoute, ...formRoutes] //  Se añaden todas las rutas dinámicas
    }
];



// import FormIndex from '../views/formsUCB/forms/index.vue';

// // Importación dinámica de formularios en "WebForms/{CRM}/*.vue"
// const formFiles = import.meta.glob('../views/formsUCB/Web/WebForms/*/*.vue');

// const formRoutes = Object.keys(formFiles).map((path) => {
//     const parts = path.split("/");
//     const crmName = parts[parts.length - 2]; // Obtiene el nombre de la carpeta CRM (Ej: CRM1, CRM2)

//     return {
//         path: /admin/formsUCB/${crmName},
//         name: admin.formsUCB.${crmName},
//         component: FormIndex, //  Siempre usa la misma vista, pero cambia el formulario dinámicamente
//         meta: {
//             requireAuth: true,
//             menuParent: "formsUCB",
//             menuKey: crmName,
//             permission: "forms_view_uCB",
//             formPath: formFiles[path], //  Guarda la referencia al formulario
//         }
//     };
// });

// //  Ruta base de Forms UCB
// const baseFormRoute = {
//     path: "/admin/formsUCB",
//     component: FormIndex,
//     name: "admin.formsUCB.index",
//     meta: {
//         requireAuth: true,
//         menuParent: "formsUCB",
//         menuKey: "formsUCB",
//         permission: "forms_view_uCB",
//     }
// };

// export default [
//     {
//         path: '/',
//         component: () => import('../../common/layouts/Admin.vue'),
//         children: [baseFormRoute, ...formRoutes] //  Se añaden todas las rutas dinámicas
//     }
// ];