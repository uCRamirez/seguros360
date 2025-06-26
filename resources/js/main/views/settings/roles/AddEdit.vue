<template>
    <a-drawer :title="pageTitle" :width="drawerWidth" :open="visible" :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }" :maskClosable="false" @close="onClose">
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item :label="$t('role.display_name')" name="display_name"
                        :help="rules.display_name ? rules.display_name.message : null"
                        :validateStatus="rules.display_name ? 'error' : null" class="required">
                        <a-input v-model:value="formData.display_name" :placeholder="$t('common.placeholder_default_text', [
                            $t('role.display_name'),
                        ])
                            " v-on:keyup="formData.name = slugify($event.target.value)" />
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item :label="$t('role.role_name')" name="name"
                        :help="rules.name ? rules.name.message : null" :validateStatus="rules.name ? 'error' : null"
                        class="required">
                        <a-input v-model:value="formData.name" :placeholder="$t('common.placeholder_default_text', [
                            $t('role.role_name'),
                        ])
                            " />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item :label="$t('role.description')" name="description"
                        :help="rules.description ? rules.description.message : null"
                        :validateStatus="rules.description ? 'error' : null">
                        <a-textarea v-model:value="formData.description" :placeholder="$t('common.placeholder_default_text', [
                            $t('role.description'),
                        ])
                            " :rows="4" />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item :label="$t('role.permissions')" name="permissions"
                        :help="rules.permissions ? rules.permissions.message : null"
                        :validateStatus="rules.permissions ? 'error' : null">
                        <div class="d-flex flex-column scroll-y">
                            <div class="tbl-responsive">
                                <a-checkbox-group v-model:value="checkedPermissions">
                                    <table class="table align-middle table-row-dashed row-gap">
                                        <tbody class="text-gray-600 fw-bold">
                                            <!-- forms -->
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.forms") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'forms_view'
                                                                ]
                                                                ">
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <!-- categorias -->
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.categories") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'categories_view'
                                                                ]
                                                                ">
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'categories_create'
                                                                ]
                                                                ">
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'categories_edit'
                                                                ]
                                                                ">
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'categories_delete'
                                                                ]
                                                                ">
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <!-- products -->
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.products") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'products_view'
                                                                ]
                                                                ">
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'products_create'
                                                                ]
                                                                ">
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'products_edit'
                                                                ]
                                                                ">
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom">
                                                            <a-checkbox :value="permissions[
                                                                'products_delete'
                                                                ]
                                                                ">
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <!-- staff members -->
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.staff_members") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'users_view'
                                                                ]
                                                                ">
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'users_create'
                                                                ]
                                                                ">
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'users_edit'
                                                                ]
                                                                ">
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'users_delete'
                                                                ]
                                                                ">
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <!-- campaigns view -->
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("campaign.campaign_view") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'campaigns_view_all'
                                                                ]
                                                                ">
                                                                {{
                                                                    $t("common.view_all")
                                                                }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'view_completed_campaigns'
                                                                ]
                                                                ">
                                                                {{
                                                                    $t(
                                                                        "campaign.view_completed_campaigns"
                                                                    )
                                                                }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <!-- campaigns -->
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.campaigns") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'campaigns_view'
                                                                ]
                                                                ">
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'campaigns_create'
                                                                ]
                                                                ">
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'campaigns_edit'
                                                                ]
                                                                ">
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'bases_view'
                                                                ]
                                                                ">
                                                                {{ $t("common.add") + ' ' + $t("bases.bases") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'campaigns_delete'
                                                                ]
                                                                ">
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'campaigns_recycle'
                                                                ]
                                                                ">
                                                                {{
                                                                    $t(
                                                                        "common.campaigns_recycle"
                                                                    )
                                                                }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'export_leads'
                                                                ]
                                                                ">
                                                                {{
                                                                    $t(
                                                                        "common.export_leads"
                                                                    )
                                                                }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <!-- leads -->
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.leads") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'leads_view_all'
                                                                ]
                                                                ">
                                                                {{
                                                                    $t("common.view_all")
                                                                }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'leads_create'
                                                                ]
                                                                ">
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'leads_delete'
                                                                ]
                                                                ">
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <!-- gestiones -->
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("common.notes") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'notes_edit'
                                                                ]
                                                                ">
                                                                {{ $t("notes.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'notes_delete'
                                                                ]
                                                                ">
                                                                {{ $t("notes.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <!-- ventas -->
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.sales") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox
                                                                :value="permissions[ 'sales_view' ]">
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions['sales_add']">
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox
                                                                :value="permissions['sales_edit']">
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox
                                                                :value="permissions['sales_delete']">
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <!-- calidad -->
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.quality") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox
                                                                :value="permissions[ 'quality_view' ]">
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions['quality_add']">
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox
                                                                :value="permissions['quality_edit']">
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox
                                                                :value="permissions['quality_delete']">
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                             <!-- templates de calidad -->
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.templates") }} 
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox
                                                                :value="permissions[ 'plantillas_calidad_view']">
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions['plantillas_calidad_create']">
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox
                                                                :value="permissions['plantillas_calidad_edit']">
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox
                                                                :value="permissions['plantillas_calidad_delete']">
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <!-- acciones -->
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ `${$t("menu.actions")}, ${$t("common.closures")} & ${$t("common.improvement_options")}` }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox
                                                                :value="permissions[ 'acciones_calidad_view' ]">
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions['acciones_calidad_create']">
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox
                                                                :value="permissions['acciones_calidad_edit']">
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox
                                                                :value="permissions['acciones_calidad_delete']">
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <!-- motivos -->
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.reasons_cancellation") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox
                                                                :value="permissions[ 'motivos_calidad_view' ]">
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions['motivos_calidad_create']">
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox
                                                                :value="permissions['motivos_calidad_edit']">
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox
                                                                :value="permissions['motivos_calidad_delete']">
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <!-- notes typifications -->
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.notes_typifications") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox
                                                                :value="permissions[ 'notes_typifications_view' ]">
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions['notes_typifications_add']">
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox
                                                                :value="permissions['notes_typifications_edit']">
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox
                                                                :value="permissions['notes_typifications_delete']">
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <!-- company edit -->
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.company") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'companies_edit'
                                                                ]
                                                                ">
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- password settings -->
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.password_settings") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'password_settings_view'
                                                                ]
                                                                ">
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'password_settings_edit'
                                                                ]
                                                                ">
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- lead status -->
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.lead_status") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions['lead_status_view']">
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions['lead_status_add']">
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions['lead_status_edit']">
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions['lead_status_delete']">
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- audits -->
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.audits") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'audits_view'
                                                                ]
                                                                ">
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- translations -->
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.translations") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'translations_view'
                                                                ]
                                                                ">
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'translations_create'
                                                                ]
                                                                ">
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'translations_edit'
                                                                ]
                                                                ">
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom">
                                                            <a-checkbox :value="permissions[
                                                                'translations_delete'
                                                                ]
                                                                ">
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- roles y permissions -->
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.roles") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'roles_view'
                                                                ]
                                                                ">
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'roles_create'
                                                                ]
                                                                ">
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'roles_edit'
                                                                ]
                                                                ">
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom">
                                                            <a-checkbox :value="permissions[
                                                                'roles_delete'
                                                                ]
                                                                ">
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- currencies -->
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.currencies") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'currencies_view'
                                                                ]
                                                                ">
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'currencies_create'
                                                                ]
                                                                ">
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'currencies_edit'
                                                                ]
                                                                ">
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label class="form-check form-check-custom">
                                                            <a-checkbox :value="permissions[
                                                                'currencies_delete'
                                                                ]
                                                                ">
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- storage settings -->
                                            <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.storage_settings") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label class="form-check form-check-custom me-5 me-lg-20">
                                                            <a-checkbox :value="permissions[
                                                                'storage_edit'
                                                                ]
                                                                ">
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>


                                            <!-- salesmans -->
                                            <!-- <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.salesmans") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'salesmans_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'salesmans_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'salesmans_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'salesmans_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr> -->

                                            <!-- email templates -->
                                            <!-- <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.email_templates") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'email_templates_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'email_templates_view_all'
                                                                    ]
                                                                "
                                                            >
                                                                {{
                                                                    $t("common.view_all")
                                                                }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'email_templates_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'email_templates_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'email_templates_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr> -->

                                            <!-- expenses categories -->
                                            <!-- <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.expense_categories") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'expense_categories_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'expense_categories_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'expense_categories_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'expense_categories_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr> -->

                                            <!-- expenses -->
                                            <!-- <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.expenses") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'expenses_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'expenses_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'expenses_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'expenses_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr> -->

                                            <!-- uphone calls -->
                                            <!-- <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.uphone_calls") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'uphone_calls_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'uphone_calls_all'
                                                                    ]
                                                                "
                                                            >
                                                                {{
                                                                    $t("common.view_all")
                                                                }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr> -->

                                            <!-- form fields -->
                                            <!-- <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.form_field_names") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'form_field_names_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'form_field_names_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'form_field_names_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'form_field_names_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr> -->

                                            <!-- email settings -->
                                            <!-- <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.email_settings") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'email_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr> -->

                                            <!-- email templates -->
                                            <!-- <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.message_templates") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'message_templates_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'message_templates_view_all'
                                                                    ]
                                                                "
                                                            >
                                                                {{
                                                                    $t("common.view_all")
                                                                }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'message_templates_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'message_templates_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'message_templates_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr> -->

                                            <!-- message providers -->
                                            <!-- <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.message_providers") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'message_providers_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'message_providers_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'message_providers_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'message_providers_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr> -->

                                            <!-- email providers -->
                                            <!-- <tr>
                                                <td class="text-gray-800">
                                                    {{ $t("menu.email_providers") }}
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'email_providers_view'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.view") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'email_providers_create'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.add") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom me-5 me-lg-20"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'email_providers_edit'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.edit") }}
                                                            </a-checkbox>
                                                        </label>
                                                        <label
                                                            class="form-check form-check-custom"
                                                        >
                                                            <a-checkbox
                                                                :value="
                                                                    permissions[
                                                                        'email_providers_delete'
                                                                    ]
                                                                "
                                                            >
                                                                {{ $t("common.delete") }}
                                                            </a-checkbox>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr> -->
                                        </tbody>
                                    </table>
                                </a-checkbox-group>
                            </div>
                        </div>
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>
        <template #footer>
            <a-button type="primary" @click="onSubmit" style="margin-right: 8px" :loading="loading">
                <template #icon>
                    <SaveOutlined />
                </template>
                {{ addEditType == "add" ? $t("common.create") : $t("common.update") }}
            </a-button>
            <a-button @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-drawer>
</template>
<script>
import { defineComponent, ref, onMounted, computed, watch } from "vue";
import { PlusOutlined, LoadingOutlined, SaveOutlined } from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import apiAdmin from "../../../../common/composable/apiAdmin";
import common from "../../../../common/composable/common";

export default defineComponent({
    props: [
        "formData",
        "data",
        "visible",
        "url",
        "addEditType",
        "pageTitle",
        "successMessage",
    ],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const roles = ref([]);
        const { t } = useI18n();
        const permissions = ref([]);
        const checkedPermissions = ref([]);
        const { slugify } = common();

        onMounted(() => {
            axiosAdmin
                .get("permissions?fields=id,xid,name,display_name&limit=10000")
                .then((response) => {
                    const permissionArray = [];
                    response.data.map((res) => {
                        permissionArray[res.name] = res.xid;
                    });
                    permissions.value = permissionArray;
                });
        });

        const onSubmit = () => {
            const newFormData = {
                ...props.formData,
                permissions: checkedPermissions.value,
            };

            addEditRequestAdmin({
                url: props.url,
                data: newFormData,
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                },
            });
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                if (newVal && props.addEditType == "edit") {
                    props.data.perms.forEach((value) => {
                        checkedPermissions.value.push(value.xid);
                    });
                } else {
                    checkedPermissions.value = [];
                }
            }
        );

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            roles,
            permissions,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "60%",
            checkedPermissions,
            slugify,
        };
    },
});
</script>

<style lang="less">
.flex-column {
    flex-direction: column !important;
}

.d-flex {
    display: flex !important;
}

.tbl-responsive {
    overflow-x: auto;
}

.table {
    width: 100%;
}

.align-middle {
    vertical-align: middle !important;
}

.table>tbody {
    vertical-align: inherit;
}

.text-gray-600 {
    color: #7e8299 !important;
}

.fw-bold {
    font-weight: 500 !important;
}

tbody,
td,
tfoot,
th,
thead,
tr {
    border-color: inherit;
    border-style: solid;
    border-width: 0;
}

.table.table-row-dashed tr {
    border-bottom-width: 1px;
    border-bottom-style: dashed;
    border-bottom-color: #eff2f5;
}

.table td:first-child,
.table th:first-child,
.table tr:first-child {
    padding-left: 0;
}

.form-check.form-check-custom {
    display: flex;
    align-items: center;
    padding-left: 0;
    margin: 0;
}

.me-9 {
    margin-right: 2.25rem !important;
}

.table.row-gap td,
.table.row-gap th {
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
}

.me-lg-20 {
    margin-right: 5rem !important;
}

.ant-checkbox-group {
    width: 100%;
}
</style>
