<template>
    <a-row :gutter="16">

        <!-- BASES DE CLIENTES -->
        <a-col :xs="24" :sm="24" :md="12" :lg="12">
            <a-col :xs="24" :sm="24" :md="24" :lg="24" class="text-center">
                <h2>{{ $t("cobranzas.customer_bases") }}</h2>
            </a-col>

            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                <Upload :folder="cob_clientes" url="cobranzas/clientes/import" :type="$t('cobranzas.customer_bases')" @callback="setUrlDataCustomers" />
            </a-col>

            <a-col :xs="24" :sm="24" :md="24" :lg="24" class="text-right">
                <a-button :href="config.customer_bases_sample_file" type="link"><DownloadOutlined /> {{ $t('messages.click_here_to_download_sample_file') }}</a-button>
            </a-col>

            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                <div class="table-responsive mt-20">
                    <a-table
                        :columns="columns"
                        :row-key="(record) => record.xid"
                        :data-source="crudVariablesCustomers.table.data"
                        :pagination="crudVariablesCustomers.table.pagination"
                        :loading="crudVariablesCustomers.table.loading"
                        @change="crudVariablesCustomers.handleTableChange"
                        bordered
                        size="small"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.key === 'user'">
                                <user-info :user="record?.user" />
                            </template>

                            <template v-if="column.key === 'fecha_subida'">
                                {{ formatDateTime(record.fecha_subida ?? record.created_at ?? null) }}
                            </template>

                            <template v-if="column.key === 'action'">
                                <a-tooltip :title="$t('common.download')">
                                    <a-button type="primary" @click=""
                                        style="margin-left: 4px">
                                        <template #icon>
                                            <DownloadOutlined />
                                        </template>
                                    </a-button>
                                </a-tooltip>
                                <a-tooltip :title="$t('common.delete')">
                                    <a-button type="primary" danger @click="showSelectedDeleteConfirm(record.xid)"
                                        style="margin-left: 4px">
                                        <template #icon>
                                            <DeleteOutlined />
                                        </template>
                                    </a-button>
                                </a-tooltip>
                            </template>

                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-col>

        <!-- BASES DE CARTERAS -->
        <a-col :xs="24" :sm="24" :md="12" :lg="12">
            <a-col :xs="24" :sm="24" :md="24" :lg="24" class="text-center">
                <h2>{{ $t("cobranzas.wallet_bases") }}</h2>
            </a-col>

            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                <Upload :folder="cob_carteras" url="cobranzas/carteras/import" :type="$t('cobranzas.wallet_bases')" @callback="setUrlDataWallet" />
            </a-col>

            <a-col :xs="24" :sm="24" :md="24" :lg="24" class="text-right">
                <a-button :href="config.wallet_bases_sample_file" type="link"><DownloadOutlined /> {{ $t('messages.click_here_to_download_sample_file') }}</a-button>
            </a-col>

            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                <div class="table-responsive mt-20">
                    <a-table
                        :columns="columns"
                        :row-key="(record) => record.xid"
                        :data-source="crudVariablesWallet.table.data"
                        :pagination="crudVariablesWallet.table.pagination"
                        :loading="crudVariablesWallet.table.loading"
                        @change="crudVariablesWallet.handleTableChange"
                        bordered
                        size="small"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.key === 'user'">
                                <user-info :user="record?.user" />
                            </template>

                            <template v-if="column.key === 'fecha_subida'">
                                {{ formatDateTime(record.fecha_subida ?? record.created_at ?? null) }}
                            </template>

                            <template v-if="column.key === 'action'">
                                <a-tooltip :title="$t('common.download')">
                                    <a-button type="primary" @click=""
                                        style="margin-left: 4px">
                                        <template #icon>
                                            <DownloadOutlined />
                                        </template>
                                    </a-button>
                                </a-tooltip>
                                <a-tooltip :title="$t('common.delete')">
                                    <a-button type="primary" danger @click="showSelectedDeleteConfirm(record.xid)"
                                        style="margin-left: 4px">
                                        <template #icon>
                                            <DeleteOutlined />
                                        </template>
                                    </a-button>
                                </a-tooltip>
                            </template>

                        </template>
                    </a-table>
                </div>
            </a-col>

        </a-col>
        
        <!-- BASES DE PAGOS -->
        <a-col :xs="24" :sm="24" :md="12" :lg="12" class="mt-20">
            <a-col :xs="24" :sm="24" :md="24" :lg="24" class="text-center">
                <h2>{{ $t("cobranzas.payment_bases") }}</h2>
            </a-col>

            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                <Upload :folder="cob_pagos" url="cobranzas/pagos/import" :type="$t('cobranzas.payment_bases')" @callback="setUrlDataPayment"/>
            </a-col>

            <a-col :xs="24" :sm="24" :md="24" :lg="24" class="text-right">
                <a-button :href="config.payment_bases_sample_file" type="link"><DownloadOutlined /> {{ $t('messages.click_here_to_download_sample_file') }}</a-button>
            </a-col>

            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                <div class="table-responsive mt-20">
                    <a-table
                        :columns="columns"
                        :row-key="(record) => record.xid"
                        :data-source="crudVariablesPayment.table.data"
                        :pagination="crudVariablesPayment.table.pagination"
                        :loading="crudVariablesPayment.table.loading"
                        @change="crudVariablesPayment.handleTableChange"
                        bordered
                        size="small"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.key === 'user'">
                                <user-info :user="record?.user" />
                            </template>

                            <template v-if="column.key === 'fecha_subida'">
                                {{ formatDateTime(record.fecha_subida ?? record.created_at ?? null) }}
                            </template>

                            <template v-if="column.key === 'action'">
                                <a-tooltip :title="$t('common.download')">
                                    <a-button type="primary" @click=""
                                        style="margin-left: 4px">
                                        <template #icon>
                                            <DownloadOutlined />
                                        </template>
                                    </a-button>
                                </a-tooltip>
                                <a-tooltip :title="$t('common.delete')">
                                    <a-button type="primary" danger @click="showSelectedDeleteConfirm(record.xid)"
                                        style="margin-left: 4px">
                                        <template #icon>
                                            <DeleteOutlined />
                                        </template>
                                    </a-button>
                                </a-tooltip>
                            </template>

                        </template>
                    </a-table>
                </div>
            </a-col>

        </a-col>
    </a-row>
</template>
<script>
import { onMounted } from "vue";
import fields from "./fields.js";
import {
    DeleteOutlined,
    DownloadOutlined,
} from "@ant-design/icons-vue";
import Upload from "../../../../components/upload.vue";
import crud from "../../../../../../../../common/composable/crud";
import common from "../../../../../../../../common/composable/common";
import UserInfo from "../../../../../../../../common/components/user/UserInfo.vue";

export default {
    components: {
        //// NUEVO ////
        Upload,
        DeleteOutlined,
        DownloadOutlined,
        UserInfo,
        ///////////////
    },
    setup() {
        const { formatDateTime} = common();
        const crudVariablesCustomers = crud();
        const crudVariablesPayment = crud();
        const crudVariablesWallet = crud();

        const { 
            columns,
            initData,
            addEditUrlCustomers,
            urlCustomers,
            filterableColumnsCustomers,
            hashableColumns,
            addEditUrlCarteras,
            urlCarteras,
            filterableColumnsCarteras,
            addEditUrlPagos,
            urlPagos,
            filterableColumnsPagos,
        } = fields();
        const config = typeof window !== "undefined" ? window.config : null;

        const setUrlDataCustomers = () => {
            crudVariablesCustomers.tableUrl.value = {
                url: urlCustomers,
            };
            crudVariablesCustomers.table.filterableColumns = filterableColumnsCustomers;

            crudVariablesCustomers.fetch({
                page: 1,
            });

            crudVariablesCustomers.table.pagination.pageSize = 5

            crudVariablesCustomers.crudUrl.value = addEditUrlCustomers;
            crudVariablesCustomers.langKey.value = "common";
            crudVariablesCustomers.initData.value = { ...initData };
            crudVariablesCustomers.formData.value = { ...initData };
            crudVariablesCustomers.hashableColumns.value = [...hashableColumns];
        };

        const setUrlDataWallet = () => {
            crudVariablesWallet.tableUrl.value = {
                url: urlCarteras,
            };
            crudVariablesWallet.table.filterableColumns = filterableColumnsCarteras;
            crudVariablesWallet.fetch({
                page: 1,
            });

            crudVariablesWallet.table.pagination.pageSize = 5

            crudVariablesWallet.crudUrl.value = addEditUrlCarteras;
            crudVariablesWallet.langKey.value = "common";
            crudVariablesWallet.initData.value = { ...initData };
            crudVariablesWallet.formData.value = { ...initData };
            crudVariablesWallet.hashableColumns.value = [...hashableColumns];
        };

        const setUrlDataPayment = () => {
            crudVariablesPayment.tableUrl.value = {
                url: urlPagos,
            };
            crudVariablesPayment.table.filterableColumns = filterableColumnsPagos;
            crudVariablesPayment.fetch({
                page: 1,
            });

            crudVariablesPayment.table.pagination.pageSize = 5

            crudVariablesPayment.crudUrl.value = addEditUrlPagos;
            crudVariablesPayment.langKey.value = "common";
            crudVariablesPayment.initData.value = { ...initData };
            crudVariablesPayment.formData.value = { ...initData };
            crudVariablesPayment.hashableColumns.value = [...hashableColumns];
        };

        onMounted(() => {
            setUrlDataCustomers();
            setUrlDataWallet();
            setUrlDataPayment();
        });

        return {
            // En uso
            config,
            columns,
            crudVariablesCustomers,
            crudVariablesWallet,
            crudVariablesPayment,
            formatDateTime,
            setUrlDataCustomers,
            setUrlDataWallet,
            setUrlDataPayment,
            /////////////
        };
    },
};
</script>