<template>
    <InputBox class="input-string" :label="true" :dirty="isDirty" :disabled="disabled" :has-errors="hasErrors" :is-empty="!modelValue" :clearable="clearable" @clear="clear">
        <input
            class="input-string__input"
            :class="{'input-string__input-small': small}"
            :value="modelValue"
            :type="type || 'text'"
            :disabled="disabled"
            :autocomplete="autocomplete"
            :placeholder="placeholder"
            @input="update"
            ref="input"
        />
    </InputBox>
</template>

<script setup lang="ts">
import {computed, ref} from "vue";
import InputBox from "@/Components/Input/Helpers/InputBox.vue";

const props = defineProps<{
    // common props
    name?: string,
    modelValue?: string,
    original?: string,
    disabled?: boolean,
    hasErrors?: boolean,
    clearable?: boolean,
    small?: boolean,
    // string props
    type?: string,
    autocomplete?: string,
    placeholder?: string,
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | null): void,
    (e: 'change', value: string | null, name: string | undefined): void,
}>()

const input = ref<HTMLInputElement | null>(null);

const isDirty = computed((): boolean => {
    return props.original !== undefined && props.modelValue !== undefined && props.modelValue !== props.original;
});

function clear() {
    emit('update:modelValue', null);
    emit('change', null, props.name);
}

function update(event: InputEvent) {
    const target: HTMLInputElement = <HTMLInputElement>event.target;
    let value: string | null = String(target.value);
    if (value === '') {
        value = null;
    }
    emit('update:modelValue', value);
    emit('change', value, props.name);
}

function focus(): void {
    if (input.value !== null) {
        input.value.focus();
    }
}

defineExpose({
    input,
    focus,
});
</script>

<style lang="scss">
@import "@/variables";

$input_placeholder_color: #757575 !default;

.input-string {
    height: $base_size_unit + 2px;
    box-sizing: content-box;

    &__input {
        border: none !important;
        outline: none !important;
        box-sizing: border-box;
        height: $base_size_unit;
        line-height: $base_size_unit;
        font-family: $project_font;
        font-size: 16px;
        color: inherit;
        padding: 0 10px;
        flex-grow: 1;
        flex-shrink: 1;
        width: 100%;
        background-color: transparent;
        display: block;
        cursor: inherit;

        &-small {
            font-size: 14px;
        }

        &::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
            color: $input_placeholder_color;
            opacity: 1; /* Firefox */
        }

        &:-ms-input-placeholder { /* Internet Explorer 10-11 */
            color: $input_placeholder_color;
        }

        &::-ms-input-placeholder { /* Microsoft Edge */
            color: $input_placeholder_color;
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
