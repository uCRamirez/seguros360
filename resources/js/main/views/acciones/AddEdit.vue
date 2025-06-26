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
						:label="$t('common.name')"
						name="nombre"
						:help="rules.nombre ? $t('category.name') : null"
						:validateStatus="rules.nombre ? 'error' : null"
						class="required"
					>
						<a-input
							v-model:value="formData.nombre"
							:placeholder="
								$t('common.placeholder_default_text', [
									$t('category.name'),
								])
							"
						/>
					</a-form-item>
					<a-form-item
						:label="$t('common.description')"
						name="descripcion"
						:help="rules.descripcion ? rules.descripcion.message : null"
						:validateStatus="rules.descripcion ? 'error' : null"
					>
						<a-input
							v-model:value="formData.descripcion"
							:placeholder="
								$t('common.placeholder_default_text', [
									$t('common.description'),
								])
							"
						/>
					</a-form-item>
					<a-form-item
						:label="$t('lead_status.type')"
						name="tipo"
						:help="rules.tipo ? $t('lead_status.type') : null"
						:validateStatus="rules.tipo ? 'error' : null"
						class="required"
					>
						<a-select
                            v-model:value="formData.tipo"
                            :placeholder="
                                $t('common.select_default_text', [$t('lead_status.type'),])"
							:allowClear="true"
							show-search
                        >
                            <a-select-option
                                key="accion"
                                value="accion"
                            >
                                {{ $t("common.action") }}
                            </a-select-option>
                            <a-select-option
                                key="cierre"
                                value="cierre"
                            >
                                {{ $t("common.closing_sale") }}
                            </a-select-option>
							<a-select-option
                                key="mejora"
                                value="mejora"
                            >
                                {{ $t("common.improvement_options") }}
                            </a-select-option>
                        </a-select>
					</a-form-item>
					<a-form-item
						v-if="formData.tipo === 'accion'"
						:label="$t('lead.send_email')"
						name="users_ids"
						:help="rules.users_ids ? $t('menu.users') : null"
						:validateStatus="rules.users_ids ? 'error' : null"
						class="required"
					>
						<a-select 
							style="width: 100%"
							:placeholder="$t('common.select_default_text', [$t('menu.users')])"
							:allowClear="true"
							show-search
							optionFilterProp="title"
							mode="multiple"
							v-model:value="formData.users_ids"
						>
							<a-select-option v-for="agente in allUsers" 
								:title="agente.name" 
								:key="agente.id"
								:value="agente.id">
								{{ agente.name }}
							</a-select-option>
						</a-select>
					</a-form-item>
				</a-col>
			</a-row>
		</a-form>
		<template #footer>
			<a-button key="submit" type="primary" :loading="loading" @click="onSubmit">
				<template #icon>
					<SaveOutlined />
				</template>
				{{ addEditType == "add" ? $t("common.create") : $t("common.update") }}
			</a-button>
			<a-button key="back" @click="onClose">
				{{ $t("common.cancel") }}
			</a-button>
		</template>
	</a-modal>
</template>
<script>
import { defineComponent, watch } from "vue";
import { PlusOutlined, LoadingOutlined, SaveOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../common/composable/apiAdmin";

export default defineComponent({
	props: [
		"formData",
		"data",
		"visible",
		"url",
		"addEditType",
		"pageTitle",
		"successMessage",
		"allUsers",
	],
	components: {
		PlusOutlined,
		LoadingOutlined,
		SaveOutlined,
	},
	setup(props, { emit }) {
		const { addEditRequestAdmin, loading, rules } = apiAdmin();

		const onSubmit = () => {
			addEditRequestAdmin({
				url: props.url,
				data: props.formData,
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

		watch(() => props.formData.users_ids,
			val => {
				if (typeof val === 'string') {
					try {
						props.formData.users_ids = JSON.parse(val);
					} catch {
						props.formData.users_ids = [];
					}
				}
			},
			{ immediate: true }
		);

		return {
			loading,
			rules,
			onClose,
			onSubmit,

			drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
		};
	},
});
</script>
