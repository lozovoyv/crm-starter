<template>
    <LayoutPage title="Учётные записи" :breadcrumbs="[{name: 'Учётные записи'}]">
        <template v-slot:actions>
            <GuiActionsMenu title="Действия">
                <GuiLink name="Создать учётную запись" :route="{name: 'user_create'}"/>
            </GuiActionsMenu>
        </template>

        <GuiTabs v-model="tab" :tabs="tabs" tab-key="tab"/>

        <UsersList v-if="tab === 'users'" ref="list"/>
        <UsersHistory v-if="tab === 'history'" ref="history"/>
    </LayoutPage>
</template>

<script setup lang="ts">
import LayoutPage from "@/Components/Layout/LayoutPage.vue";
import GuiTabs from "@/Components/GUI/GuiTabs.vue";
import {computed, ref} from "vue";
import UsersList from "@/App/Pages/System/Users/UsersList.vue";
import UsersHistory from "@/App/Pages/System/Users/UsersHistory.vue";
import GuiActionsMenu from "@/Components/GUI/GuiActionsMenu.vue";
import GuiLink from "@/Components/GUI/GuiLink.vue";

const tab = ref<string | null>(null);

const tabs = computed((): { [index: string]: string } => {
    return {users: 'Учётные записи', history: 'История'};
});

const list = ref<InstanceType<typeof UsersList> | null>(null);
const history = ref<InstanceType<typeof UsersHistory> | null>(null);

</script>
