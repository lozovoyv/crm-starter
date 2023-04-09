<template>
    <FieldWrapper
        :disabled="disabled"
        :has-errors="hasErrors"
        :title="title"
        :required="required"
        :errors="errors"
        :hide-title="hideTitle"
        :empty-title="emptyTitle"
        :vertical="vertical"
    >
        <InputPhone
            :name="name"
            v-model="proxyValue"
            :original="original"
            :disabled="disabled"
            :has-errors="hasErrors"
            :clearable="clearable"
            :type="type"
            :autocomplete="autocomplete"
            :placeholder="placeholder"
            @change="change"
            ref="input"
        />
    </FieldWrapper>
</template>

<script setup lang="ts">
import FieldWrapper from "@/Components/Fields/Helpers/FieldWrapper.vue";
import {computed, ref} from "vue";
import InputPhone from "@/Components/Input/InputPhone.vue";

interface FieldProperties {
    title?: string,
    hideTitle?: boolean,
    emptyTitle?: boolean,
    required?: boolean,
    disabled?: boolean,
    hasErrors?: boolean,
    errors?: string[],
    vertical?: boolean,
}

const props = defineProps<{
    // common props
    name?: string,
    modelValue: string | null,
    original?: string | null,
    disabled?: boolean,
    hasErrors?: boolean,
    clearable?: boolean,
    // field props
    title?: string | null,
    required?: boolean,
    errors?: string[],
    hideTitle?: boolean,
    emptyTitle?: boolean,
    vertical?: boolean,
    // string props
    type?: string,
    autocomplete?: string,
    placeholder?: string | null,
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | null): void,
    (e: 'change', value: string | null, name: string | undefined): void,
}>()

const input = ref<InstanceType<typeof InputPhone> | undefined>(undefined);

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

