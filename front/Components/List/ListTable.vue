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
        <template v-if="list.is_loaded && list.list.length > 0">
            <table class="list-table">
                <slot name="header" v-if="$slots.header"/>
                <ListTableHeader v-else :list="list" :actions="actions"/>
                <tbody class="list-table__body">
                <slot/>
                </tbody>
            </table>
            <ListPagination :list="list"/>
        </template>
        <div v-else class="list-table-container__message" :class="{'list-table-container__message-error': error}">
            {{ notification }}
        </div>
    </div>
</template>

<script setup lang="ts">
import ListTableHeader from "@/Components/List/ListTableHeader.vue";
import {List} from "@/Core/List";
import ListPagination from "@/Components/List/ListPagination.vue";
import {computed} from "vue";

const props = defineProps<{
    actions?: boolean,
    list: List<any>,
    message?: string,
}>();

const error = computed((): boolean => {
    return props.list.is_forbidden || props.list.is_not_found;
});

const notification = computed((): string | null => {
    if (props.list.is_loading) {
        return 'Загрузка...';
    } else if (props.list.is_forbidden) {
        return 'У Вас недостаточно прав для просмотра этой страницы';
    } else if (props.list.is_not_found) {
        return 'Страница не найдена';
    } else if (props.message) {
        return props.message;
    }
    return 'Список пуст';
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

        &-error{
            color: $color-error;
            border: 1px solid transparentize($color_error_lighten-2, 0.5);
        }
    }
}

.list-table {
    border-collapse: collapse;
    min-width: 100%;
}
</style>
