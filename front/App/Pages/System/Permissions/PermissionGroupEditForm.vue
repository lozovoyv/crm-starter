<template>
    <PopUpForm :form="form" ref="popup">
        <FormString :form="form" name="name"/>
        <FormDropdown :form="form" name="active" :options="[{id: true, name: 'Включена'},{id: false, name: 'Отключена'}]"/>
        <FormText :form="form" name="description"/>
        <GuiHeading>Права</GuiHeading>
        <div class="roles-permissions-edit">
            <ListTable>
                <ListTableRow v-for="permissionId in form.payload['permissions']">
                    <ListTableCell>
                        <FormCheckBox :form="form" :name="'permission.' + permissionId" :hide-title="true" :label="''"/>
                    </ListTableCell>
                    <ListTableCell>{{ form.payload['modules'][permissionId] }}</ListTableCell>
                    <ListTableCell>{{ form.titles['permission.' + permissionId] }}</ListTableCell>
                    <ListTableCell>{{ form.payload['descriptions'][permissionId] }}</ListTableCell>
                </ListTableRow>
            </ListTable>
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
import ListTable from "@/Components/List/ListComponent.vue";
import ListTableRow from "@/Components/List/ListRow.vue";
import ListTableCell from "@/Components/List/ListCell.vue";

const popup = ref<InstanceType<typeof PopUpForm> | undefined>(undefined);

const form = ref<Form>(new Form(undefined, '/api/system/roles/get', '/api/system/roles/update'));

const emit = defineEmits<{
    (e: 'update'): void,
}>()

function show(roleId: number, fromRoleId: number | null = null) {
    return popup.value?.show({role_id: roleId, from_role_id: fromRoleId});
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
