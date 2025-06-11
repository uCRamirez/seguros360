import { useI18n } from "vue-i18n";

const fields = () => {
	const addEditUrl = "plantillas-calidad";
	const { t } = useI18n();

	const initData = {
		nombre: "",
		descripcion: "",
		variable: [],
		activo: true,
	};


	const columns = [
		{
			title: t("common.name"),
			dataIndex: "nombre",
		},
		{
			title: t("common.description"),
			dataIndex: "descripcion",
		},
		{
			title: t("common.status"),
			dataIndex: "activo",
		},
		{
			title: t("campaign.created_at"),
			dataIndex: "created_at",
		},
		{
			title: t("common.action"),
			dataIndex: "action",
		},
	];

	const filterableColumns = [
        {
            key: "nombre",
            value: t("common.name"),
        },
    ];


	return {
		addEditUrl,
		initData,
		filterableColumns,
		columns,
	}
}

export default fields;
