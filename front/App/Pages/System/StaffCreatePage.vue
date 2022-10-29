<template>
    <LayoutPage :title="staff.title" :is-forbidden="staff.is_forbidden" :is-not-found="staff.is_not_found">

        <template #notFound>Сотрудник не найден</template>

        <FormBox :form="staff">
            <FormDictionary :form="staff" dictionary="users" name="user_id" @change="userChanged" search has-null null-caption="Создать новую учётную запись"
                            placeholder="Новая учётная запись" empty-caption="Ничего не найдено"/>

            <FormString :form="staff" name="lastname" :disabled="staff.values['user_id'] !== null"/>
            <FormString :form="staff" name="firstname" :disabled="staff.values['user_id'] !== null"/>
            <FormString :form="staff" name="patronymic" :disabled="staff.values['user_id'] !== null"/>
            <FormString :form="staff" name="email" :disabled="staff.values['user_id'] !== null"/>
            <FormString :form="staff" name="phone" :disabled="staff.values['user_id'] !== null"/>
            <FormString :form="staff" name="username" :disabled="staff.values['user_id'] !== null"/>
            <FormString :form="staff" name="password" v-if="staff.values['user_id'] === null"/>
            <FormDictionary :form="staff" dictionary="position_status" name="status_id"/>
            <FormDictionary :form="staff" dictionary="roles" name="roles" multi search/>
        </FormBox>

    </LayoutPage>
</template>

<script setup lang="ts">
import LayoutPage from "@/Components/Layout/LayoutPage.vue";
import {ref} from "vue";
import FormDictionary from "@/Components/Form/FormDictionary.vue";
import {Form} from "@/Core/Form";
import FormString from "@/Components/Form/FormString.vue";
import FormBox from "@/Components/Form/FormBox.vue";

const staff = ref<Form>(new Form('Добавление сотрудника', '/api/system/staff/get', '/api/system/staff/create'));

staff.value.load();

function userChanged(userId: number | null, name: string, payload: any): void {
    staff.value.update('lastname', payload ? payload.lastname : null);
    staff.value.update('firstname', payload ? payload.firstname : null);
    staff.value.update('patronymic', payload ? payload.patronymic : null);
    staff.value.update('username', payload ? payload.username : null);
    staff.value.update('email', payload ? payload.email : null,);
    staff.value.update('phone', payload ? payload.phone : null);
    staff.value.update('password', null,);
}

</script>
