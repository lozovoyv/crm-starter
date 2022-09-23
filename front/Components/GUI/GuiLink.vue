<template>
    <router-link v-if="route" class="route-link" :to="route">{{ name }}</router-link>
    <span v-else class="route-link">{{ name }}</span>
</template>

<script setup lang="ts">
import {defineProps} from "vue";
import {RouteRecordRaw} from "vue-router";

const props = defineProps<{
    name?: string,
    route?: RouteRecordRaw
}>();
</script>

<style lang="scss">
@import "@/variables.scss";

.route-link {
    font-family: $project_font;
    font-weight: normal;
    font-size: 1em;
    color: $color_default;
    text-decoration: none;
    cursor: pointer;
    transition: color $animation $animation_time;

    &:hover {
        color: $color_default_lighten_1;
    }
}
</style>
