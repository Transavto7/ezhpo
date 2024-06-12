<template>
    <b-modal v-model="show" hide-header hide-footer>
        <div class="d-block">
            <span class="fs-5 d-block fw-bold text-center">Вы уверены, что хотите удалить штап?</span>
            <div class="mt-3 d-flex justify-content-center">
                <b-button variant="danger" @click="deleteElement">
                    Удалить
                </b-button>
                <b-button class="ml-2" variant="info" @click="show = false">Отмена</b-button>
            </div>
        </div>
    </b-modal>
</template>

<script>
export default {
    data() {
        return {
            show: false,
            stamp: {}
        }
    },
    methods: {
        open(stamp) {
            this.show = true;
            this.stamp = stamp;
        },
        deleteElement() {
            axios.delete('/stamp/' + this.stamp.id).then(({ data }) => {
                this.show = false;
                this.$toast('Штамп удален', {type: 'error'});
                this.$emit('success');
            }).catch(error => {
                console.error(error)
                this.$toast('Ошибка!', {type: 'error'});
            });
        }
    }
}
</script>

<style scoped>

</style>
