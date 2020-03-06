{!! Form::open() !!}
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
