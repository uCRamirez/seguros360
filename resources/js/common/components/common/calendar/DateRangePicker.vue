<template>
	<a-config-provider :locale="antdLocale">
		<a-range-picker
			v-model:value="dateRangeValue"
			:format="appSetting.date_format"
			:placeholder="[$t('common.start_date'), $t('common.end_date')]"
			style="width: 100%"
			@change="dateTimeChanged"
		/>
    </a-config-provider>
</template>

<script>
import { defineComponent, onMounted, ref, computed, watch } from "vue";
import common from "../../../composable/common";
import esES from 'ant-design-vue/es/locale/es_ES';
import enUS from 'ant-design-vue/es/locale/en_US';
import 'dayjs/locale/es';
import 'dayjs/locale/en';
import dayjs from 'dayjs';
import { useI18n } from "vue-i18n";

export default defineComponent({
	props: {
		dateRange: {
			default: [],
		},
	},
	emits: ["dateTimeChanged"],
	setup(props, { emit }) {
		const { locale } = useI18n();

		const antdLocale = computed(() =>
            locale.value === 'en' ? enUS : esES
        );
        watch(locale, (newLang) => {
            dayjs.locale(newLang);
        });

		const dateRangeValue = ref([]);
		const { appSetting, dayjs } = common();

		onMounted(() => {
			if (props.dateRange && props.dateRange.length > 0) {
				dateRangeValue.value = [
					dayjs(props.dateRange[0]),
					dayjs(props.dateRange[1]),
				];
			}
		});

		const dateTimeChanged = (newValue) => {
			if (newValue) {
				emit("dateTimeChanged", [
					newValue[0]
						.tz(appSetting.value.timezone)
						.startOf("day")
						.utc()
						.format("YYYY-MM-DD HH:mm:ss"),
					newValue[1]
						.tz(appSetting.value.timezone)
						// .endOf("day") // si se habilita toma un dia mas del seleccionado
						.utc()
						.format("YYYY-MM-DD HH:mm:ss"),
				]);
			} else {
				emit("dateTimeChanged", []);
			}
		};

		const resetPicker = () => {
			dateRangeValue.value = [];
		};

		return {
			antdLocale,
			appSetting,
			dateRangeValue,
			dateTimeChanged,
			resetPicker,
		};
	},
});
</script>

<style></style>