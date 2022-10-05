<template>
    <ListTable :list="history">
        <!--        <template v-slot:search>-->
        <!--            <ListSearch :list="history" placeholder="ID, название"/>-->
        <!--        </template>-->
        <ListTableRow v-for="record in history.list">
            <ListTableCell style="white-space: nowrap">{{ toDatetime(record.timestamp, true) }}</ListTableCell>
            <ListTableCell style="width: 100%;">
                {{ record.action }}
                <span v-if="record.description"> ({{ record.description }})</span>
            </ListTableCell>
            <ListTableCell>
                <div v-for="link in record.links" style="white-space: nowrap">
                    {{ link.entry_title }}
                </div>
            </ListTableCell>
            <ListTableCell/>
            <ListTableCell/>
            <ListTableCell style="white-space: nowrap">{{ record.position }}</ListTableCell>
        </ListTableRow>
        <template v-slot:empty>
            Нет истории изменения ролей
        </template>
    </ListTable>
</template>

<script setup lang="ts">
import ListTable from "@/Components/List/ListTable.vue";
import ListTableRow from "@/Components/List/ListTableRow.vue";
import ListTableCell from "@/Components/List/ListTableCell.vue";
import {ref} from "vue";
import {List} from "@/Core/List";
import {toDatetime} from "@/Core/Helpers/DateTime";

const props = defineProps<{
    url: string,
    prefix?: string,
    options?: { [index: string]: any },
}>()

type HistoryLink = {
    entry_title: string,
    entry_name?: string,
    entry_id?: number,
};

type History = {
    id: number,
    timestamp: string,
    entry_name: string,
    entry_id: number | null,
    action: string,
    description: string | null,
    position_id: number | null,
    position: string | null,
    links: Array<HistoryLink>,
    changes: null,
    comments: null,
};

const history = ref<List<History>>(new List<History>(props.url, props.options ? props.options : {}, {
    prefix: props.prefix ? props.prefix : undefined, remember: {filters: ['active'], pagination: true, order: true}
}));

history.value.initial();
</script>
