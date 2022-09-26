<template>
    <table class="list-table">
        <slot name="header" v-if="$slots.header"/>
        <ListTableHeader v-else :titles="titles" :action="action"/>
        <tbody class="list-table__body">
        <slot/>
        </tbody>
    </table>
</template>

<script setup lang="ts">
import ListTableHeader from "@/Components/List/ListTableHeader.vue";

const props = defineProps<{
    titles?: { [index: string | number]: string },
    action?: boolean,
}>();
</script>

<style lang="scss">
.list-table {
    border-collapse: collapse;
    min-width: 100%;
}
</style>
