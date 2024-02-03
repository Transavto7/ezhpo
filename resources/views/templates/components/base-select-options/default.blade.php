@php
    $model = app("App\\" . $v['values']);
    $options = $model::query();
    $user = \Illuminate\Support\Facades\Auth::user();
    if ($user->hasRole("client") && in_array($v['values'], ['Driver', 'Car'])) {
        $options = $options->where('company_id', $user->company_id);
    }
    $options = $options->get()
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
