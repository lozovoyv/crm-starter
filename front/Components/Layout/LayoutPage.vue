<template>
    <div class="layout-page" :class="{'layout-page__wide': wide}">

        <Breadcrumbs :breadcrumbs="breadcrumbs"/>

        <div class="layout-page__wrapper">
            <LoadingProgress :loading="isProcessing">
                <div class="layout-page__header">
                    <div class="layout-page__header-main">
                        <h1 class="layout-page__header-main-title">{{ title }}</h1>
                        <!--
                            <slot name="header" v-if="$slots.header"/>
                            <template v-else>
                                <div class="layout-page__header-main">
                                    <template v-if="!titleNewLine">
                                        <router-link v-if="titleLink" :to="titleLink" class="layout-page__header-main-title-link">{{ title }}</router-link>
                                    </template>
                                </div>
                            </template>
                        -->
                    </div>
                    <div class="layout-page__header-actions" v-if="canViewPage && ($slots.actions || link)">
                        <div class="layout-page__header-actions-link" v-if="link">
                            <GuiLink :route="link.route" :name="link.name"/>
                        </div>
                        <slot name="actions" v-if="$slots.actions"/>
                    </div>
                </div>
                <div class="layout-page__prologue" v-if="canViewPage && $slots.header">
                    <slot name="header"/>
                </div>
                <!--
                <div class="layout-page__divider"></div>
                -->
                <div class="layout-page__body">
                    <slot v-if="canViewPage"/>
                    <div v-else-if="isForbidden" class="layout-page__body-error">
                        У Вас недостаточно прав для просмотра этой страницы
                    </div>
                    <div v-else-if="isNotFound" class="layout-page__body-error">
                        <slot name="notFound" v-if="$slots.notFound"/>
                        <template v-else>
                            Страница не найдена
                        </template>
                    </div>
                </div>

                <div class="layout-page__divider" v-if="$slots.footer"></div>

                <div class="layout-page__footer" v-if="$slots.footer">
                    <slot name="footer"/>
                </div>
            </LoadingProgress>
        </div>
    </div>
</template>

<script setup lang="ts">
import LoadingProgress from "@/Components/LoadingProgress.vue";
import {computed} from "vue";
import GuiLink from "@/Components/GUI/GuiLink.vue";
import Breadcrumbs from "@/Components/Layout/Breadcrumbs.vue";

const props = defineProps<{
    isProcessing?: boolean,
    isForbidden?: boolean,
    isNotFound?: boolean,
    link?: { name: string, route?: { name: string, params?: { id: number } } },
    breadcrumbs?: Array<{ name?: string, route?: { name: string, params?: { id: number } } }>,
    title?: string,
    wide?: boolean,
}>();

const canViewPage = computed((): boolean => {
    return !props.isForbidden && !props.isNotFound;
});
// let permission = typeof this.$route.meta['permission'] !== "undefined" ? this.$route.meta['permission'] : null;
// let passed = true;
// if (permission !== null && permission !== '') {
//     if (typeof permission === "string") permission = [permission];
//     passed = Object.keys(permission).some(key => this.$store.getters['permissions/can'](permission[key]));
// }
// return passed;
// }
</script>

<style lang="scss">
@use "sass:math";
@import "@/variables";

.layout-page {
    width: calc(100% - 20px);
    max-width: 1200px;
    margin: 0 auto;

    &__wrapper {
        background-color: $color_white;
        box-shadow: $shadow_2;
        border-radius: math.div($base_size_unit, 2);
        box-sizing: border-box;
        padding: $base_size_unit * 2;
        margin: 10px auto 30px;
    }

    &__wide {
        max-width: unset;
    }

    &__divider {
        height: 1px;
        background-color: transparentize($color_gray_lighten_2, 0.5);
        margin: 8px 0;
    }

    &__header {
        box-sizing: content-box;
        display: flex;
        flex-direction: row;
        margin: 0 0 8px 0;
        min-height: $base_size_unit * 4;

        &-main {
            flex-grow: 1;

            &-title {
                color: $color_text_black;
                font-family: $project_font;
                font-size: 20px;
                margin: 0;
                line-height: line_height($base_size_unit * 2.5);
                font-weight: 600;
                box-sizing: border-box;
                padding-left: 6px;
                padding-top: 2px;
            }
        }

        &-actions {
            flex-shrink: 0;
            flex-grow: 0;
            display: flex;

            &-link {
                line-height: line_height($base_size_unit * 2.5);
                font-size: 14px;
                margin-right: 10px;
            }
        }
    }

    &__body {
        &-error {
            font-family: $project_font;
            font-size: 20px;
            font-weight: 300;
            text-align: center;
            padding: 40px 0;
            border: 1px solid transparentize($color_error_lighten-2, 0.5);
            color: $color_error;
            border-radius: 2px;
        }
    }

    //&__footer {
    //    box-sizing: border-box;
    //    padding-top: 10px;
    //    border-top: 1px solid $base_light_gray_color;
    //    font-family: $project_font;
    //    font-size: 16px;
    //}
}
</style>
