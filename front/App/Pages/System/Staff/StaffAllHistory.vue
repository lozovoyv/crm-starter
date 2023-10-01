<template>
    <HistoryList
        url="/api/system/staff/history"
        method="get"
        remember-prefix="system_staff_all_history"
        empty-message="Нет истории изменения сотрудников"
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
