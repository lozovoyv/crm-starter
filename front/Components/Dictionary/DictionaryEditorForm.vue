<template>
    <PopUpForm :form="form" ref="popup" :width="{max: '500px'}">
        <template v-for="(type, name) in fields" v-if="fields">
            <FormString v-if="type === 'string'" :form="form" :name="String(name)"/>
            <FormCheckBox v-if="type === 'boolean'" :form="form" :name="String(name)"/>
        </template>
    </PopUpForm>
</template>

<script setup lang="ts">
import {Form} from "@/Core/Form";
import {computed, ref} from "vue";
import PopUpForm from "@/Components/PopUp/PopUpForm.vue";
import FormString from "@/Components/Form/FormString.vue";
import FormCheckBox from "@/Components/Form/FormCheckBox.vue";

const props = defineProps<{
    dictionary: string,
}>();

const popup = ref<InstanceType<typeof PopUpForm> | undefined>(undefined);

const form = ref<Form>(new Form(null));

function show(id: number | null) {
    form.value.url_load = `/api/dictionaries/${props.dictionary}/item`;
    return popup.value?.show(id);
}

const fields = computed((): { [index: string]: string } | undefined => {
    if (form.value.state.is_loaded) {
        return form.value.payload['types'];
    }
    return undefined;
});

defineExpose({
    show,
})
</script>
