<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.notes_typifications`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.notes_typifications`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <admin-page-filters>
        <a-row :gutter="[16, 16]">
            <a-col :xs="24" :sm="24" :md="12" :lg="10" :xl="16">
                <a-space>
                    <template
                        v-if="
                            permsArray.includes('notes_typifications_add') ||
                            permsArray.includes('admin')
                        "
                    >
                        <a-space>
                            <a-button type="primary" @click="addItem">
                                <PlusOutlined />
                                {{ $t("notes_typification.add") }}
                            </a-button>
                            <a-button type="primary" @click="addMultipleItems">
                                <PlusOutlined />
                                {{ $t("notes_typification.add_multiple_typification") }}
                            </a-button>
                            <ImportNotesTypification
                                :pageTitle="
                                    $t('notes_typification.import_notes_typification')
                                "
                                :sampleFileUrl="sampleFileUrl"
                                importUrl="notes-typifications/import"
                                @onUploadSuccess="getTypifications"
                            />
                        </a-space>
                    </template>
                    <a-button
                        v-if="
                            selectedRowKeys.length > 0 &&
                            (permsArray.includes('notes_typifications_delete') ||
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
            <a-col :xs="24" :sm="24" :md="12" :lg="14" :xl="8">
                <a-row :gutter="[16, 16]" justify="end">
                    <a-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                        <a-input-group compact>
                            <a-input-search
                                style="width: 100%"
                                v-model:value="extraFilters.name"
                                show-search
                                @change="searchByName"
                                @search="searchByName"
                                :allowClear="true"
                                :placeholder="$t('notes_typification.search_your_name')"
                            />
                        </a-input-group>
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
            :data="viewData"
            :destroyOnClose="true"
        />

        <a-row>
            <a-col :span="24">
                <div class="table-responsive">
                    <a-table
                        :row-selection="rowSelection"
                        :columns="columns"
                        :row-key="(record) => record.xid"
                        :data-source="allTypifications"
                        :defaultExpandAllRows="true"
                        bordered
                        size="middle"
                    >
                        <template #bodyCell="{ column, text, record }">
                            <template v-if="column.dataIndex === 'action'">
                                <a-button
                                    v-if="
                                        permsArray.includes('notes_typifications_edit') ||
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
                                            'notes_typifications_delete'
                                        ) ||
                                            permsArray.includes('admin')) &&
                                        (!record.children || record.children.length == 0)
                                    "
                                    type="primary"
                                    danger
                                    @click="showDeleteConfirm(record.xid)"
                                    style="margin-left: 4px"
                                >
                                    <template #icon><DeleteOutlined /></template>
                                </a-button>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
    </admin-page-table-content>
    <AddMultiple
        :visible="visibleAddMultiple"
        @close="closeMultipleItems"
        :pageTitle="$t('notes_typification.add_multiple_typification')"
        @setMultiple="getTypifications()"
    />
</template>
<script>
import { onMounted, ref, createVNode, unref, computed } from "vue";
import fields from "./fields";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    ExclamationCircleOutlined,
} from "@ant-design/icons-vue";
import { Modal, notification } from "ant-design-vue";
import { useStore } from "vuex";
import { useI18n } from "vue-i18n";
import { forEach, cloneDeep, filter, debounce, isString } from "lodash-es";
import common from "../../../common/composable/common";
import AddEdit from "./AddEdit.vue";
import AdminPageHeader from "../../../common/layouts/AdminPageHeader.vue";
import AddMultiple from "./AddMultiple.vue";
import ImportNotesTypification from "../../../common/core/ui/Import.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        ExclamationCircleOutlined,
        AddEdit,
        AdminPageHeader,
        AddMultiple,
        ImportNotesTypification,
    },
    setup() {
        const store = useStore();
        const { initData, columns } = fields();
        const { permsArray } = common();
        const { t } = useI18n();
        const sampleFileUrl = window.config.notes_typification_sample_file;
        const detailsVisible = ref(false);
        const viewData = ref({});

        const addEditVisible = ref(false);
        const addEditType = ref("add");
        const addEditUrl = ref("notes-typifications");
        const allTypifications = ref([]);
        const ogTypifications = ref([]);
        const visibleAddMultiple = ref(false);

        const addMultipleItems = () => {
            visibleAddMultiple.value = true;
        };

        const extraFilters = ref({
            name: "",
        });

        const formData = ref({});

        const selectedRowKeys = ref([]);

        onMounted(() => {
            getTypifications();
        });

        const onRowSelectionChange = (selectedRowKeyValues) => {
            selectedRowKeys.value = selectedRowKeyValues;
        };

        const getCheckboxProps = (record) => {
            return {
                disabled: !record.children || record.children.length == 0 ? false : true,
                name: record.xid,
            };
        };

        const rowSelection = computed(() => {
            return {
                selectedRowKeys: unref(selectedRowKeys),
                onChange: onRowSelectionChange,
                getCheckboxProps: getCheckboxProps,
            };
        });

        const getTypifications = () => {
            const notesTypificationUrl = `notes-typifications?fields=id,xid,name,parent_id,x_parent_id,sale,schedule,status&filters=status eq 1&order=id asc&limit=10000`;

            axiosAdmin.get(notesTypificationUrl).then((response) => {
                const allCategoriesArray = [];
                var listArray = response.data;
                // listArray = sortBy(listArray, "x_parent_id");

                listArray.forEach((node) => {
                    // No parentId means top level
                    if (!node.x_parent_id) return allCategoriesArray.push(node);

                    // Insert node as child of parent in listArray array
                    const parentIndex = listArray.findIndex(
                        (el) => el.xid === node.x_parent_id
                    );

                    if (!listArray[parentIndex].children) {
                        return (listArray[parentIndex].children = [node]);
                    }

                    listArray[parentIndex].children.push(node);
                });

                allTypifications.value = allCategoriesArray;
                ogTypifications.value = allCategoriesArray;
            });
        };

        const searchByNameLike = (data, searchName) => {
            return filter(data, (node) => {
                // Check if the current node's name matches the "like" condition
                const isMatch =
                    isString(node.name) &&
                    node.name.toLowerCase().includes(searchName.toLowerCase());

                // If the current node doesn't match, recursively check its children
                if (node.children) {
                    const childrenMatch = searchByNameLike(node.children, searchName);
                    if (childrenMatch.length > 0) {
                        // If any children match, include this node and matched children
                        return { ...node, children: childrenMatch };
                    }
                }
                return isMatch;
            });
        };

        const searchByName = debounce(() => {
            if (extraFilters.value.name != "") {
                let data = cloneDeep(ogTypifications.value);
                let searchName = extraFilters.value.name;
                allTypifications.value = [];

                allTypifications.value = searchByNameLike(data, searchName);
            } else {
                allTypifications.value = [...ogTypifications.value];
            }
        }, 400);

        const viewItem = (item) => {
            viewData.value = item;
            detailsVisible.value = true;
        };

        const addItem = () => {
            addEditUrl.value = "notes-typifications";
            addEditType.value = "add";
            addEditVisible.value = true;
        };

        const addEditSuccess = (id) => {
            // If add action is performed then move page to first
            if (addEditType.value == "add") {
                formData.value = {
                    name: "",
                    parent_id: null,
                    sale: false,
		            schedule: false,
                    acciones: true,
                };
            }

            getTypifications();
            addEditVisible.value = false;
        };

        const closeMultipleItems = () => {
            visibleAddMultiple.value = false;
        };

        const onCloseAddEdit = () => {
            formData.value = { ...initData };
            addEditVisible.value = false;
        };

        const editItem = (item) => {
            const { children = [] } = item;
            formData.value = {
                name: item.name,
                parent_id: item.x_parent_id,
                sale: item.sale,
                schedule: item.schedule,
                acciones: children.length === 0,
                _method: "PUT",
            };

            viewData.value = item;
            addEditUrl.value = `notes-typifications/${item.xid}`;
            addEditType.value = "edit";
            addEditVisible.value = true;
        };

        const showDeleteConfirm = (id) => {
            Modal.confirm({
                title: t("common.delete") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t("notes_typification.delete_message"),
                centered: true,
                okText: t("common.yes"),
                okType: "danger",
                cancelText: t("common.no"),
                onOk() {
                    axiosAdmin.delete(`notes-typifications/${id}`).then(() => {
                        getTypifications();
                        notification.success({
                            message: t("common.success"),
                            description: t("notes_typification.deleted"),
                        });
                    });
                },
                onCancel() {},
            });
        };

        const showSelectedDeleteConfirm = () => {
            Modal.confirm({
                title: t("common.delete") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t("notes_typification.delete_message"),
                centered: true,
                okText: t("common.yes"),
                okType: "danger",
                cancelText: t("common.no"),
                onOk() {
                    const allDeletePromise = [];
                    forEach(selectedRowKeys.value, (selectedRow) => {
                        allDeletePromise.push(
                            axiosAdmin.delete(`notes-typifications/${selectedRow}`)
                        );
                    });

                    Promise.all(allDeletePromise).then((successResponse) => {
                        selectedRowKeys.value = [];

                        // Update Visible Subscription Modules
                        store.dispatch("auth/updateVisibleSubscriptionModules");

                        getTypifications();

                        notification.success({
                            message: t("common.success"),
                            description: t(`notes_typification.deleted`),
                            placement: "bottomRight",
                        });
                    });
                },
                onCancel() {},
            });
        };

        return {
            columns,
            addEditSuccess,
            formData,
            editItem,
            addEditVisible,
            addItem,
            onCloseAddEdit,
            addEditUrl,
            addEditType,
            showDeleteConfirm,
            detailsVisible,
            viewItem,
            viewData,
            allTypifications,
            permsArray,
            getTypifications,
            addMultipleItems,
            selectedRowKeys,
            rowSelection,
            showSelectedDeleteConfirm,
            visibleAddMultiple,
            closeMultipleItems,
            extraFilters,
            sampleFileUrl,
            searchByName,
        };
    },
};
</script>
