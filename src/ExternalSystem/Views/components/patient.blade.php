@php
    $humanNameDto = $patient->getHumanNameDto();
@endphp

<emk:BirthDate>{{ $patient->getBirthDate()->format(config('external-system.date_birth_format')) }}</emk:BirthDate>
<emk:FamilyName>{{ $humanNameDto->getFamilyName() }}</emk:FamilyName>
<emk:GivenName>{{ $humanNameDto->getGivenName() }}</emk:GivenName>
<emk:IdPatientMIS>{{ $patient->getIdPatientMIS() }}</emk:IdPatientMIS>

@if($humanNameDto->getMiddleName())
    <emk:MiddleName>{{ $humanNameDto->getMiddleName() }}</emk:MiddleName>
@endif

<emk:Sex>{{ $patient->getSex() }}</emk:Sex>
