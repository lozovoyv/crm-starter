<template>
    <div>
        <GuiGroupBox width="50%" min-width="400px">
            <GuiValue dots title="Фамилия:">{{ userData.lastname }}</GuiValue>
            <GuiValue dots title="Имя:">{{ userData.firstname }}</GuiValue>
            <GuiValue dots title="Отчество:">{{ userData.patronymic }}</GuiValue>
            <GuiValue dots title="Отображаемое имя:">{{ userData.display_name }}</GuiValue>
        </GuiGroupBox>
        <GuiGroupBox width="50%" min-width="400px">
            <GuiValue dots title="Статус учётной записи:">
                <GuiAccessIndicator :locked="!userData.is_active"/>
                {{ userData.status }}
                <GuiLink @click="block" v-if="userData.is_active" name="заблокировать" style="font-size: 14px; margin-left: 5px;"/>
                <GuiLink @click="activate" v-if="!userData.is_active" name="активировать" style="font-size: 14px; margin-left: 5px;"/>
            </GuiValue>
            <GuiValue dots title="Пароль для входа:">
                {{ userData.has_password ? '******' : 'не задан' }}
                <GuiLink @click="changePassword" name="изменить пароль" style="font-size: 14px;"/>
            </GuiValue>
            <GuiValue dots title="Логин:">{{ userData.username }}</GuiValue>
            <GuiValue dots title="Адрес электронной почты:">{{ userData.email }}</GuiValue>
            <GuiValue dots title="Телефон:">{{ userData.phone }}</GuiValue>
        </GuiGroupBox>

        <PopUpForm :form="form_password" ref="ref_form_password" :width="{width: '300px'}">
            <FormPassword :form="form_password" name="password" :clearable="true" :hide-title="true"/>
        </PopUpForm>
    </div>
</template>

<script setup lang="ts">
import {UserInfo} from "@/App/types";
import GuiGroupBox from "@/Components/GUI/GuiGroupBox.vue";
import GuiValue from "@/Components/GUI/GuiValue.vue";
import GuiAccessIndicator from "@/Components/GUI/GuiAccessIndicator.vue";
import GuiLink from "@/Components/GUI/GuiLink.vue";
import {processEntry} from "@/Core/Helpers/ProcessEntry";
import dialog from "@/Core/Dialog/Dialog";
import {ref} from "vue";
import PopUpForm from "@/Components/PopUp/PopUpForm.vue";
import {Form} from "@/Core/Form";
import FormPassword from "@/Components/Form/FormPassword.vue";

const props = defineProps<{
    userId: number,
    userData: UserInfo,
}>();

const emit = defineEmits<{
    (e: 'update'): void,
}>()

const processing = ref<boolean>(false);

const form_password = ref<Form>(new Form('Изменение пароля', null, '/api/system/users/password'));

const ref_form_password = ref<InstanceType<typeof PopUpForm> | null>(null);

function block(): void {
    let name = String([props.userData.lastname, props.userData.firstname, props.userData.patronymic].join(' ')).trim();
    processEntry('Блокировка', `Заблокировать учётную запись "${name}"?`, dialog.button('yes', 'Заблокировать', 'default'),
        '/api/system/users/deactivate', {user_id: props.userData.id, user_hash: props.userData.hash},
        p => processing.value = p
    ).then(() => {
        emit('update');
    });
}

function activate(): void {
    let name = String([props.userData.lastname, props.userData.firstname, props.userData.patronymic].join(' ')).trim();
    processEntry('Активация', `Активировать учётную запись "${name}"?`, dialog.button('yes', 'Активировать', 'default'),
        '/api/system/users/activate', {user_id: props.userData.id, user_hash: props.userData.hash},
        p => processing.value = p
    ).then(() => {
        emit('update');
    });
}

function changePassword(): void {
    if (!ref_form_password.value) {
        return;
    }
    form_password.value.reset();
    form_password.value.set('password', null, 'nullable|min:6', 'новый пароль');
    ref_form_password.value.show({
        user_id: props.userId, user_hash: props.userData.hash
    })
        .then(() => {
            emit('update');
        });
}
</script>
