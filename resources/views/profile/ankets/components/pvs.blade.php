@php
//dd($points);
@endphp
@if(!isset($defaultShowPvs))
    <div class="form-group mb-4">
H        <label class="form-control-label">Пункт выпуска:</label> &nbsp;
        <select name="pv_id" class="col-sm-6 form-control" required>
            @foreach($points as $point)

                @if(count($point['pvs']) > 0)
                    <optgroup label="{{ $point['name'] }}">
                        @foreach($point['pvs'] as $child)
                            @php
                                if(is_array($child)) {
                                $child = (object)$child;
                                dd($child);
                            }
                            @endphp
                            <option
                                @if (isset($default_point) && $default_point === $child->name)
                                    selected
                                @elseif(session()->has('anketa_pv_id'))
                                    @if(!isset($default_point) && $child->id == session('anketa_pv_id')['value'])
                                        selected
                                    @endif
                                @elseif($default_pv_id)
                                    {{ $default_pv_id === $child->name || $default_pv_id === $child->id ? 'selected' : '' }}
                                @endif
                                value="{{ $child->id }}">
                                — {{ $child->name }}</option>
                        @endforeach
                    </optgroup>
                @else
                    {{-- <option {{ $default_pv_id === $point['id'] ? 'selected' : '' }} value="{{ $point['id'] }}">{{ $point['name'] }}</option> --}}
                @endif

            @endforeach
        </select>
        <small>Вы находитесь здесь?</small>
    </div>
@else
    <select name="pv_id" class="{{ $classesPvs }}">
        <option selected value="">Все пункты выпуска</option>

        @foreach($points as $point)

            @if(count($point['pvs']) > 0)
                <optgroup label="{{ $point['name'] }}">
                    @foreach($point['pvs'] as $child)
                        @php
                            if(is_array($child)){
                            $child = (object)$child;
                        }
                        @endphp
                        <option
                            {{ request()->get('pv_id') == $child->id ? 'selected' : '' }}

                            value="{{ $child->id }}">

                            — {{ $child->name }}</option>
                    @endforeach
                </optgroup>
            @endif

        @endforeach
    </select>
@endif
