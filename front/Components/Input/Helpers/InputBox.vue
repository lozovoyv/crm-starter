<template>
    <div class="input-box">
        <label class="input-box__border" v-if="label" :class="classList">
            <slot/>
            <span class="input-box__additional" v-if="$slots.additional">
                <slot name="additional"/>
            </span>
            <span class="input-box__clear" v-if="clearable && !disabled" :class="{'input-box__clear-disabled': disabled || isEmpty}" @click="clear">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 490 490">
                    <polygon fill="currentColor"
                             points="456.851,0 245,212.564 33.149,0 0.708,32.337 212.669,245.004 0.708,457.678 33.149,490 245,277.443 456.851,490 489.292,457.678 277.331,245.004 489.292,32.337 "/>
                </svg>
            </span>
        </label>
        <div class="input-box__border" v-else :class="classList">
            <slot/>
            <span class="input-box__additional" v-if="$slots.additional">
                <slot name="additional"/>
            </span>
            <span class="input-box__clear" v-if="clearable && !disabled" :class="{'input-box__clear-disabled': disabled || isEmpty}" @click="clear">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 490 490">
                    <polygon fill="currentColor"
                             points="456.851,0 245,212.564 33.149,0 0.708,32.337 212.669,245.004 0.708,457.678 33.149,490 245,277.443 456.851,490 489.292,457.678 277.331,245.004 489.292,32.337 "/>
                </svg>
            </span>
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

.input-box {
    min-height: $base_size_unit * 4;
    display: flex;
    box-sizing: border-box;
    width: 100%;
    cursor: text;

    &__border {
        background-color: $color_white;
        border-radius: 4px;
        border: 1px solid $color_gray_lighten_1;
        box-sizing: content-box;
        color: $color_text_black;
        display: flex;
        font-family: $project_font;
        position: relative;
        cursor: inherit;
        transition: border-color $animation $animation_time;
        width: 100%;
        flex-grow: 1;

        &-dirty:not(&-disabled) {
            background-color: transparentize($color_default_lighten_2, 0.96);
        }

        &:not(&-disabled):hover {
            border-color: $color_default_hover;
        }

        &:not(&-disabled):focus-within, &-focus:not(&-disabled) {
            border-color: $color_default !important;
        }

        &-error {
            border-color: $color_error;
        }

        &-disabled {
            background-color: transparentize($color_gray, 0.9);
            color: $color_gray;
            cursor: not-allowed;
        }
    }

    &__clear {
        color: $color_gray;
        width: $base_size_unit * 4;
        height: 100%;
        position: relative;
        flex-grow: 0;
        flex-shrink: 0;

        & > svg {
            position: absolute;
            top: 50%;
            left: 50%;
            width: $base_size_unit * 1.5;
            height: $base_size_unit * 1.5;
            transform: translate(-50%, -50%);
        }

        &:not(&-disabled) {
            color: $color_error;
            cursor: pointer;

            & > svg {
                transition: transform $animation_time $animation_bounce;
            }

            &:hover > svg {
                transform: translate(-50%, -50%) scale(1.2);
            }
        }

        &:before {
            content: '';
            position: absolute;
            left: 0;
            top: 20%;
            width: 1px;
            height: 60%;
            background-color: $color_gray_lighten_2;
        }
    }

    &__additional {
        height: 100%;
        position: relative;
        flex-grow: 0;
        flex-shrink: 0;
        display: flex;
        align-items: center;
    }
}
</style>
