@php
    $doctorDto = $case->getAuthenticator();
    $personDto = $doctorDto->getPersonDto();
    $humanNameDto = $personDto->getHumanNameDto();
@endphp

<d4p1:Authenticator xmlns:d5p1="http://schemas.datacontract.org/2004/07/N3.EMK.Dto">
    <d5p1:Doctor>
        <d5p1:Person>
            <d5p1:HumanName>
                <d5p1:GivenName>{{ $humanNameDto->getGivenName() }}</d5p1:GivenName>
                @if($humanNameDto->getMiddleName())
                    <d5p1:MiddleName>{{ $humanNameDto->getMiddleName() }}</d5p1:MiddleName>
                @endif
                <d5p1:FamilyName>{{ $humanNameDto->getFamilyName() }}</d5p1:FamilyName>
            </d5p1:HumanName>
            <d5p1:IdPersonMis>{{ $personDto->getIsPersonMis() }}</d5p1:IdPersonMis>
        </d5p1:Person>
        <d5p1:IdPosition>{{ $doctorDto->getIdPosition() }}</d5p1:IdPosition>
        <d5p1:IdSpeciality>{{ $doctorDto->getIdSpeciality() }}</d5p1:IdSpeciality>
    </d5p1:Doctor>
</d4p1:Authenticator>
