<template>
    <LayoutPage title="Журнал операций" :breadcrumbs="[{name: 'Журнал операций'}]">
        <HistoryList
            url="/api/system/history"
            method="get"
            remember-prefix="system_history"
            empty-message="Нет истории операций"
        />
    </LayoutPage>
</template>

<script setup lang="ts">
import LayoutPage from "@/Components/Layout/LayoutPage.vue";
import HistoryList from "@/Components/History/HistoryList.vue";
</script>
