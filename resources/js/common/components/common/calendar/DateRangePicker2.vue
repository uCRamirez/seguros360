<template>
    <a-config-provider :locale="antdLocale">
		<a-range-picker v-model:value="dateRangeValue" :format="appSetting.date_format"
			:placeholder="[$t('common.start_date'), $t('common.end_date')]" :disabled="rangeDisabled"
			:disabledDate="disabledDate" style="width: 100%" @change="dateTimeChanged" />
    </a-config-provider>

</template>

<script>
import { defineComponent, onMounted, ref, computed, watch } from 'vue';
import common from '../../../composable/common';
import esES from 'ant-design-vue/es/locale/es_ES';
import enUS from 'ant-design-vue/es/locale/en_US';
import 'dayjs/locale/es';
import 'dayjs/locale/en';
import dayjs from 'dayjs';
import { useI18n } from "vue-i18n";


export default defineComponent({
	props: {
		dateRange: { type: Array, default: () => [] }
	},
	emits: ['dateTimeChanged'],
	setup(props, { emit }) {
        const { locale } = useI18n();

		const antdLocale = computed(() =>
            locale.value === 'en' ? enUS : esES
        );
        watch(locale, (newLang) => {
            dayjs.locale(newLang);
        });

		const dateRangeValue = ref([]);
		const { appSetting, dayjs, permsArray } = common();

		const startOfMonth = dayjs()
			.tz(appSetting.value.timezone)
			.startOf('month')
			.startOf('day');
		const endOfToday = dayjs()
			.tz(appSetting.value.timezone)
			.endOf('day');

		// permiso para ver todo el rango
		const hasFullPerm = computed(() =>
			permsArray.value.includes('admin') || permsArray.value.includes('leads_view_all')
		);

		// si NO tiene permiso, deshabilita SOLO el input de inicio
		const rangeDisabled = computed(() =>
			hasFullPerm.value ? false : [true, false]
		);

		// si NO tiene permiso, bloquea cualquier fecha fuera de [startOfMonth, endOfToday]
		const disabledDate = (current) => {
			if (hasFullPerm.value) return false;
			return current < startOfMonth || current > endOfToday;
		};

		onMounted(() => {
			let start = startOfMonth;
			let end = endOfToday;

			if (props.dateRange.length === 2) {
				// si viene rango por props
				start = hasFullPerm.value
					? dayjs(props.dateRange[0])
					: startOfMonth;
				end = dayjs(props.dateRange[1]);
			}

			dateRangeValue.value = [start, end];
			emit('dateTimeChanged', [
				start.tz(appSetting.value.timezone)
					.startOf('day')
					.utc()
					.format('YYYY-MM-DD HH:mm:ss'),
				end.tz(appSetting.value.timezone)
					.endOf('day')
					.utc()
					.format('YYYY-MM-DD HH:mm:ss')
			]);
		});

		const dateTimeChanged = (vals) => {
			// Determinamos el inicio: si tiene permiso y seleccionó un valor, lo usamos;
			// si no, forzamos al primer día del mes.
			const selStart = hasFullPerm.value && vals?.[0]
				? vals[0]
				: startOfMonth;

			// Determinamos el fin: si hay un valor seleccionado, lo usamos;
			// si no (limpiado), tomamos el día de hoy.
			const selEnd = vals?.[1]
				? vals[1]
				: endOfToday;

			// Actualizamos el picker y emitimos con formato UTC
			dateRangeValue.value = [selStart, selEnd];
			emit('dateTimeChanged', [
				selStart
					.tz(appSetting.value.timezone)
					.startOf('day')
					.utc()
					.format('YYYY-MM-DD HH:mm:ss'),
				selEnd
					.tz(appSetting.value.timezone)
					.endOf('day')
					.utc()
					.format('YYYY-MM-DD HH:mm:ss')
			]);
		};

		return {
			antdLocale,
			appSetting,
			dateRangeValue,
			rangeDisabled,
			disabledDate,
			dateTimeChanged
		};
	}
});
</script>
