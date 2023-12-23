<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/"
                  xmlns:emk="http://schemas.datacontract.org/2004/07/EMKService.Data.Dto">
    <soapenv:Header/>
    <soapenv:Body>
        <tem:AddPatient>
            <guid>{{ config('external-system.guid') }}</guid>
            <idLPU>{{ config('external-system.idLPU') }}</idLPU>
            <tem:patient>
                @include('external-system::components.patient')
            </tem:patient>
        </tem:AddPatient>
    </soapenv:Body>
</soapenv:Envelope>
