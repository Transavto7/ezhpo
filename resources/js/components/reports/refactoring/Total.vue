<template>
<div class="" v-if="data">

    <b-row class="mt-5">
        <b-col class="text-right">
            <p>
                Итого по договору: {{ data }}₽
            </p>
        </b-col>
    </b-row>
</div>

</template>

<script>
export default {
    name: "Total",
    data(){
        return {
            data: 0,
            repor: null
        }
    },

    methods:{
        open(data){
            this.repor = data
            for (let contract_key in data){
                if( contract_key === 'message'){
                    continue;
                }
                if(contract_key === 'other'){
                    for(let ser in data[contract_key].company){
                        this.data += data[contract_key].company[ser]
                    }
                }
                this.data += data[contract_key].services.price

            }
        }
    }
}
</script>

<style scoped>

</style>
