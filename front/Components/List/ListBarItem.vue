<template>
    <div class="list-table-container-bar-filters__item">
        <span class="list-table-container-bar-filters__item-title">{{ title }}</span>
        <slot/>
    </div>
</template>

<script setup lang="ts">
const props = defineProps<{
    title?: string,
}>();
</script>

<style lang="scss">
@import "@/variables.scss";

.list-table-container-bar-filters__item {
    box-sizing: border-box;
    max-width: 250px;

    &-title {
        white-space: nowrap;
        font-family: $project_font;
        color: $color_text_black;
        height: 24px;
        line-height: 24px;
        font-size: 15px;
        display: block;
    }
}
</style>
