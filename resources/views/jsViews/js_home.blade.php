<script type="text/javascript">
    var Selectors = {
        id_mdl_info_producto: '#modal_info_inventario',
        id_mdl_info_cliente : '#modal_info_cliente',
        MODAL_COMMENT: '#IdmdlComment',
    };
    const startOfMonth = moment().subtract(1,'days').format('YYYY-MM-DD');
    const endOfMonth   = moment().subtract(0, "days").format("YYYY-MM-DD");
    var labelRange = startOfMonth + " to " + endOfMonth;
    $('#id_range_select').val(labelRange);

    $("#id_select_status").val(0).change();
    
    RangeStat(startOfMonth,endOfMonth)

    
    
    
    

   
    tbl_header_pedido =  [     
                {"title": "","data": "ARTICULO","className":'detalles-Pedido align-items-center', "render": function(data, type, row, meta) {
                    return `<span class="fas fa-arrow-alt-circle-down text-success"></span>`
                }},           
                {"title": "","data": "ARTICULO", "render": function(data, type, row, meta) {

                    var btns = '';
                    if(row.ESTADO  === 'PENDIENTE' ){

                        var btns = `<div class="col-auto"><a class="rounded-2 d-flex align-items-center me-3" href="#!" onclick="ChancesStatus(`+row.id + ',1'+`)"><span class="ms-1 fas fa-pencil-alt text-primary"></span><span class="ms-1">Procesar</span></a></div>
                                    <div class="col-auto"><a class="rounded-2 d-flex align-items-center me-3 text-700" href="#!" onclick="ChancesStatus(`+row.id + ',2'+`)"><span class="ms-1 fas fa-trash-alt text-danger" ></span><span class="ms-1">Cancelar</span></a></div>`;

                    }
                return `<div class="card">
                            <div class="card-header bg-light">
                              <div class="row justify-content-between">
                                <div class="col">
                                  <div class="d-flex">
                                    <div class="avatar avatar-2xl status-online">
                                      <img class="rounded-circle" src="{{ asset('images/item.png') }}" alt="" />
                                    </div>
                                    <div class="flex-1 align-self-center ms-2">
                                        <h6 class="mb-1 fs-1 fw-semi-bold">`+ row.CLIENTE + row.DESCRIPCION +`</h6>
                                        <p class="mb-0 fs--1">`+row.DIRECCION +` &bull; ` + row.FECHA + `
                                            <span class="badge rounded-pill ms-3 badge-soft-`+row.COLOR + `"><span class="fas fa-check"></span> `+row.ESTADO + `</span>
                                        </p>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="card-footer bg-light pt-0">
                                <div class="row flex-between-center g-0">
                                    <div class="col-auto">
                                    <div class="row g-0 fw-semi-bold text-center py-2 fs--1">
                                        <div class="col-auto"><a class="rounded-2 d-flex align-items-center me-3" href="#!"><span class="ms-1 text-primary">C$ `+ numeral(row.MONTO).format('0,00.00')   +`</span></a></div>
                                        `+btns+ `
                                        <div class="col-auto"><a class="rounded-2 d-flex align-items-center me-3 text-700" href="#!" onclick="AddComment('${encodeURIComponent(JSON.stringify(row))}')"><span class="ms-1 fas fa-comment text-primary" ></span><span class="ms-1"> `+ row.CountsComments+` </span></a></div>
                                    </div>
                                   
                                    
                                    </div>
                                    <div class="col-auto">
                                        Realizado por  `+ row.RUTA  + `  `+ row.VENDEDOR  + `
                                    </div>
                                </div>
                              
                              
                            </div>
                          </div>`
                }},
                ] 

    $('#id_range_select').change(function () {
        Fechas = $(this).val().split("to");
        if(Object.keys(Fechas).length >= 2 ){
            RangeStat(Fechas[0],Fechas[1]);
        } 
    });
    $( "#frm_lab_row").change(function() {
        var table = $('#tbl_mst_pedido').DataTable();
        table.page.len(this.value).draw();
    });

    $("#id_btn_new").click(function(){
        $("#id_loading").show();
        Fechas = $('#id_range_select').val().split("to");
        if(Object.keys(Fechas).length >= 2 ){
            RangeStat(Fechas[0],Fechas[1]);
        } 
    });

    $( "#id_select_status").change(function() {
        var table = $('#tbl_mst_pedido').DataTable();

        var selectedText  = this.selectedOptions[0].text;
        if(selectedText == "Todo"){
            table.search("").draw();
        }else{
            table.search(selectedText).draw();
        }
    });
    function RangeStat(Start,Ends){
        Start       = $.trim(Start)
        Ends        = $.trim(Ends)        
        Estado      = $("#id_select_status option:selected").val();  
        SAC         = $("#id_select_sac option:selected").val();  
        $.ajax({
            url: "getPedidosRangeDates",
            type: 'post',
            dataType: 'json',
            data: {
                DateStart   : Start,
                DateEnds    : Ends,
                Estado      : Estado,
                SAC         : SAC,
                _token  : "{{ csrf_token() }}" 
            },
            async: true,
            success: function(Pedidos) {

                initTable('#tbl_mst_pedido',Pedidos,tbl_header_pedido,[[0, "asc"]],[]);

                $("#tbl_mst_pedido_length").hide();
                $("#tbl_mst_pedido_filter").hide();

                $("#id_loading").hide();


            }
        })
        
    }
    
    $('#id_txt_buscar').on('keyup', function() {        
        var vTablePedido = $('#tbl_mst_pedido').DataTable();
        vTablePedido.search(this.value).draw();
    });
    tbl_header_inventarios =  [                
                {"title": "ARTICULO","data": "ARTICULO", "render": function(data, type, row, meta) {
                var regla='';

                if(row.REGLAS!='0'){
                    regla = '';
                    myArray = row.REGLAS.split(",");
                    $.each( myArray, function( key, value ) {
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
                            <div class="col-auto"><a class="rounded-2 d-flex align-items-center me-3 text-700" href="#!"><span class="ms-1 fas fa-boxes text-primary" ></span><span class="ms-1"> `+ numeral(row.total).format('0,00.00')  +` `+ row.UNIDAD_ALMACEN +`</span></a></div>
                            <div class="col-auto d-flex align-items-center"><span class="badge rounded-pill ms-3 badge-soft-primary"><span class="fas fa-check"></span> C$. `+ numeral(row.PRECIO_FARMACIA).format('0,00.00')  +`</span></div>
                                
                        </div>
                        </div>
                    </div>
                </td> `

                
                }},
                
                ]
    tbl_header_clientes =  [                              
                {"title": "CLIENTE","data": "CLIENTE", "render": function(data, type, row, meta) {
                    var lblMoroso = (row.MOROSO==='S')? ' [ MOROSO ]':''
                    return `
                        <div class="d-flex align-items-center position-relative">
                                
                            <div class="avatar avatar-2xl status-online">
                                <img class="rounded-circle" src="{{ asset('images/item.png') }}" alt="" />
                            </div>

                            <div class="flex-1 ms-3">
                                <h6 class="mb-1 fw-semi-bold">`+row.CLIENTE +` :  <a href="#!" onclick=" Modal_Cliente(`+"'" + row.CLIENTE+"'" +`)"> `+row.NOMBRE + lblMoroso +` </h6></a> 
                                <p class="fw-semi-bold mb-0 text-500 ">
                                    <p class="mb-0 fs--1" >`+row.DIRECCION +`</p>
                                    <p class="mb-0 fs--1" >Ultim Factura. ` + moment(row.fecha).format("D MMM, YYYY") + `</p>
                                </p>
                                
                            </div>
                        </div>
                    `
                }},
                {"title": "VENDEDOR","data": "VENDEDOR", "render": function(data, type, row, meta) {
                    return `<span class="badge rounded-pill ms-3 badge-soft-success ">`+ row.VENDEDOR +`</span> `
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
        
                $('#tbl_mst_pedido').on('click', 'td.detalles-Pedido', function () {
                    var table = $('#tbl_mst_pedido').DataTable();

                    var tr = $(this).closest('tr');
                    var row = table.row(tr);
                    var data = table.row( $(this).parents('tr') ).data();


                    if ( row.child.isShown() ) {
                        $("#dv-"+data.id).hide();
                        row.child.hide();
                        tr.removeClass('shown');
                    }
                    else {
                        $("#dv-"+data.id).show();
                        show_detalles_pedido(row.child,data);
                        tr.addClass('shown');
                    }

                });
                function AddComment(row){
                    obj = JSON.parse(decodeURIComponent(row))

                    var fecha_humana = obj.FECHA

                    $("#id_modal_name_item").text(obj.CLIENTE + obj.DESCRIPCION)
                    $("#id_modal_articulo").text(obj.DIRECCION)
                    $("#id_modal_nSoli").text(obj.code)
                    $("#id_modal_Fecha").text(fecha_humana)


                    $("#id_modal_vendedor").text(obj.VENDEDOR)
                    $("#id_comentario_pedido").text(obj.COMMENT)
                    
                    
                    var addcomment_ = document.querySelector(Selectors.MODAL_COMMENT);
                    var mdl_comment = new window.bootstrap.Modal(addcomment_);
                
                    mdl_comment.show();
                    getComment(obj.code)

                }


                function show_detalles_pedido(callback,detalles_pedido) {
                    var tbody = '';

                    var lineas_pedido = detalles_pedido.ARTICULOS.split('],')   
                    
                    lineas_pedido = lineas_pedido.splice(0,lineas_pedido.length-1) ;
                    $.each( lineas_pedido, function( key, value ) {
                        var Lineas_detalles     = value.split(';') 
                        tbody += `<tr>
                                    <td class="align-middle">
                                        <h6 class="mb-0 text-nowrap">` +Lineas_detalles[2] + `</h6>
                                        <p class="mb-0">` +Lineas_detalles[1] + `</p>
                                    </td>
                                    <td class="align-middle text-center">` +Lineas_detalles[0].replace("[","") + `</td>
                                    <td class="align-middle text-center">` +Lineas_detalles[3] + `</td>
                                    <td class="align-middle text-end">` +Lineas_detalles[4] + `</td>
                                </tr>`
                    });

                    var template =`
                        <div class="card">
                            <div class="card-body">                            
                                <div class="table-responsive scrollbar mt-4 fs--1">
                                    <table class="table table-striped border-bottom">
                                        <thead class="light">
                                            <tr class="bg-primary text-white dark__bg-1000">
                                            <th class="border-0">ARTICULO</th>
                                            <th class="border-0 text-center">CANTIDAD</th>
                                            <th class="border-0 text-center">BONIFICADO</th>
                                            <th class="border-0 text-end">VALOR</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ` + tbody + `                                    
                                        </tbody>
                                    </table>
                                </div>                            
                            </div>
                            <div class="card-footer bg-light">
                                <p class="fs--1 mb-0"><strong>Nota: </strong> ` + detalles_pedido.COMMENT +`</p>
                            </div>
                        </div>`

                    callback($(template)).show();
        
                
            }
            function getComment(Id){
                var items_comment = '';
                $("#id_textarea_comment").val(items_comment)
                $.ajax({
                    url: 'getCommentPedido',
                    type: 'post',
                    data: {
                        id_item     : Id,                  
                        _token      : "{{ csrf_token() }}" 
                    }, 
                    async: false,
                    dataType: "json",
                    success: function(data){
                        $.each(data,function(key, c) {
                            var var_borrar = ''

                            var_borrar = '<a href="#!" onClick="DeleteComment('+c.id_coment+' , '+ "'" +Id + "'" +' )">Borrar</a> &bull; '                             

                            var date_comment = moment(c.date_coment).format("D MMM, YYYY")
                            items_comment += ' <div class="d-flex mt-3">'+
                                                    '<div class="avatar avatar-xl">'+
                                                        '<img class="rounded-circle" src="{{ asset("images/user/avatar-4.jpg") }}" alt="" />'+
                                                    '</div>'+
                                                    '<div class="flex-1 ms-2 fs--1">'+
                                                        '<p class="mb-1 bg-200 rounded-3 p-2">'+
                                                        '<a class="fw-semi-bold" href="!#">'+c.player_id.toUpperCase()+'</a> '+
                                                        ' '+c.orden_comment+'  </p>'+
                                                        '<div class="px-2">'+
                                                        var_borrar+
                                                        date_comment+'</div>'+
                                                    '</div>'+
                                                '</div>'
                        }); 	 
                    },
                    error: function(data) {
                        //alert('error');
                    }
                }); 

                $("#id_comment_item").html(items_comment)
            }
    function DeleteComment(id_comment,id_pedido){
        Swal.fire({
            title: '¿Estas Seguro de borrar el Comentario?',
            text: "¡Esta acción no podrá ser revertida!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si!',
            target:"",
            showLoaderOnConfirm: true,
            preConfirm: () => {
                $.ajax({
                    url: "DeleteCommentPedido",
                    type: 'post',
                    data: {
                        id      : id_comment,
                        _token  : "{{ csrf_token() }}" 
                    },
                    async: true,
                    success: function(response) {
                        //Swal.fire("Exito!", "Guardado exitosamente", "success");
                    },
                    error: function(response) {
                        //Swal.fire("Oops", "No se ha podido guardar!", "error");
                    }
                }).done(function(data) {
                    getComment(id_pedido)
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        });
    }           
    $('#id_textarea_comment').keydown(function(event){
        if (event.which == 13){

            var id_Item = $("#id_modal_nSoli").text()
            var value = $(this).val();

            $.ajax({
                url: "AddCommentPedido",
                type: 'post',
                data: {
                    id_item     : id_Item,
                    comment     : value,                    
                    _token      : "{{ csrf_token() }}" 
                },
                async: true,
                success: function(response) {
                    getComment(id_Item)
                },
                error: function(response) {
                   // Swal.fire("Oops", "No se ha podido guardar!", "error");
                }
            }).done(function(data) {
                //location.reload();
            });
        }
    });             
    function ChancesStatus(id_producto,Valor){

        lblModal = (Valor ==1)? 'Procesar': 'Cancelar'

        Swal.fire({
            title: '¿Estas Seguro de '+lblModal+' el Pedido?',
            text: "¡Esta acción no podrá ser revertida!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si!',
            target:"",
            showLoaderOnConfirm: true,
            preConfirm: () => {
                $.ajax({
                    url: "ChancesStatus",
                    type: 'post',
                    data: {
                        id      : id_producto,
                        Valor   : Valor,
                        _token  : "{{ csrf_token() }}" 
                    },
                    async: true,
                    success: function(response) {
                        if(response.original){
                        Swal.fire({
                        title: 'Correcto',
                        icon: 'success',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'OK'
                        }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                            }
                        })
                    }
                    },
                    error: function(response) {
                    }
                }).done(function(data) {
                    
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        });
    }
    $.get( "getData", function( data ) {
        var Users = ''
        initTable('#tbl_inventario',data[0].Inventario,tbl_header_inventarios,[[0, "asc"]],[]);
        initTable('#tbl_inventario_liq_12',data[0].Liq12Meses,tbl_header_inventarios_liq,[[0, "asc"]],[]);
        initTable('#tbl_inventario_liq_6',data[0].Liq6Meses,tbl_header_inventarios_liq,[[0, "asc"]],[]);
        initTable('#tbl_mst_clientes',data[0].Clientes,tbl_header_clientes,[[0, "asc"]],[]);

        TBLCL = $("#tbl_mst_clientes").DataTable();

            TBLCL.rows().every( function () {
            rowNode = this.node();
            rowData = this.data();
            (rowData.MOROSO === 'S')?  $(rowNode).addClass( 'bg-soft-danger' ) : ''

        } );

        $("#id_loading").hide();
    })


    function initTable(id,datos,Header,Order,Show){
        $(id).DataTable({
            "data": datos,
            "destroy": true,
            "info": false,
            "bPaginate": true,
            "order": Order,
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
            "columnDefs": [
                {
                    "visible": false,
                    "searchable": false,
                    "targets": Show
                },
            ],
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
    $('#tbl_bodegas').on('click', 'td.detalles-Lotes', function () {
        var table = $('#tbl_bodegas').DataTable();
        var tr = $(this).closest('tr');
        var row = table.row(tr);
        var attx = $("#id_codigo_articulo").text();

        var data = table.row( $(this).parents('tr') ).data();

        if ( row.child.isShown() ) {
            $("#dv-"+data.BODEGA).hide();
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            $("#dv-"+data.BODEGA).show();
            format(row.child,data.BODEGA,attx);
            tr.addClass('shown');
        }

    });

    function format(callback,bodega,art) {
        
        $.ajax({
            url:'getLotes/'+bodega+"/"+art,
            dataType: "json",
            complete: function (response) {
                var data = JSON.parse(response.responseText);
                var ia =0;
                var thead = '',  tbody = '';

                for (var key in data) {
                    thead += '<th class="text-nowrap">LOTE</th>';
                    thead += '<th class="text-nowrap text-end">CANT. DISPONIBLE</th>';
                    thead += '<th class="text-nowrap text-end">CANT. INGRESADA POR COMPRA</th>';
                    thead += '<th class="text-nowrap text-end">FECHA ULTM. INGRESO COMPRA</th>';
                    thead += '<th class="text-nowrap text-end">FECHA DE CREACION</th>';
                    thead += '<th class="text-nowrap text-end">FECHA VENCIMIENTO</th>';
                }

                $.each(data, function (i, d) {

                    $.each(d, function (a, b) {
                        ia++;
                    });


                    for (var x=0; x<ia; x++) {

                        var ART = "'" + d[x]["ARTICULO"] + "'";
                        var lt = "'" + d[x]["LOTE"] + "'";

                        tbody += '<tr class="center">' +
                            '<td class="text-truncate">' + d[x]["LOTE"] + '</td>'+
                            '<td class="text-truncate text-end">' + d[x]["CANT_DISPONIBLE"] + '</td>'+
                            '<td class="text-truncate text-end">' + d[x]["CANTIDAD_INGRESADA"] + '</td>'+
                            '<td class="text-truncate text-center">' + d[x]["FECHA_INGRESO"] + '</td>'+
                            '<td class="text-truncate text-center">' + d[x]["FECHA_ENTRADA"] + '</td>'+
                            '<td class="text-truncate text-center">' + d[x]["FECHA_VENCIMIENTO"] + '</td>'+
                            '</tr>';
                    }
                });

                if (ia==0){
                    thead += '<th></th>';
                    tbody += '<tr><td>BODEGA SIN EXISTENCIA</td></tr>';
                }
                callback($('<table class="table fs--1 mb-0 overflow-hidden"> <thead class="bg-200 text-900">' + thead + '</thead>' + tbody + '</table>')).show();
            },
            error: function () {
                Swal.fire(
                    'Oops...',
                    'Hubo un error al cargar los detalles!',
                    'error'
                )
            }
        });
    }
    tbl_header_bodega =  [
               
                {"title": "","data": "CANT_DISPONIBLE",  "className":'detalles-Lotes',"render": function(data, type, row, meta) {
                    return `<div class="border border-1 border-300 rounded-2 p-3 ask-analytics-item position-relative">
                      <div class="d-flex align-items-center mb-3"><span class="far fa-arrow-alt-circle-down text-success"></span><a class="stretched-link text-decoration-none" href="#!">
                          <h5 class="fs--1 text-600 mb-0 ps-3"> `+ row.BODEGA + " | " + row.NOMBRE + `</h5>
                        </a></div>
                      <h5 class="fs-0 text-900 mb-0 me-2">Cantidad Disponible <span class="badge rounded-pill ms-3 badge-soft-info ">`+ numeral(row.CANT_DISPONIBLE).format('0,00.00')  +`</span></h5>
                    </div>`
                }},
                ]
    tbl_header_nivel_precio =  [
                {"title": "BODEGA","data": "BODEGA", "render": function(data, type, row, meta) {
                    return `<span class="badge rounded-pill ms-3 badge-soft-success ">`+ row.NIVEL_PRECIO +`</span> `
                }},
                {"title": "DISPONIBLE","data": "DISPONIBLE", "render": function(data, type, row, meta) {
                    
                    return `<span class="badge rounded-pill ms-3 badge-soft-info ">`+ numeral(row.PRECIO).format('0,00.00')  +`</span> `
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
            $("#id_nombre_articulo").html(data[0].InfoArticulo[0].DESCRIPCION.toUpperCase());

            initTable_modal('#tbl_bodegas',data[0].Bodega,tbl_header_bodega);
            //initTable_modal('#tbl_lista_precios',data[0].NivelPrecio,tbl_header_nivel_precio);

            
            $("#id_precio_farmacia").html("C$ " + data[0].NivelPrecio[0].PRECIO)
            $("#id_precio_mayorista").html("C$ " + data[0].NivelPrecio[2].PRECIO)
            $("#id_precio_inst_pub").html("C$ " + data[0].NivelPrecio[1].PRECIO)
            $("#id_precio_mayortista").html("C$ " + data[0].NivelPrecio[3].PRECIO)
           
            $("#id_load_articulo").hide();
        })

       

        var id_mdl_info_producto = document.querySelector(Selectors.id_mdl_info_producto);
        var modal_articulo = new window.bootstrap.Modal(id_mdl_info_producto);
        modal_articulo.show();

       
    }

    function Modal_Cliente(Id){
        $("#id_load_cliente").show();
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
                            <div class="col-auto"><a class="rounded-2 d-flex align-items-center me-3 text-700" href="#!"><span class="ms-1"> `+ moment(row.Dia).format("D MMM, YYYY") +`</span></a></div>
                        </div>
                    </div>
                </td> `
                }},
                {"title": "Dia","data": "Dia"}
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
            $("#id_load_cliente").hide();
            $("#lbl_rutas").html(data[0].InfoCliente[0].VENDEDOR)
            $("#lbl_codigo").html(data[0].InfoCliente[0].CLIENTE)
            
            $("#lbl_last_sale").html(moment(data[0].InfoCliente[0].fecha).format("D MMM, YYYY"))

            $("#lbl_limite").html(numeral(data[0].InfoCliente[0].LIMITE_CREDITO).format('0,00.00'))
            $("#lbl_saldo").html(numeral(data[0].InfoCliente[0].SALDO).format('0,00.00'))
            $("#lbl_disponible").html(numeral(data[0].InfoCliente[0].CREDITODISP).format('0,00.00'))

            var isMora  = isValue(data[0].ClienteMora[0],'N/D',true)

            if(isMora!='N/D'){
                $("#no_fact").html(numeral(data[0].ClienteMora[0].NoVencidos).format('0,00.00'))
                $("#30_dias").html(numeral(data[0].ClienteMora[0].Dias30).format('0,00.00'))
                $("#60_dias").html(numeral(data[0].ClienteMora[0].Dias60).format('0,00.00'))
                $("#90_dias").html(numeral(data[0].ClienteMora[0].Dias90).format('0,00.00'))
                $("#120_dias").html(numeral(data[0].ClienteMora[0].Dias120).format('0,00.00'))
                $("#mas_120_dias").html(numeral(data[0].ClienteMora[0].Mas120).format('0,00.00'))
            }

            initTable('#tbl_historico_factura',data[0].ClientesHistoricoFactura,tbl_header_historico_factura,[[1, "DESC"]],[1]);
            initTable('#tbl_ultm_3m',data[0].Historico3M,tbl_header_historico_3m,[[0, "asc"]],[]);
            initTable('#tbl_no_facturado',data[0].ArticulosNoFacturado,tbl_header_no_Facturado,[[0, "asc"]],[]);
            
        })


        var id_mdl_info_cliente = document.querySelector(Selectors.id_mdl_info_cliente);
        var modal = new window.bootstrap.Modal(id_mdl_info_cliente);
        modal.show();
    }
</script>
