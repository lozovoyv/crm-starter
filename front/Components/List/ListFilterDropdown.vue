<template>
    <ListBarItem :title="title">
        <InputDropDown
            :modelValue="list.filters[name]"
            :clearable="true"
            :placeholder="placeholder"
            :original="original"
            :disabled="disabled"
            :has-errors="hasErrors"
            :has-null="hasNull"
            :null-caption="nullCaption"
            :empty-caption="emptyCaption"
            :options="options"
            :show-cisabled-options="showDisabledOptions"
            :id-key="idKey"
            :caption-key="captionKey"
            :filter-key="filterKey"
            :hint-key="hintKey"
            :multi="multi"
            :search="search"
            @change="changeFilter"
        />
    </ListBarItem>
</template>

<script setup lang="ts">
import {List} from "@/Core/List";
import InputDropDown from "@/Components/Input/InputDropDown.vue";
import {DropDownOptions, DropDownValueType} from "@/Components/Input/Helpers/Types";
import ListBarItem from "@/Components/List/ListBarItem.vue";

const props = withDefaults(defineProps<{
    list: List<any>,
    title?: string,
    name: string,
    manual?: boolean,

    // common props
    original?: DropDownValueType,
    disabled?: boolean,
    hasErrors?: boolean,
    clearable?: boolean,

    // dropdown props
    placeholder?: string,
    hasNull?: boolean,
    nullCaption?: string,
    emptyCaption?: string,

    options: DropDownOptions,
    showDisabledOptions?: boolean,
    idKey?: string,
    captionKey?: string,
    filterKey?: string,
    hintKey?: string,

    multi?: boolean,

    search?: boolean,
}>(), {
    original: undefined,
});

if(props.list.filters[props.name] === undefined) {
    props.list.filters[props.name] = null;
}

function changeFilter(value: string | number | boolean | null | Array<string | number>): void {
    if (!props.manual) {
        props.list.filters[props.name] = value;
        props.list.load();
    }
}
</script>

