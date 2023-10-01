<template>
    <PopUpForm :form="form" ref="popup">
        <FormString :form="form" name="name"/>
        <FormDropdown :form="form" name="active" :options="[{id: true, name: 'Включена'},{id: false, name: 'Отключена'}]"/>
        <FormText :form="form" name="description"/>
        <GuiHeading>Права</GuiHeading>
        <div class="permissions-group-edit">
            <ListTable>
                <ListTableRow v-for="permissionId in form.payload['permissions']">
                    <ListTableCell style="padding: 3px 15px;">
                        <FormCheckBox :form="form" :name="'permission.' + permissionId" :hide-title="true" :label="''"/>
                    </ListTableCell>
                    <ListTableCell>{{ form.payload['scopes'][permissionId] }}</ListTableCell>
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
import {apiEndPoint} from "@/Core/Http/ApiEndPoints";

const popup = ref<InstanceType<typeof PopUpForm> | undefined>(undefined);

const form = ref<Form>(new Form({load_url: apiEndPoint('get', '/api/system/permissions/group')}));

const emit = defineEmits<{
    (e: 'update'): void,
}>()

function show(groupId: number | undefined, fromGroupId: number | null = null) {
    return popup.value?.show(groupId, {from_group_id: fromGroupId});
}

defineExpose({
    show,
})
</script>

<style lang="scss">
.permissions-group-edit .input-checkbox {
    min-height: unset;
}
</style>
