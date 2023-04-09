<template>
    <InputBox class="input-phone" :label="true" :dirty="isDirty" :disabled="disabled" :has-errors="hasErrors" :is-empty="!modelValue" :clearable="clearable" @clear="clear">
        <InputMask
            class="input-phone__input"
            type="text"
            :model-value="modelValue"
            :disabled="disabled"
            :autocomplete="autocomplete"
            :placeholder="placeholder ? placeholder : undefined"
            @change="update"
            ref="input"

            :mask="{
                mask: '+{7}(000) 000-00-00',
                lazy: true,
                eager: true,
            }"
        />
    </InputBox>
</template>

<script setup lang="ts">
import {computed, ref} from "vue";
import InputBox from "@/Components/Input/Helpers/InputBox.vue";
import InputMask from "@/Components/Input/Helpers/InputMask.vue";

const props = defineProps<{
    // common props
    name?: string,
    modelValue?: string | null,
    original?: string | null,
    disabled?: boolean,
    hasErrors?: boolean,
    clearable?: boolean,
    // string props
    type?: string,
    autocomplete?: string,
    placeholder?: string | null,
}>();

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

function update(value: string | null) {
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

.input-phone {
    height: $base_size_unit + 2px;
    box-sizing: content-box;
    position: relative;

    &__input, &__placeholder {
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
        opacity: 0.99;
        z-index: 2;

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

    &__placeholder {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
        color: $color_gray;
    }
}
</style>
