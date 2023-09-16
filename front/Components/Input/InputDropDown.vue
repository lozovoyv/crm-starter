<template>
    <InputBox class="input-dropdown" :label="false" :dirty="isDirty" :disabled="disabled" :has-errors="hasErrors" :is-empty="isEmpty" :clearable="clearable" @clear="clear">

        <div class="input-dropdown__value" @click="toggle">
            <span class="input-dropdown__value-placeholder" v-if="isEmpty">{{ placeholder }}</span>
            <span class="input-dropdown__value-single" v-else-if="!multi && selected.length !== 0" :title="selected[0].caption">{{ selected[0].caption }}</span>
            <div class="input-dropdown__value-multi" v-else-if="multi">
                <span class="input-dropdown__value-multi-item" :class="{'input-dropdown__value-multi-item-disabled': disabled}" v-for="item in selected">
                    {{ item.caption }}
                    <span class="input-dropdown__value-multi-item-remove" @click="removeValue(item)" v-if="!disabled">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 490 490">
                            <polygon fill="currentColor"
                                     points="456.851,0 245,212.564 33.149,0 0.708,32.337 212.669,245.004 0.708,457.678 33.149,490 245,277.443 456.851,490 489.292,457.678 277.331,245.004 489.292,32.337 "/>
                        </svg>
                    </span>
                </span>
            </div>
        </div>

        <span class="input-dropdown__toggle" :class="{'input-dropdown__toggle-expanded': dropped}" @click="toggle"><IconDropdown/></span>

        <div class="input-dropdown__list" :class="{'input-dropdown__list-shown': dropped}" @click="false">
            <InputSearch v-model="terms" class="input-dropdown__list-search" v-if="search"
                         @change="updateHeight"
                         @click.stop="false"
                         :clearable="true"
                         @clear="clearSearch"
                         ref="searchRef"
            />
            <div class="input-dropdown__list-items" ref="list">
                <div class="input-dropdown__list-items-item" v-if="hasNull && !multi" @click="setValue(null)">
                    <span class="input-dropdown__list-items-item-caption">{{ nullCaption ? nullCaption : placeholder }}</span>
                </div>
                <div v-for="value in filteredOptions" class="input-dropdown__list-items-item" :class="{'input-dropdown__list-items-item-current': isCurrent(value)}"
                     @click="setValue(value)">
                    <span class="input-dropdown__list-items-item-caption" v-html="highlight(value.caption, terms)"/>
                    <span class="input-dropdown__list-items-item-hint" v-if="value.hint" v-html="value.hint"/>
                </div>
                <div class="input-dropdown__list-empty" v-if="filteredOptions.length === 0">
                    {{ emptyCaption ? emptyCaption : 'Элементов не найдено' }}
                </div>
            </div>

        </div>

        <template #additional v-if="$slots.additional">
            <slot name="additional"/>
        </template>
    </InputBox>
</template>

<script setup lang="ts">
import InputBox from "@/Components/Input/Helpers/InputBox.vue";
import {computed, nextTick, ref, watch} from "vue";
import InputSearch from "@/Components/Input/InputSearch.vue";
import IconDropdown from "@/Icons/IconDropdown.vue";
import {highlight} from "@/Core/Highlight/highlight";
import {DropDownDisplayOption, DropDownDisplayOptions, DropDownOption, DropDownOptions, DropDownValueType} from "@/Components/Input/Helpers/Types";

const props = defineProps<{
    // common props
    name?: string,
    modelValue?: DropDownValueType,
    original?: DropDownValueType,
    disabled?: boolean,
    hasErrors?: boolean,
    clearable?: boolean,

    // dropdown props
    placeholder?: string | null,
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
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | number | boolean | null | Array<string | number>): void,
    (e: 'change', value: string | number | boolean | null | Array<string | number>, name: string | undefined, payload: any): void,
    (e: 'dropped'): void,
}>()

const terms = ref<string | null>(null);

const dropped = ref<boolean>(false);
const removing = ref<boolean>(false);

const list = ref<HTMLDivElement | undefined>(undefined);
const searchRef = ref<InstanceType<typeof InputSearch> | undefined>(undefined);

const isDirty = computed((): boolean => {
    return props.original !== undefined && JSON.stringify(props.original) !== JSON.stringify(props.modelValue);
});

const isEmpty = computed((): boolean => {
    return props.modelValue === null || props.modelValue === '' || !!props.multi && typeof props.modelValue === 'object' && Object.keys(props.modelValue).length === 0;
});

function clear(): void {
    emit('update:modelValue', null);
    emit('change', null, props.name, null);
}

const allFormattedOptions = computed((): DropDownDisplayOptions => {
    let options: DropDownDisplayOptions = [];
    Object.keys(props.options).map((index) => {
        let option: DropDownOption = props.options[<any>index];
        if (typeof option === "object") {

            const key: string | number = option[props.idKey !== undefined ? props.idKey : 'id'];
            const caption: string = option[props.captionKey !== undefined ? props.captionKey : 'name'];
            const hint: string | undefined = option[props.hintKey !== undefined ? props.hintKey : 'hint'];
            const filterKey: string = props.filterKey !== undefined ? props.filterKey : 'enabled';
            const enabled: boolean = option[filterKey] === undefined || Boolean(option[filterKey]);

            options.push({key: key, caption: caption, hint: hint, enabled: enabled, payload: option});
        } else {
            const key: number | typeof NaN = Number(index);
            options.push({key: !isNaN(key) ? key : index, caption: String(option), hint: undefined, enabled: true, payload: option});
        }
    });
    return options;
});

const filteredOptions = computed((): DropDownDisplayOptions => {
    return allFormattedOptions.value.filter((option: DropDownDisplayOption) => {
        return (props.showDisabledOptions || option.enabled)
            && (terms.value === null || option.caption.toLowerCase().indexOf(terms.value.toLowerCase()) !== -1)
            && (!props.multi || !Array.isArray(props.modelValue) || props.modelValue.indexOf(option.key) === -1);
    });
});

watch(filteredOptions, () => {
    updateHeight();
});

const selected = computed((): DropDownDisplayOptions => {
    if (isEmpty.value) {
        return [];
    }
    let selected: DropDownDisplayOptions = [];
    allFormattedOptions.value.map((option: DropDownDisplayOption) => {
        if (Array.isArray(props.modelValue) && props.modelValue.indexOf(option.key) !== -1 || props.modelValue === option.key) {
            selected.push(option)
        }
    });
    return selected;
});

function isCurrent(option: DropDownDisplayOption): boolean {
    if (props.modelValue === null || props.modelValue === '') {
        return false;
    }
    if (Array.isArray(props.modelValue)) {
        return props.modelValue.indexOf(option.key) !== -1;
    } else {
        return option !== null && props.modelValue === option.key;
    }
}

function setValue(value: DropDownDisplayOption | null): void {
    if (!props.multi) {
        emit('update:modelValue', value === null ? null : value.key);
        if (value === null)
            emit('change', null, props.name, null);
        else {
            emit('change', value.key, props.name, value.payload);
        }
    } else {
        let values: Array<string | number> = [];
        selected.value?.map(option => {
            values.push(option.key);
        })
        if (value !== null) {
            values.push(value.key);
        }
        values = values.sort().filter(val => values.indexOf(val) !== -1);
        emit('update:modelValue', values);
        emit('change', values, props.name, values);
    }
}

function removeValue(value: DropDownDisplayOption): void {
    if (props.disabled) {
        return;
    }
    removing.value = true;
    let values: Array<string | number> = [];
    selected.value?.map(option => {
        if (value.key !== option.key) {
            values.push(option.key);
        }
    });
    if (values.length === 0) {
        emit('update:modelValue', null);
        emit('change', null, props.name, null);
    } else {
        emit('update:modelValue', values);
        emit('change', values, props.name, values);
    }
    updateHeight();
}

function clearSearch(): void {
    terms.value = null;
    updateHeight();
}

function toggle(): void {
    if (props.disabled) {
        return;
    }
    if (removing.value) {
        removing.value = dropped.value;
        return;
    }
    if (dropped.value === true) {
        dropped.value = false;
        setTimeout(() => {
            window.removeEventListener('click', close);
        }, 100);
    } else {
        dropped.value = true;
        terms.value = null;
        updateHeight();
        emit('dropped');
        setTimeout(() => {
            window.addEventListener('click', close);
            if (searchRef.value) {
                searchRef.value.focus();
            }
        }, 100);
    }
}

function close() {
    if (removing.value) {
        removing.value = false;
        return;
    }
    window.removeEventListener('click', close);
    dropped.value = false;
}

function updateHeight(): void {
    nextTick(() => {
        if (list.value) {
            list.value.style.height = '';
            list.value.style.height = (list.value.clientHeight + 1) + 'px';
        }
    });
}
</script>

<style lang="scss">
@use "sass:math";
@import "@/variables.scss";

.input-dropdown {
    min-height: $base_size_unit * 4;
    position: relative;
    cursor: pointer;

    &:not(&__disabled) {
        cursor: pointer;
    }

    &__value {
        background-color: transparent;
        color: inherit;
        cursor: inherit;
        display: flex;
        flex-grow: 1;
        flex-shrink: 1;
        @include font(16px);
        height: 100%;
        line-height: $base_size_unit * 4 - 2px;
        overflow: hidden;

        &-placeholder {
            padding: 0 0 0 $base_size_unit;
            display: block;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            color: $color_gray;
        }

        &-single {
            padding: 0 0 0 $base_size_unit;
            display: block;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }

        &-multi {
            display: flex;
            flex-grow: 1;
            flex-wrap: wrap;
            box-sizing: border-box;
            padding: 0 1px;
            align-items: center;
            color: inherit;

            &-item {
                display: inline-flex;
                align-items: center;
                @include font(14px);
                color: inherit;
                border: 1px solid transparentize($color_default, 0.5);
                background-color: $color_white;
                height: 22px;
                line-height: 22px;
                box-sizing: border-box;
                border-radius: 2px;
                margin: 2px;
                padding: 0 4px;

                &-disabled {
                    border: 1px solid $color_gray_lighten_1;
                    background-color: unset;
                }

                &-remove {
                    display: inline-flex;
                    width: 16px;
                    height: 100%;
                    cursor: pointer;
                    position: relative;
                    transition: opacity $animation $animation_time;
                    opacity: 0.75;
                    color: $color_error;
                    align-items: center;
                    justify-content: center;
                    left: 2px;

                    & > svg {
                        display: block;
                        width: 10px;
                        height: 10px;
                        transition: transform $animation $animation_time;
                    }

                    &:hover {
                        opacity: 1;

                        & > svg {
                            transform: scale(1.1);
                        }
                    }
                }
            }

        }

    }

    &__toggle {
        align-items: center;
        box-sizing: border-box;
        cursor: inherit;
        display: flex;
        flex-grow: 0;
        flex-shrink: 0;
        justify-content: center;
        padding: $base_size_unit * 0.75;
        width: $base_size_unit * 3;
        position: relative;

        & > svg {
            transition: transform $animation $animation_time;
        }

        &-expanded {
            & > svg {
                transform: rotate(-180deg);
            }
        }
    }

    &__list {
        background-color: $color_white;
        bottom: -1px;
        box-shadow: $shadow_1;
        box-sizing: content-box;
        border: 1px solid $color_white;
        display: flex;
        flex-direction: column;
        left: -1px;
        max-height: $base_size_unit * 32;
        width: 100%;
        position: absolute;
        transform: translateY(100%);
        z-index: 20;
        transition: opacity $animation $animation_time, visibility $animation $animation_time;
        opacity: 0;
        visibility: hidden;

        &-shown {
            opacity: 1;
            visibility: visible;
        }

        &-search {
            margin-bottom: 1px;
        }

        &-empty {
            padding: 8px 0 8px 10px;
        }

        &-search .input-box__border,
        &-search .input-box__border:not(.input-box__border-disabled):focus-within,
        &-search .input-box__border-focus:not(.input-box__border-disabled) {
            border-width: 0 0 1px 0 !important;
            border-color: $color_gray_lighten_2 !important;
            border-radius: 0 !important;
        }

        &-items {
            overflow-x: hidden;
            overflow-y: auto;
            /* W3C standard - сейчас только для Firefox */
            scrollbar-color: $color_default_lighten_2 $color_gray_lighten_2;
            scrollbar-width: thin;

            /* для Chrome/Edge/Safari */
            &::-webkit-scrollbar {
                height: 5px;
                width: 5px;
            }

            &::-webkit-scrollbar-track {
                background: $color_gray_lighten_2;
            }

            &::-webkit-scrollbar-thumb {
                background-color: $color_default_lighten_2;
                border-radius: 2px;
            }

            &-item {
                padding: 5px 10px;
                box-sizing: border-box;
                cursor: pointer;
                display: block;
                transition: color $animation $animation_time;
                @include no_selection();

                &-caption {
                    @include font(15px);
                    color: inherit;
                    line-height: 16px;
                    display: block;
                }

                &-hint {
                    margin-top: 2px;
                    color: $color_text_black;
                    line-height: 16px;
                    display: block;
                    @include font(14px, 300);
                }

                &-current, &:hover {
                    color: $color_default;
                }

                &:first-child {
                    margin-top: 5px;
                }

                &:last-child {
                    margin-bottom: 5px;
                }
            }
        }
    }
}
</style>
