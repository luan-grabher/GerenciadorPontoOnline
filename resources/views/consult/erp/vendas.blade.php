@extends('layouts.onlyhead')
@section('content')
    @include('loading')
    <div class="col-11 mx-auto mt-3">
        @include('layouts.errors')
        @include('layouts.messages')

        <div class="text-center mt-2 col-6 mx-auto">
            <h3>ERP Consultar Vendas</h3>
            <hr class="bg-danger">
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
        <hr class="col-8 bg-danger mx-auto">
        {{var_dump($sales)}}
    </div>

@endsection
