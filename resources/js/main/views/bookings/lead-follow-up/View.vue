<template>
    <a-drawer
        :title="pageTitle"
        :width="720"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
    >
        <a-row :gutter="16">
            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                <a-descriptions
                    :labelStyle="{
                        fontWeight: 'bold',
                    }"
                    :contentStyle="{
                        marginBottom: '15px',
                    }"
                    :column="1"
                    layout="vertical"
                    size="small"
                >
                    <a-descriptions-item :label="$t('uphone_calls.lead_id')">
                        {{ data.lead.id }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('uphone_calls.campaign_id')">
                        {{ data.campaign_data.name }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('uphone_calls.campaign')">
                        {{ data.campaign }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('uphone_calls.client_id')">
                        {{ data.client_id }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('uphone_calls.direction')">
                        {{ data.direction }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('uphone_calls.direction')">
                        {{ data.direction }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('uphone_calls.guid')">
                        {{ data.guid }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('uphone_calls.number')">
                        {{ data.number }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('uphone_calls.response_data')">
                        {{ data.response_data }}
                    </a-descriptions-item>
                    <a-descriptions-item :label="$t('uphone_calls.discriptions')">
                        <div
                            v-if="data && data.discriptions"
                            style="white-space: pre-wrap"
                        >
                            {{ data.discriptions }}
                        </div>
                        <span v-else>-</span>
                    </a-descriptions-item>
                </a-descriptions>
            </a-col>
        </a-row>

        <template #footer>
            <a-button key="back" type="primary" @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-drawer>
</template>

<script>
import { defineComponent } from "vue";
import common from "../../../common/composable/common";
import apiAdmin from "../../../common/composable/apiAdmin";

export default defineComponent({
    props: ["data", "visible", "pageTitle"],
    setup(props, { emit }) {
        const { formatDateTime } = common();
        const { loading, rules } = apiAdmin();

        const onClose = () => {
            rules.value = {};
            emit("close");
        };

        return {
            rules,
            loading,
            formatDateTime,
            onClose,
        };
    },
});
</script>
