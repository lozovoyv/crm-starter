<template>
    <HistoryList
        url="/api/system/users/history"
        prefix="system_users_history"
        empty-message="Нет истории изменения учётных записей"
        ref="history"
    />
</template>

<script setup lang="ts">
import HistoryList from "@/Components/History/HistoryList.vue";
import {ref} from "vue";

const history = ref<InstanceType<typeof HistoryList> | undefined>(undefined);

function reload(): void {
    history.value?.reload();
}

defineExpose({
    reload,
})
</script>
