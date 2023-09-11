<template>
    <InputBox class="input-text" :label="true" :dirty="isDirty" :disabled="disabled" :has-errors="hasErrors" :is-empty="!modelValue" :clearable="clearable" @clear="clear">
        <textarea
            class="input-text__input"
            :value="modelValue ? modelValue : ''"
            :disabled="disabled"
            :placeholder="placeholder ? placeholder : undefined"
            @input="update"
            ref="input"
        />
    </InputBox>
</template>

<script setup lang="ts">
import {computed, ref} from "vue";
import InputBox from "@/Components/Input/Helpers/InputBox.vue";
import {InputBaseProps, InputTextProps} from "@/Components/Input/Helpers/Types";

interface Props extends InputBaseProps, InputTextProps {
    modelValue: string | null,
    original?: string | null,
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | null): void,
    (e: 'change', value: string | null, name: string | undefined): void,
}>()

const input = ref<HTMLTextAreaElement | undefined>(undefined);

const isDirty = computed((): boolean => {
    return props.original !== undefined && props.modelValue !== props.original;
});

function clear() {
    emit('update:modelValue', null);
    emit('change', null, props.name);
}

function update(event: Event) {
    const target: HTMLTextAreaElement = <HTMLTextAreaElement>event.target;
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

.input-text {
    min-height: $base_size_unit + 2px;
    box-sizing: content-box;

    &__input {
        border: none !important;
        outline: none !important;
        box-sizing: border-box;
        min-height: $base_size_unit;
        line-height: 18px;
        font-family: $project_font;
        font-size: 16px;
        color: inherit;
        padding: 8px 10px;
        flex-grow: 1;
        flex-shrink: 1;
        width: 100%;
        background-color: transparent;
        display: block;
        cursor: inherit;
        resize: vertical;

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
        }
    }
}
</style>
