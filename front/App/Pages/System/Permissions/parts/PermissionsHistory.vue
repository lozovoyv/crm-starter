<template>
    <HistoryList
        url="/api/system/permissions/history"
        prefix="system_permissions_history"
        empty-message="Нет истории изменения прав"
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
