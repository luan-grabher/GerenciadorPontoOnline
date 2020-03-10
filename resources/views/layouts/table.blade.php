<div class="table-responsive">
    <table id="table-result" class="table table-dark table-hover">
        @if(isset($data) && sizeof($data) > 0)
            <thead>
                <tr>
                    @foreach(array_keys($data[0]) as $collumn)
                        <td class="font-size-10">{{strtoupper($collumn)}}</td>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($data as $line)
                    <tr>
                        @foreach($line as $collumn)
                            <td class="font-size-10">{{$collumn}}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        @endif
    </table>
</div>

