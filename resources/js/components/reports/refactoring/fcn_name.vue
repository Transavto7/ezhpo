<template>
<div class="">
    {{name}}
</div>
</template>

<script>
export default {
    name: "fcn_name",
    data(){
        return {
            name: ''
        }
    },
    methods: {
        open(data){
            this.name = data
        }
    }
}
</script>

<style scoped>

</style>
