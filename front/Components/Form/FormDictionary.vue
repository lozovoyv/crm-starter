<template>
    <FieldDictionary
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
        :placeholder="placeholder"
        :has-null="hasNull"
        :null-caption="nullCaption"
        :empty-caption="emptyCaption"
        :dictionary="dictionary"
        :freeze="freeze"
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
</template>

<script setup lang="ts">
import {Form} from "@/Core/Form";
import {computed, ref, UnwrapRef} from "vue";
import {getErrors, getOriginal, getTitle, getValue, isRequired, isValid} from "./utils";
import FieldDictionary from "@/Components/Fields/FieldDictionary.vue";

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
    form: Form | UnwrapRef<Form>,
    defaultValue?: any,
    // string props
    placeholder?: string,
    hasNull?: boolean,
    nullCaption?: string,
    emptyCaption?: string,
    dictionary: string,
    freeze?: boolean,
    showDisabledOptions?: boolean,
    idKey?: string,
    captionKey?: string,
    filterKey?: string,
    hintKey?: string,
    multi?: boolean,
    search?: boolean,
}>();

const emit = defineEmits<{ (e: 'change', value: string | number | boolean | null | Array<string | number>, name: string, payload: any): void }>()

const input = ref<InstanceType<typeof FieldDictionary> | undefined>(undefined);

const title = computed(() => {
    return getTitle(props.form, props.name, props.title);
});
const modelValue = computed((): string | number | null | Array<string | number> => {
    return getValue(props.form, props.name, null);
});
const original = computed((): string | number | null | Array<string | number> => {
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

function change(value: string | number | boolean | null | Array<string | number>, name: string | undefined, payload: any) {
    if(name) {
        props.form.update(name, value);
        emit('change', value, name, payload);
    }
}

defineExpose({
    input,
})
</script>
