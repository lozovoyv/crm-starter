<template>
    <LayoutPage title="Профиль"
                :is-processing="data.is_loading"
    >
        <GuiTabs :tabs="{
            info: 'Информация',
            access: 'Доступ',
            personal: 'Личные данные',
        }" v-model="tab" tabKey="tab" :clearKeys="['infoTab']"/>

        <!--        <template v-slot:footer>-->
        <!--            footer-->
        <!--        </template>-->
    </LayoutPage>
</template>

<script setup lang="ts">
import LayoutPage from "@/Components/Layout/LayoutPage.vue";
import {Data} from "@/Core/Data";
import {ref} from "vue";
import GuiTabs from "@/Components/GUI/GuiTabs.vue";

const data = ref<Data<{}>>(new Data<{}>('/api/profile'));

const tab = ref<string | undefined>(undefined);

data.value.load();
</script>
