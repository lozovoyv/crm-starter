<template>
    <ListTable :list="permissions">
        <template v-slot:search>
            <ListSearch :list="permissions" placeholder="Ключ, название"/>
        </template>
        <ListTableRow v-for="permission in permissions.list">
            <ListTableCell>{{ permission.scope }}</ListTableCell>
            <ListTableCell v-html="highlight(permission.name, permissions.search)"/>
            <ListTableCell>{{ permission.description }}</ListTableCell>
            <ListTableCell style="white-space: nowrap" v-html="highlight(permission.key, permissions.search)"/>
        </ListTableRow>
        <template v-slot:empty>
            Права не найдены
        </template>
    </ListTable>
</template>

<script setup lang="ts">
import {ref} from "vue";
import {List} from "@/Core/List";
import ListTable from "@/Components/List/ListComponent.vue";
import ListTableRow from "@/Components/List/ListRow.vue";
import ListTableCell from "@/Components/List/ListCell.vue";
import ListSearch from "@/Components/List/ListSearch.vue";
import {highlight} from "@/Core/Highlight/highlight";

type Permission = {
    key: string,
    scope: string,
    name: string,
    description: string | null,
};

const permissions = ref<List<Permission>>(new List<Permission>('/api/system/permissions', {}, {without_pagination: true}));

permissions.value.load();
</script>
