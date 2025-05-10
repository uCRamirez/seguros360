<template>
    <a-typography-title :level="5">
        <slot></slot>
    </a-typography-title>
    <a-divider
        :style="{
            height: '2px',
            backgroundColor:
                themeMode == 'dark' ? '1px solid #303030' : '1px solid #f0f0f0',
            marginTop: '5px',
            marginBottom: '20px',
        }"
    />
</template>

<script>
import common from "../../../composable/common";

export default {
    setup(props, { emit }) {
        const { themeMode } = common();

        return {
            themeMode,
        };
    },
};
</script>

<style></style>
