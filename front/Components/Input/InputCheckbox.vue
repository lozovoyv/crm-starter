<template>
    <div class="input-checkbox">
        <CheckBox class="input-checkbox__input"
                  :value="value"
                  v-model="proxyValue"
                  :has-errors="hasErrors"
                  :label="label"
                  :disabled="disabled"
                  :dirty="isDirty"
        >
            <slot/>
        </CheckBox>
    </div>
</template>

<script setup lang="ts">
import CheckBox from "@/Components/Input/Helpers/CheckBox.vue";
import {computed} from "vue";

const props = defineProps<{
    // common props
    name?: string,
    modelValue: boolean | Array<number | string> | null,
    original?: boolean | Array<number | string> | null,
    disabled?: boolean,
    hasErrors?: boolean,
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

const isDirty = computed((): boolean => {
    if(props.original === undefined) {
        return false;
    }

    let original: boolean;
    let value: boolean;

    if(typeof props.original === "object" && props.original !== null) {
        original = (props.value !== undefined) ? props.original.indexOf(props.value) !== -1 : false;
    } else {
        original = !!props.original;
    }
    if(typeof props.modelValue === "object" && props.modelValue !== null) {
        value = (props.value !== undefined) ? props.modelValue.indexOf(props.value) !== -1 : false;
    } else {
        value = !!props.modelValue;
    }
        return original === value;
});
</script>

<style lang="scss">
@import "@/variables";

.input-checkbox {
    min-height: $base_size_unit;
    display: flex;
    align-items: center;

    &__input {
        width: 100%;
        padding: 0;
    }
}
</style>
