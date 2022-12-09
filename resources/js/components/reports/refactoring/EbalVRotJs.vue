<template>
<div class="">


<!--    <div v-for="(contract, index) in contracts">-->
        <div v-if="contract_info" class="my-3">
            <b-button class="mb-2" v-b-toggle="'collapse-' + contract_info.id"  variant="primary">
                {{ contract_info.name }}
            </b-button>
            <b-collapse :id="'collapse-' + contract.id" class="mt-2">
                <b-card>
                    <ReportJournalMedic
                        ref="reportsMedic"
                    />
                    <ReportJournalTech
                        class="mt-5"
                        ref="reportsTech"
                    />
                    <ReportJournalMedicOther
                        class="mt-5"
                        ref="reportsMedicOther"
                    />
                    <ReportJournalTechOther
                        class="mt-5"
                        ref="reportsTechOther"
                    />
                    <ReportJournalOther
                        class="mt-5"
                        ref="reportsOther"
                    />
                    <Total
                        ref="total"

                    >

                    </Total>
                </b-card>
            </b-collapse>
        </div>
<!--    </div>-->
</div>

</template>

<script>
import Swal2 from "sweetalert2";
import ReportJournalTechOther from './ReportJournalTechOther'
import ReportJournalOther from './ReportJournalOther'
import ReportJournalMedicOther from './ReportJournalMedicOther'
import ReportJournalMedic from './ReportJournalMedic'
import ReportJournalTech from './ReportJournalTech'
import Total from "./Total";


export default {
    name: "EbalVRotJs",
    components: {
        ReportJournalTechOther,
        ReportJournalOther,
        ReportJournalMedic,
        ReportJournalTech,
        Total,
        ReportJournalMedicOther
    },
    data(){
        return  {
            contract: {},
            contract_info: null
        }
    },
    methods:{
        open(data, contract){
            this.contract = data
            this.contract_info = contract

            console.log(
                123
            )
            console.log(this.$refs)
            console.log(this.$refs.reportsMedicOther)
            console.log(this.$refs.reportsMedic)
            // this.$refs.reportsMedic.hide();
            // this.$refs.reportsMedic.visible(data.medics);
            //
            // this.$refs.reportsTech.hide();
            // this.$refs.reportsTech.visible(data.techs);
            //
            // this.$refs.reportsTechOther.hide();
            // this.$refs.reportsTechOther.visible(data.techs_other);
            //
            // this.$refs.reportsMedicOther.hide();
            // this.$refs.reportsMedicOther.visible(data.medics_other);
            //
            // this.$refs.reportsOther.hide();
            // this.$refs.reportsOther.visible(data.other);

            // this.$refs.total.open(data);
        }
    }
}
</script>

<style scoped>

</style>
