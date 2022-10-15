<template>
    <PopUp ref="popup" :title="popUpTitle" :close-on-overlay="true">
        <ListComponent :list="list">
            <ListRow v-for="change in list.list">
                <ListCell>{{ change.parameter }}</ListCell>
                <ListCell>{{ change.old ? change.old : '—' }}</ListCell>
                <ListCell>{{ change.new ? change.new : '—' }}</ListCell>
            </ListRow>
        </ListComponent>
    </PopUp>
</template>

<script setup lang="ts">
import {ref} from "vue";
import PopUp from "@/Components/PopUp/PopUp.vue";
import {List} from "@/Core/List";
import ListComponent from "@/Components/List/ListComponent.vue";
import ListRow from "@/Components/List/ListRow.vue";
import ListCell from "@/Components/List/ListCell.vue";

type HistoryChange = {
    parameter: string,
    old: unknown,
    new: unknown,
}

const props = defineProps<{
    url: string;
}>();

const popup = ref<InstanceType<typeof PopUp> | null>(null);

const list = ref<List<HistoryChange>>(new List(props.url, {}, {without_pagination: true}));

const popUpTitle = ref<string | null>(null);

function show(recordId: number, title: string) {
    if (!popup.value) {
        return;
    }
    popUpTitle.value = title;
    popup.value.show();
    list.value.options = {id: recordId};
    list.value.initial();
}

defineExpose({
    show,
})
</script>
