import { useI18n } from "vue-i18n";
import common from "../../../common/composable/common";
import { ref } from "vue";


const fields = () => {
    const url =
        "products?fields=id,xid,name,coverage,digitar_precio,description,nombreBase,digitar_precio,price,status,campaign_id,x_campaign_id,product_type,tax_rate,tax_label,image,image_url,internal_code,currency_id,x_currency_id,currency{id,xid,name,code,symbol,position},category_id,x_category_id,categories{id,xid,name},campaigns{id,xid,name}&filters=status eq 1";
    const addEditUrl = "products";
    const { t } = useI18n();
    const hashableColumns = ["category_id", "campaign_id", "currency_id"];
    const allCampaigns = ref([]);
    const currencies = ref([]);
    const { getCampaignUrl } = common();

    const getPrefetchData = () => {
        const campaignsUrl = getCampaignUrl();
        const campaignsPromise = axiosAdmin.get(campaignsUrl);
        const currenciesPromise = axiosAdmin.get("currencies?fields=id,xid,name,symbol,position,code");

        return Promise.all([
            campaignsPromise,
            currenciesPromise,
        ]).then(
            ([
                campaignsResponse,
                currenciesResponse,
            ]) => {
                allCampaigns.value = campaignsResponse.data;
                currencies.value = currenciesResponse.data;
            }
        );
    };

    const initData = {
        name: "",
        coverage: "",
        description: "",
        nombreBase: "",
        price: "",
        product_type: "product",
        tax_rate: 0,
        tax_label: "",
        image: undefined,
        image_url: undefined,
        internal_code: "",
        category_id: undefined,
        campaign_id: undefined,
        currency_id: undefined,
        status: 1,
        digitar_precio: 0,
    };

    const columns = [
        {
            title: t("product.id"),
            dataIndex: "id",
        },
        {
            title: t("product.image"),
            dataIndex: "image",
        },
        {
            title: t("product.category"),
            dataIndex: "category_id",
        },
        {
            title: t("product.campaign"),
            dataIndex: "campaign_id",
        },
        {
            title: t("product.product_type"),
            dataIndex: "product_type",
        },
        {
            title: t("bases.base"),
            dataIndex: "nombreBase",
        },
        {
            title: t("product.internal_code"),
            dataIndex: "internal_code",
        },
        {
            title: t("product.name"),
            dataIndex: "name",
        },
        {
            title: t("product.coverage"),
            dataIndex: "coverage",
        },
        {
            title: t("product.price"),
            dataIndex: "price",
        },
        {
            title: t("product.tax_label"),
            dataIndex: "tax_label",
        },
        {
            title: t("product.tax_rate"),
            dataIndex: "tax_rate",
        },
        {
            title: t("common.status"),
            dataIndex: "status",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "name",
            value: t("common.name"),
        },
        {
            key: "coverage",
            value: t("product.coverage"),
        },
        {
            key: "internal_code",
            value: t("product.internal_code"),
        },
        {
            key: "nombreBase",
            value: t("bases.base"),
        },
    ];

    return {
        getPrefetchData,
        allCampaigns,
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns,
        currencies,
    };
};

export default fields;
