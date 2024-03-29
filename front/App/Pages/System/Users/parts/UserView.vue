<template>
    <div>
        <GuiGroupBox>
            <GuiValue dots title="Фамилия:">{{ userData.lastname ?? '—' }}</GuiValue>
            <GuiValue dots title="Имя:">{{ userData.firstname ?? '—' }}</GuiValue>
            <GuiValue dots title="Отчество:">{{ userData.patronymic ?? '—' }}</GuiValue>
            <GuiValue dots title="Отображаемое имя:">{{ userData.display_name ? userData.display_name : '—' }}</GuiValue>
            <GuiValue dots title="Телефон:">{{ formatPhone(userData.phone) }}</GuiValue>
        </GuiGroupBox>
        <GuiGroupBox>
            <GuiValue dots title="Статус учётной записи:">
                <GuiAccessIndicator :locked="!userData.is_active"/>
                {{ userData.status }}
                <template v-if="canChange && !userData.locked">
                    <GuiLink @click="block(true)" v-if="userData.is_active" name="заблокировать" style="font-size: 14px; margin-left: 5px;"/>
                    <GuiLink @click="block(false)" v-if="!userData.is_active" name="активировать" style="font-size: 14px; margin-left: 5px;"/>
                </template>
            </GuiValue>
            <GuiValue dots title="Адрес электронной почты:">
                {{ userData.email ? userData.email : '—' }}
                <GuiLink v-if="canChange && !userData.locked" @click="changeEmail" name="изменить адрес" style="font-size: 14px;"/>
            </GuiValue>
            <GuiValue dots title="Логин:">{{ userData.username ? userData.username : '—' }}</GuiValue>
            <GuiValue dots title="Пароль для входа:">
                {{ userData.has_password ? '******' : 'не задан' }}
                <GuiLink v-if="canChange && !userData.locked" @click="changePassword" name="изменить пароль" style="font-size: 14px;"/>
            </GuiValue>
        </GuiGroupBox>

        <PopUpForm :form="form_password" ref="ref_form_password" :width="{width: '400px'}">
            <FormPassword :form="form_password" name="new_password" :clearable="true" :without-title="true" :disabled="form_password.values['clear_password']"/>
            <FormCheckBox :form="form_password" name="clear_password" :without-title="true" :disabled="!userData.has_password"/>
        </PopUpForm>

        <PopUpForm :form="form_email" ref="ref_form_email" :hide-errors="true" :width="{width: '400px'}">
            <FormString :form="form_email" name="email" :clearable="true" :without-title="true"/>
            <FormCheckBox :form="form_email" name="email_confirmation_need" :without-title="true"
                          :disabled="form_email.values['email'] === null || form_email.values['email'] === form_email.originals['email']"/>
            <GuiHint v-if="form_email.values['email_confirmation_need']">* На указанную электронную почту будет отправлено письмо со ссылкой для подтверждения адреса. Адрес
                электронной почты поменяется на новый
                только после его подтверждения.
            </GuiHint>
        </PopUpForm>
    </div>
</template>

<script setup lang="ts">
import {User, UserInfo} from "@/App/types";
import GuiGroupBox from "@/Components/GUI/GuiGroupBox.vue";
import GuiValue from "@/Components/GUI/GuiValue.vue";
import GuiAccessIndicator from "@/Components/GUI/GuiAccessIndicator.vue";
import GuiLink from "@/Components/GUI/GuiLink.vue";
import {processEntry} from "@/Core/Helpers/ProcessEntry";
import dialog from "@/Core/Dialog/Dialog";
import {computed, ref} from "vue";
import PopUpForm from "@/Components/PopUp/PopUpForm.vue";
import {Form} from "@/Core/Form";
import FormPassword from "@/Components/Form/FormPassword.vue";
import {formatPhone} from "@/Core/Helpers/Phone";
import FormCheckBox from "@/Components/Form/FormCheckBox.vue";
import FormString from "@/Components/Form/FormString.vue";
import GuiHint from "@/Components/GUI/GuiHint.vue";
import {can} from "@/Core/Can";
import {apiEndPoint} from "@/Core/Http/ApiEndPoints";

const props = defineProps<{
    userId: number,
    userData: UserInfo,
}>();

const emit = defineEmits<{
    (e: 'update'): void,
}>()

const canChange = computed((): boolean => {
    return can('system__users_change');
});

const processing = ref<boolean>(false);

const form_password = ref<Form>(new Form({
    save_url: apiEndPoint('patch', '/api/system/users/user/{userID}/password', {userID: props.userId})
}, 'Изменение пароля'));
const form_email = ref<Form>(new Form({
    save_url: apiEndPoint('patch', '/api/system/users/user/{userID}/email', {userID: props.userId})
}, 'Изменение адреса электронной почты'));

const ref_form_password = ref<InstanceType<typeof PopUpForm> | undefined>(undefined);
const ref_form_email = ref<InstanceType<typeof PopUpForm> | undefined>(undefined);

function block(disabled: boolean): void {
    let name = String([props.userData.lastname, props.userData.firstname, props.userData.patronymic].join(' ')).trim();
    processEntry({
        title: disabled ? 'Блокировка' : 'Активация',
        question: (disabled ? 'Заблокировать' : 'Активировать') + ` учётную запись "${name}"?`,
        button: dialog.button('yes', disabled ? 'Заблокировать' : 'Активировать', 'default'),
        url: apiEndPoint('patch', '/api/system/users/user/{userID}/status', {userID: props.userData.id}),
        options: {disabled: disabled, hash: props.userData.hash},
        progress: p => processing.value = p
    }).then(() => {
        emit('update');
    });
}

function changePassword(): void {
    if (ref_form_password.value !== undefined) {
        form_password.value.reset();
        form_password.value.set('new_password', null, 'nullable|min:6', 'Новый пароль');
        form_password.value.set('clear_password', null, null, 'Удалить пароль');
        form_password.value.hash = props.userData.hash;
        form_password.value.setLoaded();
        ref_form_password.value.show(true)
            ?.then(() => {
                emit('update');
            });
    }
}

function changeEmail(): void {
    if (ref_form_email.value !== undefined) {
        form_email.value.reset();
        form_email.value.set('email', props.userData.email, 'nullable|email', 'Адрес электронной почты', true);
        form_email.value.set('email_confirmation_need', null, null, 'Запросить подтверждение адреса электронной почты');
        form_email.value.hash = props.userData.hash;
        form_email.value.setLoaded();
        ref_form_email.value.show(true)
            ?.then(() => {
                emit('update');
            });
    }
}
</script>
