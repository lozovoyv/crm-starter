<template>
    <div>
        Info
    </div>
</template>

<script setup lang="ts">
import {StaffInfo} from "@/App/types";

const props = defineProps<{
    staffId: number,
    staffData: StaffInfo,
}>();

</script>
