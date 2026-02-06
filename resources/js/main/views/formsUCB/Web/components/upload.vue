<template>
    <a-upload-dragger v-model:fileList="fileList" name="file" :multiple="true" :customRequest="customRequest"
        @change="handleChange" @drop="handleDrop">
        <CloudUploadOutlined style="font-size: large;" />
        <p class="ant-upload-text">{{ $t("cobranzas.upload_file") }}</p>
        <p class="ant-upload-hint">
            {{ $t("cobranzas.upload_file_description", [type]) }}
        </p>
        <small>{{ $t("cobranzas.date_format") }} : <strong>YYYY-MM-DD HH:MM:SS</strong></small>
    </a-upload-dragger>
</template>

<script>
import { defineComponent, ref } from "vue";
import { message } from "ant-design-vue";
import { CloudUploadOutlined } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";

export default defineComponent({
    props: {
        url: {
            type: String,
            default: "upload-file",
        },
        folder: {
            type: String,
            default: null,
        },
        type: {
            type: String,
            default: "",
        },
    },
    components: {
        CloudUploadOutlined,
    },
    setup(props, { emit }) {
        const fileList = ref([]);
        const { t } = useI18n();

        const customRequest = (info) => {
            const formData = new FormData();
            formData.append("file", info.file);

            if (props.folder) {
                formData.append("folder", props.folder);
            }

            axiosAdmin
                .post(props.url, formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                })
                .then((response) => {

                    // emit("onFileUploaded", {
                    //     file: response.data.file,
                    //     file_url: response.data.file_url,
                    // });

                    emit("callback");
                    
                    info.onSuccess(response.data, info.file);
                    message.success(`${t("messages.upload_success")} (${info.file.name})`);
                })
                .catch((e) => {
                    console.error(e.data?.error?.message || 'Error');
                });
        };

        const handleChange = (info) => {
            const status = info.file.status;

            if (status !== "uploading") {
                console.log(info.file, info.fileList);
            }
        };

        const handleDrop = (e) => {
            console.log("Dropped files:", e);
        };

        return {
            fileList,
            customRequest,
            handleChange,
            handleDrop,
        };
    },
});
</script>
