<template>
    <div class="input-checkbox">
        <CheckBox class="input-checkbox__input"
                  :value="value"
                  v-model="proxyValue"
                  :has-errors="hasErrors"
                  :label="label"
                  :disabled="disabled"
                  :small="small"
                  :dirty="isDirty"
        >
            <slot/>
        </CheckBox>
    </div>
</template>

<script setup lang="ts">
import CheckBox from "./Helpers/CheckBox.vue";
import {computed} from "vue";

const props = defineProps<{
    // common props
    name?: string,
    modelValue?: boolean | number | string | number[] | string[],
    original?: boolean | number | string | number[] | string[],
    disabled?: boolean,
    hasErrors?: boolean,
    small?: boolean,
    // checkbox props
    label?: string,
    value?: number | string,
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean | number | string | number[] | string[]): void,
    (e: 'change', value: boolean | number | string | number[] | string[], name: string | undefined): void,
}>();

const proxyValue = computed({
    get: (): boolean | number | string | number[] | string[] => {
        return props.modelValue || false;
    },
    set: (value: boolean | number | string | number[] | string[]) => {
        emit('update:modelValue', value);
        emit('change', value, props.name);
    }
});

const isDirty = computed((): boolean => {
    if (typeof props.modelValue === "object") {
        const val: Array<string | number> = props.modelValue;
        const orig: boolean | number | string | Array<string | number> = props.original || false;
        if (typeof orig === "object") {
            return typeof props.value !== "undefined" && orig.indexOf(props.value) !== val.indexOf(props.value);
        } else {
            return typeof props.value !== "undefined" && val.indexOf(props.value) !== -1;
        }
    } else {
        return props.modelValue === (props.original === null || props.original === false);
    }
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
