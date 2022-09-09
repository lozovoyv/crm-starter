<template>
    <h1>Welcome d</h1>
    <GuiButton @clicked="refresh">refresh</GuiButton>
</template>

<script setup lang="ts">
import GuiButton from "@/Components/GUI/GuiButton.vue";
import {useStore} from "vuex";
const store = useStore();

function refresh() {
    store.dispatch('user/refresh');
}
</script>

<style scoped>

</style>
