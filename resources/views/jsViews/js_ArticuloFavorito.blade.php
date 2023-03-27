<script type="text/javascript">
    var Selectors = {
        id_mdl_info_producto: '#modal_info_inventario',
    };

    tbl_header_inventarios_Fav =  [                
                {"title": "ARTICULO","data": "ARTICULO", "render": function(data, type, row, meta) {
                var regla='';
                var total = parseFloat( numeral(row['total']).format('00.00')) + parseFloat( numeral(row['005']).format('00.00')) 
                if(row.REGLAS!='0'){
                    regla = '';
                    myArray = row.REGLAS.split(",");
                    $.each( myArray, function( key, value ) {
                        //regla +='<span class="badge rounded-pill fs--2 bg-200 text-primary ms-1">'+value+'</span>'   
                        regla +='<span class="badge rounded-pill ms-3 badge-soft-primary"><span class="fas fa-check"></span> '+value+'</span>'
                    });
                }

                return  ` <td class="align-middle">
                    <div class="d-flex align-items-center position-relative"><img class="rounded-1 border border-200" src="{{ asset('images/item.png') }}"alt="" width="60">
                        <div class="flex-1 ms-3">
                        
                        <div class="d-flex align-items-center">
                            <h6 class="mb-1 fw-semi-bold text-nowrap"><a href="#!" onclick=" OpenModal(`+"'" + row.ARTICULO+"'" +`)"> <strong>`+  row.ARTICULO +`</strong></a> : `+row.DESCRIPCION.toUpperCase() +`</h6>
                            `+  regla +`
                            
                        </div>
                        <p class="fw-semi-bold mb-0 text-500"></p>   
                        
                        <div class="row g-0 fw-semi-bold text-center py-2"> 
                            <div class="col-auto">
                                <a class="rounded-2 d-flex align-items-center me-3 text-700" href="#!">
                                    <span class="ms-1 fas fa-boxes text-primary" ></span><span class="ms-1"> `+ numeral(total).format('0,00.00')  +` `+ row.UNIDAD_ALMACEN +`</span>
                                </a>
                            </div>
                            <div class="col-auto d-flex align-items-center"><span class="badge rounded-pill ms-3 badge-soft-primary">
                                <span class="fas fa-check"></span> C$. `+ numeral(row.PRECIO_FARMACIA).format('0,00.00')  +`</span>
                            </div>
                            <div class="col-auto d-flex align-items-center" onclick="save_fav(` + "'" +row.ARTICULO + "'" + ` )">
                                <span class="ms-1 fas fa-star text-warning" data-fa-transform="shrink-2" ></span><span class="ms-1"></span>   
                            </div>
                            
                        </div>
                        </div>
                    </div>
                </td> `

                
                }},
                
                ]

    tbl_header_inventarios =  [                
                {"title": "ARTICULO","data": "ARTICULO", "render": function(data, type, row, meta) {
                var regla='';

                var total = parseFloat( numeral(row['total']).format('00.00')) + parseFloat( numeral(row['005']).format('00.00')) 

                if(row.REGLAS!='0'){
                    regla = '';
                    myArray = row.REGLAS.split(",");
                    $.each( myArray, function( key, value ) {
                        //regla +='<span class="badge rounded-pill fs--2 bg-200 text-primary ms-1">'+value+'</span>'   
                        regla +='<span class="badge rounded-pill ms-3 badge-soft-primary"><span class="fas fa-check"></span> '+value+'</span>'
                    });
                }

                return  ` <td class="align-middle">
                    <div class="d-flex align-items-center position-relative"><img class="rounded-1 border border-200" src="{{ asset('images/item.png') }}"alt="" width="60">
                        <div class="flex-1 ms-3">
                        
                        <div class="d-flex align-items-center">
                            <h6 class="mb-1 fw-semi-bold text-nowrap"><a href="#!"> <strong>`+  row.ARTICULO +`</strong></a> : `+row.DESCRIPCION.toUpperCase() +`</h6>
                            `+  regla +`
                            
                        </div>
                        <p class="fw-semi-bold mb-0 text-500"></p>   
                        
                        <div class="row g-0 fw-semi-bold text-center py-2"> 
                            <div class="col-auto">
                                <a class="rounded-2 d-flex align-items-center me-3 text-700" href="#!">
                                    <span class="ms-1 fas fa-boxes text-primary" ></span><span class="ms-1"> `+ numeral(total).format('0,00.00')  +` `+ row.UNIDAD_ALMACEN +`</span>
                                </a>
                            </div>
                            <div class="col-auto d-flex align-items-center"><span class="badge rounded-pill ms-3 badge-soft-primary">
                                <span class="fas fa-check"></span> C$. `+ numeral(row.PRECIO_FARMACIA).format('0,00.00')  +`</span>
                            </div>
                            <div class="col-auto d-flex align-items-center" onclick="save_fav(` + "'" +row.ARTICULO + "'" + ` )">
                                <span class="ms-1 far fa-star text-warning" data-fa-transform="shrink-2" ></span><span class="ms-1"></span>   
                            </div>
                            
                        </div>
                        </div>
                    </div>
                </td> `

                
                }},
                
                ] 

    setTables()
    function setTables(){

        $.get( "getArticulosFavoritos", function( data ) {
            initTable('#tbl_inventario_fav',data[0].ArticulosFav,tbl_header_inventarios_Fav);
            initTable('#tbl_inventario',data[0].Inventario,tbl_header_inventarios);
            

            $("#tbl_inventario_fav_length").hide();
            $("#tbl_inventario_fav_filter").hide();

            $("#tbl_inventario_length").hide();
            $("#tbl_inventario_filter").hide();

            $("#id_loading").hide();
        })
    }

    $( "#tbl_select_inventario_fav").change(function() {
        var table = $('#tbl_inventario_fav').DataTable();
        table.page.len(this.value).draw();
    });

    $('#tbl_buscar_inventario_fav').on('keyup', function() {        
        var vTableFavorito = $('#tbl_inventario_fav').DataTable();
        vTableFavorito.search(this.value).draw();
    });

    $( "#tbl_select_inventario").change(function() {
        var table = $('#tbl_inventario').DataTable();
        table.page.len(this.value).draw();
    });

    $('#tbl_buscar_inventario').on('keyup', function() {        
        var vTableFavorito = $('#tbl_inventario').DataTable();
        vTableFavorito.search(this.value).draw();
    });

    function save_fav(Articulo){

        $.ajax({
            url: "AddFavs",
            type: 'post',
            data: {                
                Articulo   : Articulo,        
                _token      : "{{ csrf_token() }}" 
            },
            async: true,
            success: function(response) {
                console.log()
                if(response.original){
                    location.reload();
                }
            },
            error: function(response) {
                // Swal.fire("Oops", "No se ha podido guardar!", "error");
            }
        }).done(function(data) {
            //
        });
    }

    


    function initTable(id,datos,Header){
        $(id).DataTable({
            "data": datos,
            "destroy": true,
            "info": false,
            "bPaginate": true,
            "order": [
                [0, "asc"]
            ],
            "lengthMenu": [
                [5, -1],
                [5, "Todo"]
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
    function initTable_modal(id,datos,Header){
        $(id).DataTable({
            "data": datos,
            "destroy": true,
            "info": false,
            "bPaginate": false,
            "searching": false,
            "order": [
                [0, "asc"]
            ],
            "lengthMenu": [
                [5, -1],
                [5, "Todo"]
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
    


    $( "#select_ruta").change(function() {
        vTableClientes.search(this.value).draw();
    });

    tbl_header_bodega =  [
                {"title": "BODEGA","data": "BODEGA", "render": function(data, type, row, meta) {
                    return `<span class="badge rounded-pill ms-3 badge-soft-success ">`+ row.BODEGA +`</span> `
                }},
                {"title": "NOMBRE","data": "NOMBRE", "render": function(data, type, row, meta) {
                    return `<span class="badge rounded-pill ms-3 badge-soft-danger ">`+row.NOMBRE+`</span> `
                }},
                {"title": "DISPONIBLE","data": "DISPONIBLE", "render": function(data, type, row, meta) {
                    return `<span class="badge rounded-pill ms-3 badge-soft-info ">C$  `+ numeral(row.TOTAL).format('0,00.00')  +`</span> `
                }},
                ]
    tbl_header_nivel_precio =  [
                {"title": "BODEGA","data": "BODEGA", "render": function(data, type, row, meta) {
                    return `<span class="badge rounded-pill ms-3 badge-soft-success ">`+ row.NIVEL_PRECIO +`</span> `
                }},
                {"title": "DISPONIBLE","data": "DISPONIBLE", "render": function(data, type, row, meta) {
                    return `<span class="badge rounded-pill ms-3 badge-soft-info ">C$  `+ numeral(row.PRECIO).format('0,00.00')  +`</span> `
                }},
                ]
    function OpenModal(Id){
        var regla ='';
        var reglas_bonificadas;
        var myArray;
        $("#id_load_articulo").show();

        $.get( "getDataArticulo/" + Id, function( data ) {

            reglas_bonificadas = data[0].InfoArticulo[0].REGLAS
            
            myArray = reglas_bonificadas.split(",");
            $.each( myArray, function( key, value ) {
                regla +='<span class="badge rounded-pill fs--2 bg-200 text-primary ms-1"><span class="fas fa-caret-up me-1"></span>'+value+'</span>'
                
            });

            $("#id_reglas").html(regla);
            $("#id_codigo_articulo").html(data[0].InfoArticulo[0].ARTICULO);
            $("#lbl_unidad").html(data[0].InfoArticulo[0].UNIDAD);            
            $("#id_nombre_articulo").html(data[0].InfoArticulo[0].DESCRIPCION);

            initTable_modal('#tbl_bodegas',data[0].Bodega,tbl_header_bodega);
            initTable_modal('#tbl_lista_precios',data[0].NivelPrecio,tbl_header_nivel_precio);
        
            $("#id_load_articulo").hide();
        })

       

        var id_mdl_info_producto = document.querySelector(Selectors.id_mdl_info_producto);
        var modal = new window.bootstrap.Modal(id_mdl_info_producto);
        modal.show();

       
    }

    
</script>
