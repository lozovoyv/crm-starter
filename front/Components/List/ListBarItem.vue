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
        @include font(14px);
        color: $color_text_black;
        height: 24px;
        line-height: 24px;
        display: block;
    }
}
</style>
