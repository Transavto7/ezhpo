@php
    $default_value = $default_value ?? '';
    /** @var \App\User $user */
    $user = \Illuminate\Support\Facades\Auth::user();
    $uniqueInputId = sha1(time() + rand(999, 99999));
@endphp

@if($v['type'] !== 'select')
    @include('templates.components.linear-elements-field')
@elseif ($v['type'] === 'select')
    @if($user->hasRole('driver') && $k === 'company_name')
        @include('templates.components.driver-company-select')
    @elseif ($user->hasRole('client') && ($k === 'company_id' || $k === 'company_name'))
        @include('templates.components.client-company-select')
    @else
        @include('templates.components.base-select')
    @endif
@endif
