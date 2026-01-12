<template>
	<a-modal :open="visible" :closable="false" :centered="true" :title="$t('campaign.export_report')" @ok="onSubmit">
		<a-form layout="vertical">
			<a-row :gutter="16">
				<a-col :xs="24" :sm="24" :md="24" :lg="24">

					<a-form-item :label="$t('menu.campaigns')" name="campaigns_filter"
						:help="rules.campaigns_filter ? $t('menu.campaigns') : null"
						:validateStatus="rules.campaigns_filter ? 'error' : null" class="required">

						<a-select v-model:value="campaigns_filter"
							:placeholder="$t('common.select_default_text', [$t('lead.campaign'),])" :allowClear="true"
							mode="multiple" style="width: 100%" optionFilterProp="title" show-search
							@change="campaignChanged">

							<a-select-option v-for="campaign in campaigns_list" :key="campaign.xid"
								:title="campaign.name" :value="campaign.id" :campaign="campaign">
								{{ campaign.name }}
							</a-select-option>

						</a-select>
					</a-form-item>

				</a-col>
				<a-col :xs="24" :sm="24" :md="24" :lg="24">

					<a-form-item :label="$t('campaign.time_range')" name="time_range"
						:help="rules.time_range ? $t('campaign.time_range') : null"
						:validateStatus="rules.time_range ? 'error' : null" class="required">
						<a-range-picker style="width: 100%;" v-model:value="time_range" format="YYYY-MM-DD HH:mm:ss"
							value-format="YYYY-MM-DD HH:mm:ss" show-time />
					</a-form-item>

				</a-col>
			</a-row>
		</a-form>
		<template #footer>
			<a-button key="submit" type="primary" :loading="loading" @click="onSubmit">
				<template #icon>
					<CloudDownloadOutlined />
				</template>
				{{ $t("common.download") }}
			</a-button>
			<a-button key="back" @click="onClose">
				{{ $t("common.cancel") }}
			</a-button>
		</template>
	</a-modal>
</template>
<script>
import { defineComponent, ref, watch } from "vue";
import { PlusOutlined, LoadingOutlined, CloudDownloadOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../common/composable/apiAdmin";
import { notification } from "ant-design-vue";
import { useI18n } from "vue-i18n";

export default defineComponent({
	props: [
		"formData",
		"data",
		"visible",
		"url",
		"addEditType",
		"pageTitle",
		"successMessage",
		"campaigns_list",
	],
	components: {
		PlusOutlined,
		LoadingOutlined,
		CloudDownloadOutlined,
	},
	setup(props, { emit }) {
		const { loading, rules } = apiAdmin();
		const campaigns_filter = ref([]);
		const time_range = ref([]);
		const { t } = useI18n();

		const onSubmit = () => {
			loading.value = true;

			rules.value = {};

			if (campaigns_filter.value.length === 0) {
				rules.value.campaigns_filter = true;
				loading.value = false;
				return;
			}

			if (!time_range.value[0] || !time_range.value[1]) {
				rules.value.time_range = true;
				loading.value = false;
				return;
			}

			axiosAdmin.post(
				`campaigns/export-report/${campaigns_filter.value}/${time_range?.value[0]}/${time_range?.value[1]}`,
				{},
				{
					responseType: "blob",
				})
				.then(response => {
					// Response is a blob type object

					// Create a temporary URL for the blob
					const url = window.URL.createObjectURL(response);

					// Create a temporary link element
					const link = document.createElement("a");
					link.href = url;

					// Set the link attributes to trigger download
					link.setAttribute("download", `DetalleGestiones.xlsx`);
					document.body.appendChild(link);

					// Simulate click on the link to start the download
					link.click();

					// Clean up by removing the temporary link and URL object
					document.body.removeChild(link);
					window.URL.revokeObjectURL(url);

					notification.success({
						message: t("common.success"),
						description: t(`module.downloading_completed`),
						placement: "bottomRight",
					});
					loading.value = false;
					emit("closed");
				})
				.catch((e) => {
					// Optionally handle error here
					loading.value = false;
					console.error("Error downloading file: " + e);
				})
				.finally(() => {
					loading.value = false;
					emit("closed");
				});
		};

		const onClose = () => {
			rules.value = {};
			emit("closed");
		};


		watch(() => props.visible, newVal => {
			if (newVal) {
				campaigns_filter.value = [];
				time_range.value = [];
			}
		});

		return {
			time_range,
			campaigns_filter,
			loading,
			rules,
			onClose,
			onSubmit,

			drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
		};
	},
});
</script>
