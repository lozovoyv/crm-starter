<template>
    <div class="form">
        <LoadingProgress :loading="form.state.is_loading || form.state.is_saving">
            <slot/>
            <div class="form__errors" v-if="!hideErrors && errors.length > 0">
                <div class="form__errors-error" v-for="error in errors">{{ error.text }}</div>
            </div>
        </LoadingProgress>
        <div class="form__actions" v-if="!hideButtons">
            <GuiButton type="unset" @click="cancel">Отмена</GuiButton>
            <GuiButton type="unset" @click="clear" :disabled="disabled">Сбросить</GuiButton>
            <GuiButton @click="save" :disabled="disabled">{{ saveButton ? saveButton : 'Сохранить' }}</GuiButton>
        </div>
    </div>
</template>

<script setup lang="ts">
import GuiButton from "@/Components/GUI/GuiButton.vue";
import {Form, FormResponse} from "@/Core/Form";
import {computed} from "vue";
import LoadingProgress from "@/Components/LoadingProgress.vue";

const props = defineProps<{
    form: Form,
    saveButton?: string,
    saveDisabled?: boolean,
    hideButtons?:boolean,
    hideErrors?:boolean,
}>();

const emit = defineEmits<{
    (e: 'save', response: FormResponse): void,
    (e: 'clear'): void,
    (e: 'cancel'): void,
}>()

const disabled = computed((): boolean => {
    return props.saveDisabled || !props.form.state.is_loaded || !!props.form.state.is_loading || !!props.form.state.is_saving || !!props.form.state.is_forbidden;
});

const errors = computed((): Array<{ text: string, key: string, index: number }> => {
    let errors: Array<{ text: string, key: string, index: number }> = [];
    Object.keys(props.form.errors).map((key: string) => {
        if (props.form.errors[key].length > 0) {
            props.form.errors[key].map((error, index) => {
                errors.push({text: error, key: key, index: index});
            })
        }
    })
    return errors;
});

function clearError(key: string, index: number): void {
    if (props.form) {
        props.form.errors[key].splice(index);
    }
}

function save() {
    if (!props.form.validate()) {
        return;
    }
    props.form.save()
        .then(response => {
            emit('save', response);
        });
}

function cancel() {
    emit('cancel');
}

function clear() {
    props.form.clear();
    emit('clear');
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
            padding: 4px 8px;
            line-height: 16px;
            margin: 0 0 5px;
            border: 1px solid transparentize($color_error, 0.5);
            background-color: transparentize($color_error, 0.85);
            color: $color_text_black;
            border-radius: 2px;
            font-size: 14px;
        }
    }

    &__actions {
        margin-top: 20px;
        text-align: right;
    }
}
</style>
