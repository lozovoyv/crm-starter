<template>
    <div class="list-actions-menu">
        <div class="list-actions-menu__button" @click="toggle" :class="{'list-actions-menu__button-active':dropped}">
            <IconEllipsisH/>
        </div>
        <div class="list-actions-menu__actions" :class="{'list-actions-menu__actions-shown': dropped}">
            <slot/>
        </div>
    </div>
</template>

<script setup lang="ts">
import {ref} from "vue";
import IconEllipsisH from "@/Icons/IconEllipsisH.vue";

const dropped = ref<boolean>(false);

function toggle() {
    if (dropped.value === true) {
        dropped.value = false;
        setTimeout(() => {
            window.removeEventListener('click', close);
        }, 100);
    } else {
        dropped.value = true;
        setTimeout(() => {
            window.addEventListener('click', close);
        }, 100);
    }
}

function close() {
    window.removeEventListener('click', close);
    dropped.value = false;
}
</script>

<style lang="scss">
@use "sass:math";
@import "@/variables.scss";

.list-actions-menu {
    display: inline-block;
    flex-grow: 0;
    flex-shrink: 0;
    height: 16px;
    line-height: 16px;
    box-sizing: border-box;
    position: relative;
    text-align: left;

    &__button {
        border: 1px solid transparent;
        box-sizing: content-box;
        border-radius: 50%;
        padding: 3px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-family: $project_font;
        color: $color_gray_darken_2;
        font-size: 15px;
        cursor: pointer;
        width: 16px;
        height: 16px;
        background-color: transparent;
        transition: color $animation $animation_time, background-color $animation $animation_time, border-color $animation $animation_time;

        &:hover, &-active {
            color: $color_white;
            border-color: $color_default_lighten_1;
            background-color: $color_default_lighten_1;
        }

        & > svg {
            width: 15px;
            height: 100%;
        }
    }

    &__actions {
        position: absolute;
        left: -8px;
        top: 0;
        transform: translate(-100%, 0);
        box-sizing: border-box;
        padding: 12px 20px;
        border-radius: 2px;
        min-width: 100%;
        z-index: 9;
        background-color: $color_white;
        box-shadow: $shadow_1;
        display: flex;
        flex-direction: column;
        line-height: 24px;
        opacity: 0;
        visibility: hidden;
        transition: opacity $animation $animation_time, visibility $animation $animation_time;
        font-size: 15px;

        &:before {
            content: '';
            display: block;
            background-color: $color_white;
            width: 6px;
            height: 6px;
            position: absolute;
            right: -4px;
            top: 8px;
            transform: rotate(45deg);
            border-color: #e9e9e9;
            border-style: solid;
            border-width: 1px 1px 0 0;
        }

        &-shown {
            opacity: 1;
            visibility: visible;
        }

        & > * {
            white-space: nowrap;
        }

        & > hr {
            width: 100%;
            border: none;
            border-top: 1px solid #e9e9e9;
        }
    }
}
</style>
