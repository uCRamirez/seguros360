<template>
    <a-layout
        :style="
            themeMode == 'dark'
                ? { background: '#141414', height: '100vh' }
                : { height: '100vh' }
        "
    >

                <a-row >
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

                                <!-- <a-row class="mt-10">
                                    <a-col :span="24" style="margin-top: 1em;">
                                        <a-space>
                                            <a-button
                                                :style="{
                                                    background: '#faad14',
                                                    borderColor: '#faad14',
                                                    width: '10em'
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
                                                    width: '11.5em'
                                                }"
                                                type="primary"
                                                @click="takeLeadAction('next')"
                                            >
                                                {{ $t("campaign.next_lead") }}
                                                <ArrowRightOutlined />
                                            </a-button>
                                        </a-space>
                                    </a-col>
                                    
                                </a-row> -->

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
                                    <!-- campaign details -->
                                    <a-collapse-panel
                                        key="campaign_details"
                                        :style="{
                                            background:
                                                themeMode == 'dark' ? '#141414' : '#fff',
                                        }"
                                    >
                                        <template #header>
                                            <a-typography-title :level="5">
                                                {{ $t("campaign.campaign_details") + ' : ' +  crmState.client.campaign.name }}
                                            </a-typography-title>
                                        </template>
                                        <a-row
                                            v-for="(
                                                campaignDetails, campaignDetailsKey
                                            ) in crmState.client.campaign.detail_fields"
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
                                                <a-typography-text v-if="campaignDetails.field_type != 'link'">
                                                    {{ campaignDetails.field_value }}
                                                </a-typography-text>
                                                <a-typography-text v-else>
                                                    <a
                                                    :href="campaignDetails.field_value"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    >
                                                    {{ campaignDetails.field_value }}
                                                    </a>
                                                </a-typography-text>
                                            </a-col>
                                        </a-row>
                                    </a-collapse-panel>
                                    <!-- Lead history -->
                                    <a-card
                                        :bordered="true"
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
                                            :key="crmState.client.xid"
                                            :leadId="crmState.client.xid"
                                            :refresh="refreshTimeLine"
                                            @dataFetched="() => (refreshTimeLine = false)"
                                        />
                                    </a-card>
                                    
                                </a-collapse>
                            </perfect-scrollbar>
                        </div>
                    </a-col>
                    <a-col :xs="24" :sm="24" :md="24" :lg="18" :xl="18">
                        <a-card class="callmanager-middle-sidebar">
                            <a-tabs v-model:activeKey="activeKey">
                                
                                <!-- Tab con informacion del lead -->
                                <a-tab-pane key="lead_details">
                                    <template #tab>
                                        <span>
                                            <FileTextOutlined />
                                            {{ $t("lead.mfisico") }}
                                        </span>
                                    </template>
                                    <perfect-scrollbar
                                        :options="{
                                            wheelSpeed: 1,
                                            swipeEasing: true,
                                            suppressScrollX: true,
                                        }"
                                    >
                                        <a-form layout="vertical" class="mt-10">
                                            <a-row :gutter="10">
                                                <!-- documento -->
                                                <a-col
                                                    :xs="24"
                                                    :sm="24"
                                                    :md="12"
                                                    :lg="12"
                                                >
                                                    <a-form-item
                                                        :label="$t('lead.document')"
                                                        name="cedula"
                                                        :help="rules.cedula ? rules.cedula.message : null"
                                                        :validateStatus="rules.cedula ? 'error' : ''"
                                                        class="required"
                                                    >
                                                        <a-input-group compact>
                                                            <a-input
                                                                :disabled="!permsArray.includes('admin')"
                                                                v-model:value="
                                                                    crmState.client.cedula
                                                                "
                                                                :placeholder="
                                                                    $t(
                                                                        'common.placeholder_default_text',
                                                                        [
                                                                            $t(
                                                                                'lead.document'
                                                                            ),
                                                                        ]
                                                                    )
                                                                "
                                                            />
                                                            <!-- <SearchLead /> style="width: calc(100% - 35px);" -->
                                                        </a-input-group>
                                                        
                                                    </a-form-item>
                                                </a-col>
                                                <!-- lead status -->
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
                                                                v-model:value="leadStatusModel"
                                                                show-search
                                                                option-filter-prop="title"
                                                                :allowClear="true"
                                                                :placeholder="$t( 'common.select_default_text',[$t('lead.lead_status' ),])"
                                                            >
                                                                <a-select-option
                                                                    v-for="status in allLeadStatus.filter(s => s.type === 'lead_status')"
                                                                    :key="status.id"
                                                                    :value="status.id"
                                                                    :title="status.name"
                                                                >
                                                                    {{ status.name }}
                                                                </a-select-option>
                                                            </a-select>
                                                        </a-form-item>
                                                </a-col>
                                                <!-- nombre -->
                                                <a-col
                                                    :xs="24"
                                                    :sm="24"
                                                    :md="12"
                                                    :lg="12"
                                                >
                                                    <a-form-item
                                                        :label="
                                                            $t('lead.name')
                                                        "
                                                        name="name"
                                                        :help="
                                                            rules.name
                                                                ? rules.name
                                                                      .message
                                                                : null
                                                        "
                                                        :validateStatus="
                                                            rules.name
                                                                ? 'error'
                                                                : null
                                                        "
                                                    >
                                                        <a-input
                                                            :disabled="!permsArray.includes('admin')"
                                                            v-model:value="
                                                                crmState.client.nombre
                                                            "
                                                            :placeholder="
                                                                $t(
                                                                    'common.placeholder_default_text',
                                                                    [
                                                                        $t(
                                                                            'lead.name'
                                                                        ),
                                                                    ]
                                                                )
                                                            "
                                                        />
                                                    </a-form-item>
                                                </a-col>
                                                <!-- apellido 1 -->
                                                <a-col
                                                    :xs="24"
                                                    :sm="24"
                                                    :md="12"
                                                    :lg="12"
                                                >
                                                    <a-form-item
                                                        :label="
                                                            $t('lead.first_last_name')
                                                        "
                                                        name="first_last_name"
                                                        :help="
                                                            rules.first_last_name
                                                                ? rules.first_last_name
                                                                      .message
                                                                : null
                                                        "
                                                        :validateStatus="
                                                            rules.first_last_name
                                                                ? 'error'
                                                                : null
                                                        "
                                                    >
                                                        <a-input
                                                            :disabled="!permsArray.includes('admin')"
                                                            v-model:value="
                                                                crmState.client.apellido1
                                                            "
                                                            :placeholder="
                                                                $t(
                                                                    'common.placeholder_default_text',
                                                                    [
                                                                        $t(
                                                                            'lead.first_last_name'
                                                                        ),
                                                                    ]
                                                                )
                                                            "
                                                        />
                                                    </a-form-item>
                                                </a-col>
                                                <!-- apellido 2 -->
                                                <a-col
                                                    :xs="24"
                                                    :sm="24"
                                                    :md="12"
                                                    :lg="12"
                                                >
                                                    <a-form-item
                                                        :label="
                                                            $t('lead.second_last_name')
                                                        "
                                                        name="second_last_name"
                                                        :help="
                                                            rules.second_last_name
                                                                ? rules.second_last_name
                                                                      .message
                                                                : null
                                                        "
                                                        :validateStatus="
                                                            rules.second_last_name
                                                                ? 'error'
                                                                : null
                                                        "
                                                    >
                                                        <a-input
                                                            :disabled="!permsArray.includes('admin')"
                                                            v-model:value="
                                                                crmState.client.apellido2
                                                            "
                                                            :placeholder="
                                                                $t(
                                                                    'common.placeholder_default_text',
                                                                    [
                                                                        $t(
                                                                            'lead.second_last_name'
                                                                        ),
                                                                    ]
                                                                )
                                                            "
                                                        />
                                                    </a-form-item>
                                                </a-col>
                                                <!-- estado civil -->
                                                <a-col
                                                    :xs="24"
                                                    :sm="24"
                                                    :md="12"
                                                    :lg="12"
                                                >
                                                    <a-form-item
                                                        :label="$t('lead.marital_status')"
                                                        name="marital_status"
                                                        :help="
                                                            rules.marital_status
                                                                ? rules.marital_status
                                                                      .message
                                                                : null
                                                        "
                                                        :validateStatus="
                                                            rules.marital_status
                                                                ? 'error'
                                                                : null
                                                        "
                                                    >
                                                        <a-select
                                                            v-model:value="maritalStatusModel"
                                                            show-search
                                                            option-filter-prop="title"
                                                            :allowClear="true"
                                                            :placeholder="
                                                                $t(
                                                                    'common.select_default_text',
                                                                    [
                                                                        $t(
                                                                            'lead.marital_status'
                                                                        ),
                                                                    ]
                                                                )
                                                            "
                                                        >
                                                            <!-- llenar este select con los estados civiles-->
                                                            <a-select-option
                                                                v-for="status in allLeadStatus.filter(s => s.type === 'marital_status')"
                                                                :key="status.xid"
                                                                :value="status.id"
                                                                :title="status.name"
                                                            >
                                                                {{ status.name }}
                                                            </a-select-option>
                                                        </a-select>
                                                    </a-form-item>
                                                </a-col>
                                                <!-- hijos -->
                                                <a-col
                                                    :xs="24"
                                                    :sm="24"
                                                    :md="12"
                                                    :lg="12"
                                                >
                                                    <a-form-item
                                                        :label="
                                                            $t('lead.children')
                                                        "
                                                        name="children"
                                                        :help="
                                                            rules.children
                                                                ? rules.children
                                                                      .message
                                                                : null
                                                        "
                                                        :validateStatus="
                                                            rules.children
                                                                ? 'error'
                                                                : null
                                                        "
                                                    >
                                                        <a-input-number
                                                            v-model:value="
                                                                crmState.client.hijos
                                                            "
                                                            style="width: 100%"
                                                        />
                                                    </a-form-item>
                                                </a-col>
                                                <!-- fecha nacimiento y edad -->
                                                <a-col
                                                    :xs="24"
                                                    :sm="24"
                                                    :md="12"
                                                    :lg="12"
                                                    style="display: flex; gap: 16px;" 
                                                >
                                                    <div style="width: 50%;">
                                                        <a-form-item
                                                            :label="
                                                                $t('lead.date_birth')
                                                            "
                                                            name="date_birth"
                                                            :help="
                                                                rules.date_birth
                                                                    ? rules.date_birth
                                                                        .message
                                                                    : null
                                                            "
                                                            :validateStatus="
                                                                rules.date_birth
                                                                    ? 'error'
                                                                    : null
                                                            "
                                                        >   
                                                            <a-date-picker format="YYYY-MM-DD" value-format="YYYY-MM-DD" @change="calcularEdad" v-model:value="crmState.client.fechaNacimiento" style="width: 100%"/>
                                                        </a-form-item>
                                                    </div>
                                                    
                                                    <div style="width: 50%;">
                                                        <a-form-item
                                                            :label="
                                                                $t('lead.age')
                                                            "
                                                            name="age"
                                                            :help="
                                                                rules.age
                                                                    ? rules.age
                                                                        .message
                                                                    : null
                                                            "
                                                            :validateStatus="
                                                                rules.age
                                                                    ? 'error'
                                                                    : null
                                                            "
                                                            style="margin-left: auto;"
                                                        >
                                                            <a-input-number
                                                                v-model:value="
                                                                    crmState.client.edad
                                                                "
                                                                style="width: 100%"
                                                            />
                                                        </a-form-item>
                                                    </div>
                                                    
                                                    
                                                </a-col>
                                                <!-- nacionalidad -->
                                                <a-col
                                                    :xs="24"
                                                    :sm="24"
                                                    :md="12"
                                                    :lg="12"
                                                >
                                                    <a-form-item
                                                        :label="
                                                            $t('lead.nationality')
                                                        "
                                                        name="nationality"
                                                        :help="
                                                            rules.nationality
                                                                ? rules.nationality
                                                                      .message
                                                                : null
                                                        "
                                                        :validateStatus="
                                                            rules.nationality
                                                                ? 'error'
                                                                : null
                                                        "
                                                    >
                                                        <a-input
                                                            v-model:value="
                                                                crmState.client.nacionalidad
                                                            "
                                                            :placeholder="
                                                                $t(
                                                                    'common.placeholder_default_text',
                                                                    [
                                                                        $t(
                                                                            'lead.nationality'
                                                                        ),
                                                                    ]
                                                                )
                                                            "
                                                        />
                                                    </a-form-item>
                                                </a-col>
                                                <!-- Tel1 -->
                                                <a-col
                                                    :xs="24"
                                                    :sm="24"
                                                    :md="12"
                                                    :lg="12"
                                                >
                                                    <a-form-item
                                                        :label="`${$t('lead.phone')} 1`"
                                                        name="tel1"
                                                        :help="
                                                            rules.phone1
                                                                ? rules.phone1
                                                                      .message
                                                                : null
                                                        "
                                                        :validateStatus="
                                                            rules.phone1
                                                                ? 'error'
                                                                : null
                                                        "
                                                        class="required"
                                                    >
                                                        <a-input
                                                            :disabled="!permsArray.includes('admin')"
                                                            v-model:value="
                                                                crmState.client.tel1
                                                            "
                                                            :placeholder="
                                                                $t(
                                                                    'common.placeholder_default_text',
                                                                    [
                                                                        $t(
                                                                            'lead.phone'
                                                                        ),
                                                                    ]
                                                                )
                                                            "
                                                        />
                                                    </a-form-item>
                                                </a-col>
                                                <!-- Tel2 -->
                                                <a-col
                                                    :xs="24"
                                                    :sm="24"
                                                    :md="12"
                                                    :lg="12"
                                                >
                                                    <a-form-item
                                                        :label="`${$t('lead.phone')} 2`"
                                                        name="phone2"
                                                        :help="
                                                            rules.phone2
                                                                ? rules.phone2
                                                                      .message
                                                                : null
                                                        "
                                                        :validateStatus="
                                                            rules.phone2
                                                                ? 'error'
                                                                : null
                                                        "
                                                    >
                                                        <a-input
                                                            :disabled="!permsArray.includes('admin')"
                                                            v-model:value="
                                                                crmState.client.tel2
                                                            "
                                                            :placeholder="
                                                                $t(
                                                                    'common.placeholder_default_text',
                                                                    [
                                                                        $t(
                                                                            'lead.phone'
                                                                        ),
                                                                    ]
                                                                )
                                                            "
                                                        />
                                                    </a-form-item>
                                                </a-col>
                                                <!-- Tel3 -->
                                                <a-col
                                                    :xs="24"
                                                    :sm="24"
                                                    :md="12"
                                                    :lg="12"
                                                >
                                                    <a-form-item
                                                        :label="`${$t('lead.phone')} 3`"
                                                        name="phone3"
                                                        :help="
                                                            rules.phone3
                                                                ? rules.phone3
                                                                      .message
                                                                : null
                                                        "
                                                        :validateStatus="
                                                            rules.phone3
                                                                ? 'error'
                                                                : null
                                                        "
                                                    >
                                                        <a-input
                                                            :disabled="!permsArray.includes('admin')"
                                                            v-model:value="
                                                                crmState.client.tel3
                                                            "
                                                            :placeholder="
                                                                $t(
                                                                    'common.placeholder_default_text',
                                                                    [
                                                                        $t(
                                                                            'lead.phone'
                                                                        ),
                                                                    ]
                                                                )
                                                            "
                                                        />
                                                    </a-form-item>
                                                </a-col>
                                                <!-- Tel4 -->
                                                <a-col
                                                    :xs="24"
                                                    :sm="24"
                                                    :md="12"
                                                    :lg="12"
                                                >
                                                    <a-form-item
                                                        :label="`${$t('lead.phone')} 4`"
                                                        name="phone4"
                                                        :help="
                                                            rules.phone4
                                                                ? rules.phone4
                                                                      .message
                                                                : null
                                                        "
                                                        :validateStatus="
                                                            rules.phone4
                                                                ? 'error'
                                                                : null
                                                        "
                                                    >
                                                        <a-input
                                                            :disabled="!permsArray.includes('admin')"
                                                            v-model:value="
                                                                crmState.client.tel4
                                                            "
                                                            :placeholder="
                                                                $t(
                                                                    'common.placeholder_default_text',
                                                                    [
                                                                        $t(
                                                                            'lead.phone'
                                                                        ),
                                                                    ]
                                                                )
                                                            "
                                                        />
                                                    </a-form-item>
                                                </a-col>
                                                <!-- Tel5 -->
                                                <a-col
                                                    :xs="24"
                                                    :sm="24"
                                                    :md="12"
                                                    :lg="12"
                                                >
                                                    <a-form-item
                                                        :label="`${$t('lead.phone')} 5`"
                                                        name="phone5"
                                                        :help="
                                                            rules.phone5
                                                                ? rules.phone5
                                                                      .message
                                                                : null
                                                        "
                                                        :validateStatus="
                                                            rules.phone5
                                                                ? 'error'
                                                                : null
                                                        "
                                                    >
                                                        <a-input
                                                            :disabled="!permsArray.includes('admin')"
                                                            v-model:value="
                                                                crmState.client.tel5
                                                            "
                                                            :placeholder="
                                                                $t(
                                                                    'common.placeholder_default_text',
                                                                    [
                                                                        $t(
                                                                            'lead.phone'
                                                                        ),
                                                                    ]
                                                                )
                                                            "
                                                        />
                                                    </a-form-item>
                                                </a-col>
                                                <!-- Tel6 -->
                                                <a-col
                                                    :xs="24"
                                                    :sm="24"
                                                    :md="12"
                                                    :lg="12"
                                                >
                                                    <a-form-item
                                                        :label="`${$t('lead.phone')} 6`"
                                                        name="phone6"
                                                        :help="
                                                            rules.phone6
                                                                ? rules.phone6
                                                                      .message
                                                                : null
                                                        "
                                                        :validateStatus="
                                                            rules.phone6
                                                                ? 'error'
                                                                : null
                                                        "
                                                    >
                                                        <a-input
                                                            :disabled="!permsArray.includes('admin')"
                                                            v-model:value="
                                                                crmState.client.tel6
                                                            "
                                                            :placeholder="
                                                                $t(
                                                                    'common.placeholder_default_text',
                                                                    [
                                                                        $t(
                                                                            'lead.phone'
                                                                        ),
                                                                    ]
                                                                )
                                                            "
                                                        />
                                                    </a-form-item>
                                                </a-col>
                                                 <!-- email -->
                                                 <a-col
                                                    :xs="24"
                                                    :sm="24"
                                                    :md="12"
                                                    :lg="12"
                                                >
                                                    <a-form-item
                                                        :label="
                                                            $t('lead.email')
                                                        "
                                                        name="email"
                                                        :help="
                                                            rules.email
                                                                ? rules.email
                                                                      .message
                                                                : null
                                                        "
                                                        :validateStatus="
                                                            rules.email
                                                                ? 'error'
                                                                : null
                                                        "
                                                    >
                                                        <a-input
                                                            v-model:value="
                                                                crmState.client.email
                                                            "
                                                            :placeholder="
                                                                $t(
                                                                    'common.placeholder_default_text',
                                                                    [
                                                                        $t(
                                                                            'lead.email'
                                                                        ),
                                                                    ]
                                                                )
                                                            "
                                                        />
                                                    </a-form-item>
                                                </a-col>
                                                <!-- Provincia - Canton - Distrito -->
                                                <a-col :xxl="24" :xl="24" :xs="24" style="display: flex;">
                                                    <!-- Provincia -->
                                                    <a-col :xxl="8" :xs="8" :sm="8" :md="8" :lg="8">
                                                        <a-form-item :label="$t('lead.province')">
                                                            <a-select 
                                                                v-model:value="crmState.client.provincia" 
                                                                show-search
                                                                option-filter-prop="title"
                                                                :allowClear="true"
                                                                :placeholder="$t('common.select_default_text',[$t( 'lead.province'),])
                                                            ">
                                                                <a-select-option
                                                                    v-for="prov in provinceOptions"
                                                                    :key="prov"
                                                                    :value="prov"
                                                                    :title="prov" 
                                                                >{{ prov }}</a-select-option>
                                                                </a-select>
                                                        </a-form-item>
                                                    </a-col>

                                                    
                                                    <!-- Cantón -->
                                                    <a-col :xxl="8" :xs="8" :sm="8" :md="8" :lg="8">
                                                        <a-form-item :label="$t('lead.canton')">
                                                            <a-select
                                                                v-model:value="crmState.client.canton"
                                                                show-search
                                                                option-filter-prop="title"
                                                                :allowClear="true"
                                                                :placeholder="$t('common.select_default_text', [$t('lead.canton')])"
                                                                :disabled="!crmState.client.provincia"
                                                            >
                                                                <a-select-option
                                                                    v-for="ct in cantonOptions"
                                                                    :key="ct"
                                                                    :value="ct"
                                                                    :title="ct" 
                                                                >{{ ct }}</a-select-option>
                                                                </a-select>
                                                        </a-form-item>
                                                    </a-col>

                                                    <!-- Distrito -->
                                                    <a-col :xxl="8" :xs="8" :sm="8" :md="8" :lg="8">
                                                        <a-form-item :label="$t('lead.district')">
                                                            <a-select
                                                                v-model:value="crmState.client.distrito"
                                                                show-search
                                                                option-filter-prop="title"
                                                                :allowClear="true"
                                                                :placeholder="$t('common.select_default_text', [$t('lead.district')])"
                                                                :disabled="!crmState.client.canton || !crmState.client.provincia"
                                                            >
                                                                <a-select-option
                                                                    v-for="dist in districtOptions"
                                                                    :key="dist"
                                                                    :value="dist"
                                                                    :title="dist" 
                                                                >{{ dist }}</a-select-option>
                                                                </a-select>
                                                        </a-form-item>
                                                    </a-col>
                                                </a-col>
                                            </a-row>
                                        </a-form>
                                    </perfect-scrollbar>
                                    <div
                                        :style="{
                                            textAlign: 'center',
                                            right: 0,
                                            bottom: 0,
                                            width: '100%',
                                            borderTop: '1px solid #e9e9e9',
                                            padding: '10px',
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
                                                        :disabled="crmState.client.managing === false"
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
                                                        :disabled="crmState.client.managing === false"
                                                    >
                                                        <template #icon>
                                                            <DeliveredProcedureOutlined />
                                                        </template>
                                                        {{ $t("campaign.save_exit") }}
                                                    </a-button>
                                                    <a-button
                                                        type="primary"
                                                        @click="addNote"
                                                        :disabled="crmState.client.managing === false"
                                                    >
                                                        <template #icon>
                                                            <PlusOutlined />
                                                        </template>
                                                        {{ $t("notes.add") }}
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
                                                        {{ $t("lead.auto_save") }}
                                                    </a-typography-text>
                                                </a-button>
                                            </a-col>
                                        </a-row>
                                    </div>
                                </a-tab-pane>
                                <!-- FIN Tab con informacion del lead -->

                                <!-- Tab de busqueda de leads -->
                                <a-tab-pane key="search_lead">
                                    <template #tab>
                                        <span>
                                            <SearchOutlined />
                                            {{ $t("menu.search_lead") }}
                                        </span>
                                    </template>
                                    <a-row :gutter="16">

                                    <a-col :xs="24" :sm="24" :md="10" :lg="10">

                                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                            <a-form-item :label="$t('lead.document')" name="txtInfoClient_document"
                                            :help="rules.document ? rules.document.message : null"
                                            :validateStatus="rules.document ? 'error' : null">
                                            <a-input v-model:value="crmSerch.clientSerch.document" :placeholder="$t('common.placeholder_default_text',[$t('lead.document'),])" />
                                            </a-form-item>
                                        </a-col>

                                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                            <a-form-item :label="$t('lead.name')" name="txtInfoClient_name"
                                            :help="rules.name ? rules.name.message : null" :validateStatus="rules.name ? 'error' : null">
                                            <a-input v-model:value="crmSerch.clientSerch.name" :placeholder="$t('common.placeholder_default_text',[$t('lead.name'),])"/>
                                            </a-form-item>
                                        </a-col>

                                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                            <a-form-item
                                                        :label="$t('lead.proyect')"
                                                        name="proyect"
                                                        :help="
                                                            rules.proyect
                                                                ? rules.proyect
                                                                      .message
                                                                : null
                                                        "
                                                        :validateStatus="
                                                            rules.proyect
                                                                ? 'error'
                                                                : null
                                                        "
                                            >
                                                <a-select
                                                    v-model:value="crmSerch.clientSerch.campana"
                                                    show-search
                                                    option-filter-prop="title"
                                                    :allowClear="true"
                                                    :placeholder="$t('common.select_default_text', [$t('lead.proyect')])"
                                                >
                                                    <a-select-option
                                                        v-for="allAgentCamp in allAgentCamps"
                                                        :key="allAgentCamp.xid"
                                                        :value="allAgentCamp.id"
                                                        :title="allAgentCamp.name" 
                                                    >
                                                        {{ allAgentCamp.name }}
                                                    </a-select-option>
                                                </a-select>

                                            </a-form-item>
                                        </a-col>

                                    </a-col>

                                    <a-col :xs="24" :sm="24" :md="10" :lg="10">

                                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                            <a-form-item :label="$t('lead.email')" name="txtInfoClient_email"
                                            :help="rules.mail ? rules.mail.message : null" :validateStatus="rules.mail ? 'error' : null">
                                            <a-input v-model:value="crmSerch.clientSerch.email" :placeholder="$t('common.placeholder_default_text',[$t('lead.email'),])"/>
                                            </a-form-item>
                                        </a-col>

                                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                            <a-form-item :label="$t('lead.phone')" name="txtInfoClient_phone">

                                                <a-input style="flex: 1; margin-right: 8px;" v-model:value="crmSerch.clientSerch.phone" :placeholder="$t('common.placeholder_default_text',[$t('lead.phone'),])" />

                                            </a-form-item>
                                        </a-col>
                                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
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
                                                    v-model:value="crmSerch.clientSerch.leadStatus"
                                                    show-search
                                                    option-filter-prop="title"
                                                    :allowClear="true"
                                                    :placeholder="$t('common.select_default_text',[$t('lead.lead_status' ),])"
                                                >
                                                    <a-select-option
                                                        v-for="status in allLeadStatus.filter(s => s.type === 'lead_status')"
                                                        :key="status.xid"
                                                        :value="status.id"
                                                        :title="status.name"
                                                    >
                                                        {{ status.name }}
                                                    </a-select-option>
                                                </a-select>
                                            </a-form-item>
                                        </a-col>

                                                    

                                    </a-col>
                                    <!-- acciones de busqueda y gestion -->
                                    <a-col :xs="24" :sm="24" :md="4" :lg="4">
                                        <div
                                            style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%;">
                                            <a-button @click="serchInformationClient" :disabled="crmState.client.managing === true" type="primary" style="min-width: 130px;">{{ $t('common.search')}}</a-button>
                                            <a-button @click="clearSerchInformation" :disabled="crmState.client.managing === true" type="primary" style="margin-top: 10px;min-width: 130px;">{{$t('common.clear')}}</a-button>
                                            <a-button @click="handleClientToManage" :disabled="crmState.client.toManage === false" type="primary"
                                            style="margin-top: 10px;min-width: 130px;">{{ $t('common.management') }}</a-button>
                                        </div>
                                    </a-col>


                                    <!--######################################################-->
                                    <!--###########   TABLA DE BUSQUEDA   ##############-->
                                    <a-col :xs="24" :sm="24" :md="24" :lg="24" class="mt-10">

                                        <a-col :span="24">
                                            <div class="table-responsive">
                                                <a-table :row-key="record => record.id" :row-selection="{
                                                    selectedRowKeys: tableClienteSerch.selectedRowKeys,
                                                    onSelect: (record) => {
                                                    if (tableClienteSerch.selectedRowKeys.includes(record.id)) {
                                                        tableClienteSerch.selectedRowKeys = [];
                                                        clearClientSelection();
                                                    } else {
                                                        tableClienteSerch.selectedRowKeys = [record.id];
                                                        onClientSelected(record);
                                                    }
                                                    },
                                                    hideSelectAll: true,
                                                    getCheckboxProps: (record) => ({
                                                    disabled: false,
                                                    name: String(record.id),
                                                    }),
                                                }" :columns="columns" :data-source="tableClienteSerch.data"
                                                    :pagination="tableClienteSerch.pagination" :loading="tableClienteSerch.loading"
                                                    @change="handleClientSerchTableChange" bordered size="middle">
                                                    <template #bodyCell="{ column, record }">
                                                        <template v-if="column.dataIndex === 'assign_to'">
                                                                {{ record.assign_to && record.assign_to.name
                                                                    ? record.assign_to.name
                                                                    : ''
                                                                }}
                                                        </template>
                                                    </template>
                                                </a-table>
                                            </div>
                                        </a-col>

                                    </a-col>
                                    <!--########### FIN TABLA DE BUSQUEDA ##############-->
                                    <!--######################################################-->

                                    </a-row>

                                    
                                </a-tab-pane>
                                <!-- FIN Tab de busqueda de leads -->

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
                                        :leadId="crmState.client.xid"
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
                                            {{ $t("menu.correspondence") }}
                                        </span>
                                    </template>
                                    <LeadNotesTable
                                        pageName="lead_action"
                                        :leadId="crmState.client.xid"
                                        :leadInfo="crmState.client"
                                        :managing="crmState.client.managing"
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
                            </a-tabs>
                        </a-card>
                    </a-col>
                </a-row>
    </a-layout>
</template>

<script>
import { onMounted, onUnmounted, ref, createVNode, watch, computed } from "vue";
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
    PlusOutlined,
} from "@ant-design/icons-vue";
import { Modal, notification } from "ant-design-vue";
import { useRoute, useRouter } from "vue-router";
import { forEach, find } from "lodash-es";
import { useI18n } from "vue-i18n";
import { useStore } from "vuex";
import apiAdmin from "../../../../../../../common/composable/apiAdmin";
import common from "../../../../../../../common/composable/common";
// import crud from "../../../../../../../common/composable/crud";
import BookingModal from "../../../../../bookings/BookingModal.vue";
import LeadLogTable from "../../../../../../components/lead-logs/index.vue";
import LogTimeline from "../../../../../../components/lead-logs/LogTimeline.vue";
import LeadNotesTable from "../../../../../../components/lead-notes/index.vue";
import SkipLeadModal from "./SkipLeadModal.vue";
import SendMail from "./SendMail.vue";
import SendMessage from "./SendMessage.vue";
import Sidebar from "../../../../../../../common/layouts/Sidebar.vue";
import SendCall from "./SendCall.vue";
import SearchLead from "./SearchLead.vue";
// import UphoneCallTable from "./UphoneCallsHistory.vue";
import fields from "../fields";
import functions from "./functions.js";

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
        SearchLead,
        // UphoneCallTable,
        PlusOutlined,
    },
    setup() {
        const { columns } = fields();
        const { formatDateTime, rightSidebarValue, themeMode, permsArray } = common();
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const route = useRoute();
        const router = useRouter();
        const store = useStore();
        const leadDetails = ref({});
        const leadCallLogDetails = ref({});
        const activeLeftPanelKey = ref("lead_details");
        const leadFormData = ref({});
        const { t } = useI18n();
        const referenceNumber = ref("");
        const leadFollowUp = ref({});
        const leadNumber = ref(0);
        const newPageLoad = ref(true);
        const refreshTimeLine = ref(false);
        const autoSaved = ref(false);
        const saveLoading = ref(false);
        const saveExitLoading = ref(false);
        const showSkipModal = ref(false);
        const allLeadStatus = ref([]); // todos los lead status
        const allAgentCamps = ref([]); // Campanas del agente
        const leadStatusId = ref(undefined);
        const uPhoneCampaignsLists = ref(undefined);
        const uPhoneEmailCampaignsLists = ref(undefined);

        const {
            // En uso
            timer,
            crmSerch,
            crmState,
            tableClienteSerch,
            serchInformationClient,
            clearSerchInformation,
            fetchUserCampaigns,
            fetchLeadStatus,
            fetchLeadMaritalStatus,
            onClientSelected,
            calcularEdad,
            fetchLocalidades,
            fetchAgenteCampanas,
            clearClientSelection,
            provinceOptions,
            cantonOptions,
            districtOptions,
            activeKey,
            clientToManage,
            handleClientSerchTableChange,
            //////////
        } = functions();

        const addNote = () =>{
            activeKey.value = 'lead_notes';
        };
        

        function handleClientToManage() {
            clientToManage();
            fetchInitData(crmState.client.xid);
        }

        const leadStatusModel = computed({
            get() {
                const ls = crmState.client.lead_status
                return ls?.id != null ? ls.id : null
            },
            set(val) {
                if (!crmState.client.lead_status) {
                    crmState.client.lead_status = { id: val }
                    return
                }
                if (val && typeof val === 'object') {
                    crmState.client.lead_status = val
                } else {
                    crmState.client.lead_status.id = val
                }
            }
        });

        const maritalStatusModel = computed({
            get() {
                const ec = crmState.client.estado_civil
                return ec?.id != null ? ec.id : null
            },
            set(val) {
                if (!crmState.client.estado_civil) {
                    crmState.client.estado_civil = { id: val }
                return
                }
                if (val && typeof val === 'object') {
                    crmState.client.estado_civil = val
                } else {
                    crmState.client.estado_civil.id = val
                }
            }
        });

        onMounted(async () => {

            allAgentCamps.value = await fetchUserCampaigns() || [];
            allLeadStatus.value = await fetchLeadStatus() || [];
            
            fetchLocalidades();
            fetchAgenteCampanas();
            if(route.params.id){
                fetchInitData(route.params.id)
            }

        });

        onUnmounted(() => {
            activeKey.value = 'lead_details';
            if(crmState.client.managing){
                saveLead('save');
            }
            clearSerchInformation();
        });


        const openRightSidebar = () => {
            store.commit("auth/updateRightSidebarValue", !rightSidebarValue.value);
        };

        const fetchInitData = (xid) => {
            const leadId = xid;
            if(leadId){
                const campaignUrl =
                    "campaign{id,xid,name,last_action_by,x_last_action_by,completed_by,x_completed_by,started_on,completed_on,upcoming_lead_action},campaign:lastActioner{id,xid,name},campaign:completedBy{id,xid,name}";
                const leadDetailsUrl = `leads/${leadId}?fields=id,xid,cedula,nombre,apellido1,apellido2,email,tel1,tel2,tel3,tel4,tel5,tel6,provincia,canton,distrito,hijos,fechaNacimiento,tarjeta,nombreBase,edad,nacionalidad,started,time_taken,last_action_by,x_last_action_by,lastActioner{id,xid,name},campaign_id,x_campaign_id,${campaignUrl},leadFollowUp{id,xid,log_type,user_id,x_user_id,date_time,notes},leadFollowUp:user{id,xid,name},leadStatus{id,xid,name,type},estadoCivil{id,xid,name,type},'assignTo{id,xid,name}'`;
                leadDetails.value = {};
                leadCallLogDetails.value = {};
                activeKey.value = "lead_details";
                activeLeftPanelKey.value = "lead_details";
                leadFormData.value = {};
                leadFollowUp.value = {};
                referenceNumber.value = "";
                leadStatusId.value = undefined;
                leadNumber.value = 0;
                newPageLoad.value = true;

                const leadDetailsPromise = axiosAdmin.get(leadDetailsUrl);
                const leadCallLogPromise = axiosAdmin.get(`leads/create-call-log/${leadId}`);

                Promise.all([
                    leadDetailsPromise,
                    leadCallLogPromise,
                ]).then(
                    ([
                        leadDetailsResponse,
                        leadCallLogResponse,
                    ]) => {

                        leadNumber.value = leadCallLogResponse.data.lead_number;
                        if(route.params.id){
                            // crmState.client = leadDetailsResponse.data;
                            onClientSelected(leadDetailsResponse.data);
                            clientToManage();
                        }
                        crmState.client.call_log = leadCallLogResponse.data.call_log;

                        newPageLoad.value = false;
                        refreshTimeLine.value = false;

                    }
                );
            }
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
                    ...crmState.client,
                    call_log_id: crmState.client.call_log.xid,
                    call_time: calculateTotalTimeInSeconds(),
                    x_lead_id: crmState.client.xid,
                },
                success: (res) => {
                    autoSaved.value = true;
                    saveLoading.value = false;

                    if (saveType == "save_exit") {
                        saveExitLoading.value = false;
                        router.push({
                            name: "admin.leads.index",
                            //name: "admin.call_manager.index",
                        });
                        // store.dispatch("auth/showNotificaiton", {});
                    }else{
                        crmState.client.managing = false;
                        timer.reset(crmState.client.time_taken, false);
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
            const total = 
                timer.seconds.value + 
                timer.minutes.value * 60 + 
                timer.hours.value   * 3600 + 
                timer.days.value    * 86400;

            crmState.client.time_taken = total;
            if (timer.seconds.value % 5 == 0) {
               // saveLead(); // Aqui es donde hace el guardado automatico
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

        watch(() => crmState.client.xid,newId => {
                if (newId) {
                    refreshTimeLine.value = true;
                    newPageLoad.value = false;
                }else{
                    refreshTimeLine.value = false;
                    newPageLoad.value = true;
                }
            }
        );


        return {
            // En uso
            columns,
            timer,
            crmSerch,
            crmState,
            tableClienteSerch,
            serchInformationClient,
            clearSerchInformation,
            allAgentCamps,
            onClientSelected,
            calcularEdad,
            clearClientSelection,
            provinceOptions,
            cantonOptions,
            districtOptions,
            clientToManage,
            leadStatusModel,
            maritalStatusModel,
            addNote,
            handleClientToManage,
            handleClientSerchTableChange,
            permsArray,
            ///////////////////

            themeMode,
            activeLeftPanelKey,
            leadDetails,
            activeKey,
            leadFormData,
            referenceNumber,
            leadFollowUp,
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
    height: calc(110vh - 90px);
}

.callmanager-left-sidebar .ps {
    height: calc(110vh - 270px);
}

.callmanager-middle-sidebar {
    height: calc(110vh - 90px);
}

.callmanager-middle-sidebar .ps {
    height: calc(110vh - 235px);
}

.callmanager-right-sidebar {
    height: calc(82vh - 99px);
}
</style>
