<template>
    <InputBox class="input-image" :label="false" :dirty="false" :disabled="false" :valid="false">

    </InputBox>
</template>

<script setup lang="ts">
import InputBox from "@/Components/Input/Helpers/InputBox.vue";
import {computed} from "vue";

// props: {
//     name: String,
//     modelValue: {type: Array, default: null},
//     original: {type: Array, default: null},
//     valid: {type: Boolean, default: true},
//     disabled: {type: Boolean, default: false},
//
//     maxImages: {type: Number, default: 0},
// },

const canAdd = computed((): boolean => {
    return true;
});

// handleFile(e) {
//     this.processFiles(e.target.files);
//     e.target.value = '';
// },

// processFiles(files) {
//     for (let i = 0; i < files.length; i++) {
//         const file = files[i];
//         if (!file.type.startsWith('image/')) {
//             continue;
//         }
//         let val = {
//             name: file.name,
//             type: file.type,
//             size: file.size,
//             width: null,
//             height: null,
//             content: null,
//         }
//
//         const reader = new FileReader();
//         reader.onload = (val => {
//             return e => {
//                 val.content = e.target.result;
//                 if (this.canAdd) {
//                     let value = clone(this.modelValue);
//                     value.push(val);
//                     this.$emit('update:modelValue', value);
//                     this.$emit('change', value, this.name);
//                 }
//             }
//         })(val);
//
//         reader.readAsDataURL(file);
//     }
// },

// discard(index) {
//     let value = clone(this.modelValue);
//     value.splice(index, 1);
//     this.$emit('update:modelValue', value);
//     this.$emit('change', value, this.name);
// },
</script>

<style lang="scss">
@import "@/variables";

.input-image {

}
</style>
