<div class="table-responsive">
    <table id="table-result" class="table table-dark table-hover" style="font-size: 70%">
        @if(isset($data) && sizeof($data) > 0)
            <thead>
            <tr>
                @foreach(array_keys($data[0]) as $column)
                    <th class="font-size-10">{{strtoupper($column)}}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($data as $key=>$line)
                <tr>
                    @foreach($line as $column)
                        @if(isset($columnsToSum[$key]))
                            @php($columnsToSum[$key] = "$key")
                        @endif
                        <td class="font-size-10">{{$column}}</td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                @foreach($data[0] as $key=>$column)
                    <td class="font-size-10">
                        @isset($columnsToSum[$key])
                            Total: {{$columnsToSum[$key]}}
                        @endif
                    </td>
                @endforeach
            </tr>
            </tfoot>
        @endif
    </table>
</div>

