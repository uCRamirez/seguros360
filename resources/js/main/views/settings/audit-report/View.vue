<template>
    <a-drawer
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :maskClosable="false"
        @close="onClose"
    >
        <div class="user-details">
            <a-row :gutter="[16, 16]" v-if="auditData && auditData.old_values">
                <a-col
                    :xs="24"
                    :sm="24"
                    :md="20"
                    :lg="20"
                    v-if="Object.keys(auditData.old_values).length > 0"
                >
                    <FormItemHeading>
                        {{ $t("audit.old_values") }}
                    </FormItemHeading>
                    <a-descriptions>
                        <a-descriptions-item
                            v-for="(value, key) in auditData.old_values"
                            :key="key"
                        >
                            <span style="font-weight: bold"> {{ key }}</span
                            >:&nbsp;{{ value }}
                        </a-descriptions-item>
                    </a-descriptions>
                </a-col>
            </a-row>
            <a-row :gutter="[16, 16]" v-if="auditData && auditData.new_values">
                <a-col
                    :xs="24"
                    :sm="24"
                    :md="20"
                    :lg="20"
                    v-if="Object.keys(auditData.new_values).length > 0"
                >
                    <FormItemHeading>
                        {{ $t("audit.new_values") }}
                    </FormItemHeading>
                    <a-descriptions>
                        <a-descriptions-item
                            v-for="(value, key) in auditData.new_values"
                            :key="key"
                        >
                            <span style="font-weight: bold"> {{ key }}</span>
                            :&nbsp;{{ value }}
                        </a-descriptions-item>
                    </a-descriptions>
                </a-col>
            </a-row>
        </div>
    </a-drawer>
</template>
<script>
import { defineComponent, ref, watch, computed } from "vue";
import { useI18n } from "vue-i18n";
import FormItemHeading from "../../../../common/components/common/typography/FormItemHeading.vue";
import common from "../../../../common/composable/common";

export default defineComponent({
    components: {
        FormItemHeading,
    },
    props: ["auditData", "visible"],
    emits: ["closed"],
    setup(props, { emit }) {
        const { formatAmountCurrency } = common();
        const { t } = useI18n();

        const onClose = () => {
            emit("closed");
        };

        return {
            formatAmountCurrency,
            onClose,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "55%",
        };
    },
});
</script>

<style lang="less">
.user-details {
    .ant-descriptions-item {
        padding-bottom: 5px;
    }
}
.auditList {
    list-style: none;
}
</style>
