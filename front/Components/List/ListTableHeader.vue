<template>
    <thead class="list-table__header" v-if="titles">
        <tr class="list-table__header-row">
            <th class="list-table__header-cell" v-for="title in titles">{{ title }}</th>
            <th class="list-table__header-cell list-table__header-cell-actions" v-if="action"></th>
        </tr>
    </thead>
</template>

<script setup lang="ts">

const props = defineProps<{
    titles?: { [index: string | number]: string },
    action?: boolean,
}>();
</script>

<style lang="scss">
@import "@/variables.scss";

.list-table__header {
    background-color: $color_white;

    &-row {
        border-style: solid;
        border-color: $color_gray_lighten_2;
        border-width: 0 0 1px 0;
    }

    &-cell {
        color: $color_text_black;
        vertical-align: top;
        font-family: $project_font;
        font-size: 14px;
        text-transform: uppercase;
        box-sizing: border-box;
        padding: 7px 15px;
        min-height: 60px;
        text-align: left;
        font-weight: 600;

        &-actions {
            width: 70px;
        }
    }
}
</style>
