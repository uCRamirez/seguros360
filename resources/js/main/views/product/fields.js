import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "products?fields=id,xid,name,price,product_type,tax_rate,tax_label,image,image_url,internal_code,category_id,x_category_id,categories{id,xid,name}";
    const addEditUrl = "products";
    const { t } = useI18n();
    const hashableColumns = ["category_id"];

    const initData = {
        name: "",
        price: "",
        product_type: "product",
        tax_rate: 0,
        tax_label: "",
        image: undefined,
        image_url: undefined,
        internal_code: "",
        category_id: undefined,
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
            title: t("product.product_type"),
            dataIndex: "product_type",
        },
        {
            title: t("product.name"),
            dataIndex: "name",
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
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "name",
            value: t("common.name"),
        },
    ];

    return {
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns,
    };
};

export default fields;
