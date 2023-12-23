<d4p1:MedRecords xmlns:d5p1="http://schemas.datacontract.org/2004/07/N3.EMK.Dto.MedRec">
    @foreach($case->getMedRecords() as $medRecord)
        @php
            $diagnosisInfo = $medRecord->getDiagnosisInfoDto();

            $doctorDto = $medRecord->getDoctorDto();
            $personDto = $doctorDto->getPersonDto();
            $humanNameDto = $personDto->getHumanNameDto();
        @endphp

    @endforeach
    <d5p1:MedRecord xmlns:d6p1="http://schemas.datacontract.org/2004/07/N3.EMK.Dto.MedRec.Diag" i:type="d6p1:ClinicMainDiagnosis">
        <d6p1:DiagnosisInfo>
            <d6p1:DiagnosedDate>{{ $diagnosisInfo->getDiagnosisDate()->format(config('external-system.date_time_format')) }}</d6p1:DiagnosedDate>
            <d6p1:IdDiagnosisType>{{ $diagnosisInfo->getIdDiagnosisType() }}</d6p1:IdDiagnosisType>
            <d6p1:Comment>{{ $diagnosisInfo->getComment() }}</d6p1:Comment>
            <d6p1:MkbCode>{{ $diagnosisInfo->getMkbCode() }}</d6p1:MkbCode>
        </d6p1:DiagnosisInfo>
        <d6p1:Doctor xmlns:d7p1="http://schemas.datacontract.org/2004/07/N3.EMK.Dto">
            <d7p1:Person>
                <d7p1:HumanName>
                    <d7p1:GivenName>{{ $humanNameDto->getGivenName() }}</d7p1:GivenName>
                    @if($humanNameDto->getMiddleName())
                        <d7p1:MiddleName>{{ $humanNameDto->getMiddleName() }}</d7p1:MiddleName>
                    @endif
                    <d7p1:FamilyName>{{ $humanNameDto->getFamilyName() }}</d7p1:FamilyName>
                </d7p1:HumanName>
                <d7p1:IdPersonMis>{{ $personDto->getIsPersonMis() }}</d7p1:IdPersonMis>
            </d7p1:Person>
            <d7p1:IdLpu i:nil="true" />
            <d7p1:IdSpeciality>{{ $doctorDto->getIdSpeciality() }}</d7p1:IdSpeciality>
            <d7p1:IdPosition>{{ $doctorDto->getIdPosition() }}</d7p1:IdPosition>
        </d6p1:Doctor>
    </d5p1:MedRecord>
</d4p1:MedRecords>
