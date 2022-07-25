<template>
    <RouterView/>
</template>

<script setup lang="ts">
import {useStore} from "vuex";
import {computed} from "vue";
import {User} from "../Core/Types/User";

const store = useStore();
const user = computed((): User => {
    return store.getters['user/user'];
});
</script>

<style lang="scss">
body, html {
    width: 100%;
    height: 100%;
    min-width: 300px;
    margin: 0;
}

#application {
    width: 100%;
    height: 100%;
    background-color: #f9fafd;
}
</style>
