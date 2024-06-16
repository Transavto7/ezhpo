@php
    $default_pv_id = $default_pv_id ?? null;
    $pv_id = $pv_id ?? null;
    $isSelected = function($child) use ($pv_id, $default_pv_id): bool {
        switch (true) {
            /** Установлено в осмотре */
            case !is_null($pv_id):
                return $pv_id === $child['id'] || $pv_id === $child['name'];
            /** Установлено в сессии */
            case session()->has('anketa_pv_id'):
                return $child['id'] === session('anketa_pv_id')['value'];
            /** Установлено у пользователя */
            case !is_null($default_pv_id):
                return $default_pv_id === $child['id'] || $default_pv_id === $child['name'];
            default:
                return false;
        }
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
