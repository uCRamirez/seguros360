<template>
    <a-row v-if="parsedFileData.length == 0" :gutter="16">
        <a-col :xs="24" :sm="24" :md="24" :lg="24">
            <a-upload-dragger
                v-model:fileList="fileList"
                name="file"
                :accept="acceptFormat"
                :customRequest="customRequest"
                @drop="customRequest"
            >
                <p class="ant-upload-drag-icon">
                    <UploadOutlined />
                </p>
                <p class="ant-upload-text">Click or drag file to this area to upload</p>
                <p class="ant-upload-hint">
                    Support for a single or bulk upload. Strictly prohibit from uploading
                    company data or other band files
                </p>
            </a-upload-dragger>
        </a-col>
    </a-row>
    <a-row v-else :gutter="16">
        <a-col :xs="24" :sm="24" :md="24" :lg="24">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-tabs v-model:activeKey="activeTabKey" @change="tabChanged">
                        <a-tab-pane key="all">
                            <template #tab>
                                <a-space>
                                    <a-badge
                                        :count="size(formProperties)"
                                        :number-style="{
                                            backgroundColor: '#fff',
                                            color: '#999',
                                            boxShadow: '0 0 0 1px #d9d9d9 inset',
                                        }"
                                        :overflowCount="2000"
                                    />
                                    {{ $t("campaign.all_columns") }}
                                </a-space>
                            </template>
                        </a-tab-pane>

                        <a-tab-pane key="matched">
                            <template #tab>
                                <a-space>
                                    <a-badge
                                        :count="
                                            size(formProperties) - unMatchedColumnsCount
                                        "
                                        :number-style="{
                                            backgroundColor: '#52c41a',
                                        }"
                                        :overflowCount="2000"
                                    />
                                    {{ $t("campaign.matched_columns") }}
                                </a-space>
                            </template>
                        </a-tab-pane>
                        <a-tab-pane key="un_matched">
                            <template #tab>
                                <a-space>
                                    <a-badge
                                        :count="unMatchedColumnsCount"
                                        :overflowCount="2000"
                                    />
                                    {{ $t("campaign.unmatched_columns") }}
                                </a-space>
                            </template>
                        </a-tab-pane>
                    </a-tabs>
                </a-col>
            </a-row>

            <a-table
                :columns="columns"
                :row-key="(record) => record"
                :data-source="tableRecords"
                :pagination="false"
            >
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'csv_header_field'">
                        {{ record }}
                    </template>
                    <template v-if="column.dataIndex === 'csv_field_data'">
                        <span v-html="getPreviewData(record)"></span>
                    </template>
                    <template v-if="column.dataIndex === 'column_status'">
                        <a-select
                            v-model:value="formProperties[record]"
                            :placeholder="
                                $t('common.select_default_text', [$t('lead.campaign')])
                            "
                            :allowClear="true"
                            style="width: 100%"
                            optionFilterProp="title"
                            show-search
                            @change="reAssignCurrentFormFields"
                            :open="
                                formProperties[record] != undefined ? false : undefined
                            "
                        >
                            <a-select-option
                                v-for="formFields in currentFormFields"
                                :key="formFields"
                                :title="record"
                                :value="formFields"
                            >
                                {{ formFields }}
                            </a-select-option>
                        </a-select>
                        <div
                            v-show="formProperties[record] == undefined"
                            class="ant-form-item-explain-error"
                            style=""
                        >
                            {{ $t("campaign.column_will_not_imported") }}
                        </div>
                    </template>
                </template>
            </a-table>
        </a-col>
    </a-row>
</template>

<script>
import { computed, defineComponent, ref, watch } from "vue";
import { UploadOutlined, FileOutlined } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import Papa from "papaparse";
import { forEach, find, includes, size } from "lodash-es";

export default defineComponent({
    props: {
        acceptFormat: {
            default: "image/*,.pdf",
            type: String,
        },
        allFields: null,
    },
    emits: ["fileUploaded", "leadColumnChanged"],
    components: {
        UploadOutlined,
        FileOutlined,
    },

    setup(props, { emit }) {
        const fileList = ref([]);
        const loading = ref(false);
        const { t } = useI18n();
        const currentUploadedFile = ref(undefined);
        const parsedFileData = ref([]);
        const fullData = ref([])  
        const tableRecords = ref([]);
        const parsedHeader = ref([]);
        const formProperties = ref({});
        const currentFormFields = ref([...props.allFields]);
        const activeTabKey = ref("all");

        const columns = [
            {
                title: t("campaign.csv_header_field"),
                dataIndex: "csv_header_field",
            },
            {
                title: t("campaign.csv_field_data"),
                dataIndex: "csv_field_data",
            },
            {
                title: t("campaign.column_status"),
                dataIndex: "column_status",
            },
        ];

        const customRequest = (info) => {
            const newFormData = new FormData();
            newFormData.append("file", info.file);
            newFormData.append("folder", props.folder);

            loading.value = true;

            let reader = new FileReader();
            reader.readAsText(info.file, "UTF-8");
            reader.onload = function (evt) {
                const parsedResult = Papa.parse(evt.target.result, {
                    // preview: 3,
                    header: true,
                    skipEmptyLines: true,
                    transformHeader: function (header) {
                        // Convert the header to lowercase and exclude 'agent' regardless of case
                        return header.toLowerCase() === "agent" ? null : header;
                    },
                    complete: function (results) {
                        // Filter out rows where the column header is null or undefined
                        const filteredFields = results.meta.fields.filter(
                            (field) => field !== null
                        );
                        const filteredData = results.data.map((row) => {
                            const filteredRow = {};
                            filteredFields.forEach((field) => {
                                filteredRow[field] = row[field];
                            });
                            return filteredRow;
                        });

                        // Update meta fields and data to reflect removed 'action' column
                        results.meta.fields = filteredFields;
                        results.data = filteredData;
                    },
                });

                const fullResult = Papa.parse(evt.target.result, {
                    preview: 3,
                    header: true,
                    skipEmptyLines: true,
                    transformHeader: function (header) {
                        return header.toLowerCase() === "agent" ? null : header;
                    },
                    complete: function (results) {
                        const filteredFields = results.meta.fields.filter(
                            (field) => field !== null
                        );
                        const filteredData = results.data.map((row) => {
                            const filteredRow = {};
                            filteredFields.forEach((field) => {
                                filteredRow[field] = row[field];
                            });
                            return filteredRow;
                        });
                        results.meta.fields = filteredFields;
                        results.data = filteredData;
                    },
                });

                fullData.value = fullResult.data;
                parsedFileData.value = parsedResult.data;
                parsedHeader.value = parsedResult.meta.fields;
                currentUploadedFile.value = info.file;

                console.log(parsedResult, "parsedHeader.value");
                console.log(parsedFileData.value, "parsedFileData.value");

                var newValues = {};
                forEach(parsedHeader.value, (filterValue, filterKey) => {
                    const isFieldFind = includes(props.allFields, filterValue);

                    newValues[filterValue] = isFieldFind ? filterValue : undefined;
                });
                formProperties.value = newValues;

                reAssignCurrentFormFields();
            };

            emit("fileUploaded", info.file);
        };

        const getPreviewData = (fieldName) => {
            var fieldDataString = "";

            forEach(fullData.value, (filterValue, filterKey) => {
                fieldDataString += `${filterValue[fieldName]}<br />`;
            });

            return fieldDataString;
        };

        const reAssignCurrentFormFields = () => {
            var newFormFields = [];

            forEach(props.allFields, (filterValue, filterKey) => {
                if (!includes(formProperties.value, filterValue)) {
                    newFormFields.push(filterValue);
                }
            });
            currentFormFields.value = newFormFields;

            tabChanged(activeTabKey.value);

            // emit("leadColumnChanged", formProperties.value);
            emit("leadColumnChanged", parsedFileData.value);
        };

        const unMatchedColumnsCount = computed(() => {
            var counter = 0;

            forEach(formProperties.value, (filterValue, filterKey) => {
                if (filterValue == undefined) {
                    counter = counter + 1;
                }
            });

            return counter;
        });

        const tabChanged = (activeKey) => {
            if (activeKey == "matched") {
                var newDataResult = [];

                forEach(parsedHeader.value, (filterValue, filterKey) => {
                    if (formProperties.value[filterValue] != undefined) {
                        newDataResult.push(filterValue);
                    }
                });
                tableRecords.value = newDataResult;
            } else if (activeKey == "un_matched") {
                var newDataResult = [];

                forEach(parsedHeader.value, (filterValue, filterKey) => {
                    if (formProperties.value[filterValue] == undefined) {
                        newDataResult.push(filterValue);
                    }
                });
                tableRecords.value = newDataResult;
            } else {
                tableRecords.value = parsedHeader.value;
            }
        };

        watch(
            () => props.allFields,
            (newVal, oldVal) => {
                reAssignCurrentFormFields();
            }
        );

        return {
            fileList,
            loading,
            customRequest,

            columns,
            currentUploadedFile,
            parsedFileData,
            parsedHeader,
            getPreviewData,
            formProperties,

            currentFormFields,
            reAssignCurrentFormFields,
            unMatchedColumnsCount,

            activeTabKey,
            tabChanged,
            tableRecords,
            size,
        };
    },
});
</script>
