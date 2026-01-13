import { computed, ref } from "vue";
import moment from "moment";
import { useStore } from "vuex";
import { useI18n } from "vue-i18n";
import { forEach, includes, find } from "lodash-es";
import { checkUserPermission } from "../scripts/functions";
import dayjs from "dayjs";
import { useRouter } from "vue-router";

moment.suppressDeprecationWarnings = true;
import utc from "dayjs/plugin/utc";
import timezone from "dayjs/plugin/timezone";
dayjs.extend(utc);
dayjs.extend(timezone);

const common = () => {
    const store = useStore();
    const { t } = useI18n();
    const appType = window.config.app_type;
    const downloadLangCsvUrl = window.config.download_lang_csv_url;
    const menuCollapsed = computed(() => store.state.auth.menuCollapsed);
    const cssSettings = computed(() => store.state.auth.cssSettings);
    const appModules = computed(() => store.state.auth.activeModules);
    const visibleSubscriptionModules = computed(
        () => store.state.auth.visibleSubscriptionModules
    );
    const globalSetting = computed(() => store.state.auth.globalSetting);
    const appSetting = computed(() => store.state.auth.appSetting);
    const addMenus = computed(() => store.state.auth.addMenus);
    const selectedLang = computed(() => store.state.auth.lang);
    const user = computed(() => store.state.auth.user);
    const themeMode = computed(() => store.state.auth.themeMode);
    const rightSidebarValue = computed(() => store.state.auth.rightSidebar);
    const reloadUphoneStringData = computed(() => store.state.auth.uphoneCallReloadString);

    const countryCodes = ref([
        { code: "1", name: "United States/Canada", length: 10 },
        { code: "7", name: "Russia", length: 10 },
        { code: "20", name: "Egypt", length: 9 },
        { code: "27", name: "South Africa", length: 9 },
        { code: "30", name: "Greece", length: 10 },
        { code: "31", name: "Netherlands", length: 9 },
        { code: "32", name: "Belgium", length: 9 },
        { code: "33", name: "France", length: 9 },
        { code: "34", name: "Spain", length: 9 },
        { code: "36", name: "Hungary", length: 9 },
        { code: "39", name: "Italy", length: 9 },
        { code: "40", name: "Romania", length: 9 },
        { code: "41", name: "Switzerland", length: 9 },
        { code: "43", name: "Austria", length: 10 },
        { code: "44", name: "United Kingdom", length: 10 },
        { code: "45", name: "Denmark", length: 8 },
        { code: "46", name: "Sweden", length: 9 },
        { code: "47", name: "Norway", length: 8 },
        { code: "48", name: "Poland", length: 9 },
        { code: "49", name: "Germany", length: 10 },
        { code: "51", name: "Peru", length: 9 },
        { code: "52", name: "Mexico", length: 10 },
        { code: "53", name: "Cuba", length: 8 },
        { code: "54", name: "Argentina", length: 10 },
        { code: "55", name: "Brazil", length: 10 },
        { code: "56", name: "Chile", length: 9 },
        { code: "57", name: "Colombia", length: 10 },
        { code: "58", name: "Venezuela", length: 10 },
        { code: "60", name: "Malaysia", length: 9 },
        { code: "61", name: "Australia", length: 9 },
        { code: "62", name: "Indonesia", length: 10 },
        { code: "63", name: "Philippines", length: 10 },
        { code: "64", name: "New Zealand", length: 8 },
        { code: "65", name: "Singapore", length: 8 },
        { code: "66", name: "Thailand", length: 9 },
        { code: "81", name: "Japan", length: 10 },
        { code: "82", name: "South Korea", length: 9 },
        { code: "84", name: "Vietnam", length: 9 },
        { code: "86", name: "China", length: 11 },
        { code: "90", name: "Turkey", length: 10 },
        { code: "91", name: "India", length: 10 },
        { code: "92", name: "Pakistan", length: 10 },
        { code: "93", name: "Afghanistan", length: 9 },
        { code: "94", name: "Sri Lanka", length: 9 },
        { code: "95", name: "Myanmar", length: 9 },
        { code: "98", name: "Iran", length: 10 },
        { code: "212", name: "Morocco", length: 9 },
        { code: "213", name: "Algeria", length: 9 },
        { code: "216", name: "Tunisia", length: 8 },
        { code: "218", name: "Libya", length: 9 },
        { code: "220", name: "Gambia", length: 7 },
        { code: "221", name: "Senegal", length: 9 },
        { code: "222", name: "Mauritania", length: 9 },
        { code: "223", name: "Mali", length: 8 },
        { code: "224", name: "Guinea", length: 9 },
        { code: "225", name: "Ivory Coast", length: 8 },
        { code: "226", name: "Burkina Faso", length: 8 },
        { code: "227", name: "Niger", length: 8 },
        { code: "228", name: "Togo", length: 8 },
        { code: "229", name: "Benin", length: 8 },
        { code: "230", name: "Mauritius", length: 7 },
        { code: "231", name: "Liberia", length: 8 },
        { code: "232", name: "Sierra Leone", length: 8 },
        { code: "233", name: "Ghana", length: 9 },
        { code: "234", name: "Nigeria", length: 10 },
        { code: "235", name: "Chad", length: 8 },
        { code: "236", name: "Central African Republic", length: 8 },
        { code: "237", name: "Cameroon", length: 9 },
        { code: "238", name: "Cape Verde", length: 7 },
        { code: "239", name: "São Tomé and Príncipe", length: 7 },
        { code: "240", name: "Equatorial Guinea", length: 9 },
        { code: "241", name: "Gabon", length: 9 },
        { code: "242", name: "Republic of the Congo", length: 9 },
        { code: "243", name: "Democratic Republic of the Congo", length: 9 },
        { code: "244", name: "Angola", length: 9 },
        { code: "245", name: "Guinea-Bissau", length: 7 },
        { code: "246", name: "British Indian Ocean Territory", length: 7 },
        { code: "247", name: "Ascension Island", length: 4 },
        { code: "248", name: "Seychelles", length: 7 },
        { code: "249", name: "Sudan", length: 9 },
        { code: "250", name: "Rwanda", length: 9 },
        { code: "251", name: "Ethiopia", length: 9 },
        { code: "252", name: "Somalia", length: 8 },
        { code: "253", name: "Djibouti", length: 8 },
        { code: "254", name: "Kenya", length: 9 },
        { code: "255", name: "Tanzania", length: 9 },
        { code: "256", name: "Uganda", length: 9 },
        { code: "257", name: "Burundi", length: 8 },
        { code: "258", name: "Mozambique", length: 9 },
        { code: "260", name: "Zambia", length: 9 },
        { code: "261", name: "Madagascar", length: 9 },
        { code: "262", name: "Réunion", length: 9 },
        { code: "263", name: "Zimbabwe", length: 9 },
        { code: "264", name: "Namibia", length: 9 },
        { code: "265", name: "Malawi", length: 9 },
        { code: "266", name: "Lesotho", length: 8 },
        { code: "267", name: "Botswana", length: 9 },
        { code: "268", name: "Eswatini", length: 8 },
        { code: "269", name: "Comoros", length: 7 },
        { code: "290", name: "Ascension Island", length: 4 },
        { code: "291", name: "Eritrea", length: 7 },
        { code: "297", name: "Aruba", length: 7 },
        { code: "298", name: "Faroe Islands", length: 6 },
        { code: "299", name: "Greenland", length: 6 },
        { code: "350", name: "Gibraltar", length: 8 },
        { code: "351", name: "Portugal", length: 9 },
        { code: "352", name: "Luxembourg", length: 9 },
        { code: "353", name: "Ireland", length: 9 },
        { code: "354", name: "Iceland", length: 7 },
        { code: "355", name: "Albania", length: 9 },
        { code: "356", name: "Malta", length: 8 },
        { code: "357", name: "Cyprus", length: 8 },
        { code: "358", name: "Finland", length: 9 },
        { code: "359", name: "Bulgaria", length: 8 },
        { code: "370", name: "Lithuania", length: 8 },
        { code: "371", name: "Latvia", length: 8 },
        { code: "372", name: "Estonia", length: 8 },
        { code: "373", name: "Moldova", length: 8 },
        { code: "374", name: "Armenia", length: 8 },
        { code: "375", name: "Belarus", length: 9 },
        { code: "376", name: "Andorra", length: 6 },
        { code: "377", name: "Monaco", length: 8 },
        { code: "378", name: "San Marino", length: 8 },
        { code: "379", name: "Vatican City", length: 8 },
        { code: "380", name: "Ukraine", length: 9 },
        { code: "381", name: "Serbia", length: 8 },
        { code: "382", name: "Montenegro", length: 8 },
        { code: "383", name: "Kosovo", length: 8 },
        { code: "385", name: "Croatia", length: 9 },
        { code: "386", name: "Slovenia", length: 8 },
        { code: "387", name: "Bosnia and Herzegovina", length: 8 },
        { code: "389", name: "North Macedonia", length: 8 },
        { code: "420", name: "Czech Republic", length: 9 },
        { code: "421", name: "Slovakia", length: 9 },
        { code: "423", name: "Liechtenstein", length: 7 },
        { code: "500", name: "Falkland Islands", length: 5 },
        { code: "501", name: "Belize", length: 7 },
        { code: "502", name: "Guatemala", length: 8 },
        { code: "503", name: "El Salvador", length: 8 },
        { code: "504", name: "Honduras", length: 8 },
        { code: "505", name: "Nicaragua", length: 8 },
        { code: "506", name: "Costa Rica", length: 8 },
        { code: "507", name: "Panama", length: 8 },
        { code: "508", name: "Saint Pierre and Miquelon", length: 6 },
        { code: "509", name: "Haiti", length: 8 },
        { code: "590", name: "Guadeloupe", length: 9 },
        { code: "591", name: "Bolivia", length: 8 },
        { code: "592", name: "Guyana", length: 7 },
        { code: "593", name: "Ecuador", length: 8 },
        { code: "594", name: "French Guiana", length: 9 },
        { code: "595", name: "Paraguay", length: 9 },
        { code: "596", name: "Martinique", length: 9 },
        { code: "597", name: "Suriname", length: 7 },
        { code: "598", name: "Uruguay", length: 8 },
        { code: "599", name: "Netherlands Antilles", length: 7 },
        { code: "670", name: "Timor-Leste", length: 7 },
        { code: "672", name: "Australian External Territories", length: 6 },
        { code: "673", name: "Brunei", length: 7 },
        { code: "674", name: "Nauru", length: 7 },
        { code: "675", name: "Papua New Guinea", length: 8 },
        { code: "676", name: "Tonga", length: 5 },
        { code: "677", name: "Solomon Islands", length: 7 },
        { code: "678", name: "Vanuatu", length: 7 },
        { code: "679", name: "Fiji", length: 7 },
        { code: "680", name: "Palau", length: 7 },
        { code: "681", name: "Wallis and Futuna", length: 6 },
        { code: "682", name: "Cook Islands", length: 5 },
        { code: "683", name: "Niue", length: 4 },
        { code: "685", name: "Samoa", length: 5 },
        { code: "686", name: "Kiribati", length: 5 },
        { code: "687", name: "New Caledonia", length: 6 },
        { code: "688", name: "Tuvalu", length: 5 },
        { code: "689", name: "French Polynesia", length: 6 },
        { code: "690", name: "Tokelau", length: 4 },
        { code: "691", name: "Micronesia", length: 7 },
        { code: "692", name: "Marshall Islands", length: 7 },
        { code: "850", name: "North Korea", length: 9 },
        { code: "852", name: "Hong Kong", length: 8 },
        { code: "853", name: "Macau", length: 8 },
        { code: "855", name: "Cambodia", length: 9 },
        { code: "856", name: "Laos", length: 9 },
        { code: "880", name: "Bangladesh", length: 10 },
        { code: "886", name: "Taiwan", length: 9 },
        { code: "960", name: "Maldives", length: 7 },
        { code: "961", name: "Lebanon", length: 7 },
        { code: "962", name: "Jordan", length: 9 },
        { code: "963", name: "Syria", length: 9 },
        { code: "964", name: "Iraq", length: 9 },
        { code: "965", name: "Kuwait", length: 8 },
        { code: "966", name: "Saudi Arabia", length: 9 },
        { code: "967", name: "Yemen", length: 9 },
        { code: "968", name: "Oman", length: 8 },
        { code: "970", name: "Palestine", length: 9 },
        { code: "971", name: "United Arab Emirates", length: 9 },
        { code: "972", name: "Israel", length: 9 },
        { code: "973", name: "Bahrain", length: 8 },
        { code: "974", name: "Qatar", length: 8 },
        { code: "975", name: "Bhutan", length: 8 },
        { code: "976", name: "Mongolia", length: 8 },
        { code: "977", name: "Nepal", length: 10 },
        { code: "992", name: "Tajikistan", length: 9 },
        { code: "993", name: "Turkmenistan", length: 8 },
        { code: "994", name: "Azerbaijan", length: 9 },
        { code: "995", name: "Georgia", length: 9 },
        { code: "996", name: "Kyrgyzstan", length: 9 },
        { code: "997", name: "Kazakhstan", length: 10 },
        { code: "998", name: "Uzbekistan", length: 9 },
    ]);

    const statusColors = {
        enabled: "success",
        disabled: "error",
    };

    const userStatus = [
        {
            key: "enabled",
            value: t("common.enabled"),
        },
        {
            key: "disabled",
            value: t("common.disabled"),
        },
    ];

    const disabledDate = (current) => {
        // Can not select days before today and today
        return current && current > moment().endOf("day");
    };

    const dayjsObject = (date) => {
        if (date == undefined) {
            return dayjs().tz(appSetting.value.timezone);
        } else {
            return dayjs(date).tz(appSetting.value.timezone);
        }
    };

    const formatDate = (date) => {
        if (date == undefined) {
            return dayjs()
                .tz(appSetting.value.timezone)
                .format(`${appSetting.value.date_format}`);
        } else {
            return dayjs(date)
                .tz(appSetting.value.timezone)
                .format(`${appSetting.value.date_format}`);
        }
    };


    const formatDateTime = (dateTime) => {
        if (dateTime == undefined) {
            return dayjs()
                .tz(appSetting.value.timezone)
                .format(
                    `${appSetting.value.date_format} ${appSetting.value.time_format}`
                );
        } else {
            return dayjs(dateTime)
                .tz(appSetting.value.timezone)
                .format(
                    `${appSetting.value.date_format} ${appSetting.value.time_format}`
                );
        }
    };

    const formatTimeDuration = (seconds) => {
        if (seconds == 0) {
            return 0;
        } else {
            var totalSeconds = parseInt(seconds);
            var hours = Math.floor(totalSeconds / 3600);
            totalSeconds %= 3600;
            var minutes = Math.floor(totalSeconds / 60);
            var remainingSeconds = totalSeconds % 60;

            var secondsString = "";
            if (hours > 0) {
                secondsString += `${hours} H,`;
            }
            if (minutes > 0) {
                secondsString += ` ${minutes} M,`;
            }
            if (remainingSeconds > 0) {
                secondsString = secondsString.trim() + ` ${remainingSeconds} S`;
            }

            return secondsString.trim();
        }
    };

    const formatAmount = (amount) => {
        return parseFloat(parseFloat(amount).toFixed(2));
    };

    const formatAmountCurrency = (amount) => {
        const newAmount = parseFloat(Math.abs(amount))
            .toFixed(2)
            .toString()
            .replace(/\B(?=(\d{3})+(?!\d))/g, ",");

        if (appSetting.value.currency.position == "front") {
            var newAmountString = `${appSetting.value.currency.symbol}${newAmount}`;
        } else {
            var newAmountString = `${newAmount}${appSetting.value.currency.symbol}`;
        }

        return amount < 0 ? `- ${newAmountString}` : newAmountString;
    };

    const formatAmountUsingCurrencyObject = (amount, currency) => {
        const newAmount = parseFloat(Math.abs(amount))
            .toFixed(2)
            .toString()
            .replace(/\B(?=(\d{3})+(?!\d))/g, ",");

        if (currency.position == "front") {
            var newAmountString = `${currency.symbol}${newAmount}`;
        } else {
            var newAmountString = `${newAmount}${currency.symbol}`;
        }

        return amount < 0 ? `${newAmountString}` : newAmountString;
    };

    const calculateFilterString = (filters) => {
        var filterString = "";

        forEach(filters, (filterValue, filterKey) => {
            if (filterValue != undefined) {
                filterString += `${filterKey} eq "${filterValue}" and `;
            }
        });

        if (filterString.length > 0) {
            filterString = filterString.substring(0, filterString.length - 4);
        }

        return filterString;
    };

    const checkPermission = (permissionName) =>
        checkUserPermission(permissionName, user.value);

    /*
    const updatePageTitle = (pageName) => {
        store.commit("auth/updatePageTitle", t(`menu.${pageName}`));
    };
    */
    const updatePageTitle = (pageName) => {
        store.commit("auth/updatePageTitle", t(`menu.${pageName}`, "CRM"));
    };
    
    
    const permsArray = computed(() => {
        const permsArrayList =
            user && user.value && user.value.role ? [user.value.role.name] : [];

        if (user && user.value && user.value.role) {
            forEach(user.value.role.perms, (permission) => {
                permsArrayList.push(permission.name);
            });
        }

        return permsArrayList;
    });

    const slugify = (text) => {
        return text
            .toString()
            .toLowerCase()
            .replace(/\s+/g, "-") // Replace spaces with -
            .replace(/[^\w\-]+/g, "") // Remove all non-word chars
            .replace(/\-\-+/g, "-") // Replace multiple - with single -
            .replace(/^-+/, "") // Trim - from start of text
            .replace(/-+$/, ""); // Trim - from end of text
    };

    const convertStringToKey = (textString) => {
        return textString
            .toString()
            .toLowerCase()
            .replace(/\s+/g, "_") // Replace spaces with -
            .replace(/[^\w\-]+/g, "") // Remove all non-word chars
            .replace(/\-\-+/g, "_") // Replace multiple - with single -
            .replace(/^-+/, "") // Trim - from start of text
            .replace(/-+$/, ""); // Trim - from end of text
    };

    const convertToPositive = (amount) => {
        return amount < 0 ? amount * -1 : amount;
    };

    const willSubscriptionModuleVisible = (moduleName) => {
        if (appType == "non-saas") {
            return true;
        } else {
            if (
                appSetting.value.subscription_plan &&
                appSetting.value.subscription_plan.modules
            ) {
                return includes(
                    appSetting.value.subscription_plan.modules,
                    moduleName
                );
            } else {
                return false;
            }
        }
    };

    // const getRecursiveTypifications2 = (response, excludeId = null) => {
    //     const allTypificationArray = [];
    //     const listArray = [];

    //     response.data.map((responseArray) => {
    //         if (excludeId == null || (excludeId != null && responseArray.x_parent_id != excludeId)) {

    //             let disableNode = true;
    //             let parentNode = find(response.data, { xid: responseArray.x_parent_id });
    //             if (responseArray.x_parent_id === null || (parentNode != undefined && parentNode.x_parent_id === null)) {
    //                 disableNode = false;
    //             }

    //             listArray.push({
    //                 xid: responseArray.xid,
    //                 x_parent_id: responseArray.x_parent_id,
    //                 title: responseArray.name,
    //                 value: responseArray.xid,
    //                 disabled: disableNode,
    //             });
    //         }
    //     });

    //     // console.log(listArray);
    //     listArray.forEach((node) => {
    //         // No parentId means top level
    //         if (!node.x_parent_id) return allTypificationArray.push(node);

    //         // Insert node as child of parent in listArray array

    //         const parentIndex = listArray.findIndex(
    //             (el) => el.xid === node.x_parent_id
    //         );

    //         if (!listArray[parentIndex].children) {
    //             return (listArray[parentIndex].children = [node]);
    //         }


    //         listArray[parentIndex].children.push(node);

    //     });

    //     return allTypificationArray;
    // }

    const getRecursiveTypifications = (response, excludeId = null) => {
    // 1) Plano sin disabled
    const listArray = response.data
        .filter(item => !(excludeId != null && item.x_parent_id === excludeId))
        .map(item => ({
        xid:         item.xid,
        x_parent_id: item.x_parent_id,
        title:       item.name,
        value:       item.xid,
        children:    []
        }));

    // 2) Armo el árbol
    const allTypificationArray = [];
    listArray.forEach(node => {
        if (!node.x_parent_id) {
        allTypificationArray.push(node);
        } else {
        const parent = listArray.find(el => el.xid === node.x_parent_id);
        if (parent) parent.children.push(node);
        }
    });

    // 3) Marco disabled solo en hojas de nivel >=2
    const markDisabled = (nodes, depth = 1) => {
        nodes.forEach(node => {
        if (node.children.length === 0) {
            // si es hoja, solo deshabilito si depth > 1
            node.disabled = depth > 1;
        } else {
            // si tiene hijos, siempre habilitado y sigo bajando
            node.disabled = false;
            markDisabled(node.children, depth + 1);
        }
        });
    };
    markDisabled(allTypificationArray);

    return allTypificationArray;
    };



    const getCampaignUrl = (campaignStatus = "active", viewType = "self") => {
        var campaignsUrl = `call-managers?fields=id,xid,name,status,active,form_id,x_form_id,form{id,xid,name,form_fields},campaignUsers{id,xid,user_id,x_user_id,campaign_id,x_campaign_id},campaignUsers:user{id,xid,name,profile_image,profile_image_url}&campaign_status=${campaignStatus}&view_type=${viewType}&filters=active eq 1&limit=10000`;

        return campaignsUrl;
    };

    const getCampaignStatsUrl = (
        campaignStatus = "active",
        campaignId = undefined,
        userId = undefined,
    ) => {
        var campaignStatsUrl = `leads/campaign-stats?campaign_status=${campaignStatus}`;

        if (campaignId != undefined) {
            campaignStatsUrl += `&campaign_id=${campaignId}`;
        }

        if (userId != undefined) {
            campaignStatsUrl += `&user_id=${userId}`;
        }
        
        return campaignStatsUrl;
    };

    const extractCountryCodeAndNumber = (phoneNumber) => {
        let cleanedPhone = phoneNumber.toString().trim().replace(/\D/g, "");
        let number = cleanedPhone.replace(/^\+/, "");

        let countryCode = null;
        let remainingNumber = number;

        // Check for country code starting from longest to shortest
        for (let i = 0; i < countryCodes.value.length; i++) {
            const country = countryCodes.value[i];
            const code = country.code;

            // Check if the number starts with the country code
            if (number.startsWith(code)) {
                // Extract the remaining part of the number
                const potentialRemainingNumber = number.slice(code.length);

                // Check if the length of the remaining number matches the expected length
                if (potentialRemainingNumber.length === country.length) {
                    countryCode = code;
                    remainingNumber = potentialRemainingNumber;
                    break;
                }
            }
        }

        return {
            remainingNumber,
            countryCode: countryCode ? countryCode : null,
        }
    }
    
    const router = useRouter();
    // Obtener formularios dinámicamente desde las rutas definidas
    const formFolders = computed(() =>
        router.getRoutes()
            .filter(route => route.path.startsWith("/admin/formsUCB/") && route.name !== "admin.formsUCB.index")
            .map(route => ({
                name: route.name,
                title: route.meta.menuKey, // Usa el nombre de la carpeta como título del menú
            }))
    );

    return {
        menuCollapsed,
        appSetting,
        appType,
        themeMode,
        addMenus,
        selectedLang,
        user,
        checkPermission,
        permsArray,
        statusColors,
        userStatus,

        disabledDate,
        formatAmount,
        formatAmountCurrency,
        formatAmountUsingCurrencyObject,
        convertToPositive,
        getRecursiveTypifications,
        calculateFilterString,
        updatePageTitle,

        downloadLangCsvUrl,
        appModules,
        dayjs,
        formatDate,
        formatDateTime,
        dayjsObject,
        slugify,
        convertStringToKey,

        cssSettings,
        globalSetting,

        willSubscriptionModuleVisible,
        visibleSubscriptionModules,
        formatTimeDuration,

        getCampaignUrl,
        getCampaignStatsUrl,
        rightSidebarValue,

        reloadUphoneStringData,
        countryCodes,
        extractCountryCodeAndNumber,
        formFolders, // Se retornan los nombres de los CRM actuales para poder ser utilizados
    };
};

export default common;
