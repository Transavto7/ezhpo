@php
    $options = \App\User::with('roles')
        ->whereHas('roles', function ($q) {
            $q->whereNotIn('roles.id', [3, 6, 9]);
        });
    $selectedOptions = \App\User::with('roles')
        ->whereHas('roles', function ($q) {
            $q->whereNotIn('roles.id', [3, 6, 9]);
        });
@endphp

@foreach($selectedOptions->whereIn($key, $default_value)->get() ?? [] as $option)
    <option selected value="{{ $option[$key] }}">
        @if ($concatField)
            [{{ $option[$concatField] }}] {{ $option[$value] }}
        @else
            {{ $option[$value] }}
        @endif
    </option>
@endforeach

@foreach($options->whereNotIn($key, $default_value)->limit(100)->get() ?? [] as $option)
    <option value="{{ $option[$key] }}">
        @if ($concatField)
            [{{ $option[$concatField] }}] {{ $option[$value] }}
        @else
            {{ $option[$value] }}
        @endif
    </option>
@endforeach
