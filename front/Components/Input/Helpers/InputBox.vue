<template>
    <div class="input-box">
        <label class="input-box__border" v-if="label" :class="classList">
            <slot/>
        </label>
        <div class="input-box__border" v-else :class="classList">
            <slot/>
        </div>
        <div class="input-box__clear" v-if="clearable" :class="{'input-box__clear-disabled': disabled || isEmpty}" @click="clear">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 490 490">
                <polygon fill="currentColor"
                         points="456.851,0 245,212.564 33.149,0 0.708,32.337 212.669,245.004 0.708,457.678 33.149,490 245,277.443 456.851,490 489.292,457.678 277.331,245.004 489.292,32.337 "/>
            </svg>
        </div>
    </div>
</template>

<script setup lang="ts">
import {computed} from "vue";

const props = defineProps<{
    label?: boolean,
    dirty?: boolean,
    disabled?: boolean,
    hasErrors?: boolean,
    hasFocus?: boolean,
    isEmpty?: boolean,
    clearable?: boolean,
}>();

const emit = defineEmits<{ (e: 'clear'): void }>();

const classList = computed(() => {
    let list = [];
    if (props.dirty) list.push('input-box__border-dirty');
    if (props.disabled) list.push('input-box__border-disabled');
    if (props.hasErrors) list.push('input-box__border-error');
    if (props.hasFocus) list.push('input-box__border-focus');
    return list;
});

function clear() {
    if (!props.disabled && !props.isEmpty) emit('clear');
}
</script>

<style lang="scss">
@use "sass:math";
@import "@/variables.scss";

$input_color: $color_text_black !default;
$input_hover_color: $color_default_lighten_2 !default;
$input_active_color: $color_default_lighten_1 !default;
$input_background_color: $color_white !default;
$input_error_color: $color-error !default;
$input_border_color: $color_gray_lighten_1 !default;
$input_dirty_color: transparentize($color_default_lighten_2, 0.9) !default;
$input_disabled_color: $color_gray !default;
$input_disabled_background_color: transparentize($color_gray, 0.9) !default;
$input_clear_color: $color-error !default;

.input-box {
    min-height: $base_size_unit;
    display: flex;
    box-sizing: content-box;
    width: 100%;

    &__border {
        background-color: $input_background_color;
        border-radius: 2px;
        border: 1px solid $input_border_color;
        box-sizing: content-box;
        color: $input_color;
        cursor: text;
        display: flex;
        font-family: $project_font;
        position: relative;
        transition: border-color $animation $animation_time;
        width: 100%;
        flex-grow: 1;

        &:not(&-disabled):hover {
            border-color: $input_hover_color;
        }

        &:not(&-disabled):focus-within, &-focus:not(&-disabled) {
            border-color: $input_active_color !important;
        }

        &-dirty {
            background-color: $input_dirty_color;
        }

        &-error {
            border-color: $input_error_color;
        }

        &-disabled {
            background-color: $input_disabled_background_color;
            color: $input_disabled_color;
            cursor: not-allowed;
        }
    }

    &__clear {
        color: $input_disabled_color;
        width: $base_size_unit;
        height: 100%;
        position: relative;
        flex-grow: 0;
        flex-shrink: 0;

        & > svg {
            position: absolute;
            top: math.div($base_size_unit, 2);
            left: 50%;
            width: math.div($base_size_unit, 2);
            height: math.div($base_size_unit, 2);
            transform: translate(-50%, -50%);
        }

        &:not(&-disabled) {
            color: $input_clear_color;
            cursor: pointer;

            & > svg {
                transition: transform $animation_time $animation_bounce;
            }

            &:hover > svg {
                transform: translate(-50%, -50%) scale(1.2);
            }
        }
    }
}
</style>
