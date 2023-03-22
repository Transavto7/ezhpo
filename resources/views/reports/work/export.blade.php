<html>

@foreach($data as $k => $pointReport)
    <table>
        <thead>
        <tr>
            @foreach($pointReport['pointRow'] as $col)
                <th>{{$col}}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($pointReport['reports'] as $report)
            @foreach($report as $n => $reportRow)
                @if ($n === 0)
                    <tr>
                        @foreach($reportRow as $k => $reportCell)
                            <td>{{$reportCell}}</td>
                        @endforeach
                    </tr>
                @else
                    <tr>
                        @foreach($reportRow as $reportCell)
                            <td>{{$reportCell}}</td>
                        @endforeach
                    </tr>
                @endif
            @endforeach
        @endforeach

        </tbody>
    </table>
@endforeach

</html>