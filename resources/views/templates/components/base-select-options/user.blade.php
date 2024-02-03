@php
    $options = \App\User::with('roles')
        ->whereHas('roles', function ($q) {
            $q->whereNotIn('roles.id', [3, 6, 9]);
        })
        ->get();
@endphp

@foreach($options as $option)
    <option @if(in_array($key, $default_value)) selected @endif value="{{ $option[$key] }}">
        @if ($concatField)
            [{{ $option[$concatField] }}] {{ $option[$value] }}
        @else
            {{ $option[$value] }}
        @endif
    </option>
@endforeach
