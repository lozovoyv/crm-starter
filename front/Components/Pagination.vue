<template>
    <div class="pagination" v-if="pagination">

        <span class="pagination__shown">Показано {{ shown }} из {{ pagination.total }}</span>

        <div class="pagination__per-page">
            <div class="pagination__per-page-select">
<!--                <InputDropDown-->
<!--                    :options="options"-->
<!--                    v-model="perPage"-->
<!--                    :original="10"-->
<!--                    :top="true"-->
<!--                    :small="true"-->
<!--                />-->
                {{ perPage }}
            </div>
            <span class="pagination__per-page-text">на страницу</span>
        </div>

        <div class="pagination__links">

            <span class="pagination__links-button pagination__links-button-icon"
                  :class="{'pagination__links-button-link' : pagination.current_page !== 1}"
                  @click="setPage(1, pagination.per_page)"><IconBackwardFast/></span>

            <span class="pagination__links-button pagination__links-button-icon"
                  :class="{'pagination__links-button-link' : pagination.current_page !== 1}"
                  @click="setPage(pagination.current_page - 1, pagination.per_page)"><IconBackward/></span>

            <span class="pagination__links-spacer"><span v-if="hasBefore">...</span></span>

            <span class="pagination__links-button" v-for="page in pages"
                  :class="{
                    'pagination__links-button-link-active': page === pagination.current_page && pagination.last_page > 1,
                    'pagination__links-button-link': pagination.last_page > 1,
                  }"
                  :key="page"
                  @click="setPage(page, pagination.per_page)"
            >{{ page }}</span>

            <span class="pagination__links-spacer"><span v-if="hasAfter">...</span></span>

            <span class="pagination__links-button pagination__links-button-icon"
                  :class="{'pagination__links-button-link' : pagination.current_page !== pagination.last_page}"
                  @click="setPage(pagination.current_page + 1, pagination.per_page)"><IconForward/></span>

            <span class="pagination__links-button pagination__links-button-icon"
                  :class="{'pagination__links-button-link' : pagination.current_page !== pagination.last_page}"
                  @click="setPage(pagination.last_page, pagination.per_page)"><IconForwardFast/></span>
        </div>

    </div>
</template>

<script setup lang="ts">
import {ListPagination} from "@/Core/List";
import {computed} from "vue";
import IconBackwardFast from "@/Icons/IconBackwardFast.vue";
import IconBackward from "@/Icons/IconBackward.vue";
import IconForward from "@/Icons/IconForward.vue";
import IconForwardFast from "@/Icons/IconForwardFast.vue";


const props = defineProps<{
    pagination?: ListPagination,
    options?: Array<Number>,
}>();

const emit = defineEmits<{
    (e: 'pagination', page: Number, perPage: Number): void,
}>()

const max_links: number = 7;

const shown = computed((): string => {
    if (typeof props.pagination === "undefined") return '—';
    return props.pagination.from && props.pagination.to ? `${props.pagination.from} - ${props.pagination.to}` : '0'
});

const pages = computed((): Array<number> => {
    let pages: Array<number> = [];
    if (typeof props.pagination === "undefined") return pages;

    if (props.pagination.last_page <= max_links) {
        for (let i: number = 1; i <= props.pagination.last_page; i++) {
            pages.push(i);
        }
    } else {
        let start: number = props.pagination.current_page - Math.floor(max_links / 2);
        if (start < 1) {
            start = 1;
        }
        if (start + max_links > props.pagination.last_page) {
            start = props.pagination.last_page - max_links + 1;
        }
        for (let i: number = start; i < start + max_links; i++) {
            pages.push(i);
        }
    }
    return pages;
})

const hasBefore = computed((): boolean => {
    return typeof props.pagination !== 'undefined' && (props.pagination.last_page > max_links) && (props.pagination.current_page - Math.floor(max_links / 2) > 1);
});


const hasAfter = computed((): boolean => {
    return typeof props.pagination !== 'undefined' && (props.pagination.last_page > max_links) && (props.pagination.current_page - Math.floor(max_links / 2) + max_links - 1 < props.pagination.last_page);
});

const perPage = computed({
    get: (): number => {
        return typeof props.pagination !== 'undefined' ? props.pagination.per_page : 10;
    },
    set: (value: number): void => {
        if (typeof props.pagination !== 'undefined' && props.pagination.per_page !== value) {
            setPage(1, value);
        }
    }
});

function setPage(page: number, perPage: number): void {
    if (typeof props.pagination === 'undefined' || page < 1 || page > props.pagination.last_page || (page === props.pagination.current_page && perPage === props.pagination.per_page)) {
        return;
    }

    emit('pagination', page, perPage);
}
</script>

<style lang="scss">
@use "sass:math";
@import "@/variables.scss";

$project_font: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji !default;
$animation_time: 150ms !default;
$animation: cubic-bezier(0.24, 0.19, 0.28, 1.29) !default;
$base_size_unit: 35px !default;
$base_white_color: #ffffff !default;
$base_primary_color: #0D74D7 !default;
$base_primary_hover_color: lighten(#0D74D7, 10%) !default;
$base_black_color: #1e1e1e !default;
$base_gray_color: #8f8f8f !default;
$base_light_gray_color: #e5e5e5 !default;
$base_lightest_gray_color: #f7f7f7 !default;

.pagination {
    display: flex;
    flex-direction: row;
    height: $base_size_unit;
    margin-top: 20px;
    @include no_selection;

    &__shown, &__per-page {
        flex-grow: 0;
        flex-shrink: 0;
        font-family: $project_font;
        line-height: $base_size_unit;
        font-size: 14px;
        padding-right: 15px;
    }

    &__per-page {
        display: flex;
        flex-direction: row;

        &-select {
            margin-right: 10px;
        }

        &-text {

        }
    }

    &__links {
        flex-grow: 1;
        display: flex;
        justify-content: right;
        line-height: $base_size_unit;
        font-family: $project_font;

        &-spacer {
            width: math.div($base_size_unit, 2);
            height: $base_size_unit;
            line-height: $base_size_unit;
            text-align: center;
            cursor: default;
            color: $base_gray_color;
            margin-right: 5px;
        }

        &-button {
            width: $base_size_unit;
            height: $base_size_unit;
            line-height: $base_size_unit;
            text-align: center;
            cursor: default;
            border-radius: 2px;
            box-sizing: border-box;
            color: $base_gray_color;
            border: 1px solid transparent;
            background-color: transparent;

            &:not(:last-child) {
                margin-right: 5px;
            }

            &-link {
                cursor: pointer;
                color: $base_black_color;
                border: 1px solid $base_light_gray_color;
                background-color: $base_lightest_gray_color;
                transition: border-color $animation $animation_time;

                &:not(&-active):hover {
                    border-color: $base_primary_hover_color;
                }

                &-active {
                    color: $base_white_color !important;
                    border-color: $base_primary_color !important;
                    background-color: $base_primary_color !important;
                    cursor: default;
                }
            }

            &-icon {
                display: flex;
                justify-content: center;
                align-items: center;

                & > svg {
                    width: math.div($base_size_unit, 2);
                    height: math.div($base_size_unit, 2);
                }
            }
        }
    }
}
</style>
