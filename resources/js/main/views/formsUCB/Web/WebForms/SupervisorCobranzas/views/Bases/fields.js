import { useI18n } from "vue-i18n";

const fields = () => {
    const { t } = useI18n();

    // Bases clientes
    let addEditUrlCustomers = 'cobranzas-bases-clientes';
    let urlCustomers = `${addEditUrlCustomers}?fields=id,xid,nombre_base,registros,user_id,x_user_id,user{id,xid,name,user,profile_image,profile_image_url},fecha_subida,activo&limit=100`;
    let filterableColumnsCustomers = [];
    const hashableColumns = ["user_id"];

    // Bases carteras
    let addEditUrlCarteras = 'cobranzas-bases-carteras';
    let urlCarteras = `${addEditUrlCarteras}?fields=id,xid,nombre_base,registros,user_id,x_user_id,user{id,xid,name,user,profile_image,profile_image_url},fecha_subida,activo&limit=100`;
    let filterableColumnsCarteras = [];

    // Bases pagos
    let addEditUrlPagos = 'cobranzas-bases-pagos';
    let urlPagos = `${addEditUrlPagos}?fields=id,xid,nombre_base,registros,user_id,x_user_id,user{id,xid,name,user,profile_image,profile_image_url},fecha_subida,activo&limit=100`;
    let filterableColumnsPagos = [];

    // COLUMNAS DE LA TABLA DE BASES DE CLIENTES
    const columns = [
        {
            title: t("cobranzas.date"),
            dataIndex: "fecha_subida",
            key: "fecha_subida",
        },
        {
            title: t("audit.user"),
            dataIndex: "user",
            key: "user",
        },
        {
            title: t("cobranzas.base_name"),
            dataIndex: "nombre_base",
            key: "nombre_base",
            align: "center",
        },
        {
            title: t("cobranzas.records"),
            dataIndex: "registros",
            key: "registros",
            align: "center",
        },
        // {
        //     title: t("cobranzas.status"),
        //     dataIndex: "activa",
        //     key: "activa",
        //     align: "center",
        // },
        {
            title: t("common.action"),
            key: "action",
            fixed: "right",
            align: "center",
        },
    ];

    const initData = {
        nombre_base: "",
        registros: 0,
        user_id: null,
        fecha_subida: null,
        activo: 1,
    };


    return {
        columns,
        initData,
        addEditUrlCustomers,
        urlCustomers,
        filterableColumnsCustomers,
        hashableColumns,
        addEditUrlCarteras,
        urlCarteras,
        filterableColumnsCarteras,
        addEditUrlPagos,
        urlPagos,
        filterableColumnsPagos,
    };
};

export default fields;
