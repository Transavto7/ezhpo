<template>
    <b-col>
        <b-button
            :class="collapsed ? null : 'collapsed'"
            :aria-expanded="collapsed ? 'true' : 'false'"
            aria-controls="collapse-4"
            size="sm"
            @click="collapsed = !collapsed"
        >
            Раскрыть пункты выпуска
        </b-button>
        <b-collapse id="collapse-4" v-model="collapsed" class="mt-2">
            <b-card>
                <b-row class=" mx-0 px-0 mb-3">
                    <div class="col-lg-6">
                        <b-form-input v-model="search" placeholder="Поиск ПВ" />
                    </div>
                    <div class="col-lg-6">
                        <b-button
                            variant="success"
                            size="sm"
                            @click="handleSelectAllClick">
                            Выбрать все
                        </b-button>
                        <b-button
                            size="sm"
                            @click="handleUnselectAllClick">
                            Убрать все
                        </b-button>
                    </div>
                </b-row>
                <b-form-group label="Пункты выпуска:" v-slot="{ ariaDescribedby }">
                    <b-form-checkbox-group
                        :aria-describedby="ariaDescribedby"
                        name="flavour-2"
                        v-model="selectedItems"
                        @input="$emit('input', selectedItems)"
                    >
                        <b-row>
                            <div class="box">
                                <div v-for="(item, index) in searchedItems">
                                    <b-col>
                                        <b-form-checkbox
                                            :value="item.id"
                                            :key="index"
                                        >
                                            {{ item.name }}
                                        </b-form-checkbox>
                                    </b-col>
                                </div>
                            </div>
                        </b-row>
                    </b-form-checkbox-group>
                </b-form-group>
            </b-card>
        </b-collapse>
    </b-col>
</template>

<script>
export default {
    name: "UserPvsList",

    props: {
        allItems: {
            required: true,
            type: Array
        },
        selectedItems: {
            required: true,
            type: Array
        }
    },

    data() {
        return {
            searchedItems: [],
            search: '',
            collapsed: false
        }
    },

    mounted() {
        this.searchedItems = this.allItems
    },

    watch: {
        search(val) {
            val = val.trim().toLowerCase();

            if (!val.length) {
                this.searchedItems = this.allItems
                return;
            }

            this.searchedItems = this.allItems.filter((item) => {
                return item.name.toLowerCase().match(val)
            })
        },
    },

    methods: {
        handleSelectAllClick() {
            this.$emit('input', this.allItems.map((item) => (item.id)))
        },
        handleUnselectAllClick() {
            this.$emit('input', [])
        },
    }
}
</script>

<style scoped>

</style>
