<template>
    <LayoutPage
        title="Журнал операций"
        :breadcrumbs="[{name: 'Журнал операций'}]"
        :reload="true"
        @reload="callReload"
    >
        <HistoryList
            url="/api/system/history"
            method="get"
            remember-prefix="system_history"
            empty-message="Нет истории операций"
            ref="history"
        />
    </LayoutPage>
</template>

<script setup lang="ts">
import LayoutPage from "@/Components/Layout/LayoutPage.vue";
import HistoryList from "@/Components/History/HistoryList.vue";
import {ref} from "vue";

const history = ref<InstanceType<typeof HistoryList> | undefined>(undefined);

function callReload(): void {
    if (history.value !== undefined) {
        history.value.reload();
    }
}
</script>
