@php
    $isSelected = function($child): bool {
        if (isset($default_point)) {
            if ($default_point === $child['id'] || $default_point === $child['name']) {
                return true;
            }
        } else {
            if (session()->has('anketa_pv_id') && $child['id'] == session('anketa_pv_id')['value']) {
                return true;
            }
        }

        if (isset($default_pv_id) && ($default_pv_id === $child['name'] || $default_pv_id === $child['id'])) {
            return true;
        }

        return false;
    };
@endphp

<div class="form-group mb-4">
    <label class="form-control-label">Пункт выпуска:</label> &nbsp;
    <select name="pv_id" class="col-sm-6 form-control" required>
        @foreach($points as $point)
            @if(count($point['pvs']) > 0)
                <optgroup label="{{ $point['name'] }}">
                    @foreach($point['pvs'] ?? [] as $child)
                        <option
                            @if($isSelected($child))
                                selected
                            @endif
                            value="{{ $child['id'] }}">
                            — {{ $child['name'] }}
                        </option>
                    @endforeach
                </optgroup>
            @endif
        @endforeach
    </select>
    <small>Вы находитесь здесь?</small>
</div>
