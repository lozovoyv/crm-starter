<template>
    <ListTable :list="staffs" :actions="false">
        <template v-slot:search>
            <ListSearch :list="staffs" title="Поиск" placeholder="Поиск"/>
        </template>
        <template v-slot:filters>
            <ListFilterDictionary :list="staffs" title="Статус" name="status_id" dictionary="position_statuses" :has-null="true" placeholder="Все"/>
        </template>
        <ListTableRow v-for="staff in staffs.list">
            <ListTableCell v-html="highlight(staff.id, staffs.search)"/>
            <ListTableCell/>
            <ListTableCell>
                <GuiLink :route="{name: 'staff_view', params: {id: staff.id}}">
                    <span v-html="highlight(staff.user.lastname, staffs.search)"/> <span v-html="highlight(staff.user.firstname, staffs.search)"/> <span
                    v-html="highlight(staff.user.patronymic, staffs.search)"/>
                </GuiLink>
            </ListTableCell>
            <ListTableCell v-html="highlight(staff.user.username, staffs.search)"/>
            <ListTableCell v-html="highlight(staff.user.email, staffs.search)"/>
            <ListTableCell v-html="highlight(staff.user.phone, staffs.search)"/>
            <ListTableCell>{{ toDatetime(staff.created_at) }}</ListTableCell>
            <ListTableCell>{{ toDatetime(staff.updated_at) }}</ListTableCell>
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
import ListTable from "@/Components/List/ListComponent.vue";
import ListTableRow from "@/Components/List/ListRow.vue";
import ListTableCell from "@/Components/List/ListCell.vue";
import ListSearch from "@/Components/List/ListSearch.vue";
import ListFilterDictionary from "@/Components/List/ListFilterDictionary.vue";
import {ref} from "vue";
import {List} from "@/Core/List";
import {toDatetime} from "@/Core/Helpers/DateTime";
import {highlight} from "@/Core/Highlight/highlight";
import {Position} from "@/App/types";
import GuiLink from "@/Components/GUI/GuiLink.vue";

const staffs = ref<List<Position>>(new List<Position>('/api/system/staff', {}, {
    prefix: 'system_staffs', remember: {filters: ['status_id'], pagination: true, order: true}
}));

staffs.value.initial();

function reload(): void {
    staffs.value.reload();
}

defineExpose({
    reload,
});
</script>
