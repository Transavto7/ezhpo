<template>
    <div>
        <span :id='id'>
            {{ shortUuid }}
        </span>
        <b-tooltip :target="id" placement="bottom">
            {{ uuid }}
        </b-tooltip>
    </div>
</template>

<script>

export default {
    name: "uuid-cell",
    props: [ 'uuid' ],
    data() {
        return {

        }
    },
    computed: {
        id: function () {
            return (Math.random() + 1).toString(36).substring(7);
        },
        shortUuid: function () {
            return this.uuid ? this.uuid.substring(0, 7) : null
        },
    }
}
</script>

<style scoped>

</style>
