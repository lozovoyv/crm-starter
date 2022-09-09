<template>
    <FieldCheckBox
        :name="name"
        :model-value="modelValue"
        :original="original"
        :disabled="disabled"
        :has-errors="!valid"
        :clearable="clearable"
        :small="small"
        :title="label ? title : ''"
        :required="required"
        :errors="errors"
        :hide-title="hideTitle"
        :vertical="vertical"
        :placeholder="title"
        :label="label ? label : title"
        :value="value"
        @change="change"
    />
</template>

<script setup lang="ts">
import {Form} from "@/Core/Form";
import {computed} from "vue";
import {getErrors, getOriginal, getTitle, getValue, isRequired, isValid} from "./utils";
import FieldCheckBox from "@/Components/Fields/FieldCheckBox.vue";

const props = defineProps<{
    // common props
    name: string,
    disabled?: boolean,
    clearable?: boolean,
    small?: boolean,
    // field props
    hideTitle?: boolean,
    vertical?: boolean,
    // form props
    form: Form,
    defaultValue?: any,
    // string props
    label?: string,
    value?: number | string,
}>();

const emit = defineEmits<{ (e: 'change', value: string | null, name: string, payload: any): void }>()

const title = computed(() => {
    return getTitle(props.form, props.name);
});
const modelValue = computed((): boolean | number | string | Array<number | string> => {
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

function change(value: any, name: string, payload: any = null) {
    props.form.update(name, value);
    emit('change', value, name, payload);
}
</script>
