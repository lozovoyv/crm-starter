<template>
    <LayoutPage
        title="Права"
        :breadcrumbs="[{name: 'Права'}]"
        :reload="true"
        @reload="callReload"
    >
        <template v-slot:actions>
            <GuiActionsMenu title="Действия">
                <GuiLink name="Добавить группу прав" @click="create()"/>
            </GuiActionsMenu>
        </template>

        <GuiTabs v-model="tab" :tabs="tabs" tab-key="tab"/>
        <PermissionGroupsList v-if="tab === 'groups'" ref="groups"/>
        <PermissionsList v-if="tab === 'permissions'" ref="permissions"/>
        <PermissionsHistory v-if="tab === 'history'" ref="history"/>

        <PermissionGroupEditForm ref="form"/>
    </LayoutPage>
</template>

<script setup lang="ts">
import LayoutPage from "@/Components/Layout/LayoutPage.vue";
import GuiActionsMenu from "@/Components/GUI/GuiActionsMenu.vue";
import GuiLink from "@/Components/GUI/GuiLink.vue";
import GuiTabs from "@/Components/GUI/GuiTabs.vue";
import {computed, ref} from "vue";
import PermissionGroupsList from "@/App/Pages/System/Permissions/parts/PermissionGroupsList.vue";
import PermissionsHistory from "@/App/Pages/System/Permissions/parts/PermissionsHistory.vue";
import PermissionsList from "@/App/Pages/System/Permissions/parts/PermissionsList.vue";
import PermissionGroupEditForm from "@/App/Pages/System/Permissions/parts/PermissionGroupEditForm.vue";

const tab = ref<string | undefined>(undefined);

const tabs = computed((): { [index: string]: string } => {
    return {groups: 'Группы прав', permissions: 'Права', history: 'История'};
});

const form = ref<InstanceType<typeof PermissionGroupEditForm> | undefined>(undefined);
const groups = ref<InstanceType<typeof PermissionGroupsList> | undefined>(undefined);
const permissions = ref<InstanceType<typeof PermissionsList> | undefined>(undefined);
const history = ref<InstanceType<typeof PermissionsHistory> | undefined>(undefined);

function create(): void {
    if (form.value) {
        form.value.show(undefined)
            ?.then(() => {
                if (tab.value === 'groups' && groups.value) {
                    groups.value?.reload();
                }
                if (tab.value === 'history' && history.value) {
                    history.value?.reload();
                }
            });
    }
}

function callReload(): void {
    if (tab.value === 'groups' && groups.value !== undefined) {
        groups.value.reload();
    }
    if (tab.value === 'permissions' && permissions.value !== undefined) {
        permissions.value.reload();
    }
    if (tab.value === 'history' && history.value !== undefined) {
        history.value.reload();
    }
}
</script>
