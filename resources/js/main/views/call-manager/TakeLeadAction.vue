<template>
    <a-layout
        :style="
            themeMode == 'dark'
                ? { background: '#141414', height: '100vh' }
                : { height: '100vh' }
        "
    >
        <a-row v-if="leadDetails && leadDetails.xid">
            <a-col>
                <a-card
                    class="page-content-sub-header breadcrumb-left-border"
                    :bodyStyle="{ padding: '0px', margin: '0px 16px 0' }"
                >
                    <a-row>
                        <a-col :span="24">
                            <a-page-header @back="saveAndExit" class="p-0">
                                <template #title>
                                    {{
                                        leadDetails &&
                                        leadDetails.campaign &&
                                        leadDetails.campaign.name
                                            ? referenceNumber != undefined &&
                                              referenceNumber != ""
                                                ? `${leadDetails.campaign.name} (${referenceNumber})`
                                                : leadDetails.campaign.name
                                            : ""
                                    }}
                                </template>
                                <template #extra>
                                    <a-button
                                        :style="{
                                            background: '#faad14',
                                            borderColor: '#faad14',
                                        }"
                                        type="primary"
                                        @click="takeLeadAction('previous')"
                                    >
                                        <ArrowLeftOutlined />
                                        {{ $t("campaign.previous_lead") }}
                                    </a-button>
                                    <a-button
                                        :style="{
                                            background: '#52c41a',
                                            borderColor: '#52c41a',
                                        }"
                                        type="primary"
                                        @click="takeLeadAction('next')"
                                    >
                                        {{ $t("campaign.next_lead") }}
                                        <ArrowRightOutlined />
                                    </a-button>
                                </template>
                            </a-page-header>
                        </a-col>
                        <!-- <a-col :span="24">
                            <a-breadcrumb separator="-" style="font-size: 12px">
                                <a-breadcrumb-item>
                                    <router-link :to="{ name: 'admin.dashboard.index' }">
                                        {{ $t(`menu.dashboard`) }}
                                    </router-link>
                                </a-breadcrumb-item>
                                <a-breadcrumb-item>
                                    <router-link
                                        :to="{ name: 'admin.call_manager.index' }"
                                    >
                                        {{ $t(`menu.call_manager`) }}
                                    </router-link>
                                </a-breadcrumb-item>
                                <a-breadcrumb-item>
                                    {{
                                        leadDetails &&
                                        leadDetails.campaign &&
                                        leadDetails.campaign.name
                                            ? leadDetails.campaign.name
                                            : ""
                                    }}
                                </a-breadcrumb-item>
                            </a-breadcrumb>
                        </a-col> -->
                    </a-row>
                </a-card>

                <a-row v-if="leadDetails && leadDetails.xid">
                    <a-col
                        :xs="24"
                        :sm="24"
                        :md="24"
                        :lg="6"
                        :xl="6"
                        class="bg-setting-sidebar"
                        :style="
                            themeMode == 'dark'
                                ? { background: '#141414 !important' }
                                : { background: '#fff !important' }
                        "
                    >
                        <div class="callmanager-left-sidebar">
                            <a-card
                                :bordered="false"
                                :bodyStyle="{ paddingBottom: '0px' }"
                            >
                                <a-row>
                                    <a-col :span="24" class="text-center">
                                        <a-typography-title :level="2">
                                            <ClockCircleOutlined />
                                            {{
                                                timer.hours.value < 10
                                                    ? `0${timer.hours.value}`
                                                    : timer.hours
                                            }}:{{
                                                timer.minutes.value < 10
                                                    ? `0${timer.minutes.value}`
                                                    : timer.minutes
                                            }}:{{
                                                timer.seconds.value < 10
                                                    ? `0${timer.seconds.value}`
                                                    : timer.seconds
                                            }}
                                        </a-typography-title>
                                    </a-col>
                                </a-row>

                                <a-row class="mt-10">
                                    <a-col :span="24">
                                        <a-space>
                                            <BookingModal
                                                v-if="leadDetails && leadDetails.xid"
                                                key="lead_follow_up"
                                                :leadId="leadDetails.xid"
                                                bookingType="lead_follow_up"
                                                @success="
                                                    (resultValue) => {
                                                        leadFollowUp = resultValue;
                                                        refreshTimeLine = true;
                                                    }
                                                "
                                            >
                                                <ScheduleOutlined />
                                                {{ $t("menu.lead_follow_up") }}
                                            </BookingModal>

                                            <BookingModal
                                                v-if="leadDetails && leadDetails.xid"
                                                key="salesman_bookings"
                                                :leadId="leadDetails.xid"
                                                bookingType="salesman_bookings"
                                                @success="
                                                    (resultValue) => {
                                                        salesmanBooking = resultValue;
                                                        refreshTimeLine = true;
                                                    }
                                                "
                                            >
                                                <ShoppingCartOutlined />
                                                {{ $t("menu.salesman_bookings") }}
                                            </BookingModal>
                                        </a-space>
                                    </a-col>
                                </a-row>

                                <a-divider />
                            </a-card>

                            <perfect-scrollbar
                                :options="{
                                    wheelSpeed: 1,
                                    swipeEasing: true,
                                    suppressScrollX: true,
                                }"
                            >
                                <a-collapse
                                    v-model:activeKey="activeLeftPanelKey"
                                    :bordered="false"
                                >
                                    <a-collapse-panel
                                        key="lead_details"
                                        :style="{
                                            background:
                                                themeMode == 'dark' ? '#141414' : '#fff',
                                        }"
                                    >
                                        <template #header>
                                            <a-typography-title :level="5">
                                                {{ $t("lead.lead_details") }}
                                            </a-typography-title>
                                        </template>
                                        <a-skeleton v-if="newPageLoad" active />
                                        <template v-else>
                                            <a-row>
                                                <a-col :span="24">
                                                    <a-typography-text strong>
                                                        {{ $t("lead.lead_number") }}
                                                    </a-typography-text>
                                                </a-col>
                                                <a-col :span="24" class="mt-5">
                                                    <a-typography-text>
                                                        {{ leadNumber }} /
                                                        {{
                                                            leadDetails.campaign
                                                                .total_leads
                                                        }}
                                                    </a-typography-text>
                                                </a-col>
                                            </a-row>
                                            <a-row class="mt-5">
                                                <a-col :span="24">
                                                    <a-typography-text strong>
                                                        {{ $t("campaign.last_actioner") }}
                                                    </a-typography-text>
                                                </a-col>
                                                <a-col :span="24" class="mt-5">
                                                    <a-typography-text>
                                                        {{
                                                            leadDetails.campaign
                                                                .last_actioner &&
                                                            leadDetails.campaign
                                                                .last_actioner.name
                                                                ? leadDetails.campaign
                                                                      .last_actioner.name
                                                                : "-"
                                                        }}
                                                    </a-typography-text>
                                                </a-col>
                                            </a-row>
                                            <a-row class="mt-5">
                                                <a-col :span="24">
                                                    <a-typography-text strong>
                                                        {{
                                                            $t("lead_follow_up.follow_up")
                                                        }}
                                                    </a-typography-text>
                                                </a-col>
                                                <a-col :span="24" class="mt-5">
                                                    <a-typography-text
                                                        v-if="
                                                            leadFollowUp &&
                                                            leadFollowUp.user &&
                                                            leadFollowUp.user.name
                                                        "
                                                    >
                                                        {{ leadFollowUp.user.name }} on
                                                        {{
                                                            formatDateTime(
                                                                leadFollowUp.date_time
                                                            )
                                                        }}
                                                    </a-typography-text>
                                                    <span v-else>-</span>
                                                </a-col>
                                            </a-row>
                                            <a-row class="mt-5">
                                                <a-col :span="24">
                                                    <a-typography-text strong>
                                                        {{
                                                            $t(
                                                                "salesman_booking.salesman_booking"
                                                            )
                                                        }}
                                                    </a-typography-text>
                                                </a-col>
                                                <a-col :span="24" class="mt-5">
                                                    <a-typography-text
                                                        v-if="
                                                            salesmanBooking &&
                                                            salesmanBooking.user &&
                                                            salesmanBooking.user.name
                                                        "
                                                    >
                                                        {{ salesmanBooking.user.name }} on
                                                        {{
                                                            formatDateTime(
                                                                salesmanBooking.date_time
                                                            )
                                                        }}
                                                    </a-typography-text>
                                                    <span v-else>-</span>
                                                </a-col>
                                            </a-row>
                                        </template>
                                    </a-collapse-panel>
                                    <a-collapse-panel
                                        key="campaign_details"
                                        :style="{
                                            background:
                                                themeMode == 'dark' ? '#141414' : '#fff',
                                        }"
                                    >
                                        <template #header>
                                            <a-typography-title :level="5">
                                                {{ $t("campaign.campaign_details") }}
                                            </a-typography-title>
                                        </template>
                                        <a-row
                                            v-for="(
                                                campaignDetails, campaignDetailsKey
                                            ) in leadDetails.campaign.detail_fields"
                                            :key="campaignDetails.id"
                                            :gutter="16"
                                            :class="{ 'mt-25': campaignDetailsKey > 0 }"
                                        >
                                            <a-col :span="24">
                                                <a-typography-text strong>
                                                    {{ campaignDetails.field_name }}
                                                </a-typography-text>
                                            </a-col>
                                            <a-col :span="24" class="mt-5">
                                                <a-typography-text>
                                                    {{ campaignDetails.field_value }}
                                                </a-typography-text>
                                            </a-col>
                                        </a-row>
                                    </a-collapse-panel>
                                </a-collapse>
                            </perfect-scrollbar>
                        </div>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="24" :lg="12" :xl="12">
                        <a-card class="callmanager-middle-sidebar">
                            <a-tabs v-model:activeKey="activeKey">
                                <a-tab-pane key="lead_details">
                                    <template #tab>
                                        <span>
                                            <FileTextOutlined />
                                            {{ $t("lead.lead_details") }}
                                        </span>
                                    </template>
                                    <perfect-scrollbar
                                        :options="{
                                            wheelSpeed: 1,
                                            swipeEasing: true,
                                            suppressScrollX: true,
                                        }"
                                    >
                                        <a-skeleton v-if="newPageLoad" active />
                                        <a-form v-else layout="vertical" class="mt-20">
                                            <a-row :gutter="16">
                                                <a-col
                                                    :xs="24"
                                                    :sm="24"
                                                    :md="12"
                                                    :lg="12"
                                                >
                                                    <a-form-item
                                                        :label="
                                                            $t('lead.reference_number')
                                                        "
                                                        name="reference_number"
                                                        :help="
                                                            rules.reference_number
                                                                ? rules.reference_number
                                                                      .message
                                                                : null
                                                        "
                                                        :validateStatus="
                                                            rules.reference_number
                                                                ? 'error'
                                                                : null
                                                        "
                                                        class="required"
                                                    >
                                                        <a-input
                                                            v-model:value="
                                                                referenceNumber
                                                            "
                                                            :placeholder="
                                                                $t(
                                                                    'common.placeholder_default_text',
                                                                    [
                                                                        $t(
                                                                            'lead.reference_number'
                                                                        ),
                                                                    ]
                                                                )
                                                            "
                                                        />
                                                    </a-form-item>
                                                </a-col>
                                                <a-col
                                                    :xs="24"
                                                    :sm="24"
                                                    :md="12"
                                                    :lg="12"
                                                >
                                                    <a-form-item
                                                        :label="$t('lead.lead_status')"
                                                        name="lead_status"
                                                        :help="
                                                            rules.lead_status
                                                                ? rules.lead_status
                                                                      .message
                                                                : null
                                                        "
                                                        :validateStatus="
                                                            rules.lead_status
                                                                ? 'error'
                                                                : null
                                                        "
                                                    >
                                                        <a-select
                                                            v-model:value="leadStatusId"
                                                            :placeholder="
                                                                $t(
                                                                    'common.select_default_text',
                                                                    [
                                                                        $t(
                                                                            'lead.lead_status'
                                                                        ),
                                                                    ]
                                                                )
                                                            "
                                                        >
                                                            <a-select-option
                                                                v-for="allLeadStatu in allLeadStatus"
                                                                :key="allLeadStatu.xid"
                                                                :value="allLeadStatu.xid"
                                                            >
                                                                {{ allLeadStatu.name }}
                                                            </a-select-option>
                                                        </a-select>
                                                    </a-form-item>
                                                </a-col>
                                            </a-row>
                                            <a-row :gutter="16">
                                                <a-col
                                                    v-for="leadData in leadFormData.lead_data"
                                                    :key="leadData.id"
                                                    :xs="24"
                                                    :sm="24"
                                                    :md="12"
                                                    :lg="12"
                                                >
                                                    <a-form-item
                                                        :name="leadData.field_name"
                                                        :help="
                                                            rules.name
                                                                ? rules.name.message
                                                                : null
                                                        "
                                                        :validateStatus="
                                                            rules.name ? 'error' : null
                                                        "
                                                    >
                                                        <template #label>
                                                            {{ leadData.field_name }}
                                                        </template>
                                                        <a-input-group compact>
                                                            <a-input
                                                                v-model:value="
                                                                    leadData.field_value
                                                                "
                                                                :placeholder="
                                                                    $t(
                                                                        'common.placeholder_default_text',
                                                                        [
                                                                            leadData.field_name,
                                                                        ]
                                                                    )
                                                                "
                                                                :style="{
                                                                    width:
                                                                        getLeadDataFieldType(
                                                                            leadData.field_name
                                                                        ) == 'email' ||
                                                                        getLeadDataFieldType(
                                                                            leadData.field_name
                                                                        ) == 'phone'
                                                                            ? 'calc(100% - 64px)'
                                                                            : '100%',
                                                                }"
                                                            />
                                                            <SendMail
                                                                v-if="
                                                                    getLeadDataFieldType(
                                                                        leadData.field_name
                                                                    ) == 'email'
                                                                "
                                                                :email="
                                                                    leadData.field_value
                                                                "
                                                                :lead="leadDetails"
                                                                :leadFormData="
                                                                    leadFormData
                                                                "
                                                                @success="
                                                                    () =>
                                                                        (refreshTimeLine = true)
                                                                "
                                                                :campaigns="
                                                                    uPhoneEmailCampaignsLists
                                                                "
                                                            />
                                                            <SendCall
                                                                v-if="
                                                                    getLeadDataFieldType(
                                                                        leadData.field_name
                                                                    ) == 'phone'
                                                                "
                                                                :phone="
                                                                    leadData.field_value
                                                                "
                                                                :campaigns="
                                                                    uPhoneCampaignsLists
                                                                "
                                                            />
                                                            <SendMessage
                                                                v-if="
                                                                    getLeadDataFieldType(
                                                                        leadData.field_name
                                                                    ) == 'phone'
                                                                "
                                                                :phone="
                                                                    leadData.field_value
                                                                "
                                                                :lead="leadDetails"
                                                                :leadFormData="
                                                                    leadFormData
                                                                "
                                                                @success="
                                                                    () =>
                                                                        (refreshTimeLine = true)
                                                                "
                                                            />
                                                        </a-input-group>
                                                    </a-form-item>
                                                </a-col>
                                            </a-row>
                                        </a-form>
                                    </perfect-scrollbar>
                                    <div
                                        :style="{
                                            position: 'absolute',
                                            right: 0,
                                            bottom: 0,
                                            width: '100%',
                                            borderTop: '1px solid #e9e9e9',
                                            padding: '10px 16px',
                                            background:
                                                themeMode == 'dark' ? '#141414' : '#fff',
                                            zIndex: 1,
                                        }"
                                    >
                                        <a-row :gutter="16">
                                            <a-col :xs="24" :sm="24" :md="20" :lg="20">
                                                <a-space>
                                                    <a-button
                                                        type="primary"
                                                        :loading="saveLoading"
                                                        @click="saveLead('save')"
                                                    >
                                                        <template #icon>
                                                            <SaveOutlined />
                                                        </template>
                                                        {{ $t("common.save") }}
                                                    </a-button>
                                                    <a-button
                                                        type="primary"
                                                        :loading="saveExitLoading"
                                                        @click="saveAndExit"
                                                    >
                                                        <template #icon>
                                                            <DeliveredProcedureOutlined />
                                                        </template>
                                                        {{ $t("campaign.save_exit") }}
                                                    </a-button>
                                                    <!-- <a-button
                                            :style="{
                                                background: '#ff4d4f',
                                                borderColor: '#ff4d4f',
                                            }"
                                            type="primary"
                                            @click="skipLead"
                                        >
                                            {{ $t("campaign.skip_lead") }}
                                            <DoubleRightOutlined />
                                        </a-button> -->
                                                </a-space>
                                            </a-col>
                                            <a-col :xs="24" :sm="24" :md="4" :lg="4">
                                                <a-button
                                                    v-if="autoSaved"
                                                    type="text"
                                                    :style="{
                                                        background: 'transparent',
                                                        borderColor: 'transparent',
                                                    }"
                                                >
                                                    <a-typography-text type="secondary">
                                                        <CheckOutlined />
                                                        Auto Saved
                                                    </a-typography-text>
                                                </a-button>
                                            </a-col>
                                        </a-row>
                                    </div>
                                </a-tab-pane>
                                <a-tab-pane key="call_logs">
                                    <template #tab>
                                        <span>
                                            <PhoneOutlined />
                                            {{ $t("menu.call_logs") }}
                                        </span>
                                    </template>
                                    <LeadLogTable
                                        key="lead_logs"
                                        pageName="lead_action"
                                        logType="call_log"
                                        :leadId="leadDetails.xid"
                                        :showTableSearch="false"
                                        :showLeadDetails="false"
                                        :showAction="false"
                                        :scrollStyle="{ y: 'calc(100vh - 320px)' }"
                                    />
                                </a-tab-pane>
                                <a-tab-pane key="lead_notes">
                                    <template #tab>
                                        <span>
                                            <FileTextOutlined />
                                            {{ $t("common.notes") }}
                                        </span>
                                    </template>
                                    <LeadNotesTable
                                        pageName="lead_action"
                                        :leadId="leadDetails.xid"
                                        :scrollStyle="{ y: 'calc(100vh - 320px)' }"
                                        @success="() => (refreshTimeLine = true)"
                                        :showAddButton="
                                            leadDetails &&
                                            leadDetails.campaign &&
                                            leadDetails.campaign.status == 'completed'
                                                ? false
                                                : true
                                        "
                                    />
                                </a-tab-pane>
                                <a-tab-pane key="message_history">
                                    <template #tab>
                                        <span>
                                            <MessageOutlined />
                                            {{ $t("common.message_history") }}
                                        </span>
                                    </template>
                                    <LeadLogTable
                                        key="lead_logs"
                                        pageName="lead_action"
                                        logType="message"
                                        :leadId="leadDetails.xid"
                                        :showLeadDetails="false"
                                        :showTableSearch="false"
                                        :showUserFilter="false"
                                        :showDateFilter="false"
                                    />
                                </a-tab-pane>
                                <a-tab-pane key="uphone_calls">
                                    <template #tab>
                                        <span>
                                            <MobileOutlined />
                                            {{ $t("common.uphone_calls") }}
                                        </span>
                                    </template>
                                    <UphoneCallTable
                                        :leadId="leadDetails.xid"
                                        :visible="true"
                                    />
                                </a-tab-pane>
                                <a-tab-pane key="email_history">
                                    <template #tab>
                                        <span>
                                            <IeOutlined />
                                            {{ $t("common.email_history") }}
                                        </span>
                                    </template>
                                    <LeadLogTable
                                        key="lead_logs"
                                        pageName="lead_action"
                                        logType="email"
                                        :leadId="leadDetails.xid"
                                        :showLeadDetails="false"
                                        :showTableSearch="false"
                                        :showUserFilter="false"
                                        :showDateFilter="false"
                                    />
                                </a-tab-pane>
                            </a-tabs>
                        </a-card>
                    </a-col>
                    <a-col
                        :xs="24"
                        :sm="24"
                        :md="24"
                        :lg="6"
                        :xl="6"
                        class="bg-setting-sidebar"
                        :style="
                            themeMode == 'dark'
                                ? { background: '#141414 !important' }
                                : { background: '#fff !important' }
                        "
                    >
                        <a-card
                            :bordered="false"
                            class="callmanager-right-sidebar"
                            :bodyStyle="{ padding: '15px' }"
                        >
                            <template #title>
                                <a-typography-title :level="5" type="success" strong>
                                    {{ $t("lead.lead_history") }}
                                </a-typography-title>
                            </template>
                            <a-skeleton v-if="newPageLoad" active />
                            <LogTimeline
                                v-else
                                :leadId="leadDetails.xid"
                                :refresh="refreshTimeLine"
                                @dataFetched="() => (refreshTimeLine = false)"
                            />
                        </a-card>
                    </a-col>
                </a-row>
            </a-col>
            <!-- <a-col :span="4" v-show="rightSidebarValue">
                <Sidebar :leadId="route.params.id" />
            </a-col> -->
        </a-row>

        <SkipLeadModal
            v-if="leadDetails && leadDetails.xid"
            :leadId="leadDetails.xid"
            :visible="showSkipModal"
            @onSkipDelete="skipDeleteSuccess"
            @onSkip="skipSuccess"
            @close="() => (showSkipModal = false)"
        />
    </a-layout>
</template>

<script>
import { onMounted, ref, createVNode, watch } from "vue";
import {
    SaveOutlined,
    DoubleRightOutlined,
    ArrowRightOutlined,
    ArrowLeftOutlined,
    SearchOutlined,
    ClockCircleOutlined,
    ScheduleOutlined,
    FileTextOutlined,
    PhoneOutlined,
    ShoppingCartOutlined,
    ExclamationCircleOutlined,
    CheckOutlined,
    DeliveredProcedureOutlined,
    MailOutlined,
    MobileOutlined,
    StepBackwardOutlined,
    StepForwardOutlined,
    MessageOutlined,
    IeOutlined,
} from "@ant-design/icons-vue";
import { Modal, notification } from "ant-design-vue";
import { useRoute, useRouter } from "vue-router";
import { forEach, find } from "lodash-es";
import { useStopwatch, useTimer } from "vue-timer-hook";
import { useI18n } from "vue-i18n";
import { useStore } from "vuex";
import apiAdmin from "../../../common/composable/apiAdmin";
import common from "../../../common/composable/common";
import BookingModal from "../bookings/BookingModal.vue";
import LeadLogTable from "../../components/lead-logs/index.vue";
import LogTimeline from "../../components/lead-logs/LogTimeline.vue";
import LeadNotesTable from "../../components/lead-notes/index.vue";
import SkipLeadModal from "./SkipLeadModal.vue";
import SendMail from "./SendMail.vue";
import SendMessage from "./SendMessage.vue";
import Sidebar from "../../../common/layouts/Sidebar.vue";
import SendCall from "./SendCall.vue";
import UphoneCallTable from "./UphoneCallsHistory.vue";

export default {
    components: {
        SaveOutlined,
        DoubleRightOutlined,
        ArrowRightOutlined,
        ArrowLeftOutlined,
        ScheduleOutlined,
        FileTextOutlined,
        PhoneOutlined,
        ShoppingCartOutlined,
        ExclamationCircleOutlined,
        CheckOutlined,
        DeliveredProcedureOutlined,
        MailOutlined,
        MobileOutlined,
        StepBackwardOutlined,
        ClockCircleOutlined,
        SearchOutlined,
        StepForwardOutlined,
        MessageOutlined,
        IeOutlined,
        BookingModal,
        LeadLogTable,
        LeadNotesTable,
        LogTimeline,
        SkipLeadModal,
        SendMail,
        SendMessage,
        Sidebar,
        SendCall,
        UphoneCallTable,
    },
    setup() {
        const { formatDateTime, rightSidebarValue, themeMode } = common();
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const route = useRoute();
        const router = useRouter();
        const store = useStore();
        const leadDetails = ref({});
        const leadCallLogDetails = ref({});
        const activeKey = ref("lead_details");
        const activeLeftPanelKey = ref("lead_details");
        const leadFormData = ref({});
        const timer = useStopwatch(0, true);
        const { t } = useI18n();
        const referenceNumber = ref("");
        const leadFollowUp = ref({});
        const salesmanBooking = ref({});
        const leadNumber = ref(0);
        const newPageLoad = ref(true);
        const refreshTimeLine = ref(false);
        const autoSaved = ref(false);
        const saveLoading = ref(false);
        const saveExitLoading = ref(false);
        const showSkipModal = ref(false);
        const allLeadStatus = ref([]);
        const leadStatusId = ref(undefined);
        const uPhoneCampaignsLists = ref(undefined);
        const uPhoneEmailCampaignsLists = ref(undefined);

        onMounted(() => {
            fetchInitData();
        });

        const openRightSidebar = () => {
            store.commit("auth/updateRightSidebarValue", !rightSidebarValue.value);
        };

        const fetchInitData = () => {
            const leadId = route.params.id;
            const campaignUrl =
                "campaign{id,xid,name,status,remaining_leads,total_leads,form_id,x_form_id,email_template_id,x_email_template_id,detail_fields,last_action_by,x_last_action_by,completed_by,x_completed_by,started_on,completed_on,upcoming_lead_action},campaign:campaignUsers{id,xid,user_id,x_user_id,campaign_id,x_campaign_id},campaign:campaignUsers:user{id,xid,name,profile_image,profile_image_url},campaign:emailTemplate{id,xid,name},campaign:lastActioner{id,xid,name},campaign:completedBy{id,xid,name}";
            const leadDetailsUrl = `leads/${leadId}?fields=id,xid,reference_number,lead_data,started,time_taken,last_action_by,x_last_action_by,lastActioner{id,xid,name},campaign_id,x_campaign_id,${campaignUrl},leadFollowUp{id,xid,log_type,user_id,x_user_id,date_time,notes},leadFollowUp:user{id,xid,name},salesmanBooking{id,xid,log_type,user_id,x_user_id,date_time,notes},salesmanBooking:user{id,xid,name},lead_status_id,x_lead_status_id`;// ,campaign:form{id,xid,name,form_fields}
            leadDetails.value = {};
            leadCallLogDetails.value = {};
            activeKey.value = "lead_details";
            activeLeftPanelKey.value = "lead_details";
            leadFormData.value = {};
            leadFollowUp.value = {};
            salesmanBooking.value = {};
            referenceNumber.value = "";
            leadStatusId.value = undefined;
            leadNumber.value = 0;
            newPageLoad.value = true;

            const leadDetailsPromise = axiosAdmin.get(leadDetailsUrl);
            const leadCallLogPromise = axiosAdmin.get(`leads/create-call-log/${leadId}`);
            const leadStatusPromise = axiosAdmin.get(
                `lead-status?fields=id,xid,name,color,type`
            );
            const uphoneCampaignsPromise = axiosAdmin.post(`leads/uphone-campaigns`);

            Promise.all([
                leadDetailsPromise,
                leadCallLogPromise,
                leadStatusPromise,
                uphoneCampaignsPromise,
            ]).then(
                ([
                    leadDetailsResponse,
                    leadCallLogResponse,
                    leadStatusResponse,
                    uphoneCampaignsResponse,
                ]) => {
                    var leadResult = leadDetailsResponse.data;
                    allLeadStatus.value = leadStatusResponse.data;
                    uPhoneCampaignsLists.value = uphoneCampaignsResponse.data.campaigns;
                    uPhoneEmailCampaignsLists.value =
                        uphoneCampaignsResponse.data.email_campaigns;

                    leadNumber.value = leadCallLogResponse.data.lead_number;
                    leadCallLogDetails.value = leadCallLogResponse.data.call_log;
                    leadDetails.value = leadResult;

                    var newLeadDataArray = [];
                    if (leadResult.lead_data && leadResult.lead_data.length > 0) {
                        forEach(leadResult.lead_data, (fieldData) => {
                            newLeadDataArray.push({
                                id: fieldData.id,
                                field_name: fieldData.field_name,
                                field_value: fieldData.field_value,
                            });
                        });
                    }

                    if (
                        leadResult.campaign &&
                        leadResult.campaign.form &&
                        leadResult.campaign.form.form_fields &&
                        leadResult.campaign.form.form_fields.length > 0
                    ) {
                        forEach(
                            leadResult.campaign.form.form_fields,
                            (leadFormFields) => {
                                var newResult = find(newLeadDataArray, {
                                    field_name: leadFormFields.name,
                                });

                                if (newResult == undefined) {
                                    newLeadDataArray.push({
                                        id: Math.random().toString(36).slice(2),
                                        field_name: leadFormFields.name,
                                        field_value: "",
                                    });
                                }
                            }
                        );
                    }

                    leadFormData.value = {
                        campaign_id: leadResult.x_campaign_id,
                        lead_data: newLeadDataArray,
                    };
                    referenceNumber.value = leadResult.reference_number;

                    leadFollowUp.value = leadResult.lead_follow_up
                        ? leadResult.lead_follow_up
                        : [];
                    salesmanBooking.value = leadResult.salesman_booking
                        ? leadResult.salesman_booking
                        : [];
                    leadStatusId.value = leadResult.x_lead_status_id;

                    timer.reset(leadResult.time_taken, true);

                    newPageLoad.value = false;
                }
            );

        };

        const calculateTotalTimeInSeconds = () => {
            var minutesInSeconds = timer.minutes.value * 60;
            var hoursInSeconds = timer.hours.value * 60 * 60;
            var daysInSeconds = timer.days.value * 24 * 60 * 60;

            return (
                timer.seconds.value + minutesInSeconds + hoursInSeconds + daysInSeconds
            );
        };

        const saveAndExit = () => {
            saveExitLoading.value = true;

            Modal.confirm({
                title: t("common.are_you_sure") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t("lead.are_you_want_go_to_save_exit"),
                centered: true,
                okText: t("common.yes"),
                okType: "danger",
                cancelText: t("common.no"),
                onOk() {
                    saveLead("save_exit");
                },
                onCancel() {
                    saveExitLoading.value = false;
                },
            });
        };

        const saveLead = (saveType = "auto") => {
            if (saveType == "save") {
                saveLoading.value = true;
            } else if (saveType == "save_exit") {
                saveExitLoading.value = true;
            }

            addEditRequestAdmin({
                url: `campaigns/update-actioned-lead`,
                data: {
                    ...leadFormData.value,
                    reference_number: referenceNumber.value,
                    lead_status_id: leadStatusId.value,
                    call_log_id: leadCallLogDetails.value.xid,
                    call_time: calculateTotalTimeInSeconds(),
                    x_lead_id: route.params.id,
                },
                success: (res) => {
                    autoSaved.value = true;
                    saveLoading.value = false;

                    if (saveType == "save_exit") {
                        saveExitLoading.value = false;

                        router.push({
                            name: "admin.call_manager.index",
                        });

                        // store.dispatch("auth/showNotificaiton", {});
                    }
                },
            });
        };

        const takeLeadAction = (actionName) => {
            var contentLangText = "";
            if (actionName == "next") {
                contentLangText = t("lead.are_you_want_go_to_next_lead");
            } else if (actionName == "previous") {
                contentLangText = t("lead.are_you_want_go_to_previous_lead");
            }

            Modal.confirm({
                title: t("common.are_you_sure") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: contentLangText,
                centered: true,
                okText: t("common.yes"),
                okType: "danger",
                cancelText: t("common.no"),
                onOk() {
                    nextPreviousLead(actionName);
                },
                onCancel() {},
            });
        };

        const skipSuccess = () => {
            showSkipModal.value = false;
            nextPreviousLead("skip-next");
        };

        const nextPreviousLead = (actionName) => {
            var newActionName = actionName == "skip-next" ? "next" : actionName;

            addEditRequestAdmin({
                url: `campaigns/take-lead-action`,
                data: {
                    ...leadFormData.value,
                    reference_number: referenceNumber.value,
                    call_log_id: leadCallLogDetails.value.xid,
                    call_time: calculateTotalTimeInSeconds(),
                    action_type: newActionName,
                    x_lead_id: route.params.id,
                },
                success: (res) => {
                    if (res && res.lead && res.lead.xid) {
                        notification.success({
                            message: t("common.success"),
                            description: t(`lead.you_are_on_${newActionName}_lead`),
                            placement: "bottomRight",
                        });

                        router.push({
                            name: "admin.call_manager.take_action",
                            params: { id: res.lead.xid },
                        });
                    } else {
                        // TODO - No lead exists redirect to campaign page
                        // with No Lead exists message

                        Modal.confirm({
                            title: t("lead.no_lead_exists") + "?",
                            icon: createVNode(ExclamationCircleOutlined),
                            content: t("lead.no_lead_exist_message"),
                            centered: true,
                            okText: t("campaign.save_exit"),
                            okType: "danger",
                            cancelText: t("common.continue"),
                            onOk() {
                                saveLead("save_exit");
                            },
                            onCancel() {
                                saveLead("save");
                            },
                        });
                    }
                },
            });
        };

        const skipDeleteSuccess = (lead) => {
            showSkipModal.value = false;

            if (lead && lead.xid) {
                router.push({
                    name: "admin.call_manager.take_action",
                    params: { id: lead.xid },
                });
            } else {
                router.push({
                    name: "admin.call_manager.index",
                });
            }
        };

        const getLeadDataFieldType = (fieldName) => {
            var fieldType = "text";

            if (
                leadDetails.value.campaign &&
                leadDetails.value.campaign.form &&
                leadDetails.value.campaign.form.form_fields &&
                leadDetails.value.campaign.form.form_fields.length > 0
            ) {
                var newResult = find(leadDetails.value.campaign.form.form_fields, {
                    name: fieldName,
                });

                if (newResult && newResult.type) {
                    fieldType = newResult.type;
                }
            }

            return fieldType;
        };

        const skipLead = () => {
            showSkipModal.value = true;
        };

        watch(timer.seconds, (newVal, oldVal) => {
            if (timer.seconds.value % 5 == 0) {
                saveLead();
            }
        });

        watch(route, (newVal, oldVal) => {
            if (newVal.params.id != undefined) {
                fetchInitData();
            }
        });

        watch(autoSaved, (newVal, oldVal) => {
            if (newVal) {
                setTimeout(() => (autoSaved.value = false), 3000);
            }
        });

        return {
            themeMode,
            activeLeftPanelKey,
            leadDetails,
            activeKey,
            leadFormData,
            referenceNumber,
            leadFollowUp,
            salesmanBooking,
            loading,
            rules,

            formatDateTime,
            timer,
            leadNumber,
            newPageLoad,

            takeLeadAction,
            refreshTimeLine,

            saveAndExit,
            saveLead,
            saveExitLoading,
            saveLoading,
            autoSaved,

            skipLead,
            showSkipModal,
            skipDeleteSuccess,
            skipSuccess,

            getLeadDataFieldType,
            allLeadStatus,
            leadStatusId,
            openRightSidebar,
            rightSidebarValue,
            route,
            uPhoneCampaignsLists,
            uPhoneEmailCampaignsLists,
            fetchInitData,
        };
    },
};
</script>

<style scoped>
.callmanager-left-sidebar {
    height: calc(100vh - 90px);
}

.callmanager-left-sidebar .ps {
    height: calc(100vh - 270px);
}

.callmanager-middle-sidebar {
    height: calc(100vh - 90px);
}

.callmanager-middle-sidebar .ps {
    height: calc(100vh - 235px);
}

.callmanager-right-sidebar {
    height: calc(100vh - 99px);
}
</style>
