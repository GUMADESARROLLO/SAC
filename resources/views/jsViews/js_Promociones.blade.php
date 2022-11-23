<script type="text/javascript">
    var dta_calendar = []  
    var table = ''
    var nMes   = $("#IdSelectMes option:selected").val();           
    var annio  = $("#IdSelectAnnio option:selected").val()
    moment.lang('es', {
            months: 'Enero_Febrero_Marzo_Abril_Mayo_Junio_Julio_Agosto_Septiembre_Octubre_Noviembre_Diciembre'.split('_'),
            monthsShort: 'Ene._Feb._Mar_Abr._May_Jun_Jul._Ago_Sept._Oct._Nov._Dic.'.split('_'),
            weekdays: 'Domingo_Lunes_Martes_Miercoles_Jueves_Viernes_Sabado'.split('_'),
            weekdaysShort: 'Dom._Lun._Mar._Mier._Jue._Vier._Sab.'.split('_'),
            weekdaysMin: 'Do_Lu_Ma_Mi_Ju_Vi_Sa'.split('_')
        }
    );
    
   getDataCalendar()

    
    appCalendarInit(dta_calendar)
    
    function soloNumeros(caracter, e, numeroVal) {
        var numero = numeroVal;
        if (String.fromCharCode(caracter) === "." && numero.length === 0) {
            e.preventDefault();
            swal.showValidationError('No se puede iniciar con un punto');
        } else if (numero.includes(".") && String.fromCharCode(caracter) === ".") {
            e.preventDefault();
            swal.showValidationError('No puede haber mas de dos puntos');
        } else {
            const soloNumeros = new RegExp("^[0-9]+$");
            if (!soloNumeros.test(String.fromCharCode(caracter)) && !(String.fromCharCode(caracter) === ".")) {
                e.preventDefault();
                swal.showValidationError(
                    'No se pueden escribir letras, solo se permiten datos númericos'
                );
            }
        }
    }
  
    /*-----------------------------------------------
    |   Calendar
    -----------------------------------------------*/
    function getStackIcon(icon, transform) {
        return `<span class="fa-stack ms-n1 me-3">
                    <i class="fas fa-circle fa-stack-2x text-200"></i>
                    <i class="${icon} fa-stack-1x text-primary" data-fa-transform=${transform}></i>
                </span>
                `;
        
    };
    function getTemplate(event) {
        edit_promo(event);
        return `
        <div class="modal-header bg-light ps-card pe-5 border-bottom-0">
        <div>
            <h5 class="modal-title mb-0">${event.title}</h5>
        </div>
        <button type="button" class="btn-close position-absolute end-0 top-0 mt-3 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body px-card pb-card pt-1 fs--1">
        <div class="d-flex mt-3">
                ${getStackIcon('fas fa-archive')}
                <div class="flex-1">
                <h6>Articulo</h6>
                <p class="mb-0">
                ${event.extendedProps.nombre.split(' ').slice(0, 30).join(' ')}
                </p>
                </div>
            </div>
            <div class="d-flex mt-3">
                ${getStackIcon('fas fa-align-left')}
                <div class="flex-1">
                <h6>Descripción</h6>
                <p class="mb-0">
                ${event.extendedProps.description.split(' ').slice(0, 30).join(' ')}
                </p>
                </div>
            </div>
            <div class="d-flex mt-3">
                ${getStackIcon('fas fa-calendar-check')}
                <div class="flex-1">
                    <h6>Fecha Inicio</h6>
                    <p class="mb-1">
                    ${ moment(event.start).locale('es').format("dddd, MMMM D, YYYY") } 
                    </p>
                </div>
            </div>
            <div class="d-flex mt-3">
                ${getStackIcon('fas fa-calendar-check')}
                <div class="flex-1">
                    <h6>Fecha Finalización</h6>
                    <p class="mb-1">
                    ${ moment(event.end).locale('es').format("dddd, MMMM D, YYYY") } 
                    </p>
                </div>
            </div>
        </div>
        </div>
        <div class="modal-footer d-flex justify-content-end bg-light px-card border-top-0">
        <h5 class="mb-1 fw-semi-bold text-nowrap"><a href="#!" onclick=" desactivarPromo(${event.extendedProps.Id_evnt},'${event.extendedProps.activo}')"> <strong class="activo">Reactivar</strong> </a></h5>
        <button class="btn btn-primary btn-sm" type="submit" data-bs-toggle="modal" data-bs-target="#editEventModal"> <span class="fas fa-pencil-alt me-2"></span>Editar</button>
        </div>
        `;
    };

    function appCalendarInit(events) {
        var Selectors = {
            ACTIVE: '.active',
            ADD_EVENT_FORM: '#addEventForm',
            ADD_EVENT_MODAL: '#addEventModal',
            CALENDAR: '#appCalendar',
            CALENDAR_TITLE: '.calendar-title',
            DATA_CALENDAR_VIEW: '[data-fc-view]',
            DATA_EVENT: '[data-event]',
            DATA_VIEW_TITLE: '[data-view-title]',
            EVENT_DETAILS_MODAL: '#eventDetailsModal',
            EVENT_DETAILS_MODAL_CONTENT: '#eventDetailsModal .modal-content',
            EVENT_START_DATE: '#addEventModal [name="startDate"]',
            EVENT_END_DATE: '#addEventModal [name="endDate"]',
            INPUT_TITLE: '[name="title"]'
        };

        var _window3 = window,dayjs = _window3.dayjs;
        var currentDay = dayjs && dayjs().format('DD');
        var currentMonth = dayjs && dayjs().format('MM');
        var prevMonth = dayjs && dayjs().subtract(1, 'month').format('MM');
        var nextMonth = dayjs && dayjs().add(1, 'month').format('MM');
        var currentYear = dayjs && dayjs().format('YYYY');

        

       
        var Events = {
            CLICK: 'click',
            SHOWN_BS_MODAL: 'shown.bs.modal',
            SUBMIT: 'submit'
        };
        var DataKeys = {
            EVENT: 'event',
            FC_VIEW: 'fc-view'
        };
        var ClassNames = {
            ACTIVE: 'active'
        };
        var eventList = events.reduce(function (acc, val) {
            return val.schedules ? acc.concat(val.schedules.concat(val)) : acc.concat(val);
        }, []);

        var updateTitle = function updateTitle(title) {



            document.querySelector(Selectors.CALENDAR_TITLE).textContent = title;

            
            /*var month_ = moment().month(title).format("M");
            var year_ = moment().month(title).format("YYYY");
            
            getDataCalendar(month_,year_)*/
            
        };

        var appCalendar = document.querySelector(Selectors.CALENDAR);
        var addEventForm = document.querySelector(Selectors.ADD_EVENT_FORM);
        var addEventModal = document.querySelector(Selectors.ADD_EVENT_MODAL);
        var eventDetailsModal = document.querySelector(Selectors.EVENT_DETAILS_MODAL);

        if (appCalendar) {
            var calendar = renderCalendar(appCalendar, {
            headerToolbar: false,
            dayMaxEvents: 2,
            height: 800,
            locale: 'es',
            stickyHeaderDates: false,
            views: {
                week: {
                eventLimit: 3
                }
            },
            eventTimeFormat: {
                hour: 'numeric',
                minute: '2-digit',
                omitZeroMinute: true,
                meridiem: true
            },
            events: eventList,
            eventClick: function eventClick(info) {
                var template = getTemplate(info.event);
                document.querySelector(Selectors.EVENT_DETAILS_MODAL_CONTENT).innerHTML = template;
                if(info.event.extendedProps.activo == "S"){
                    $(".activo").text('Desactivar');
                }
                var modal = new window.bootstrap.Modal(eventDetailsModal);
                modal.show();

            },
            dateClick: function dateClick(info) {
                var day_ = new Date(info.dateStr);
               
                var modal = new window.bootstrap.Modal(addEventModal);
                modal.show();

                var flatpickr = document.querySelector(Selectors.EVENT_START_DATE)._flatpickr;
                var flatpickr2 = document.querySelector(Selectors.EVENT_END_DATE)._flatpickr;

                flatpickr.setDate([info.dateStr]);
                flatpickr2.setDate([day_.getFullYear()+'-'+(day_.getMonth()+1)+'-'+(day_.getDate()+1)+' 23:00']);
                $(".previsualizar").attr("src", "{{ asset('images/promocion/item.jpg') }}");

            }
            });

            updateTitle(calendar.currentData.viewTitle);
            document.querySelectorAll(Selectors.DATA_EVENT).forEach(function (button) {
            button.addEventListener(Events.CLICK, function (e) {
                var el = e.currentTarget;
                var type = utils.getData(el, DataKeys.EVENT);

                switch (type) {
                case 'prev':
                    calendar.prev();
                    updateTitle(calendar.currentData.viewTitle);
                    break;

                case 'next':
                    calendar.next();
                    updateTitle(calendar.currentData.viewTitle);
                    break;

                case 'today':
                    calendar.today();
                    updateTitle(calendar.currentData.viewTitle);
                    break;

                default:
                    calendar.today();
                    updateTitle(calendar.currentData.viewTitle);
                    break;
                }
            });
            });

            addEventForm && addEventForm.addEventListener(Events.SUBMIT, function (e) {
            e.preventDefault();
            var _e$target = e.target,
                title = _e$target.pTitulo,
                startDate = _e$target.pStartDate,
                description = _e$target.pDescription;
                endDate = _e$target.pEndDate,
                articulo = _e$target.pArticulo

                arti = explode("!", articulo.value)
                /*label = _e$target.label,
                
                allDay = _e$target.allDay;*/
                
                if(articulo.value != 0){
                    calendar.addEvent({
                        title: title.value,
                        start: startDate.value,
                        end: endDate.value,
                        className: 'bg-soft-success',
                        description: description.value,
                        nombre: arti[1]
                    });
                    e.target.reset();
                    window.bootstrap.Modal.getInstance(addEventModal).hide();
                }
            
            });
            
        }

        addEventModal && addEventModal.addEventListener(Events.SHOWN_BS_MODAL, function (_ref13) {
            var currentTarget = _ref13.currentTarget;
            currentTarget.querySelector(Selectors.INPUT_TITLE).focus();
        });
    }
         
    function getData(month_,year_){
        
        $.ajax({
            type: "GET",
            url: 'getDataPromocion/'+ year_, 
            async: false,
            dataType: "json",
            success: function(data){
                //$("#eArticulos").empty().append('<option value="0"> -- SIN RESULTADO--</option>')
                $.each(data,function(key, registro) {
                    $("#eArticulos").append('<option value='+registro.id+'>'+registro.articulo + ' | ' + registro.articulotxt+'</option>');
                }); 	 
                
            },
            error: function(data) {
                //alert('error');
            }
        });
    }

    function getDataCalendar(){
        var annio = moment().format('M');
        dta_calendar = []

        $.ajax({
            type: "GET",
            url: 'getDataPromocion/'+ annio, 
            async: false,
            dataType: "json",
            success: function(data){
                $.each(data,function(key, registro) {
                    
                    var color = "";
                    if(registro.activo ==  "N"){
                        color = 'bg-soft-danger';
                    }else{
                        color = 'bg-soft-success';
                    }

                    dta_calendar.push(
                        {
                            'Id_evnt'       : registro.id,
                            'title'         : registro.titulo,
                            'start'         : registro.fechaInicio,
                            'end'           : registro.fechaFinal,
                            'description'   : registro.descripcion,
                            'nombre'        : registro.nombre,
                            'articulo'      : registro.articulo,
                            'image'         : registro.image,
                            'activo'        : registro.activo,
                            'className'     : color
                        }
                    )
                }); 	 
                
            },
            error: function(data) {
                //alert('error');
            }
        });
        return dta_calendar;
        
    }

    function edit_promo(data){

        $('#ePTitulo').val(data.title);
        $('#idPromocion').val(data.extendedProps.Id_evnt);
        
        var flatpickr = document.querySelector('#editEventModal [name="eStartDate"]')._flatpickr;
        var flatpickr2 = document.querySelector('#editEventModal [name="eEndDate"]')._flatpickr;

        flatpickr.setDate([moment(data.start).format("Y-M-D H:i")]);
        flatpickr2.setDate([moment(data.end).format("Y-M-D H:i")]);

        $('#ePDescription').val(data.extendedProps.description.split(' ').slice(0, 30).join(' '));
        $("#ePArticulo option:contains("+data.extendedProps.nombre.split(' ').slice(0, 30).join(' ')+")").attr("selected",'');
        $('#fotoActual').val(data.extendedProps.image);

        if(data.extendedProps.image != "item.jpg"){
            $ruta ="{{ asset('images/promocion/') }}/"+data.extendedProps.image;
            $(".previsualizar").attr("src", $ruta);
        }else{
            $(".previsualizar").attr("src", "{{ asset('images/promocion/item.jpg') }}");
        }
    }

    $(".nuevaFoto").change(function(){

        var imagen = this.files[0];

        var datosImagen = new FileReader;
        datosImagen.readAsDataURL(imagen);

        $(datosImagen).on("load", function(event){

            var rutaImagen = event.target.result;

            $(".previsualizar").attr("src", rutaImagen);

        })

    })

    function desactivarPromo(id, activo){
        Swal.fire({
        title: '¡El estado de la promoción sera cambiado!',
        text: "¿Desea continuar con esta acción?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si!',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            $.ajax({
                url: "editPromocion/"+id+"/"+activo,
                type: 'GET',
                async: true,
                success: function(response) {
                    if('ok' == 'ok'){
                    Swal.fire({
                    title: 'Estado Modificado',
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

    /*$('#addEventModal').on('hidden.bs.modal', function(e){
        $('#addEventModal input').val("");
        $('#addEventModal textarea').val("");
        $('#addEventModal select').val("");
    })*/

  
</script>
