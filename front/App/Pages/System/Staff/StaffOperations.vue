<template>
    <HistoryList
        :url="'/api/system/staff/' + staffId + '/operations'"
        method="get"
        remember-prefix="system_staff_history"
        empty-message="Нет истории действий сотрудника"
        ref="history"
    />
</template>

<script setup lang="ts">
import HistoryList from "@/Components/History/HistoryList.vue";
import {ref} from "vue";

const props = defineProps<{
    staffId: number,
}>();

const history = ref<InstanceType<typeof HistoryList> | undefined>(undefined);

function reload(): void {
    history.value?.reload();
}

defineExpose({
    reload,
})
</script>
