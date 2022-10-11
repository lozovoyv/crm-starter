<template>
    <InputBox class="input-password" :label="true" :dirty="isDirty" :disabled="disabled" :has-errors="hasErrors" :is-empty="!modelValue" :clearable="clearable" @clear="clear">
        <input
            class="input-password__input"
            :value="modelValue"
            :type="show ? 'text' : 'password'"
            :disabled="disabled"
            :autocomplete="autocomplete"
            :placeholder="placeholder"
            @input="update"
            ref="input"
        />
        <div class="input-password__button" v-if="!disabled" @click="show = !show && !disabled" :title="show ? 'Скрыть' : 'Показать'">
            <IconEyeSlash v-if="show"/>
            <IconEye v-else/>
        </div>
    </InputBox>
</template>

<script setup lang="ts">
import {computed, ref} from "vue";
import InputBox from "@/Components/Input/Helpers/InputBox.vue";
import IconEye from "@/Icons/IconEye.vue";
import IconEyeSlash from "@/Icons/IconEyeSlash.vue";

const props = defineProps<{
    // common props
    name?: string,
    modelValue?: string,
    original?: string,
    disabled?: boolean,
    hasErrors?: boolean,
    clearable?: boolean,
    // password props
    autocomplete?: string,
    placeholder?: string,
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | null): void,
    (e: 'change', value: string | null, name: string | undefined): void,
}>()

const show = ref<boolean>(false);
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
@import "@/variables";

.input-password {
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

    &__button {
        width: $base_size_unit;
        height: 100%;
        position: relative;
        cursor: pointer;
        color: $color_gray;
        flex-grow: 0;
        flex-shrink: 0;

        &:hover {
            color: $color_default_lighten_1;
        }

        & > svg {
            position: absolute;
            top: 50%;
            left: 50%;
            width: math.div($base_size_unit, 1.6);
            height: math.div($base_size_unit, 1.6);
            transform: translate(-50%, -50%);
        }
    }
}
</style>
