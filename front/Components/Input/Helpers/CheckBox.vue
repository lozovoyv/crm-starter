<template>
    <label class="checkbox" :class="{'checkbox__disabled': disabled, 'checkbox__error': hasErrors}">
        <input class="checkbox__input" type="checkbox"
               v-model="proxyValue"
               :value="value"
               :disabled="disabled"
        >
        <span class="checkbox__check" :class="{'checkbox__check-dirty': dirty}">
            <IconCheck class="checkbox__check-checked"/>
        </span>
        <span class="checkbox__label" v-if="!label" :class="{'checkbox__label-small': small}"><slot/></span>
        <span class="checkbox__label" v-else :class="{'checkbox__label-small': small}">{{ label }}</span>
    </label>
</template>

<script setup lang="ts">
import IconCheck from "@/Icons/IconCheck.vue";
import {computed} from "vue";

const props = defineProps<{
    modelValue?: boolean | number | string | Array<number | string>,
    value?: number | string,
    dirty?: boolean,
    disabled?: boolean,
    hasErrors?: boolean,
    small?: boolean,
    label?: string,
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean | number | string | Array<number | string>): void,
}>();

const proxyValue = computed({
    get: (): boolean | number | string | Array<number | string> => {
        return props.modelValue || false;
    },
    set: (value: boolean | number | string | Array<number | string>) => {
        emit('update:modelValue', value);
    }
});
</script>

<style lang="scss">
@import "@/variables";

$input_color: $color_text_black !default;
$input_disabled_color: $color_gray !default;
$input_active_color: $color_default !default;
$input_background_color: $color_white !default;
$input_hover_color: $color_default_lighten_2 !default;
$input_error_color: $color-error !default;
$input_border_color: $color_gray_lighten_1 !default;
$input_dirty_color: transparentize($color_default_lighten_2, 0.9) !default;

.checkbox {
    height: 100%;
    display: flex;
    align-items: center;
    cursor: pointer;
    position: relative;

    &__disabled {
        cursor: not-allowed;
    }

    &__input {
        visibility: hidden;
        opacity: 0;
        position: absolute;
        width: 0;
        height: 0;
    }

    &__check {
        width: 16px;
        height: 16px;
        border: 1px solid $input_border_color;
        border-radius: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;

        &:hover {
            border-color: $input_hover_color;
        }

        &-checked {
            color: inherit;
            display: none;
            width: 75%;
            height: 75%;
        }

        &-dirty {
            background-color: $input_dirty_color;
        }
    }

    &__error:not(&__disabled) &__check {
        border-color: $input_error_color !important;
    }

    &__disabled &__check {
        border-color: transparentize($input_disabled_color, 0.5) !important;
        color: $input_disabled_color !important;
        background-color: transparentize($input_disabled_color, 0.75) !important;
    }

    &__input:checked + &__check {
        color: $input_active_color;
    }

    &__input:checked + &__check > &__check-checked {
        display: block;
    }

    &__label {
        margin: 0 7px 0 7px;
        font-size: 15px;
        font-family: $project_font;
        display: inline-block;
        color: $input_color;
        position: relative;
        @include no_selection;

        &-small {
            font-size: 14px;
        }
    }

    &__disabled &__label {
        color: $input_disabled_color;
    }
}
</style>
