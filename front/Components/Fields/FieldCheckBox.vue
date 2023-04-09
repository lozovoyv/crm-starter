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
        <InputCheckbox
            v-model="proxyValue"
            :name="name"
            :original="original"
            :has-errors="hasErrors"
            :disabled="disabled"
            :value="value"
            :label="label"
            @change="change"
            ref="input"
        >
            <slot/>
        </InputCheckbox>
    </FieldWrapper>
</template>

<script setup lang="ts">
import FieldWrapper from "@/Components/Fields/Helpers/FieldWrapper.vue";
import InputCheckbox from "@/Components/Input/InputCheckbox.vue";
import {computed} from "vue";

const props = defineProps<{
    // common props
    name?: string,
    modelValue: boolean | Array<number | string> | null,
    original?: boolean | Array<number | string> | null,
    disabled?: boolean,
    hasErrors?: boolean,
    // field props
    title?: string | null,
    required?: boolean,
    errors?: string[],
    hideTitle?: boolean,
    emptyTitle?: boolean,
    vertical?: boolean,
    // checkbox props
    label?: string | null,
    value?: number | string,
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean | Array<number | string>): void,
    (e: 'change', value: boolean | Array<number | string>, name: string | undefined): void,
}>();

const proxyValue = computed({
    get: (): boolean | Array<number | string> => {
        return props.modelValue || false;
    },
    set: (value: boolean | Array<number | string>) => {
        emit('update:modelValue', value);
        emit('change', value, props.name);
    }
});

function change(value: boolean | Array<number | string>, name: string | undefined): void {
    emit('change', value, name);
}
</script>

