<template>
    <InputBox class="input-password" :label="true" :dirty="isDirty" :disabled="disabled" :has-errors="hasErrors" :is-empty="!modelValue" :clearable="clearable" @clear="clear">
        <input
            class="input-password__input"
            :value="modelValue"
            :type="show ? 'text' : 'password'"
            :disabled="disabled"
            :autocomplete="autocomplete"
            :placeholder="placeholder ? placeholder : undefined"
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
import {InputBaseProps, InputPasswordProps} from "@/Components/Input/Helpers/Types";

interface Props extends InputBaseProps, InputPasswordProps {
    modelValue?: string | null,
    original?: string | null,
}
const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | null): void,
    (e: 'change', value: string | null, name: string | undefined): void,
}>()

const show = ref<boolean>(false);
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
@import "@/variables";

.input-password {
    height: $base_size_unit * 4;
    box-sizing: border-box;

    &__input {
        border: none !important;
        outline: none !important;
        box-sizing: border-box;
        height: $base_size_unit * 4 - 2px;
        line-height: $base_size_unit * 4 - 2px;
        @include font(16px);
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
        width: $base_size_unit * 4;
        height: 100%;
        position: relative;
        cursor: pointer;
        color: $color_gray;
        flex-grow: 0;
        flex-shrink: 0;

        &:hover {
            color: $color_default_hover;
        }

        & > svg {
            position: absolute;
            top: 50%;
            left: 50%;
            width: $base_size_unit * 2;
            height: $base_size_unit * 2;
            transform: translate(-50%, -50%);
        }

        &:before {
            content: '';
            position: absolute;
            left: 0;
            top: 20%;
            width: 1px;
            height: 60%;
            background-color: $color_gray_lighten_2;
        }
    }
}
</style>
