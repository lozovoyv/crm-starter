<template>
    <FieldWrapper
        :disabled="disabled"
        :has-errors="hasErrors"
        :title="title"
        :required="required"
        :errors="errors"
        :hide-title="hideTitle"
        :vertical="vertical"
    >
        <InputPassword
            :name="name"
            v-model="proxyValue"
            :original="original"
            :disabled="disabled"
            :has-errors="hasErrors"
            :clearable="clearable"
            :small="small"
            :autocomplete="autocomplete"
            :placeholder="placeholder"
            @change="change"
            ref="input"
        />
    </FieldWrapper>
</template>

<script setup lang="ts">
import FieldWrapper from "./Helpers/FieldWrapper.vue";
import {computed, ref} from "vue";
import InputPassword from "../Input/InputPassword.vue";
import InputString from "../Input/InputString.vue";

const props = defineProps<{
    // common props
    name?: string,
    modelValue?: string,
    original?: string,
    disabled?: boolean,
    hasErrors?: boolean,
    clearable?: boolean,
    small?: boolean,
    // field props
    title?: string,
    required?: boolean,
    errors?: string[],
    hideTitle?: boolean,
    vertical?: boolean,
    // string props
    autocomplete?: string,
    placeholder?: string,
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | null): void,
    (e: 'change', value: string | null, name: string | undefined): void,
}>()

const input = ref<InstanceType<typeof InputPassword> | null>(null);

const proxyValue = computed({
    get: (): string | null => {
        return props.modelValue || null;
    },
    set: (value: string | null) => {
        emit('update:modelValue', value);
    }
});

function change(value: string | null, name: string | undefined): void {
    emit('change', value, name);
}

function focus(): void {
    input.value?.focus();
}

defineExpose({
    input,
    focus,
});
</script>

