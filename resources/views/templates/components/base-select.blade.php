@php $default_value = isset($default_value) ? $default_value : ''; @endphp
@php $uniqueInputId = sha1(time() + rand(999, 99999)); @endphp

@if($v['type'] !== 'select')
    @include('templates.components.linear-elements-field')
@elseif (user()->hasRole('driver') && $k === 'company_name')
    @include('templates.components.driver-company-select')
@elseif (user()->hasRole('client') && ($k === 'company_id' || $k === 'company_name'))
    @include('templates.components.client-company-select')
@else
    @include('templates.components.base-select')
@endif
