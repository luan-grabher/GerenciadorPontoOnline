$('.form-loadable').on('submit', function (e) {
    //var token = $(this).find("[name='_token']").val();
    //var src = "/progresso/" + token;

    $("#progress").toggleClass('d-none');
    //$('#progress iframe').attr('src',src);
});

$('.table').dataTable({
    "language": {
        "lengthMenu": 'Mostrando  <select class="form-control">' +
            '<option value="5">5</option>' +
            '<option value="10">10</option>' +
            '<option value="20">20</option>' +
            '<option value="30">30</option>' +
            '<option value="40">40</option>' +
            '<option value="50">50</option>' +
            '<option value="-1">All</option>' +
            '</select> resultados por página.',
        "zeroRecords": "Nenhum resultado encontrado.",
        "info": "Mostrando página _PAGE_ de _PAGES_",
        "infoEmpty": "Nenhum resultado disponível",
        "infoFiltered": "(Filtrado de _MAX_ resultados)",
        "search": "Procurar:",
        "paginate": {
            "first": "Primeira",
            "last": "Última",
            "next": "Próxima",
            "previous": "Anterior"
        }
    },
    "pageLength": 5
});
