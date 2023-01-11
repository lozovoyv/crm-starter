<template>
    <LayoutPage title="Роли и права" :breadcrumbs="[{name: 'Роли и права'}]">
        <template v-slot:actions>
            <GuiActionsMenu title="Действия">
                <GuiLink name="Добавить роль" @click="create()"/>
            </GuiActionsMenu>
        </template>

        <GuiTabs v-model="tab" :tabs="tabs" tab-key="tab"/>

        <RolesList v-if="tab === 'roles'" ref="list"/>
        <Permissions v-if="tab === 'permissions'"/>
        <RolesHistory v-if="tab === 'history'" ref="history"/>

        <RoleEditForm ref="form"/>
    </LayoutPage>
</template>

<script setup lang="ts">
import LayoutPage from "@/Components/Layout/LayoutPage.vue";
import GuiActionsMenu from "@/Components/GUI/GuiActionsMenu.vue";
import GuiLink from "@/Components/GUI/GuiLink.vue";
import GuiTabs from "@/Components/GUI/GuiTabs.vue";
import {computed, ref} from "vue";
import RolesList from "@/App/Pages/System/Roles/RolesList.vue";
import RolesHistory from "@/App/Pages/System/Roles/RolesHistory.vue";
import Permissions from "@/App/Pages/System/Roles/Permissions.vue";
import RoleEditForm from "@/App/Pages/System/Roles/RoleEditForm.vue";

const tab = ref<string | null>(null);

const tabs = computed((): { [index: string]: string } => {
    return {roles: 'Роли', permissions: 'Права', history: 'История'};
});

const form = ref<InstanceType<typeof RoleEditForm> | null>(null);
const list = ref<InstanceType<typeof RolesList> | null>(null);
const history = ref<InstanceType<typeof RolesHistory> | null>(null);

function create(): void {
    if (form.value !== null) {
        form.value.show(0)
            .then(() => {
                if (tab.value === 'roles' && list.value !== null) {
                    list.value.reload();
                }
                if (tab.value === 'history' && history.value !== null) {
                    history.value.reload();
                }
            });
    }
}
</script>
