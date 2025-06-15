<script>
import { defineComponent } from 'vue'
import { VueSelect } from 'vue-select'
import { Deselect, OpenIndicator } from './conf';
export default defineComponent({
    name: 'VSelect',
    components: { VueSelect },
    props: {
        value: {
            type: [Object, Array],
            required: false,
            default: null,
        },
        options: {
            type: Array,
            required: true,
        },
        label: {
            type: String,
            required: false,
            default: null,
        },
        placeholder: {
            type: String,
            default: '',
        },
        required: {
            type: Boolean,
            default: false,
        },
        multiple: {
            type: Boolean,
            default: false,
        },
        clearable: {
            type: Boolean,
            default: false,
        },
        closeOnSelect: {
            type: Boolean,
            default: true,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
    },
    emits: ['input'],
    data() {
        return {
            Deselect,
            OpenIndicator,
        }
    },
    computed: {
        displayedOptions() {
            return this.options.map((item) => ({
                code: String(item.id),
                label: item.name,
                ...item,
            }))
        },

        displayedValue() {
            if (this.value === null || this.value === undefined) {
                return null
            }

            let { value } = this

            if (!Array.isArray(value)) {
                value = [value]
            }

            return value.map((item) => ({
                code: item.id,
                label: item.name,
            }))
        },

        displayedPlaceholder() {
            if (this.placeholder) {
                return this.placeholder
            }

            return this.multiple ? 'Выберите одно или несколько значений' : 'Выберите значение'
        },
    },
    methods: {
        handleSelect(value) {
            if (value === null) {
                this.$emit('input', null)
                return
            }

            if (Array.isArray(value)) {
                const items = value.map((item) => ({
                    id: item.code,
                    name: item.label,
                }))
                this.$emit('input', items)
                return
            }

            this.$emit('input', {
                id: value.code,
                name: value.label,
            })
        },
    },
})
</script>

<template>
    <div class="form-control-wrapper">
    <span
        v-if="label"
        class="form-control-label"
        :class="{ required }"
    >
      {{ label }}:
    </span>
        <vue-select
            :clearable="clearable"
            :close-on-select="closeOnSelect"
            :components="{ Deselect, OpenIndicator }"
            :disabled="disabled"
            :multiple="multiple"
            :options="displayedOptions"
            :placeholder="displayedPlaceholder"
            :value="displayedValue"
            @input="handleSelect"
        >
            <template #no-options> Ничего не найдено </template>
            <template #option="option">
                <slot
                    name="option"
                    :option="option"
                />
            </template>
        </vue-select>
    </div>
</template>

<style scoped lang="scss"></style>
