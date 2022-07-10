<template>
    <div class="input-checkbox">
        <CheckBox class="input-checkbox__input" :value="value" v-model="proxyValue" :valid="valid" :label="label" :disabled="disabled" :small="small">
            <slot/>
        </CheckBox>
    </div>
</template>

<script>
import CheckBox from "@/Components/Input/Helpers/CheckBox";
import empty from "@/Core/Helpers/Empty";

export default {
    components: {CheckBox},
    props: {
        name: String,
        modelValue: {type: [Number, String, Boolean, Array], default: null},
        original: {type: [Number, String, Boolean, Array], default: null},
        value: {type: [Number, String, Boolean], default: null},
        valid: {type: Boolean, default: true},
        disabled: {type: Boolean, default: false},
        label: {type: String, default: null},
        small: {type: Boolean, default: false},
    },

    emits: ['update:modelValue', 'change'],

    computed: {
        isDirty() {
            if (this.modelValue instanceof Array) {
                return empty(this.original) ? this.modelValue.indexOf(this.value) !== -1 : this.original.indexOf(this.value) !== this.modelValue.indexOf(this.value);
            } else {
                return this.modelValue === (this.original === null || this.original === false);
            }
        },
        proxyValue: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit('update:modelValue', value);
                this.$emit('change', value, this.name);
            }
        }
    }
}
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
