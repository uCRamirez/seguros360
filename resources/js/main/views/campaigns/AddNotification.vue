<template>
    <a-drawer :title="$t('common.add_notification')" :width="drawerWidth" :open="visible" :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }" :maskClosable="false" @close="onClose">
        <a-form layout="vertical" ref="formRef" :model="datos.notificacion" :rules="rules">
            
            <a-row>
                <a-col :span="24">
                    <a-descriptions 
                        :title="`${$t('common.information')} ${$t('campaign.campaign')}`" 
                        :column="1" 
                    > 
                        <a-descriptions-item>
                            <a-image :width="100" :src="campaign.image_url" />
                        </a-descriptions-item>
                        
                        <a-descriptions-item :label="$t('campaign.name')">
                            {{ campaign.name }}
                        </a-descriptions-item>
                        
                    </a-descriptions>
                </a-col>
            </a-row>

            <a-row :gutter="16" class="mt-20">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item :label="$t('common.title')" name="title" class="required">
                        <a-input v-model:value="datos.notificacion.title" :placeholder="$t('common.placeholder_default_text', [
                            $t('common.title')
                        ])
                            " />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16" class="mt-20">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item :label="$t('message_template.message')" name="message" class="required">
                        <a-textarea
                            v-model:value="datos.notificacion.message"
                            :placeholder="$t('common.placeholder_default_text', [$t('message_template.message')])"
                            :rows="4" 
                            :maxlength="100"
                        />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16" class="mt-20">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item :label="$t('common.link')" name="url">
                        <a-input v-model:value="datos.notificacion.url" :placeholder="$t('common.placeholder_default_text', [
                            $t('common.link')
                        ])
                            " />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row>
                <a-col :span="24">
                    <a-col :xs="24" :sm="24" :md="24" :lg="24">
                        <a-form-item :label="$t('common.addressed_to')" name="users_id" class="required">
                            <span style="display: flex">
                                <a-select v-model:value="datos.notificacion.users_id" mode="multiple" :placeholder="$t('common.select_default_text', [
                                    $t('campaign.members'),
                                ])
                                    " :allowClear="true">
                                    <a-select-option v-for="allStaffMember in campaign.campaign_users" :key="allStaffMember.user.xid"
                                        :value="allStaffMember.user.id">
                                        {{ allStaffMember.user.name }}
                                    </a-select-option>
                                </a-select>
                            </span>
                        </a-form-item>
                    </a-col>
                </a-col>
            </a-row>

            <a-row>
                <a-col :span="24">
                    <a-col :xs="24" :sm="24" :md="24" :lg="24" class="text-center">
                        <a-button key="submit" type="primary" :loading="loading" @click="submitForm">
                            <SendOutlined />
                            {{ $t("common.send") }}
                        </a-button>
                    </a-col>
                </a-col>
            </a-row>

        </a-form>
    </a-drawer>
</template>
<script>
import { defineComponent, ref, onMounted, watch } from "vue";
import {
    LoadingOutlined,
    SaveOutlined,
    FileTextOutlined,
    DoubleRightOutlined,
    DoubleLeftOutlined,
    SendOutlined,
} from "@ant-design/icons-vue";
import StaffMemberAddButton from "../users/StaffAddButton.vue";
import ImportLeads from "./ImportLeads.vue";
import { useI18n } from "vue-i18n";
import { reactive } from "vue";
import { notification } from 'ant-design-vue';


function getEmptyNotificacion() {
    return {
        users_id: [],
        title: '',
        message: '',
        url: '',
    };
}

export const datos = reactive({
    notificacion: getEmptyNotificacion(),
});

export default defineComponent({
    props: [
        "formData",
        "permsArray",
        "campaign",
        "visible",
        "successMessage",
    ],
    components: {
        LoadingOutlined,
        SaveOutlined,
        FileTextOutlined,
        DoubleRightOutlined,
        DoubleLeftOutlined,
        ImportLeads,
        StaffMemberAddButton,
        SendOutlined,
    },
    setup(props, { emit }) {

        const { t } = useI18n();
        const staffMembersUrl = "users?limit=10000";
        const allStaffMembers = ref([]);

        const formRef = ref(null);
        const rules = {
        title:    [{ required: true, message: t('common.title') }],
        message:  [{ required: true, message: t('message_template.message') }],
        users_id: [{ required: true, message: t('common.addressed_to') }],
        };

        onMounted(() => {
            datos.notificacion = getEmptyNotificacion();
            const staffMemberPromise = axiosAdmin.get(staffMembersUrl);

            Promise.all([staffMemberPromise]).then(
                ([staffMemberResponse, calidadTemplatesResponse]) => {
                    allStaffMembers.value = staffMemberResponse.data;
                }
            );

        });


        const submitForm = async () => {
            try {
                await formRef.value.validate();
                await axiosAdmin.post('notifications/send-to-users', {
                    user_ids: datos.notificacion.users_id,
                    title:    datos.notificacion.title,
                    message:  datos.notificacion.message,
                    url:      datos.notificacion.url,
                });
                datos.notificacion = getEmptyNotificacion();
                notification.success({ message: t(`common.success`), description: t(`common.created`) });
                emit("closed");
            } catch (err) {
                console.error(err)
            }
        };


        const onClose = () => {
            emit("closed");
        };

        watch(() => props.visible,
            (newVal) => {
                if (newVal) {

                }
            }
        );

        return {
            datos,
            onClose,
            submitForm,
            allStaffMembers,
            formRef,
            rules,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "30%",
        };
    },
});
</script>