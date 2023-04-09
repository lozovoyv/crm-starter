<template>
    <HistoryList
        :url="'/api/system/users/' + userId + '/history'"
        :commentsUrl="'/api/system/users/' + userId + '/history/comments'"
        :changesUrl="'/api/system/users/' + userId + '/history/changes'"
        prefix="system_users_history"
        empty-message="Нет истории изменения учётной записи"
        ref="history"
    />
</template>

<script setup lang="ts">
import HistoryList from "@/Components/History/HistoryList.vue";
import {ref} from "vue";

const props = defineProps<{
    userId: number,
}>();

const history = ref<InstanceType<typeof HistoryList> | undefined>(undefined);

function reload(): void {
    history.value?.reload();
}

defineExpose({
    reload,
})
</script>
