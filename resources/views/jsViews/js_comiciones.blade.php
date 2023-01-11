<script type="text/javascript">

    // INICIALIZA Y ASIGNA LA FECHA EN EL DATEPICKER
    const startOfMonth  = moment().subtract(1,'days').format('YYYY-MM-DD');
    const endOfMonth    = moment().subtract(0, "days").format("YYYY-MM-DD");
    var labelRange      = startOfMonth + " to " + endOfMonth;      
    $('#id_range_select').val(labelRange);
    
    
    // INICIALIZA LA DATATABLE CON LOS VALORES POR DEFECTO 
    $("#table_comisiones").DataTable({
        "destroy": true,
        "info": false,
        "bPaginate": true,
        "lengthMenu": [
            [7 -1],
            [7, "Todo"]
        ],
        "language": {
            "zeroRecords": "NO HAY COINCIDENCIAS",
            "paginate": {
                "first": "Primera",
                "last": "Última ",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "lengthMenu": "MOSTRAR _MENU_",
            "emptyTable": "-",
            "search": "BUSCAR"
        },
    });

    //OCULTA DE LA PANTALLA EL FILTRO DE PAGINADO Y FORM DE BUSQUEDA
    $("#table_comisiones_length").hide();
    $("#table_comisiones_filter").hide();
</script>
