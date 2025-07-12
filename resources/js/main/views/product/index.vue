<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.products`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.products`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="10">
                <a-space>
                    <template
                        v-if="
                            permsArray.includes('products_create') ||
                            permsArray.includes('admin')
                        "
                    >
                        <a-button type="primary" @click="addItem">
                            <PlusOutlined />
                            {{ $t("product.add") }}
                        </a-button>
                        <ImportProducts
                            :pageTitle="$t('product.import_products')"
                            :camposRequerido="$t('bases.required_fields')"
                            :sampleFileUrl="sampleFileUrl"
                            importUrl="products/import"
                            @onUploadSuccess="setUrlData"
                        />
                    </template>
                    <a-button
                        v-if="
                            table.selectedRowKeys.length > 0 &&
                            (permsArray.includes('products_delete') ||
                                permsArray.includes('admin'))
                        "
                        type="primary"
                        @click="showSelectedDeleteConfirm"
                        danger
                    >
                        <template #icon><DeleteOutlined /></template>
                        {{ $t("common.delete") }}
                    </a-button>
                </a-space>
            </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="14">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="8">
                        <a-input-group compact>
                            <a-select
                                style="width: 35%"
                                v-model:value="table.searchColumn"
                                :placeholder="
                                    $t('common.select_default_text', [''])
                                "
                            >
                                <a-select-option
                                    v-for="filterableColumn in filterableColumns"
                                    :key="filterableColumn.key"
                                >
                                    {{ filterableColumn.value }}
                                </a-select-option>
                            </a-select>
                            <a-input-search
                                style="width: 65%"
                                v-model:value="table.searchString"
                                show-search
                                @change="onTableSearch"
                                @search="onTableSearch"
                                :loading="table.filterLoading"
                            />
                        </a-input-group>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="6">
                        <a-select
                            v-model:value="filters.category_id"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('product.category'),
                                ])
                            "
                            :allowClear="true"
                            style="width: 100%"
                            optionFilterProp="title"
                            show-search
                            @change="setUrlData"
                        >
                            <a-select-option
                                v-for="category in categories"
                                :key="category.xid"
                                :title="category.name"
                                :value="category.xid"
                            >
                                {{ category.name }}
                            </a-select-option>
                        </a-select>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="8" :lg="8" :xl="6">
                        <a-select
                            v-model:value="filters.campaign_id"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('product.campaign'),
                                ])
                            "
                            :allowClear="true"
                            style="width: 100%"
                            optionFilterProp="title"
                            show-search
                            @change="setUrlData"
                        >
                            <a-select-option
                                v-for="campaign in allCampaigns"
                                :key="campaign.xid"
                                :title="campaign.name"
                                :value="campaign.xid"
                            >
                                {{ campaign.name }}
                            </a-select-option>
                        </a-select>
                    </a-col>
                </a-row>
            </a-col>
        </a-row>
    </admin-page-filters>

    <admin-page-table-content>
        <AddEdit
            :addEditType="addEditType"
            :visible="addEditVisible"
            :url="addEditUrl"
            @addEditSuccess="addEditSuccess"
            @closed="onCloseAddEdit"
            :formData="formData"
            :campaigns="allCampaigns"
            :data="viewData"
            :pageTitle="pageTitle"
            :successMessage="successMessage"
            @categoryReload="setUrlData"
        />

        <a-row>
            <a-col :span="24">
                <div class="table-responsive">
                    <a-table
                        :row-selection="{
                            selectedRowKeys: table.selectedRowKeys,
                            onChange: onRowSelectChange,
                            getCheckboxProps: (record) => ({
                                disabled: false,
                                name: record.xid,
                            }),
                        }"
                        :columns="columns"
                        :row-key="(record) => record.xid"
                        :data-source="table.data"
                        :pagination="table.pagination"
                        :loading="table.loading"
                        @change="handleTableChange"
                        bordered
                        size="middle"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'image'">
                                <a-image :width="32" :src="record.image_url" />
                            </template>
                            <template v-if="column.dataIndex === 'category_id'">
                                {{ record.categories?.name }}
                            </template>
                            <template v-if="column.dataIndex === 'campaign_id'">
                                {{ record.campaigns?.name }}
                            </template>
                            <template
                                v-if="column.dataIndex === 'product_type'"
                            >
                                {{
                                    record.product_type == "product"
                                        ? $t("common.product")
                                        : $t("common.service")
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'price'">
                                {{ formatAmountCurrency(record.price) }}
                            </template>
                            <template v-if="column.dataIndex === 'status'">
                                <a-tag
                                    color="success"
                                    v-if="record.status == 1"
                                >
                                    {{ $t("common.active") }}
                                </a-tag>
                                <a-tag
                                    color="error"
                                    v-if="record.status == 0"
                                >
                                    {{ $t("common.inactive") }}
                                </a-tag>
                            </template>
                            <template v-if="column.dataIndex === 'action'">
                                <a-button
                                    v-if="
                                        permsArray.includes('products_edit') ||
                                        permsArray.includes('admin')
                                    "
                                    type="primary"
                                    @click="editItem(record)"
                                    style="margin-left: 4px"
                                >
                                    <template #icon><EditOutlined /></template>
                                </a-button>
                                <a-button
                                    v-if="
                                        (permsArray.includes(
                                            'products_delete'
                                        ) ||
                                            permsArray.includes('admin')) &&
                                        (!record.children ||
                                            record.children.length == 0)
                                    "
                                    type="primary"
                                    danger
                                    @click="showDeleteConfirm(record.xid)"
                                    style="margin-left: 4px"
                                >
                                    <template #icon
                                        ><DeleteOutlined
                                    /></template>
                                </a-button>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>
</template>

<script>
import { onMounted, ref } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
} from "@ant-design/icons-vue";
import crud from "../../../common/composable/crud";
import common from "../../../common/composable/common";
import AdminPageHeader from "../../../common/layouts/AdminPageHeader.vue";
import fields from "./fields";
import AddEdit from "./AddEdit.vue";
import { useI18n } from "vue-i18n";
import ImportProducts from "../../../common/core/ui/Import.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        AddEdit,
        AdminPageHeader,
        ImportProducts,
    },
    setup() {
        const { permsArray, appSetting, formatAmountCurrency } = common();
        const {
            url,
            addEditUrl,
            initData,
            columns,
            filterableColumns,
            hashableColumns,
            getPrefetchData,
            allCampaigns,
        } = fields();

        const { t } = useI18n();
        const crudVariables = crud();
        const sampleFileUrl = window.config.product_sample_file;
        const categories = ref([]);
        const filters = ref({
            category_id: undefined,
            campaign_id: undefined,
            status: 0,
        });

        onMounted(() => {
            setUrlData();
            getPrefetchData();
        });

        const setUrlData = () => {
            const categoriesPromise = axiosAdmin.get("categories?limit=10000");

            Promise.all([categoriesPromise]).then(([categoriesResponse]) => {
                categories.value = categoriesResponse.data;
            });

            crudVariables.tableUrl.value = {
                url,
                filters,
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "product";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
            crudVariables.hashableColumns.value = [...hashableColumns];
        };

        return {
            permsArray,
            appSetting,
            formatAmountCurrency,
            columns,
            ...crudVariables,
            filterableColumns,
            setUrlData,

            filters,
            allCampaigns,
            categories,
            sampleFileUrl,
        };
    },
};
</script>
