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
            // data: 0
        }
    },

    computed:{
        total(){

            let aaa = 0;
            for (let contract_key in this.data){
                if( contract_key === 'message'){
                    continue;
                }
                if(contract_key === 'other'){
                    for(let ser in this.data[contract_key].company){
                        aaa += this.data[contract_key].company[ser]
                    }
                    continue;
                }
                aaa += this.data[contract_key].services.price

            }

            return aaa;
        }
    },
    methods:{
        // open(data){
        //     this.repor = data
        //     for (let contract_key in data){
        //         if( contract_key === 'message'){
        //             continue;
        //         }
        //         if(contract_key === 'other'){
        //             for(let ser in data[contract_key].company){
        //                 this.data += data[contract_key].company[ser]
        //             }
        //             continue;
        //         }
        //         this.data += data[contract_key].services.price
        //
        //     }
        // }
    }
}
</script>

<style scoped>

</style>
