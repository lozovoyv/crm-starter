<template>
    <button class="button" :class="classProxy" @click="click">
        <slot></slot>
    </button>
</template>

<script setup lang="ts">
import {computed} from "vue";

interface Props {
    identifier?: string,
    disabled?: boolean,
    type?: 'success' | 'info' | 'error' | 'default' | 'unset',
    uppercase?: boolean,
}

const props = withDefaults(defineProps<Props>(), {
    type: 'default',
    uppercase: false,
});

const emit = defineEmits<{ (e: 'click', identifier: string | null): void }>()

const classProxy = computed((): string => {
    return (props.type !== 'unset' ? 'button__' + props.type : '') + (props.disabled ? ' button__disabled' : '') + (props.uppercase ? ' button__uppercase' : '');
});

function click(event: MouseEvent) {
    event.preventDefault();
    event.stopPropagation();
    if (!props.disabled) {
        emit('click', props.identifier ? props.identifier : null);
    }
}
</script>

<style lang="scss" scoped>
@use "sass:math";
@import "@/variables";


.button {
    background-color: $color_white;
    border-radius: math.div($base_size_unit * 4, 2);
    border: 1px solid lighten($color_text_black, 50%);
    box-sizing: border-box;
    color: $color_text_black;
    cursor: pointer;
    display: inline-block;
    font-family: $project_font;
    font-size: 16px;
    height: $base_size_unit * 4;
    letter-spacing: 0.01rem;
    line-height: $base_size_unit * 4 - 2px;
    padding: 0 $base_size_unit * 2.5;
    position: relative;
    text-align: center;
    text-decoration: none;
    text-rendering: geometricPrecision;
    transition: background-color $animation $animation_time, border-color $animation $animation_time, box-shadow $animation $animation_time;
    white-space: nowrap;
    @include no_selection;

    &:not(:last-child) {
        margin-right: 8px;
    }

    &:hover {
        box-shadow: $shadow_hover;
        background-color: $color_white;
        border-color: #c1c1c1;
    }

    &:not(&__disabled):active {
        box-shadow: none;
    }

    &__uppercase {
        text-transform: uppercase;
    }

    &__disabled {
        background-color: $color_gray_lighten_1 !important;
        border-color: $color_gray_lighten_1 !important;
        color: $color_white;
        cursor: not-allowed;
        box-shadow: unset !important;
    }


    &__success {
        background-color: $color_success;
        border-color: $color_success;
        color: $color_white;

        &:hover {
            background-color: $color_success_hover;
            border-color: $color_success_hover;
        }
    }

    &__info {
        background-color: $color_info;
        border-color: $color_info;
        color: $color_white;

        &:hover {
            background-color: $color_info_hover;
            border-color: $color_info_hover;
        }
    }

    &__error {
        background-color: $color_error;
        border-color: $color_error;
        color: $color_white;

        &:hover {
            background-color: $color_error_hover;
            border-color: $color_error_hover;
        }
    }

    &__default {
        background-color: $color_default;
        border-color: $color_default;
        color: $color_white;

        &:hover {
            background-color: $color_default_hover;
            border-color: $color_default_hover;
        }
    }
}
</style>
