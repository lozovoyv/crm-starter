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
@import "@/variables.scss";

.tabs {
    width: 100%;
    display: flex;
    flex-direction: row;
    flex-wrap: wrap-reverse;
    box-sizing: border-box;
    padding: 0 3px 15px;
    @include no_selection();

    &__tab {
        font-size: 15px;
        font-family: $project_font;
        height: $base_size_unit;
        line-height: $base_size_unit;
        white-space: nowrap;
        box-sizing: border-box;
        padding: 0 12px;
        border-style: solid;
        color: $color_default;
        border-color: $color_gray_lighten_2;
        border-width: 1px 1px 0;
        border-radius: 4px 4px 0 0;
        margin: 3px 2px 0;
        background-color: transparentize($color_gray_lighten_2, 0.75);
        cursor: pointer;
        transition: color $animation $animation_time;
        position: relative;

        &:before, &:after {
            content: '';
            display: block;
            height: 1px;
            background-color: $color_gray_lighten_2;
            position: absolute;
            bottom: 0;
        }

        &:before {
            width: calc(50% + 6px);
            left: -3px;
        }

        &:after {
            width: calc(50% + 6px);
            right: -3px;
        }

        &:not(&-active):hover {
            color: $color_default_lighten_1;
        }

        &-active {
            color: $color_text_black;
            background-color: $color_white;
            cursor: default;

            &:before, &:after {
                width: 3px;
            }
        }
    }

    &__spacer {
        border-bottom: 1px solid $color_gray_lighten_2;
        flex-grow: 1;
    }
}
</style>
