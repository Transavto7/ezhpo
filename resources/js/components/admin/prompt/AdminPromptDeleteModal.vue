<template>
    <b-modal v-model="show" hide-header hide-footer>
        <div class="d-block">
            <span class="fs-5 d-block fw-bold">Вы уверены, что хотите удалить подсказку?</span>
            <div class="mt-3 d-flex justify-content-end">
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
        }
    },
    methods: {
        open(prompt) {
            this.show = true;
            this.prompt = prompt;
        },
        deleteElement() {
            axios.delete('/field/prompt/' + this.prompt.id).then(({ data }) => {
                this.show = false;
                this.$toast('Подсказка удалена', {type: 'error'});
                this.$emit('success');
            }).catch(error => {
                console.log(error);
            });
        }
    }
}
</script>

<style scoped>

</style>
