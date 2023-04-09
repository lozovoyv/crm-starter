<template>
    <LoadingProgress :loading="list.is_loading || processing">
        <GuiTitle style="display: flex; align-items: center">
            <span style="flex-grow: 1">{{ title }}</span>
            <GuiButton :type="'default'" @clicked="edit(null)">Добавить</GuiButton>
        </GuiTitle>

        <DictionaryEditorForm ref="form" :dictionary="dictionary"/>

        <table class="dictionary-editor">
            <thead>
            <tr>
                <th></th>
                <th v-for="title in list.titles">{{ title }}</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <DictionaryEditorItem v-for="item in items"
                                  :item="item"
                                  :options="list.payload['fields']"
                                  :orderable="orderable"
                                  :switchable="switchable"
                                  @edit="edit"
                                  @remove="remove"
                                  @switch-off="switchOff"
                                  @switch-on="switchOn"
                                  @dragstart="dragstart"
                                  @drop="drop"
                                  @dragenter="dragenter"
                                  @dragend="dragend"
            />
            </tbody>
        </table>
    </LoadingProgress>
</template>

<script setup lang="ts">
import {List} from "@/Core/List";
import {computed, ref, watch} from "vue";
import LoadingProgress from "@/Components/LoadingProgress.vue";
import GuiTitle from "@/Components/GUI/GuiTitle.vue";
import GuiButton from "@/Components/GUI/GuiButton.vue";
import DictionaryEditorForm from "@/Components/Dictionary/DictionaryEditorForm.vue";
import DictionaryEditorItem from "@/Components/Dictionary/DictionaryEditorItem.vue";
import {http} from "@/Core/Http/Http";
import toaster from "@/Core/Toaster/Toaster";
import {processEntry} from "@/Core/Helpers/ProcessEntry";
import dialog from "@/Core/Dialog/Dialog";

const props = defineProps<{
    dictionary: string,
}>();

const list = ref<List<{ id: number, enabled?: boolean, order?: number | null, locked?: boolean, hash: string | null, [index: string]: string | number | boolean | undefined | null }>>(new List('/api/dictionaries/list', {dictionary: props.dictionary}, {without_pagination: true}));
const form = ref<InstanceType<typeof DictionaryEditorForm> | undefined>(undefined);
const processing = ref<boolean>(false);

const title = computed((): string => {
    if (list.value.is_loaded) {
        return list.value.payload['title'];
    }
    return '...';
});

const items = computed((): Array<{ id: number, enabled?: boolean, order?: number | null, locked?: boolean, hash: string | null, [index: string]: string | number | boolean | undefined | null }> => {
    return list.value.list.sort((itemA, itemB): -1 | 0 | 1 => {
        const orderA = itemA.order ? itemA.order : null;
        const orderB = itemB.order ? itemB.order : null;
        if (orderA === null && orderB === null) return 0;
        if (orderA === null && orderB !== null) return -1;
        if (orderA !== null && orderB === null) return 1;
        if (orderA && orderB && orderA < orderB) return -1;
        if (orderA && orderB && orderA > orderB) return 1;
        return 0;
    })
})
init();

const orderable = computed((): boolean => {
    return list.value.is_loaded && list.value.payload['orderable'] ? list.value.payload['orderable'] : false;
})

const switchable = computed((): boolean => {
    return list.value.is_loaded && list.value.payload['switchable'] ? list.value.payload['switchable'] : false;
})

const dragging = ref<number | undefined>(undefined);

watch(() => props.dictionary, () => init());

function init(): void {
    list.value.load();
}

function dragstart(id: number) {
    dragging.value = id;
}

function drop() {
    dragging.value = undefined;
}

function dragenter(id: number): void {
    if (!dragging.value) {
        return;
    }
    const draggingItem = list.value.list.find(item => item.id === dragging.value);
    if (!draggingItem || id === draggingItem.id) {
        return;
    }
    const draggingOverItem = list.value.list.find(item => item.id === id);
    if (!draggingOverItem) {
        return;
    }
    const order = draggingOverItem.order;
    draggingOverItem.order = draggingItem.order;
    draggingItem.order = order;
}

function dragend() {
    dragging.value = undefined;
    sync();
}

function switchOff(id: number) {
    setItemEnabled(id, false);
}

function switchOn(id: number) {
    setItemEnabled(id, true);
}

function setItemEnabled(id: number, state: boolean) {
    const hash = list.value.list.find(item => item.id === id)?.hash;
    http.post<{}, { data: { message: string, payload: { hash: string } } }>('/api/dictionaries/toggle', {dictionary: props.dictionary, id: id, state: state, hash: hash})
        .then(response => {
            const item = list.value.list.find(item => item.id === id);
            if (item) {
                item.enabled = state;
                item.hash = response.data.payload.hash;
            }
            toaster.success(response.data.message, 3000);
        })
        .catch(error => {
            toaster.error(error.data.message);
        });
}

function edit(id: number | null) {
    if (form.value) {
        form.value.show(id)?.then(() => {
            init();
        });
    }
}

function remove(id: number | null) {
    const item = list.value.list.find(item => item.id === id);

    processEntry('Удаление', `Удалить запись "${item?.name}"?`, dialog.button('yes', 'Удалить', 'error'),
        '/api/dictionaries/delete', {dictionary: props.dictionary, id: id, hash: item?.hash},
        p => processing.value = p
    ).then(() => {
        init();
    });
}

function sync() {
    const items: Array<{ id: number, order: number | null | undefined }> = [];
    list.value.list.map(item => {
        items.push({id: item.id, order: item.order});
    })
    http.post('/api/dictionaries/sync', {dictionary: props.dictionary, data: items})
        .then(() => {
            init();
        })
        .catch(error => {
            toaster.error(error.message);
        })
}
</script>

<style lang="scss">
@import "@/variables.scss";

.dictionary-editor {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 5px;

    & th {
        font-family: $project_font;
        font-weight: normal;
        text-align: left;
        padding: 0 8px;
        font-size: 14px;
        color: $color_gray_darken_2;
    }
}
</style>
