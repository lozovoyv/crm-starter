<template>
    <HistoryList
        :url="`/api/system/users/user/${userId}/history`"
        prefix="system_users_history"
        empty-message="Нет истории изменения учётной записи"
        ref="history"
        :external-state="externalState"
    />
</template>

<script setup lang="ts">
import HistoryList from "@/Components/History/HistoryList.vue";
import {ref} from "vue";
import {CommunicationState} from "@/Core/Types/Communications";

const props = defineProps<{
    userId: number,
    externalState?: CommunicationState,
}>();

const history = ref<InstanceType<typeof HistoryList> | undefined>(undefined);

function reload(): void {
    history.value?.reload();
}

defineExpose({
    reload,
})
</script>
