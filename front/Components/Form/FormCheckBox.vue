<template>
    <FormFieldWrapper
        :title="title"
        :has-errors="!valid"
        :errors="errors"
        :required="required"
        :hide-title="hideTitle"
        :without-title="withoutTitle"
        :vertical="vertical"
    >
        <InputCheckbox
            :model-value="modelValue"
            :original="original"
            :name="name"
            :value="value"
            :disabled="disabled"
            :has-errors="!valid"
            :placeholder="placeholderProxy"
            @change="change"
            ref="input"
        >
            <slot/>
        </InputCheckbox>
    </FormFieldWrapper>
</template>

<script setup lang="ts">
import {Form} from "@/Core/Form";
import {computed, ref} from "vue";
import {getErrors, getOriginal, getTitle, getValue, isRequired, isValid} from "./utils";
import FormFieldWrapper from "@/Components/Form/Helpers/FormFieldWrapper.vue";
import InputCheckbox from "@/Components/Input/InputCheckbox.vue";
import {FormFieldProps} from "@/Components/Form/Helpers/Types";
import {InputCheckboxProps} from "@/Components/Input/Helpers/Types";

interface Props extends FormFieldProps, InputCheckboxProps {
    form: Form,
    name: string,
    defaultValue?: any,
}

const props = withDefaults(defineProps<Props>(), {hideTitle: true});

const emit = defineEmits<{ (e: 'change', value: boolean | Array<number | string>, name: string, payload: any): void }>()

const input = ref<InstanceType<typeof InputCheckbox> | undefined>(undefined);

const title = computed(() => {
    return getTitle(props.form, props.name, props.title);
});
const placeholderProxy = computed(() => {
    return props.placeholder !== undefined ? props.placeholder : title.value;
});
const modelValue = computed((): boolean | Array<number | string> => {
    return getValue(props.form, props.name, null);
});
const original = computed((): undefined | boolean | Array<number | string> | null => {
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

function change(value: boolean | Array<number | string>, name: string | undefined) {
    if (name) {
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
