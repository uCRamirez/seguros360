<template>
    <div v-if="appEnv != 'envato'">
        <a-divider style="font-size: inherit;">
            {{ $t("common.demo_account_credentials") }}
        </a-divider>
        <a-table
            :pagination="false"
            :dataSource="demoCredentials"
            :columns="demoCredentialsColumns"
            :style="{
                backgroundColor: backgroundColor,
            }"
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.dataIndex === 'details'">
                    <b>{{ record.name }}</b> <br />
                    Email : {{ record.email }} <br />
                    Password : {{ record.password }}
                </template>
                <template v-if="column.dataIndex === 'action'">
                    <a-tooltip
                        @click="
                            () => {
                                credentials.email = record.email;
                                credentials.password = record.password;
                            }
                        "
                    >
                        <template #title>
                            {{
                                $t("popover.click_here_to_copy_credentials", [
                                    record.name,
                                ])
                            }}
                        </template>
                        <SnippetsOutlined />
                    </a-tooltip>
                </template>
            </template>
        </a-table>
    </div>
</template>

<script>
import { defineComponent, reactive, ref, onMounted } from "vue";
import { SnippetsOutlined } from "@ant-design/icons-vue";

export default defineComponent({
    props: ["credentials", "backgroundColor"],
    components: {
        SnippetsOutlined,
    },
    setup() {
        const appEnv = window.config.app_env;
        const appType = window.config.app_type;
        const demoCredentialsColumns = ref([
            {
                title: "Details",
                dataIndex: "details",
            },
            {
                title: "action",
                dataIndex: "action",
            },
        ]);
        const demoCredentials = ref([
            {
                name: "Admin",
                email: "admin@example.com",
                password: 12345678,
            },
            {
                name: "Supervisor",
                email: "jose@gmail.com",
                password: 123,
            },
        ]);

        onMounted(() => {
            if (appType == "saas") {
                demoCredentials.value = [
                    {
                        name: "Supervisor",
                        email: "jose@gmail.com",
                        password: 123,
                    },
                    ...demoCredentials.value,
                ];
            }
        });

        return {
            appEnv,
            demoCredentials,
            demoCredentialsColumns,
        };
    },
});
</script>
