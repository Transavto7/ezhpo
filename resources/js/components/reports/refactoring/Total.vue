<template>
<div class="" v-if="data">

    <b-row class="mt-5">
        <b-col class="text-right">
            <p>
                Итого по договору: {{ total }}₽
            </p>
        </b-col>
    </b-row>
</div>

</template>

<script>
export default {
    name: "Total",
    props: ['data'],
    data(){
        return {
        }
    },

    computed:{
        total(){

            let aaa = 0;
            for (let contract_key in this.data) {
                if( contract_key === 'message'){
                    continue;
                }
                if(contract_key === 'other'){
                    for(let ser in this.data[contract_key].company){
                        aaa += parseInt(this.data[contract_key].company[ser]);
                    }
                    continue;
                }

                aaa += parseInt(this.data[contract_key].total.price)
            }

            return aaa;
        }
    },
    methods:{
    }
}
</script>

<style scoped>

</style>
