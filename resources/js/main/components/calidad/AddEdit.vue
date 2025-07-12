<template>
    <a-drawer :title="$t('menu.quality')" :width="drawerWidth" :open="visible" :body-style="{ paddingBottom: '80px' }"
        :maskClosable="false" @close="onClose" :destroyOnClose="true">
        <!-- Informaion general del lead -->
        <a-descriptions class="label-bold" :layout="descLayout" :column="{ xs: 1, sm: 1, md: 2, lg: 3 }"
            :title="$t('common.basic_details')">
            <a-descriptions-item class="label-bold" strong :label="$t('uphone_calls.client_id')">
                {{
                    data.lead && data.lead.id && data.lead.id
                        ? data.lead.id
                        : "-"
                }}
            </a-descriptions-item>
            <a-descriptions-item class="label-bold" :label="$t('lead.document')">
                {{ data.lead && data.lead.cedula ? data.lead.cedula : "-" }}
            </a-descriptions-item>
            <a-descriptions-item class="label-bold" :label="$t('lead.name')">
                {{
                    data.lead && data.lead.nombre && data.lead.nombre
                        ? data.lead.nombre
                        : "-"
                }}
                {{
                    data.lead && data.lead.segundo_nombre && data.lead.segundo_nombre
                        ? data.lead.segundo_nombre
                        : ""
                }}
                {{
                    data.lead && data.lead.apellido1 && data.lead.apellido1
                        ? data.lead.apellido1
                        : "-"
                }}
                {{
                    data.lead && data.lead.apellido2 && data.lead.apellido2
                        ? data.lead.apellido2
                        : "-"
                }}
            </a-descriptions-item>
        </a-descriptions>

        <a-descriptions :layout="descLayout" :column="{ xs: 1, sm: 1, md: 2, lg: 3 }">

            <a-descriptions-item class="label-bold" :label="$t('lead.date_birth')">
                {{
                    data.lead && data.lead.fechaNacimiento && data.lead.fechaNacimiento
                        ? formatDateTime(data.lead.fechaNacimiento)
                        : "-"
                }}
            </a-descriptions-item>
            <a-descriptions-item class="label-bold" :label="$t('lead.age')">
                {{
                    data.lead && data.lead.edad && data.lead.edad
                        ? data.lead.edad
                        : "-"
                }}
            </a-descriptions-item>
            <a-descriptions-item class="label-bold" :label="$t('lead.base_name')">
                {{
                    data.lead && data.lead.nombreBase && data.lead.nombreBase
                        ? data.lead.nombreBase
                        : "-"
                }}
            </a-descriptions-item>
        </a-descriptions>

        <a-descriptions :layout="descLayout" :column="{ xs: 1, sm: 1, md: 2, lg: 3 }">
            <a-descriptions-item class="label-bold" :label="$t('lead.nationality')">
                {{
                    data.lead && data.lead.nacionalidad && data.lead.nacionalidad
                        ? data.lead.nacionalidad
                        : "-"
                }}
            </a-descriptions-item>
            <a-descriptions-item class="label-bold" :label="$t('lead.email')">
                {{
                    data.lead && data.lead.email && data.lead.email
                        ? data.lead.email
                        : "-"
                }}
            </a-descriptions-item>
            <a-descriptions-item class="label-bold" :label="$t('lead.expiration_date')">
                {{
                    data.lead && data.lead.fechaVencimiento && data.lead.fechaVencimiento
                        ? formatDateTime(data.lead.fechaVencimiento)
                        : "-"
                }}
            </a-descriptions-item>
        </a-descriptions>

        <a-descriptions
            class="mt-10"
        >
            <a-descriptions-item class="label-bold" :label="$t('lead.plan_type')">
                        {{
                            data.lead && data.lead.tipo_plan && data.lead.tipo_plan
                                ? data.lead.tipo_plan
                                : "-"
                        }}
            </a-descriptions-item>
            <a-descriptions-item class="label-bold" :label="$t('lead.card_type')">
                {{
                    data.lead && data.lead.tipo_tarjeta && data.lead.tipo_tarjeta
                        ? data.lead.tipo_tarjeta
                        : "-"
                }}
            </a-descriptions-item>
            <a-descriptions-item class="label-bold" :label="$t('lead.transmitter')">
                {{
                    data.lead && data.lead.emisor && data.lead.emisor
                        ? data.lead.emisor
                        : "-"
                }}
            </a-descriptions-item>
        </a-descriptions>
        <a-descriptions
            class="mt-10"
        >
            <a-descriptions-item  class="label-bold" :label="$t('lead.last_digits')">
                {{
                    data.lead && data.lead.ultimos_digitos && data.lead.ultimos_digitos
                        ? data.lead.ultimos_digitos
                        : "-"
                }}
            </a-descriptions-item>
            <a-descriptions-item class="label-bold" :label="$t('lead.sales_focus')">
                {{
                    data.lead && data.lead.foco_venta && data.lead.foco_venta
                        ? data.lead.foco_venta
                        : "-"
                }}
            </a-descriptions-item>
        </a-descriptions>

        <a-tabs v-model:activeKey="activeTabKey" class="mt-20">
            <!-- Informacion de la venta general solicitada -->
            <a-tab-pane key="info_venta">
                <template #tab>
                    <span>
                        <UnorderedListOutlined />
                        {{ $t("common.information") }} {{ $t('lead_notes.sale') }}
                    </span>
                </template>
                <a-descriptions :layout="descLayout" :column="{ xs: 1, sm: 1, md: 2, lg: 3 }">

                    <a-descriptions-item class="label-bold" :label="$t('lead.id') + ' ' + $t('lead_notes.sale')">
                        {{ data.is_sale && data.is_sale.idVenta ? data.is_sale.idVenta : "-" }}
                    </a-descriptions-item>

                    <a-descriptions-item class="label-bold" :label="$t('lead_notes.date_time')">
                        {{ data.date_time ? formatDateTime(data.date_time) : "-" }}
                    </a-descriptions-item>

                    <a-descriptions-item class="label-bold" :label="$t('lead_log.user_id')">
                        {{ data.is_sale.user && data.is_sale.user.name ? data.is_sale.user.name : "-" }}
                    </a-descriptions-item>
                </a-descriptions>
                <a-descriptions :layout="descLayout" :column="{ xs: 1, sm: 1, md: 2, lg: 3 }">
                    <a-descriptions-item class="label-bold" :span="1" :label="$t('lead.card')">
                        {{ data.is_sale && data.is_sale.tarjeta ? data.is_sale.tarjeta : "-" }}
                    </a-descriptions-item>

                    <a-descriptions-item class="label-bold" :label="$t('lead.total_amount')">
                        {{ data.is_sale && data.is_sale.montoTotal ? formatAmountCurrency(data.is_sale.montoTotal) : "-"
                        }}
                    </a-descriptions-item>

                    <a-descriptions-item class="label-bold" :label="$t('lead.phone')">
                        {{ data.is_sale && data.is_sale.telVenta ? data.is_sale.telVenta : "-" }}
                    </a-descriptions-item>
                </a-descriptions>
                <div class="row">
                    <div class="table-responsive">
                        <a-table
                            :columns="columnsProduct"
                            :data-source="tableProducts.data"
                            :pagination="tableProducts.pagination"
                            @change="handleTableChange"
                            :row-key="record => record.price"
                            size="small"
                        >
                            <template #bodyCell="{ column, record }">

                                <template v-if="column.dataIndex === 'price'">
                                        {{ formatAmountCurrency(record.price)}}
                                </template>
                                
                            </template>
                            <template #footer>
                                <div class="text-center">
                                    <strong>{{ $t('lead.total_amount') }} : {{ formatAmountCurrency(data.is_sale.montoTotal) }}</strong>
                                </div>
                            </template>
                        </a-table>
                    </div>
                </div>
                <a-descriptions :layout="descLayout" :column="{ xs: 1, sm: 1, md: 2, lg: 3 }">

                    <a-col v-if="data.is_sale && data.is_sale.aplicaBeneficiarios === 1" :span="(data.is_sale.aplicaBeneficiarios && !data.is_sale.aplicaBeneficiariosAsist) ? 3 : 2">
                        <a-form-item  :label="$t('lead.beneficiaries')">
                            <a-checkbox :checked="(data.is_sale && data.is_sale.aplicaBeneficiarios == 1)"></a-checkbox>
                        </a-form-item>
                    </a-col>

                    <a-col v-if="data.is_sale && data.is_sale.aplicaBeneficiariosAsist === 1" :span="(data.is_sale.aplicaBeneficiariosAsist && !data.is_sale.aplicaBeneficiarios) ? 3 : 1">
                        <a-form-item :label="$t('lead.beneficiaries_asist')">
                            <a-checkbox :checked="(data.is_sale && data.is_sale.aplicaBeneficiariosAsist == 1)"></a-checkbox>
                        </a-form-item>
                    </a-col>

                </a-descriptions>
                <row class="d-flex">
                    <a-col style="margin-right: 1%;" v-if="data.is_sale && data.is_sale.aplicaBeneficiarios === 1" :xs="12" :sm="12" :md="12" :lg="12">
                        <a-form-item class="label-bold" :label="$t('lead.beneficiary_information')" :labelCol="{ span: 24 }"
                            :wrapperCol="{ span: 24 }">
                            <a-space direction="vertical" style="width: 100%;">
                                <div v-for="(benef, i) in beneficiarios" :key="i" style="display: flex; gap: 1rem;">
                                    <a-form-item :name="['beneficiarios', i, 'nombre']" noStyle>
                                        <a-input :value="benef.nombre" :placeholder="$t('lead.name')" style="flex: 1;" />
                                    </a-form-item>
                                    <a-form-item :name="['beneficiarios', i, 'porcentaje']" noStyle>
                                        <a-input-number disabled v-model:value="benef.porcentaje" :min="0" :precision="0"
                                            :step="1" :placeholder="$t('lead.percentage')" style="width: 80px;" />
                                    </a-form-item>
                                </div>
                            </a-space>
                        </a-form-item>
                    </a-col>

                    <a-col v-if="data.is_sale && data.is_sale.aplicaBeneficiariosAsist === 1" :xs="12" :sm="12" :md="12" :lg="12">
                        <a-form-item class="label-bold" :label="$t('lead.beneficiary_asist_information')" :labelCol="{ span: 24 }"
                            :wrapperCol="{ span: 24 }">
                            <a-space direction="vertical" style="width: 100%;">
                                <div v-for="(benef, i) in beneficiariosAsist" :key="i" style="display: flex; gap: 1rem;">
                                    <a-form-item :name="['beneficiarios', i, 'nombre']" noStyle>
                                        <a-input :value="benef.nombre" :placeholder="$t('lead.name')" style="flex: 1;" />
                                    </a-form-item>
                                    <a-form-item :name="['beneficiarios', i, 'porcentaje']" noStyle>
                                        <a-input-number disabled v-model:value="benef.porcentaje" :min="0" :precision="0"
                                            :step="1" :placeholder="$t('lead.percentage')" style="width: 80px;" />
                                    </a-form-item>
                                </div>
                            </a-space>
                        </a-form-item>
                    </a-col>
                </row>



            </a-tab-pane>

            <!-- Informacion de calidad de la venta -->
            <a-tab-pane key="calidad_data" class="text-center">
                <template #tab>
                    <span>
                        <UnorderedListOutlined />
                        {{ $t("common.information") }} {{ $t('menu.quality') }}
                    </span>
                </template>

                <a-typography-text v-if="!plantillaCalidad" type="warning" strong>
                    {{ $t("campaign.no_template") }}
                </a-typography-text>

                <a-form v-else ref="formRef" :model="datos.calidad" :rules="rules">
                    <a-col :xs="24" :sm="24" :md="24" :lg="24">
                        <h2 v-if="plantillaCalidad.nombre"><strong>{{ $t('common.template') }}: {{ plantillaCalidad.nombre }}</strong></h2>
                        <strong><small v-if="plantillaCalidad.descripcion"> {{ plantillaCalidad.descripcion }}</small></strong>

                        <div v-if="plantillaCalidad.variables && plantillaCalidad.variables.length"
                            class="table-responsive">
                            <a-table :columns="columns" :dataSource="plantillaCalidad.variables" :pagination="false"
                                :row-key="(record) => record.xid" bordered size="middle">
                                <template #bodyCell="{ column, record }">
                                    <!-- tipo de variables -->
                                    <template v-if="column.dataIndex === 'tipo'">
                                        <a-tag style="width: 100%; text-align: center;" v-if="record.tipo === 'critica'" color="#f5b041">
                                            {{ $t('message_template.critical_variable') }}
                                        </a-tag>
                                        <a-tag style="width: 100%; text-align: center;" v-else color="#4cb050">
                                            {{ $t('message_template.critical_not_variable') }}
                                        </a-tag>
                                    </template>
                                    <!-- peso de las varibales -->
                                    <template v-if="column.dataIndex === 'nota_maxima'">
                                        {{ record.tipo === 'critica' ? 'N/A' :record.nota_maxima }}
                                    </template>
                                    <!-- slot para acciones -->
                                    <template v-if="column.dataIndex === 'acciones'">
                                        <a-checkbox v-if="datos.calidad.accion === 'add'" class="checkbox-x" @change="selectVariable(record)"></a-checkbox>
                                        <a-checkbox v-else v-model:checked="record.marcada" @change="selectVariable(record)" class="checkbox-x"></a-checkbox>
                                    </template>
                                </template>
                                <template #footer>
                                    <div class="w-full text-right">
                                        <strong> {{ $t('common.qualification') }} : <span :style="{color: calificacion === 0  ? 'red' : calificacion > 0 && calificacion <= 50 ? '#f5b041' : 'inherit'}">{{ calificacion }}</span></strong>
                                    </div>
                                </template>

                            </a-table>
                        </div>
                        <a-typography-text v-else type="warning" strong>
                            {{ $t("campaign.no_template") }}
                        </a-typography-text>
                    </a-col>
                    <a-col v-if="plantillaCalidad.variables && plantillaCalidad.variables.length" :xs="24" :sm="24"
                        :md="24" :lg="24" style="margin-top: 20px;">

                        <a-row :gutter="[16, 16]">

                            <a-col :xs="{ span: 24 }" :sm="{ span: 24 }" :md="{ span: 6 }" :lg="{ span: 6 }">
                                <a-form-item name="minuto_precio" :label-col="{ span: 24 }" :label="$t('message_template.minute')" class="required label-bold">
                                    <a-input-number v-model:value="datos.calidad.minuto_precio" style="width:100%" :placeholder="$t('message_template.minute')" :min="0" :max="59" />
                                </a-form-item>
                            </a-col>
                            

                            <a-col :xs="{ span: 24 }" :sm="{ span: 24 }" :md="{ span: 6 }" :lg="{ span: 6 }">
                                <a-form-item name="duracion" :label-col="{ span: 24 }" :label="$t('lead.call_duration')" class="required label-bold">
                                    <a-input-number v-model:value="datos.calidad.duracion" style="width:100%" :placeholder="$t('lead.call_duration')" :min="0" />
                                </a-form-item>
                            </a-col>

                            <a-col :xs="{ span: 24 }" :sm="{ span: 24 }" :md="{ span: 12 }" :lg="{ span: 12 }">
                                <a-form-item name="accion_calidad_id" :label-col="{ span: 24 }" :label="$t('common.action')" class="required label-bold">
                                    <a-select 
                                        style="width: 100%"
                                        :placeholder="$t('common.select_default_text', [$t('common.action')])"
                                        :allowClear="true"
                                        show-search
                                        optionFilterProp="title"
                                        v-model:value="datos.calidad.accion_calidad_id"
                                    >
                                        <a-select-option v-for="accion in allAccionesCalidad.filter(s => s.tipo === 'accion')" 
                                            :key="accion.xid"
                                            :value="accion.xid"
                                            :title="accion.nombre"
                                        >
                                            {{ accion.nombre }}
                                        </a-select-option>
                                    </a-select>
                                </a-form-item>

                            </a-col>
                        </a-row>

                        <a-row :gutter="[16, 16]">

                            

                            <a-col :xs="{ span: 24 }" :sm="{ span: 24 }" :md="{ span: datos.calidad.cierre_venta ? 6 : 12 }" :lg="{ span: datos.calidad.cierre_venta ? 6: 12 }">
                                <a-form-item name="cierre_venta" :label-col="{ span: 24 }" :label="$t('common.closing_sale')" class="required label-bold">
                                    <a-select 
                                        style="width: 100%"
                                        :placeholder="$t('common.select_default_text', [$t('common.closing_sale')])"
                                        :allowClear="true"
                                        show-search
                                        optionFilterProp="title"
                                        v-model:value="datos.calidad.cierre_venta"
                                    >
                                        <a-select-option  
                                            key="si"
                                            :value="true"
                                            :title="$t('common.yes')"
                                        >
                                            {{ $t('common.yes') }}
                                        </a-select-option>
                                        <a-select-option  
                                            key="no"
                                            :value="false"
                                            :title="$t('common.no')"
                                        >
                                            {{ $t('common.no') }}
                                        </a-select-option>
                                    </a-select>
                                </a-form-item>
                            </a-col>

                            <a-col v-if="datos.calidad.cierre_venta" :xs="{ span: 24 }" :sm="{ span: 24 }" :md="{ span: 6 }" :lg="{ span: 6 }">
                                <a-form-item name="cerrado_por" :label-col="{ span: 24 }" :label="$t('common.closed_by')" class="required label-bold">
                                    <a-select 
                                        style="width: 100%"
                                        :placeholder="$t('common.select_default_text', [$t('common.closed_by')])"
                                        :allowClear="true"
                                        show-search
                                        optionFilterProp="title"
                                        v-model:value="datos.calidad.cerrado_por"
                                    >
                                        <a-select-option v-for="accion in allAccionesCalidad.filter(s => s.tipo === 'cierre')" 
                                            :key="accion.xid"
                                            :value="accion.xid"
                                            :title="accion.nombre"
                                        >
                                            {{ accion.nombre }}
                                        </a-select-option>
                                    </a-select>
                                </a-form-item>
                            </a-col>

                            <a-col :xs="{ span: 24 }" :sm="{ span: 24 }" :md="{ span: datos.calidad.estado === 'CERTIFICADA' || datos.calidad.estado === 'RELLAMADA_CERTIFICADA' || datos.calidad.estado === 'CANCELADA_CALIDAD' || datos.calidad.estado === 'REASIGNADA' ? 12 : 12 }"  :lg="{ span: datos.calidad.estado === 'CERTIFICADA' || datos.calidad.estado === 'RELLAMADA_CERTIFICADA' || datos.calidad.estado === 'CANCELADA_CALIDAD' || datos.calidad.estado === 'REASIGNADA' ? 6 : 12 }">
                                <a-form-item
                                    :label="$t('common.status')"
                                    name="estado"
                                    class="required label-bold"
                                    :label-col="{ span: 24 }"
                                >
                                    <a-select
                                        style="width: 100%"
                                        :placeholder="$t('common.select_default_text', [$t('common.status')])"
                                        :allowClear="true"
                                        show-search
                                        optionFilterProp="title"
                                        v-model:value="datos.calidad.estado"
                                    >
                                        <a-select-option
                                            v-for="opt in cierreVentaOptions"
                                            :key="opt.value"
                                            :value="opt.value"
                                            :title="opt.label"
                                        >
                                            {{ opt.label }}
                                        </a-select-option>
                                    </a-select>
                                </a-form-item> 
                            </a-col>
                            <!-- opciones que aparecen dependiendo se la selecion superior -->
                            <a-col v-if="datos.calidad.estado === 'CERTIFICADA'" :xs="{ span: 24 }" :sm="{ span: 24 }" :md="{ span: datos.calidad.estado === 'CERTIFICADA' ? 24 : 12 }" :lg="{ span: datos.calidad.estado === 'CERTIFICADA' ? 6 : 12 }">
                                <a-form-item
                                    :label="$t('common.policy')"
                                    name="numero_poliza"
                                    class="label-bold"
                                    :label-col="{ span: 24 }"
                                >
                                    <a-input :maxlength="99" v-model:value="datos.calidad.numero_poliza" :placeholder="$t('common.policy')" />
                                </a-form-item> 
                            </a-col>

                            <a-col v-if="datos.calidad.estado === 'RELLAMADA_CERTIFICADA'" :xs="{ span: 24 }" :sm="{ span: 24 }" :md="{ span: datos.calidad.estado === 'RELLAMADA_CERTIFICADA' ? 24 : 12 }" :lg="{ span: datos.calidad.estado === 'RELLAMADA_CERTIFICADA' ? 6 : 12 }">
                                <a-form-item
                                    :label="$t('common.call_certificate')"
                                    name="numero_certificado"
                                    class="required label-bold"
                                    :label-col="{ span: 24 }"
                                >
                                    <a-input :maxlength="99" v-model:value="datos.calidad.numero_certificado" :placeholder="$t('common.call_certificate')" />
                                </a-form-item> 
                            </a-col>
                            <a-col v-if="datos.calidad.estado === 'CANCELADA_CALIDAD'" :xs="{ span: 24 }" :sm="{ span: 24 }" :md="{ span: datos.calidad.estado === 'CANCELADA_CALIDAD' ? 24 : 12 }" :lg="{ span: datos.calidad.estado === 'CANCELADA_CALIDAD' ? 6 : 12 }">
                                <a-form-item
                                    :label="$t('message_template.reason')"
                                    name="motivo_cancelacion_id"
                                    class="required label-bold"
                                    :label-col="{ span: 24 }"
                                >
                                    <a-select 
                                        style="width: 100%"
                                        :placeholder="$t('common.select_default_text', [$t('message_template.reason')])"
                                        :allowClear="true"
                                        show-search
                                        optionFilterProp="title"
                                        v-model:value="datos.calidad.motivo_cancelacion_id"
                                    >
                                        <a-select-option v-for="motivo in allMotivosCalidad" 
                                            :key="motivo.xid"
                                            :value="motivo.xid"
                                            :title="motivo.motivo"
                                        >
                                            {{ motivo.motivo }}
                                        </a-select-option>
                                    </a-select>
                                </a-form-item> 
                            </a-col>

                            <a-col v-if="datos.calidad.estado === 'REASIGNADA'" :xs="{ span: 24 }" :sm="{ span: 24 }" :md="{ span: datos.calidad.estado === 'REASIGNADA' ? 24 : 12 }" :lg="{ span: datos.calidad.estado === 'REASIGNADA' ? 6 : 12 }">
                                <a-form-item
                                    :label="$t('lead.agent')"
                                    name="reasignado_a"
                                    class="required label-bold"
                                    :label-col="{ span: 24 }"
                                >
                                    <a-select 
                                        style="width: 100%"
                                        :placeholder="$t('common.select_default_text', [$t('lead.agent')])"
                                        :allowClear="true"
                                        show-search
                                        optionFilterProp="title"
                                        v-model:value="datos.calidad.reasignado_a"
                                    >
                                        <a-select-option v-for="agente in agenteCampana" 
                                            :title="agente.name" 
                                            :key="agente"
                                            :value="agente.id">
                                            {{ agente.name }}
                                        </a-select-option>
                                    </a-select>
                                </a-form-item> 
                            </a-col>

                            <!-- ---------------------------------------------------------- -->
                            <a-col :xs="{ span: 24 }" :sm="{ span: 24 }" :md="{ span: 12 }" :lg="{ span: 12 }">
                                <a-form-item 
                                    :label="`${$t('common.status')} ${$t('lead_notes.sale')}`" 
                                    class="required label-bold" 
                                    :label-col="{ span: 24 }"
                                    name="estadoVenta"
                                >
                                    <a-select
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('common.status'),
                                            ])
                                        "
                                        :allowClear="true"
                                        show-search
                                        v-model:value="datos.calidad.estadoVenta"
                                    >
                                        <a-select-option
                                            key="Efectiva"
                                            value="Efectiva"
                                        >
                                            {{ $t("lead_notes.effective") }}
                                        </a-select-option>
                                        <a-select-option
                                            key="Cancelada"
                                            value="Cancelada"
                                        >
                                            {{ $t("lead_notes.canceled") }}
                                        </a-select-option>
                                    </a-select>
                                </a-form-item>
                            </a-col>

                            <a-col :xs="{ span: 24 }" :sm="{ span: 24 }" :md="{ span: 12 }" :lg="{ span: 12 }">
                                <a-form-item 
                                    :label="$t('common.improvement_options')" 
                                    class="label-bold" 
                                    :label-col="{ span: 24 }"
                                    name="oportunidades"
                                >
                                    <a-select
                                        :placeholder="
                                            $t('common.select_default_text', [
                                                $t('common.improvement_options'),
                                            ])
                                        "
                                        mode="multiple"
                                        :allowClear="true"
                                        show-search
                                        v-model:value="datos.calidad.oportunidades"
                                    >
                                        <a-select-option v-for="accion in allAccionesCalidad.filter(s => s.tipo === 'mejora')" 
                                            :key="accion.xid"
                                            :value="accion.id"
                                            :title="accion.nombre"
                                        >
                                            {{ accion.nombre }}
                                        </a-select-option>
                                    </a-select>
                                </a-form-item>
                            </a-col>
                            
                            <a-col v-if="datos.calidad.oportunidades.length >= 1" :xs="{ span: 24 }" :sm="{ span: 24 }" :md="{ span: 24 }" :lg="{ span: 24 }">
                                <a-form-item
                                    :label="`${$t('common.comments')} ${$t('common.improvement_options')}`"
                                    name="comentarios"
                                    class="label-bold"
                                    :label-col="{ span: 24 }"
                                >
                                    <a-textarea
                                        v-model:value="datos.calidad.comentarios_oportunidades"
                                        :placeholder="$t('common.placeholder_default_text', [$t('common.comments') + ' ' + $t('common.improvement_options')])"
                                        :rows="2" 
                                        :maxlength="2000"
                                    />
                                </a-form-item>
                            </a-col>

                            <a-col :xs="{ span: 24 }" :sm="{ span: 24 }" :md="{ span: 24 }" :lg="{ span: 24 }">
                                <a-form-item
                                    :label="$t('common.comments')"
                                    name="comentarios"
                                    class="label-bold"
                                    :label-col="{ span: 24 }"
                                >
                                    <a-textarea
                                        v-model:value="datos.calidad.comentarios"
                                        :placeholder="$t('common.placeholder_default_text', [$t('common.comments')])"
                                        :rows="2" 
                                        :maxlength="2000"
                                    />
                                </a-form-item>
                            </a-col>

                            <a-descriptions v-if="datos.calidad.fecha_calidad" :layout="descLayout" :column="{ xs: 1, sm: 1, md: 2, lg: 3 }">

                                <a-descriptions-item class="label-bold" :label="$t('common.date_time')">
                                    {{datos.calidad && datos.calidad.fecha_calidad ? formatDateTime(datos.calidad.fecha_calidad) : "-" }}
                                </a-descriptions-item>

                                <a-descriptions-item>
                                    <a-typography-text v-if="isQualityDateExpired" type="warning" strong>
                                        {{ $t("common.non_editable_quality") }}
                                    </a-typography-text>
                                </a-descriptions-item>

                            </a-descriptions>

                            <a-col class="text-center" :xs="{ span: 24 }" :sm="{ span: 24 }" :md="{ span: 24 }" :lg="{ span: 24 }">
                                <a-button :disabled="isQualityDateExpired" key="submit" type="primary" @click="submitQuality" style="margin-right: 1%;" :loading="loading">
                                    <SaveOutlined />
                                    {{ datos.calidad.accion === 'add' ? $t("common.add") : $t("common.edit") }}
                                </a-button>
                                <a-popconfirm :title="$t('menu.you_agree')" @confirm="onDelete">
                                    <a-button v-if="datos.calidad.accion === 'edit'" danger type="primary" :loading="loading">
                                        <DeleteOutlined />
                                        {{ $t("common.delete") }}
                                    </a-button>
                                    <template #icon>
                                        <question-circle-outlined style="color: red" />
                                    </template>
                                </a-popconfirm>
                                
                            </a-col>

                        </a-row>
                        
                    </a-col>
                </a-form>


            </a-tab-pane>

        </a-tabs>
    </a-drawer>
</template>
<script>
import { defineComponent, computed, ref, onMounted, watch, reactive } from "vue";
import { useI18n } from "vue-i18n";
import {
    UnorderedListOutlined,
    PhoneOutlined,
    FileTextOutlined,
    SaveOutlined,
    DeleteOutlined,
    QuestionCircleOutlined,
} from "@ant-design/icons-vue";
import common from "../../../common/composable/common";
import crud from "../../../common/composable/crud";
import { Grid, notification } from 'ant-design-vue';
import apiAdmin from "../../../common/composable/apiAdmin";
import _ from 'lodash';

function getEmptyEvaluacionCalidad() {
    return {
        //De la evaluacion
        user_id: null,
        idVenta: null,
        plantilla_id: null,
        variables: [],
        fecha_calidad: undefined,
        duracion: null,
        minuto_precio: null,
        cierre_venta: null,
        cerrado_por: null, 
        accion_calidad_id: null,
        oportunidades: [],
        comentarios_oportunidades: "",
        comentarios: null,
        numero_poliza: 'N/A',
        // Del estado
        evaluacion_id: null,
        estado: null,
        fecha_estado: null,
        nota_estado: null,
        numero_certificado: "",
        motivo_cancelacion_id: null,
        cancelado_por_supervision: false,
        reasignado_a: null,
        // estado de la venta en la tb venta
        estadoVenta: null, 
        // accion a realizar
        accion: "add"
    };
}

export const datos = reactive({
    calidad: getEmptyEvaluacionCalidad()
});

export default defineComponent({
    props: [
        "formData",
        "allCalidadTemplates",
        "allAccionesCalidad",
        "allMotivosCalidad",
        "allProductos",
        "data",
        "visible",
        "pageTitle",
        "successMessage",
    ],
    emits: ["close"],
    components: {
        UnorderedListOutlined,
        PhoneOutlined,
        FileTextOutlined,
        SaveOutlined,
        DeleteOutlined,
        QuestionCircleOutlined,
    },
    setup(props, { emit }) {
        const { formatTimeDuration, formatDateTime, formatAmountCurrency } = common();
        const crudVariables = crud();
        // const {  } = fields();
        const { t } = useI18n();
        const { addEditRequestAdmin } = apiAdmin();
        const drawerTitle = ref(t("lead.lead_details"));
        const activeTabKey = ref("info_venta");
        const beneficiarios = ref([]);
        const beneficiariosAsist = ref([]);
        const plantillaCalidad = ref([]);
        const calificacion = ref(100);
        const variablesUse = [];
        const notaInicial = 100;
        const formRef = ref();
        const agenteCampana = ref([]);
        const loading = ref(false);
        const isQualityDateExpired = ref(false);

        const tableProducts = reactive({
            data: [],
            pagination: {
                current: 1,
                pageSize: 3,
                total: 0,
                showSizeChanger: false,
            },
            loading: false,
            selectedRowKeys: [],
        });

        const rules = reactive({
            'estadoVenta': [ { required: true, message: t('common.status') } ],
            'accion_calidad_id': [ { required: true, message: t('common.action') } ],
            'duracion': [ { required: true, message: t('lead.call_duration') } ],
            'minuto_precio': [ { required: true, message: t('message_template.minute') } ],
            'cierre_venta': [ { required: true, message: t('common.closing_sale') } ],
            'estado': [ { required: true, message: t('common.status') } ],
            // 'numero_poliza': [ 
            //     { required: () => datos.estado === 'CERTIFICADA',
            //         message: t('common.policy'),
            //         trigger: 'blur' }
            // ],
            'numero_certificado': [ 
                { required: () => datos.estado === 'RELLAMADA_CERTIFICADA',
                    message: t('common.call_certificate'),
                    trigger: 'blur' }
            ],
            'motivo_cancelacion_id': [ 
                { required: () => datos.estado === 'CANCELADA_CALIDAD',
                    message: t('message_template.reason'),
                    trigger: 'blur' }
            ],
            'cerrado_por': [ 
                { required: () => datos.cierre_venta === true,
                    message: t('common.closed_by'),
                    trigger: 'blur' }
            ],
            'reasignado_a': [ 
                { required: () => datos.estado === 'REASIGNADA',
                    message: t('lead.agent'),
                    trigger: 'blur' }
            ],
        });

        const columnsProduct = [
            {
                title: t('lead.internal_code'),
                dataIndex: "internal_code",
                className: 'font-bold',
            },
            {
                title: t('product.name'),
                dataIndex: "name",
            },
            {
                title: t('product.coverage'),
                dataIndex: "coverage",
            },
            {
                title: t('product.price'),
                dataIndex: "price",
            },
            {
                title: t('dashboard.quantity'),
                dataIndex: "product_quantity",
            },
        ];

        const columns = [
            { title: t('lead_status.type'), dataIndex: 'tipo', key: 'tipo' },
            { title: t('common.name'), dataIndex: 'nombre', key: 'nombre' },
            { title: t('common.description'), dataIndex: 'descripcion', key: 'descripcion' },
            { title: t('message_template.maximum_grade'), dataIndex: 'nota_maxima', key: 'nota_maxima' },
            { title: t('message_template.not_made'), dataIndex: 'acciones', key: 'acciones' },
        ];

        const cierreVentaOptions = [
            { value: 'CERTIFICADA', label: t('common.certified') },
            { value: 'RELLAMADA_CERTIFICADA', label: t('common.certified_recall') },
            { value: 'RELLAMADA', label: t('common.recall') },
            { value: 'CANCELADA_CALIDAD', label: t('common.cancelled_quality') },
            { value: 'CANCELADA_SUPERVISION', label: t('common.cancelled_supervision') },
            { value: 'REASIGNADA', label: t('common.reassigned') },
        ];

        const submitQuality = async () => {
            loading.value = true;
            try {
                await formRef.value.validate();
                datos.calidad.nota_estado = calificacion.value;
                datos.calidad.user_id = props.data.is_sale && props.data.is_sale.user ? props.data.is_sale.user.id : null;
                addEditRequestAdmin({
                    url: 'calidad/save',
                    data: datos.calidad,
                    successMessage: t("common.created"),
                    success: (res) => { 
                        loading.value = false;
                        emit("addEditDelete");
                    },
                });

            } catch (error) {
                loading.value = false;
                console.log('Errores de validación:', error.errorFields)
            }
        };

        const onDelete = async () => {
            loading.value = true;
            try {
                const resp = await axiosAdmin.get(`delete-calidad/{${props.data.is_sale.idVenta}}`);
                if(resp.success){
                    loading.value = false;
                    notification.success({ message: t(`common.success`), description: t(`common.deleted`) });
                    emit("addEditDelete");
                }
            } catch (e) {
                loading.value = false;
                console.error('Error:', e);   
            }
        };

        const extraFilters = ref({
            lead_id: "",
        });


        // onMounted(() => {

        // });

        const onClose = () => {
            emit("closed");
        };

        const setNotesUrl = () => {
            emit("setNotesCount");
        };

        watch(
            () => props.visible,
            async(newVal) => {
                if (newVal) {
                    activeTabKey.value = "info_venta";
                    if (props.data.is_sale.aplicaBeneficiarios || props.data.is_sale.aplicaBeneficiariosAsist) {
                        let raw = props.data.is_sale.beneficiarios || '[]';
                        let raw2 = props.data.is_sale.beneficiariosAsist || '[]';
                        let list;
                        let list2;
                        try {
                            list = JSON.parse(raw);
                            list2 = JSON.parse(raw2);
                        } catch {
                            list = [];
                            list2 = [];
                        }
                        beneficiarios.value = list;
                        beneficiariosAsist.value = list2;
                    } else {
                        beneficiarios.value = [];
                        beneficiariosAsist.value = [];
                    }

                    if (props.data.is_sale.calidad) {
                        
                        datos.calidad.accion = 'edit';

                        try {
                            const resp = await axiosAdmin.get(`evaluaciones-calidad/{${props.data.is_sale.idVenta}}`);
                            await llenarInformacion(resp);
                            await obtenerPlantillaUso(datos.calidad.variables,resp.evaluacion_calidad.xPlantillaId);
                        } catch (e) {
                            console.error('Error:', e);   
                        }

                    } else {
                        isQualityDateExpired.value = false;
                        datos.calidad = getEmptyEvaluacionCalidad();
                        await obtenerPlantillaUso(null,null);
                        datos.calidad.variables = (plantillaCalidad.value?.variables ?? []).map(v => ({
                            ...v,
                            marcada: false
                        }));
                    }
                    
                    datos.calidad.idVenta = props.data.is_sale.idVenta;
                    datos.calidad.plantilla_id = plantillaCalidad.value ? plantillaCalidad.value.id : null;
                    datos.calidad.estadoVenta = props.data.is_sale.estadoVenta;

                    tableProducts.data = props.data.is_sale.productos.map(item => {
                        const prod = props.allProductos.find(p => p.xid === item.x_id_producto);
                        return {
                            xid:           item.x_id_producto,
                            internal_code: prod?.internal_code ?? '',
                            name:          prod?.name          ?? '',
                            coverage:      prod?.coverage      ?? '',
                            price:         item?.precio ?? 0,
                            product_quantity: item?.cantidadProducto ?? 0,
                        };
                    });
                    
                    tableProducts.pagination.total = tableProducts.data.length;

                    axiosAdmin.get(`campaigns/${props.data.lead.campaign.xid}/users`).then(res => {
                        agenteCampana.value = res;
                    });
                    
                }
            }
        );

        const llenarInformacion = async (data) =>{
            datos.calidad.fecha_calidad = data.evaluacion_calidad.fecha_calidad;
            datos.calidad.variables = data.evaluacion_calidad.variables;
            datos.calidad.duracion = data.evaluacion_calidad.duracion;
            datos.calidad.minuto_precio = data.evaluacion_calidad.minuto_precio;
            datos.calidad.cierre_venta = data.evaluacion_calidad.cierre_venta;
            datos.calidad.cerrado_por =  data.evaluacion_calidad.xCerradoPor;
            datos.calidad.accion_calidad_id =  data.evaluacion_calidad.xAccionCalidadId;
            datos.calidad.oportunidades =  data.evaluacion_calidad.oportunidades;
            datos.calidad.comentarios_oportunidades =  data.evaluacion_calidad.comentarios_oportunidades;
            datos.calidad.comentarios =  data.evaluacion_calidad.comentarios;
            datos.calidad.numero_poliza =  data.evaluacion_calidad.numero_poliza;

            datos.calidad.estado =  data.estado_calidad_venta.estado;
            datos.calidad.fecha_estado =  data.estado_calidad_venta.fecha_estado;
            datos.calidad.nota_estado =  data.estado_calidad_venta.nota_estado;
            datos.calidad.numero_certificado =  data.estado_calidad_venta.numero_certificado;
            datos.calidad.motivo_cancelacion_id =  data.estado_calidad_venta.xMotivoCancelacionId;
            datos.calidad.cancelado_por_supervision =  data.estado_calidad_venta.cancelado_por_supervision;
            datos.calidad.reasignado_a =  data.estado_calidad_venta.reasignado_a;

            variablesUse.splice(0, variablesUse.length);

            data.evaluacion_calidad.variables
            .filter(v => v.marcada)
            .forEach(v => {
                variablesUse.push({
                id: v.id,
                tipo: v.tipo,
                nota_maxima: v.nota_maxima
                })
            });

            calificacion.value =  data.estado_calidad_venta.nota_estado;

            const diffMs = Date.now() - new Date(datos.calidad.fecha_calidad).getTime();
            isQualityDateExpired.value = diffMs > 3 * 24 * 60 * 60 * 1000 ? true : false;

        };


        const obtenerPlantillaUso = async (variables,id) => {
            const campId = id ? id : props.data.lead?.campaign?.x_plantilla_calidad_id;
            const source = props.allCalidadTemplates.find(tpl => tpl.xid === campId);

            if (campId && source) {
                plantillaCalidad.value = {
                    ...source,
                    variables: source.variables ? source.variables.map(v => ({ ...v, marcada: false })) : []
                };
            } else {
                plantillaCalidad.value = undefined;
            }
            
            if (!variables) {
                calificacion.value = 100;
            } else {
                plantillaCalidad.value.variables = variables.map(v => ({ ...v }));
            }
        };



        const calcularNota = () => {
            if (variablesUse.some(v => v.tipo === 'critica')) {
                return 0;
            }

            const totalRestar = variablesUse
                .filter(v => v.tipo !== 'critica')
                .reduce((sum, v) => sum + v.nota_maxima, 0);

            return Math.max(0, notaInicial - totalRestar);
        };

        const selectVariable = (variable) => {
            const idx = variablesUse.findIndex(v => v.id === variable.id);
            const variableInModel = datos.calidad.variables.find(v => v.id === variable.id);

            if (idx === -1) {
                variablesUse.push({ id: variable.id, tipo: variable.tipo, nota_maxima: variable.nota_maxima });
                if (variableInModel) variableInModel.marcada = true;
            } else {
                variablesUse.splice(idx, 1);
                if (variableInModel) variableInModel.marcada = false;
            }

            calificacion.value = calcularNota();
        };

        const screens = Grid.useBreakpoint();
        const descLayout = computed(() =>
            (screens.value.xs || screens.value < 950) ? 'vertical' : 'horizontal'
        );

        const handleTableChange = (pagination) => {
            tableProducts.pagination.current = pagination.current;
            tableProducts.pagination.pageSize = pagination.pageSize;
        };

        return {
            isQualityDateExpired,
            loading,
            agenteCampana,
            handleTableChange,
            tableProducts,
            datos,
            formRef,
            rules,
            submitQuality,
            onDelete,
            cierreVentaOptions,
            descLayout,
            calificacion,
            selectVariable,
            columns,
            columnsProduct,
            plantillaCalidad,
            ...crudVariables,
            drawerTitle,
            beneficiarios,
            beneficiariosAsist,
            formatTimeDuration,
            activeTabKey,
            setNotesUrl,
            onClose,
            formatDateTime,
            formatAmountCurrency,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "60%",
        };
    },
});
</script>
<style scoped>
    :deep(.checkbox-x .ant-checkbox-input:checked + .ant-checkbox-inner::after) {
        border: none !important;
        transform: none !important;
        content: '×' !important;
        position: absolute !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        font-size: 14px !important;
        color: white !important;
    }
    :deep(.label-bold .ant-descriptions-item-label) {
        font-weight: bold;
    }
</style>