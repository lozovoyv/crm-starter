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
        <InputCheckbox
            v-model="proxyValue"
            :name="name"
            :original="original"
            :has-errors="hasErrors"
            :disabled="disabled"
            :value="value"
            :label="label"
            :small="small"
            @change="change"
            ref="input"
        />
    </FieldWrapper>
</template>

<script setup lang="ts">
import FieldWrapper from "@/Components/Fields/Helpers/FieldWrapper.vue";
import InputCheckbox from "@/Components/Input/InputCheckbox.vue";
import {computed} from "vue";

const props = defineProps<{
    // common props
    name?: string,
    modelValue?: boolean | number | string | Array<number | string>,
    original?: boolean | number | string | Array<number | string>,
    disabled?: boolean,
    hasErrors?: boolean,
    small?: boolean,
    // field props
    title?: string,
    required?: boolean,
    errors?: string[],
    hideTitle?: boolean,
    vertical?: boolean,
    // checkbox props
    label?: string,
    value?: number | string,
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean | number | string | Array<number | string>): void,
    (e: 'change', value: boolean | number | string | Array<number | string>, name: string | undefined): void,
}>();

const proxyValue = computed({
    get: (): boolean | number | string | Array<number | string> => {
        return props.modelValue || false;
    },
    set: (value: boolean | number | string | Array<number | string>) => {
        emit('update:modelValue', value);
        emit('change', value, props.name);
    }
});

function change(value: boolean | number | string | Array<number | string>, name: string | undefined): void {
    emit('change', value, name);
}
</script>

