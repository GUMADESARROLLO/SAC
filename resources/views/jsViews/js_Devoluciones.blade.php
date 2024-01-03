<script type="text/javascript">
    $("#id_loading").hide();
    tbl_header_inventarios_Fav =  [
        {"title": "FACTURAS","data": "FACTURAS"},
        {"title": "FECHA","data": "FCT_DATE"},
        {"title": "RUTA","data": "FCT_RUTA"},
        {"title": "NOMBRE","data": "FCT_NAME"},
        {"title": "ARTICULO","data": "FCT_ARTI"},
        {"title": "DESCRIPCION","data": "FCT_DESC"},
        {"title": "CANTIDAD","data": "FCT_CANT"},
        {"title": "LOTE","data": "FCT_LOTE"},
    ];

    setTables()
    function setTables(){
        initTable('#tbl_facturas',[],tbl_header_inventarios_Fav);
        $("#tbl_facturas_length").hide();
        $("#tbl_facturas_filter").hide();
        $("#id_loading").hide();
    }



    $('#tbl_buscar_tbl_facturas').on('keyup', function() {        
        var vTableFavorito = $('#tbl_facturas').DataTable();
        vTableFavorito.search(this.value).draw();
    });

    


    function initTable(id,datos,Header){
        $(id).DataTable({
            "data": datos,
            "destroy": true,
            "responsive": true, 
            "info": false,
            "bPaginate": true,
            "order": [
                [0, "asc"]
            ],
            "lengthMenu": [
                [100, -1],
                [100, "Todo"]
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
            'columns': Header,
        });
    }

    
    $("#btn_search").click(function(){
        var Factura         = $("#tbl_buscar_tbl_facturas").val();    
            
        Factura             = isValue(Factura,'N/D',true)

        if(Factura === 'N/D' ){
            Swal.fire("Oops", "Digite el Numero de Factura", "error");
        }else{
            $("#id_loading").show();
            $.ajax({
                url: "getDevoluciones",
                type: 'POST',
                data: {
                    FACTURA   : Factura,
                    _token  : "{{ csrf_token() }}" 
                },
                async: true,
                success: function(data) {

                    initTable('#tbl_facturas',data,tbl_header_inventarios_Fav);
                    $("#tbl_facturas_length").hide();
                    $("#tbl_facturas_filter").hide();
                    $("#id_loading").hide();
                },
                error: function(response) {
                    Swal.fire("Oops", "No se ha podido guardar!", "error");
                }
            }).done(function(data) {
                //location.reload();
            });

          
        }

        
    })

</script>
