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
                <a-card :title="$t('dashboard.active_actioned_campaigns')">
                    <ActionedCampaigns :data="responseData" />
                </a-card>
            </a-col>
            <a-col style="height: 100%;" :xs="24" :sm="24" :md="24" :lg="24" :xl="12">
                <a-card :title="$t('dashboard.products_sold')">
                    <topProducts :data="responseData" />
                </a-card>
            </a-col>
            
        </a-row>

        <div class="mt-30 mb-20">
            <a-row :gutter="[15, 15]">
                <!-- campañas activas -->
                <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="8">
                    <StateWidget bgColor="#08979c">
                        <template #image>
                            <CopyrightCircleOutlined
                                style="color: #fff; font-size: 24px"
                            />
                        </template>
                        <template #description>
                            <h2 v-if="responseData.stateData">
                                {{ responseData.stateData.campaign_count }}
                            </h2>
                            <p>{{ $t("dashboard.active_campaigns") }}</p>
                        </template>
                    </StateWidget>
                </a-col>
                <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="8">
                    <StateWidget bgColor="#d46b08">
                        <template #image>
                            <MobileOutlined
                                style="color: #fff; font-size: 24px"
                            />
                        </template>
                        <template #description>
                            <h2 v-if="responseData.stateData">
                                {{ responseData.stateData.lead_count }}
                            </h2>
                            <p>{{ $t("dashboard.call_made") }}</p>
                        </template>
                    </StateWidget>
                </a-col>
                <!-- total de duracion -->
                <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="8">
                    <StateWidget bgColor="#ffa39e">
                        <template #image>
                            <ClockCircleOutlined
                                style="color: #fff; font-size: 24px"
                            />
                        </template>
                        <template #description>
                            <h2 v-if="responseData.stateData">
                                <small>
                                    {{
                                        formatTimeDuration(
                                            responseData.stateData.total_times
                                        )
                                    }}
                                </small>
                            </h2>
                            <p>{{ $t("dashboard.total_duration") }}</p>
                        </template>
                    </StateWidget>
                </a-col>
            </a-row>
        </div>

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

        <!-- Montos Ventas -->
        <a-row v-if="permsArray.includes('users_view') || permsArray.includes('admin')" :gutter="[18, 18]" class="mt-30 mb-20">
            <a-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                <a-card :title="$t('dashboard.total_sales_amount')">
                    <AmountSalesMade :data="responseData" />
                </a-card>
            </a-col>
        </a-row>

        <!-- Ventas -->
        <a-row v-if="permsArray.includes('users_view') || permsArray.includes('admin')" :gutter="[18, 18]" class="mt-30 mb-20">
            <a-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                <a-card :title="$t('dashboard.sales_made')">
                    <SalesMade :data="responseData" />
                </a-card>
            </a-col>
        </a-row>

        <!-- Contactos o llamadas realizadas -->
        <!-- <a-row v-if="permsArray.includes('users_view') || permsArray.includes('admin')" :gutter="[18, 18]" class="mt-30 mb-20">
            <a-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                <a-card :title="$t('dashboard.call_made')">
                    <CallMade :data="responseData" />
                    <template #extra>
                        <a-button
                            class="mt-10"
                            type="link"
                            @click="
                                $router.push({
                                    name: 'admin.leads.index',
                                })
                            "
                        >
                            {{ $t("common.view_all") }}
                            <DoubleRightOutlined />
                        </a-button>
                    </template>
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
import AmountSalesMade from "../components/charts/dashboard/AmountSalesMade.vue";
import DateRangePicker from "../../common/components/common/calendar/DateRangePicker.vue";

export default {
    components: {
        AdminPageHeader,
        StateWidget,
        ActionedCampaigns,
        graficoVentas,
        CallMade,
        SalesMade,
        AmountSalesMade,
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
