<template>
    <a-modal
        :open="visible"
        :closable="false"
        :centered="true"
        :title="pageTitle"
        @ok="onSubmit"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('lead.campaign_user')"
                        name="assign_to"
                        :help="rules.assign_to ? rules.assign_to.message : null"
                        :validateStatus="rules.assign_to ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.assign_to"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('lead.campaign_user'),
                                    ])
                                "
                            >
                                <a-select-option
                                    v-for="user in users"
                                    :key="user.xid"
                                    :value="user.xid"
                                >
                                    {{ user.name }}
                                </a-select-option>
                            </a-select>
                        </span>
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>

        <template #footer>
            <a-button
                type="primary"
                @click="onSubmit"
                style="margin-right: 8px"
                :loading="loading"
            >
                {{ $t("common.update") }}
            </a-button>
            <a-button @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-modal>
</template>

<script>
import { defineComponent, ref, watch } from "vue";
import common from "../../../../common/composable/common";
import apiAdmin from "../../../../common/composable/apiAdmin";
import { forEach, includes } from "lodash-es";
import { useI18n } from "vue-i18n";

export default defineComponent({
    props: ["data", "keys", "visible", "pageTitle"],
    setup(props, { emit }) {
        const { formatDateTime } = common();
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const campaignUser = ref([]);
        const formData = ref({
            assign_to: undefined,
            lead_ids: [],
        });
        const users = ref([]);
        const { t } = useI18n();

        const onSubmit = () => {
            addEditRequestAdmin({
                url: "assign-multiple-users",
                data: formData.value,
                successMessage: t("lead.user_assign_success"),
                success: (response) => {
                    if (response && response.success == "success") {
                    }
                    emit("close");
                    emit("setMultiUser");
                },
            });
        };

        const onClose = () => {
            rules.value = {};
            emit("close");
        };

        const assignMultiUser = () => {
            emit("setMultiUser");
        };

        watch(
            () => props.visible,
            (newVal) => {
                if (newVal) {
                    users.value = [];

                    forEach(props.data, (lead) => {
                        if (props.keys.includes(lead.xid)) {
                            campaignUser.value = lead.campaign_users;
                        }
                    });

                    forEach(campaignUser.value, (campaign) => {
                        if (campaign.user) {
                            users.value.push(campaign.user);
                        }
                    });

                    formData.value.assign_to = props.data.x_assign_to
                        ? props.data.x_assign_to
                        : undefined;
                    formData.value.lead_ids = props.keys;
                }
            }
        );

        return {
            rules,
            loading,
            formatDateTime,
            onClose,
            onSubmit,
            users,
            formData,
            assignMultiUser,
        };
    },
});
</script>
