<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/" xmlns:ser="http://schemas.microsoft.com/2003/10/Serialization/" xmlns:emk="http://schemas.datacontract.org/2004/07/EMKService.Data.Dto">
    <soapenv:Header/>
    <soapenv:Body>
        <tem:GetPatient>
            <guid>{{ config('external-system.guid') }}</guid>
            <idLPU>{{ config('external-system.idLPU') }}</idLPU>
            <tem:patient>
                <emk:IdPatientMIS>{{ $idPatientMis }}</emk:IdPatientMIS>
            </tem:patient>
        </tem:GetPatient>
    </soapenv:Body>
</soapenv:Envelope>
