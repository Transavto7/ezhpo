@php
    $default_value = is_array($default_value)
        ? $default_value
        : explode(',', $default_value);
    $key = $v['getFieldKey'] ?? 'id';
    $value = $v['getField'] ?? 'name';
    $company = $user->company
@endphp
<select
    disabled
    name="company_name"
    class="filled-select2 filled-select">
    <option selected value="{{ $company->name }}">
        {{ $company->name }}
    </option>
</select>
