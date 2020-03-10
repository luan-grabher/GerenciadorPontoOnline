require('./bootstrap');
require( 'jszip' );
require( 'pdfmake' );
require( 'datatables.net-bs4' )();
require( 'datatables.net-buttons-bs4' )();
require( 'datatables.net-buttons/js/buttons.colVis.js' )();
require( 'datatables.net-buttons/js/buttons.flash.js' )();
require( 'datatables.net-buttons/js/buttons.html5.js' )();
require( 'datatables.net-colreorder-bs4' )();
require( 'datatables.net-fixedheader-bs4' )();
require( 'datatables.net-responsive-bs4' )();

$(document).ready(function() {
    $('.form').DataTable();
} );


