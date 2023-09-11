<template>
    <ListBarItem :title="title">
        <InputSearch
            v-model="list.search"
            :clearable="clearable"
            :placeholder="placeholder"
            :disabled="disabled"
            @change="changeSearch"
        />
    </ListBarItem>
</template>

<script setup lang="ts">
import InputSearch from "@/Components/Input/InputSearch.vue";
import {List} from "@/Core/List";
import ListBarItem from "@/Components/List/ListBarItem.vue";
import {InputSearchProps} from "@/Components/Input/Helpers/Types";

interface Props extends InputSearchProps {
    list: List<any>,
    title?: string,
    manual?: boolean,
}

const props = withDefaults(defineProps<Props>(), {
    clearable: true,
});

function changeSearch(value: string | null): void {
    if (!props.manual) {
        props.list.search = value;
        props.list.load();
    }
}
</script>

