<template>
    <div class="dialog__overlay" v-if="shown" :class="{'dialog__overlay-hide': hiding, 'dialog__overlay-shown': showing}" @click="popupClose" ref="overlay">
        <div class="dialog__window" :style="windowStyles">
            <LoadingProgress :loading="processing">
                <div class="dialog__window-header">
                    <div class="dialog__window-header-icon" v-if="type" :class="'dialog__window-header-icon-' + type">
                        <svg v-if="type === 'success'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path fill="currentColor"
                                  d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z"/>
                        </svg>
                        <svg v-if="type === 'info'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path fill="currentColor"
                                  d="M504 256c0 136.997-111.043 248-248 248S8 392.997 8 256C8 119.083 119.043 8 256 8s248 111.083 248 248zm-248 50c-25.405 0-46 20.595-46 46s20.595 46 46 46 46-20.595 46-46-20.595-46-46-46zm-43.673-165.346l7.418 136c.347 6.364 5.609 11.346 11.982 11.346h48.546c6.373 0 11.635-4.982 11.982-11.346l7.418-136c.375-6.874-5.098-12.654-11.982-12.654h-63.383c-6.884 0-12.356 5.78-11.981 12.654z"/>
                        </svg>
                        <svg v-if="type === 'error'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path fill="currentColor"
                                  d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm121.6 313.1c4.7 4.7 4.7 12.3 0 17L338 377.6c-4.7 4.7-12.3 4.7-17 0L256 312l-65.1 65.6c-4.7 4.7-12.3 4.7-17 0L134.4 338c-4.7-4.7-4.7-12.3 0-17l65.6-65-65.6-65.1c-4.7-4.7-4.7-12.3 0-17l39.6-39.6c4.7-4.7 12.3-4.7 17 0l65 65.7 65.1-65.6c4.7-4.7 12.3-4.7 17 0l39.6 39.6c4.7 4.7 4.7 12.3 0 17L312 256l65.6 65.1z"/>
                        </svg>
                        <svg v-if="type === 'confirmation'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" shape-rendering="geometricPrecision">
                            <path fill="currentColor"
                                  d="M504 256c0 136.997-111.043 248-248 248S8 392.997 8 256C8 119.083 119.043 8 256 8s248 111.083 248 248zM262.655 90c-54.497 0-89.255 22.957-116.549 63.758-3.536 5.286-2.353 12.415 2.715 16.258l34.699 26.31c5.205 3.947 12.621 3.008 16.665-2.122 17.864-22.658 30.113-35.797 57.303-35.797 20.429 0 45.698 13.148 45.698 32.958 0 14.976-12.363 22.667-32.534 33.976C247.128 238.528 216 254.941 216 296v4c0 6.627 5.373 12 12 12h56c6.627 0 12-5.373 12-12v-1.333c0-28.462 83.186-29.647 83.186-106.667 0-58.002-60.165-102-116.531-102zM256 338c-25.365 0-46 20.635-46 46 0 25.364 20.635 46 46 46s46-20.636 46-46c0-25.365-20.635-46-46-46z"/>
                        </svg>
                    </div>
                    <div class="dialog__window-header-title">{{ title }}</div>
                    <div class="dialog__window-header-close" @click="resolve(null)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 490 490">
                            <polygon fill="currentColor"
                                     points="456.851,0 245,212.564 33.149,0 0.708,32.337 212.669,245.004 0.708,457.678 33.149,490 245,277.443 456.851,490 489.292,457.678 277.331,245.004 489.292,32.337"/>
                        </svg>
                    </div>
                </div>
                <div class="dialog__window-message" v-if="message">{{ message }}</div>
                <slot/>
                <div class="dialog__window-buttons" v-if="buttons" :class="align ? 'dialog__window-buttons-' + align : 'dialog__window-buttons-right'">
                    <span class="dialog__window-buttons-button" v-for="button in buttons"
                          :class="button.type ? 'dialog__window-buttons-button-' + button.type : null"
                          @click="resolve(button.result)"
                    >{{ button.caption }}</span>
                </div>
            </LoadingProgress>
        </div>
    </div>
</template>

<script setup lang="ts">

import {computed, ref} from "vue";
import {DialogButtons, DialogButtonsAlign, DialogType, DialogWidth} from "@/Core/Dialog/Dialog";
import {DialogResolveFunction} from "@/Core/Dialog/Dialog";
import LoadingProgress from "@/Components/LoadingProgress.vue";

const props = defineProps<{
    type?: DialogType,
    title?: string,
    message?: string,
    buttons?: DialogButtons,
    align?: DialogButtonsAlign,
    closeOnOverlay?: boolean,
    manual?: boolean,
    resolving?: DialogResolveFunction,
    width?: DialogWidth,
}>();

const overlay = ref<HTMLDivElement | undefined>(undefined);

const shown = ref<boolean>(false);
const hiding = ref<boolean>(false);
const showing = ref<boolean>(false);
const processing = ref<boolean>(false);

let internalResolveFunction: null | { (value: unknown): void } = null;

const windowStyles = computed((): { [index: string]: string } | undefined => {
    if (props.width) {
        let styles: { [index: string]: string } = {};
        if (props.width.min) {
            styles['minWidth'] = props.width.min;
        }
        if (props.width.width) {
            styles['width'] = props.width.width;
        }
        if (props.width.max) {
            styles['maxWidth'] = props.width.max;
        }
        return styles;
    }
    return undefined;
});

function show() {
    processing.value = false;
    showing.value = true;
    hiding.value = false;
    shown.value = true;
    return new Promise(resolve => {
        internalResolveFunction = resolve;
    });
}

function resolve(value: string | null = null) {
    if (props.resolving === undefined || props.resolving(value)) {
        if (internalResolveFunction !== null) {
            internalResolveFunction(value);
            if (!props.manual) {
                hide();
            }
        }
    }
}

function process(isProcessing: boolean) {
    processing.value = isProcessing;
}

function popupClose(event: MouseEvent) {
    if (props.closeOnOverlay && <HTMLDivElement>event.target === overlay.value) {
        resolve(null);
    }
}

function hide() {
    hiding.value = true;
    setTimeout(() => {
        shown.value = false;
        showing.value = false;
        hiding.value = false;
        processing.value = false;
    }, 300);
}

defineExpose({
    show,
    process,
    hide,
});
</script>

<style lang="scss">
@import "@/Core/Dialog/dialog.scss";
</style>
