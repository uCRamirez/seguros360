import { useI18n } from "vue-i18n";

const fields = () => {
	const addEditUrl = "notes-typifications";

	const { t } = useI18n();
	const hashableColumns = ['parent_id'];

	const initData = {
		name: "",
		parent_id: null,
		sale: false,
		schedule: false,
	};

	const columns = [
        // {
		// 	title: t("notes_typification.parent_id"),
		// 	dataIndex: "parent_id",
		// },

		{
			title: t("notes_typification.name"),
			dataIndex: "name",
		},

		{
			title: t("common.action"),
			dataIndex: "action",
		},
	];


	return {
		hashableColumns,
		addEditUrl,
		initData,
		columns,
	}
}

export default fields;
