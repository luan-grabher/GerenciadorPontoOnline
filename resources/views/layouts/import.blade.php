@extends('layouts.onlyhead')

@section('content')
    @include('loading')

    <div class="col-10 mx-auto my-5 text-center">
        @include('layouts.errors')
        @include('layouts.messages')

        {!! Form::open(['class'=>'form-loadable']) !!}
        <h3>Importar @yield('importName')</h3>
        <div class="alert alert-info">Depois que clicar em 'Importar' a página poderá demorar um pouco para carregar. NÃO FECHE A ABA OU O NAVEGADOR NESTE MOMENTO.</div>
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
