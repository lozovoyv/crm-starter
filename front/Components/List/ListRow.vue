<template>
    <tr class="list-table__row" :class="{
        'list-table__row-no-highlight': noHighlight,
        'list-table__row-no-even-odd': noOddEven,
    }">
        <slot/>
    </tr>
</template>

<script setup lang="ts">
const props = defineProps<{
    noHighlight?: boolean,
    noOddEven?: boolean,
}>();
</script>

<style lang="scss">
@import "@/variables.scss";

.list-table__row {
    border-bottom: 1px solid transparentize($color_gray_lighten_2, 0.75);

    // regular odd if enabled
    &:not(&-no-even-odd):nth-child(odd) {
        background-color: $color_white;
    }

    // regular even if enabled
    &:not(&-no-even-odd):nth-child(even) {
        background-color: transparentize($color_gray_lighten_2, 0.9);
    }

    // hover highlighting if enabled
    &:not(&-no-highlight):hover {
        background-color: transparentize($color_success, 0.92);
    }
}
</style>
