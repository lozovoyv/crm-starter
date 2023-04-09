<template>
    <div class="breadcrumbs" v-if="breadcrumbs">
        <router-link class="breadcrumbs__link breadcrumbs__link-home" :to="{name: 'home'}">
            <IconHome/>
        </router-link>
        <span class="breadcrumbs__divider">{{ dividerInner }}</span>

        <template v-for="(link, index) in breadcrumbs">
            <router-link v-if="link.route" class="breadcrumbs__link" :to="link.route">{{ link.name }}</router-link>
            <span v-else class="breadcrumbs__text">{{ link.name ? link.name : '...' }}</span>
            <span class="breadcrumbs__divider" v-if="index +1 < nodesCount">{{ dividerInner }}</span>
        </template>
    </div>
</template>

<script setup lang="ts">
import {computed} from "vue";
import IconHome from "@/Icons/IconHome.vue";

const props = defineProps<{
    breadcrumbs?: Array<{ name?: string, route?: { name: string, params?: { id: number } } }>,
    divider?: string,
}>();

const dividerInner = computed((): string => {
    return !props.divider ? '/' : props.divider;
});

const nodesCount = computed((): number => {
    return !props.breadcrumbs ? 0 : props.breadcrumbs.length;
});

</script>

<style lang="scss">
@import "@/variables";

.breadcrumbs {
    font-family: $project_font;
    font-size: 14px;
    margin: 16px 0;
    line-height: 14px;

    &__text {
        color: $color_gray_darken_2;
        white-space: nowrap;
    }

    &__link {
        color: $color_default;
        text-decoration: none;
        font-weight: normal;
        white-space: nowrap;
        transition: color $animation $animation_time;

        &:hover {
            color: $color_default_lighten_1
        }

        &-home > svg {
            width: 14px;
            height: 14px;
            display: inline-block;
            vertical-align: top;
        }
    }

    &__divider {
        margin: 0 5px;
        color: $color_gray_lighten_1;
    }
}
</style>
