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
        <a href="javascript:$('#iframe-main').attr('src','{{route('import.pagarme.recebimentos')}}')"
           class="list-group-item list-group-item-action rounded-0 border-bottom pl-5">
            Pagarme Recebimetnos
        </a>
        <a href="javascript:$('#iframe-main').attr('src','{{route('import.erp.vendas')}}')"
           class="list-group-item list-group-item-action rounded-0 border-bottom  pl-5">
            ERP Vendas
        </a>
    </div>
    <a href="" data-toggle="collapse" data-target="#consult-options"
       class="list-group-item list-group-item-action bg-secondary text-light rounded-0 border-bottom">
        {{ucfirst(__('Consultar'))}}
    </a>
    <div id="consult-options"
         class="collapse">
        <a href="javascript:$('#iframe-main').attr('src','{{route('consult.pagarme.recebimentos')}}')"
           class="list-group-item list-group-item-action rounded-0 border-bottom pl-5">
            Pagarme Recebimetnos
        </a>
        <a href="javascript:$('#iframe-main').attr('src','{{route('consult.erp.vendas')}}')"
           class="list-group-item list-group-item-action rounded-0 border-bottom  pl-5">
            ERP Vendas
        </a>
    </div>
</div>
