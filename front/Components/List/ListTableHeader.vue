<template>
    <thead class="list-table__header" v-if="items">
    <tr class="list-table__header-row">
        <th class="list-table__header-cell" v-for="item in items">
            <div class="list-table__header-cell-inner" :class="{'list-table__header-cell-inner-ordered': item.ordering}" @click="changeOrder(item)">
                <span class="list-table__header-cell-inner-title">{{ item.title }}</span>
                <span class="list-table__header-cell-inner-order" v-if="item.ordering">
                <span class="list-table__header-cell-inner-order-desc" :class="{'list-table__header-cell-inner-order-desc-active':item.ordered && item.order === 'desc'}">
                    <IconSortUp/>
                </span>
                <span class="list-table__header-cell-inner-order-asc" :class="{'list-table__header-cell-inner-order-asc-active':item.ordered && item.order === 'asc'}">
                    <IconSortDown/>
                </span>
            </span>
            </div>
        </th>
        <th class="list-table__header-cell list-table__header-cell-actions" v-if="actions"></th>
    </tr>
    </thead>
</template>

<script setup lang="ts">
import {computed} from "vue";
import IconSortUp from "@/Icons/IconSortUp.vue";
import IconSortDown from "@/Icons/IconSortDown.vue";
import {List} from "@/Core/List";

type HeaderItem = {
    key: string,
    title: string | null,
    ordering: boolean,
    order: 'asc' | 'desc' | null,
    ordered: boolean,
};

const props = defineProps<{
    actions?: boolean,
    list: List<any>
}>();

const items = computed((): Array<HeaderItem> => {
    if (!props.list.is_loaded || !props.list.titles) {
        return [];
    }
    return Object.keys(props.list.titles).map((key: string) => {
        return {
            key: key,
            title: typeof props.list.titles !== 'undefined' && typeof props.list.titles[key] !== 'undefined' ? props.list.titles[key] : null,
            ordering: props.list.ordering?.indexOf(key) !== -1,
            order: props.list.order,
            ordered: props.list.order_by === key,
        };
    })
});

function changeOrder(item: HeaderItem): void {
    if (!item.ordering) {
        return;
    }
    if (props.list.order_by === item.key) {
        if (props.list.order !== 'asc') {
            props.list.order = 'asc';
        } else {
            props.list.order = 'desc';
        }
    } else {
        props.list.order_by = item.key;
        props.list.order = 'asc';
    }
    props.list.load();
}
</script>

<style lang="scss">
@import "@/variables.scss";

.list-table__header {
    background-color: $color_white;
    position: sticky;
    top: 0;
    z-index: 10;

    &-row {
        border-style: solid;
        border-color: $color_gray_lighten_2;
        border-width: 0 0 1px 0;
    }

    &-cell {
        color: $color_text_black;
        vertical-align: middle;
        font-family: $project_font;
        font-size: 14px;
        text-transform: uppercase;
        box-sizing: border-box;
        padding: 7px 15px;
        min-height: 60px;
        text-align: left;
        font-weight: 600;
        cursor: default;
        @include no_selection;

        &-actions {
            width: 70px;
        }

        &-inner {
            line-height: 20px;
            position: relative;
            box-sizing: border-box;
            display: flex;
            align-items: center;

            &-ordered {
                cursor: pointer;

                &:hover {
                    color: $color_default_lighten_1;
                }
            }

            &-order {
                display: inline-block;
                width: 10px;
                height: 12px;
                margin-left: 5px;

                &-desc, &-asc {
                    display: block;
                    width: 8px;
                    height: 6px;
                    color: $color_gray_lighten_2;
                    position: relative;

                    & > svg {
                        width: 100%;
                        vertical-align: middle;
                        display: block;
                        position: absolute;
                        left: 0;
                        top: 50%;
                        transform: translateY(-50%);
                    }

                    &-active {
                        color: $color_default;
                    }
                }
            }
        }
    }
}
</style>
