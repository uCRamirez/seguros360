<template>
    <a-row :gutter="16">

        <!-- BASES DE CLIENTES -->
        <a-col :xs="24" :sm="24" :md="12" :lg="12">
            <a-col :xs="24" :sm="24" :md="24" :lg="24" class="text-center">
                <h2>{{ $t("cobranzas.customer_bases") }}</h2>
            </a-col>

            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                <Upload :folder="cob_clientes" url="cobranzas/clientes/import" :type="$t('cobranzas.customer_bases')" />
            </a-col>

            <a-col :xs="24" :sm="24" :md="24" :lg="24" class="text-right">
                <a-button :href="config.customer_bases_sample_file" type="link"><DownloadOutlined /> {{ $t('messages.click_here_to_download_sample_file') }}</a-button>
            </a-col>

            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                <div class="table-responsive mt-20">
                    <a-table size="small" :columns="columns" :data-source="data" :scroll="{ x: 1300, y: 1000 }">
                        <template #bodyCell="{ column }">

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
                <Upload :folder="cob_carteras" url="cobranzas/carteras/import" :type="$t('cobranzas.wallet_bases')" />
            </a-col>

            <a-col :xs="24" :sm="24" :md="24" :lg="24" class="text-right">
                <a-button :href="config.wallet_bases_sample_file" type="link"><DownloadOutlined /> {{ $t('messages.click_here_to_download_sample_file') }}</a-button>
            </a-col>

            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                <div class="table-responsive mt-20">
                    <a-table size="small" :columns="columns" :data-source="data" :scroll="{ x: 1300, y: 1000 }">
                        <template #bodyCell="{ column }">
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
                <Upload :folder="cob_pagos" url="cobranzas/pagos/import" :type="$t('cobranzas.payment_bases')" />
            </a-col>

            <a-col :xs="24" :sm="24" :md="24" :lg="24" class="text-right">
                <a-button :href="config.payment_bases_sample_file" type="link"><DownloadOutlined /> {{ $t('messages.click_here_to_download_sample_file') }}</a-button>
            </a-col>

            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                <div class="table-responsive mt-20">
                    <a-table size="small" :columns="columns" :data-source="data" :scroll="{ x: 1300, y: 1000 }">
                        <template #bodyCell="{ column }">
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
import { watch, onMounted } from "vue";
import fields from "./fields.js";
import {
    DeleteOutlined,
    DownloadOutlined,
} from "@ant-design/icons-vue";
import Upload from "../../../../components/upload.vue";
import crud from "../../../../../../../../common/composable/crud";

export default {
    components: {
        //// NUEVO ////
        Upload,
        DeleteOutlined,
        DownloadOutlined,
        ///////////////
    },
    setup() {
        const crudVariablesCustomers = crud();
        const crudVariablesPayment = crud();
        const crudVariablesWallet = crud();

        const { columns } = fields();
        const config = typeof window !== "undefined" ? window.config : null;

        const setUrlDataCustomers = () => {
            crudVariablesCustomers.tableUrl.value = {
                url: url,
            };
            crudVariablesCustomers.table.filterableColumns = filterableColumns;

            crudVariablesCustomers.fetch({
                page: 1,
            });

            crudVariablesCustomers.crudUrl.value = addEditUrl;
            crudVariablesCustomers.langKey.value = "user";
            crudVariablesCustomers.initData.value = { ...initData };
            crudVariablesCustomers.formData.value = { ...initData };
            crudVariablesCustomers.hashableColumns.value = [...hashableColumns];
        };

        const setUrlDataPayment = () => {
            crudVariablesPayment.tableUrl.value = {
                url: url,
            };
            crudVariablesPayment.table.filterableColumns = filterableColumns;
            crudVariablesPayment.fetch({
                page: 1,
            });

            crudVariablesPayment.crudUrl.value = addEditUrl;
            crudVariablesPayment.langKey.value = "user";
            crudVariablesPayment.initData.value = { ...initData };
            crudVariablesPayment.formData.value = { ...initData };
            crudVariablesPayment.hashableColumns.value = [...hashableColumns];
        };

        const setUrlDataWallet = () => {
            crudVariablesWallet.tableUrl.value = {
                url: url,
            };
            crudVariablesWallet.table.filterableColumns = filterableColumns;
            crudVariablesWallet.fetch({
                page: 1,
            });

            crudVariablesWallet.crudUrl.value = addEditUrl;
            crudVariablesWallet.langKey.value = "user";
            crudVariablesWallet.initData.value = { ...initData };
            crudVariablesWallet.formData.value = { ...initData };
            crudVariablesWallet.hashableColumns.value = [...hashableColumns];
        };

        // onMounted(() => {
        //     setUrlDataCustomers();
        //     setUrlDataPayment();
        //     setUrlDataWallet();
        // });

        return {
            // En uso
            config,
            columns,

            ...crudVariablesCustomers,
            ...crudVariablesPayment,
            ...crudVariablesWallet,

            /////////////
        };
    },
};
</script>