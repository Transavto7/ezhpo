<d4p1:Steps xmlns:d5p1="http://schemas.datacontract.org/2004/07/N3.EMK.Dto.Step">
    @foreach($case->getSteps() as $step)
        @php
            $doctorDto = $step->getDoctor();
            $personDto = $doctorDto->getPersonDto();
            $humanNameDto = $personDto->getHumanNameDto();
        @endphp

        <d5p1:StepAmb>
            <d5p1:DateStart>{{ $step->getDateStart()->format(config('external-system.date_time_format')) }}</d5p1:DateStart>
            <d5p1:DateEnd>{{ $step->getDateEnd()->format(config('external-system.date_time_format')) }}</d5p1:DateEnd>
            <d5p1:Comment i:nil="true" />
            <d5p1:Doctor xmlns:d7p1="http://schemas.datacontract.org/2004/07/N3.EMK.Dto">
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
            </d5p1:Doctor>
            <d5p1:IdStepMis>{{ $step->getIdStepMis() }}</d5p1:IdStepMis>
            <d5p1:IdVisitPlace>{{ $step->getIdVisitPlace() }}</d5p1:IdVisitPlace>
            <d5p1:IdVisitPurpose>{{ $step->getIdVisitPurpose() }}</d5p1:IdVisitPurpose>
        </d5p1:StepAmb>
    @endforeach

</d4p1:Steps>
