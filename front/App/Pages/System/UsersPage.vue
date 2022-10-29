<template>
    <LayoutPage title="Учётные записи">

        <GuiTabs v-model="tab" :tabs="tabs" tab-key="tab"/>

        <UsersList v-if="tab === 'users'" ref="list"/>
        <UsersHistory v-if="tab === 'history'" ref="history"/>
    </LayoutPage>
</template>

<script setup lang="ts">
import LayoutPage from "@/Components/Layout/LayoutPage.vue";
import GuiTabs from "@/Components/GUI/GuiTabs.vue";
import {computed, ref} from "vue";
import UsersList from "@/App/Pages/System/UsersList.vue";
import UsersHistory from "@/App/Pages/System/UsersHistory.vue";

const tab = ref<string | null>(null);

const tabs = computed((): { [index: string]: string } => {
    return {users: 'Учётные записи', history: 'История'};
});

const list = ref<InstanceType<typeof UsersList> | null>(null);
const history = ref<InstanceType<typeof UsersHistory> | null>(null);
</script>
