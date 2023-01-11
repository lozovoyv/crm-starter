<template>
    <div class="group-box" :style="style">
        <div class="group-box__box"></div>
        <div class="group-box__title" v-if="title">{{ title }}</div>
        <slot/>
    </div>
</template>

<script setup lang="ts">
import {computed} from "vue";

const props = defineProps<{
    title?: string,
    width?: string,
    minWidth?: string,
    maxWidth?: string,
}>();

const style = computed((): object => {
    let style: { width?: string, minWidth?: string, maxWidth?: string } = {};
    style['width'] = props.width ? props.width : '100%';
    if (props.minWidth) {
        style['minWidth'] = props.minWidth;
    }
    if (props.maxWidth) {
        style['maxWidth'] = props.maxWidth;
    }
    return style;
});
</script>

<style lang="scss">
@import "@/variables.scss";

.group-box {
    font-family: $project_font;
    padding: 8px 24px;
    position: relative;
    box-sizing: border-box;
    margin: 10px 0 15px;
    display: inline-block;
    vertical-align: top;

    &__box {
        position: absolute;
        top: 0;
        left: 4px;
        width: calc(100% - 8px);
        height: 100%;
        border: 1px solid transparentize($color_gray_lighten_2, 0.5);
    }

    &__title {
        position: absolute;
        left: 10px;
        top: -7px;
        font-family: $project_font;
        font-size: 14px;
        color: $color_gray;
        height: 14px;
        line-height: 14px;
        background-color: $color_white;
        padding: 0 5px;
    }
}
</style>
