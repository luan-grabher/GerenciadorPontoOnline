@extends('layouts.onlyhead')
@section('content')
    @include('loading')
    <div class="col-12 mx-auto mt-3">
        @include('layouts.errors')
        @include('layouts.messages')

        <div class="text-center mt-2 col-6 mx-auto">
            <h3>{{$title??"Consultar"}}</h3>
            <hr class="">
        </div>
        {!! Form::open(['class'=>'form form-loadable form-inline col-10 mx-auto justify-content-center']) !!}
        <div class="form-group m-1 align-content-center">
            <label><b>Inicio: </b></label>
            {!! Form::date('inicio',now(),['class'=>'form-control']) !!}
        </div>
        <div class="form-group m-1 align-content-center">
            <label><b>Fim: </b></label>
            {!! Form::date('fim',now(),['class'=>'form-control']) !!}
        </div>
        <div>
            {!! Form::submit('Procurar',['class'=>'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}

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
                {{view('layouts.table',['data'=>$results])}}
            @endif
        </div>
    </div>

@endsection
