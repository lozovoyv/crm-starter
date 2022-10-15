<template>
    <div class="heading" :class="classProxy" @click="expand">
        <slot/>
        <div class="heading__expand" :class="{'heading__expand-expanded': expanded}" v-if="expandable">
            <IconDropdown :class="'heading__expand-button'"/>
        </div>
    </div>
</template>

<script setup lang="ts">
import IconDropdown from "@/Icons/IconDropdown.vue";
import {computed, ref} from "vue";

const props = defineProps<{
    expandable?: boolean,
}>();

const emit = defineEmits<{
    (e: 'expand', expanded: boolean): void,
}>();

const classProxy = computed((): string | null => {
    return props.expandable ? 'heading__expandable' : null;
});

const expanded = ref<boolean>(false);

function expand(): void {
    if (props.expandable) {
        expanded.value = !expanded.value;
        emit('expand', expanded.value);
    }
}
</script>

<style lang="scss">
@use "sass:math";
@import "@/variables.scss";

.heading {
    font-family: $project_font;
    width: 100%;
    font-size: 16px;
    font-weight: bold;
    color: $color_text_black;
    margin: 10px 0;

    &__expandable {
        cursor: pointer;
    }

    &__expand {
        display: inline-flex !important;
        justify-content: center;
        align-items: center;
        width: $base_size_unit;
        height: $base_size_unit;
        cursor: pointer;

        &-button {
            display: block;
            width: math.div($base_size_unit, 4);
            height: math.div($base_size_unit, 4);
            transition: transform $animation $animation_time;
            color: $color_text_black;
        }

        &:hover &-button {
            transform: scale(1.2);
        }

        &-expanded &-button {
            transform: rotate(-180deg);
        }

        &-expanded:hover &-button {
            transform: rotate(-180deg) scale(1.2);
        }
    }
}
</style>
