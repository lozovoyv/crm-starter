<template>
    <LayoutPage title="Роли и права">
        <template v-slot:actions>
            <GuiActionsMenu title="Действия">
                <GuiLink name="Добавить роль" @click="editRole(null)"/>
            </GuiActionsMenu>
        </template>

        <div v-for="role in roles.list">
            {{ role.id }} - {{ role.name }}
        </div>

        <ListPagination :list="roles"/>
    </LayoutPage>
</template>

<script setup lang="ts">
import LayoutPage from "@/Components/Layout/LayoutPage.vue";
import GuiActionsMenu from "@/Components/GUI/GuiActionsMenu.vue";
import GuiLink from "@/Components/GUI/GuiLink.vue";
import {ref} from "vue";
import {List} from "@/Core/List";
import ListPagination from "@/Components/List/ListPagination.vue";

type Role = {
    id: number,
    name: string,
    description: string,
    active: boolean,
    locked: boolean,
};

const roles = ref<List<Role>>(new List<Role>('/api/settings/roles', {}, {
    prefix: 'settings_roles',remember:{filters:['active']}
}));
roles.value.initial();

function editRole(role: Role | null): void {
    console.log(role);
}
</script>
