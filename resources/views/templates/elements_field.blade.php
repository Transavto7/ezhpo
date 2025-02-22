@php
    $default_value = $default_value ?? '';
    /** @var \App\User $user */
    $user = \Illuminate\Support\Facades\Auth::guard("web")->user() ??
        \Illuminate\Support\Facades\Auth::guard("api")->user();

    if (empty($user)) {
        $request = request();

        \Illuminate\Support\Facades\Log::channel('deprecated-api')->info(json_encode(
                [
                    'request' => $request->all(),
                    'headers' => $request->headers->all(),
                    'url' => $request->url(),
                    'full-url' => $request->fullUrl(),
                    'ip' => $request->getClientIp() ?? null,
                ]
            ));
    }
@endphp

@if($v['type'] !== 'select')
    @include('templates.components.linear-elements-field')
@elseif ($v['type'] === 'select')
    <!-- todo: пользовать-водитель попасть сюда не может, этого условия и блока тут вроде не должно быть -->
    @if($user && $user->isDriver() && $k === 'company_name')
        @include('templates.components.driver-company-select')
    @elseif ($user && $user->isCompany() && ($k === 'company_id' || $k === 'company_name'))
        @include('templates.components.client-company-select')
    @else
        @include('templates.components.base-select')
    @endif
@endif
