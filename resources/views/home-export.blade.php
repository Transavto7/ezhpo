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
                <td>{{ $item[$key] }}</td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
