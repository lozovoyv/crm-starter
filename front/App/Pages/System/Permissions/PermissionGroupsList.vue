<template>
    <ListTable :list="roles" :actions="true">
        <template v-slot:filters>
            <ListFilterDropdown :list="roles" title="Статус" name="active" :has-null="true" placeholder="Все"
                                :options="[{id: true,name: 'Включенные'}, {id: false,name: 'Отключенные'}]"
            />
        </template>
        <template v-slot:search>
            <ListSearch :list="roles" title="Поиск" placeholder="ID, название"/>
        </template>
        <ListTableRow v-for="role in roles.list">
            <ListTableCell v-html="highlight(role.id, roles.search)"/>
            <ListTableCell style="text-align: center">
                <span v-if="role.locked" style="width: 10px; display: inline-block;" title="Системная роль"><IconLock/></span>
                <GuiIndicator v-else :active="role.active" style="margin-right: 0"/>
            </ListTableCell>
            <ListTableCell style="white-space: nowrap" v-html="highlight(role.name, roles.search)"/>
            <ListTableCell style="text-align: center">{{ role.count ? role.count : '—' }}</ListTableCell>
            <ListTableCell style="width: 100%;">{{ role.description }}</ListTableCell>
            <ListTableCell style="white-space: nowrap">{{ toDatetime(role.updated_at, true) }}</ListTableCell>
            <ListTableCell :action="true">
                <ListActions v-if="!role.locked">
                    <GuiLink @click="edit(role)">Редактировать</GuiLink>
                    <GuiLink @click="edit(null, role)">Создать копию</GuiLink>
                    <GuiLink @click="deactivate(role)" v-if="role.active">Отключить</GuiLink>
                    <GuiLink @click="activate(role)" v-if="!role.active">Включить</GuiLink>
                    <GuiLink @click="remove(role)">Удалить</GuiLink>
                </ListActions>
            </ListTableCell>
        </ListTableRow>
        <template v-slot:empty>
            Группы прав не найдены
        </template>
    </ListTable>

    <!--    <RoleEditForm ref="form"/>-->

</template>

<script setup lang="ts">
import GuiLink from "@/Components/GUI/GuiLink.vue";
import {ref} from "vue";
import {List} from "@/Core/List";
import ListTable from "@/Components/List/ListComponent.vue";
import ListTableRow from "@/Components/List/ListRow.vue";
import ListTableCell from "@/Components/List/ListCell.vue";
import IconLock from "@/Icons/IconLock.vue";
import GuiIndicator from "@/Components/GUI/GuiIndicator.vue";
import ListActions from "@/Components/List/ListActions.vue";
import {processEntry} from "@/Core/Helpers/ProcessEntry";
import dialog from "@/Core/Dialog/Dialog";
import {toDatetime} from "@/Core/Helpers/DateTime";
import ListSearch from "@/Components/List/ListSearch.vue";
import {highlight} from "@/Core/Highlight/highlight";
import ListFilterDropdown from "@/Components/List/ListFilterDropdown.vue";
// import RoleEditForm from "@/App/Pages/System/Permissions/RoleEditForm.vue";

// const form = ref<InstanceType<typeof RoleEditForm> | undefined>(undefined);

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

const roles = ref<List<Role>>(new List<Role>('/api/system/permissions/groups', {}, {
    prefix: 'system_roles', remember: {filters: ['active'], pagination: true, order: true}
}));

roles.value.load();

function edit(role: Role | null, fromRole: Role | null = null): void {
//     form.value?.show(role ? role.id : 0, fromRole ? fromRole.id : null)
//         ?.then(() => {
//             reload();
//         });
}

const processing = ref<boolean>(false);

function deactivate(role: Role): void {
    processEntry('Отключение', `Отключить роль "${role.name}"?`, dialog.button('yes', 'Отключить', 'default'),
        '/api/system/roles/deactivate', {role_id: role.id, role_hash: role.hash},
        p => processing.value = p
    ).then(() => {
        reload();
    });
}

function activate(role: Role): void {
    processEntry('Включение', `Включить роль "${role.name}"?`, dialog.button('yes', 'Включить', 'default'),
        '/api/system/roles/activate', {role_id: role.id, role_hash: role.hash},
        p => processing.value = p
    ).then(() => {
        reload();
    });
}

function remove(role: Role): void {
    processEntry('Удаление', `Удалить роль "${role.name}"?`, dialog.button('yes', 'Удалить', 'error'),
        '/api/system/roles/remove', {role_id: role.id, role_hash: role.hash},
        p => processing.value = p
    ).then(() => {
        reload();
    });
}

function reload(): void {
    roles.value.reload();
}

defineExpose({
    reload,
})
</script>
