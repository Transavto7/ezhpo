@php
    $model = app("App\\" . $v['values']);
    $options = $model::query();
    $selectedOptions = $model::query();

    if (isset($v['orderBy'])) {
        $options->orderBy($v['orderBy'], $v['order'] ?? 'asc');
    }

    if ($user && $user->hasRole("client") && in_array($v['values'], ['Driver', 'Car'])) {
        $options->where('company_id', $user->company_id);
        $selectedOptions->where('company_id', $user->company_id);
    }
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
