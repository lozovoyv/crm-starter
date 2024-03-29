<template>
    <div class="actions-menu">
        <div class="actions-menu__button" @click="toggle" :class="{'actions-menu__button-active':dropped}">
            <IconBars v-if="right"/>
            <span class="actions-menu__button-title" :class="{'actions-menu__button-title-right': right}" v-if="title">{{ title }}</span>
            <IconBars v-if="!right"/>
        </div>
        <div class="actions-menu__actions" :class="{'actions-menu__actions-shown': dropped, 'actions-menu__actions-top': top, 'actions-menu__actions-right': right}">
            <slot/>
        </div>
    </div>
</template>

<script setup lang="ts">
import IconBars from "@/Icons/IconBars.vue";
import {ref} from "vue";

const emit = defineEmits<{ (e: 'dropped'): void }>();

const props = defineProps<{
    title?: string,
    top?: boolean,
    right?: boolean,
}>();

const dropped = ref<boolean>(false);

function toggle() {
    if (dropped.value === true) {
        dropped.value = false;
        setTimeout(() => {
            window.removeEventListener('click', close);
        }, 100);
    } else {
        dropped.value = true;
        emit('dropped');
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

.actions-menu {
    display: inline-block;
    flex-grow: 0;
    flex-shrink: 0;
    height: $base_size_unit * 4;
    position: relative;
    text-align: left;

    &__button {
        border: 1px solid transparentize($color_default, 0.75);
        box-sizing: border-box;
        border-radius: $base_size_unit * 2;
        padding: 0 14px;
        display: inline-flex;
        flex-direction: row;
        align-items: center;
        @include font(16px);
        color: $color_default;
        cursor: pointer;
        height: $base_size_unit * 4;
        background-color: transparent;
        transition: color $animation $animation_time, background-color $animation $animation_time, border-color $animation $animation_time;

        &:hover, &-active {
            color: $color_white;
            border-color: $color_default_hover;
            background-color: $color_default_hover;
        }

        &-title {
            margin: 0 8px 0 0;
            display: flex;
            align-items: center;
            position: relative;
            line-height: $base_size_unit * 4 - 2px;
            top: -1px;
            @media screen and (max-width: 450px) {
                display: none;
            }

            &-right {
                margin: 0 0 0 8px;
            }
        }

        & > svg {
            width: 15px;
            height: 100%;
        }
    }

    &__actions {
        position: absolute;
        right: 6px;
        top: $base_size_unit * 4 + 5px;
        box-sizing: border-box;
        padding: 12px 20px;
        border-radius: math.div($base_size_unit, 2);
        min-width: 100%;
        z-index: 50;
        background-color: $color_white;
        box-shadow: $shadow_1;
        border: 1px solid $color_gray_lighten_1;
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
            background-color: inherit;
            width: 6px;
            height: 6px;
            position: absolute;
            right: 12px;
            top: -4px;
            transform: rotate(45deg);
            border-color: inherit;
            border-style: solid;
            border-width: 1px 0 0 1px;
        }

        &-top {
            top: unset;
            bottom: $base_size_unit + 6px;
        }

        &-top:before {
            top: unset;
            bottom: -4px;
            border-width: 0 1px 1px 0;
        }

        &-right {
            right: unset;
            left: 0;
        }

        &-right:before {
            right: unset;
            left: 12px;
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
