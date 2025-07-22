import { useI18n } from "vue-i18n";
import common from "../../../common/composable/common";
import { ref, onMounted } from "vue";


const fields = () => {
    const { getCampaignUrl } = common();

	const addEditUrl = "notes-typifications";

	const { t } = useI18n();
	const hashableColumns = ['parent_id'];
	const allCampaigns = ref([]);
    const activeCampaignType = ref("active");
    const viewType = ref("self");

	const initData = {
		campaign_id: null,
		name: "",
		parent_id: null,
		sale: false,
		schedule: false,
		no_contact: false,
	};

	const columns = [
		{
			title: t("notes_typification.name"),
			dataIndex: "name",
		},
		{
			title: t("campaign.campaign"),
			dataIndex: "campaigns_id",
		},
		{
			title: t("common.action"),
			dataIndex: "action",
		},
	];

	onMounted(() => {
			const campaignsUrl = getCampaignUrl(
				activeCampaignType.value,
				viewType.value
			);
			const campaignsPromise = axiosAdmin.get(campaignsUrl);
			Promise.all([
				campaignsPromise,
			]).then(
				([
					campaignsResponse,
				]) => {
					allCampaigns.value = campaignsResponse.data;
				}
			);
		});


	return {
		allCampaigns,
		hashableColumns,
		addEditUrl,
		initData,
		columns,
	}
}

export default fields;
