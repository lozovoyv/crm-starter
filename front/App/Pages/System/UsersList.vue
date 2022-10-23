<template>
    <ListTable :list="users" :actions="false">
        <template v-slot:filters>
            <ListFilterDictionary :list="users" title="Статус" name="status_id" dictionary="user_statuses" :has-null="true" placeholder="Все"/>
        </template>
        <template v-slot:search>
            <ListSearch :list="users" title="Поиск" placeholder="Поиск"/>
        </template>
        <ListTableRow v-for="user in users.list">
            <ListTableCell v-html="highlight(user.id, users.search)"/>
            <ListTableCell/>
            <ListTableCell>
                <span v-html="highlight(user.lastname, users.search)"/> <span v-html="highlight(user.firstname, users.search)"/> <span
                v-html="highlight(user.patronymic, users.search)"/>
            </ListTableCell>
            <ListTableCell v-html="highlight(user.username, users.search)"/>
            <ListTableCell v-html="highlight(user.email, users.search)"/>
            <ListTableCell v-html="highlight(user.phone, users.search)"/>
            <ListTableCell>{{ toDatetime(user.created_at) }}</ListTableCell>
            <ListTableCell>{{ toDatetime(user.updated_at) }}</ListTableCell>
            <!--            <ListTableCell :action="true">-->
            <!--                <ListActionsMenu v-if="!user.locked">-->
            <!--                    <GuiLink @click="edit(null, role)">Создать копию</GuiLink>-->
            <!--                    <GuiLink @click="deactivate(role)" v-if="role.active">Отключить</GuiLink>-->
            <!--                    <GuiLink @click="activate(role)" v-if="!role.active">Включить</GuiLink>-->
            <!--                    <GuiLink @click="edit(role)">Редактировать</GuiLink>-->
            <!--                    <GuiLink @click="remove(role)">Удалить</GuiLink>-->
            <!--                </ListActionsMenu>-->
            <!--            </ListTableCell>-->
        </ListTableRow>
        <template v-slot:empty>
            Роли не найдены
        </template>
    </ListTable>
</template>

<script setup lang="ts">
import {ref} from "vue";
import {List} from "@/Core/List";
import ListTable from "@/Components/List/ListComponent.vue";
import ListTableRow from "@/Components/List/ListRow.vue";
import ListTableCell from "@/Components/List/ListCell.vue";
import {toDatetime} from "@/Core/Helpers/DateTime";
import ListSearch from "@/Components/List/ListSearch.vue";
import {highlight} from "@/Core/Highlight/highlight";
import RoleEditForm from "@/App/Pages/System/RoleEditForm.vue";
import ListFilterDictionary from "@/Components/List/ListFilterDictionary.vue";

const form = ref<InstanceType<typeof RoleEditForm> | null>(null);

type User = {
    id: number,
    lastname: string,
    firstname: string,
    patronymic: string,
    username: string,
    email: string,
    phone: string,
    created_at: string,
    updated_at: string,
    hash: string | null,
};

const users = ref<List<User>>(new List<User>('/api/system/users', {}, {
    prefix: 'system_users', remember: {filters: ['status_id'], pagination: true, order: true}
}));

users.value.initial();

function reload(): void {
    users.value.reload();
}

defineExpose({
    reload,
})
</script>
