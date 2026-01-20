import { useI18n } from "vue-i18n";

const fields = () => {
    const { t } = useI18n();

    // COLUMNAS DE LA TABLA DE BUSQUEDA
    const columns = [
        {
            title: t("lead.document"),
            dataIndex: "cedula",
            key: "cedula",
        },
        {
            title: t("lead.name"),
            dataIndex: "nombre",
            key: "nombre",
        },
        {
            title: t("lead.phone"),
            dataIndex: "tel1",
            key: "tel1",
        },
        {
            title: t("lead.email"),
            dataIndex: "email",
            key: "email",
        },
        {
            title: t("lead.proyect"),
            dataIndex: "campaign",
            key: "campaign",
        },
        {
            title: t("lead.agent"),
            dataIndex: "assign_to",
            key: "assign_to",
        },
    ];


    return {
        columns,
    };
};

export default fields;
