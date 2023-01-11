<template>
    <LayoutPage :title="form.title"
                :is-forbidden="form.is_forbidden"
                :is-not-found="form.is_not_found"
                :breadcrumbs="[
                    {name: 'Учётные записи', route: {name: 'users'}},
                    {name: form.title, route: userId !== null ? {name: 'user_view', params: {id: userId}} : null},
                ]"
    >
        <FormBox :form="form" @save="saved" @cancel="canceled">
            <FormString :form="form" name="lastname" :clearable="true"/>
            <FormString :form="form" name="firstname" :clearable="true"/>
            <FormString :form="form" name="patronymic" :clearable="true"/>
            <FormString :form="form" name="display_name" :clearable="true"/>
            <GuiBreak/>
            <FormDictionary :form="form" name="status_id" dictionary="user_statuses"/>
            <FormString :form="form" name="username" :clearable="true"/>
            <FormString :form="form" name="email" :clearable="true"/>
            <FormCheckBox :form="form" name="email_confirmation_need" v-if="form.values['email'] !== form.originals['email']"/>
            <FormPassword :form="form" name="new_password" :clearable="true"/>
            <FormString :form="form" name="phone" :clearable="true"/>
        </FormBox>
    </LayoutPage>
</template>

<script setup lang="ts">
import LayoutPage from "@/Components/Layout/LayoutPage.vue";
import {ref} from "vue";
import {Form} from "@/Core/Form";
import FormString from "@/Components/Form/FormString.vue";
import FormBox from "@/Components/Form/FormBox.vue";
import {useRouter} from "vue-router";
import FormDictionary from "@/Components/Form/FormDictionary.vue";
import GuiBreak from "@/Components/GUI/GuiBreak.vue";
import FormCheckBox from "@/Components/Form/FormCheckBox.vue";
import FormPassword from "@/Components/Form/FormPassword.vue";

const props = defineProps<{
    userId?: number,
}>();

const router = useRouter();

const form = ref<Form>(new Form(null, '/api/system/users/get', '/api/system/users/update', {
    user_id: props.userId ? props.userId : 0
}));

form.value.load();

function saved(response: { values: object, payload: { id: number } }): void {
    router.push({name: 'user_view', params: {id: response.payload['id']}});
}

function canceled(): void {
    if (props.userId) {
        router.push({name: 'user_view', params: {id: props.userId}});
    } else {
        router.push({name: 'users'});
    }
}
</script>
