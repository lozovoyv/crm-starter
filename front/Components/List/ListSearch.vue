<template>
    <InputSearch
        v-model="list.search"
        :clearable="true"
        :placeholder="placeholder"
        @change="changeSearch"
    />
</template>

<script setup lang="ts">
import InputSearch from "@/Components/Input/InputSearch.vue";
import {List} from "@/Core/List";

const props = defineProps<{
    list: List<any>,
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

