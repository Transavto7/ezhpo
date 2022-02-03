<p>Количество элементов на странице:

<form action="?orderBy={{ $orderBy }}&orderKey={{ $orderKey . (isset($queryString) ? $queryString : '') }}" method="GET" id="FORM_COUNT_ELEMENTS">
    @include('templates.GET_INPUTS')

    <select onchange="FORM_COUNT_ELEMENTS.submit()" name="take">
        @foreach([20, 50, 100, 250, 500] as $numb)
            <option
                @isset($take)
                @if($take == $numb)
                selected
                @endif
                @endisset
                value="{{ $numb }}">{{ $numb }}</option>
        @endforeach
    </select>
</form>
</p>
