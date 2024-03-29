<template>
    <PopUp ref="popup" title="Изменения" :close-on-overlay="true" :width="{min: '320px'}">
        <table class="history-change-table">
            <tbody>
            <tr>
                <td class="history-change-table__head">Дата:</td>
                <td>{{ _date }}</td>
            </tr>
            <tr>
                <td class="history-change-table__head">Событие:</td>
                <td>{{ _message }}</td>
            </tr>
            </tbody>
        </table>
        <table class="history-change-table">
            <thead>
            <tr>
                <th v-for="title in list.titles" class="history-change-table__head">{{ title }}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="change in list.list">
                <td class="history-change-table__head">{{ change.parameter }}:</td>
                <td class="history-change-table__value" v-html="format(change.old)"/>
                <td class="history-change-table__value" v-html="format(change.new)"/>
            </tr>
            </tbody>
        </table>
    </PopUp>
</template>

<script setup lang="ts">
import {ref} from "vue";
import PopUp from "@/Components/PopUp/PopUp.vue";
import {List} from "@/Core/List";
import {apiEndPoint} from "@/Core/Http/ApiEndPoints";

type HistoryChange = {
    parameter: string,
    old: number | string | number[] | string[] | null,
    new: number | string | number[] | string[] | null,
}

const props = defineProps<{
    url: string;
}>();

const popup = ref<InstanceType<typeof PopUp> | undefined>(undefined);

const list = ref<List<HistoryChange>>(new List());

const _date = ref<string | null>(null);
const _message = ref<string | null>(null);

function show(recordID: number, date: string | null, message: string | null) {
    if (popup.value === undefined) {
        return;
    }
    _date.value = date;
    _message.value = message;
    list.value.clear();
    popup.value.show();
    popup.value.process(true);
    list.value.setConfig({
        load_url: apiEndPoint('get', props.url + '/{recordID}/change', {recordID: recordID}),
        use_pagination: false,
    });
    list.value.load()
        .then(() => {
            if (popup.value !== undefined) {
                popup.value.process(false);
            }
        });
}

function format(value: number | string | number[] | string[] | null): string {
    if (value instanceof Array) {
        return value.join('<br/>');
    }

    return value !== null ? String(value) : '—';
}

defineExpose({
    show,
})
</script>

<style lang="scss">
@import "@/variables";

.history-change-table {
    @include font(16px);
    color: $color_text_black;
    margin: 16px 0;
    border-collapse: collapse;
    width: 100%;

    &__head {
        @include font(14px);
        color: $color_gray_darken_2;
    }

    &__value {
        padding: 0 8px;
    }

    & td {
        vertical-align: baseline;
    }

    & th, & td {
        padding: 4px 8px 4px 0;
    }

    & tr:not(:last-child), & thead tr {
        border-bottom: 1px dashed transparentize($color_gray_lighten_2, 0.5);
    }

    & td:nth-child(1) {
        width: 150px;
    }
}
</style>
