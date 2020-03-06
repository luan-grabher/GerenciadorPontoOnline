<div class="list-group h-100 bg-secondary">
    <a class="list-group-item list-group-item-action bg-info text-light disabled rounded-0 border-bottom" href="{{ url('/') }}">
        {{ strtoupper(config('app.name', 'Laravel')) }}
    </a>
    <a href="" data-toggle="collapse" data-target="#import-options"
       class="list-group-item list-group-item-action bg-secondary text-light rounded-0 border-bottom">
        {{ucfirst(__('importation'))}}
    </a>
    <div id="import-options"
         class="collapse">
        <a href="javascript:$('iframe').attr('src','{{route('import.pagarme.recebimentos')}}')"
           class="list-group-item list-group-item-action rounded-0 border-bottom pl-5">
            Pagarme Recebimetnos
        </a>
        <a href="javascript:alert(1)"
           class="list-group-item list-group-item-action rounded-0 border-bottom  pl-5">
            ERP
        </a>
    </div>
</div>
