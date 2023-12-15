<table>
    <thead>
    <tr>
        @foreach($fields as $key => $field)
            <th
                height="60" width="15" valign="center" align="center" style="word-wrap: break-word"
            ><b>{{ $field }}</b></th>
        @endforeach
    </tr>
    </thead>

    <tbody>
    @foreach($data as $item)
        <tr>
            @foreach($fields as $key => $field)
                @if ($item['type_anketa'] === 'bdd' && $key === 'date')
                    <td>{{ date('d-m-Y', strtotime($item[$key])) }}</td>
                @elseif($key === 'date' || $key === 'created_at' || $key === 'updated_at')
                    <td>{{$item[$key] ? \Carbon\Carbon::parse($item[$key])->format("d.m.Y H:i:s") : '' }}</td>
                @else
                    <td>{{ $item[$key] }}</td>
                @endif
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
