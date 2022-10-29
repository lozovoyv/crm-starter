<template>
    <ListTable :list="history">
        <ListTableRow v-for="record in history.list">
            <ListTableCell style="white-space: nowrap">{{ toDatetime(record.timestamp, true) }}</ListTableCell>
            <ListTableCell style="width: 100%;">
                <span v-html="formatAction(record)"/>
                <span v-if="record.description"> ({{ record.description }})</span>
            </ListTableCell>
            <ListTableCell>
                <div v-for="link in record.links" style="white-space: nowrap">
                    {{ link.entry_title }}
                </div>
            </ListTableCell>
            <ListTableCell style="text-align: center">
                <template v-if="record.comments_count === 0 || !commentsUrl">{{ record.comments_count ? record.comments_count : '—' }}</template>
                <GuiLink v-else @click="showComments(record)">{{ record.comments_count }}</GuiLink>
            </ListTableCell>
            <ListTableCell style="text-align: center">
                <template v-if="record.changes_count === 0 || !changesUrl">{{ record.changes_count ? record.changes_count : '—' }}</template>
                <GuiLink v-else @click="showChanges(record)">{{ record.changes_count }}</GuiLink>
            </ListTableCell>
            <ListTableCell style="white-space: nowrap">{{ record.position }}</ListTableCell>
        </ListTableRow>
        <template v-slot:empty>
            {{ emptyMessage ? emptyMessage : 'Нет истории' }}
        </template>
    </ListTable>

    <HistoryChanges :url="changesUrl" ref="changes"/>

</template>

<script setup lang="ts">
import ListTable from "@/Components/List/ListComponent.vue";
import ListTableRow from "@/Components/List/ListRow.vue";
import ListTableCell from "@/Components/List/ListCell.vue";
import {ref} from "vue";
import {List} from "@/Core/List";
import {toDatetime} from "@/Core/Helpers/DateTime";
import GuiLink from "@/Components/GUI/GuiLink.vue";
import HistoryChanges from "@/Components/History/HistoryChanges.vue";

const props = defineProps<{
    url: string,
    commentsUrl?: string,
    changesUrl?: string,
    prefix?: string,
    options?: { [index: string]: any },
    emptyMessage?: string,
}>()

type HistoryLink = {
    entry_title: string,
    entry_name?: string,
    entry_id?: number,
};

type History = {
    id: number,
    timestamp: string,
    entry_title: string | null,
    entry_name: string | null,
    entry_id: number | null,
    action: string,
    description: string | null,
    position_id: number | null,
    position: string | null,
    links: Array<HistoryLink>,
    changes: null,
    comments: null,
    links_count: number,
    changes_count: number,
    comments_count: number,
};

const history = ref<List<History>>(new List<History>(props.url, props.options ? props.options : {}, {
    prefix: props.prefix ? props.prefix : undefined, remember: {filters: ['active'], pagination: true, order: true}
}));

const changes = ref<InstanceType<typeof HistoryChanges>>(null);

history.value.initial();

function reload(): void {
    history.value.reload();
}

function formatAction(history: History): string {
    let entry: string | null;
    if (history.entry_name && history.entry_id) {
        // todo make link
        entry = history.entry_title;
    } else {
        entry = history.entry_title;
    }
    return history.action.replace(':entry', entry ? entry : '');
}

function showComments(record: History): void {
    console.log(record.id, toDatetime(record.timestamp));
}

function showChanges(record: History): void {
    if (changes.value !== null) {
        changes.value.show(record.id, formatAction(record) + ' — ' + toDatetime(record.timestamp));
    }
}

defineExpose({
    reload,
});
</script>
