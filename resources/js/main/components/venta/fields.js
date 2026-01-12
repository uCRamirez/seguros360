import { ref } from "vue";
import { useI18n } from "vue-i18n";
import { forEach } from "lodash-es";
import common from "../../../common/composable/common";

const fields = (props) => {
    const { convertStringToKey, getCampaignUrl, user } = common();
    const leadUrl =
        "lead{id,xid,reference_number,cedula,nombre,segundo_nombre,apellido1,apellido2,fechaNacimiento,edad,nacionalidad,nombreBase,tel1,email,lead_data,started,campaign_id,x_campaign_id,time_taken,first_action_by,x_first_action_by,last_action_by,x_last_action_by},lead:campaign{id,xid,name,status},lead:firstActioner{id,xid,name},lead:lastActioner{id,xid,name}";
    const formFieldNamesUrl = "form-field-names/all";
    const url = 
        `lead-logs?fields=id,xid,log_type,time_taken,campaign_id,x_campaign_id,next_contact,date_time,user_id,x_user_id,notes,phone,message,user{id,xid,name,profile_image,profile_image_url},lead_id,x_lead_id,notes_file,notes_file_url,${leadUrl},campaign_id,notes_typification_id_1,x_notes_typification_id_1,notes_typification_id_2,x_notes_typification_id_2,notes_typification_id_3,x_notes_typification_id_3,notes_typification_id_4,x_notes_typification_id_4,notes_typification_name_1,notes_typification_name_2,notes_typification_name_3,notes_typification_name_4,isSale{idVenta,idNota,idLead,user_id,telVenta,estadoVenta,tarjeta,aplicaBeneficiarios,cantidadBeneficiarios,beneficiarios,aplicaBeneficiariosAsist,cantidadBeneficiariosAsist,beneficiariosAsist,montoTotal,calidad},isSale:productos{x_id_producto,idProducto,cantidadProducto,precio,precio_digitado},isSale:user{id,xid,name}`;
    const allFormFieldNames = ref([]);
    const addEditUrl = "lead-logs";
    const hashableColumns = ["lead_id", "campaign_id", "user_id", "notes_typification_id_1", "notes_typification_id_2", "notes_typification_id_3","notes_typification_id_4"];
    const urlProductos =
    "products?fields=id,xid,name,coverage,price,campaign_id,x_campaign_id,product_type,tax_rate,tax_label,image,image_url,internal_code,digitar_precio,category_id,x_category_id,categories{id,xid,name},campaigns{id,xid,name}";

    const { t } = useI18n();
    const initData = {
        notes: "",
        notes_file: undefined,
        notes_file_url: undefined,
        log_type: "notes",
        notes_typification_id_1: undefined,
        notes_typification_id_2: undefined,
        notes_typification_id_3: undefined,
        notes_typification_id_4: undefined,
        is_sale: undefined,
        phone: "",
        message: ""
    };
    const columns = ref([]);
    const allCampaigns = ref([]);
    const allUsers = ref([]);
    const allProductos = ref([]);

    const filterableColumns = [
         {
            key: "isSale.idVenta",
            value: `${t("lead.id")} ${t("lead_notes.sale")}`,
        },
        {
            key: "leads.id",
            value: t("uphone_calls.client_id"),
        },
        {
            key: "leads.cedula",
            value: t("lead.document"),
        },
        {
            key: "leads.nombre",
            value: t("lead.name"),
        },
        {
            key: "leads.segundo_nombre",
            value: t("lead.middle_name"),
        },
        {
            key: "leads.apellido1",
            value: t("lead.first_last_name"),
        },
        {
            key: "leads.apellido2",
            value: t("lead.second_last_name"),
        },
    ];

    const getPrefetchData = () => {
        const campaignsUrl = getCampaignUrl();
        const campaignsPromise = axiosAdmin.get(campaignsUrl);
        const formFieldNamesPromise = axiosAdmin.get(formFieldNamesUrl);
        const staffMembersPromise = axiosAdmin.get(
            `all-users?log_type=${props.logType}`
        );
        const productosPromise = axiosAdmin.get(urlProductos);

        return Promise.all([
            formFieldNamesPromise,
            campaignsPromise,
            staffMembersPromise,
            productosPromise,
        ]).then(
            ([
                formFieldNamesResponse,
                campaignsResponse,
                staffMembersResponse,
                productosResponse,
            ]) => {
                allFormFieldNames.value = formFieldNamesResponse.data.data;
                allCampaigns.value = campaignsResponse.data;
                allUsers.value = staffMembersResponse.data.users;
                allProductos.value = productosResponse.data;

                var newColumnsArray = [];

                if (props.showLeadDetails) {
                    var newColumnsArray = [
                        {
                            title: `${t("lead.id")} ${t("lead_notes.sale")}`,
                            dataIndex: "is_sale.idVenta",
                        },
                        {
                            title: t("uphone_calls.client_id"),
                            dataIndex: "id",
                        },
                        {
                            title: t("lead.document"),
                            dataIndex: "cedula",
                        },
                        {
                            title: t("lead.name"),
                            dataIndex: "nombre",
                        },
                        {
                            title: t("lead.campaign"),
                            dataIndex: "campaign",
                        },
                    ];

                    // Showing form fields if props.showFormFields
                    // sets to true
                    if (props.showFormFields) {
                        forEach(
                            formFieldNamesResponse.data.data,
                            (formFieldName) => {
                                newColumnsArray.push({
                                    title: formFieldName.field_name,
                                    dataIndex: convertStringToKey(
                                        formFieldName.field_name
                                    ),
                                });
                            }
                        );
                    }
                }

                newColumnsArray.push({
                    title: t("common.notes"),
                    dataIndex: "notes",
                    width:
                        props.showLeadDetails && props.showFormFields
                            ? "40%"
                            : "40%",
                });


                if (props.showActionButton) {
                    newColumnsArray.push({
                        title: t("common.action"),
                        dataIndex: "action",
                    });
                }

                columns.value = newColumnsArray;
            }
        );
    };

    return {
        t,
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns,
        allFormFieldNames,
        allCampaigns,
        allProductos,
        allUsers,
        getPrefetchData,
    };
};

export default fields;
