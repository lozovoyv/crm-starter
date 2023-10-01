<template>
    <LayoutPage :title="form.title"
                :is-forbidden="form.state.is_forbidden"
                :is-not-found="form.state.is_not_found"
                :breadcrumbs="[
                    {name: 'Учётные записи', route: {name: 'users'}},
                    {name: form.title, route: userId? {name: 'user_view', params: {id: userId}} : undefined},
                ]"
                :reload="true"
                @reload="form.load()"
    >
        <FormBox :form="form" @save="saved" @cancel="canceled">
            <FormString :form="form" name="lastname" :clearable="true" autocomplete="off"/>
            <FormString :form="form" name="firstname" :clearable="true" autocomplete="off"/>
            <FormString :form="form" name="patronymic" :clearable="true" autocomplete="off"/>
            <FormString :form="form" name="display_name" :clearable="true" autocomplete="off"/>
            <FormPhone :form="form" name="phone" :clearable="true" autocomplete="off"/>
            <GuiBreak/>
            <FormDictionary :form="form" name="status_id" dictionary="user_statuses"/>
            <FormString :form="form" name="email" :clearable="true" autocomplete="off"/>
            <template v-if="form.values['email'] !== form.originals['email']">
                <FormCheckBox :form="form" name="email_confirmation_need"/>
                <FieldWrapper v-if="form.values['email_confirmation_need']">
                    <GuiHint v-if="userId">* На указанную электронную почту будет отправлено письмо со ссылкой для подтверждения адреса. Адрес электронной почты поменяется на новый
                        только после его подтверждения.
                    </GuiHint>
                    <GuiHint v-else>* На указанную электронную почту будет отправлено письмо со ссылкой для подтверждения адреса. Адрес электронной почты станет действительным
                        только после его подтверждения.
                    </GuiHint>
                </FieldWrapper>
            </template>
            <FormString :form="form" name="username" :clearable="true" autocomplete="off"/>
            <FieldString v-if="userId" title="Пароль" :disabled="true" :model-value="form.payload['has_password'] ? '******' : 'Не задан'"/>
            <FormPassword :form="form" name="new_password" :clearable="true" :disabled="form.values['clear_password']" autocomplete="new-password"/>
            <FieldWrapper>
                <GuiHint v-if="userId">* Введите новый пароль для того, чтобы его изменить. Чтобы пароль остался прежним, оставьте поле пустым.</GuiHint>
                <GuiHint v-else>* Введите новый пароль для учётной записи. Если оставить поле пустым, по пользователь не сможет войти в систему.</GuiHint>
            </FieldWrapper>
            <FormCheckBox v-if="userId" :form="form" name="clear_password"/>
        </FormBox>
    </LayoutPage>
</template>

<script setup lang="ts">
import LayoutPage from "@/Components/Layout/LayoutPage.vue";
import {ref} from "vue";
import {Form, FormResponse} from "@/Core/Form";
import FormString from "@/Components/Form/FormString.vue";
import FormBox from "@/Components/Form/FormBox.vue";
import {useRouter} from "vue-router";
import FormDictionary from "@/Components/Form/FormDictionary.vue";
import GuiBreak from "@/Components/GUI/GuiBreak.vue";
import FormCheckBox from "@/Components/Form/FormCheckBox.vue";
import FormPassword from "@/Components/Form/FormPassword.vue";
import FieldWrapper from "@/Components/Fields/Helpers/FieldWrapper.vue";
import GuiHint from "@/Components/GUI/GuiHint.vue";
import FieldString from "@/Components/Fields/FieldString.vue";
import FormPhone from "@/Components/Form/FormPhone.vue";
import {apiEndPoint} from "@/Core/Http/ApiEndPoints";

const props = defineProps<{
    userId?: number,
}>();

const router = useRouter();

const form = ref<Form>(new Form({
    load_url: apiEndPoint('get', '/api/system/users/user/{userID}', {userID: props.userId ?? null}),
    save_url: apiEndPoint('put', '/api/system/users/user/{userID}', {userID: props.userId ?? null})
}));

form.value.load();

function saved(response: FormResponse): void {
    router.push({name: 'user_view', params: {id: response.payload?.id}});
}

function canceled(): void {
    if (router.options.history.state.back) {
        router.back();
    } else if (props.userId) {
        router.push({name: 'user_view', params: {id: props.userId}});
    } else {
        router.push({name: 'users'});
    }
}
</script>
