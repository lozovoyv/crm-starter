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
        <slot/>
    </PopUp>
</template>

<script setup lang="ts">
import dialog, {DialogButtons, DialogButtonsAlign, DialogButtonType, DialogResolveFunction, DialogType, DialogWidth} from "@/Core/Dialog/Dialog";
import {Form} from "@/Core/Form";
import {computed, ref} from "vue";
import PopUp from "@/Components/PopUp/PopUp.vue";

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
}>();

const popUpButtons = computed((): DialogButtons => {
    return [
        dialog.button('save', props.saveButtonCaption ? props.saveButtonCaption : 'Сохранить', props.saveButtonType ? props.saveButtonType : 'default'),
        dialog.button('cancel', 'Отмена'),
    ];
});

const popup = ref<InstanceType<typeof PopUp> | null>(null);

let internalResolveFunction: null | { (value: unknown): void } = null;

function show(options: { [index: string]: any } = {}) {
    if (popup.value === null) {
        console.error('Popup instance not set');
        return;
    }
    popup.value.show();
    popup.value.process(true);
    props.form.options = options;
    props.form.load()
        .catch(() => {
            popup.value.hide();
        })
        .finally(() => {
            popup.value.process(false);
        });
    return new Promise(resolve => {
        internalResolveFunction = resolve;
    });
}

function hide() {
    if (popup.value === null) {
        console.error('Popup instance not set');
        return;
    }
    popup.value.hide();
}

function resolved(result: string | null) {
    if (result !== 'save') {
        hide();
        return true;
    }
    if (!props.form.validate()) {
        return false;
    }
    console.log(result);
    popup.value.process(true);

    props.form.save()
        .then((payload) => {
            popup.value.hide();
            if (internalResolveFunction !== null) {
                internalResolveFunction(payload);
            }
            return true;
        })
        .finally(() => {
            popup.value.process(false);
        });
    return false;
}

defineExpose({
    show,
    hide,
});
</script>