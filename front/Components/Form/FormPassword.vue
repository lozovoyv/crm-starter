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
        <InputPassword
            :model-value="modelValue"
            :original="original"
            :name="name"
            :disabled="disabled"
            :has-errors="!valid"
            :clearable="clearable"
            :autocomplete="autocomplete"
            :placeholder="placeholderProxy"
            @change="change"
            ref="input"
        />
    </FormFieldWrapper>
</template>

<script setup lang="ts">
import {Form} from "@/Core/Form";
import {computed, ref} from "vue";
import {getErrors, getOriginal, getTitle, getValue, isRequired, isValid} from "./utils";
import {FormFieldProps} from "@/Components/Form/Helpers/Types";
import {InputPasswordProps} from "@/Components/Input/Helpers/Types";
import FormFieldWrapper from "@/Components/Form/Helpers/FormFieldWrapper.vue";
import InputPassword from "@/Components/Input/InputPassword.vue";

interface Props extends FormFieldProps, InputPasswordProps {
    form: Form,
    name: string,
    defaultValue?: any,
}

const props = defineProps<Props>();

const emit = defineEmits<{ (e: 'change', value: string | null, name: string, payload: any): void }>()

const input = ref<InstanceType<typeof InputPassword> | undefined>(undefined);

const placeholderProxy = computed(() => {
    return props.placeholder !== undefined ? props.placeholder : title.value;
});
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
