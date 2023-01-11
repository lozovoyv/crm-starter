<template>
    <HistoryList
        :url="'/api/system/staff/' + staffId + '/history'"
        :commentsUrl="'/api/system/staff/' + staffId + '/history/comments'"
        :changesUrl="'/api/system/staff/' + staffId + '/history/changes'"
        prefix="system_staff_history"
        empty-message="Нет истории изменения сотрудника"
        ref="history"
    />
</template>

<script setup lang="ts">
import HistoryList from "@/Components/History/HistoryList.vue";
import {ref} from "vue";

const props = defineProps<{
    staffId: number,
}>();

const history = ref<InstanceType<typeof HistoryList>>(null);

function reload(): void {
    history.value.reload();
}

defineExpose({
    reload,
})
</script>
