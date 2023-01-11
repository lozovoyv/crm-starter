<template>
    <UserEdit :user-id="userID"/>
</template>

<script setup lang="ts">

import UserEdit from "@/App/Pages/System/Users/UserEdit.vue";
import {computed} from "vue";
import {useRoute} from "vue-router";

const route = useRoute();

const userID = computed((): number => {
    return Number(route.params['id']);
})
</script>
