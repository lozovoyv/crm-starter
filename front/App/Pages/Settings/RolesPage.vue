<template>
    <LayoutPage title="Роли и права">
        <template v-slot:actions>
            <GuiActionsMenu title="Действия">
                <GuiLink name="Добавить роль" @click="create()"/>
            </GuiActionsMenu>
        </template>

        <GuiTabs v-model="tab" :tabs="tabs" tab-key="tab"/>

        <Roles v-if="tab === 'roles'"/>
        <Permissions v-if="tab === 'permissions'"/>
        <RolesHistory v-if="tab === 'history'"/>

    </LayoutPage>
</template>

<script setup lang="ts">
import LayoutPage from "@/Components/Layout/LayoutPage.vue";
import GuiActionsMenu from "@/Components/GUI/GuiActionsMenu.vue";
import GuiLink from "@/Components/GUI/GuiLink.vue";
import GuiTabs from "@/Components/GUI/GuiTabs.vue";
import {computed, ref} from "vue";
import Roles from "@/App/Pages/Settings/Roles.vue";
import RolesHistory from "@/App/Pages/Settings/RolesHistory.vue";
import Permissions from "@/App/Pages/Settings/Permissions.vue";

const tab = ref<string | null>(null);

const tabs = computed((): { [index: string]: string } => {
    return {roles: 'Роли', permissions: 'Права', history: 'История'};
});

function create(): void {
    console.log('create role');
}
</script>
