<template>
    <FieldPassword
        :name="name"
        :model-value="modelValue"
        :original="original"
        :disabled="disabled"
        :has-errors="!valid"
        :clearable="clearable"
        :title="title"
        :required="required"
        :errors="errors"
        :hide-title="hideTitle"
        :empty-title="emptyTitle"
        :vertical="vertical"
        :autocomplete="autocomplete"
        :placeholder="title"
        @change="change"
        ref="input"
    />
</template>

<script setup lang="ts">
import {Form} from "@/Core/Form";
import {computed, ref} from "vue";
import {getErrors, getOriginal, getTitle, getValue, isRequired, isValid} from "./utils";
import FieldPassword from "@/Components/Fields/FieldPassword.vue";

const props = defineProps<{
    // common props
    name: string,
    disabled?: boolean,
    clearable?: boolean,
    // field props
    hideTitle?: boolean,
    emptyTitle?: boolean,
    vertical?: boolean,
    // form props
    form: Form,
    defaultValue?: any,
    // string props
    autocomplete?: string | 'off',
}>();

const emit = defineEmits<{ (e: 'change', value: string | null, name: string, payload: any): void }>()

const input = ref<InstanceType<typeof FieldPassword> | undefined>(undefined);

const title = computed(() => {
    return getTitle(props.form, props.name);
});
const modelValue = computed((): string | null => {
    return getValue(props.form, props.name, null);
});
const original = computed((): string | null => {
    return getOriginal(props.form, props.name, props.defaultValue);
});
const valid = computed((): boolean => {
    return isValid(props.form, props.name);
});
const errors = computed((): string[] => {
    return getErrors(props.form, props.name);
});

const required = computed((): boolean => {
    return isRequired(props.form, props.name)
});

function change(value: string | null, name: string | undefined) {
    if(name) {
        props.form.update(name, value);
        emit('change', value, name, null);
    }
}

function focus(): void {
    input.value?.focus();
}

defineExpose({
    input,
    focus,
})
</script>
