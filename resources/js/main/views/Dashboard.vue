<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.dashboard`)" style="padding: 0px" />
        </template>
    </AdminPageHeader>
    <div class="dashboard-page-content-container">
        <a-row :gutter="[8, 8]" class="mt-30 mb-10">
            <a-col :xs="24" :sm="24" :md="12" :lg="6" :xl="6">
                <DateRangePicker
                    ref="serachDateRangePicker"
                    @dateTimeChanged="
                        (changedDateTime) => (filters.dates = changedDateTime)
                    "
                />
            </a-col>
        </a-row>

        <a-row :gutter="[18, 18]" class="mt-30 mb-20">
            <!-- Campañas activas gestionadas -->   
            <a-col :xs="24" :sm="24" :md="24" :lg="24" :xl="12">
                <a-card :title="$t('dashboard.active_actioned_campaigns')" style="height: 100%; display: flex; flex-direction: column;">
                <div style="flex: 1; display: flex; align-items: center; justify-content: center;">
                    <ActionedCampaigns :data="responseData" style="width: 100%; height: 100%;" />
                </div>
                </a-card>
            </a-col>

            <!-- Productos vendidos -->
            <a-col :xs="24" :sm="24" :md="24" :lg="24" :xl="12">
                <a-card :title="$t('dashboard.products_sold')" style="height: 100%; display: flex; flex-direction: column;">
                <div style="flex: 1; display: flex; align-items: center; justify-content: center;">
                    <topProducts :data="responseData" style="width: 100%; height: 100%;" />
                </div>
                </a-card>
            </a-col>
        </a-row>


        <!-- ventas y monto total por usuario -->   
        <a-row :gutter="[18, 18]" class="mt-30 mb-20">
            <a-col :xs="24" :sm="24" :md="24" :lg="12" :xl="12">
                <a-card :title="$t('dashboard.sales_user')">
                        <graficoVentas :data="responseData.ventasMontos"/>
                </a-card>
            </a-col>
            <a-col :xs="24" :sm="24" :md="24" :lg="12" :xl="12">
                <a-card :title="$t('menu.quality')" style="height: 100%; min-height: 40%; ">
                    <div style="width: 100%; height: 100%;padding-top: 13%;">
                        <Calidad
                            v-if="responseData?.calidad"
                            :totalRellamadas="responseData.calidad.total_rellamada"
                            :promedioNota="responseData.calidad.promedio_nota"
                        />
                    </div>
                </a-card>
            </a-col>
        </a-row>

        <a-divider v-if="permsArray.includes('users_view') || permsArray.includes('admin')" orientation="left">{{ $t('dashboard.general_sales') }}</a-divider>
        
        <!-- Montos Ventas -->
        <a-row v-if="permsArray.includes('users_view') || permsArray.includes('admin')" :gutter="[18, 18]" class="mt-30 mb-20">
            <a-col :xs="24" :sm="24" :md="24" :lg="12" :xl="12">
                <a-card :title="$t('dashboard.total_sales_amount')">
                    <AmountSalesMade :data="responseData" />
                </a-card>
            </a-col>
            <a-col :xs="24" :sm="24" :md="24" :lg="12" :xl="12">
                <a-card :title="$t('dashboard.sales_made')">
                    <SalesMade :data="responseData" />
                </a-card>
            </a-col>
        </a-row>

        <a-divider v-if="permsArray.includes('users_view') || permsArray.includes('admin')" orientation="left">{{ $t('dashboard.sales_with_quality') }} ({{ $t('common.certified') + '-' + $t('common.certified_recall') }})</a-divider>

        <a-row v-if="permsArray.includes('users_view') || permsArray.includes('admin')" :gutter="[18, 18]" class="mt-30 mb-20">
            <a-col :xs="24" :sm="24" :md="24" :lg="12" :xl="12">
                <a-card :title="$t('dashboard.total_sales_amount')">
                    <AmountSalesMadeCalidad :data="responseData" />
                </a-card>
            </a-col>
            <a-col :xs="24" :sm="24" :md="24" :lg="12" :xl="12">
                <a-card :title="$t('dashboard.sales_made')">
                    <SalesMadeCalidad :data="responseData" />
                </a-card>
            </a-col>
        </a-row>

        <!-- Ventas -->
        <!-- <a-row v-if="permsArray.includes('users_view') || permsArray.includes('admin')" :gutter="[18, 18]" class="mt-30 mb-20">
            <a-col :xs="24" :sm="24" :md="24" :lg="12" :xl="12">
                <a-card :title="$t('dashboard.sales_made')">
                    <SalesMade :data="responseData" />
                </a-card>
            </a-col>
        </a-row> -->

    </div>
</template>

<script>
import { onMounted, reactive, ref, watch } from "vue";
import {
    CopyrightCircleOutlined,
    MobileOutlined,
    ScheduleOutlined,
    ClockCircleOutlined,
    DoubleRightOutlined,
} from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import common from "../../common/composable/common";
import AdminPageHeader from "../../common/layouts/AdminPageHeader.vue";
import StateWidget from "../../common/components/common/card/StateWidget.vue";
import ActionedCampaigns from "../components/charts/dashboard/ActionedCampaigns.vue";
import graficoVentas from "../components/charts/dashboard/ventasCantidadMonto.vue";
import topProducts from "../components/charts/dashboard/TopProducts.vue";
import Calidad from "../components/charts/dashboard/Calidad.vue";
import CallMade from "../components/charts/dashboard/CallMade.vue";
import SalesMade from "../components/charts/dashboard/SalesMade.vue";
import SalesMadeCalidad from "../components/charts/dashboard/SalesMadeCalidad.vue";
import AmountSalesMade from "../components/charts/dashboard/AmountSalesMade.vue";
import AmountSalesMadeCalidad from "../components/charts/dashboard/AmountSalesMadeCalidad.vue";
import DateRangePicker from "../../common/components/common/calendar/DateRangePicker.vue";

export default {
    components: {
        AdminPageHeader,
        StateWidget,
        ActionedCampaigns,
        graficoVentas,
        CallMade,
        SalesMade,
        SalesMadeCalidad,
        AmountSalesMade,
        AmountSalesMadeCalidad,
        topProducts,
        Calidad,
        DateRangePicker,

        MobileOutlined,
        CopyrightCircleOutlined,
        ClockCircleOutlined,
        ScheduleOutlined,
        DoubleRightOutlined,
    },
    setup() {
        const { formatTimeDuration,permsArray } = common();
        const { t } = useI18n();
        const responseData = ref([]);
        const filters = reactive({
            dates: [],
            usuarioPermitido: permsArray.value.includes('leads_view_all') || permsArray.value.includes('admin') ? true : false,
        });

        onMounted(() => {
            getInitData();
        });

        const getInitData = () => {
            const dashboardPromise = axiosAdmin.post("dashboard", filters);

            Promise.all([dashboardPromise]).then(([dashboardResponse]) => {
                responseData.value = dashboardResponse.data;
            });
        };

        watch([filters], (newVal, oldVal) => {
            getInitData();
        });

        return {
            formatTimeDuration,
            filters,
            responseData,
            permsArray,
        };
    },
};
</script>

<style lang="less">
.ant-card-extra,
.ant-card-head-title {
    padding: 0px;
}

.ant-card-head-title {
    margin-top: 10px;
}
</style>
