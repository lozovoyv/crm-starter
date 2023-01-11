<template>
    <PopUpForm :form="form" ref="popup" :width="{width: '450px'}">
        <FormString :form="form" name="name"/>
        <FormDropdown :form="form" name="active" :options="[{id: true, name: 'Включена'},{id: false, name: 'Отключена'}]"/>
        <FormText :form="form" name="description"/>
        <GuiHeading>Права</GuiHeading>
        <div class="roles-permissions-edit">
            <template v-for="permissionId in form.payload['permissions']">
                <FormCheckBox :form="form" :name="'permission.' + permissionId" :hide-title="true"/>
                <GuiHint v-if="form.payload['descriptions'][permissionId]" style="padding: 0 0 3px 25px;">
                    {{ form.payload['descriptions'][permissionId] }}
                </GuiHint>
            </template>
        </div>
    </PopUpForm>
</template>

<script setup lang="ts">
import PopUpForm from "@/Components/PopUp/PopUpForm.vue";
import {Form} from "@/Core/Form";
import {ref} from "vue";
import FormString from "@/Components/Form/FormString.vue";
import FormDropdown from "@/Components/Form/FormDropdown.vue";
import FormText from "@/Components/Form/FormText.vue";
import FormCheckBox from "@/Components/Form/FormCheckBox.vue";
import GuiHeading from "@/Components/GUI/GuiHeading.vue";
import GuiHint from "@/Components/GUI/GuiHint.vue";

const popup = ref<InstanceType<typeof PopUpForm> | null>(null);

const form = ref<Form>(new Form(null, '/api/system/roles/get', '/api/system/roles/update'));

const emit = defineEmits<{
    (e: 'update'): void,
}>()

function show(roleId: number, fromRoleId: number | null = null) {
    if (!popup.value) {
        return;
    }
    return popup.value.show({role_id: roleId, from_role_id: fromRoleId});
}

defineExpose({
    show,
})
</script>

<style lang="scss">
.roles-permissions-edit .input-checkbox {
    min-height: unset;
}
</style>
