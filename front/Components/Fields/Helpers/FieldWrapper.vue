<template>
    <div class="input-field" :class="{'input-field__required': required}">
        <span class="input-field__title" v-if="!hideTitle">{{ title }}</span>
        <div class="input-field__wrapper">
            <div class="input-field__input">
                <slot/>
            </div>
            <div class="input-field__errors">
                <span class="input-field__errors-error" v-if="!valid" v-for="error in errors">{{ error }}</span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        title: String,
        required: {type: Boolean, default: false},
        disabled: {type: Boolean, default: false},
        valid: {type: Boolean, default: true},
        errors: {type: Array, default: () => ([])},
        hideTitle: {type: Boolean, default: false},
    }
}
</script>

<style lang="scss">
@import "@/variables";

$field_title_color: $color-text-black !default;
$field_required_color: $color-error !default;
$field_error_color: $color-error !default;

.input-field {
    display: inline-flex;
    flex-direction: row;
    width: 100%;
    box-sizing: border-box;
    padding: 5px 0;

    &__title {
        font-family: $project_font;
        font-size: 14px;
        margin: 0 0 8px;
        width: 200px;
        box-sizing: border-box;
        padding-top: 8px;
        flex-shrink: 0;
        color: $field_title_color;
    }

    &__required &__title:after {
        content: '*';
        color: $field_required_color;
        margin-left: 3px;
    }

    &__wrapper {
        flex-grow: 1;
    }

    &__input {
        flex-grow: 1;
        display: flex;
    }

    &__errors {
        display: flex;
        flex-direction: column;
        min-height: 8px;

        &-error {
            font-family: $project_font;
            font-size: 14px;
            margin-top: 5px;
            text-transform: lowercase;
            color: $field_error_color;
        }
    }
}
</style>
