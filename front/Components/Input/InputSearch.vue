<template>
    <InputBox class="input-search" :label="true" :dirty="isDirty" :disabled="disabled" :has-errors="hasErrors" :is-empty="!modelValue" :clearable="clearable" @clear="clear">
        <div class="input-search__input-icon">
            <IconSearch/>
        </div>
        <input
            class="input-search__input"
            :value="modelValue"
            :disabled="disabled"
            :placeholder="placeholder ? placeholder : undefined"
            autocomplete="off"
            @input="update"
            ref="input"
        />
    </InputBox>
</template>

<script setup lang="ts">
import {computed, ref} from "vue";
import InputBox from "@/Components/Input/Helpers/InputBox.vue";
import IconSearch from "@/Icons/IconSearch.vue";
import {InputBaseProps, InputSearchProps} from "@/Components/Input/Helpers/Types";

interface Props extends InputBaseProps, InputSearchProps{
    modelValue?: string | null,
    original?: string | null,
}
const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | null): void,
    (e: 'change', value: string | null, name: string | undefined): void,
}>()

const input = ref<HTMLInputElement | undefined>(undefined);

const isDirty = computed((): boolean => {
    return props.original !== undefined && props.modelValue !== undefined && props.modelValue !== props.original;
});

function clear() {
    emit('update:modelValue', null);
    emit('change', null, props.name);
}

function update(event: Event) {
    const target: HTMLInputElement = <HTMLInputElement>event.target;
    let value: string | null = String(target.value);
    if (value === '') {
        value = null;
    }
    emit('update:modelValue', value);
    emit('change', value, props.name);
}

function focus(): void {
    if (input.value) {
        input.value.focus();
    }
}

defineExpose({
    input,
    focus,
});
</script>

<style lang="scss">
@use "sass:math";
@import "@/variables.scss";

.input-search {
    height: $base_size_unit * 4;
    box-sizing: content-box;

    &__input {
        border: none !important;
        outline: none !important;
        box-sizing: border-box;
        height: $base_size_unit * 4 - 2px;
        line-height: $base_size_unit * 4 - 2px;
        @include font(16px);
        color: inherit;
        padding: 0 0 0 8px;
        flex-grow: 1;
        flex-shrink: 1;
        width: 100%;
        background-color: transparent;
        display: block;
        cursor: inherit;

        &-icon {
            color: $color_gray_lighten_1;
            width: $base_size_unit * 2;
            height: $base_size_unit * 4;
            margin-left: 8px;
            flex-shrink: 0;

            & > svg {
                width: 100%;
                height: 100%;
            }
        }

        &::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
            color: $color_gray;
            opacity: 1; /* Firefox */
            font-size: 16px;
        }

        &:-ms-input-placeholder { /* Internet Explorer 10-11 */
            color: $color_gray;
            font-size: 16px;
        }

        &::-ms-input-placeholder { /* Microsoft Edge */
            color: $color_gray;
            font-size: 16px;
        }

        &:-webkit-autofill,
        &:-webkit-autofill:hover,
        &:-webkit-autofill:focus {
            border: none;
            -webkit-text-fill-color: inherit;
            -webkit-box-shadow: 0 0 0 1000px transparent inset;
            transition: background-color 5000s ease-in-out 0s;
        }
    }
}
</style>
