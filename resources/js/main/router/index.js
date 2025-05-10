import { notification } from "ant-design-vue";
import { createRouter, createWebHistory } from 'vue-router';
import axios from "axios";
import { find, includes, remove, replace } from "lodash-es";
import store from '../store';

import AuthRoutes from './auth';
import DashboardRoutes from './dashboard';
import UserRoutes from './users';
import CampaignRoutes from './campaigns';
import ExpensesRoutes from './expenses';
import ProductsRoutes from './products';
import MessagingRoutes from './messaging';
import FormRoutes from './forms';
import FormRoutesUCB from './formsUCB'; // SE DEBE IMPORTAR EL JS QUE CONTIENE EL PATH Y DEMAS INFO DE LA RUTA
import LeadCallRoutes from './leadsCalls';
import SettingRoutes from './settings';
import BookingRoutes from './bookings';
import SetupAppRoutes from './setupApp';
import CategoryRoutes from './categories';
import UphoneCallsRoutes from './uphoneCalls';
import notesTypification from "./notes-typification";
import { checkUserPermission } from '../../common/scripts/functions';

const appType = window.config.app_type;
const allActiveModules = window.config.modules;

const isAdminCompanySetupCorrect = () => {
    var appSetting = store.state.auth.appSetting;

    if (appSetting.x_currency_id == null) {
        return false;
    }

    return true;
}

const isSuperAdminCompanySetupCorrect = () => {
    var appSetting = store.state.auth.appSetting;

    if (appSetting.x_currency_id == null || appSetting.white_label_completed == false) {
        return false;
    }

    return true;
}

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '',
            redirect: '/admin/login'
        },
        ...AuthRoutes,
        ...DashboardRoutes,
        ...UserRoutes,
        ...SettingRoutes,
        ...CampaignRoutes,
        ...MessagingRoutes,
        ...FormRoutes,
        ...FormRoutesUCB, // HABILITAR LA RUTA EN EL ARRAY DE RUTAS
        ...LeadCallRoutes,
        ...BookingRoutes,
        ...ExpensesRoutes,
        ...ProductsRoutes,
        ...CategoryRoutes,
        ...UphoneCallsRoutes,
        ...notesTypification
    ],
    scrollBehavior: () => ({ left: 0, top: 0 }),
});

// Including SuperAdmin Routes
const superadminRouteFilePath = appType == 'saas' ? 'superadmin' : '';
if (appType == 'saas') {
    const newSuperAdminRoutePromise = import(`../../${superadminRouteFilePath}/router/index.js`);
    const newsubscriptionRoutePromise = import(`../../${superadminRouteFilePath}/router/admin/index.js`);

    Promise.all([newSuperAdminRoutePromise, newsubscriptionRoutePromise]).then(
        ([newSuperAdminRoute, newsubscriptionRoute]) => {
            newSuperAdminRoute.default.forEach(route => router.addRoute(route));
            newsubscriptionRoute.default.forEach(route => router.addRoute(route));
            SetupAppRoutes.forEach(route => router.addRoute(route));
        }
    );
} else {
    SetupAppRoutes.forEach(route => router.addRoute(route));
}


export default router
