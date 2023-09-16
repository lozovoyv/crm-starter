<template>
    <ListTable :list="history" :external-state="externalState">
        <ListTableRow v-for="record in history.list">
            <ListTableCell style="white-space: nowrap">{{ toDatetime(record.timestamp, true) }}</ListTableCell>
            <ListTableCell style="width: 100%;">
                <span v-html="formatAction(record)"/>
                <span v-if="record.description"> ({{ record.description }})</span>
            </ListTableCell>
            <ListTableCell>
                <div v-for="link in record.links" style="white-space: nowrap" v-html="formatLink(link)"/>
            </ListTableCell>
            <ListTableCell style="text-align: center">
                <template v-if="record.comments_count === 0">{{ record.comments_count ? record.comments_count : '—' }}</template>
                <GuiLink v-else @click="showComments(record)">{{ record.comments_count }}</GuiLink>
            </ListTableCell>
            <ListTableCell style="text-align: center">
                <template v-if="record.changes_count === 0">{{ record.changes_count ? record.changes_count : '—' }}</template>
                <GuiLink v-else @click="showChanges(record)">{{ record.changes_count }}</GuiLink>
            </ListTableCell>
            <ListTableCell style="white-space: nowrap">{{ record.position }}</ListTableCell>
        </ListTableRow>
        <template v-slot:empty>
            {{ emptyMessage ? emptyMessage : 'Нет истории' }}
        </template>
    </ListTable>

    <HistoryChanges :url="url" ref="changes"/>

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
import {useRouter} from "vue-router";
import {CommunicationState} from "@/Core/Types/Communications";
import {apiEndPoint, ApiEndPointMethod} from "@/Core/Http/ApiEndPoints";
import {makeLink} from "@/Core/EntryLink/EntryLink";

const props = defineProps<{
    url: string,
    method: ApiEndPointMethod,
    rememberPrefix?: string,
    emptyMessage?: string,
    externalState?: CommunicationState,
}>()

type HistoryLink = {
    entry_type: string | null,
    entry_id: number | string | null,
    entry_caption: string | null,
    entry_tag: string | null,
    has_entry: boolean,
    key: string | null,
};

type History = {
    id: number,
    action: string,
    description: string | null,
    entry_type: string | null,
    entry_id: number | string | null,
    entry_tag: string | null,
    entry_caption: string | null,
    has_entry: boolean,
    links: Array<HistoryLink>,
    position: string | null,
    position_id: number | null,
    timestamp: string,
    comments_count: number,
    changes_count: number,
};

const router = useRouter();

const history = ref<List<History>>(new List<History>({
    load_url: apiEndPoint(props.method, props.url),
    remember: {
        prefix: props.rememberPrefix,
        filters: ['active'],
        pagination: true,
        order: true
    }
}));

const changes = ref<InstanceType<typeof HistoryChanges> | undefined>(undefined);

history.value.load();

function reload(): void {
    history.value.reload();
}

function formatAction(history: History): string {
    const entry: string | undefined = makeLink(history.entry_caption, history.entry_type, history.entry_id, history.entry_tag, history.has_entry);

    return history.action.replace(':entry', entry ? entry : '');
}

function formatLink(link: HistoryLink):string {
    const entry: string | undefined =  makeLink(link.entry_caption, link.entry_type, link.entry_id, link.entry_tag, link.has_entry, true);

    return entry ?? '***'
}

function showComments(record: History): void {
    console.log(record.id, toDatetime(record.timestamp));
}

function showChanges(record: History): void {
    changes.value?.show(record.id, toDatetime(record.timestamp, true), record.action.replace(':entry', record.entry_caption ? record.entry_caption : ''));
}

defineExpose({
    reload,
});
</script>
