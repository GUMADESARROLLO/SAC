<script type="text/javascript">
    var Selectors = {
        TABLE_SETTING: '#modal_new_product',
    };
    var vTableUsuario = $('#tbl_usuarios').DataTable({
            "destroy": true,
            "info": false,
            "bPaginate": false,
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
            }
        });
    $("#tbl_usuarios_filter").hide();
    $('#id_txt_buscar_user').on('keyup', function() {     
        var vTableUsuario = $('#tbl_usuarios').DataTable(); 
        vTableUsuario.search(this.value).draw();
    });
    $("#id_btn_new").click(function(){

        $("#id_remover").hide();
        

        $("#id_nombre_usuario").val("");   
        $("#id_nombre_completo").val("");
        $("#id_password").val("");
        $("#id_modal_state").html("0");

        var TABLE_SETTING = document.querySelector(Selectors.TABLE_SETTING);
        var modal = new window.bootstrap.Modal(TABLE_SETTING);
        modal.show();
        
    });
    function OpenModal(Obj){
        $("#id_remover").show();
        


        $("#id_modal_state").html(Obj.id);

        $("#id_nombre_usuario").val(Obj.username);   
        $("#id_nombre_completo").val(Obj.nombre);
        $("#id_tipo_usuaro").val(Obj.id_rol).change();
        $("#id_password").val("");

        var TABLE_SETTING = document.querySelector(Selectors.TABLE_SETTING);
        var modal = new window.bootstrap.Modal(TABLE_SETTING);
        modal.show();

    }

    function AsginarRuta(Obj){

        var SelectData      = [];
        $.get( "getRutas", function( data ) {
            
            $.map(data,function(o) {
                SelectData[o.VENDEDOR] = o.VENDEDOR + ' | ' +o.NOMBRE;
            });


            Swal.fire({
                title: "seleccione la Ruta",          
                input: "select",
                inputOptions: SelectData,
                inputPlaceholder: 'N/D',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar',
                target:"",                
                showLoaderOnConfirm: true,
                preConfirm: (value) => {
                    if (!value) {
                        Swal.showValidationMessage("Debe Ingresar algo")
                    } else {
                        $.ajax({
                            url: "AsignarRuta",
                            type: 'post',
                            data:  {
                                valor   : value,
                                Id      : Obj.id,
                                _token  : "{{ csrf_token() }}" 
                            },
                            async: true,
                            success: function(response) {
                                if(response.original){
                                    Swal.fire({
                                        title: 'Se agrego el producto',
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
                            //CargarDatos(nMes,annio);
                        });
                    }
                },
                allowOutsideClick: () => !Swal.isLoading()
            });
        });

    }

    function Remover(Ruta){
        Swal.fire({
            title: '¿Estas Seguro de Remover la Ruta?',
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
                    url: "RemoverRutaAsignada",
                    type: 'post',
                    data: {
                        id      : Ruta.id,
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


    $("#id_remover").click(function(){
        var id_usuario      = $("#id_modal_state").text();
        Swal.fire({
            title: '¿Estas Seguro de borrar el Ususario?',
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
                    url: "DeleteUsuario",
                    type: 'post',
                    data: {
                        id      : id_usuario,
                        _token  : "{{ csrf_token() }}" 
                    },
                    async: true,
                    success: function(response) {
                        if(response.original){
                        Swal.fire({
                        title: 'Usuario Borrado.',
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
    })
    $("#id_send_frm_produc").click(function(){

        
        var usuario     = $("#id_nombre_usuario").val();   
        var nombre      = $("#id_nombre_completo").val();   
        var passwprd    = $("#id_password").val();
        var Estado      = $("#id_modal_state").text();
        var id_rol      = $("#id_tipo_usuaro option:selected").val();  

        usuario      = isValue(usuario,'N/D',true)
        nombre       = isValue(nombre,'N/D',true)

        if(usuario === 'N/D' || nombre ==='N/D'){
            Swal.fire("Oops", "Datos no Completos", "error");
        }else{

            $.ajax({
                url: "SaveUsuario",
                type: 'post',
                data: {
                    usuario     : usuario,
                    nombre      : nombre,
                    passwprd    : passwprd,
                    id_rol      : id_rol,
                    Estado      : Estado,
                    _token  : "{{ csrf_token() }}" 
                },
                async: true,
                success: function(response) {
                    if(response.original){
                        Swal.fire({
                        title: 'Correcto ',
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
</script>
