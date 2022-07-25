<template>
    <div class="input-field" :class="{'input-field__required': required, 'input-field__vertical': vertical}">
        <span class="input-field__title" v-if="!hideTitle">
            <span class="input-field__title-wrapper">{{ title }}</span>
        </span>
        <div class="input-field__wrapper">
            <div class="input-field__input">
                <slot/>
            </div>
            <div class="input-field__errors">
                <span class="input-field__errors-error" v-if="hasErrors" v-for="error in errors">{{ error }}</span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
const props = defineProps<{
    title?: string
    required?: boolean,
    disabled?: boolean,
    hasErrors?: boolean,
    errors?: string[],
    hideTitle?: boolean,
    vertical?: boolean,
}>();
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

    &__vertical {
        flex-direction: column;
    }

    &__title {
        font-family: $project_font;
        font-size: 15px;
        width: 200px;
        box-sizing: border-box;
        flex-shrink: 0;
        line-height: 15px;
        color: $field_title_color;
        display: flex;
        align-items: center;

        &-wrapper {
            box-sizing: border-box;
            padding-right: 5px;
        }
    }

    &__vertical &__title {
        width: 100% !important;
        padding-left: 2px;
    }

    &__vertical &__title-wrapper {
        padding-right: 0;

        &:not(:empty) {
            margin-bottom: 5px;
        }
    }

    &__required &__title-wrapper:after {
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
        min-height: 2px;

        &-error {
            font-family: $project_font;
            font-size: 12px;
            margin-top: 2px;
            text-transform: lowercase;
            color: $field_error_color;
            padding-left: 2px;
            box-sizing: border-box;
        }
    }
}
</style>
