<script>
import { defineComponent } from 'vue'
import { VueSelect } from 'vue-select'
import { Deselect, OpenIndicator } from './conf'
import debounce from "../helpers/debounce";
import logsFilter from "../components/logs/logs-filter.vue";

export default defineComponent({
    name: 'VSelectRemote',
    components: { VueSelect },
    props: {
        value: {
            type: [Object, Array],
            required: false,
            default: null,
        },
        fetchOptionsAction: {
            type: Function,
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
        limit: {
            type: Number,
            default: 10,
        },
    },
    emits: ['input'],
    data() {
        return {
            loading: false,
            options: [],
            Deselect,
            OpenIndicator,
        }
    },
    computed: {
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

        handleOpen() {
            this.options = []
            this.fetchOptions('')
        },

        fuseSearch(options) {
            return options
        },

        fetchOptions: debounce(async function fetchOptions(search) {
            this.loading = true
            const { data } = await this.fetchOptionsAction({
                search: search.trim(),
            })

            this.options = []
            this.options = data
                .map((item) => ({
                    code: String(item.id),
                    label: item.name,
                }))
                .slice(0, this.limit)
            this.loading = false
        }, 300),
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
            :filter="fuseSearch"
            max-height="100px"
            :multiple="multiple"
            :options="options"
            :placeholder="displayedPlaceholder"
            :value="displayedValue"
            @input="handleSelect"
            @open="handleOpen"
            @search="fetchOptions"
        >
            <template #no-options="{ search }">
                <span v-if="loading">Загрузка...</span>
                <span v-else-if="search.length < 1"> Введите текст для поиска </span>
                <span v-else>Ничего не найдено</span>
            </template>
        </vue-select>
    </div>
</template>

<style lang="scss">
@import "../../sass/libs/vue-select";
</style>
