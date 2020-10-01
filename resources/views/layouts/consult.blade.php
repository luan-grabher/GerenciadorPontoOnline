@extends('layouts.onlyhead')
@section('content')
    @include('layouts.forms.searchInRange')
    <div class="col-12 mx-auto" id="results">
        <hr class="">
        @if(isset($filters))
            <div id="filters" class="col-10 mx-auto text-center">
                <h5 class="font-weight-bold">Filtros Utilizados:</h5>
                @foreach(array_keys($filters) as $filterName)
                    <div class="badge badge-lg badge-info">{{$filterName}} : {{$filters[$filterName]}}</div>
                @endforeach
            </div>
        @endif
        @if(isset($results))
            {{view('layouts.table',['data'=>$results,'columnsToSum'=>$columnsToSum])}}
        @endif
    </div>
@endsection
