<template>
    <div class="form-field" :class="{'form-field__required': required, 'form-field__vertical': vertical}">
        <span class="form-field__title" v-if="!withoutTitle">
            <span class="form-field__title-wrapper" v-if="!hideTitle">{{ title }}</span>
        </span>
        <div class="form-field__wrapper">
            <div class="form-field__input">
                <slot/>
            </div>
            <div class="form-field__errors">
                <span class="form-field__errors-error" v-if="hasErrors" v-for="error in errors">{{ error }}</span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">

import {FormFieldBaseProps, FormFieldProps} from "@/Components/Form/Helpers/Types";

interface Props extends FormFieldBaseProps, FormFieldProps {}

const props = defineProps<Props>();
</script>

<style lang="scss">
@import "@/variables.scss";

.form-field {
    display: inline-flex;
    flex-direction: row;
    width: 100%;
    box-sizing: border-box;
    padding: 5px 0;

    &__vertical {
        flex-direction: column;
    }

    &__title {
        @include font(15px);
        width: 150px;
        box-sizing: border-box;
        flex-shrink: 0;
        line-height: 15px;
        color: $color_text_black;
        display: flex;
        align-items: flex-start;

        &-wrapper {
            box-sizing: border-box;
            padding: $base_size_unit 5px 0 0;
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
        color: $color_error;
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
            @include font(13px);
            margin-top: 2px;
            text-transform: lowercase;
            color: $color_error;
            padding-left: 2px;
            box-sizing: border-box;
        }
    }
}
</style>
