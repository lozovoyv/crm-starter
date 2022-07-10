<template>
    <FieldCheckBox
        :model-value="value"
        :name="name"
        :title="null"
        :hide-title="hideTitle"
        :original="original"
        :valid="valid"
        :errors="errors"
        :required="required"
        :disabled="disabled"
        :value="val"
        :label="title"
        @change="change"
        ref="input"
    />
</template>

<script>
import FormMixin from "./Helpers/FormMixin";
import FieldCheckBox from "../Fields/FieldCheckBox";

export default {
    components: {FieldCheckBox},
    props: {
        disabled: {type: Boolean, default: false},
        hideTitle: {type: Boolean, default: false},

        val: {type: [Number, String, Boolean], default: null},

        small: {type: Boolean, default: false},
    },

    mixins: [FormMixin],
}
</script>
