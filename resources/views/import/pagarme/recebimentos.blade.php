@extends('layouts.onlyhead')

@section('content')
    <div class="col-10 mx-auto my-5 text-center">
        <div class="alert-of-form">
        </div>

        {!! Form::open() !!}
        <h3>Pagarme {{__('Recebimentos')}}</h3>
        <div class="form-group">
            {!! Form::label('Inicio:') !!}
            {!! Form::date('inicio', \Carbon\Carbon::now(),['class'=>'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('Fim:') !!}
            {!! Form::date('fim', \Carbon\Carbon::now(),['class'=>'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::submit('Importar',['class'=>'btn btn-success']) !!}
        </div>
        {!! Form::close() !!}
    </div>

@endsection
