<template>
    <input
        class="input-mask__input"
        :disabled="disabled"
        :placeholder="placeholder"
        ref="input"
    />
</template>

<script setup lang="ts">
import {onMounted, ref, watch} from "vue";
import IMask from "imask";
import InputMask = IMask.InputMask;
import AnyMaskedOptions = IMask.AnyMaskedOptions;

const props = defineProps<{
    // common props
    name?: string,
    modelValue?: string | null,
    disabled?: boolean,
    // mask props
    mask?: AnyMaskedOptions,
    placeholder?: string,
    masked?: boolean,
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | null): void,
    (e: 'change', value: string | null, name: string | undefined): void,
}>()

const input = ref<HTMLInputElement | undefined>(undefined);
const mask = ref<InputMask<any> | null>(null);
const value = ref<string | null>(null);

watch(() => props.modelValue, () => {
    if (mask.value) {
        mask.value.value = props.modelValue ? props.modelValue : '';
    }
});

function update(value: string) {
    emit('update:modelValue', value === '' ? null : value);
    emit('change', value === '' ? null : value, props.name);
}

function focus(): void {
    input.value?.focus();
}

onMounted((): void => {
    if (input.value) {
        mask.value = IMask(input.value, props.mask ? props.mask : {mask: /.*/})
            .on('accept', onAccept)
            .on('complete', onComplete);
        mask.value.value = props.modelValue ? props.modelValue : '';
    }
})

function onUnmounted(): void {
    if (mask.value) {
        mask.value.destroy();
        mask.value = null;
    }
}

function onAccept(): void {
    if (mask.value) {
        update(props.masked ? mask.value.value : mask.value.unmaskedValue);
    }
}

function onComplete(): void {
    if (mask.value) {
        update(props.masked ? mask.value.value : mask.value.unmaskedValue);
    }
}

defineExpose({
    input,
    focus,
});
</script>


<style lang="scss">
@import "@/variables.scss";

.input-mask {
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
