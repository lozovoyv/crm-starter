<template>
    <nav class="application__menu" :class="{'application__menu-initializing': !is_initialized}">
        <div class="application__menu-root" ref="menuContainer">
            <ApplicationMenuRootItem v-for="(menuItem, key) in visibleMenuItems"
                                     :item="menuItem"
                                     :expanded="is_expanded"
                                     :toggle-state="toggle_state"
                                     :width="itemsWidths[key]"
                                     :resize-state="resize_state"
                                     @expand="expand"
                                     @hovered="hovered"
                                     @clicked="clicked"
            />
            <div class="application__menu-root-hidden">
                <ApplicationMenuRootItem v-if="hiddenMenu"
                                         :item="hiddenMenu"
                                         :expanded="is_expanded"
                                         :right="true"
                                         :toggle-state="toggle_state"
                                         :resize-state="resize_state"
                                         @expand="expand"
                                         @hovered="hovered"
                                         @clicked="clicked"
                >
                    <IconBars class="application__menu-root-hidden-button"/>
                </ApplicationMenuRootItem>
            </div>
        </div>
    </nav>
</template>

<script setup lang="ts">
import menu from "@/App/menu";
import {Menu, MenuItem} from "@/Core/Types/Menu";
import {computed, onMounted, ref} from "vue";
import ApplicationMenuRootItem from "@/Components/Layout/Menu/ApplicationMenuRootItem.vue";
import IconBars from "@/Icons/IconBars.vue";

const props = defineProps<{
    menu: Menu,
}>();

const is_initialized = ref<boolean>(false);
const is_expanded = ref<boolean>(false);
const toggle_state = ref<boolean>(false);
const resize_state = ref<boolean>(false);

const menuContainer = ref<HTMLDivElement | null>(null);
const menuWidth = ref<number>(0);
const itemsWidths = ref<number[]>([]);

let hiddenWidth: number = 0;
const visibleItems = ref<number | null>(null);

onMounted(() => {
    if (menuContainer.value) {
        const items: NodeListOf<HTMLDivElement> = menuContainer.value.querySelectorAll(':scope > *');
        items.forEach((item) => {
            const width = Math.ceil(item.clientWidth);
            if (item.classList.contains('application__menu-root-hidden')) {
                hiddenWidth = width;
            } else {
                itemsWidths.value.push(width);
            }
        });
        handleSizeChange(menuContainer.value.clientWidth);
        new ResizeObserver(onMenuResize).observe(menuContainer.value);
        document.addEventListener('click', close, {passive: true, capture: false});
        setTimeout(() => {
            is_initialized.value = true;
        }, 50);
    }
});

function onMenuResize(entries: ResizeObserverEntry[]): void {
    if (!menuContainer.value) {
        return;
    }
    resize_state.value = !resize_state.value;
    handleSizeChange(Math.ceil(entries[0].contentRect.width));
}

function handleSizeChange(newSize: number): void {
    menuWidth.value = newSize;
    let visibleItemsCalculated = 0;
    let widthCalculated = hiddenWidth;
    itemsWidths.value.some(width => {
        const show = width + widthCalculated < menuWidth.value - 2; // 2px is delta to prevent misfires and flickering
        widthCalculated += width;
        if (show) {
            visibleItemsCalculated += 1;
            return false;
        }
        return true;
    });
    visibleItems.value = visibleItemsCalculated;
}

const visibleMenuItems = computed<Menu | null>((): Menu | null => {
    return visibleItems.value === null ? menu : menu.slice(0, visibleItems.value);
});

const hiddenMenu = computed<MenuItem | null>((): MenuItem | null => {
    return (visibleItems.value === null || visibleItems.value === props.menu.length) ? null : {title: null, items: menu.slice(visibleItems.value)};
});

function expand(expanded: boolean): void {
    is_expanded.value = expanded;
}

function hovered(): void {
    toggle_state.value = !toggle_state.value;
}

function close(): void {
    if (is_expanded.value) {
        is_expanded.value = false;
        toggle_state.value = !toggle_state.value;
    }
}

function clicked(): void {
    is_expanded.value = false;
    toggle_state.value = !toggle_state.value;
}
</script>

<style lang="scss">
@import "@/variables.scss";

.application__menu {
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
    height: 100%;
    flex-shrink: 1;
    flex-grow: 1;
    @include no_selection();

    &-initializing {
        opacity: 0;
    }

    &-root {
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        height: 100%;
        flex-shrink: 1;
        flex-grow: 1;

        &-hidden {
            display: flex;
            flex-grow: 0;
            flex-shrink: 0;
            width: $base_size_unit * 1.5;
            height: 100%;

            &-button {
                width: $base_size_unit * 1.2;
                height: 100%;
                padding: 0 30%;
                box-sizing: border-box;
            }
        }
    }
}
</style>
