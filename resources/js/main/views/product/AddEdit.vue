<template>
    <a-drawer
        :title="pageTitle"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
        :maskClosable="false"
        @close="onClose"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="6" :lg="6">
                    <a-form-item
                        :label="$t('product.image')"
                        name="image"
                        :help="rules.image ? rules.image.message : null"
                        :validateStatus="rules.image ? 'error' : null"
                    >
                        <Upload
                            :formData="formData"
                            folder="product"
                            imageField="image"
                            @onFileUploaded="
                                (file) => {
                                    formData.image = file.file;
                                    formData.image_url = file.file_url;
                                }
                            "
                        />
                    </a-form-item>
                    <a-form-item
                            :label="$t('common.status')"
                            name="status"
                            :help="
                                rules.status ? rules.status.message : null
                            "
                            :validateStatus="rules.status ? 'error' : null"
                            class="required"
                            style="margin-top: 20%;"
                        >
                            <a-select
                                v-model:value="formData.status"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('user.status'),
                                    ])
                                "
                                :allowClear="true"
                            >
                                <a-select-option :value="1">{{ $t('common.enabled') }}</a-select-option>
                                <a-select-option :value="0">{{ $t('common.disabled') }}</a-select-option>
                            </a-select>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="18" :lg="18">
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('product.product_type')"
                                name="product_type"
                                :help="
                                    rules.product_type
                                        ? rules.product_type.message
                                        : null
                                "
                                :validateStatus="
                                    rules.product_type ? 'error' : null
                                "
                                class="required"
                            >
                                <a-select
                                    v-model:value="formData.product_type"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('product.product_type'),
                                        ])
                                    "
                                    :allowClear="true"
                                    show-search
                                >
                                    <a-select-option
                                        key="products"
                                        value="product"
                                    >
                                        {{ $t("common.product") }}
                                    </a-select-option>
                                    <a-select-option
                                        key="service"
                                        value="service"
                                    >
                                        {{ $t("common.service") }}
                                    </a-select-option>
                                </a-select>
                            </a-form-item>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('product.campaign')"
                                name="campaign_id"
                                :help="
                                    rules.campaign_id
                                        ? rules.campaign_id.message
                                        : null
                                "
                                :validateStatus="
                                    rules.campaign_id ? 'error' : null
                                "
                                class="required"
                            >
                                <a-select
                                    v-model:value="formData.campaign_id"
                                    :placeholder="
                                        $t('common.select_default_text', [
                                            $t('product.campaign'),
                                        ])
                                    "
                                    :allowClear="true"
                                    optionFilterProp="label"
                                    show-search
                                >
                                    <a-select-option
                                        v-for="campaign in campaigns"
                                        :key="campaign.xid"
                                        :value="campaign.xid"
                                        :label="campaign.name"
                                    >
                                        {{ campaign.name }}
                                    </a-select-option>
                                </a-select>
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('product.name')"
                                name="name"
                                :help="rules.name ? rules.name.message : null"
                                :validateStatus="rules.name ? 'error' : null"
                                class="required"
                            >
                                <a-input
                                    v-model:value="formData.name"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('product.name'),
                                        ])
                                    "
                                />
                            </a-form-item>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="12" :lg="12">
                            <a-form-item
                                :label="$t('product.price')"
                                name="price"
                                :help="rules.price ? rules.price.message : null"
                                :validateStatus="rules.price ? 'error' : null"
                                class="required"
                            >
                                <a-input-number
                                    v-model:value="formData.price"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('product.price'),
                                        ])
                                    "
                                    min="0"
                                    style="width: 100%"
                                >
                                    <template #addonBefore>
                                        {{ appSetting.currency.symbol }}
                                    </template>
                                </a-input-number>
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row :gutter="16">
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item
                                :label="$t('product.coverage')"
                                name="coverage"
                                :help="rules.coverage ? rules.coverage.message : null"
                                :validateStatus="rules.coverage ? 'error' : null"
                                class="required"
                            >
                                <a-input
                                    v-model:value="formData.coverage"
                                    :placeholder="
                                        $t('common.placeholder_default_text', [
                                            $t('product.coverage'),
                                        ])
                                    "
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('product.tax_label')"
                        name="tax_label"
                        :help="rules.tax_label ? rules.tax_label.message : null"
                        :validateStatus="rules.tax_label ? 'error' : null"
                    >
                        <a-input
                            v-model:value="formData.tax_label"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('product.tax_label'),
                                ])
                            "
                        >
                        </a-input>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('product.tax_rate')"
                        name="tax_rate"
                        :help="rules.tax_rate ? rules.tax_rate.message : null"
                        :validateStatus="rules.tax_rate ? 'error' : null"
                    >
                        <a-input-number
                            v-model:value="formData.tax_rate"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('product.tax_rate'),
                                ])
                            "
                            min="0"
                            style="width: 100%"
                        >
                        </a-input-number>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('product.internal_code')"
                        name="internal_code"
                        :help="
                            rules.internal_code
                                ? rules.internal_code.message
                                : null
                        "
                        :validateStatus="rules.internal_code ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.internal_code"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('product.internal_code'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('product.category')"
                        name="category_id"
                        :help="
                            rules.category_id ? rules.category_id.message : null
                        "
                        :validateStatus="rules.category_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.category_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('product.category'),
                                    ])
                                "
                                :allowClear="true"
                                optionFilterProp="label"
                                show-search
                            >
                                <a-select-option
                                    v-for="category in categories"
                                    :key="category.xid"
                                    :value="category.xid"
                                    :label="category.name"
                                >
                                    {{ category.name }}
                                </a-select-option>
                            </a-select>
                            <CategoryAddButton @onAddSuccess="categoryAdded" />
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('product.description')"
                        name="description"
                        :help="
                            rules.description ? rules.description.message : null
                        "
                        :validateStatus="rules.description ? 'error' : null"
                    >
                        <a-textarea
                            v-model:value="formData.description"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('product.description'),
                                ])
                            "
                            :auto-size="{ minRows: 2, maxRows: 3 }"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-checkbox v-model:checked="formData.digitar_precio">{{ $t('product.allows_digitize_price') }}</a-checkbox>
                </a-col>
            </a-row>
        </a-form>
        <template #footer>
            <a-space>
                <a-button
                    key="submit"
                    type="primary"
                    :loading="loading"
                    @click="onSubmit"
                >
                    <template #icon>
                        <SaveOutlined />
                    </template>
                    {{
                        addEditType == "add"
                            ? $t("common.create")
                            : $t("common.update")
                    }}
                </a-button>
                <a-button key="back" @click="onClose">
                    {{ $t("common.cancel") }}
                </a-button>
            </a-space>
        </template>
    </a-drawer>
</template>
<script>
import { defineComponent, ref, onMounted, watch } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
} from "@ant-design/icons-vue";
import Upload from "../../../common/core/ui/file/Upload.vue";
import common from "../../../common/composable/common";
import apiAdmin from "../../../common/composable/apiAdmin";
import CategoryAddButton from "../categories/AddButton.vue";
export default defineComponent({
    props: [
        "formData",
        "campaigns",
        "data",
        "visible",
        "url",
        "addEditType",
        "pageTitle",
        "successMessage",
    ],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
        Upload,
        CategoryAddButton,
    },
    setup(props, { emit }) {
        const { appSetting } = common();
        const categories = ref([]);
        const categoryUrl = "categories?limit=10000";
        const { addEditRequestAdmin, loading, rules } = apiAdmin();

        onMounted(() => {
            fetchCategoryData();
        });
        const fetchCategoryData = () => {
            const categoriesPromise = axiosAdmin.get(categoryUrl);

            Promise.all([categoriesPromise]).then(([categoriesResponse]) => {
                categories.value = categoriesResponse.data;
            });
        };

        const categoryAdded = () => {
            axiosAdmin.get(categoryUrl).then((response) => {
                categories.value = response.data;
                emit("categoryReload");
            });
        };

        const onSubmit = () => {
            addEditRequestAdmin({
                url: props.url,
                data: props.formData,
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                },
            });
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                if (props.addEditType == "edit") {
                    fetchCategoryData();
                }
            }
        );
        return {
            appSetting,
            loading,
            rules,
            onClose,
            onSubmit,
            categories,
            categoryAdded,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
