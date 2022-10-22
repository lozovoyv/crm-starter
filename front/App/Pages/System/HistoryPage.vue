<template>
    <LayoutPage title="Журнал операций">
        <HistoryList
            url="/api/system/history"
            commentsUrl="/api/system/history/comments"
            changesUrl="/api/system/history/changes"
            prefix="system_history"
            empty-message="Нет истории операций"
            ref="history"
        />
    </LayoutPage>
</template>

<script setup lang="ts">
import LayoutPage from "@/Components/Layout/LayoutPage.vue";
import HistoryList from "@/Components/History/HistoryList.vue";
</script>
