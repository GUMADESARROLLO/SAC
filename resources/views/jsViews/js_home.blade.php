<script type="text/javascript">
    var Selectors = {
        id_mdl_info_producto: '#modal_info_inventario',
        id_mdl_info_cliente : '#modal_info_cliente',
        MODAL_COMMENT: '#IdmdlComment',
    };
    const startOfMonth = moment().subtract(1,'days').format('YYYY-MM-DD');
    const endOfMonth   = moment().subtract(0, "days").format("YYYY-MM-DD");
    var labelRange = startOfMonth + " to " + endOfMonth;
    var dta_ventas_mercados ;
    
    dta_ventas_mercados = {
        dataset: {
        VentasDelMes:   [[],[],[]],
        }
    };

   


    function AddPlan() {
        var Codigo = $("#lbl_codigo").text();


        Swal.fire({
            title: '¿Estas Seguro ?',
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
                    url: "AddPlanCrecimiento",
                    type: 'post',
                    data: {
                        id      : Codigo,
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
                   // getComment(id_pedido)
                   location.reload();
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        });
    }

    $('#id_range_select').val(labelRange);

    $("#id_select_status").val(0).change();
    
    RangeStat(startOfMonth,endOfMonth)

    
    tbl_header_pedido =  [     
                {"title": "","data": "ARTICULO","className":'detalles-Pedido align-middle ', "render": function(data, type, row, meta) {
                    return `<span class="fas fa-arrow-alt-circle-down text-success"></span>`
                }},           
                {"title": "","data": "ARTICULO", "render": function(data, type, row, meta) {

                    var btns = '';
                    if(row.ESTADO  === 'PENDIENTE' ){

                        var btns = `<div class="col-auto"><a class="rounded-2 d-flex align-items-center me-3" href="#!" onclick="ChancesStatus(`+row.id + ',1'+`)"><span class="ms-1 fas fa-pencil-alt text-primary"></span><span class="ms-1">Procesar</span></a></div>
                                    <div class="col-auto"><a class="rounded-2 d-flex align-items-center me-3 text-700" href="#!" onclick="ChancesStatus(`+row.id + ',2'+`)"><span class="ms-1 fas fa-trash-alt text-danger" ></span><span class="ms-1">Cancelar</span></a></div>`;

                    }
                return `<div class="card">
                            <div class="card-header">
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


    $( "#tbl_select_inventario").change(function() {
        var table = $('#tbl_inventario').DataTable();
        table.page.len(this.value).draw();
    });

    $('#tbl_buscar_inventario').on('keyup', function() {        
        var vTablePedido = $('#tbl_inventario').DataTable();
        vTablePedido.search(this.value).draw();
    });


    $( "#tbl_select_cliente").change(function() {
        var table = $('#tbl_mst_clientes').DataTable();
        table.page.len(this.value).draw();
    });

    $('#tbl_buscar_cliente').on('keyup', function() {        
        var vTablePedido = $('#tbl_mst_clientes').DataTable();
        vTablePedido.search(this.value).draw();
    });


    $( "#tbl_select_liq6").change(function() {
        var table = $('#tbl_inventario_liq_6').DataTable();
        table.page.len(this.value).draw();
    });

    $('#tbl_buscar_liq6').on('keyup', function() {        
        var vTablePedido = $('#tbl_inventario_liq_6').DataTable();
        vTablePedido.search(this.value).draw();
    });

    $( "#tbl_select_liq12").change(function() {
        var table = $('#tbl_inventario_liq_12').DataTable();
        table.page.len(this.value).draw();
    });

    $('#tbl_buscar_liq12').on('keyup', function() {        
        var vTablePedido = $('#tbl_inventario_liq_12').DataTable();
        vTablePedido.search(this.value).draw();
    });


    $( "#tbl_select_history_factura").change(function() {
        var table = $('#tbl_historico_factura').DataTable();
        table.page.len(this.value).draw();
    });

    $('#tbl_buscar_history_factura').on('keyup', function() {        
        var vTablePedido = $('#tbl_historico_factura').DataTable();
        vTablePedido.search(this.value).draw();
    });

    $( "#tbl_select_last3m").change(function() {
        var table = $('#tbl_ultm_3m').DataTable();
        table.page.len(this.value).draw();
    });

    $('#tbl_buscar_last3m').on('keyup', function() {        
        var vTablePedido = $('#tbl_ultm_3m').DataTable();
        vTablePedido.search(this.value).draw();
    });


    $( "#tbl_select_nofacturado").change(function() {
        var table = $('#tbl_no_facturado').DataTable();
        table.page.len(this.value).draw();
    });

    $('#tbl_search_nofacturado').on('keyup', function() {        
        var vTablePedido = $('#tbl_no_facturado').DataTable();
        vTablePedido.search(this.value).draw();
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
                var regla = cuarentena = '';

                var total = parseFloat( numeral(row['total']).format('00.00')) + parseFloat( numeral(row['005']).format('00.00')) 

                if(row.REGLAS!='0'){
                    regla = '';
                    myArray = row.REGLAS.split(",");
                    $.each( myArray, function( key, value ) {
                        regla +='<span class="badge rounded-pill ms-3 badge-soft-primary"><span class="fas fa-check"></span> '+value+'</span>'
                    });
                }

                if(row.cuarentena != null){
                    cuarentena = `<div class="col-auto d-flex align-items-center"><span class="badge rounded-pill ms-3 badge-soft-warning"> En Revisión </span></div>`;
                } else{
                    cuarentena = '';
                }

                return  ` <td class="align-middle ">
                    <div class="d-flex align-items-center position-relative">
                            <img class="rounded-1 border border-200 img-fluid" src="`+row.IMG_URL+`" arti="`+row.ARTICULO+`" descr="`+row.DESCRIPCION+`" nombreImg="`+row.IMG_NOMBRE+`" alt="" width="60">
                        <div class="flex-1 ms-3">
                        
                            <div class="d-flex align-items-center">
                                <h6 class="mb-1 fw-semi-bold text-nowrap"><a href="#!" onclick=" OpenModal(`+"'" + row.ARTICULO+"'"+`)"> <strong>`+  row.ARTICULO +`</strong></a> : `+row.DESCRIPCION.toUpperCase() +`</h6>
                                `+  regla +`
                            </div>
                            <p class="fw-semi-bold mb-0 text-500"></p>   
                        
                            <div class="row g-0 fw-semi-bold text-center py-2"> 
                                <div class="col-auto"><a class="rounded-2 d-flex align-items-center me-3 text-700" href="#!"><span class="ms-1 fas fa-boxes text-primary" ></span><span class="ms-1"> `+ numeral(total).format('0,00.00')  +` `+ row.UNIDAD_ALMACEN +`</span></a></div>
                                <div class="col-auto d-flex align-items-center"><span class="badge rounded-pill ms-3 badge-soft-primary"><span class="fas fa-check"></span> C$. `+ numeral(row.PRECIO_FARMACIA).format('0,00.00')  +`</span></div>
                                 `+cuarentena+`
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
                                <img class="rounded-circle" src="{{ asset('images/user/avatar-4.jpg') }}" alt="" />
                            </div>

                            <div class="flex-1 ms-3">
                                <h6 class="mb-1 fw-semi-bold">`+row.CLIENTE +` :  <a href="#!" onclick=" Modal_Cliente(`+"'" + row.CLIENTE+"'" +`)"> `+row.NOMBRE + lblMoroso +` `+mostrarIcono(row.CLIENTE)+`  </h6></a> 
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
                    <div class="d-flex align-items-center position-relative"><img class="rounded-1 border border-200 img-fluid" src="`+row.IMG_URL+`"alt="" width="60">
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

                $('#tbl_historico_factura').on('click', 'td.detalles-factura', function () {
                    var table = $('#tbl_historico_factura').DataTable();

                    var tr = $(this).closest('tr');
                    var row = table.row(tr);
                    var data = table.row( $(this).parents('tr') ).data();
                    
                    if ( row.child.isShown() ) {
                        $("#dv-"+data.FACTURA).hide();
                        row.child.hide();
                        tr.removeClass('shown');
                    }
                    else {
                        $("#dv-"+data.FACTURA).show();
                        show_detalles_factura(row.child,data);
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
                                        <th class="border-0">ARTICULO (  ` + lineas_pedido.length +`  ) </th>
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

            function show_detalles_factura(callback,detalles_pedido) {
                $.get( "getDetallesFactura/" + detalles_pedido.FACTURA, function( data ) {

                    var tbody = '';

                    $.each( data, function( key, value ) {

                        tbody += `<tr>
                                <td class="align-middle">
                                    <h6 class="mb-0 text-nowrap"> `+value.DESCRIPCION+` </h6>
                                    <p class="mb-0"> `+value.ARTICULO+` </p>
                                </td>
                                <td class="align-middle text-center"> `+numeral(value.CANTIDAD).format('0,00.00') +` </td>
                                <td class="align-middle text-center">C$. `+numeral(value.PRECIO_UNITARIO).format('0,00.00') +` </td>
                                <td class="align-middle text-end"> C$. `+numeral(value.PRECIO_TOTAL).format('0,00.00') +` </td>
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
                        </div>`

                        callback($(template)).show();

                })
                
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

    var cod_client = [];

    $.get( "getPlanCrecimientoIco", function( data ) {
        $('.activo').removeClass('text-warning');
                cod_client = data
    })

    function mostrarIcono(id){
        
        for(var i = 0; i < cod_client.length; i++){
            if(cod_client[i]['CODIGO_CLIENTE'] == id){
                return '<span class="text-warning fas fa-crown me-1"></span>';                
            }            
        }
        return "";        
    }

    $.ajax({
        url: "dtaEstadisticas",       
        type: 'get',
        async: true,
        success: function(data) {
            Estadisticas(data[0].Estadistica)
        },
        error: function(response) {
            //swal("Oops", "No se ha podido guardar!", "error");
        }
    }).done(function(data) {
        //mostrarRequisado(num_orden, id_articulo, 2);
    });

    $.ajax({
        url: "getData",       
        type: 'get',
        async: true,
        success: function(data) {
            var Users = ''
            initTable('#tbl_inventario',data[0].Inventario,tbl_header_inventarios,[[0, "asc"]],[]);
            initTable('#tbl_inventario_liq_12',data[0].Liq12Meses,tbl_header_inventarios_liq,[[0, "asc"]],[]);
            initTable('#tbl_inventario_liq_6',data[0].Liq6Meses,tbl_header_inventarios_liq,[[0, "asc"]],[]);
            initTable('#tbl_mst_clientes',data[0].Clientes,tbl_header_clientes,[[0, "asc"]],[]);
        
            $("#tbl_mst_clientes_length").hide();
            $("#tbl_mst_clientes_filter").hide();

            $("#tbl_inventario_length").hide();
            $("#tbl_inventario_filter").hide();

            $("#tbl_inventario_liq_6_length").hide();
            $("#tbl_inventario_liq_6_filter").hide();

            $("#tbl_inventario_liq_12_length").hide();
            $("#tbl_inventario_liq_12_filter").hide();

            TBLCL = $("#tbl_mst_clientes").DataTable();

            TBLCL.rows().every( function () {
                rowNode = this.node();
                rowData = this.data();
                (rowData.MOROSO === 'S')?  $(rowNode).addClass( 'bg-soft-danger' ) : ''

            } );

            $("#id_loading").hide();
        },
        error: function(response) {
            //swal("Oops", "No se ha podido guardar!", "error");
        }
    }).done(function(data) {
        //mostrarRequisado(num_orden, id_articulo, 2);
    });


    function Estadisticas(Data){
        
        $("#id_Venta_Meta").text('C$ ' + numeral(Data['Venta_Meta']).format('0,00.00'))
        $("#id_Venta_Real").text('C$ ' + numeral(Data['Venta_Real']).format('0,00.00'))
        $("#id_Venta_Actu").text('C$ ' + numeral(Data['Venta_Actu']).format('0,00.00'))
        $("#id_Venta_Week").text('C$ ' + numeral(Data['Venta_Week']).format('0,00.00'))
        $("#id_Venta_Week_Label").text(Data['Venta_Week_Label'])

        $("#id_Venta_Porc").text('% ' + numeral(Data['Venta_Porc']).format('0,00'))

        $("#id_Dias_Habiles").text(numeral(Data['Dias_Habiles']).format('0,00'))
        $("#id_Dias_Facturados").text(numeral(Data['Dias_Facturados']).format('0,00'))
        $("#id_Dias_porcent").text(Data['Dias_porcent'])

        $("#id_Cliente_Meta").text(Data['Cliente_Meta'])
        $("#id_Cliente_Real").text(Data['Cliente_Real'])
        $("#id_Cliente_Porc").text('% ' + Data['Cliente_Porc'])

    }


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
                   // thead += '<th class="text-nowrap text-end">CANT. INGRESADA POR COMPRA</th>';
                   // thead += '<th class="text-nowrap text-end">FECHA ULTM. INGRESO COMPRA</th>';
                   // thead += '<th class="text-nowrap text-end">FECHA DE CREACION</th>';
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
                          //  '<td class="text-truncate text-end">' + d[x]["CANTIDAD_INGRESADA"] + '</td>'+
                          //  '<td class="text-truncate text-center">' + d[x]["FECHA_INGRESO"] + '</td>'+
                          //  '<td class="text-truncate text-center">' + d[x]["FECHA_ENTRADA"] + '</td>'+
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
            $("#opcion1").val(data[0].InfoArticulo[0].ARTICULO);
            data[0].InfoArticulo[0]['cuarentena'] != null ? $("#opcion1").prop('checked',true) : $("#opcion1").prop('checked',false);

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
                {"title": "","data": "FACTURA","className":'detalles-factura align-middle', "render": function(data, type, row, meta) {
                    return `<span class="fas fa-arrow-alt-circle-down text-success"></span>`
                }},              
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

                       
                        <div class="row g-0 fw-semi-bold text-center py-2 fs--1"> 
                            <div class="col-auto"><a class="rounded-2 d-flex align-items-center me-3 text-700" href="#!"><span class="ms-1"> Disponible. `+ numeral(row.total).format('0,00.00') + " " + row.UNIDAD_ALMACEN +`</span></a></div>
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

            $("#lbl_limite").html('C$ ' + numeral(data[0].InfoCliente[0].LIMITE_CREDITO).format('0,00.00'))
            $("#lbl_saldo").html('C$ ' + numeral(data[0].InfoCliente[0].SALDO).format('0,00.00'))
            $("#lbl_disponible").html('C$ ' + numeral(data[0].InfoCliente[0].CREDITODISP).format('0,00.00'))

            $("#id_monto_base").html('C$ ' + numeral(data[0].PlanCrecimieto[0].InfoCliente.EVALUADO).format('0,00.00'))
            $("#id_crecimiento_esperado").html('C$ ' + numeral(data[0].PlanCrecimieto[0].InfoCliente.CRECIMIENTO).format('0,00.00'))
            $("#id_crecimiento_minimo").html('C$ ' + numeral(data[0].PlanCrecimieto[0].InfoCliente.COMPRA_MIN).format('0,00.00'))
            $("#id_porcent_crecimientio").html(numeral(data[0].PlanCrecimieto[0].InfoCliente.PROM_CUMP).format('0,00') + ' %')
            
            setBarVentasMes(data[0].PlanCrecimieto[0].SalesMonths)

            $("#flexSwitchCheckDefault").attr('checked',data[0].PlanCrecimieto[0].isPlan);
            $("#id_ttmes").html('TOTAL: C$ ' +numeral(data[0].PlanCrecimieto[0].ttactual).format('0,00'))

    


            var isMora  = isValue(data[0].ClienteMora[0],'N/D',true)

            if(isMora!='N/D'){
                $("#no_fact").html(numeral(data[0].ClienteMora[0].NoVencidos).format('0,00.00'))
                $("#30_dias").html(numeral(data[0].ClienteMora[0].Dias30).format('0,00.00'))
                $("#60_dias").html(numeral(data[0].ClienteMora[0].Dias60).format('0,00.00'))
                $("#90_dias").html(numeral(data[0].ClienteMora[0].Dias90).format('0,00.00'))
                $("#120_dias").html(numeral(data[0].ClienteMora[0].Dias120).format('0,00.00'))
                $("#mas_120_dias").html(numeral(data[0].ClienteMora[0].Mas120).format('0,00.00'))
            }

            initTable('#tbl_historico_factura',data[0].ClientesHistoricoFactura,tbl_header_historico_factura,[[2, "DESC"]],[2]);
            initTable('#tbl_ultm_3m',data[0].Historico3M,tbl_header_historico_3m,[[0, "asc"]],[]);
            initTable('#tbl_no_facturado',data[0].ArticulosNoFacturado,tbl_header_no_Facturado,[[0, "asc"]],[]);

            $("#tbl_historico_factura_length").hide();
            $("#tbl_historico_factura_filter").hide();

            $("#tbl_ultm_3m_length").hide();
            $("#tbl_ultm_3m_filter").hide();

            $("#tbl_no_facturado_length").hide();
            $("#tbl_no_facturado_filter").hide();
            
        })

        function setBarVentasMes(Data){

            dta_ventas_mercados = {
                dataset: {
                VentasDelMes:   [[],[],[]],
                }
            };

            $.each(Data, function (i, d) {
                dta_ventas_mercados.dataset['VentasDelMes'][0].push(numeral(d.ttMonth).format('00.00'))
                dta_ventas_mercados.dataset['VentasDelMes'][1].push(0)
                dta_ventas_mercados.dataset['VentasDelMes'][2].push(d.name_month + '/' + d.annio)
            });
            
            Build_Echart_bar(dta_ventas_mercados)
            
        }

        function abbrNum(number, decPlaces) {    
            decPlaces = Math.pow(10,decPlaces);
            var abbrev = [ " K", " M", " B", " T" ];
            for (var i=abbrev.length-1; i>=0; i--) {
                var size = Math.pow(10,(i+1)*3);
                if(size <= number) {
                    number = Math.round(number*decPlaces/size)/decPlaces;
                    if((number == 1000) && (i < abbrev.length - 1)) {
                        number = 1;
                        i++;
                    }
                    number += abbrev[i];
                    break;
                }
            }

            return number;
        }

        function Build_Echart_bar(data) {

      

var tooltipFormatter = function tooltipFormatter(params) {
    return `<div class="card">
              <div class="card-header bg-light py-2">
                <h6 class="text-600 mb-0">`+params[0].axisValue+ `</h6>
              </div>
              <div class="card-body py-2">
                <h6 class="text-600 mb-0 fw-normal">
                  <span class="fas fa-circle text-primary me-2"></span>C$ 
                  <span class="fw-medium">`+ numeral(params[0].data).format('0,00.00')+` </span>
                </h6>
                </div>
            </div>`;
};




            var getOptionSales = function getOptionSales(data1, data2, data3) {
                return function () {
                    return {
                        color: utils.getGrays().white,
                        tooltip: {
                        trigger: 'axis',
                        padding: 0,
                        backgroundColor: 'transparent',
                        borderWidth: 0,
                        transitionDuration: 0,
                        position: function position(pos, params, dom, rect, size) {
                            return getPosition(pos, params, dom, rect, size);
                        },
                        
                        axisPointer: {
                            type: 'none'
                        },
                        formatter: tooltipFormatter
                        },
                        xAxis: {
                            type: 'category',
                            data: data3,
                            axisLabel: {
                                color: utils.getGrays()['600'],
                            },
                        axisLine: {
                            lineStyle: {
                            color: utils.getGrays()['300'],
                            type: 'dashed'
                            }
                        },
                        axisTick:false,
                        boundaryGap: true
                        },
                        yAxis: {
                        position: 'right',
                        axisPointer: {
                            type: 'none'
                        },
                        axisTick: 'none',
                        splitLine: {
                            show: false
                        },
                        axisLine: {
                            show: false
                        },
                        axisLabel: {
                            show: false
                        }
                        },
                        
                        series: [{
                            type: 'bar',
                            name: 'Revenue',
                            data: data1,
                            lineStyle: {
                                color: utils.getColor('primary')
                            },
                            label: {
                                show: true,
                                position: 'top',
                                formatter: function(d) {
                                return "C$ " + abbrNum(d.data,2);
                                }
                            },
                            
                            itemStyle: {
                                barBorderRadius: [4, 4, 0, 0],
                                color: utils.getColor('primary'),
                                borderColor: utils.getGrays()['300'],
                                borderWidth: 1
                            },
                            emphasis: {
                                itemStyle: {
                                color: utils.getColor('primary')
                                }
                            }
                        }, {
                        type: 'line',
                        name: 'Optimo',
                        data: data2,
                        symbol: 'circle',
                        symbolSize: 6,
                        animation: false,
                        itemStyle: {
                            color: utils.getColor('warning')
                        },
                        
                        lineStyle: {
                            type: 'dashed',
                            width: 2,
                            color: utils.getColor('warning')
                        }
                        }],
                        grid: {
                        right: 5,
                        left: 5,
                        bottom: '8%',
                        top: '5%'
                        }
                    };
                };
            };

            var initChart = function initChart(el, options) {
                var userOptions = utils.getData(el, 'options');
                var chart = window.echarts.init(el);
                echartSetOption(chart, userOptions, options);
            };

            var chartKeys = ['VentasDelMes'];
            chartKeys.forEach(function (key) {
                var el = document.querySelector(".echart-sale-".concat(key));
                el && initChart(el, getOptionSales(
                data.dataset[key][0], 
                data.dataset[key][1], 
                data.dataset[key][2]
                ));
            });
            };
        var id_mdl_info_cliente = document.querySelector(Selectors.id_mdl_info_cliente);
        var modal = new window.bootstrap.Modal(id_mdl_info_cliente);
        modal.show();
    }

    function getStackIcon(icon, transform) {
        return `<span class="fa-stack ms-n1 me-3">
                    <i class="fas fa-circle fa-stack-2x text-200"></i>
                    <i class="${icon} fa-stack-1x text-primary" data-fa-transform=${transform}></i>
                </span>
                `;
        
    };

    function promocionLeerMas(id){
        $.ajax({
            type: "GET",
            url: 'getDataPromocion', 
            async: false,
            dataType: "json",
            success: function(data){
                $.each(data,function(key, registro) {
                   if(id == registro.id){
                    Swal.fire({
                        title: registro.titulo,
                        html: '<div style="text-align: justify">'+registro.descripcion.replace(/\n/g, '</br>')+'</div>'
                        
                   })
                   }
                }); 	 
                
            },
            error: function(data) {
                //alert('error');
            }
        });    

    }

    $(document).on('click', '.img-fluid', function (e) {
        articulo = $(this).attr('arti');
        descripcion = $(this).attr('descr');
        nombreImg = $(this).attr('nombreImg');
        user = $('#userId').val();
        
        if(nombreImg == "item.png" && user == 1){
            Swal.fire({
            title: '¡Desea ingresar una imagen para este articulo!',
            text: "¿Desea continuar con esta acción?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si!',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                Swal.fire({
                    title: 'Ingrese una Imagen',
                    html: '<form role="form" action="imgArticulo" method="post" enctype="multipart/form-data">'+
                            '@csrf'+
                            '<input type="hidden" value="'+articulo+'" name="sku">'+
                            '<input type="hidden" value="'+descripcion+'" name="nombre">'+
                            '<div class="form-group">'+
                            '<div class="panel">SUBIR IMAGEN</div>'+
                            '<input type="file" class="nuevaImagen" name="nuevaImagen" required>'+
                            '</div>'+
                            '<div class="modal-footer">'+
                            '<div class="col-md-12 text-center">'+
                            '<button type="submit" class="btn btn-primary">Subir</button>'+
                            '</div>'+
                            '</div>'+
                        '</form>',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ok!',
                    showConfirmButton: false,
                    showLoaderOnConfirm: true,
                    preConfirm: (result) => {
                                              
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                })
                
            },
                allowOutsideClick: () => !Swal.isLoading()
            });
        }else{
    
            url_image = $(this).attr('src');
            Swal.fire({
                showCloseButton: true,
                closeButtonColor: '#d33',
                showConfirmButton: false,
                imageUrl: url_image,
                imageAlt: 'Custom image',
            })

            $(".swal2-popup").css('width', '50%');
        }
    })   

    $(".nuevaImagen").change(function(){

        var imagen = this.files[0];

        var datosImagen = new FileReader;
        datosImagen.readAsDataURL(imagen);

        $(datosImagen).on("load", function(event){

            var rutaImagen = event.target.result;

            $(".previsualizar").attr("src", rutaImagen);

        })

    })

    $("#opcion1").click(function(){
        Articulo = $(this)[0];
        if(Articulo.checked){
            $.ajax({
                url: "insertCuarentena",
                type: 'post',
                data: {                
                    Articulo    : $(Articulo).val(),
                    Opcion      : 1,      
                    _token      : "{{ csrf_token() }}" 
                },
                async: true,
                success: function(response) {
                    Swal.fire("Guardar", "El producto ha sido puesto en revisión", "success");
                },
                error: function(response) {
                    
                }
            }).done(function(data) {
                
            });
        }else{
            $.ajax({
                url: "insertCuarentena",
                type: 'post',
                data: {                
                    Articulo   : $(Articulo).val(),
                    Opcion      : 2,    
                    _token      : "{{ csrf_token() }}" 
                },
                async: true,
                success: function(response) {console.log(response);
                    Swal.fire("Correcto", "El producto ya no esta en revisión", "success");
                },
                error: function(response) {
                    
                }
            }).done(function(data) {
                
            });   
        }
    })
    
</script>
