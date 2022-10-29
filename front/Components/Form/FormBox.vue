<template>
    <div class="form">
        <LoadingProgress :loading="form.is_loading || form.is_saving">
            <slot/>
            <div class="form__errors" v-if="errors.length > 0">
                <div class="form__errors-error" v-for="error in errors">{{ error }}</div>
            </div>
        </LoadingProgress>
        <div class="form__actions">
            <GuiButton type="success" @clicked="save" :disabled="disabled">Сохранить</GuiButton>
            <GuiButton type="default" @clicked="cancel">Отмена</GuiButton>
            <GuiButton @clicked="clear" :disabled="disabled">Сбросить</GuiButton>
        </div>
    </div>
</template>

<script setup lang="ts">
import GuiButton from "@/Components/GUI/GuiButton.vue";
import {Form} from "@/Core/Form";
import {computed} from "vue";
import LoadingProgress from "@/Components/LoadingProgress.vue";

const props = defineProps<{
    form: Form,
}>();

const disabled = computed((): boolean => {
    return !props.form.is_loaded || props.form.is_loading || props.form.is_saving || props.form.is_forbidden;
});

const errors = computed((): Array<string> => {
    let errors: Array<string> = [];
    Object.keys(props.form.errors).map((key: string) => {
        if (props.form.errors[key].length > 0) {
            errors.push(...props.form.errors[key]);
        }
    })
    return errors;
});

function save() {
    if (!props.form.validate()) {
        return;
    }
    props.form.save();
}

function cancel() {

}

function clear() {
    props.form.clear();
}
</script>

<style lang="scss">
@import "@/variables.scss";

.form {
    margin: 10px 0;

    &__errors {
        margin: 10px 0;
        box-sizing: border-box;
        font-family: $project_font;

        &-error {
            padding: 0 8px;
            height: $base_size_unit;
            line-height: $base_size_unit;
            margin: 0 0 5px;
            border: 1px solid transparentize($color_error, 0.9);
            background-color: transparentize($color_error, 0.95);
            color: $color_text_black;
            border-radius: 2px;
            font-size: 14px;
        }
    }

    &__actions {
        margin-top: 20px;
    }
}
</style>
