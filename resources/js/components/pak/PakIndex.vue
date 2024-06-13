<script>
import Swal from "sweetalert2";
import axios from "axios";

export default {
    props: [ 'fields', 'time', 'reloadInterval' ],
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

      this.time = new Date(this.time).getTime();
      setInterval(() => {
          this.time += 1000;
          this.rows.forEach(row => {
              this.setTimer(row);
          });
          this.$forceUpdate();
      }, 1000);
    },
    methods: {
        async loadData() {
            this.rows = await axios.get('/pak/list', {
                params: {
                    order_by: this.order.by ? 'ASC' : 'DESC',
                    order_key: this.order.key,
                }
            }).then(({ data }) => {
                data.forEach(row => {
                    this.setTimer(row);
                });
                return data;
            });
        },
        setTimer(row) {
            const date = new Date(row.created_at);
            let seconds =  Math.floor((this.time - date.getTime()) / 1000);
            let minutes = 0;
            let hours = 0;

            if (seconds > 60) {
                minutes = Math.floor(seconds / 60);
                seconds -= minutes * 60;
            }

            if (minutes > 60) {
                hours = Math.floor(minutes / 60);
                minutes -= hours * 60;
            }

            row.timer = hours < 10 ? '0' + hours : hours;
            row.timer += ':' + (minutes < 10 ? '0' + minutes : minutes);
            row.timer += ':' + (seconds < 10 ? '0' + seconds : seconds);
        },
        notAdmittedField(row, field) {
            const reasons = row.not_admitted_reasons ?? [];

            if (reasons.includes(field)) {
                return {
                    backgroundColor: 'pink'
                }
            }

            return {}
        },
        approveAdmitting(row) {
            if (row.not_admitted_reasons.length === 0) {
                this.admit(row.id)

                return
            }

            Swal.fire({
                title: 'Отклонение от параметров!',
                text: 'Обратите внимание, у водителя имеются отклонения от установленных предельных параметров. Подтвердите действие.',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Допустить водителя',
                cancelButtonText: "Отмена",
            }).then((result) => {
                if (result.isConfirmed) {
                    this.admit(row.id)
                }
            })
        },
        admit(id) {
            const url = `/anketa/change-pak-queue/${id}/Допущен`

            axios.get(url)
                .then((response) => {
                    this.rows = this.rows.filter((row) => row.id !== id)
                })
                .catch(error => {
                    console.error(error)
                    Swal.fire(
                        'Ошибка принятия осмотра!',
                        error.response.data.message,
                        'error'
                    )
                })
        }
    },
}
</script>

<template>
    <table id="ankets-table" class="ankets-table table table-striped table-sm">
        <thead>
        <tr>
            <th class="not-export">
                Таймер
            </th>

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
            <td>
                <div class="text-danger">
                    {{ row.timer }}
                </div>
            </td>
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
                <a :href="`/anketa/${row.id}`"
                   class="btn btn-info btn-sm mr-1">
                    <i class="fa fa-search"></i>
                </a>
                <a :href="`#`"
                   class="btn btn-sm btn-success mr-1"
                   @click.prevent="approveAdmitting(row)">
                    <i class="fa fa-check"></i>
                </a>
                <a :href="`/anketa/change-pak-queue/${row.id}/Не идентифицирован`"
                   class="btn btn-sm btn-secondary mr-1">
                    <i class="fa fa-question"></i>
                </a>
                <a :href="`/anketa/change-pak-queue/${row.id}/Не допущен`"
                   class="btn btn-sm btn-danger">
                    <i class="fa fa-close"></i>
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
