<template>
    <LayoutPage :title="title"
                :is-processing="staff.is_loading"
                :is-forbidden="staff.is_forbidden"
                :is-not-found="staff.is_not_found"
                :breadcrumbs="[
                    {name: 'Сотрудники', route: {name: 'staff'}},
                    {name: title},
                ]"
    >
        <template v-slot:actions>
            <GuiActionsMenu title="Действия">
                <GuiLink name="Удалить сотрудника"/>
            </GuiActionsMenu>
        </template>

        <GuiTabs v-model="tab" :tabs="tabs" tab-key="tab"/>

        <StaffView v-if="tab === 'staff'" :staff-id="staffID" :staff-data="staff.data"/>
        <StaffHistory v-if="tab === 'history'" :staff-id="staffID" ref="history"/>
        <StaffOperations v-if="tab === 'operations'" :staff-id="staffID" ref="operations"/>
    </LayoutPage>
</template>

<script setup lang="ts">
import LayoutPage from "@/Components/Layout/LayoutPage.vue";
import GuiActionsMenu from "@/Components/GUI/GuiActionsMenu.vue";
import GuiLink from "@/Components/GUI/GuiLink.vue";
import GuiTabs from "@/Components/GUI/GuiTabs.vue";
import {computed, ref} from "vue";
import StaffHistory from "@/App/Pages/System/Staff/StaffHistory.vue";
import {useRoute} from "vue-router";
import StaffOperations from "@/App/Pages/System/Staff/StaffOperations.vue";
import StaffView from "@/App/Pages/System/Staff/StaffView.vue";
import {Data} from "@/Core/Data";
import {StaffInfo} from "@/App/types";

const route = useRoute();

const staffID = computed((): number => {
    return Number(route.params['id']);
})

const tab = ref<string | null>(null);

const tabs = computed((): { [index: string]: string } => {
    return {staff: 'Сотрудник', operations: 'Журнал операций', history: 'История'};
});

const staff = ref<Data<StaffInfo>>(new Data<StaffInfo>('/api/system/staff/view', {id: staffID.value}));

staff.value.load();

const title = computed((): string => {
    return staff.value.is_loaded ? staff.value.data.name : '...';
});

const history = ref<InstanceType<typeof StaffHistory> | null>(null);
const operations = ref<InstanceType<typeof StaffOperations> | null>(null);
</script>
