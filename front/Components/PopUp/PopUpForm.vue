<template>
    <PopUp :title="form.title"
           :message="message"
           :buttons="popUpButtons"
           :align="align"
           :close-on-overlay="closeOnOverlay"
           :manual="true"
           :resolving="resolved"
           :width="width"
           ref="popup"
    >
        <FormBox :form="form" :hide-buttons="true" :hide-errors="hideErrors">
            <slot/>
        </FormBox>
    </PopUp>
</template>

<script setup lang="ts">
import dialog, {DialogButtons, DialogButtonsAlign, DialogButtonType, DialogResolveFunction, DialogType, DialogWidth} from "@/Core/Dialog/Dialog";
import {Form} from "@/Core/Form";
import {computed, ref} from "vue";
import PopUp from "@/Components/PopUp/PopUp.vue";
import FormBox from "@/Components/Form/FormBox.vue";
import {notify} from "@/Core/Notify";

const props = defineProps<{
    form: Form,
    type?: DialogType,
    message?: string,
    saveButtonType?: DialogButtonType,
    saveButtonCaption?: string,
    align?: DialogButtonsAlign,
    closeOnOverlay?: boolean,
    manual?: boolean,
    resolving?: DialogResolveFunction,
    width?: DialogWidth,
    hideErrors?: boolean,
}>();

const popUpButtons = computed((): DialogButtons => {
    return [
        dialog.button('save', props.saveButtonCaption ? props.saveButtonCaption : 'Сохранить', props.saveButtonType ? props.saveButtonType : 'default'),
        dialog.button('cancel', 'Отмена'),
    ];
});

const popup = ref<InstanceType<typeof PopUp> | undefined>(undefined);

let internalResolveFunction: undefined | { (value: unknown): void } = undefined;

function show(id: number | undefined, options: { [index: string]: any } = {}, skipLoading: boolean = false) {
    if (!popup.value) {
        console.error('Popup instance not set');
        return;
    }
    popup.value?.show();
    props.form.options = options;
    if (!skipLoading) {
        props.form.load(id, options)
            .catch(() => {
                popup.value?.hide();
            });
    }
    return new Promise(resolve => {
        internalResolveFunction = resolve;
    });
}

function hide() {
    if (!popup.value) {
        console.error('Popup instance not set');
        return;
    }
    popup.value?.hide();
}

function resolved(result: string | null): boolean {
    if (!popup.value) {
        console.error('Popup instance not set');
        return false;
    }
    if (result !== 'save') {
        hide();
        return true;
    }
    if (!props.form.validate()) {
        return false;
    }

    props.form.save()
        .then((payload) => {
            popup.value?.hide();
            if (internalResolveFunction) {
                internalResolveFunction(payload);
            }
            return true;
        })
        .catch(error => {
            if ([422, 500].indexOf(error.code) === -1) {
                notify(error.message, 0, 'error');
            }
        })
    return false;
}

defineExpose({
    show,
    hide,
});
</script>
