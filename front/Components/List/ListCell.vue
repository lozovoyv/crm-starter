<template>
    <td class="list-table__cell" :class="{'list-table__cell-nowrap': nowrap, 'list-table__cell-action': action}" :colspan="colspan">
        <slot/>
    </td>
</template>

<script setup lang="ts">
import {computed} from "vue";

const props = defineProps<{
    nowrap?: boolean,
    action?: boolean,
    cols?: number,
}>();

const colspan = computed((): number => {
    return props.cols === undefined ? 1 : props.cols;
});
</script>

<style lang="scss">
@import "@/variables.scss";

.list-table__cell {
    box-sizing: border-box;
    padding: 8px 15px;
    vertical-align: top;
    font-family: $project_font;
    font-size: 14px;
    color: $color_text_black;

    &-nowrap {
        white-space: nowrap;
    }

    &-action {
        padding: 0 10px;
        vertical-align: middle;
    }

    //
    //& > div {
    //    display: block;
    //
    //    &:not(:last-child) {
    //        margin-bottom: 8px;
    //    }
    //}
}
</style>
