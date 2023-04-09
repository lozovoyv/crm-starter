<template>
    <ListTable :list="users" :actions="canChange">
        <template v-slot:filters>
            <ListFilterDictionary :list="users" title="Статус учётной записи" name="status_id" dictionary="user_statuses" :has-null="true" placeholder="Все"/>
        </template>
        <template v-slot:search>
            <ListSearch :list="users" title="Поиск" placeholder="Поиск"/>
        </template>
        <ListTableRow v-for="user in users.list">
            <ListTableCell v-html="highlight(user.id, users.search)"/>
            <ListTableCell/>
            <ListTableCell>
                <GuiAccessIndicator :locked="!user.is_active"/>
                <GuiLink :route="{name: 'user_view', params: {id: user.id}}">
                    <span v-html="highlight(user.lastname, users.search)"/> <span v-html="highlight(user.firstname, users.search)"/> <span
                    v-html="highlight(user.patronymic, users.search)"/>
                </GuiLink>
            </ListTableCell>
            <ListTableCell v-html="highlight(user.display_name, users.search)"/>
            <ListTableCell v-html="highlight(user.username, users.search)"/>
            <ListTableCell v-html="highlight(user.email, users.search)"/>
            <ListTableCell v-html="highlight(formatPhone(user.phone), users.search)"/>
            <ListTableCell>{{ toDatetime(user.created_at) }}</ListTableCell>
            <ListTableCell>{{ toDatetime(user.updated_at) }}</ListTableCell>
            <ListTableCell :action="true" v-if="canChange">
                <ListActions>
                    <GuiLink :route="{name: 'user_edit', params: {id: user.id}}">Редактировать</GuiLink>
                    <GuiLink @click="block(user)" v-if="user.is_active">Заблокировать</GuiLink>
                    <GuiLink @click="activate(user)" v-if="!user.is_active">Активировать</GuiLink>
                    <GuiLink @click="remove(user)">Удалить</GuiLink>
                </ListActions>
            </ListTableCell>
        </ListTableRow>
    </ListTable>
</template>

<script setup lang="ts">
import {computed, ref} from "vue";
import {List} from "@/Core/List";
import ListTable from "@/Components/List/ListComponent.vue";
import ListTableRow from "@/Components/List/ListRow.vue";
import ListTableCell from "@/Components/List/ListCell.vue";
import {toDatetime} from "@/Core/Helpers/DateTime";
import ListSearch from "@/Components/List/ListSearch.vue";
import {highlight} from "@/Core/Highlight/highlight";
import ListFilterDictionary from "@/Components/List/ListFilterDictionary.vue";
import {User} from "@/App/types";
import GuiAccessIndicator from "@/Components/GUI/GuiAccessIndicator.vue";
import ListActions from "@/Components/List/ListActions.vue";
import GuiLink from "@/Components/GUI/GuiLink.vue";
import {can} from "@/Core/Can";
import {processEntry} from "@/Core/Helpers/ProcessEntry";
import dialog from "@/Core/Dialog/Dialog";
import {formatPhone} from "@/Core/Helpers/Phone";

const users = ref<List<User>>(new List<User>('/api/system/users', {}, {
    prefix: 'system_users', remember: {filters: ['status_id'], pagination: true, order: true}
}));

const canChange = computed((): boolean => {
    return can('system.users.change');
})

users.value.initial();

function reload(): void {
    users.value.reload();
}

const processing = ref<boolean>(false);

function block(user: User): void {
    let name = String([user.lastname, user.firstname, user.patronymic].join(' ')).trim();
    processEntry('Блокировка', `Заблокировать учётную запись "${name}"?`, dialog.button('yes', 'Заблокировать', 'default'),
        '/api/system/users/deactivate', {user_id: user.id, user_hash: user.hash},
        p => processing.value = p
    ).then(() => {
        reload();
    });
}

function activate(user: User): void {
    let name = String([user.lastname, user.firstname, user.patronymic].join(' ')).trim();
    processEntry('Активация', `Активировать учётную запись "${name}"?`, dialog.button('yes', 'Активировать', 'default'),
        '/api/system/users/activate', {user_id: user.id, user_hash: user.hash},
        p => processing.value = p
    ).then(() => {
        reload();
    });
}

function remove(user: User): void {
    let name = String([user.lastname, user.firstname, user.patronymic].join(' ')).trim();
    processEntry('Удаление', `Удалить учётную запись "${name}"?`, dialog.button('yes', 'Удалить', 'default'),
        '/api/system/users/remove', {user_id: user.id, user_hash: user.hash},
        p => processing.value = p
    ).then(() => {
        reload();
    });
}

defineExpose({
    reload,
});
</script>
