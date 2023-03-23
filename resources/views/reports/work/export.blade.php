<html>
<style>
    table, th, td {
        border: 1px solid;
        border-collapse: collapse;
    }
</style>
@foreach($data as $key => $datumTable)
<table style="border: solid 1px;" class="table table-bordered">
    <thead>
        @foreach($datumTable['pointData'] as $pointCell)
            <th>{{$pointCell}}</th>
        @endforeach
    </thead>
    <tbody>
        @foreach($datumTable['reports'] as $reportRow)
                <tr>
                    @foreach($reportRow as $cell)
                        <td>
                            {{$cell}}
                        </td>
                    @endforeach
                </tr>
        @endforeach
    </tbody>
</table>
@endforeach

</html>