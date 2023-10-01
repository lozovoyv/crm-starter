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
        <InputDropDown
            :name="name"
            v-model="proxyValue"
            :original="original"
            :disabled="disabled"
            :has-errors="hasErrors"
            :clearable="clearable"
            :placeholder="placeholder"
            :has-null="hasNull"
            :null-caption="nullCaption"
            :empty-caption="emptyCaption"
            :options="options"
            :show-disabled-options="showDisabledOptions"
            :id-key="idKey"
            :caption-key="captionKey"
            :filter-key="filterKey"
            :hint-key="hintKey"
            :multi="multi"
            :search="search"
            @change="change"
            ref="input"
        />
    </FieldWrapper>
</template>

<script setup lang="ts">
import FieldWrapper from "@/Components/Fields/Helpers/FieldWrapper.vue";
import {computed, ref} from "vue";
import {DropDownOptions, DropDownValueType} from "@/Components/Input/Helpers/Types";
import InputDropDown from "@/Components/Input/InputDropDown.vue";

const props = defineProps<{
    // common props
    name?: string,
    modelValue: DropDownValueType,
    original?: DropDownValueType,
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

    // dropdown props
    placeholder?: string | null,
    hasNull?: boolean,
    nullCaption?: string,
    emptyCaption?: string,
    options: DropDownOptions,
    showDisabledOptions?: boolean,
    idKey?: string,
    captionKey?: string,
    filterKey?: string,
    hintKey?: string,
    multi?: boolean,
    search?: boolean,
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | number | boolean | null | Array<string | number>): void,
    (e: 'change', value: string | number | boolean | null | Array<string | number>, name: string | undefined, payload: any): void,
}>()

const input = ref<InstanceType<typeof InputDropDown> | undefined>(undefined);

const proxyValue = computed({
    get: (): string | number | boolean | null | Array<string | number> => {
        return props.modelValue === undefined ? null : props.modelValue;
    },
    set: (value: string | number | boolean | null | Array<string | number>) => {
        emit('update:modelValue', value);
    }
});

function change(value: string | number | boolean | null | Array<string | number>, name: string | undefined, payload: any): void {
    emit('change', value, name, payload);
}

defineExpose({
    input,
});
</script>

