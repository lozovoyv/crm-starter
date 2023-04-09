<template>
    <Pagination :pagination="list.pagination" :options="options" @pagination="setPage"/>
</template>

<script setup lang="ts">
import {List} from "@/Core/List";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps<{
    list: List<any>,
    options?: Array<number>,
}>();

const emit = defineEmits<{
    (e: 'pagination', page: number, perPage: number): void,
}>()

function setPage(page: number, perPage: number): void {
    props.list.load(page, perPage);
    emit('pagination', page, perPage);
}
</script>
