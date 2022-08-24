<script type="text/javascript">
    var Selectors = {
        id_mdl_info_producto: '#modal_info_inventario',
        id_mdl_info_cliente : '#modal_info_cliente',
    };
    $('#tbl_bodegas').DataTable();
    $('#tbl_lista_precios').DataTable();
    
   

    tbl_header_inventarios =  [                
                {"title": "ARTICULO","data": "ARTICULO", "render": function(data, type, row, meta) {
                return ` <td class="align-middle">
                    <div class="d-flex align-items-center position-relative"><img class="rounded-1 border border-200" src="{{ asset('images/item.png') }}"alt="" width="60">
                        <div class="flex-1 ms-3">
                        
                        <div class="d-flex align-items-center">
                            <h6 class="mb-1 fw-semi-bold text-nowrap"><a href="#!" onclick=" OpenModal(1)">`+ row.DESCRIPCION +`</a></h6>
                            <span class="badge rounded-pill ms-3 badge-soft-success"><span class="fas fa-check"></span>C$. `+ numeral(row.PRECIO_FARMACIA).format('0,00.00')  +`</span>
                        </div>
                        
                        <div class="row g-0 fw-semi-bold text-center py-2 fs--1"> 
                                <div class="col-auto"><a class="rounded-2 d-flex align-items-center me-3 text-700" href="#!"><span class="ms-1 fas fa-boxes text-primary" ></span><span class="ms-1"> `+ numeral(row.EXISTENCIA).format('0,00.00')  +` `+ row.UNIDAD +`</span></a></div>
                                <div class="col-auto d-flex align-items-center"><span class="ms-1 fas fa-boxes text-primary" data-fa-transform="shrink-2" ></span><span class="ms-1"> `+ row.ARTICULO +` </span></div>
                                
                        </div>
                        <p class="fw-semi-bold mb-0 text-500">`+row.REGLAS +`</p>   
                        
                        </div>
                    </div>
                </td> `
                }},
                ]
    tbl_header_clientes =  [                
                {"title": "CLIENTE","data": "CLIENTE", "render": function(data, type, row, meta) {
                    return `
                        <div class="d-flex align-items-center position-relative">
                                
                            <div class="avatar avatar-2xl status-online">
                                <img class="rounded-circle" src="{{ asset('images/item.png') }}" alt="" />
                            </div>
                            <div class="flex-1 ms-3">
                                <h6 class="mb-1 fw-semi-bold"><a href="#!" onclick=" Modal_Cliente(`+"'" + row.CLIENTE+"'" +`)"> `+row.NOMBRE +` </h6></a> 
                                <p class="fw-semi-bold mb-0 text-500 ">
                                    <p class="mb-1 fs--1 ">`+row.CLIENTE +` &bull; `+row.VENDEDOR +` </p>
                                    <p class="mb-0 fs--1" >`+row.DIRECCION +`</p>
                                    <p class="mb-0 fs--1" >Ultim Factura `+row.fecha + `</p>
                                    
                                    
                                </p>
                                
                            </div>
                        </div>
                    `
                }},
                {"title": "LIMITE","data": "LIMITE", "render": function(data, type, row, meta) {
                    return `<span class="badge rounded-pill ms-3 badge-soft-success ">C$  `+ numeral(row.LIMITE_CREDITO).format('0,00.00')  +`</span> `
                }},
                {"title": "SALDO","data": "SALDO", "render": function(data, type, row, meta) {
                    return `<span class="badge rounded-pill ms-3 badge-soft-danger ">C$  `+ numeral(row.SALDO).format('0,00.00')  +`</span> `
                }},
                {"title": "DISPONIBLE","data": "DISPONIBLE", "render": function(data, type, row, meta) {
                    return `<span class="badge rounded-pill ms-3 badge-soft-info ">C$  `+ numeral(row.CREDITODISP).format('0,00.00')  +`</span> `
                }},
                {"title": "TELEFONO 1","data": "TELEFONO1"},
                {"title": "TELEFONO 2","data": "TELEFONO2"},
                ]
        tbl_header_inventarios_liq =  [                
                {"title": "ARTICULO","data": "ARTICULO", "render": function(data, type, row, meta) {
                return ` <td class="align-middle">
                    <div class="d-flex align-items-center position-relative"><img class="rounded-1 border border-200" src="{{ asset('images/item.png') }}"alt="" width="60">
                        <div class="flex-1 ms-3">
                        
                        <div class="d-flex align-items-center">
                            <h6 class="mb-1 fw-semi-bold text-nowrap"><a href="#!"><strong>`+ row.ARTICULO +`</strong></a> : `+ row.DESCRIPCION +`</h6>
                            <span class="badge rounded-pill ms-3 badge-soft-success"><span class="fas fa-check"></span> Vencimiento. `+ row.fecha_vencimientoR +`</span>
                            <span class="badge rounded-pill ms-3 badge-soft-danger"><span class="fas fa-check"></span> Dias.. `+ row.DIAS_VENCIMIENTO +`</span>
                        </div>
                        
                        <div class="row g-0 fw-semi-bold text-center py-2 fs--1"> 
                                <div class="col-auto"><a class="rounded-2 d-flex align-items-center me-3 text-700" href="#!"><span class="ms-1 fas fa-boxes text-primary" ></span><span class="ms-1"> `+ numeral(row.totalExistencia).format('0,00.00')  +` `+ row.UNIDAD_VENTA +`</span></a></div>
                                
                                
                        </div>
                        <p class="fw-semi-bold mb-0 text-500"></p>   
                        
                        </div>
                    </div>
                </td> `
                }},
                ]

    $.get( "getData", function( data ) {
        initTable('#tbl_inventario',data[0].Inventario,tbl_header_inventarios);
        initTable('#tbl_inventario_liq_12',data[0].Liq12Meses,tbl_header_inventarios_liq);
        initTable('#tbl_inventario_liq_6',data[0].Liq6Meses,tbl_header_inventarios_liq);
        initTable('#tbl_mst_clientes',data[0].Clientes,tbl_header_clientes);
    })


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
    


    $( "#select_ruta").change(function() {
        vTableClientes.search(this.value).draw();
    });

 
    function OpenModal(Id){
        let reglas_bonificadas = "1+1,2+2,3+3,4+4,5+5,6+6,7+7,8+8,9+9,10+10,15+15,20+20,25+25,30+30";
        var regla ='';

        const myArray = reglas_bonificadas.split(",");
        $.each( myArray, function( key, value ) {
            regla +='<span class="badge rounded-pill fs--2 bg-200 text-primary ms-1"><span class="fas fa-caret-up me-1"></span>'+value+'</span>'
        });

        var id_mdl_info_producto = document.querySelector(Selectors.id_mdl_info_producto);
        var modal = new window.bootstrap.Modal(id_mdl_info_producto);
        modal.show();

        $("#id_reglas").html(regla);
    }

    function Modal_Cliente(Id){

        tbl_header_historico_factura =  [                
                {"title": "FACTURA","data": "FACTURA", "render": function(data, type, row, meta) {
                return ` <td class="align-middle">
                    <div class="d-flex align-items-center position-relative"><img class="rounded-1 border border-200" src="{{ asset('images/item.png') }}"alt="" width="60">
                        <div class="flex-1 ms-3">
                        
                        <div class="d-flex align-items-center">
                            <h6 class="mb-1 fw-semi-bold text-nowrap"><a href="#!">Fact. `+ row.FACTURA +`</h6>
                            <span class="badge rounded-pill ms-3 badge-soft-success"> C$. `+ numeral(row.Venta).format('0,00.00') +`</span>
                            <span class="badge rounded-pill ms-3 badge-soft-danger"> Ven. `+ row.FECHA_VENCE +`</span>
                        </div>
                        
                        <div class="row g-0 fw-semi-bold text-center py-2 fs--1"> 
                                <div class="col-auto"><a class="rounded-2 d-flex align-items-center me-3 text-700" href="#!"><span class="ms-1"> `+ row.FACTURA +`</span></a></div>
                                
                                
                        </div>
                        <p class="fw-semi-bold mb-0 text-500"></p>   
                        
                        </div>
                    </div>
                </td> `
                }},
                ]
        tbl_header_historico_3m =  [                
                {"title": "FACTURA","data": "FACTURA", "render": function(data, type, row, meta) {
                return ` <td class="align-middle">
                    <div class="d-flex align-items-center position-relative"><img class="rounded-1 border border-200" src="{{ asset('images/item.png') }}"alt="" width="60">
                        <div class="flex-1 ms-3">
                        
                        <div class="d-flex align-items-center">                            
                            <h6 class="mb-1 fw-semi-bold text-nowrap"><a href="#!"><strong>`+ row.ARTICULO +`</strong></a> : `+ row.DESCRIPCION +`</h6>
                           
                        </div>
                        
                        <div class="flex-1 align-self-center ms-2">
                          <p class="mb-0 fs--1"> C$. `+ numeral(row.Venta).format('0,00.00') +` &bull;  Cant. `+ numeral(row.CANTIDAD).format('0,00.00') +`  &bull; `+ row.Dia +` &bull; <span class="fas fa-globe-americas"></span></p>
                        </div>  
                        
                        </div>
                    </div>
                </td> `
                }},
                ]
        tbl_header_no_Facturado =  [                
                {"title": "FACTURA","data": "FACTURA", "render": function(data, type, row, meta) {
                return ` <td class="align-middle">
                    <div class="d-flex align-items-center position-relative"><img class="rounded-1 border border-200" src="{{ asset('images/item.png') }}"alt="" width="60">
                        <div class="flex-1 ms-3">
                        
                        <div class="d-flex align-items-center">                            
                            <h6 class="mb-1 fw-semi-bold text-nowrap"><a href="#!"><strong>`+ row.ARTICULO +`</strong></a> : `+ row.DESCRIPCION +`</h6>
                           
                        </div>
                     
                        </div>
                    </div>
                </td> `
                }},
                ]

        $.get( "getDataCliente/" + Id, function( data ) {




            $("#lbl_nombre_cliente").html(data[0].InfoCliente[0].NOMBRE)
            $("#lbl_rutas").html(data[0].InfoCliente[0].VENDEDOR)
            $("#lbl_codigo").html(data[0].InfoCliente[0].CLIENTE)
            $("#lbl_last_sale").html(data[0].InfoCliente[0].fecha)

            $("#lbl_limite").html(numeral(data[0].InfoCliente[0].LIMITE_CREDITO).format('0,00.00'))
            $("#lbl_saldo").html(numeral(data[0].InfoCliente[0].SALDO).format('0,00.00'))
            $("#lbl_disponible").html(numeral(data[0].InfoCliente[0].CREDITODISP).format('0,00.00'))

            $("#no_fact").html(numeral(data[0].ClienteMora[0].NoVencidos).format('0,00.00'))
            $("#30_dias").html(numeral(data[0].ClienteMora[0].Dias30).format('0,00.00'))
            $("#60_dias").html(numeral(data[0].ClienteMora[0].Dias60).format('0,00.00'))
            $("#90_dias").html(numeral(data[0].ClienteMora[0].Dias90).format('0,00.00'))
            $("#120_dias").html(numeral(data[0].ClienteMora[0].Dias120).format('0,00.00'))
            $("#mas_120_dias").html(numeral(data[0].ClienteMora[0].Mas120).format('0,00.00'))


            initTable('#tbl_historico_factura',data[0].ClientesHistoricoFactura,tbl_header_historico_factura);
            initTable('#tbl_ultm_3m',data[0].Historico3M,tbl_header_historico_3m);
            initTable('#tbl_no_facturado',data[0].ArticulosNoFacturado,tbl_header_no_Facturado);

            
            
            
            
            

            
        })


        var id_mdl_info_cliente = document.querySelector(Selectors.id_mdl_info_cliente);
        var modal = new window.bootstrap.Modal(id_mdl_info_cliente);
        modal.show();
    }
</script>
