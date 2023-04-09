<template>
    <LayoutPage :title="staff.title"
                :is-forbidden="staff.is_forbidden"
                :is-not-found="staff.is_not_found"
                :breadcrumbs="[
                    {name: 'Сотрудники', route: {name: 'staff'}},
                    {name: staff.title},
                ]"
    >

        <FormBox :form="staff" :save-disabled="staff.values['mode'] === null" @save="saved" @cancel="canceled" @clear="cleared" save-button="Зарегистрировать">
            <div style="width: 50%; min-width: 350px; display: inline-block; vertical-align: top">

                <FormDropdown :form="staff" name="mode"
                              placeholder="Выберите"
                              :options="{new: 'Создать новую учётную запись', existing: 'Добавить существующую учётную запись'}"
                              @change="modeChanged"
                />

                <template v-if="staff.values['mode'] === 'new'">
                    <FormString :form="staff" name="lastname" :disabled="staff.values['user_id'] !== null" autocomplete="nope" :clearable="true"/>
                    <FormString :form="staff" name="firstname" :disabled="staff.values['user_id'] !== null" autocomplete="nope" :clearable="true"/>
                    <FormString :form="staff" name="patronymic" :disabled="staff.values['user_id'] !== null" autocomplete="nope" :clearable="true"/>
                    <FormString :form="staff" name="display_name" :disabled="staff.values['user_id'] !== null" autocomplete="nope" :clearable="true"/>
                    <FormString :form="staff" name="email" :disabled="staff.values['user_id'] !== null" autocomplete="nope" :clearable="true"/>
                    <FormPhone :form="staff" name="phone" :disabled="staff.values['user_id'] !== null" autocomplete="nope" :clearable="true"/>
                    <FormString :form="staff" name="username" :disabled="staff.values['user_id'] !== null" autocomplete="nope" :clearable="true"/>
                    <FormPassword :form="staff" name="password" v-if="staff.values['user_id'] === null" autocomplete="new-password" :clearable="true"/>
                </template>
                <template v-if="staff.values['mode'] === 'existing'">
                    <FormDictionary :form="staff" dictionary="users" name="user_id" :search="true"
                                    placeholder="Выберите учётную запись"
                                    empty-caption="Ничего не найдено"
                                    :empty-title="true"
                                    :clearable="true"
                                    @change="userSelected"
                    />
                    <FieldWrapper v-if="selectedUser">
                        <div>
                            <GuiValue dots title="Фамилия:">{{ selectedUser.lastname }}</GuiValue>
                            <GuiValue dots title="Имя:">{{ selectedUser.firstname }}</GuiValue>
                            <GuiValue dots title="Отчество:">{{ selectedUser.patronymic }}</GuiValue>
                            <GuiValue dots title="Отображаемое имя:">{{ selectedUser.display_name }}</GuiValue>
                            <GuiValue dots title="Статус учётной записи:">
                                <GuiAccessIndicator :locked="!selectedUser.is_active"/>
                                {{ selectedUser.status }}
                            </GuiValue>
                            <GuiValue dots title="Пароль для входа:">
                                {{ selectedUser.has_password ? '******' : 'не задан' }}
                            </GuiValue>
                            <GuiValue dots title="Логин:">{{ selectedUser.username }}</GuiValue>
                            <GuiValue dots title="Адрес электронной почты:">{{ selectedUser.email }}</GuiValue>
                            <GuiValue dots title="Телефон:">{{ formatPhone(selectedUser.phone) }}</GuiValue>
                        </div>
                    </FieldWrapper>
                </template>
                <template v-if="staff.values['mode'] !== null">
                    <GuiTitle>Параметры:</GuiTitle>
                    <FormDictionary :form="staff" dictionary="position_statuses" name="status_id"/>
                    <FormDictionary :form="staff" dictionary="roles" name="roles" multi search/>
                </template>
            </div>
            <div style="width: 50%; display: inline-block; vertical-align: top">

            </div>
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
import GuiTitle from "@/Components/GUI/GuiTitle.vue";
import FormPhone from "@/Components/Form/FormPhone.vue";
import FormPassword from "@/Components/Form/FormPassword.vue";
import {useRouter} from "vue-router";
import GuiValue from "@/Components/GUI/GuiValue.vue";
import GuiAccessIndicator from "@/Components/GUI/GuiAccessIndicator.vue";
import {formatPhone} from "@/Core/Helpers/Phone";
import FieldWrapper from "@/Components/Fields/Helpers/FieldWrapper.vue";
import FormDropdown from "@/Components/Form/FormDropdown.vue";

const staff = ref<Form>(new Form('Добавление сотрудника', '/api/system/staff/get', '/api/system/staff/create'));

const router = useRouter();

staff.value.load();

const selectedUser = ref<{
    created_at: string,
    email?: string,
    firstname?: string,
    has_password: boolean,
    lastname?: string
    patronymic?: string
    phone?: string
    updated_at: string,
    username?: string,
    display_name?: string,
    is_active: boolean,
    status: string,
} | null>(null);

function userSelected(userId: string | number | boolean | null | Array<string | number>, name: string, payload: any): void {
    selectedUser.value = payload ? payload['info'] : null;
}

function modeChanged(): void {
    if (staff.value) {
        staff.value.clear(['mode']);
    }
    selectedUser.value = null;
}

function cleared(): void {
    selectedUser.value = null;
}

function saved(response: { values: { [index: string]: any }, payload: { [index: string]: any } }): void {
    router.push({name: 'staff_view', params: {id: response.payload.id}});
}

function canceled(): void {
    router.push({name: 'staff'});
}
</script>
