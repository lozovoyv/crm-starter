<template>
    <div class="tabs" v-if="tabs">
        <span class="tabs__tab" v-for="(tab, key) in tabs"
              :key="key"
              :class="{'tabs__tab-active': key === currentTab}"
              @click="setTab(key)"
        >{{ tab }}</span>
        <span class="tabs__spacer"></span>
    </div>
</template>

<script setup lang="ts">
import {ref, watch} from "vue";
import {useRoute, useRouter} from "vue-router";
import {Url} from "@/Core/Url";

const props = defineProps<{
    modelValue?: string,
    tabs: { [index: string]: string },
    tabKey?: string,
    clearKeys?: Array<string>,
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | null): void,
    (e: 'change', value: string | null): void,
}>()

const route = useRoute();
const router = useRouter();

const currentTab = ref<string | null>(null);

initTabs();

watch(() => props.tabs, () => {
    initTabs(false);
});

function initTabs(initial: boolean = true): void {
    let initialTab: string | null;

    initialTab = props.modelValue ? props.modelValue : null;

    if (!initialTab) {
        if (props.tabKey) {
            initialTab = typeof route.query[props.tabKey] === 'string' ? (new Url).getQueryParam(props.tabKey) : null;
        }
        let tabKeys: Array<string> = Object.keys(props.tabs);
        if (initialTab === null || tabKeys.indexOf(initialTab) === -1) {
            initialTab = tabKeys.length > 0 ? tabKeys[0] : null;
        }
    }

    setTab(initialTab, initial);
}

function setTab(newTab: string | number | symbol | null, initial: boolean = false): void {
    const newTabName: string | null = newTab === null ? null : String(newTab);
    if (newTab === currentTab.value) {
        return;
    }
    currentTab.value = newTabName;

    if (props.tabKey) {
        const url = new Url;
        if (props.clearKeys && !initial) {
            props.clearKeys.map(key => {
                url.removeQueryParam(key);
            })
        }
        url.setQueryParam(props.tabKey, newTabName);
        url.replace();
        emit('update:modelValue', newTabName);
        emit('change', newTabName);
    } else {
        emit('update:modelValue', newTabName);
        emit('change', newTabName);
    }
}
</script>

<style lang="scss">
@use "sass:math";
@import "@/variables.scss";

.tabs {
    width: 100%;
    display: flex;
    flex-direction: row;
    box-sizing: border-box;
    padding: 0 math.div($base_size_unit, 2) 0 0;
    margin-bottom: $base_size_unit * 2;
    align-items: flex-start;
    @include no_selection();

    &__tab {
        border-color: transparentize($color_gray_lighten_2, 0.25);
        border-radius: $base_size_unit $base_size_unit 0 0;
        border-style: solid;
        border-width: 1px 1px 0;
        box-sizing: border-box;
        color: transparentize($color_default, 0.3);
        cursor: pointer;
        font-family: $project_font;
        font-size: 15px;
        height: $base_size_unit * 3 + 2px + math.div($base_size_unit, 2);
        line-height: line_height($base_size_unit * 3.5);
        margin: 4px 2px 0;
        padding: 1px $base_size_unit * 1.5 0;
        position: relative;
        transition: color $animation $animation_time;
        white-space: nowrap;

        &:before, &:after {
            content: '';
            display: block;
            height: math.div($base_size_unit, 2);
            border-color: $color_gray_lighten_2;
            border-style: solid;
            position: absolute;
            bottom: 0;

        }

        &:before {
            width: calc(50% + 3px);
            left: -3px;
            border-width: 0 0 1px 0;
        }

        &:after {
            width: calc(50% + 3px);
            right: -3px;
            border-width: 0 0 1px 0;
        }

        &:not(&-active):hover {
            color: $color_default_lighten_2;
        }

        &-active {
            color: $color_text_black;
            background-color: $color_white;
            cursor: default;
            height: $base_size_unit * 3 + 1px;
            border-color: $color_gray_lighten_2;
            margin-bottom: math.div($base_size_unit, 2) + 1px;

            &:before {
                width: math.div($base_size_unit, 2);
                left: - math.div($base_size_unit, 2) - 1px;
                border-width: 0 1px 1px 0;
                border-radius: 0 0 math.div($base_size_unit, 2) 0;
                transform: translateY(100%);
            }

            &:after {
                width: math.div($base_size_unit, 2);
                right: - math.div($base_size_unit, 2) - 1px;
                border-width: 0 0 1px 1px;
                border-radius: 0 0 0 math.div($base_size_unit, 2);
                transform: translateY(100%);
            }
        }
    }

    &__spacer {
        border-bottom: 1px solid $color_gray_lighten_2;
        flex-grow: 1;
        align-self: flex-end;
    }
}
</style>
