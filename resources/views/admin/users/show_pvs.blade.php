<label>Пункт выпуска</label>
<select name="pv_id" required class="form-control">
    <option value="0">--none--</option>
    @foreach($points as $point)

        @if(count($point['pvs']) > 0)
            <optgroup label="{{ $point['name'] }}">
                @foreach($point['pvs'] as $child)
                    <option
                        @isset($pv_id)
                        @if($child->id === $pv_id) selected @endif
                        @endisset
                        value="{{ $child->id }}">— {{ $child->name }}</option>
                @endforeach
            </optgroup>
        @endif

    @endforeach
</select>
