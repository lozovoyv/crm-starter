<template>
    <InputBox class="input-string" :label="true" :dirty="isDirty" :disabled="disabled" :has-errors="hasErrors" :is-empty="!modelValue" :clearable="clearable" @clear="clear">
        <input
            class="input-string__input"
            :value="modelValue"
            :type="type || 'text'"
            :disabled="disabled"
            :autocomplete="autocomplete"
            :placeholder="placeholder ? placeholder : undefined"
            @input="update"
            ref="input"
        />
    </InputBox>
</template>

<script setup lang="ts">
import {computed, ref} from "vue";
import InputBox from "@/Components/Input/Helpers/InputBox.vue";
import {InputCommonProps, InputStringCustomizableProps} from "@/Components/Input/Helpers/Types";

interface InputStringProps extends InputCommonProps, InputStringCustomizableProps {
    modelValue?: string | null,
    original?: string | null,
}
const props = defineProps<InputStringProps>();

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
@import "@/variables.scss";

.input-string {
    height: $base_size_unit * 4;
    box-sizing: border-box;

    &__input {
        border: none !important;
        outline: none !important;
        box-sizing: border-box;
        height: $base_size_unit * 4 - 2px;
        line-height: $base_size_unit * 4 - 2px;
        font-family: $project_font;
        font-size: 16px;
        color: inherit;
        padding: 0 0 0 8px;
        flex-grow: 1;
        flex-shrink: 1;
        width: 100%;
        background-color: transparent;
        display: block;
        cursor: inherit;

        &::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
            color: $color_gray;
            opacity: 1; /* Firefox */
        }

        &:-ms-input-placeholder { /* Internet Explorer 10-11 */
            color: $color_gray;
        }

        &::-ms-input-placeholder { /* Microsoft Edge */
            color: $color_gray;
        }

        &:-webkit-autofill,
        &:-webkit-autofill:hover,
        &:-webkit-autofill:focus {
            border: none;
            -webkit-text-fill-color: inherit;
            -webkit-box-shadow: 0 0 0 1000px transparent inset;
            transition: background-color 5000s ease-in-out 0s;
            font-size: 16px;
        }
    }
}
</style>
