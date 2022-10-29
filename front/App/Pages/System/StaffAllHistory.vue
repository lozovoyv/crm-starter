<template>
    <HistoryList
        url="/api/system/staff/history"
        commentsUrl="/api/system/staff/history/comments"
        changesUrl="/api/system/staff/history/changes"
        prefix="system_staff_history"
        empty-message="Нет истории изменения ролей"
        ref="history"
    />
</template>

<script setup lang="ts">
import HistoryList from "@/Components/History/HistoryList.vue";
import {ref} from "vue";

const history = ref<InstanceType<typeof HistoryList>>(null);

function reload(): void {
    history.value.reload();
}

defineExpose({
    reload,
})
</script>
