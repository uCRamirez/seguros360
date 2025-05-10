<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.formsUCB`)" class="p-0" />
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`menu.forms`) }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t(`${route.meta.menuKey}`) }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>
    
    <admin-page-table-content>
        <component :is="formComponent" v-if="formComponent" /> 
        <p v-else>{{ $t(`crm.form_not_found`) }}</p>
    </admin-page-table-content>
</template>

<script setup>
import { computed, defineAsyncComponent } from "vue";
import { useRoute } from "vue-router";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";

const route = useRoute();

const pageTitle = computed(() => route.meta.menuKey || "Formularios UCB");

const formComponent = computed(() => {
    if (route.meta.formPath) {
        return defineAsyncComponent(() => route.meta.formPath());
    }
    return null;
});


</script>
