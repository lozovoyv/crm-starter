<template>
    <InputBox class="input-search" :label="true" :dirty="isDirty" :disabled="disabled" :has-errors="hasErrors" :is-empty="!modelValue" :clearable="clearable" @clear="clear">
        <div class="input-search__input-icon">
            <IconSearch/>
        </div>
        <input
            class="input-search__input"
            :value="modelValue"
            :disabled="disabled"
            :placeholder="placeholder"
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

const props = defineProps<{
    // common props
    name?: string,
    modelValue?: string,
    original?: string,
    disabled?: boolean,
    hasErrors?: boolean,
    clearable?: boolean,
    // string props
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
@use "sass:math";
@import "@/variables.scss";

.input-search {
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

        &-icon {
            color: $color_gray_lighten_1;
            width: math.div($base_size_unit, 1.7);
            height: $base_size_unit;
            margin-left: math.div($base_size_unit, 5);
            flex-shrink: 0;

            & > svg {
                width: 100%;
                height: 100%;
            }
        }

        &::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
            color: $color_gray;
            opacity: 1; /* Firefox */
            font-size: 14px;
        }

        &:-ms-input-placeholder { /* Internet Explorer 10-11 */
            color: $color_gray;
            font-size: 14px;
        }

        &::-ms-input-placeholder { /* Microsoft Edge */
            color: $color_gray;
            font-size: 14px;
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
