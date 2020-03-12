<div class="list-group h-100 bg-secondary">
    <a class="list-group-item list-group-item-action bg-info text-light disabled rounded-0 border-bottom"
       href="{{ url('/') }}">
        {{ strtoupper(config('app.name', 'Laravel')) }}
    </a>

    <a id="navbarDropdown" class="list-group-item list-group-item-action bg-dark text-light rounded-0 border-bottom nav-link dropdown-toggle" href="#" role="button"
       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
        {{ Auth::user()->name }} <span class="caret"></span>
    </a>

    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href="{{ route('logout') }}"
           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
            {{ __('Logout') }}
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST"
              style="display: none;">
            @csrf
        </form>
    </div>

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
        <a href="javascript:$('#iframe-main').attr('src','{{route('import.pagarme.vendas')}}')"
           class="list-group-item list-group-item-action rounded-0 border-bottom pl-5">
            Pagarme Vendas
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
        <a href="javascript:$('#iframe-main').attr('src','{{route('consult.pagarme.vendas')}}')"
           class="list-group-item list-group-item-action rounded-0 border-bottom  pl-5">
            Pagarme Vendas
        </a>
        <a href="javascript:$('#iframe-main').attr('src','{{route('consult.erp.vendas')}}')"
           class="list-group-item list-group-item-action rounded-0 border-bottom  pl-5">
            ERP Vendas
        </a>
    </div>

    <a href="" data-toggle="collapse" data-target="#analysis-options"
       class="list-group-item list-group-item-action bg-secondary text-light rounded-0 border-bottom">
        {{ucfirst(__('Análises'))}}
    </a>
    <div id="analysis-options"
         class="collapse">
        <a href="javascript:$('#iframe-main').attr('src','{{route('analysis.balances.tid')}}')"
           class="list-group-item list-group-item-action rounded-0 border-bottom pl-5">
            TID - Saldos
        </a>
    </div>

</div>
