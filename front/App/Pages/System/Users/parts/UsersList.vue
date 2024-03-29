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
            <ListTableCell :nowrap="true">
                <div>
                    <GuiAccessIndicator :locked="!user.is_active"/>
                    <GuiLink :route="{name: 'user_view', params: {id: user.id}}">
                        <span v-html="highlight(user.lastname, users.search)"/> <span v-html="highlight(user.firstname, users.search)"/> <span
                        v-html="highlight(user.patronymic, users.search)"/>
                    </GuiLink>
                </div>
                <div v-html="highlight(user.display_name, users.search)"/>
            </ListTableCell>
            <ListTableCell>
                <div v-html="highlight(user.email ?? '—', users.search)"/>
                <div v-html="highlight(user.username, users.search)"/>
            </ListTableCell>
            <ListTableCell v-html="highlight(formatPhone(user.phone), users.search)" style="white-space: nowrap"/>
            <ListTableCell>{{ toDatetime(user.created_at) }}</ListTableCell>
            <ListTableCell>{{ toDatetime(user.updated_at) }}</ListTableCell>
            <ListTableCell :action="true" v-if="canChange">
                <ListActions v-if="!user.locked">
                    <GuiLink :route="{name: 'user_edit', params: {id: user.id}}">Редактировать</GuiLink>
                    <GuiLink @click="block(user, true)" v-if="user.is_active">Заблокировать</GuiLink>
                    <GuiLink @click="block(user, false)" v-if="!user.is_active">Активировать</GuiLink>
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
import {apiEndPoint} from "@/Core/Http/ApiEndPoints";

const users = ref<List<User>>(new List<User>({
    load_url: apiEndPoint('get', '/api/system/users'),
    remember: {
        prefix: 'system_users',
        filters: ['status_id'],
        pagination: true,
        order: true
    }
}));

const canChange = computed((): boolean => {
    return can('system__users_change');
})

users.value.load();

function reload(): void {
    users.value.reload();
}

const processing = ref<boolean>(false);

function block(user: User, disabled: boolean): void {
    let name = String([user.lastname, user.firstname, user.patronymic].join(' ')).trim();
    processEntry({
        title: disabled ? 'Блокировка' : 'Активация',
        question: (disabled ? 'Заблокировать' : 'Активировать') + ` учётную запись "${name}"?`,
        button: dialog.button('yes', disabled ? 'Заблокировать' : 'Активировать', 'default'),
        url: apiEndPoint('patch', '/api/system/users/user/{userID}/status', {userID: user.id}),
        options: {disabled: disabled, hash: user.hash},
        progress: p => processing.value = p
    }).then(() => {
        reload();
    });
}

function remove(user: User): void {
    const name = String([user.lastname, user.firstname, user.patronymic].join(' ')).trim();
    processEntry({
        title: 'Удаление',
        question: `Удалить учётную запись "${name}"?`,
        button: dialog.button('yes', 'Удалить', 'error'),
        url: apiEndPoint('delete', '/api/system/users/user/{userID}', {userID: user.id}),
        options: {hash: user.hash},
        progress: p => processing.value = p
    }).then(() => {
        reload();
    });
}

defineExpose({
    reload,
});
</script>
