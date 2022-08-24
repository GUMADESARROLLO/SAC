<script type="text/javascript">
    var Selectors = {
        TABLE_SETTING: '#modal_new_product',
        LIST_PRODUCT : '#eventLabel'
    };
    $("#id_btn_new").click(function(){
    
        OpenModal(0);

    });

    

    var SELECT_ITEM_PRODUCT = document.querySelector(Selectors.LIST_PRODUCT);
    if(SELECT_ITEM_PRODUCT) {
        const choices = new Choices(SELECT_ITEM_PRODUCT); 
    }
    var vTableArticulos= $('#tbl_productos').DataTable({
        "destroy": true,
        "info": false,
        "bPaginate": true,
        "order": [
            [0, "asc"]
        ],
        "lengthMenu": [
            [10, -1],
            [10, "Todo"]
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

    $("#tbl_productos_length").hide();
    $("#tbl_productos_filter").hide();
    $("#tbl_productos_paginate").hide();

    var vTableClientes= $('#tbl_clientes').DataTable({
        "destroy": true,
        "info": false,
        "bPaginate": true,
        "order": [
            [0, "asc"]
        ],
        "lengthMenu": [
            [10, -1],
            [10, "Todo"]
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
    
    $("#tbl_clientes_length").hide();
    $("#tbl_clientes_filter").hide();
    $("#tbl_clientes_paginate").hide();
    
    $('#id_txt_buscar').on('keyup', function() {        
        vTableArticulos.search(this.value).draw();
    });

    $('#id_txt_buscar_cliente').on('keyup', function() {        
        vTableClientes.search(this.value).draw();
    });

    $("#id_send_frm_produc").click(function(){

        var cod_sistema     = $("#id_articulo option:selected").val();   
        var descrip_corta   = $("#id_nombre_corto").val();   
        var descrip_larga   = $("#id_nombre_largo").val();
        var produc_type     = $("#id_tipo option:selected").val();  
        var modl_states     = $("#id_modal_state").text();  


        var cod_unidad      = $("#id_unidad_almacen option:selected").val();   
        var cod_labora      = $("#id_laboratorio option:selected").val();   
        var cod_provee      = $("#id_proveedor option:selected").val();   
        

        var ttModal = (modl_states=='0')? 'Creado.' : 'Actualizado.'

        descrip_corta   = isValue(descrip_corta,'N/D',true)
        descrip_larga   = isValue(descrip_larga,'N/D',true)
        produc_type     = isValue(produc_type,'N/D',true)

        if(descrip_corta === 'N/D' || descrip_larga ==='N/D' || produc_type ==='N/D'){
            Swal.fire("Oops", "Datos no Completos", "error");
        }else{

            $.ajax({
                url: "SaveProducto",
                type: 'post',
                data: {
                    cod_sistema     : cod_sistema,
                    descrip_corta   : descrip_corta,
                    descrip_larga   : descrip_larga,
                    produc_type     : produc_type,
                    idProducto      : modl_states,
                    cod_unidad      : cod_unidad,
                    cod_labora      : cod_labora,
                    cod_provee      : cod_provee,
                    _token  : "{{ csrf_token() }}" 
                },
                async: true,
                success: function(response) {
                    if(response.original){
                        Swal.fire({
                        title: 'Producto ' + ttModal,
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
                    Swal.fire("Oops", "No se ha podido guardar!", "error");
                }
            }).done(function(data) {
                //location.reload();
            });

        }

    });
    function OpenModal(Id){

        $("#id_nombre_corto").val("");   
        $("#id_nombre_largo").val("");   
        var id_vendor = Id

        if(Id!=0){
            id_vendor = Id
            $.ajax({
                url: "getOneProducto/" + Id,
                type: 'GET',
                async: true,
                success: function(obj_producto) {               
                    $("#id_nombre_corto").val(obj_producto[0].descripcion_corta);   
                    $("#id_nombre_largo").val(obj_producto[0].descripcion_larga);
                    $("#id_articulo").val(obj_producto[0].Articulo_exactus).change();;   
                    $("#id_tipo").val(obj_producto[0].id_type_product).change();

                    $("#id_unidad_almacen").val(obj_producto[0].Clasificacion_1).change();
                    $("#id_laboratorio").val(obj_producto[0].Clasificacion_2).change();
                    $("#id_proveedor").val(obj_producto[0].Clasificacion_3).change();
                },
                error: function(response) {
                }
            }).done(function(data) {
                //location.reload();
            });

        }

        var TABLE_SETTING = document.querySelector(Selectors.TABLE_SETTING);
        var modal = new window.bootstrap.Modal(TABLE_SETTING);
        modal.show();

        $("#id_modal_state").text(id_vendor);  

    }

    function RemoveProducto(id_producto){
        Swal.fire({
            title: '¿Estas Seguro de borrar el Producto?',
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
                    url: "DeleteProducto",
                    type: 'post',
                    data: {
                        id      : id_producto,
                        _token  : "{{ csrf_token() }}" 
                    },
                    async: true,
                    success: function(response) {
                        if(response.original){
                        Swal.fire({
                        title: 'Producto Borrado.',
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
</script>
