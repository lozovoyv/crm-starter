<template>
    <button class="button" :class="classProxy" @click="click">
        <slot></slot>
    </button>
</template>

<script setup lang="ts">
import {computed} from "vue";

const props = defineProps<{
    identifier?: 'success' | 'info' | 'error' | 'default',
    disabled?: boolean,
    type?: string,
}>();

const emit = defineEmits<{ (e: 'clicked', identifier: string | null): void }>()

const classProxy = computed((): string => {
    return (props.type ? 'button__' + props.type : '') + (props.disabled ? ' button__disabled' : '');
});

function click() {
    if (!props.disabled) {
        emit('clicked', props.identifier ? props.identifier : null);
    }
}
</script>

<style lang="scss" scoped>
@use "sass:math";
@import "@/variables";

$button_default_color: $color-white !default;
$button_default_hover_color: $color-white !default;
$button_default_text_color: $color-text-black !default;
$button_primary_color: $color-default-darken-1 !default;
$button_primary_hover_color: lighten($color-default-darken-1, 5%) !default;
$button_primary_text_color: $color-white !default;
$button_success_color: $color-success-darken-1 !default;
$button_success_hover_color: lighten($color-success-darken-1, 5%) !default;
$button_success_text_color: $color-white !default;
$button_info_color: $color-info-darken-1 !default;
$button_info_hover_color: lighten($color-info-darken-1, 5%) !default;
$button_info_text_color: $color-white !default;
$button_error_color: $color-error-darken-1 !default;
$button_error_hover_color: lighten($color-error-darken-1, 5%) !default;
$button_error_text_color: $color-white !default;
$button_disabled_color: $color-gray-darken-1 !default;
$button_disabled_text_color: $color-white !default;

.button {
    text-rendering: geometricPrecision;
    display: inline-block;
    text-decoration: none;
    height: $base_size_unit;
    line-height: $base_size_unit;
    text-align: center;
    cursor: pointer;
    border-radius: 2px;
    padding: 0 math.div($base_size_unit, 1.5);
    letter-spacing: 0.03rem;
    color: $button_default_text_color;
    border: 1px solid #efefef;
    background-color: $button_default_color;
    transition: background-color $animation $animation_time, border-color $animation $animation_time, box-shadow $animation $animation_time;
    font-family: $project_font;
    font-size: 16px;
    box-shadow: $shadow_1;
    box-sizing: content-box;
    white-space: nowrap;
    @include no_selection;

    &:not(:last-child) {
        margin-right: 15px;
    }

    &:hover {
        box-shadow: $shadow_hover;
        background-color: $button_default_hover_color;
        border-color: #c1c1c1;
    }

    &:not(&__disabled):active {
        box-shadow: none;
    }

    &__disabled {
        background-color: $button_disabled_color !important;
        border-color: $button_disabled_color !important;
        cursor: not-allowed;
        box-shadow: $shadow_1 !important;
        color: $button_disabled_text_color;

        &:hover {
        }
    }

    &:hover {
        box-shadow: $shadow_hover;
        background-color: $button_default_hover_color;
        border-color: #efefef;
    }

    &__success {
        background-color: $button_success_color;
        border-color: $button_success_color;
        color: $button_success_text_color;

        &:hover {
            background-color: $button_success_hover_color;
            border-color: $button_success_hover_color;
        }
    }

    &__info {
        background-color: $button_info_color;
        border-color: $button_info_color;
        color: $button_info_text_color;

        &:hover {
            background-color: $button_info_hover_color;
            border-color: $button_info_hover_color;
        }
    }

    &__error {
        background-color: $button_error_color;
        border-color: $button_error_color;
        color: $button_error_text_color;

        &:hover {
            background-color: $button_error_hover_color;
            border-color: $button_error_hover_color;
        }
    }

    &__default {
        background-color: $button_primary_color;
        border-color: $button_primary_color;
        color: $button_primary_text_color;

        &:hover {
            background-color: $button_primary_hover_color;
            border-color: $button_primary_hover_color;
        }
    }
}
</style>
