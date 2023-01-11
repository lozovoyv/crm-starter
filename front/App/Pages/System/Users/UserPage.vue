<template>
    <LayoutPage :title="title"
                :is-processing="user.is_loading"
                :is-forbidden="user.is_forbidden"
                :is-not-found="user.is_not_found"
                :breadcrumbs="[
                    {name: 'Учётные записи', route: {name: 'users'}},
                    {name: title},
                ]"
    >
        <template v-slot:actions v-if="can('system.users.change') && user.is_loaded">
            <GuiActionsMenu title="Действия">
                <GuiLink name="Редактировать" :route="{name: 'user_edit', params: {id: userID}}"/>
                <GuiLink name="Удалить" @click="remove"/>
            </GuiActionsMenu>
        </template>

        <GuiTabs v-model="tab" :tabs="tabs" tab-key="tab"/>

        <UserView v-if="tab === 'staff'" :user-id="userID" :user-data="user.data"/>
        <UserHistory v-if="tab === 'history'" :user-id="userID" ref="history"/>
    </LayoutPage>
</template>

<script setup lang="ts">
import LayoutPage from "@/Components/Layout/LayoutPage.vue";
import GuiActionsMenu from "@/Components/GUI/GuiActionsMenu.vue";
import GuiLink from "@/Components/GUI/GuiLink.vue";
import GuiTabs from "@/Components/GUI/GuiTabs.vue";
import {computed, ref} from "vue";
import {useRoute, useRouter} from "vue-router";
import {Data} from "@/Core/Data";
import {can} from "@/Core/Can";
import UserHistory from "@/App/Pages/System/Users/UserHistory.vue";
import UserView from "@/App/Pages/System/Users/UserView.vue";
import {UserInfo} from "@/App/types";
import {processEntry} from "@/Core/Helpers/ProcessEntry";
import dialog from "@/Core/Dialog/Dialog";

const router = useRouter();
const route = useRoute();

const userID = computed((): number => {
    return Number(route.params['id']);
})

const tab = ref<string | null>(null);

const tabs = computed((): { [index: string]: string } => {
    return {staff: 'Учётная запись', history: 'История'};
});

const user = ref<Data<UserInfo>>(new Data<UserInfo>('/api/system/users/view', {id: userID.value}));

user.value.load();

const title = computed((): string => {
    return user.value.is_loaded ? user.value.data.name : '...';
});

const history = ref<InstanceType<typeof UserView> | null>(null);
const operations = ref<InstanceType<typeof UserHistory> | null>(null);

const processing = ref<boolean>(false);

function remove(): void {
    let name = String([user.value.data.lastname, user.value.data.firstname, user.value.data.patronymic].join(' ')).trim();
    processEntry('Удаление', `Удалить учётную запись "${name}"?`, dialog.button('yes', 'Удалить', 'default'),
        '/api/system/users/remove', {user_id: user.value.data.id, user_hash: user.value.data.hash},
        p => processing.value = p
    ).then(() => {
        router.push({name: 'users'});
    });
}
</script>
