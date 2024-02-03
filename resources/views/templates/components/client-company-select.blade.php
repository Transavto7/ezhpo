@php
    $default_value = is_array($default_value)
        ? $default_value
        : explode(',', $default_value);
    $key = $v['getFieldKey'] ?? 'id';
    $value = $v['getField'] ?? 'name';
    $company = \Illuminate\Support\Facades\Auth::user()->company
@endphp
<select
    disabled
    name="company_id"
    class="filled-select2 filled-select">
    <option selected value="{{ $company->hash_id }}">
        [{{ $company->hash_id }}] {{ $company->name }}
    </option>
</select>
