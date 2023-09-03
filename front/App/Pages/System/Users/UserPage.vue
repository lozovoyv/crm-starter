<template>
    <LayoutPage :title="title"
                :is-processing="user.state.is_loading"
                :is-forbidden="user.state.is_forbidden"
                :is-not-found="user.state.is_not_found"
                :breadcrumbs="[
                    {name: 'Учётные записи', route: {name: 'users'}},
                    {name: title},
                ]"
    >
        <template v-slot:actions v-if="can('system__users_change') && user.state.is_loaded">
            <GuiActionsMenu title="Действия" v-if="!user.data.locked">
                <GuiLink name="Редактировать" :route="{name: 'user_edit', params: {id: userID}}"/>
                <GuiLink name="Удалить" @click="remove"/>
            </GuiActionsMenu>
        </template>

        <GuiTabs v-model="tab" :tabs="tabs" tab-key="tab"/>

        <UserView v-if="tab === 'user'" :user-id="userID" :user-data="user.data" @update="update"/>
        <UserHistory v-if="tab === 'history'" :user-id="userID" ref="history" :external-state="user.state"/>
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
import UserHistory from "@/App/Pages/System/Users/parts/UserHistory.vue";
import UserView from "@/App/Pages/System/Users/parts/UserView.vue";
import {UserInfo} from "@/App/types";
import {processEntry} from "@/Core/Helpers/ProcessEntry";
import dialog from "@/Core/Dialog/Dialog";

const router = useRouter();
const route = useRoute();

const userID = computed((): number => {
    return Number(route.params['id']);
})

const tab = ref<string | undefined>(undefined);

const tabs = computed((): { [index: string]: string } => {
    return {user: 'Учётная запись', history: 'История'};
});

const user = ref<Data<UserInfo>>(new Data<UserInfo>(`/api/system/users/${userID.value}`));

user.value.load();

const title = computed((): string => {
    return user.value.state.is_loaded ? user.value.data.name : '...';
});

const history = ref<InstanceType<typeof UserView> | undefined>(undefined);
const operations = ref<InstanceType<typeof UserHistory> | undefined>(undefined);

const processing = ref<boolean>(false);

function update(): void {
    user.value.load();
}

function remove(): void {
    const name = String([user.value.data.lastname, user.value.data.firstname, user.value.data.patronymic].join(' ')).trim();
    processEntry({
        title: 'Удаление',
        question: `Удалить учётную запись "${name}"?`,
        button: dialog.button('yes', 'Удалить', 'error'),
        method: 'delete',
        url: `/api/system/users/user/${userID.value}`,
        options: {hash: user.value.data.hash},
        progress: p => processing.value = p
    }).then(() => {
        router.push({name: 'users'});
    });
}
</script>
