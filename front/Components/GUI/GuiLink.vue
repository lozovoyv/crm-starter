<template>
    <router-link v-if="route" class="route-link" :class="{'route-link__underline': underline}" :to="route" :target="newTab ? '_blank' : '_self'">
        <slot v-if="$slots.default"/>
        <template v-else>{{ name }}</template>
    </router-link>
    <span v-else class="route-link" :class="{'route-link__underline': underline}">
        <slot v-if="$slots.default"/>
        <template v-else>{{ name }}</template>
    </span>
</template>

<script setup lang="ts">

const props = defineProps<{
    name?: string,
    route?: { name: string, params?: { [index: string]: string | number }, query?: { [index: string]: string | number } },
    underline?: boolean,
    newTab?: boolean,
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

    &__underline {
        text-decoration: underline;
    }

    &:hover {
        color: $color_default_lighten_1;
    }
}
</style>
