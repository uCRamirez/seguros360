<template>
    <!-- :width="isSale ? 1550 : 600" -->
    <a-drawer placement="left" :width="drawerWidth" :open="visible" :closable="false" :centered="true"
        :title="pageTitle" @ok="onSubmit">
        <a-form layout="vertical" ref="formRef" :model="datos.venta" :rules="isSale ? validationRules : null">
            <a-row :gutter="[16, 16]">
                <!-- COLUMNA IZQUIERDA: formulario principal -->
                <a-col :xs="24" :sm="24" :md="isSale ? 5 : 24" :lg="isSale ? 5 : 24">
                    <a-row :gutter="[16, 16]">
                        <!-- Nivel 1 -->
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item class="required floating-form-item phone-select" name="notes_typification_id_1"
                                :help="rules.notes_typification_id_1 ? rules.notes_typification_id_1.message : null"
                                :validateStatus="rules.notes_typification_id_1 ? 'error' : null">
                                <div class="floating-input floating-select"
                                    :class="{ 'has-value': !!datos.venta.telVenta }">
                                    <a-select v-model:value="formData.notes_typification_id_1"
                                        :placeholder="$t('common.select_default_text', [$t('lead_notes.notes_typification_1')])"
                                        optionFilterProp="title" show-search :allowClear="true" @change="() => {
                                            formData.notes_typification_id_2 = undefined;
                                            formData.notes_typification_id_3 = undefined;
                                            formData.notes_typification_id_4 = undefined;
                                            getChildTypification(formData.notes_typification_id_1);
                                        }">
                                        <a-select-option v-for="parentTypification in filteredParentTypifications"
                                            :key="parentTypification.xid" :title="parentTypification.name"
                                            :value="parentTypification.xid">
                                            {{ parentTypification.name }}
                                        </a-select-option>
                                    </a-select>
                                </div>
                            </a-form-item>
                        </a-col>

                        <!-- Nivel 2 -->
                        <a-col v-if="childrenTypificationData.length > 0" :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item class="required floating-form-item phone-select" name="notes_typification_id_2"
                                :help="rules.notes_typification_id_2 ? rules.notes_typification_id_2.message : null"
                                :validateStatus="rules.notes_typification_id_2 ? 'error' : null">
                                <div class="floating-input floating-select"
                                    :class="{ 'has-value': !!formData.notes_typification_id_2 }">
                                    <a-select v-model:value="formData.notes_typification_id_2"
                                        :placeholder="$t('common.select_default_text', [$t('lead_notes.notes_typification_2')])"
                                        optionFilterProp="title" show-search :allowClear="true" @change="() => {
                                            formData.notes_typification_id_3 = undefined;
                                            formData.notes_typification_id_4 = undefined;
                                            getChildrenChildTypification(formData.notes_typification_id_2);
                                        }">
                                        <a-select-option v-for="childrenTypification in childrenTypificationData"
                                            :key="childrenTypification.xid" :title="childrenTypification.name"
                                            :value="childrenTypification.xid">
                                            {{ childrenTypification.name }}
                                        </a-select-option>
                                    </a-select>
                                </div>
                            </a-form-item>
                        </a-col>

                        <!-- Nivel 3 -->
                        <a-col v-if="childrenChildData.length > 0" :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item class="required floating-form-item phone-select" name="notes_typification_id_3"
                                :help="rules.notes_typification_id_3 ? rules.notes_typification_id_3.message : null"
                                :validateStatus="rules.notes_typification_id_3 ? 'error' : null">
                                <div class="floating-input floating-select"
                                    :class="{ 'has-value': !!formData.notes_typification_id_3 }">
                                    <a-select v-model:value="formData.notes_typification_id_3"
                                        :placeholder="$t('common.select_default_text', [$t('lead_notes.notes_typification_3')])"
                                        optionFilterProp="title" show-search :allowClear="true" @change="() => {
                                            formData.notes_typification_id_4 = undefined;
                                            getLastChildrenChildTypification(formData.notes_typification_id_3);
                                        }">
                                        <a-select-option v-for="childrenChild in childrenChildData"
                                            :key="childrenChild.xid" :title="childrenChild.name"
                                            :value="childrenChild.xid">
                                            {{ childrenChild.name }}
                                        </a-select-option>
                                    </a-select>
                                </div>
                            </a-form-item>
                        </a-col>

                        <!-- Nivel 4 -->
                        <a-col v-if="lastChildrenChildData.length > 0" :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item class="required floating-form-item phone-select" name="notes_typification_id_4"
                                :help="rules.notes_typification_id_4 ? rules.notes_typification_id_4.message : null"
                                :validateStatus="rules.notes_typification_id_4 ? 'error' : null">
                                <div class="floating-input floating-select"
                                    :class="{ 'has-value': !!formData.notes_typification_id_4 }">
                                    <a-select v-model:value="formData.notes_typification_id_4"
                                        :placeholder="$t('common.select_default_text', [$t('lead_notes.notes_typification_4')])"
                                        optionFilterProp="title" show-search :allowClear="true"
                                        @change="getAccion(formData.notes_typification_id_4)">
                                        <a-select-option v-for="lastChildrenChild in lastChildrenChildData"
                                            :key="lastChildrenChild.xid" :title="lastChildrenChild.name"
                                            :value="lastChildrenChild.xid">
                                            {{ lastChildrenChild.name }}
                                        </a-select-option>
                                    </a-select>
                                </div>
                            </a-form-item>
                        </a-col>


                        <!-- Comentario de la nota -->
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item class="floating-form-item" name="notes"
                                :help="rules.notes ? rules.notes.message : null"
                                :validateStatus="rules.notes ? 'error' : null">
                                <div class="floating-input">
                                    <a-textarea v-model:value="formData.notes"
                                        :placeholder="$t('common.placeholder_default_text', [$t('common.notes')])"
                                        :rows="4" :maxlength="2000" />
                                    <!-- <label>{{ $t('common.notes')}}</label> -->
                                </div>
                            </a-form-item>
                        </a-col>

                        <!-- campo para proximo contacto -->
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item :label="$t('common.next_contact')" name="next_contact"
                                :help="rules.next_contact ? rules.next_contact.message : null"
                                :validateStatus="rules.next_contact ? 'error' : null" class="label-bold">
                                <a-config-provider :locale="antdLocale">
                                    <a-date-picker :disabled="!schedule" v-model:value="formData.next_contact" show-time
                                        :format="'YYYY-MM-DD HH:mm'" value-format="YYYY-MM-DD HH:mm" />
                                </a-config-provider>
                            </a-form-item>
                        </a-col>

                        <!-- Adjunto + checkbox -->
                        <a-col :xs="24" :sm="24" :md="24" :lg="24">
                            <a-form-item class="label-bold" :label="$t('lead_notes.notes_file')">
                                <div class="label-bold" style="display: flex; align-items: center; gap: 12px;">
                                    <UploadFile :acceptFormat="'image/*,.pdf'" :formData="formData" folder="leadNotes"
                                        uploadField="notes_file" @onFileUploaded="file => {
                                            formData.notes_file = file.file;
                                            formData.notes_file_url = file.file_url;
                                        }" />
                                    <a-checkbox :disabled="addEditType == 'edit'" :checked="isSale">
                                        <strong>{{ $t('lead_notes.sale') }}</strong>
                                    </a-checkbox>
                                </div>
                            </a-form-item>
                        </a-col>
                    </a-row>
                </a-col>

                <!-- COLUMNA DERECHA: campos adicionales -->
                <a-col v-if="isSale" :xs="24" :sm="24" :md="19" :lg="19">
                    <div class="side-panel" style="display: flex; align-items: flex-start; gap: 12px;">

                        <a-col :xs="16" :sm="16" :md="8" :lg="8">

                            <!-- <a-col :xs="24">
                                <a-form-item class="floating-form-item">
                                    <div class="floating-input">
                                        <a-input v-model:value="datos.venta.idLead" placeholder=" " />
                                        <label>{{ $t('lead.id') }}</label>
                                    </div>
                                </a-form-item>
                            </a-col> -->

                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <!-- Cédula -->
                                <a-form-item class="floating-form-item">
                                    <div class="floating-input">
                                        <a-input v-model:value="datos.venta.cedula" placeholder=" " />
                                        <label class="floating-label">
                                            {{ $t('lead.document') }}
                                        </label>
                                    </div>
                                </a-form-item>
                            </a-col>

                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <!-- Nombre cliente completo -->
                                <a-form-item class="floating-form-item">
                                    <div class="floating-input">
                                        <a-input v-model:value="datos.venta.nombre" placeholder=" " />
                                        <label class="floating-label">
                                            {{ $t('lead.name') }}
                                        </label>
                                    </div>
                                </a-form-item>
                            </a-col>

                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <!-- Email -->
                                <a-form-item class="floating-form-item">
                                    <div class="floating-input">
                                        <a-input v-model:value="datos.venta.email" placeholder=" " />
                                        <label class="floating-label">
                                            {{ $t('lead.email') }}
                                        </label>
                                    </div>
                                </a-form-item>
                            </a-col>

                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <!-- Agente -->
                                <a-form-item class="floating-form-item">
                                    <div class="floating-input">
                                        <a-input v-model:value="datos.venta.agente" placeholder=" " />
                                        <label class="floating-label">
                                            {{ $t('lead.agent') }}
                                        </label>
                                    </div>
                                </a-form-item>
                            </a-col>

                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <!-- Teléfonos -->
                                <a-form-item v-if="leadInfo" name="telVenta" class="floating-form-item">
                                    <div class="floating-input floating-select"
                                        :class="{ 'has-value': !!datos.venta.telVenta }">
                                        <a-select v-model:value="datos.venta.telVenta" placeholder=" " class="phone-select"
                                            style="width: 100%;">
                                            <a-select-option v-for="tel in [
                                                leadInfo.tel1,
                                                leadInfo.tel2,
                                                leadInfo.tel3,
                                                leadInfo.tel4,
                                                leadInfo.tel5,
                                                leadInfo.tel6
                                            ].filter(t => t)" :key="tel" :value="tel">
                                                {{ tel }}
                                            </a-select-option>
                                        </a-select>

                                        <label class="floating-label">
                                            {{ $t('lead.phone') }}
                                        </label>
                                    </div>
                                </a-form-item>

                                <a-form-item v-else name="telVenta" class="floating-form-item">
                                    <div class="floating-input">
                                        <a-input v-model:value="datos.venta.telVenta" placeholder=" " />
                                        <label class="floating-label">
                                            {{ $t('lead.phone') }}
                                        </label>
                                    </div>
                                </a-form-item>
                            </a-col>

                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <!-- tarjeta -->
                                <a-form-item class="floating-form-item" name="tarjeta">
                                    <div class="floating-input">
                                        <a-input v-model:value="datos.venta.tarjeta" placeholder=" " />
                                        <label class="floating-label">
                                            {{ $t('lead.card') }}
                                        </label>
                                    </div>
                                </a-form-item>
                            </a-col>

                            <a-divider>{{ $t('common.product') }}</a-divider>

                            <!-- Código -->
                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <a-form-item name="internal_code" class="floating-form-item">
                                    <div class="floating-input floating-select"
                                        :class="{ 'has-value': !!datosProducto.producto.internal_code }">
                                        <a-select v-model:value="datosProducto.producto.internal_code" placeholder=" "
                                            style="width: 100%;" show-search option-filter-prop="title"
                                            :allowClear="true">
                                            <a-select-option v-for="code in internalCodeOptions" :key="code"
                                                :value="code" :title="code">
                                                {{ code }}
                                            </a-select-option>
                                        </a-select>

                                        <label class="floating-label">
                                            {{ $t('lead.internal_code') }}
                                        </label>
                                    </div>
                                </a-form-item>
                            </a-col>

                            <!-- Producto -->
                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <a-form-item name="producto" class="floating-form-item">
                                    <div class="floating-input floating-select"
                                        :class="{ 'has-value': !!datosProducto.producto.name }">
                                        <a-select v-model:value="datosProducto.producto.name" placeholder=" "
                                            style="width: 100%;" show-search option-filter-prop="title"
                                            :allowClear="true">
                                            <a-select-option v-for="name in productNameOptions" :key="name"
                                                :value="name" :title="name">
                                                {{ name }}
                                            </a-select-option>
                                        </a-select>

                                        <label class="floating-label">
                                            {{ $t('lead.product') }}
                                        </label>
                                    </div>
                                </a-form-item>
                            </a-col>

                            <!-- Cobertura -->
                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <a-form-item name="coverage" class="floating-form-item">
                                    <div class="floating-input floating-select"
                                        :class="{ 'has-value': !!datosProducto.producto.coverage }">
                                        <a-select v-model:value="datosProducto.producto.coverage" placeholder=" "
                                            style="width: 100%;"
                                            :disabled="(!datosProducto.producto.internal_code || !datosProducto.producto.name)"
                                            show-search option-filter-prop="title" :allowClear="true">
                                            <a-select-option v-for="cov in coverageOptions" :key="cov" :value="cov"
                                                :title="cov">
                                                {{ cov }}
                                            </a-select-option>
                                        </a-select>

                                        <label class="floating-label">
                                            {{ $t('lead.coverage') }}
                                        </label>
                                    </div>
                                </a-form-item>
                            </a-col>

                            <!-- Cantidad -->
                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <a-form-item name="product_quantity" class="floating-form-item">
                                    <div class="floating-input"
                                        :class="{ 'has-value': datosProducto.producto.cantidadProducto !== null && datosProducto.producto.cantidadProducto !== undefined && datosProducto.producto.cantidadProducto !== '' }">
                                        <a-input-number :min="1" v-model:value="datosProducto.producto.cantidadProducto"
                                            :disabled="!datosProducto.producto.price" style="width:100%" />
                                    </div>
                                </a-form-item>
                            </a-col>

                            <div class="d-flex">
                                <!-- Precio -->
                                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                    <a-form-item name="precio" class="floating-form-item">
                                        <div v-if="matchingProducts.length > 1" class="floating-input floating-select"
                                            :class="{ 'has-value': !!datosProducto.producto.price }">
                                            <a-select v-model:value="datosProducto.producto.price" placeholder=" "
                                                style="width: 100%;" show-search option-filter-prop="title" allowClear>
                                                <a-select-option v-for="p in matchingProducts" :key="p.xid"
                                                    :value="p.price"
                                                    :title="p.x_currency_id ? formatAmountUsingCurrencyObject(p.price, p.currency) : formatAmountCurrency(p.price)">
                                                    {{
                                                        p.x_currency_id
                                                            ? formatAmountUsingCurrencyObject(p.price, p.currency)
                                                            : formatAmountCurrency(p.price)
                                                    }}
                                                </a-select-option>
                                            </a-select>

                                            <label class="floating-label">
                                                {{ $t('lead.price') }}
                                            </label>
                                        </div>

                                        <div v-else class="floating-input" :class="{ 'has-value': true }">
                                            <a-input-number :value="datosProducto.producto.x_currency_id
                                                ? formatAmountUsingCurrencyObject(datosProducto.producto.price, datosProducto.producto.currency)
                                                : formatAmountCurrency(datosProducto.producto.price)" disabled
                                                style="width:100%" />
                                        </div>
                                    </a-form-item>
                                </a-col>
                            </div>

                            <!-- Monto a digitar -->
                            <a-col :xs="24" :sm="24" :md="24" :lg="24" v-if="datosProducto.producto.digitar_precio">
                                <a-form-item name="precio_digitado" class="floating-form-item">
                                    <div class="floating-input"
                                        :class="{ 'has-value': datosProducto.producto.precio_digitado !== null && datosProducto.producto.precio_digitado !== undefined && datosProducto.producto.precio_digitado !== '' }">
                                        <a-input-number v-model:value="datosProducto.producto.precio_digitado" :min="0"
                                            style="width:100%" />
                                    </div>
                                </a-form-item>
                            </a-col>

                            <!-- Botón -->
                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <a-form-item class="floating-form-item">
                                    <div class="floating-input has-value">
                                        <a-button :disabled="!datosProducto.producto.price" type="primary" block
                                            @click="addProducto">
                                            <ShoppingCartOutlined />
                                            {{ $t('common.add') }} {{ $t('common.product') }}
                                        </a-button>
                                    </div>
                                </a-form-item>
                            </a-col>

                        </a-col>
                        <!-- Tabla de productos agregados -->
                        <a-col :xs="16" :sm="16" style="border-left: 1px solid #f0f0f0;" :md="16" :lg="16">

                            <div class="table-responsive">
                                <a-table :columns="columns" :data-source="table.data" :pagination="table.pagination"
                                    @change="handleTableChange" :scroll="scrollStyle" size="small">
                                    <template #bodyCell="{ column, record }">
                                        <template v-if="column.dataIndex === 'price'">
                                            {{ record.x_currency_id
                                                ? formatAmountUsingCurrencyObject(record.precio_digitado > 0 ?
                                                    record.precio_digitado : record.price, record?.currency)
                                                : formatAmountCurrency(record.precio_digitado > 0 ? record.precio_digitado :
                                                    record.price) }}
                                        </template>

                                        <template v-if="column.dataIndex === 'action'">
                                            <a-tooltip :title="$t('common.delete')">
                                                <a-button type="primary" danger @click="removeProducto(record.xid)"
                                                    style="margin-left: 4px">
                                                    <template #icon>
                                                        <DeleteOutlined />
                                                    </template>
                                                </a-button>
                                            </a-tooltip>
                                        </template>
                                    </template>

                                    <template #footer>
                                        <div class="text-center">
                                            <strong>{{ $t('lead.total_amount') }} : {{ datos.venta.montoTotal
                                            }}</strong>
                                        </div>
                                    </template>
                                </a-table>
                            </div>

                            <!-- Toggles beneficiarios -->
                            <a-col :xs="24" :sm="24" :md="24" :lg="24">
                                <!-- Beneficiarios seguros % -->
                                <a-row gutter="12" style="width:100%">
                                    <a-col :span="12">
                                        <a-form-item class="label-bold" :label="$t('lead.beneficiaries')">
                                            <a-checkbox v-model:checked="esBeneficiario"></a-checkbox>
                                        </a-form-item>
                                    </a-col>

                                    <a-col :span="12" style="text-align: right">
                                        <a-form-item class="label-bold" :label="$t('lead.number_beneficiaries')">
                                            <a-input-number style="width:120px" :disabled="esBeneficiario === false"
                                                :min="0" :max="5" v-model:value="datos.venta.cantidadBeneficiarios" />
                                        </a-form-item>
                                    </a-col>
                                </a-row>

                                <!-- Beneficiarios asistencia % -->
                                <a-row gutter="12" style="width:100%">
                                    <a-col :span="12">
                                        <a-form-item class="label-bold" :label="$t('lead.beneficiaries_asist')">
                                            <a-checkbox v-model:checked="esBeneficiarioAsist"></a-checkbox>
                                        </a-form-item>
                                    </a-col>

                                    <a-col :span="12" style="text-align: right">
                                        <a-form-item class="label-bold" :label="$t('lead.number_beneficiaries')">
                                            <a-input-number style="width:120px"
                                                :disabled="esBeneficiarioAsist === false" :min="0" :max="5"
                                                v-model:value="datos.venta.cantidadBeneficiariosAsist" />
                                        </a-form-item>
                                    </a-col>
                                </a-row>
                            </a-col>

                            <a-col v-if="esBeneficiario || esBeneficiarioAsist" :xs="24" :sm="24" :md="24" :lg="24">
                                <!-- Beneficiarios seguros -->
                                <a-form-item v-if="esBeneficiario" class="label-bold" style="justify-content: center;"
                                    :label="$t('lead.beneficiary_information')">
                                    <a-space v-for="(benef, i) in datos.venta.beneficiarios" :key="i"
                                        style="display: flex; align-items: center; width: 100%;">

                                        <!-- Nombre más ancho -->
                                        <a-form-item :name="['beneficiarios', i, 'nombre']"
                                            :rules="[{ required: true, message: $t('lead.name') }]"
                                            style="margin-right: 8px;">
                                            <a-input v-model:value="benef.nombre" :placeholder="$t('lead.name')" />
                                        </a-form-item>

                                        <!-- Porcentaje más angosto -->
                                        <a-form-item :name="['beneficiarios', i, 'porcentaje']"
                                            :rules="[{ required: true, type: 'number', message: $t('uphone_calls.number') }]">
                                            <a-input-number @change="value => validarSumaMaxima(i, value)"
                                                v-model:value="benef.porcentaje" :min="0" :max="100" :precision="0"
                                                :step="1" :placeholder="$t('lead.percentage')" style="width: 100%;" />
                                        </a-form-item>

                                        <!-- Botón eliminar -->
                                        <a-form-item>
                                            <MinusCircleOutlined @click="removeUser(i)" />
                                        </a-form-item>
                                    </a-space>


                                    <a-form-item class="text-center">
                                        <h4 :style="{ color: suma > 100 ? 'red' : 'inherit', fontWeight: 'bold' }">
                                            <a-tooltip :style="{ color: suma > 100 ? 'red' : 'inherit' }"
                                                :title="$t('message_template.maximum_percentage_exceded')">
                                                <InfoCircleOutlined />
                                            </a-tooltip>
                                            {{ $t('common.total') }}: {{ suma }}
                                        </h4>
                                    </a-form-item>

                                    <a-form-item>
                                        <a-button :disabled="datos.venta.cantidadBeneficiarios === 5 || suma >= 100"
                                            type="dashed" block @click="addUser">
                                            <PlusOutlined />
                                            {{ $t('common.add') }}
                                        </a-button>
                                    </a-form-item>
                                </a-form-item>

                                <!-- Beneficiarios asistencia -->
                                <a-form-item v-if="esBeneficiarioAsist" class="label-bold"
                                    style="justify-content: center; border-top: 1px solid #f0f0f0; padding-top: 12px;"
                                    :label="$t('lead.beneficiary_asist_information')">
                                    <a-space v-for="(benef, i) in datos.venta.beneficiariosAsist" :key="i"
                                        style="display: flex; align-items: center; width: 100%;">

                                        <a-form-item :name="['beneficiariosAsist', i, 'nombre']"
                                            :rules="[{ required: true, message: $t('lead.name') }]"
                                            style="flex: 2; margin-right: 8px;">
                                            <a-input v-model:value="benef.nombre" :placeholder="$t('lead.name')" />
                                        </a-form-item>

                                        <a-form-item :name="['beneficiariosAsist', i, 'porcentaje']"
                                            :rules="[{ required: true, type: 'number', message: $t('uphone_calls.number') }]"
                                            style="flex: 1; margin-right: 8px;">
                                            <a-input-number @change="value => validarSumaMaximaAsist(i, value)"
                                                v-model:value="benef.porcentaje" :min="0" :max="100" :precision="0"
                                                :step="1" :placeholder="$t('lead.percentage')" style="width: 100%;" />
                                        </a-form-item>

                                        <a-form-item>
                                            <MinusCircleOutlined @click="removeUserAsis(i)" />
                                        </a-form-item>
                                    </a-space>


                                    <a-form-item class="text-center">
                                        <h4 :style="{ color: sumaAsist > 100 ? 'red' : 'inherit', fontWeight: 'bold' }">
                                            <a-tooltip :style="{ color: sumaAsist > 100 ? 'red' : 'inherit' }"
                                                :title="$t('message_template.maximum_percentage_exceded')">
                                                <InfoCircleOutlined />
                                            </a-tooltip>
                                            {{ $t('common.total') }}: {{ sumaAsist }}
                                        </h4>
                                    </a-form-item>

                                    <a-form-item>
                                        <a-button
                                            :disabled="datos.venta.cantidadBeneficiariosAsist === 5 || sumaAsist >= 100"
                                            type="dashed" block @click="addUserAsis">
                                            <PlusOutlined />
                                            {{ $t('common.add') }}
                                        </a-button>
                                    </a-form-item>
                                </a-form-item>
                            </a-col>

                        </a-col>

                    </div>
                </a-col>

            </a-row>
        </a-form>

        <template #footer>
            <div class="text-center" style="padding: 1%;">
                <a-button style="margin-right: 5px;"
                    v-if="addEditType == 'add' && (permsArray.includes('admin') || permsArray.includes('forms_view'))"
                    key="submit" type="primary" :loading="loading" @click="onSubmit">
                    <SaveOutlined />
                    {{ $t("common.create") }}
                </a-button>
                <a-button style="margin-right: 5px;"
                    v-if="addEditType == 'edit' && (permsArray.includes('admin') || permsArray.includes('notes_edit'))"
                    key="submit" type="primary" :loading="loading" @click="onSubmit">
                    <SaveOutlined />
                    {{ $t("common.update") }}
                </a-button>
                <a-button key="back" @click="onClose">
                    {{ $t("common.cancel") }}
                </a-button>
            </div>
        </template>
    </a-drawer>
</template>

<script>
import { defineComponent, ref, reactive, onMounted, onUnmounted, nextTick, watch, computed, toRef } from "vue";
import { PlusOutlined, DeleteOutlined, ShoppingCartOutlined, InfoCircleOutlined, MinusCircleOutlined, LoadingOutlined, SaveOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../common/composable/apiAdmin";
import common from "../../../common/composable/common";
import UploadFile from "../../../common/core/ui/file/UploadFile.vue";
import { useI18n } from "vue-i18n";
import { message } from 'ant-design-vue';
import esES from 'ant-design-vue/es/locale/es_ES';
import enUS from 'ant-design-vue/es/locale/en_US';
import 'dayjs/locale/es';
import 'dayjs/locale/en';
import dayjs from 'dayjs';

function getEmptyVenta() {
    return {
        idLead: null,
        cedula: "",
        nombre: "",
        email: "",
        agente: "",
        user_id: null,
        nombreBase: "",
        tarjeta: "",
        productos: [],
        estadoVenta: "Efectiva",
        telVenta: null,
        aplicaBeneficiarios: false,
        cantidadBeneficiarios: 0,
        beneficiarios: [],
        aplicaBeneficiariosAsist: false,
        cantidadBeneficiariosAsist: 0,
        beneficiariosAsist: [],
        montoTotal: 0,
    };
}

function getEmptyProducto() {
    return {
        idProducto: null,
        internal_code: null,
        name: null,
        coverage: null,
        price: 0,
        precio_digitado: 0,
        cantidadProducto: 0,
        digitar_precio: false,
        x_currency_id: null,
        currency: null,
    };
}

export const datos = reactive({
    venta: getEmptyVenta(),
});

export const datosProducto = reactive({
    producto: getEmptyProducto(),
});

export default defineComponent({
    props: [
        "soloVer",
        "formData",
        "leadInfo",
        "allCampaigns",
        "allProductos",
        "data",
        "visible",
        "url",
        "addEditType",
        "pageTitle",
        "successMessage",
    ],
    components: {
        PlusOutlined,
        ShoppingCartOutlined,
        DeleteOutlined,
        MinusCircleOutlined,
        InfoCircleOutlined,
        LoadingOutlined,
        SaveOutlined,
        UploadFile,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { permsArray, formatAmountCurrency, formatAmountUsingCurrencyObject } = common();
        const { locale, t } = useI18n();
        const soloVer = ref(props.soloVer);

        var myId = JSON.parse(localStorage.getItem("auth_user") || "{}").id;

        // Typifications
        const notesTypifications = ref([]);
        const notesTypificationUrl = "notes-typifications?fields=id,xid,name,parent_id,x_parent_id,campaign_id,x_campaign_id,sale,schedule,status,campaign{id,xid,name}&limit=10000";
        const parentTypificationData = ref([]);
        const childrenTypificationData = ref([]);
        const childrenChildData = ref([]);
        const lastChildrenChildData = ref([]);
        const isInitializing = ref(true);
        const suma = ref(0);
        const sumaAsist = ref(0);
        const campaigns_id = ref([]);

        const filteredProductosByCampaign = computed(() =>
            props.allProductos.filter(p =>
                campaigns_id.value.includes(p.x_campaign_id)
            )
        );

        const antdLocale = computed(() =>
            locale.value === 'en' ? enUS : esES
        );
        watch(locale, (newLang) => {
            dayjs.locale(newLang);
        });

        const columns = [
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
            {
                title: t("common.action"),
                dataIndex: 'action',
                fixed: 'right',
            },
        ];

        const table = reactive({
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

        const handleTableChange = (pagination) => {
            table.pagination.current = pagination.current;
            table.pagination.pageSize = pagination.pageSize;
        };
        const addProducto = () => {
            const p = datosProducto.producto;

            if (!p.idProducto) {
                message.info(t('lead.not_found'));
                return;
            }

            const yaExiste = table.data.some(item => item.xid === p.idProducto);
            if (yaExiste) {
                message.warning(t("messages.product_already_added") || "Este producto ya fue agregado");
                return;
            }

            let precio = p.digitar_precio && p.precio_digitado > 0 ? parseFloat(p.precio_digitado) : parseFloat(p.price);
            const nuevo = {
                xid: p.idProducto,
                internal_code: p.internal_code,
                name: p.name,
                coverage: p.coverage,
                price: p.price,
                product_quantity: p.cantidadProducto,
                precio_digitado: p.precio_digitado,
                digitar_precio: p.digitar_precio,
                x_currency_id: p?.x_currency_id,
                currency: p?.currency,
            };

            table.data = [...table.data, nuevo];
            table.pagination.total = table.data.length;

            const totalActual = parseFloat(datos.venta.montoTotal) || 0;
            const incremento = parseFloat(precio) * parseFloat(p.cantidadProducto);
            datos.venta.montoTotal = Number((totalActual + incremento).toFixed(2));

            Object.assign(datosProducto.producto, getEmptyProducto());
        };

        function removeProducto(xid) {
            // 1) Busca el ítem a eliminar
            const item = table.data.find(row => row.xid === xid);
            if (item) {
                let precio = item.precio_digitado > 0 ? item.precio_digitado : item.price;
                // 2) Resta subtotal (price * cantidad) de datos.venta.montoTotal
                datos.venta.montoTotal -= precio * item.product_quantity;
            }

            // 3) Luego filtra el array
            table.data = table.data.filter(row => row.xid !== xid);
            table.pagination.total = table.data.length;
        }


        onMounted(() => {
            window.addEventListener('resize', onResize);
            axiosAdmin.get(notesTypificationUrl).then(res => {
                notesTypifications.value = res.data;
                getParentTypification();
            });
        });

        function getParentTypification() {
            parentTypificationData.value = [];
            notesTypifications.value.forEach(p => {
                if (p.x_parent_id == null && p.status === 1) parentTypificationData.value.push(p);
            });
            // getAccion(xid);
        }
        function getChildTypification(xid) {
            childrenTypificationData.value = [];
            childrenChildData.value = [];
            lastChildrenChildData.value = [];
            notesTypifications.value.forEach(c => {
                if (c.x_parent_id === xid && c.status === 1) childrenTypificationData.value.push(c);
            });
            getAccion(xid);
        }
        function getChildrenChildTypification(xid) {
            childrenChildData.value = [];
            lastChildrenChildData.value = [];
            notesTypifications.value.forEach(c => {
                if (c.x_parent_id === xid && c.status === 1) childrenChildData.value.push(c);
            });
            getAccion(xid);
        }
        function getLastChildrenChildTypification(xid) {
            lastChildrenChildData.value = [];
            notesTypifications.value.forEach(c => {
                if (c.x_parent_id === xid && c.status === 1) lastChildrenChildData.value.push(c);
            });
            getAccion(xid);
        }
        const getAccion = (xid) => {
            if (props.addEditType === 'add') {
                if (xid) {
                    let actionObject = notesTypifications.value.find(item => item.xid === xid);
                    if (actionObject) {
                        if (!schedule.value) {
                            schedule.value = actionObject.schedule;
                        }
                        if (!isSale.value) {
                            isSale.value = actionObject.sale;
                        }
                    }
                } else {
                    schedule.value = false;
                    isSale.value = false;
                }
            }
        };

        // Flags sale/beneficiarios
        const isSale = ref(false);
        const schedule = ref(false);
        const esBeneficiario = ref(false);
        const esBeneficiarioAsist = ref(false);

        const filteredByCode = computed(() =>
            datosProducto.producto.internal_code
                ? filteredProductosByCampaign.value.filter(p =>
                    p.internal_code === datosProducto.producto.internal_code
                )
                : filteredProductosByCampaign.value
        );
        const filteredByName = computed(() =>
            datosProducto.producto.name
                ? filteredProductosByCampaign.value.filter(p =>
                    p.name === datosProducto.producto.name
                )
                : filteredProductosByCampaign.value
        );
        const intersection = computed(() => {
            let arr = filteredProductosByCampaign.value.slice();
            if (datosProducto.producto.internal_code) {
                arr = arr.filter(p => p.internal_code === datosProducto.producto.internal_code);
            }
            if (datosProducto.producto.name) {
                arr = arr.filter(p => p.name === datosProducto.producto.name);
            }
            return arr;
        });

        const matchingProducts = computed(() =>
            intersection.value.filter(p => p.coverage === datosProducto.producto.coverage)
        );
        const internalCodeOptions = computed(() =>
            [...new Set(filteredByName.value.map(p => p.internal_code))]
        );
        const productNameOptions = computed(() =>
            [...new Set(filteredByCode.value.map(p => p.name))]
        );
        const coverageOptions = computed(() =>
            [...new Set(intersection.value.map(p => p.coverage))]
        );
        const finalProduct = computed(() =>
            intersection.value.find(p => p.coverage === datosProducto.producto.coverage) || {}
        );

        // Watchers refactorizados
        watch(() => datosProducto.producto.internal_code, internal_code => {
            if (isInitializing.value) return;
            if (internal_code && !productNameOptions.value.includes(datosProducto.producto.name)) {
                datosProducto.producto.name = null;
            }
            datosProducto.producto.coverage = null;
            datosProducto.producto.price = 0;
            datosProducto.producto.digitar_precio = false;
        });

        watch(() => datosProducto.producto.name, prod => {
            if (isInitializing.value) return;
            if (prod && !internalCodeOptions.value.includes(datosProducto.producto.internal_code)) {
                datosProducto.producto.internal_code = null;
            }
            datosProducto.producto.coverage = null;
            datosProducto.producto.digitar_precio = false;
            datosProducto.producto.x_currency_id = null;
            datosProducto.producto.currency = null;
        });

        watch(() => datosProducto.producto.coverage, cov => {
            if (isInitializing.value) return;
            const precio = finalProduct.value.price || 0;
            datosProducto.producto.price = precio;
            datosProducto.producto.cantidadProducto = cov
                ? (props.addEditType === 'edit'
                    ? datosProducto.producto.cantidadProducto
                    : 1
                )
                : 0;
            datosProducto.producto.x_currency_id = finalProduct.value.x_currency_id;
            datosProducto.producto.currency = finalProduct.value.currency;
            // datos.venta.montoTotal = precio * datosProducto.producto.cantidadProducto;
        });

        watch(() => datosProducto.producto.price, (nuevoPrecio) => {
            if (isInitializing.value) return;
            if (!nuevoPrecio) {
                datosProducto.producto.cantidadProducto = 0;
                // datos.venta.montoTotal = 0;
            } else {
                datosProducto.producto.cantidadProducto = 1;
                // datos.venta.montoTotal = nuevoPrecio;
            }
        },
            { immediate: true }
        );

        watch(
            matchingProducts,
            (lista) => {
                if (isInitializing.value) return;
                if (lista.length >= 1) {
                    const p = lista[0];
                    datosProducto.producto.idProducto = p.xid;
                    datosProducto.producto.price = p.price;
                    datosProducto.producto.digitar_precio = p.digitar_precio;
                }
                // else {
                //     datosProducto.producto.idProducto = null;
                //     datosProducto.producto.price = null;
                //     datosProducto.producto.cantidadProducto = 0;
                //     datos.venta.montoTotal = 0;
                // }
            },
            { immediate: true }
        );

        const filteredParentTypifications = ref([]);

        // Mostrar/editar
        watch(() => props.visible, async newVal => {
            datos.venta = getEmptyVenta();
            getAccion(null);
            isSale.value = false;
            getParentTypification();
            childrenTypificationData.value = [];
            childrenChildData.value = [];
            lastChildrenChildData.value = [];
            campaigns_id.value = [];

            if (newVal && props.addEditType === "edit") {
                campaigns_id.value.push(props.data?.x_campaign_id || props.data?.lead?.campaign?.xid || 0);
                filteredParentTypifications.value = parentTypificationData.value.filter(pt =>
                    campaigns_id.value.includes(pt.x_campaign_id)
                );

                isInitializing.value = true;

                if (filteredParentTypifications.value.length !== 0) {
                    if (props.formData.notes_typification_id_1 != null)
                        getChildTypification(props.formData.notes_typification_id_1);
                    if (props.formData.notes_typification_id_2 != null)
                        getChildrenChildTypification(props.formData.notes_typification_id_2);
                    if (props.formData.notes_typification_id_3 != null)
                        getLastChildrenChildTypification(props.formData.notes_typification_id_3);
                } else {
                    props.formData.notes_typification_id_1 = null;
                    props.formData.notes_typification_id_2 = null;
                    props.formData.notes_typification_id_3 = null;
                    props.formData.notes_typification_id_4 = null;
                }

                datos.venta = (isSale.value = !!props.formData.is_sale)
                    ? { ...props.formData.is_sale }
                    : getEmptyVenta();

                table.data = datos.venta.productos.map(item => {
                    const prod = props.allProductos.find(p => p.xid === item.x_id_producto);
                    return {
                        xid: item.x_id_producto,
                        internal_code: prod?.internal_code ?? '',
                        name: prod?.name ?? '',
                        coverage: prod?.coverage ?? '',
                        price: item?.precio ?? 0,
                        product_quantity: item?.cantidadProducto ?? 0,
                        precio_digitado: item?.precio_digitado ?? 0,
                        digitar_precio: item?.precio_digitado > 0 ? true : false,
                        x_currency_id: prod?.x_currency_id ?? null,
                        currency: prod?.currency ?? null,
                    };
                });

                if (props.formData.is_sale && (props.formData.is_sale.aplicaBeneficiarios || props.formData.is_sale.aplicaBeneficiariosAsist)) {

                    esBeneficiario.value = props.formData.is_sale.aplicaBeneficiarios ? true : false;
                    esBeneficiarioAsist.value = props.formData.is_sale.aplicaBeneficiariosAsist ? true : false;

                    let raw = props.formData.is_sale.beneficiarios || '[]';
                    let raw2 = props.formData.is_sale.beneficiariosAsist || '[]';
                    let list;
                    let list2;
                    try {
                        list = JSON.parse(raw);
                        list2 = JSON.parse(raw2);
                    } catch {
                        list = [];
                        list2 = [];
                    }
                    datos.venta.beneficiarios = list;
                    datos.venta.beneficiariosAsist = list2;

                    suma.value = datos.venta.beneficiarios
                        .reduce((acc, b) => acc + Number(b.porcentaje || 0), 0);

                    sumaAsist.value = datos.venta.beneficiariosAsist
                        .reduce((acc, b) => acc + Number(b.porcentaje || 0), 0);

                } else {
                    suma.value = 0;
                    sumaAsist.value = 0;
                    esBeneficiario.value = false;
                    esBeneficiarioAsist.value = false;
                    datos.venta.beneficiarios = [];
                    datos.venta.beneficiariosAsist = [];
                }

            } else {

                if (props.leadInfo && props.leadInfo.campaign?.xid) {
                    campaigns_id.value.push(props.leadInfo.campaign.xid);
                } else if (props.leadInfo && props.leadInfo.campaign?.id) {
                    campaigns_id.value.push(props.allCampaigns.find(c => c.id === props.leadInfo.campaign.id)?.xid || 0);
                } else {
                    props.allCampaigns.forEach(item => {
                        campaigns_id.value.push(item.xid);
                    });
                }

                filteredParentTypifications.value = parentTypificationData.value.filter(pt =>
                    campaigns_id.value.includes(pt.x_campaign_id)
                );

                suma.value, sumaAsist.value = 0;
                table.data = [];
                table.pagination.total = 0;
                table.pagination.current = 1;
            }
            Object.assign(
                datos.venta,
                props.addEditType === "add" ? getEmptyVenta() : {},
                {
                    idLead: props.leadInfo ? props.leadInfo.id : props.data.lead.id,
                    cedula: props.leadInfo ? props.leadInfo.cedula : props.data.lead.cedula,
                    nombre: props.leadInfo ? `${props.leadInfo?.nombre ?? ''}  ${props.leadInfo?.apellido1 ?? ''} ${props.leadInfo?.apellido2 ?? ''}` : `${props.data?.lead?.nombre ?? ''} ${props.data?.lead?.apellido1 ?? ''} ${props.data?.lead?.apellido2 ?? ''}`,
                    email: props.leadInfo ? props.leadInfo.email : props.data.lead.email,

                    agente: props.addEditType === "edit"
                        ? (props.data.user?.name ?? "")
                        : (props.leadInfo.assign_to && props.leadInfo.assign_to.name ? props.leadInfo.assign_to.name : ''),

                    user_id: props.leadInfo ? (props.leadInfo.assign_to && props.leadInfo.assign_to.id ? props.leadInfo.assign_to.id : myId) : datos.venta.user_id,

                    nombreBase: props.leadInfo ? props.leadInfo.nombreBase : props.data.lead.nombreBase,

                    tarjeta: props.addEditType === "edit"
                        ? (props.formData.is_sale?.tarjeta ?? "")
                        : props.leadInfo.tarjeta,

                    telVenta: props.addEditType === "edit"
                        ? (props.formData.is_sale?.telVenta ?? null)
                        : null,

                }
            );

            // 3) Espera al siguiente tick para que Vue aplique todos los cambios
            await nextTick();
            isInitializing.value = false;
        });

        // Validaciones beneficiarios
        const formRef = ref();
        const validationRules = reactive({
            telVenta: [{ required: true, message: t('lead.phone') }],
            // internal_code: [{ required: true, message: t('lead.internal_code') }],
            // producto: [{ required: true, message: t('lead.product') }],
            // coverage: [{ required: true, message: t('lead.coverage') }],
            // precio: [{ required: true, message: t('lead.price') }],
            tarjeta: [{ required: true, message: t('lead.card') }],
            beneficiarios: [],
        });

        watch(
            () => datos.venta.cantidadBeneficiarios,
            count => {
                if (isInitializing.value) return;
                // recorta exceso
                datos.venta.beneficiarios.splice(count);
                if (datos.venta.cantidadBeneficiarios === 0) {
                    esBeneficiario.value = false;
                }
                // añade faltantes
                while (datos.venta.beneficiarios.length < count) {
                    datos.venta.beneficiarios.push({ nombre: "", porcentaje: 0 });
                }

            },
            { immediate: true }
        );

        watch(
            () => datos.venta.cantidadBeneficiariosAsist,
            count => {
                if (isInitializing.value) return;
                // recorta exceso
                datos.venta.beneficiariosAsist.splice(count);
                if (datos.venta.cantidadBeneficiariosAsist === 0) {
                    esBeneficiarioAsist.value = false;
                }
                // añade faltantes
                while (datos.venta.beneficiariosAsist.length < count) {
                    datos.venta.beneficiariosAsist.push({ nombre: "", porcentaje: 0 });
                }
            },
            { immediate: true }
        );

        const addUser = () => {
            datos.venta.cantidadBeneficiarios++;
        };

        const removeUser = (index) => {
            suma.value = Math.max(0, suma.value - (Number(datos.venta.beneficiarios[index].porcentaje) || 0));
            datos.venta.beneficiarios.splice(index, 1);
            datos.venta.cantidadBeneficiarios = datos.venta.beneficiarios.length;
            if (props.addEditType === 'edit' || props.formData.is_sale) {
                removeUserFormData(index);
            } else if (datos.venta.cantidadBeneficiarios === 0) {
                esBeneficiario.value = false;
            }
        };

        const addUserAsis = () => {
            datos.venta.cantidadBeneficiariosAsist++;
        };

        const removeUserAsis = (index) => {
            sumaAsist.value = Math.max(0, sumaAsist.value - (Number(datos.venta.beneficiariosAsist[index].porcentaje) || 0));
            datos.venta.beneficiariosAsist.splice(index, 1);
            datos.venta.cantidadBeneficiariosAsist = datos.venta.beneficiariosAsist.length;
            if (props.addEditType === 'edit' || props.formData.is_sale) {
                removeUserAsistFormData(index);
            } else if (datos.venta.cantidadBeneficiariosAsist === 0) {
                esBeneficiarioAsist.value = false;
            }
        };

        const removeUserFormData = (index) => {
            const arr = JSON.parse(props.formData.is_sale.beneficiarios || '[]')
            arr.splice(index, 1)
            props.formData.is_sale.beneficiarios = JSON.stringify(arr)
        }

        const removeUserAsistFormData = (index) => {
            const arr = JSON.parse(props.formData.is_sale.beneficiariosAsist || '[]')
            arr.splice(index, 1)
            props.formData.is_sale.beneficiariosAsist = JSON.stringify(arr)
        }


        const clearDataVenta = () => {
            datos.venta = getEmptyVenta();
        };

        const getInteractionInfo = async (key) => {
            try {
                const raw = localStorage.getItem("auth_user_ucontact");
                if (!raw) return null;

                const state = JSON.parse(raw);
                return state?.lastCall?.[0]?.[key] ?? null;
            } catch (e) {
                console.error(`Error leyendo ${key} de uContact`, e);
                return null;
            }
        };

        const getUcontactCallStatus = async () => {
            try {
                const raw = localStorage.getItem("auth_user_ucontact");
                if (!raw) return false;

                const state = JSON.parse(raw);
                return !!state?.isOnCall;
            } catch (e) {
                console.error("Error leyendo estado de llamada uContact", e);
                return false;
            }
        };


        // Submit
        const onSubmit = async () => {
            const isAgentOnCall = await getUcontactCallStatus();
            if (isAgentOnCall) {
                message.warn(t('messages.must_end_call'));
                return;
            }
            try {
                props.formData.phone = props.addEditType === 'add' ? null : props.formData.phone;
                props.formData.guid = props.addEditType === 'add' ? null : props.formData.guid;

                if (props.addEditType === 'add') {
                    const phone = await getInteractionInfo('number');
                    const guid = await getInteractionInfo('guid');


                    const tels = [
                        props.leadInfo?.tel1, props.leadInfo?.tel2, props.leadInfo?.tel3,
                        props.leadInfo?.tel4, props.leadInfo?.tel5, props.leadInfo?.tel6
                    ];

                    if (phone && tels.includes(phone)) {
                        props.formData.phone = phone;
                        props.formData.guid = guid;
                    }
                }

                props.formData.isSale = isSale.value ? 1 : 0;
                props.formData.campaign_id = props.leadInfo?.campaign?.xid ?? props.data.x_campaign_id;

                props.formData.lead_id ||= props.leadInfo.xid;

                if (isSale.value) {
                    await formRef.value.validate();
                    if (isSale.value && table.data.length === 0) {
                        message.info(t('common.minimum_product'));
                        return;
                    }

                    addEditRequestAdmin({
                        url: props.url,
                        data: props.formData,
                        successMessage: null,
                        success: (res) => {
                            datos.venta.idNota = res.xid;
                            datos.venta.accion = props.addEditType;
                            datos.venta.aplicaBeneficiarios = esBeneficiario.value;
                            datos.venta.beneficiarios = esBeneficiario
                                ? JSON.stringify(datos.venta.beneficiarios)
                                : {};

                            datos.venta.aplicaBeneficiariosAsist = esBeneficiarioAsist.value;
                            datos.venta.beneficiariosAsist = esBeneficiarioAsist
                                ? JSON.stringify(datos.venta.beneficiariosAsist)
                                : {};


                            datos.venta.productos = JSON.stringify(table.data);

                            table.data = [];

                            addEditRequestAdmin({
                                url: 'ventas/save',
                                data: datos.venta,
                                successMessage: props.successMessage,
                                success: () => { },
                            });

                            emit("onAddEditSuccess");
                            emit("addEditSuccess", res.xid);
                            clearDataVenta();
                        },
                    });
                } else {
                    addEditRequestAdmin({
                        url: props.url,
                        data: props.formData,
                        successMessage: props.successMessage,
                        success: (res) => {
                            emit("addEditSuccess", res.xid);
                            clearDataVenta();
                        },
                    });
                }
            } catch (validationErrors) {
                console.log("Errores de validación:", validationErrors);
            }
        };

        const onClose = () => {
            clearDataVenta();
            rules.value = {};
            emit("closed");
        };

        const validarSumaMaxima = (index, valorNuevo) => {
            // 1) suma de todos menos el que estoy editando
            const totalExcluido = datos.venta.beneficiarios
                .reduce((acc, item, idx) =>
                    idx !== index
                        ? acc + Number(item.porcentaje || 0)
                        : acc
                    , 0);

            // 2) cuánto queda hasta 100
            const maxPermitido = 100 - totalExcluido;

            // 3) si el valor ingresado supera el máximo, lo recorto
            if (valorNuevo > maxPermitido) {
                datos.venta.beneficiarios[index].porcentaje = maxPermitido;
                suma.value = 100;
            } else {
                suma.value = totalExcluido + valorNuevo;
            }
        };

        const validarSumaMaximaAsist = (index, valorNuevo) => {
            // 1) suma de todos menos el que estoy editando
            const totalExcluido = datos.venta.beneficiariosAsist
                .reduce((acc, item, idx) =>
                    idx !== index
                        ? acc + Number(item.porcentaje || 0)
                        : acc
                    , 0);

            // 2) cuánto queda hasta 100
            const maxPermitido = 100 - totalExcluido;

            // 3) si el valor ingresado supera el máximo, lo recorto
            if (valorNuevo > maxPermitido) {
                datos.venta.beneficiariosAsist[index].porcentaje = maxPermitido;
                sumaAsist.value = 100;
            } else {
                sumaAsist.value = totalExcluido + valorNuevo;
            }
        };

        const windowWidth = ref(window.innerWidth);

        const onResize = () => {
            windowWidth.value = window.innerWidth
        }

        onUnmounted(() => window.removeEventListener('resize', onResize))

        const drawerWidth = computed(() => {

            if (windowWidth.value >= 768 && windowWidth.value <= 1194) return '70%'
            if (windowWidth.value >= 600 && windowWidth.value <= 767) return '60%'

            // if (windowWidth.value <= 991) return '80%'
            return isSale.value ? '76%' : '50%'
        })

        return {
            campaigns_id,
            antdLocale,
            getAccion,
            handleTableChange,
            drawerWidth,
            removeProducto,
            datosProducto,
            addProducto,
            table,
            columns,
            validarSumaMaxima,
            validarSumaMaximaAsist,
            suma,
            sumaAsist,
            soloVer,
            datos,
            isSale,
            schedule,
            esBeneficiario,
            esBeneficiarioAsist,
            loading,
            rules,
            internalCodeOptions,
            productNameOptions,
            coverageOptions,
            matchingProducts,
            formatAmountCurrency,
            formatAmountUsingCurrencyObject,
            notesTypifications,
            parentTypificationData,
            childrenTypificationData,
            childrenChildData,
            lastChildrenChildData,
            getChildTypification,
            getChildrenChildTypification,
            getLastChildrenChildTypification,
            formRef,
            validationRules,
            addUser,
            addUserAsis,
            removeUser,
            removeUserAsis,
            onSubmit,
            onClose,
            permsArray,
            filteredParentTypifications,
        };
    },
});
</script>

<style>
.label-bold .ant-form-item-label>label {
    font-weight: bold;
}

.side-panel {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

@media (max-width: 895px) {
    .side-panel>.ant-col {
        /* selecciona cada col de Ant */
        flex-wrap: wrap;
        /* permite envoltura */
        flex: 0 0 100% !important;
        /* ancho 100% */
        max-width: 100% !important;
        flex-wrap: wrap;
    }
}

.floating-input input {
    padding: 20px 12px 6px 12px;
    border-top: none;
}

/* Label dentro del input */
.floating-input label {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    transition: all 0.2s ease;
    padding: 0 4px;
    font-weight: bold;
}

/* Cuando tiene foco o valor */
.floating-input:focus-within label,
.floating-input input:not(:placeholder-shown)+label {
    top: -6px;
}

.floating-input:focus-within .floating-label,
.floating-select.has-value .floating-label {
    top: -6px;
}

.phone-select .ant-select-selector {
  border-top: none !important;
}
</style>