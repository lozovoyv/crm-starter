<template>
    <ListTable :list="groups" :actions="true">
        <template v-slot:filters>
            <ListFilterDropdown :list="groups" title="Статус" name="active" :has-null="true" placeholder="Все"
                                :options="[{id: true,name: 'Включенные'}, {id: false,name: 'Отключенные'}]"
            />
        </template>
        <template v-slot:search>
            <ListSearch :list="groups" title="Поиск" placeholder="ID, название"/>
        </template>
        <ListTableRow v-for="group in groups.list">
            <ListTableCell v-html="highlight(group.id, groups.search)"/>
            <ListTableCell style="text-align: center">
                <span v-if="group.locked" style="width: 10px; display: inline-block;" title="Системная роль"><IconLock/></span>
                <GuiIndicator v-else :active="group.active" style="margin-right: 0"/>
            </ListTableCell>
            <ListTableCell style="white-space: nowrap" v-html="highlight(group.name, groups.search)"/>
            <ListTableCell style="text-align: center">{{ group.count ? group.count : '—' }}</ListTableCell>
            <ListTableCell style="width: 100%;">{{ group.description }}</ListTableCell>
            <ListTableCell style="white-space: nowrap">{{ toDatetime(group.updated_at, true) }}</ListTableCell>
            <ListTableCell :action="true">
                <ListActions v-if="!group.locked">
                    <GuiLink @click="edit(group)">Редактировать</GuiLink>
                    <GuiLink @click="edit(null, group)">Создать копию</GuiLink>
                    <GuiLink @click="deactivate(group)" v-if="group.active">Отключить</GuiLink>
                    <GuiLink @click="activate(group)" v-if="!group.active">Включить</GuiLink>
                    <GuiLink @click="remove(group)">Удалить</GuiLink>
                </ListActions>
            </ListTableCell>
        </ListTableRow>
        <template v-slot:empty>
            Группы прав не найдены
        </template>
    </ListTable>

    <PermissionGroupEditForm ref="form"/>

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
import PermissionGroupEditForm from "@/App/Pages/System/Permissions/parts/PermissionGroupEditForm.vue";

const form = ref<InstanceType<typeof PermissionGroupEditForm> | undefined>(undefined);

type PermissionGroup = {
    id: number,
    name: string,
    count: number | null,
    description: string,
    active: boolean,
    locked: boolean,
    hash: string | null,
    updated_at: string,
};

const groups = ref<List<PermissionGroup>>(new List<PermissionGroup>('/api/system/permissions/groups', {}, {
    prefix: 'system_permissions_groups', remember: {filters: ['active'], pagination: true, order: true}
}));

groups.value.load();

function edit(group: PermissionGroup | null, fromGroup: PermissionGroup | null = null): void {
    form.value?.show(group ? group.id : undefined, fromGroup ? fromGroup.id : null)
        ?.then(() => {
            reload();
        });
}

const processing = ref<boolean>(false);

function deactivate(group: PermissionGroup): void {
    processEntry({
        title: 'Отключение',
        question: `Отключить группу "${group.name}"?`,
        button: dialog.button('yes', 'Отключить', 'default'),
        method: 'put',
        url: `/api/system/permissions/group/${group.id}/status`,
        options: {disable: true, hash: group.hash},
        progress: p => processing.value = p
    }).then(() => {
        reload();
    });
}

function activate(group: PermissionGroup): void {
    processEntry({
        title: 'Включение',
        question: `Включить группу "${group.name}"?`,
        button: dialog.button('yes', 'Включить', 'default'),
        method: 'put',
        url: `/api/system/permissions/group/${group.id}/status`,
        options: {hash: group.hash},
        progress: p => processing.value = p
    }).then(() => {
        reload();
    });
}

function remove(group: PermissionGroup): void {
    processEntry({
        title: 'Удаление',
        question: `Удалить группу "${group.name}"?`,
        button: dialog.button('yes', 'Удалить', 'error'),
        method: 'delete',
        url: `/api/system/permissions/group/${group.id}`,
        options: {hash: group.hash},
        progress: p => processing.value = p
    }).then(() => {
        reload();
    });
}

function reload(): void {
    groups.value.reload();
}

defineExpose({
    reload,
})
</script>
