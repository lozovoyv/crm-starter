<template>
    <div class="pagination" v-if="pagination">
        <div class="pagination__info">
            <span class="pagination__info-shown">Показано: {{ shown }} из {{ pagination.total }},</span>
            <div class="pagination__info-per-page">
                <div class="pagination__info-per-page-select">
                    <GuiLink :underline="true" @click="toggle">{{ perPage }}</GuiLink>
                    <div class="pagination__info-per-page-select-options" :class="{'pagination__info-per-page-select-options-shown': showOptions}">
                        <div class="pagination__info-per-page-select-options-option" v-for="option in optionsList"
                             :class="{'pagination__info-per-page-select-options-option-selected': option === perPage}"
                             @click="perPage = option"
                        >{{ option }}
                        </div>
                    </div>
                </div>
                <span class="pagination__info-per-page-text">на страницу</span>
            </div>
        </div>

        <div class="pagination__links">
            <span class="pagination__links-button pagination__links-button-icon"
                  :class="{'pagination__links-button-link' : pagination.current_page !== 1}"
                  @click="setPreviousPage"><IconBackward/></span>
            <span class="pagination__links-button" v-if="pagination.last_page > 1 && isLong"
                  :class="{'pagination__links-button-link-active': pagination.current_page === 1, 'pagination__links-button-link': pagination.last_page > 1}"
                  @click="setFirstPage"
            >{{ 1 }}</span>
            <span class="pagination__links-spacer" v-if="hasBefore"><span>...</span></span>
            <span class="pagination__links-button" v-for="page in pages"
                  :class="{
                    'pagination__links-button-link-active': page === pagination.current_page && pagination.last_page > 1,
                    'pagination__links-button-link': pagination.last_page > 1,
                  }"
                  :key="page"
                  @click="setPage(page)"
            >{{ page }}</span>
            <span class="pagination__links-spacer" v-if="hasAfter"><span>...</span></span>
            <span class="pagination__links-button pagination__links-button-link" v-if="pagination.last_page > 1 && isLong"
                  :class="{'pagination__links-button-link-active': pagination.current_page === pagination.last_page}"
                  @click="setLastPage"
            >{{ pagination.last_page }}</span>
            <span class="pagination__links-button pagination__links-button-icon"
                  :class="{'pagination__links-button-link' : pagination.current_page !== pagination.last_page}"
                  @click="setNextPage"><IconForward/></span>
        </div>
    </div>
</template>

<script setup lang="ts">
import {ListPagination} from "@/Core/List";
import {computed, ref} from "vue";
import IconBackward from "@/Icons/IconBackward.vue";
import IconForward from "@/Icons/IconForward.vue";
import GuiLink from "@/Components/GUI/GuiLink.vue";

const props = defineProps<{
    pagination?: ListPagination,
    options?: Array<number>,
}>();

const emit = defineEmits<{
    (e: 'pagination', page: number, perPage: number): void,
}>()

const optionsList = computed((): Array<number> => {
    if (typeof props.options !== "undefined") return props.options;
    return [5, 10, 25, 50, 100, 250];
});

const showOptions = ref<boolean>(false);

function toggle() {
    if (showOptions.value === true) {
        showOptions.value = false;
        setTimeout(() => {
            window.removeEventListener('click', close);
        }, 100);
    } else {
        showOptions.value = true;
        setTimeout(() => {
            window.addEventListener('click', close);
        }, 100);
    }
}

function close() {
    window.removeEventListener('click', close);
    showOptions.value = false;
}

const max_links: number = 6;

const shown = computed((): string => {
    if (typeof props.pagination === "undefined") return '—';
    return props.pagination.from && props.pagination.to ? `${props.pagination.from} - ${props.pagination.to}` : '0'
});

const pages = computed((): Array<number> => {
    let pages: Array<number> = [];
    if (typeof props.pagination === "undefined") return pages;

    if (props.pagination.last_page - 2 <= max_links) {
        for (let i: number = 1; i <= props.pagination.last_page; i++) {
            pages.push(i);
        }
    } else {
        let start: number = props.pagination.current_page - Math.floor(max_links / 2) + 1;
        if (start < 2) {
            start = 2;
        }
        if (start + max_links > props.pagination.last_page) {
            start = props.pagination.last_page - max_links + 1;
        }
        for (let i: number = start; i < start + max_links - 1; i++) {
            pages.push(i);
        }
    }
    return pages;
})

const isLong = computed((): boolean => {
    return typeof props.pagination !== "undefined" && props.pagination.last_page - 2 > max_links;
});

const hasBefore = computed((): boolean => {
    return typeof props.pagination !== 'undefined' && (props.pagination.last_page - 2 > max_links) && (props.pagination.current_page - Math.floor(max_links / 2) > 1);
});


const hasAfter = computed((): boolean => {
    return typeof props.pagination !== 'undefined' && (props.pagination.last_page - 2 > max_links) && (props.pagination.current_page - Math.floor(max_links / 2) + max_links < props.pagination.last_page);
});

const perPage = computed({
    get: (): number => {
        return props.pagination !== undefined ? props.pagination.per_page : 10;
    },
    set: (value: number): void => {
        if (props.pagination !== undefined && props.pagination.per_page !== value) {
            updatePagination(1, value);
        }
    }
});

function setFirstPage(): void {
    updatePagination(1, perPage.value);
}

function setPreviousPage(): void {
    updatePagination(props.pagination ? props.pagination.current_page - 1 : 1, perPage.value);
}

function setPage(page: number): void {
    updatePagination(page, perPage.value);
}

function setNextPage(): void {
    updatePagination(props.pagination ? props.pagination.current_page + 1 : 1, perPage.value);
}

function setLastPage(): void {
    updatePagination(props.pagination ? props.pagination.last_page : 1, perPage.value);
}

function updatePagination(page: number, perPage: number): void {
    if (typeof props.pagination === 'undefined' || page < 1 || page > props.pagination.last_page || (page === props.pagination.current_page && perPage === props.pagination.per_page)) {
        return;
    }

    emit('pagination', page, perPage);
}
</script>

<style lang="scss">
@use "sass:math";
@import "@/variables.scss";

.pagination {
    display: flex;
    flex-direction: column;
    margin-top: $base_size_unit * 2;
    @include no_selection;

    &__info {
        display: flex;
        justify-content: center;
        box-sizing: border-box;
        margin: 8px 0;

        &-shown, &-per-page {
            flex-grow: 0;
            flex-shrink: 0;
            @include font(14px);
            color: $color_text_black;
        }

        &-shown {
            padding-right: 5px;
        }

        &-per-page {
            display: flex;
            flex-direction: row;

            &-select {
                margin-right: 5px;
                position: relative;

                &-options {
                    position: absolute;
                    left: 50%;
                    top: -5px;
                    transform: translate(-50%, -100%);
                    display: flex;
                    flex-direction: column-reverse;
                    box-shadow: $shadow_1;
                    border-radius: 2px;
                    background-color: $color_white;
                    box-sizing: border-box;
                    padding: 6px 12px;
                    transition: opacity $animation $animation_time, visibility $animation $animation_time;
                    opacity: 0;
                    visibility: hidden;
                    z-index: 20;
                    border: 1px solid $color_gray_lighten_1;

                    &:before {
                        content: '';
                        display: block;
                        background-color: inherit;
                        width: 6px;
                        height: 6px;
                        position: absolute;
                        left: 50%;
                        bottom: -4px;
                        transform: translate(-50%, 0) rotate(45deg);
                        border-color: inherit;
                        border-style: solid;
                        border-width: 0 1px 1px 0;
                    }

                    &-shown {
                        opacity: 1;
                        visibility: visible;
                    }

                    &-option {
                        text-align: center;
                        cursor: pointer;
                        transition: color $animation $animation_time;
                        line-height: 20px;

                        &:hover {
                            color: $color_default;
                        }

                        &-selected {
                            color: $color_default_hover;
                        }
                    }
                }
            }
        }
    }

    &__links {
        flex-grow: 1;
        display: flex;
        justify-content: center;
        line-height: line_height($base_size_unit * 4);
        @include font(14px);

        &-spacer {
            width: $base_size_unit * 2;
            height: $base_size_unit * 4;
            line-height: line_height($base_size_unit * 4);
            text-align: center;
            cursor: default;
            color: $color_gray;
            margin-right: 5px;
        }

        &-button {
            width: $base_size_unit * 4;
            height: $base_size_unit * 4;
            line-height: $base_size_unit * 4 - 2px;
            text-align: center;
            cursor: default;
            border-radius: 50%;
            box-sizing: border-box;
            color: $color_gray_lighten_2;
            border: 1px solid transparent;
            background-color: transparent;

            &:not(:last-child) {
                margin-right: 5px;
            }

            &-link {
                cursor: pointer;
                color: $color_text_black;
                border: 1px solid transparentize($color_gray_lighten_2, 0.5);
                transition: box-shadow $animation $animation_time;

                &:not(&-active):hover {
                    box-shadow: $shadow_hover;
                }

                &-active {
                    color: $color_white !important;
                    border-color: $color_default_lighten_1 !important;
                    background-color: $color_default_lighten_1 !important;
                    cursor: default;
                }
            }

            &-icon {
                display: flex;
                justify-content: center;
                align-items: center;

                & > svg {
                    width: $base_size_unit * 2;
                    height: $base_size_unit * 2;
                }
            }
        }
    }
}
</style>
