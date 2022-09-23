<template>
    <div class="loading-progress">
        <div class="loading-progress__wrapper" v-if="loading" :class="{'loading-progress__wrapper-loading': loading, 'loading-progress__wrapper-transparent': transparent}">
            <div class="loading-progress__spinner">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" xml:space="preserve">
                    <polygon class="loading-progress__spinner-part loading-progress__spinner-part-0" points="44.4,30.3 50,27 55.6,30.3 63,17.5 50,10 37,17.5"/>
                    <polygon class="loading-progress__spinner-part loading-progress__spinner-part-1" points="44.4,30.3 50,27 55.6,30.3 63,17.5 50,10 37,17.5"/>
                    <polygon class="loading-progress__spinner-part loading-progress__spinner-part-2" points="44.4,30.3 50,27 55.6,30.3 63,17.5 50,10 37,17.5"/>
                    <polygon class="loading-progress__spinner-part loading-progress__spinner-part-3" points="44.4,30.3 50,27 55.6,30.3 63,17.5 50,10 37,17.5"/>
                    <polygon class="loading-progress__spinner-part loading-progress__spinner-part-4" points="44.4,30.3 50,27 55.6,30.3 63,17.5 50,10 37,17.5"/>
                    <polygon class="loading-progress__spinner-part loading-progress__spinner-part-5" points="44.4,30.3 50,27 55.6,30.3 63,17.5 50,10 37,17.5"/>
                </svg>
            </div>
        </div>
        <slot></slot>
    </div>
</template>

<script setup lang="ts">
const props = defineProps<{
    loading: boolean,
    transparent?: boolean,
}>();
</script>

<style lang="scss">
@use "sass:math";
@import "@/variables";

$loader_background_color: transparentize($color_white, 0.5) !default;
$delta: 5%;
$max_size: 80px;
$loader_animation_time: 2s;

.loading-progress {
    width: 100%;
    height: 100%;
    position: relative;

    &__wrapper {
        position: absolute;
        z-index: 9;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: $loader_background_color;
        opacity: 0;
        visibility: hidden;
        transition: opacity $animation $animation_time, visibility $animation $animation_time;
        min-height: $base_size_unit;

        &-transparent {
            background-color: transparent;
        }

        &-loading {
            opacity: 1;
            visibility: visible;
        }
    }

    &__spinner {
        width: 100%;
        height: 100%;
        max-width: $max_size;
        max-height: $max_size;
        animation: loading-progress $loader_animation_time cubic-bezier(0.5, 0, 0.5, 1) infinite;

        & > svg {
            width: 100%;
            height: 100%;
        }

        &-part {
            fill: #FF931E;
            transform-origin: center;

            &-0 {
                animation: loading-progress-part-0 $loader_animation_time cubic-bezier(0.5, 0, 0.5, 1) infinite;
            }

            &-1 {
                animation: loading-progress-part-1 $loader_animation_time cubic-bezier(0.5, 0, 0.5, 1) infinite;
            }

            &-2 {
                animation: loading-progress-part-2 $loader_animation_time cubic-bezier(0.5, 0, 0.5, 1) infinite;
            }

            &-3 {
                animation: loading-progress-part-3 $loader_animation_time cubic-bezier(0.5, 0, 0.5, 1) infinite;
            }

            &-4 {
                animation: loading-progress-part-4 $loader_animation_time cubic-bezier(0.5, 0, 0.5, 1) infinite;
            }

            &-5 {
                animation: loading-progress-part-5 $loader_animation_time cubic-bezier(0.5, 0, 0.5, 1) infinite;
            }
        }
    }
}


@keyframes loading-progress {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

@keyframes loading-progress-part-0 {
    0% {
        transform: rotate(0deg) translateY(0);
    }
    50% {
        transform: rotate(0deg) translateY($delta);
    }
    100% {
        transform: rotate(0deg) translateY(0);
    }
}

@keyframes loading-progress-part-1 {
    0% {
        transform: rotate(60deg) translateY(0);
    }
    50% {
        transform: rotate(60deg) translateY($delta);
    }
    100% {
        transform: rotate(60deg) translateY(0);
    }
}

@keyframes loading-progress-part-2 {
    0% {
        transform: rotate(120deg) translateY(0);
    }
    50% {
        transform: rotate(120deg) translateY($delta);
    }
    100% {
        transform: rotate(120deg) translateY(0);
    }
}

@keyframes loading-progress-part-3 {
    0% {
        transform: rotate(180deg) translateY(0);
    }
    50% {
        transform: rotate(180deg) translateY($delta);
    }
    100% {
        transform: rotate(180deg) translateY(0);
    }
}

@keyframes loading-progress-part-4 {
    0% {
        transform: rotate(240deg) translateY(0);
    }
    50% {
        transform: rotate(240deg) translateY($delta);
    }
    100% {
        transform: rotate(240deg) translateY(0);
    }
}

@keyframes loading-progress-part-5 {
    0% {
        transform: rotate(300deg) translateY(0);
    }
    50% {
        transform: rotate(300deg) translateY($delta);
    }
    100% {
        transform: rotate(300deg) translateY(0);
    }
}
</style>
