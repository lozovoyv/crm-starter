<template>
    <PopUpForm :form="form" ref="popup">
        <FormString :form="form" name="name"/>
        <FormDropdown :form="form" name="active" :options="[{id: true, name: 'Включена'},{id: false, name: 'Отключена'}]"/>
        <FormText :form="form" name="description"/>
        <table class="permissions-group-edit">
            <template v-for="(scope, index) in form.payload['scopes']">
                <thead>
                <tr>
                    <th colspan="3">
                        <span class="permissions-group-edit__title">{{ scope['name'] }}</span>
                        <GuiLink name="выбрать все" class="permissions-group-edit__select" :underline="true" @click="select(index, true)"/>
                        <GuiLink name="снять выделение" class="permissions-group-edit__select" :underline="true" @click="select(index, false)"/>
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="permission in scope['permissions']">
                    <td class="permissions-group-edit__spacer"></td>
                    <td class="permissions-group-edit__check">
                        <FormCheckBox :form="form" :name="permission" :without-title="true"/>
                    </td>
                    <td class="permissions-group-edit__description"> {{ form.payload['descriptions'][permission] }}</td>
                </tr>
                </tbody>
            </template>
        </table>
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
import {apiEndPoint} from "@/Core/Http/ApiEndPoints";
import GuiLink from "@/Components/GUI/GuiLink.vue";

const popup = ref<InstanceType<typeof PopUpForm> | undefined>(undefined);

const form = ref<Form>(new Form({load_url: apiEndPoint('get', '/api/system/permissions/group')}));

const emit = defineEmits<{
    (e: 'update'): void,
}>()

function show(groupId: number | null, fromGroupId: number | null = null) {
    form.value.setConfig({
        load_url: apiEndPoint('get', '/api/system/permissions/group/{groupID}', {groupID: groupId, from_group_id: fromGroupId}),
        save_url: apiEndPoint('put', '/api/system/permissions/group/{groupID}', {groupID: groupId})
    })
    return popup.value?.show();
}

function select(index: number, selected: boolean): void {
    form.value.payload['scopes'][index]['permissions'].map((permission: string) => {
        form.value.update(permission, selected);
    });
}

defineExpose({
    show,
})
</script>

<style lang="scss">
@import "@/variables";

.permissions-group-edit {
    font-family: $project_font;

    &__select {
        padding-left: 12px;
        font-size: 14px;
    }

    th {
        vertical-align: middle;
        height: 32px;
    }

    &__spacer {
        width: 16px;
    }

    &__description, &__name, &__check {
        height: 28px;
        vertical-align: middle;
        border-bottom: 1px dotted $color_gray_lighten_2;
    }

    &__check {

        & .form-field {
            padding: 0;
        }

        & .form-field__wrapper {
            padding: 0;
        }

        & .form-field__errors {
            display: none;
        }

        & .input-checkbox {
            min-height: 24px;
        }
    }

    &__name {
        font-size: 15px;
        color: $color_text_black;
    }

    &__description {
        font-size: 14px;
        color: $color_gray_darken_1;
        padding: 0 8px 0 16px;
    }
}
</style>
