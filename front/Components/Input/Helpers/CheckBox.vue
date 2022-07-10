<template>
    <label class="checkbox" :class="{'checkbox__disabled': disabled, 'checkbox__error': !valid}">
        <input class="checkbox__input" type="checkbox"
               v-model="proxyValue"
               :value="value"
               :disabled="disabled"
        >
        <span class="checkbox__check">
            <IconCheck class="checkbox__check-checked"/>
        </span>
        <span class="checkbox__label" v-if="!label" :class="{'checkbox__label-small': small}"><slot/></span>
        <span class="checkbox__label" v-else :class="{'checkbox__label-small': small}">{{ label }}</span>
    </label>
</template>

<script>
import IconCheck from "../../../../front_temp/Components/Icons/IconCheck";

export default {
    components: {IconCheck},
    props: {
        modelValue: {type: [String, Number, Boolean, Array], default: null},
        label: {type: String, default: null},
        value: {type: [String, Number, Boolean], default: null},
        valid: {type: Boolean, default: true},
        disabled: {type: Boolean, default: false},
        small: {type: Boolean, default: false},
    },

    emits: ['update:modelValue'],

    computed: {
        proxyValue: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            }
        },
    }
}
</script>

<style lang="scss">
@import "@/variables";

$input_color: #1e1e1e !default;
$input_disabled_color: #626262 !default;
$input_active_color: $color-default !default;
$input_background_color: $color-white !default;
$input_placeholder_color: #757575;
$input_error_color: $color-error !default;

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
        border: 1px solid $input_placeholder_color;
        border-radius: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;

        &-checked {
            color: inherit;
            display: none;
            width: 80%;
            height: 80%;
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
        border-color: $input_active_color;
        background-color: $input_active_color;
        color: $input_background_color;
    }

    &__input:checked + &__check > &__check-checked {
        display: block;
    }

    &__label {
        margin: 0 7px 0 7px;
        font-size: 16px;
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
