var pgurl = window.location.href.substr(window.location.href.lastIndexOf("/")+1).replace('#!', '', );
var band = false;

$("nav.pcoded-navbar div div ul li").each(function() {
    var link = $(this).find('a').attr('href');
    var sub_clase = '';

    if (typeof link !== "undefined") {
        const ruta = link.substring(link.lastIndexOf("/") + 1);

        if( ruta == pgurl ) {
            sub_clase = $(this).parent().parent().parent().parent().attr('class');

            if (sub_clase=='nav-item pcoded-hasmenu') {
                $(this).parent().parent().removeClass().addClass("nav-item pcoded-hasmenu active pcoded-trigger")
                $(this).parent().parent().parent().parent().removeClass().addClass("nav-item pcoded-hasmenu active pcoded-trigger")
            }else {
                sub_clase = $(this).parent().parent().attr('class');

                if (sub_clase=='nav-item pcoded-hasmenu') {
                   $(this).parent().parent().removeClass().addClass("nav-item pcoded-hasmenu active pcoded-trigger")
                }else {
                    sub_clase = $(this).attr('class');

                    if (sub_clase=='nav-item') {
                        $(this).removeClass().addClass("nav-item active")
                    }
                }
            }
            $(this).removeClass('text-secondary').addClass("active");
        }

    }

});

//METODO QUE PERMITE ENVIAR POR POST AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function mensaje(mensaje, tipo) {
    /*
    Tipos:
    success, error, warning, info, question
    +*/
    const toast = swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });

    toast({
      type: tipo,
      title: mensaje
    })
}

$("body").click( function(e) {
    if ( $("#sidebar").hasClass('active') || $(e.target).hasClass('active-menu') ) {
        $("#sidebar").toggleClass('active');
    }
});

// Sidebar toggle behavior
$('#sidebarCollapse').on('click', function() {
    $.removeCookie('navbar');
    if ( $("#sidebar-menu-left").hasClass('active') ) {
        $.cookie( 'navbar' , true)

    }else {
        $.cookie( 'navbar' , false)

    }
    $('#sidebar-menu-left, #content').toggleClass('active');


});

function fullScreen() {
    //SI ESTA EN UN TELEFONO
    if (($('header').width() <= 420 )) {
        $('#sidebar-menu-left, #content')
        .addClass('active')
        .removeClass('notactive');
    }

    if ( $.cookie('navbar')=='true' ) {
        $('#sidebar-menu-left, #content')
        .addClass('notactive')
        .removeClass('active');
    }else if( $.cookie('navbar')=='false'  ) {
        $('#sidebar-menu-left, #content')
        .addClass('active')
        .removeClass('notactive');
    }
}

function    inicializaControlFecha() {
    $('input[class="input-fecha form-control"]').daterangepicker({
        "locale": {
            "format": "YYYY-MM-DD",
            "separator": " - ",
            "applyLabel": "Apply",
            "cancelLabel": "Cancel",
            "fromLabel": "From",
            "toLabel": "To",
            "customRangeLabel": "Custom",
            "daysOfWeek": [
                "D",
                "L",
                "M",
                "M",
                "J",
                "V",
                "S"
            ],
            "monthNames": [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre"
            ],
            "firstDay": 0
        },
        singleDatePicker: true,
        showDropdowns: true
    });
}
