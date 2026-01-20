<template>
    <a-row :gutter="16">

    </a-row>
</template>
<script>
import { watch, onMounted } from "vue";
import fields from "./fields.js";
import {
    DeleteOutlined,
} from "@ant-design/icons-vue";
import Upload from "../../../../components/upload.vue";
import crud from "../../../../../../../../common/composable/crud";

export default {
    components: {
        //// NUEVO ////
        Upload,
        DeleteOutlined,
        ///////////////
    },
    setup() {
        const crudVariables = crud();

        const { columns } = fields();

        const setUrlData = () => {
            crudVariables.tableUrl.value = {
                url: url,
            };
            crudVariables.table.filterableColumns = filterableColumns;

            crudVariables.fetch({
                page: 1,
            });

            crudVariables.crudUrl.value = addEditUrl;
            crudVariables.langKey.value = "user";
            crudVariables.initData.value = { ...initData };
            crudVariables.formData.value = { ...initData };
            crudVariables.hashableColumns.value = [...hashableColumns];
        };

        // onMounted(() => {
        //     setUrlData();
        // });

        return {
            // En uso
            columns,
            ...crudVariables,
            /////////////
        };
    },
};
</script>