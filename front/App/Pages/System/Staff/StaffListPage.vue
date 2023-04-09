<template>
    <LayoutPage title="Сотрудники" :breadcrumbs="[{name: 'Сотрудники'}]">
        <template v-slot:actions>
            <GuiActionsMenu title="Действия">
                <GuiLink name="Зарегистрировать сотрудника" :route="{name: 'staff_create'}"/>
            </GuiActionsMenu>
        </template>

        <GuiTabs v-model="tab" :tabs="tabs" tab-key="tab"/>

        <StaffList v-if="tab === 'staff'" ref="list"/>
        <StaffAllHistory v-if="tab === 'history'" ref="history"/>
    </LayoutPage>
</template>

<script setup lang="ts">
import LayoutPage from "@/Components/Layout/LayoutPage.vue";
import GuiActionsMenu from "@/Components/GUI/GuiActionsMenu.vue";
import GuiLink from "@/Components/GUI/GuiLink.vue";
import GuiTabs from "@/Components/GUI/GuiTabs.vue";
import {computed, ref} from "vue";
import StaffList from "@/App/Pages/System/Staff/StaffList.vue";
import StaffAllHistory from "@/App/Pages/System/Staff/StaffAllHistory.vue";

const tab = ref<string | undefined>(undefined);

const tabs = computed((): { [index: string]: string } => {
    return {staff: 'Сотрудники', history: 'История'};
});

const list = ref<InstanceType<typeof StaffList> | null>(null);
const history = ref<InstanceType<typeof StaffAllHistory> | null>(null);
</script>
