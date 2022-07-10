<template>
    <FieldString
        :model-value="value"
        :name="name"
        :title="title"
        :hide-title="hideTitle"
        :original="original"
        :valid="valid"
        :errors="errors"
        :required="required"
        :disabled="disabled"
        :placeholder="title"
        :type="type"
        :autocomplete="autocomplete"
        :small="small"
        @change="change"
        ref="input"
    />
</template>

<script>
import FormMixin from "./Helpers/FormMixin.vue";
import FieldString from "../Fields/FieldString.vue";

export default {
    components: {FieldString},
    props: {
        disabled: {type: Boolean, default: false},
        small: {type: Boolean, default: false},
        type: {type: String, default: 'text'},
        autocomplete: {type: String, default: 'off'},
    },
    mixins: [FormMixin],
}
</script>
