<template>
    <ListBarItem :title="title">
    <InputSearch
        v-model="list.search"
        :clearable="true"
        :placeholder="placeholder"
        @change="changeSearch"
    />
    </ListBarItem>
</template>

<script setup lang="ts">
import InputSearch from "@/Components/Input/InputSearch.vue";
import {List} from "@/Core/List";
import ListBarItem from "@/Components/List/ListBarItem.vue";

const props = defineProps<{
    list: List<any>,
    title?: string,
    manual?: boolean,
    placeholder?: string,
}>();

function changeSearch(value: string | null): void {
    if(!props.manual) {
        props.list.search = value;
        props.list.load();
    }
}
</script>

