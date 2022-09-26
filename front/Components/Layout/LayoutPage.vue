<template>
    <div class="layout-page" :class="{'layout-page__wide': wide}">
        <LoadingProgress :loading="isProcessing">
            <div class="layout-page__header">
                <div class="layout-page__header-main">
                    <h1 class="layout-page__header-main-title">{{ title }}</h1>
                    <!--
                        <slot name="header" v-if="$slots.header"/>
                        <template v-else>
                            <div class="layout-page__header-main">
                                <div class="layout-page__header-main-breadcrumbs" v-if="breadcrumbs" :class="{'layout-page__header-main-breadcrumbs-small':titleNewLine}">
                                    <template v-for="link in breadcrumbs">
                                        <router-link v-if="link['to']" class="layout-page__header-main-breadcrumbs-link" :to="link['to']">{{ link['caption'] }}</router-link>
                                        <span v-else class="layout-page__header-main-breadcrumbs-link">{{ link['caption'] }}</span>
                                        <span class="layout-page__header-main-breadcrumbs-divider">{{ divider }}</span>
                                    </template>
                                </div>
                                <template v-if="!titleNewLine">
                                    <router-link v-if="titleLink" :to="titleLink" class="layout-page__header-main-title-link">{{ title }}</router-link>
                                </template>
                            </div>
                        </template>
                    -->
                </div>
                <div class="layout-page__header-actions" v-if="canViewPage && ($slots.actions)">
                    <div class="layout-page__header-actions-link" v-if="link">
                        <GuiLink :route="link.route" :name="link.name"/>
                    </div>
                    <slot name="actions" v-if="$slots.actions"/>
                </div>
            </div>
            <div class="layout-page__prologue" v-if="canViewPage && $slots.header">
                <slot name="header"/>
            </div>
            <div class="layout-page__divider"></div>
            <div class="layout-page__body">
                <slot v-if="canViewPage"/>
                <div v-else-if="isForbidden" class="layout-page__body-error">
                    У Вас недостаточно прав для просмотра этой страницы
                </div>
                <div v-else-if="isNotFound" class="layout-page__body-error">
                    Страница не найдена
                </div>
            </div>

            <div class="layout-page__divider" v-if="$slots.footer"></div>

            <div class="layout-page__footer" v-if="$slots.footer">
                <slot name="footer"/>
            </div>
        </LoadingProgress>
    </div>
</template>

<script setup lang="ts">
import LoadingProgress from "@/Components/LoadingProgress.vue";
import {computed} from "vue";
import {RouteRecordRaw} from "vue-router";
import GuiLink from "@/Components/GUI/GuiLink.vue";

const props = defineProps<{
    isProcessing?: boolean,
    isForbidden?: boolean,
    isNotFound?: boolean,
    link?: { name: string, route: Array<RouteRecordRaw> },
    breadcrumbs?: Array<{ name: string, route: Array<RouteRecordRaw> }>,
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
@import "@/variables";

.layout-page {
    width: calc(100% - 20px);
    margin: 10px auto 30px;
    background-color: $color_white;
    box-shadow: $shadow_1;
    border-radius: 2px;
    box-sizing: border-box;
    padding: 16px;
    max-width: 1200px;

    &__wide {
        max-width: unset;
    }

    &__divider {
        height: 1px;
        background-color: transparentize($color_gray_lighten_2, 0.5);
        margin: 16px 0;
    }

    &__header {
        box-sizing: content-box;
        display: flex;
        flex-direction: row;

        &-main {
            flex-grow: 1;

            &-title {
                color: $color_text_black;
                font-family: $project_font;
                font-size: 20px;
                margin: 0;
                line-height: $base_size_unit;
                font-weight: 600;
                box-sizing: border-box;
                padding-left: 6px;
            }
        }

        &-actions {
            flex-shrink: 0;
            flex-grow: 0;
            display: flex;

            &-link {
                line-height: $base_size_unit;
                font-size: 14px;
                margin-right: 10px;
            }
        }

        /**
                    &-breadcrumbs {
                        &-small {
                            font-size: 16px;
                        }

                        &-link {
                            color: $base_primary_color;
                            text-decoration: none;
                            font-weight: bold;
                            white-space: nowrap;

                            &:hover {
                                color: $base_primary_hover_color
                            }
                        }

                        &-divider {
                            margin: 0 5px;
                            color: $base_gray_color;
                        }
                    }
         */
    }

    &__body {
        &-error {
            text-align: center;
            box-sizing: border-box;
            padding: 40px 0;
            font-family: $project_font;
            font-size: 20px;
            color: $color-error;
            background-color: transparentize($color-error-lighten-2, 0.9);
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
