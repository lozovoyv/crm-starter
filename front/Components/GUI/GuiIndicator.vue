<template>
    <span class="activity" :class="stateClass"></span>
</template>

<script setup lang="ts">
import {computed} from "vue";

type State = boolean | null;

const props = defineProps<{
    active?: boolean,
    wait?: boolean,
}>();

const stateClass = computed((): string | null => {
    if (props.wait) {
        return 'activity__waiting';
    }
    if (props.active === undefined || props.active === null) {
        return null;
    }
    return props.active ? 'activity__active' : 'activity__inactive';
});
</script>

<style lang="scss">
@import "@/variables.scss";

.activity {
    display: inline-block;
    width: 7px;
    height: 7px;
    vertical-align: baseline;
    background-color: $color_gray;
    border-radius: 50%;
    margin-right: 8px;
    position: relative;
    top: -1px;

    &__waiting {
        background-color: $color-info;
    }

    &__active {
        background-color: $color-success;
    }

    &__inactive {
        background-color: $color-error;
    }
}
</style>
