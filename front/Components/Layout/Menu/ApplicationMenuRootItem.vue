<template>
    <div class="application__menu-root-item-wrapper"
         :style="{width: width ? width + 'px' : 'auto'}"
    >
        <div class="application__menu-root-item"
             :class="{'application__menu-root-item-dropped': dropped}"
             @click="toggle"
             @mouseenter="mouseenter"
        >
            <span v-if="$slots.default" class="application__menu-root-item-link">
                <slot/>
            </span>
            <RouterLink v-else-if="item.route" class="application__menu-root-item-link" :to="item.route">
                <span>{{ item.title }}<IconDropdown class="application__menu-root-item-link-drop" v-if="parent"
                                                    :class="{'application__menu-root-item-link-drop-dropped': dropped}"
                /></span>
            </RouterLink>
            <span v-else class="application__menu-root-item-link">
                <span>{{ item.title }}<IconDropdown class="application__menu-root-item-link-drop" v-if="parent"
                                                    :class="{'application__menu-root-item-link-drop-dropped': dropped}"
                /></span>
            </span>
        </div>
        <div v-if="parent" class="application__menu-root-submenu"
             :class="{'application__menu-root-submenu-right': right, 'application__menu-root-submenu-dropped': dropped}"
             :style="{left: right ? 'unset':leftAdjust + 'px', width: widthAdjust ? widthAdjust + 'px' : 'unset'}"
             ref="subMenu"
        >
            <ApplicationMenuItem v-for="menuItem in item.items"
                                 :item="menuItem"
                                 :toggleState="toggle_state_internal"
                                 @clicked="clicked"
                                 @hovered="internalHovered"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import {MenuItem} from "@/Core/Types/Menu";
import {computed, nextTick, ref, watch} from "vue";
import IconDropdown from "@/Icons/IconDropdown.vue";
import ApplicationMenuItem from "@/Components/Layout/Menu/ApplicationMenuItem.vue";

const props = defineProps<{
    item: MenuItem,
    width?: number,
    right?: boolean,
    expanded: boolean,
    toggleState: boolean,
    resizeState: boolean,
}>();

const emit = defineEmits<{
    (event: 'expand', expanded: boolean): void,
    (event: 'hovered'): void,
    (event: 'clicked'): void,
}>();

const parent = computed<boolean>((): boolean => {
    return typeof props.item.items !== 'undefined' && props.item.items.length > 0;
});

const dropped = ref<boolean>(false);
const leftAdjust = ref<number>(0);
const widthAdjust = ref<number>(0);
const subMenu = ref<HTMLDivElement | null>(null);
const toggle_state_internal = ref<boolean>(false);

// Hover handle
let is_initiator: boolean = false;

watch(() => props.toggleState, () => {
    if (!is_initiator) {
        drop(false);
    }
    is_initiator = false;
    toggle_state_internal.value = !toggle_state_internal.value;
});

watch(() => props.resizeState, () => {
    adjust();
});

function mouseenter(): void {
    if (props.expanded && parent.value) {
        drop(true);
        is_initiator = true;
    }
    emit('hovered');
}

// Local click handler
function toggle(): void {
    if (parent.value) {
        drop(!dropped.value);
        emit('expand', dropped.value);
    } else {
        emit('clicked');
    }
}

// External click handler
function clicked(): void {
    drop(false);
    emit('clicked');
}

function drop(state: boolean): void {
    dropped.value = state;
    adjust();
}

function hovered(): void {
    is_initiator = true;
    emit('hovered');
}

function internalHovered(): void {
    toggle_state_internal.value = !toggle_state_internal.value;
}

// Adjust submenu position
function adjust(): void {
    if (dropped.value) {
        leftAdjust.value = 0;
        nextTick(() => {
            if (subMenu.value) {
                const rect = subMenu.value.getBoundingClientRect();
                const windowWidth = window.innerWidth;
                const delta: number = windowWidth - rect.right;
                if (delta < 0) {
                    if (rect.left + delta > 0) {
                        leftAdjust.value = delta;
                    } else {
                        leftAdjust.value = -rect.left;
                    }
                }
            }
        });
    }
}
</script>

<style lang="scss">
@import "@/variables.scss";

.application__menu-root {
    &-item-wrapper {
        flex-grow: 0;
        font-family: $project_font;
        font-size: 14px;
        position: relative;
        color: $color_text_black;
        transition: color $animation $animation_time, background-color $animation $animation_time;
        display: flex;
    }

    &-item-wrapper:last-child > &-item > &-link {
        border-right: 1px solid transparentize($color_gray_lighten_2, 0.5);
    }

    &-item {
        align-items: center;
        height: 100%;
        display: flex;
        width: 100%;

        &-link {
            text-decoration: none;
            color: $color_text_black;
            box-sizing: border-box;
            cursor: pointer;
            line-height: 100%;
            border-left: 1px solid transparentize($color_gray_lighten_2, 0.5);
            white-space: nowrap;

            & > span {
                display: block;
                padding: 0 10px;
            }

            &-drop {
                width: 8px;
                margin-left: 6px;
                transition: transform $animation $animation_time;

                &-dropped {
                    transform: rotate(-180deg);
                }
            }
        }

        &-link:hover {
            color: $color_default_lighten_1;
        }

        &-link.router-link-exact-active {
            color: $color_default;
        }
    }

    &-submenu {
        transition: opacity $animation $animation_time, visibility $animation $animation_time;
        opacity: 0;
        visibility: hidden;
        position: absolute;
        left: 0;
        bottom: 0;
        transform: translateY(100%);
        background-color: $color_white;
        box-sizing: border-box;
        padding: 0;
        box-shadow: $shadow_1;
        border-radius: 0 0 2px 2px;
        min-width: 100%;
        z-index: 200;

        &:before {
            content: '';
            position: absolute;
            top: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: $color_white;
        }

        &:after {
            content: '';
            position: absolute;
            top: -1px;
            left: 10px;
            width: calc(100% - 20px);
            height: 1px;
            background-color: $color_gray_lighten_2;
            opacity: 0.25;
        }

        &-right {
            left: unset;
            right: 0;
        }

        &-dropped {
            opacity: 1;
            visibility: visible;
        }
    }
}
</style>
