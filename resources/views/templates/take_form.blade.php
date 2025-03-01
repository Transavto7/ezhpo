<div>Количество элементов на странице:
    <form action="?orderBy={{ $orderBy }}&orderKey={{ $orderKey . $queryString ?? '' }}" method="GET" id="FORM_COUNT_ELEMENTS">
        @include('templates.GET_INPUTS')

        @php
            $takeVariants = [500, 1500, 2000, 2500];
            $take = $take ?? $takeVariants[0];
            if (!in_array($take, $takeVariants)) {
                $takeVariants[] = $take;
                sort($takeVariants);
            }
        @endphp

        <select onchange="FORM_COUNT_ELEMENTS.submit()" name="take">
            @foreach($takeVariants as $numb)
                <option
                    @if($take == $numb)
                        selected
                    @endif
                    value="{{ $numb }}">{{ $numb }}</option>
            @endforeach
        </select>
    </form>
</div>
