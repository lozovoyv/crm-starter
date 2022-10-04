<template>
    <ListTable :list="roles" :actions="true">
        <template v-slot:search>
            <ListSearch :list="roles" placeholder="ID, название"/>
        </template>
        <ListTableRow v-for="role in roles.list">
            <ListTableCell v-html="highlight(role.id, roles.search)"/>
            <ListTableCell style="text-align: center">
                <span v-if="role.locked" style="width: 10px; display: inline-block;" title="Системная роль"><IconLock/></span>
                <GuiIndicator v-else :active="role.active" style="margin-right: 0"/>
            </ListTableCell>
            <ListTableCell v-html="highlight(role.name, roles.search)"/>
            <ListTableCell style="text-align: center">{{ role.count ? role.count : '—' }}</ListTableCell>
            <ListTableCell style="width: 100%;">{{ role.description }}</ListTableCell>
            <ListTableCell style="white-space: nowrap">{{ DateTime.toDatetime(role.updated_at, true) }}</ListTableCell>
            <ListTableCell :action="true">
                <ListActionsMenu v-if="!role.locked">
                    <GuiLink @click="edit(role)">Редактировать</GuiLink>
                    <GuiLink @click="deactivate(role)" v-if="role.active">Отключить</GuiLink>
                    <GuiLink @click="activate(role)" v-if="!role.active">Включить</GuiLink>
                    <GuiLink @click="remove(role)">Удалить</GuiLink>
                </ListActionsMenu>
            </ListTableCell>
        </ListTableRow>
        <template v-slot:empty>
            Пока не добавлено ни одной роли
        </template>
    </ListTable>
</template>

<script setup lang="ts">
import GuiLink from "@/Components/GUI/GuiLink.vue";
import {ref} from "vue";
import {List} from "@/Core/List";
import ListTable from "@/Components/List/ListTable.vue";
import ListTableRow from "@/Components/List/ListTableRow.vue";
import ListTableCell from "@/Components/List/ListTableCell.vue";
import IconLock from "@/Icons/IconLock.vue";
import GuiIndicator from "@/Components/GUI/GuiIndicator.vue";
import ListActionsMenu from "@/Components/List/ListActionsMenu.vue";
import {processEntry} from "@/Core/Helpers/ProcessEntry";
import dialog from "@/Core/Dialog/Dialog";
import {DateTime} from "@/Core/Helpers/DateTime";
import ListSearch from "@/Components/List/ListSearch.vue";
import {highlight} from "@/Core/Highlight/highlight";

type Role = {
    id: number,
    name: string,
    count: number | null,
    description: string,
    active: boolean,
    locked: boolean,
    hash: string | null,
    updated_at: string,
};

const roles = ref<List<Role>>(new List<Role>('/api/settings/roles', {}, {
    prefix: 'settings_roles', remember: {filters: ['active'], pagination: true, order: true}
}));

roles.value.initial();

function edit(role: Role | null): void {
    console.log(role);
}

const processing = ref<boolean>(false);

function deactivate(role: Role): void {
    processEntry('Отключение', `Отключить роль "${role.name}"?`, dialog.button('yes', 'Отключить', 'default'),
        '/api/settings/roles/deactivate', {role_id: role.id, role_hash: role.hash},
        p => processing.value = p
    ).then(() => {
        roles.value.reload();
    });
}

function activate(role: Role): void {
    processEntry('Включение', `Включить роль "${role.name}"?`, dialog.button('yes', 'Включить', 'default'),
        '/api/settings/roles/activate', {role_id: role.id, role_hash: role.hash},
        p => processing.value = p
    ).then(() => {
        roles.value.reload();
    });
}

function remove(role: Role): void {
    processEntry('Удаление', `Удалить роль "${role.name}"?`, dialog.button('yes', 'Удалить', 'error'),
        '/api/settings/roles/remove', {role_id: role.id, role_hash: role.hash},
        p => processing.value = p
    ).then(() => {
        roles.value.reload();
    });
}
</script>
