<script>
import Swal from "sweetalert2";
import axios from "axios";

export default {
    props: ['fields', 'reloadInterval'],
    data() {
        return {
            order: {
                by: true,
                key: 'id'
            },
            rows: [],
        }
    },
    async created() {
        await this.loadData();
        const audio = new Audio('/sounds/notice.mp3');
        const promise = () => new Promise(async (resolve, reject) => {
            const data = this.rows.map(row => {
                return row.id;
            });

            await this.loadData();

            for (const key in this.rows) {
                const row = this.rows[key];
                if (!data.includes(row.id)) {
                    audio.play();
                    break;
                }
            }

            setTimeout(() => {
                promise().then(resolve).catch(reject);
            }, this.reloadInterval);
        });
        promise();
    },
    methods: {
        async loadData() {
            this.rows = await axios.get('/pak/list', {
                params: {
                    order_by: this.order.by ? 'ASC' : 'DESC',
                    order_key: this.order.key,
                }
            }).then(({data}) => {
                return data;
            });
        },
        notAdmittedField(row, field) {
            const reasons = row.not_admitted_reasons ?? [];

            if (reasons.includes(field)) {
                return {
                    backgroundColor: 'pink'
                }
            }

            return {}
        }
    },
}
</script>

<template>
    <table id="ankets-table" class="ankets-table table table-striped table-sm">
        <thead>
            <tr>
                <th v-for="field in fields" :data-field-key="field.field" :key="field.id">
                            <span class="user-select-none"
                                  :data-toggle="{ tooltip: field.content }"
                                  :data-html="true"
                                  data-trigger="click hover"
                                  :title="field.content"
                            >
                                {{ field.name }}
                            </span>

                    <a href="javascript:void(0);" class="not-export" @click="order.key = field.field; order.by = !order.by">
                        <i class="fa fa-sort"></i>
                    </a>
                </th>
                <th class="not-export">#</th>
            </tr>
        </thead>

        <tbody>
        <tr v-for="row in rows" :key="row.id" :data-field="row.id">
            <td v-for="field in fields" :key="field.id" :style="notAdmittedField(row, field.field)">
                <a :href="row[field.field]" v-if="field.field === 'photos' && row[field.field]">
                    <i class="fa fa-camera"></i>
                </a>
                <a :href="row[field.field]" v-else-if="field.field === 'videos' && row[field.field]">
                    <i class="fa fa-video-camera"></i>
                </a>
                <a :href="`/elements/Driver?filter=1&fio=${row[field.field]}`" v-else-if="field.field === 'driver_fio'">
                    {{ row[field.field] }}
                </a>
                <span v-else>{{ row[field.field] }}</span>
            </td>
            <td class="td-option not-export d-flex">
                <a :href="`/forms/${row.id}`"
                   class="btn btn-info btn-sm mr-1">
                    <i class="fa fa-search"></i>
                </a>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<style>
thead {
    position: sticky;
    top: 0;
    background-color: white;
}
</style>
