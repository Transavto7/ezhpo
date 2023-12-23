<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
    <s:Body>
        <AddCase xmlns="http://tempuri.org/">
            <guid>{{ config('external-system.guid') }}</guid>
            <caseDto xmlns:d4p1="http://schemas.datacontract.org/2004/07/N3.EMK.Dto.Case" xmlns:i="http://www.w3.org/2001/XMLSchema-instance" i:type="d4p1:CaseAmb">
                <d4p1:OpenDate>{{ $case->getOpenDate()->format(config('external-system.date_time_format')) }}</d4p1:OpenDate>
                <d4p1:CloseDate>{{ $case->getCloseDate()->format(config('external-system.date_time_format')) }}</d4p1:CloseDate>
                <d4p1:HistoryNumber>{{ $case->getHistoryNumber() }}</d4p1:HistoryNumber>
                <d4p1:IdCaseMis>{{ $case->getIdCaseMis() }}</d4p1:IdCaseMis>
                <d4p1:IdCaseAidType>{{ $case->getIdCaseAidType() }}</d4p1:IdCaseAidType>
                <d4p1:IdPaymentType>{{ $case->getIdPaymentType() }}</d4p1:IdPaymentType>
                <d4p1:Confidentiality>{{ $case->getConfidentiality() }}</d4p1:Confidentiality>
                <d4p1:DoctorConfidentiality>{{ $case->getDoctorConfidentiality() }}</d4p1:DoctorConfidentiality>
                <d4p1:CuratorConfidentiality>{{ $case->getCuratorConfidentiality() }}</d4p1:CuratorConfidentiality>
                <d4p1:IdLpu>{{ $case->getIdLpu() }}</d4p1:IdLpu>
                <d4p1:IdCaseResult>{{ $case->getIdCaseResult() }}</d4p1:IdCaseResult>
                <d4p1:Comment>{{ $case->getComment() }}</d4p1:Comment>

                @include('external-system::components.doctor_in_charge')
                @include('external-system::components.authenticator')
                @include('external-system::components.author')

                <d4p1:IdPatientMis>{{ $case->getIdPatientMis() }}</d4p1:IdPatientMis>

                <d4p1:CaseVisitType>{{ $case->getCaseVisitType() }}</d4p1:CaseVisitType>
                @if($case->getIdCasePurpose())
                    <d4p1:IdCasePurpose>{{ $case->getIdCasePurpose() }}</d4p1:IdCasePurpose>
                @endif
                <d4p1:IdCaseType>{{ $case->getIdCaseType() }}</d4p1:IdCaseType>
                @if($case->getIdAmbResult())
                    <d4p1:IdAmbResult>{{ $case->getIdAmbResult() }}</d4p1:IdAmbResult>
                @endif
                @if($case->getIsActive())
                    <d4p1:IsActive>{{ $case->getIsActive() }}</d4p1:IsActive>
                @endif

                @include('external-system::components.steps')
                @include('external-system::components.med_records')
            </caseDto>
        </AddCase>
    </s:Body>
</s:Envelope>
