<template>
    <a-layout-sider :width="240" :style="{
        margin: '0 0 0 0',
        overflowY: 'auto',
        position: 'fixed',
        paddingTop: '8px',
        zIndex: 998,
        borderRight: themeMode == 'dark' ? '1px solid #303030' : '1px solid #f0f0f0',
    }" :trigger="null" :collapsed="menuCollapsed"
        :theme="themeMode == 'dark' ? 'light' : appSetting.left_sidebar_theme" class="sidebar-right-border">
        <div v-if="menuCollapsed" class="logo">
            <img :style="{
                height: '32px',
            }" :src="themeMode == 'dark'
                ? appSetting.small_dark_logo_url
                : appSetting.left_sidebar_theme == 'dark'
                    ? appSetting.small_dark_logo_url
                    : appSetting.small_light_logo_url
                " />
        </div>
        <div v-else>
            <img :style="{
                width: '150px',
                height: '53px',
                paddingLeft: appSetting.rtl ? '0px' : '30px',
                paddingRight: appSetting.rtl ? '30px' : '0px',
                paddingTop: '5px',
                paddingBottom: '20px',
                marginLeft: appSetting.rtl ? '0px' : '10px',
                marginRight: appSetting.rtl ? '10px' : '0px',
            }" :src="themeMode == 'dark'
                ? appSetting.dark_logo_url
                : appSetting.left_sidebar_theme == 'dark'
                    ? appSetting.dark_logo_url
                    : appSetting.light_logo_url
                " />
            <CloseOutlined v-if="innerWidth <= 991" :style="{
                marginLeft: appSetting.rtl ? '0px' : '45px',
                marginRight: appSetting.rtl ? '45px' : '0px',
                verticalAlign: 'super',
                color:
                    themeMode == 'dark'
                        ? '#fff'
                        : appSetting.left_sidebar_theme == 'dark'
                            ? '#fff'
                            : '#000000',
            }" @click="menuSelected" />
        </div>

        <div class="main-sidebar">
            <perfect-scrollbar :options="{
                wheelSpeed: 1,
                swipeEasing: true,
                suppressScrollX: true,
            }">
                <a-menu :theme="themeMode == 'dark' ? 'light' : appSetting.left_sidebar_theme" :openKeys="openKeys"
                    v-model:selectedKeys="selectedKeys" :mode="mode" @openChange="onOpenChange"
                    :style="{ borderRight: 'none' }">
                    <a-menu-item @click="
                        () => {
                            menuSelected();
                            $router.push({ name: 'admin.dashboard.index' });
                        }
                    " key="dashboard">
                        <HomeOutlined />
                        <span>{{ $t("menu.dashboard") }}</span>
                    </a-menu-item>

                    <!--#########################-->
                    <!-- OPCION FORMS UCBUSINESS -->
                    <!-- La variable  formFolders se toma del archivo common.js -->

                    <a-sub-menu key="formsUCB" v-if="(formFolders.length && permsArray.includes('forms_view')) || permsArray.includes('admin')">
                        <template #title>
                            <span>
                                <FolderOpenOutlined /><span>{{ $t("menu.forms") }}</span>
                            </span>
                        </template>

                        <a-menu-item v-for="form in formFolders" :key="form.title" @click="() => {
                            menuSelected();
                            $router.push({ name: form.name});
                        }">
                            <FolderOpenOutlined />
                            <span>{{ form.title }}</span>
                        </a-menu-item>
                    </a-sub-menu>

                    <!-- FIN OPCION FORMS UCBUSINESS -->
                    <!--#############################-->

                    <LeftSideBarMainHeading  v-if="
                        permsArray.includes('categories_view') ||
                        permsArray.includes('admin') ||
                        ((permsArray.includes('products_view') ||
                            permsArray.includes('admin')))
                        " 
                        :title="$t('menu.products')" :visible="menuCollapsed" 
                    />

                    <a-menu-item v-if="
                        (permsArray.includes('categories_view') ||
                            permsArray.includes('admin'))
                    " @click="
                        () => {
                            menuSelected();
                            $router.push({
                                name: 'admin.categories.index',
                            });
                        }
                    " key="categories">
                        <FileAddOutlined />
                        <span>{{ $t("menu.categories") }}</span>
                    </a-menu-item>

                    <a-menu-item v-if="
                        (permsArray.includes('products_view') ||
                            permsArray.includes('admin'))
                    " @click="
                        () => {
                            menuSelected();
                            $router.push({ name: 'admin.products.index' });
                        }
                    " key="products">
                        <CopyrightCircleOutlined />
                        <span>{{ $t("menu.products") }}</span>
                    </a-menu-item>

                    <LeftSideBarMainHeading v-if="
                        permsArray.includes('users_view') ||
                        permsArray.includes('admin') ||
                        ((permsArray.includes('salesmans_view') ||
                            permsArray.includes('admin')))
                    " :title="$t('menu.user_management')" :visible="menuCollapsed" />

                    <a-menu-item v-if="
                        permsArray.includes('users_view') ||
                        permsArray.includes('admin')
                    " @click="
                        () => {
                            menuSelected();
                            $router.push({ name: 'admin.users.index' });
                        }
                    " key="users">
                        <SolutionOutlined />
                        <span>{{ $t("menu.staff_members") }}</span>
                    </a-menu-item>

                    <LeftSideBarMainHeading :title="$t('menu.lead_management')" :visible="menuCollapsed" />

                    <a-menu-item @click="
                        () => {
                            menuSelected();
                            $router.push({
                                name: 'admin.call_manager.index',
                            });
                        }
                    " key="call_manager">
                        <AppstoreOutlined />
                        <span>{{ $t("menu.call_manager") }}</span>
                    </a-menu-item>

                    <a-menu-item v-if="
                        permsArray.includes('campaigns_view') ||
                        permsArray.includes('admin')
                    " @click="
                        () => {
                            menuSelected();
                            $router.push({ name: 'admin.campaigns.index' });
                        }
                    " key="campaigns">
                        <CopyrightCircleOutlined />
                        <span>{{ $t("menu.campaigns") }}</span>
                    </a-menu-item>

                    <!-- Opciones de Leads and Calls -->
                    <a-sub-menu key="leads">
                        <template #title>
                            <span>
                                <ApartmentOutlined />
                                <span>{{ $t("menu.leads_calls") }}</span>
                            </span>
                        </template>
                        <a-menu-item @click="
                            () => {
                                menuSelected();
                                $router.push({ name: 'admin.leads.index' });
                            }
                        " key="leads">
                            <SoundOutlined />
                            <span>{{ $t("menu.leads") }}</span>
                        </a-menu-item>
                        <a-menu-item @click="
                            () => {
                                menuSelected();
                                $router.push({
                                    name: 'admin.call_logs.index',
                                });
                            }
                        " key="call_logs">
                            <PhoneOutlined />
                            <span>{{ $t("menu.call_logs") }}</span>
                        </a-menu-item>
                        <a-menu-item @click="
                            () => {
                                menuSelected();
                                $router.push({
                                    name: 'admin.lead_notes.index',
                                });
                            }
                        " key="lead_notes">
                            <FileTextOutlined />
                            <span>{{ $t("menu.lead_notes") }}</span>
                        </a-menu-item>
                    </a-sub-menu>
                    <!-- ventas -->
                    <LeftSideBarMainHeading v-if="(permsArray.includes('sales_view') || permsArray.includes('admin'))" :title="$t('menu.sales')" :visible="menuCollapsed" />
                    <a-menu-item
                        v-if="(permsArray.includes('sales_view') ||
                                permsArray.includes('admin'))"
                        @click="
                        () => {
                            menuSelected();
                            $router.push({
                                name: 'admin.lead_notes_ventas.index',
                            });
                        }
                        " key="lead_notes_ventas">
                        <ShopOutlined />
                        <span>{{ $t("menu.sales") }}</span>
                    </a-menu-item>

                    <!-- calidad -->
                    <LeftSideBarMainHeading v-if="(permsArray.includes('quality_view') || permsArray.includes('templates_view') || permsArray.includes('admin'))" :title="$t('menu.quality')" :visible="menuCollapsed" />
                    <a-menu-item  @click="
                            () => {
                                menuSelected();
                                $router.push({
                                    name: 'admin.lead_notes_calidad.index',
                                });
                            }
                        " key="lead_notes_calidad"
                        v-if="(permsArray.includes('quality_view') ||
                            permsArray.includes('admin'))"
                    >
                            <FileTextOutlined />
                            <span>{{ $t("menu.quality") }}</span>
                    </a-menu-item>
                    <!-- templates -->
                    <a-menu-item v-if="
                        permsArray.includes('plantillas_calidad_view') ||
                        permsArray.includes('admin')
                    " @click="
                        () => {
                            menuSelected();
                            $router.push({
                                name: 'admin.templates.index',
                            });
                        }
                    " key="quality_templates">
                        <LineHeightOutlined />
                        <span>{{ $t("menu.templates") }}</span>
                    </a-menu-item>

                    <!-- acciones -->
                    <a-menu-item v-if="
                        permsArray.includes('acciones_calidad_view') ||
                        permsArray.includes('admin')
                    " @click="
                        () => {
                            menuSelected();
                            $router.push({
                                name: 'admin.templates_acciones.index',
                            });
                        }
                    " key="templates_acciones">
                        <AppstoreOutlined />
                        <span>{{ `${$t("menu.actions")}, ${$t("common.closures")} & ${$t("common.improvement_options")}` }}</span>
                    </a-menu-item>

                    <!-- motivos -->
                    <a-menu-item v-if="
                        permsArray.includes('motivos_calidad_view') ||
                        permsArray.includes('admin')
                    " @click="
                        () => {
                            menuSelected();
                            $router.push({
                                name: 'admin.templates_motivos.index',
                            });
                        }
                    " key="templates_motivos">
                        <AppstoreAddOutlined />
                        <span>{{ $t("menu.reasons_cancellation") }}</span>
                    </a-menu-item>
                    

                    <LeftSideBarMainHeading :title="$t('menu.settings')" :visible="menuCollapsed" />

                    <a-menu-item v-if="
                        permsArray.includes('notes_typifications_view') ||
                        permsArray.includes('admin')
                    " @click="
                        () => {
                            menuSelected();
                            $router.push({
                                name: 'admin.notes_typifications.index',
                            });
                        }
                    " key="notes_typifications">
                        <LineHeightOutlined />
                        <span>{{ $t("menu.notes_typifications") }}</span>
                    </a-menu-item>

                    
                    <component v-for="(appModule, index) in appModules" :key="index" v-bind:is="appModule + 'Menu'"
                        @menuSelected="menuSelected" />

                    <a-menu-item @click="
                        () => {
                            menuSelected();
                            $router.push({
                                name: 'admin.settings.profile.index',
                            });
                        }
                    " key="settings">
                        <SettingOutlined />
                        <span>{{ $t("menu.settings") }}</span>
                    </a-menu-item>

                    <a-menu-item v-if="appType == 'saas' && appSetting.x_admin_id == user.xid" @click="
                        () => {
                            menuSelected();
                            $router.push({
                                name: 'admin.subscription.current_plan',
                            });
                        }
                    " key="subscription">
                        <DollarCircleOutlined />
                        <span>{{ $t("menu.subscription") }}</span>
                    </a-menu-item>

                    <a-popconfirm :title="$t('menu.you_agree')" @confirm="logout">
                        <template #icon>
                            <question-circle-outlined style="color: red" />
                        </template>

                        <a-menu-item key="logout">
                            <LogoutOutlined />
                            <span>{{ $t('menu.logout') }}</span>
                        </a-menu-item>
                    </a-popconfirm>

                </a-menu>
            </perfect-scrollbar>
        </div>
    </a-layout-sider>
</template>

<script>
import { defineComponent, ref, watch, onMounted, computed } from "vue";
import { Layout } from "ant-design-vue";
import { useStore } from "vuex";
import { useRoute } from "vue-router";
import {
    HomeOutlined,
    LogoutOutlined,
    UserOutlined,
    SettingOutlined,
    CloseOutlined,
    ShoppingOutlined,
    ShoppingCartOutlined,
    AppstoreOutlined,
    AppstoreAddOutlined,
    ShopOutlined,
    BarChartOutlined,
    CalculatorOutlined,
    TeamOutlined,
    WalletOutlined,
    BankOutlined,
    RocketOutlined,
    LaptopOutlined,
    CarOutlined,
    DollarCircleOutlined,
    CopyrightCircleOutlined,
    IeOutlined,
    PhoneOutlined,
    FolderOpenOutlined,
    FolderViewOutlined,
    FileTextOutlined,
    SoundOutlined,
    ApartmentOutlined,
    ScheduleOutlined,
    SolutionOutlined,
    MailOutlined,
    FormOutlined,
    InsertRowBelowOutlined,
    FileAddOutlined,
    UnderlineOutlined,
    LineHeightOutlined,
    CommentOutlined,
    QuestionCircleOutlined,
} from "@ant-design/icons-vue";
import { PerfectScrollbar } from "vue3-perfect-scrollbar";
import common from "../../common/composable/common";
import LeftSideBarMainHeading from "../components/common/typography/LeftSideBarMainHeading.vue";
const { Sider } = Layout;

export default defineComponent({
    components: {
        Sider,
        PerfectScrollbar,
        Layout,
        LeftSideBarMainHeading,

        HomeOutlined,
        LogoutOutlined,
        UserOutlined,
        SettingOutlined,
        CloseOutlined,
        ShoppingOutlined,
        ShoppingCartOutlined,
        AppstoreOutlined,
        AppstoreAddOutlined,
        ShopOutlined,
        BarChartOutlined,
        CalculatorOutlined,
        TeamOutlined,
        WalletOutlined,
        BankOutlined,
        RocketOutlined,
        LaptopOutlined,
        CarOutlined,
        DollarCircleOutlined,
        CopyrightCircleOutlined,
        IeOutlined,
        FolderOpenOutlined,
        FolderViewOutlined,
        FileTextOutlined,
        SoundOutlined,
        PhoneOutlined,
        ApartmentOutlined,
        ScheduleOutlined,
        SolutionOutlined,
        MailOutlined,
        FormOutlined,
        InsertRowBelowOutlined,
        FileAddOutlined,
        UnderlineOutlined,
        LineHeightOutlined,
        CommentOutlined,
        QuestionCircleOutlined,
    },
    setup(props, { emit }) {
        const {
            appSetting,
            appType,
            themMode,
            user,
            permsArray,
            appModules,
            menuCollapsed,
            willSubscriptionModuleVisible,
            themeMode,
            formFolders, // se toman los nombres de los formularios, estos son globales tomado en common.js
        } = common();
        const rootSubmenuKeys = [
            "dashboard",
            "leads",
            "users",
            "salesmans",
            "forms",
            "formsUCB",
            "form_field_names",
            "settings",
            "subscription",
        ];
        const store = useStore();
        const route = useRoute();

        const innerWidth = window.innerWidth;
        const openKeys = ref([]);
        const selectedKeys = ref([]);
        const mode = ref("inline");

        onMounted(() => {
            var menuKey =
                typeof route.meta.menuKey == "function"
                    ? route.meta.menuKey(route)
                    : route.meta.menuKey;

            if (route.meta.menuParent == "settings") {
                menuKey = "settings";
            }

            if (route.meta.menuParent == "subscription") {
                menuKey = "subscription";
            }

            if (innerWidth <= 991) {
                openKeys.value = [];
            } else {
                openKeys.value = menuCollapsed.value ? [] : [route.meta.menuParent];
            }

            selectedKeys.value = [menuKey.replace("-", "_")];
        });

        const logout = () => {
            store.dispatch("auth/updateGlobalSetting");
            store.dispatch("auth/logout");
        };

        const menuSelected = () => {
            if (innerWidth <= 991) {
                store.commit("auth/updateMenuCollapsed", true);
            }
        };

        const onOpenChange = (currentOpenKeys) => {
            const latestOpenKey = currentOpenKeys.find(
                (key) => openKeys.value.indexOf(key) === -1
            );

            if (rootSubmenuKeys.indexOf(latestOpenKey) === -1) {
                openKeys.value = currentOpenKeys;
            } else {
                openKeys.value = latestOpenKey ? [latestOpenKey] : [];
            }
        };

        watch(route, (newVal, oldVal) => {
            const menuKey =
                typeof newVal.meta.menuKey == "function"
                    ? newVal.meta.menuKey(newVal)
                    : newVal.meta.menuKey;

            if (innerWidth <= 991) {
                openKeys.value = [];
            } else {
                openKeys.value = [newVal.meta.menuParent];
            }

            if (newVal.meta.menuParent == "settings") {
                selectedKeys.value = ["settings"];
            } else if (newVal.meta.menuParent == "subscription") {
                selectedKeys.value = ["subscription"];
            } else {
                selectedKeys.value = [menuKey.replace("-", "_")];
            }
        });

        watch(
            () => menuCollapsed.value,
            (newVal, oldVal) => {
                const menuKey =
                    typeof route.meta.menuKey == "function"
                        ? route.meta.menuKey(route)
                        : route.meta.menuKey;

                if (innerWidth <= 991 && menuCollapsed.value) {
                    openKeys.value = [];
                } else {
                    openKeys.value = menuCollapsed.value ? [] : [route.meta.menuParent];
                }

                if (route.meta.menuParent == "settings") {
                    selectedKeys.value = ["settings"];
                } else if (route.meta.menuParent == "subscription") {
                    selectedKeys.value = ["subscription"];
                } else {
                    selectedKeys.value = [menuKey.replace("-", "_")];
                }
            }
        );

        return {
            mode,
            selectedKeys,
            openKeys,
            logout,
            innerWidth: window.innerWidth,

            onOpenChange,
            menuSelected,
            menuCollapsed,
            appSetting,
            appType,
            themeMode,
            user,
            permsArray,
            appModules,
            willSubscriptionModuleVisible,
            formFolders, // Se retorna la variable para poderla utilizar en la plantilla VUE
        };
    },
});
</script>

<style lang="less">
.main-sidebar .ps {
    height: calc(100vh - 62px);
}

@media only screen and (max-width: 1150px) {
    .ant-layout-sider.ant-layout-sider-collapsed {
        left: -80px !important;
    }
}
</style>
