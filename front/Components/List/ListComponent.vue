<template>
    <div class="list-table-container">
        <div class="list-table-container-bar" v-if="!error && ($slots.filters || $slots.search)">
            <div class="list-table-container-bar-filters">
                <slot name="filters"/>
            </div>
            <div class="list-table-container-bar-search">
                <slot name="search"/>
            </div>
        </div>
        <LoadingProgress :loading="list?.state.is_loading">
            <template v-if="!notification">
                <table class="list-table" v-if="!list || list?.list && list?.list.length > 0">
                    <slot name="header" v-if="$slots.header"/>
                    <ListTableHeader v-else-if="list" :list="list" :actions="actions"/>
                    <tbody class="list-table__body">
                    <slot/>
                    </tbody>
                </table>
                <div v-else-if="list?.state.is_loading" class="list-table-container__message">
                    Загрузка
                </div>
                <div v-else-if="$slots.empty" class="list-table-container__message">
                    <slot name="empty"/>
                </div>
                <div v-else class="list-table-container__message">
                    Ничего не найдено
                </div>
                <ListPagination :list="list" v-if="list && !list.config?.without_pagination"/>
            </template>
            <div v-else class="list-table-container__message" :class="{'list-table-container__message-error': error}">
                {{ notification }}
            </div>
        </LoadingProgress>
    </div>
</template>

<script setup lang="ts">
import ListTableHeader from "@/Components/List/ListHeader.vue";
import {List} from "@/Core/List";
import ListPagination from "@/Components/List/ListPagination.vue";
import {computed} from "vue";
import LoadingProgress from "@/Components/LoadingProgress.vue";

const props = defineProps<{
    actions?: boolean,
    list?: List<any>,
    message?: string,
}>();

const error = computed((): boolean => {
    return props.list?.state.is_forbidden || props.list?.state.is_not_found || false;
});

const notification = computed((): string | null => {
    if (props.list?.state.is_forbidden) {
        return 'У Вас недостаточно прав для просмотра этой страницы';
    } else if (props.list?.state.is_not_found) {
        return 'Страница не найдена';
    }

    return null;
})
</script>

<style lang="scss">
@import "@/variables.scss";

.list-table-container {
    &-bar {
        display: flex;
        align-items: flex-start;
        margin-bottom: 10px;

        &-filters {
            flex-grow: 1;
        }

        &-search {
            flex-grow: 0;
        }
    }

    &__message {
        font-family: $project_font;
        font-size: 20px;
        font-weight: 300;
        color: $color_text_black;
        text-align: center;
        padding: 40px 0;
        border: 1px solid transparentize($color_gray_lighten_2, 0.5);
        border-radius: 2px;

        &-error {
            color: $color_error;
            border: 1px solid transparentize($color_error_lighten-2, 0.5);
        }
    }
}

.list-table {
    border-collapse: collapse;
    min-width: 100%;
    margin-bottom: 10px;
}
</style>
