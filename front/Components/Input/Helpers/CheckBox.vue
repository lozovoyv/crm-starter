<template>
    <label class="checkbox" :class="{'checkbox__disabled': disabled, 'checkbox__error': hasErrors}">
        <input class="checkbox__input" type="checkbox"
               v-model="proxyValue"
               :value="value"
               :disabled="disabled"
               ref="input"
        >
        <span class="checkbox__check" :class="{'checkbox__check-dirty': dirty}">
            <IconCheck class="checkbox__check-checked"/>
        </span>
        <span class="checkbox__label" v-if="!label"><slot/></span>
        <span class="checkbox__label" v-else>{{ label }}</span>
    </label>
</template>

<script setup lang="ts">
import IconCheck from "@/Icons/IconCheck.vue";
import {computed, ref} from "vue";

const props = defineProps<{
    modelValue?: boolean | number | string | Array<number | string>,
    value?: number | string,
    dirty?: boolean,
    disabled?: boolean,
    hasErrors?: boolean,
    label?: string | null,
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean | number | string | Array<number | string>): void,
}>();

const input = ref<HTMLInputElement | undefined>(undefined);

const proxyValue = computed({
    get: (): boolean | number | string | Array<number | string> => {
        return props.modelValue || false;
    },
    set: (value: boolean | number | string | Array<number | string>) => {
        emit('update:modelValue', value);
    }
});

function focus(): void {
    input.value?.focus();
}

defineExpose({
    input,
    focus,
});
</script>

<style lang="scss">
@import "@/variables";

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
        border: 1px solid $color_gray_lighten_1;
        border-radius: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;

        &:hover {
            border-color: $color_default_lighten_2;
        }

        &-checked {
            color: inherit;
            display: none;
            width: 75%;
            height: 75%;
        }

        &-dirty {
            background-color: transparentize($color_default_lighten_2, 0.96);
        }
    }

    &__error:not(&__disabled) &__check {
        border-color: $color_error !important;
    }

    &__disabled &__check {
        border-color: transparentize($color_gray, 0.5) !important;
        color: $color_gray !important;
        background-color: transparentize($color_gray, 0.75) !important;
    }

    &__input:checked + &__check {
        color: $color_default;
    }

    &__input:checked + &__check > &__check-checked {
        display: block;
    }

    &__label {
        margin: 0 7px 0 7px;
        @include font(16px);
        display: inline-block;
        color: $color_text_black;
        position: relative;
        @include no_selection;
    }

    &__disabled &__label {
        color: $color_gray;
    }
}
</style>
