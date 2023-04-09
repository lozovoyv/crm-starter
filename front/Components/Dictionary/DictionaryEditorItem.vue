<template>
    <!--         :class="{'dictionary-editor-item__dragging': isDragging}"-->
    <tr class="dictionary-editor-item"
        :draggable="orderable"
        @dragstart="dragstart"
        @drop="drop"
        @dragenter="dragenter"
        @dragend="dragend"
    >
        <td class="dictionary-editor-item__head" :class="{'dictionary-editor-item__head-draggable': orderable}">
            <div class="dictionary-editor-item__head-wrapper">
                <IconGripVertical class="dictionary-editor-item__head-icon" v-if="orderable"/>
                <GuiActivityIndicator class="dictionary-editor-item__head-indicator" :active="item.enabled"/>
            </div>
        </td>
        <td draggable="false" v-for="field in fields" class="dictionary-editor-item__body" :class="{'dictionary-editor-item__body-last': field.last}">
            {{ field.value }}
        </td>
        <td class="dictionary-editor-item__actions" draggable="false">
            <div v-if="item.locked" class="dictionary-editor-item__actions-button dictionary-editor-item__actions-button-system"
                 title="Системная запись">
                <IconLock/>
            </div>
            <template v-else>
                <template v-if="switchable">
                    <div v-if="item.enabled" class="dictionary-editor-item__actions-button dictionary-editor-item__actions-button-on" title="Отключить" @click="switchOff">
                        <IconToggleOn/>
                    </div>
                    <div v-else class="dictionary-editor-item__actions-button dictionary-editor-item__actions-button-off" title="Включить" @click="switchOn">
                        <IconToggleOff/>
                    </div>
                </template>
                <div class="dictionary-editor-item__actions-button dictionary-editor-item__actions-button-edit" title="Редактировать" @click="editItem">
                    <IconEdit/>
                </div>
                <div class="dictionary-editor-item__actions-button dictionary-editor-item__actions-button-remove" title="Удалить" @click="deleteItem">
                    <IconCross/>
                </div>
            </template>
        </td>
    </tr>
</template>

<script setup lang="ts">
import IconGripVertical from "@/Icons/IconGripVertical.vue";
import IconLock from "@/Icons/IconLock.vue";
import IconCross from "@/Icons/IconCross.vue";
import IconEdit from "@/Icons/IconEdit.vue";
import IconToggleOff from "@/Icons/IconToggleOff.vue";
import IconToggleOn from "@/Icons/IconToggleOn.vue";
import {computed} from "vue";
import GuiActivityIndicator from "@/Components/GUI/GuiActivityIndicator.vue";

const props = defineProps<{
    item: { id: number, enabled?: boolean, order?: number | null, locked?: boolean, hash: string | null, [index: string]: string | number | boolean | undefined | null },
    options: { [index: string]: any },
    orderable?: boolean,
    switchable?: boolean,
}>();

const emit = defineEmits<{
    (e: 'switch-off', id: number): void
    (e: 'switch-on', id: number): void
    (e: 'edit', id: number): void
    (e: 'remove', id: number): void
    (e: 'dragstart', id: number): void
    (e: 'drop'): void
    (e: 'dragenter', id: number): void
    (e: 'dragend'): void
}>();

const fields = computed((): Array<{ name: string, value: string | null, last: boolean }> => {
    let fields: Array<{ name: string, value: string | null, last: boolean }> = [];
    Object.keys(props.options).map(name => {
        const option: { [index: string]: any } = props.options[name];
        const type: string = option['type'];
        let value: string | null;
        if (type === 'boolean') {
            value = Boolean(props.item[name]) ? 'да' : 'нет';
        } else {
            value = props.item[name] ? String(props.item[name]) : null;
        }
        fields.push({name: name, value: value, last: false});
    });
    fields[fields.length - 1].last = true;

    return fields;
});

function switchOff(): void {
    if (props.switchable) {
        emit('switch-off', props.item.id);
    }
}

function switchOn(): void {
    if (props.switchable) {
        emit('switch-on', props.item.id);
    }
}

function editItem(): void {
    emit('edit', props.item.id);
}

function deleteItem(): void {
    emit('remove', props.item.id);
}

function dragstart(event: DragEvent) {
    if (!props.orderable) {
        return false;
    }
    if (event.dataTransfer) {
        event.dataTransfer.setData('text/plain', String(props.item['id']));
        event.dataTransfer.effectAllowed = "linkMove";
        emit('dragstart', props.item['id']);
    }
}

function drop(): boolean {
    if (!props.orderable) {
        return false;
    }
    emit('drop');
    return true;
}

function dragenter() {
    if (!props.orderable) {
        return false;
    }
    emit('dragenter', props.item['id']);
}

function dragend() {
    if (!props.orderable) {
        return false;
    }
    emit('dragend');
}
</script>

<style lang="scss">
@use "sass:math";
@import "@/variables.scss";

.dictionary-editor-item {
    height: $base_size_unit;

    &__head {
        border-width: 1px 0 1px 1px;
        border-style: solid;
        border-color: transparentize($color_gray_lighten_2, 0.25);
        border-radius: 5px 0 0 5px;
        width: $base_size_unit;
        vertical-align: middle;
        text-align: center;
        position: relative;
        box-sizing: border-box;
        padding: 0 10px;
        white-space: nowrap;

        &-draggable {
            cursor: grab;
        }

        &-wrapper {
            display: flex;
            align-items: center;
        }

        &-icon {
            height: math.div($base_size_unit, 1.75);
            display: block;
            color: $color_gray_darken_2;
            margin-right: 10px;
        }

        &-indicator {
            display: block;
        }

        &:after {
            content: '';
            width: 1px;
            height: calc(100% - 8px);
            right: 0;
            top: 4px;
            background-color: $color_gray_lighten_2;
            position: absolute;
        }
    }

    &__body {
        border-width: 1px 0 1px 0;
        border-style: solid;
        border-color: transparentize($color_gray_lighten_2, 0.25);
        position: relative;
        font-family: $project_font;
        color: $color_text_black;
        padding: 0 8px;

        &:after {
            content: '';
            width: 1px;
            height: calc(100% - 8px);
            right: 0;
            top: 4px;
            background-color: $color_gray_lighten_2;
            position: absolute;
        }
    }

    &__actions {
        border-width: 1px 1px 1px 0;
        border-style: solid;
        border-color: transparentize($color_gray_lighten_2, 0.25);
        border-radius: 0 5px 5px 0;
        white-space: nowrap;
        vertical-align: middle;
        padding-left: 5px;
        width: $base_size_unit;

        &-button {
            height: $base_size_unit;
            width: $base_size_unit;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
            box-sizing: border-box;
            vertical-align: middle;

            & > svg {
                height: 100%;
                width: 100%;
                display: block;
            }

            &-on {
                color: $color_success;
                cursor: pointer;
            }

            &-off {
                color: $color_error;
                cursor: pointer;
            }

            &-edit {
                color: $color_default;
                cursor: pointer;

                & > svg {
                    height: 80%;
                }
            }

            &-system {
                color: $color_gray;

                & > svg {
                    height: 80%;
                }
            }

            &-remove {
                color: $color_error;
                cursor: pointer;
            }
        }
    }
}
</style>
