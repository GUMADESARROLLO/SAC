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
    

    var intVal = function ( i ) {
        return typeof i === 'string' ?
        i.replace(/[^0-9.]/g, '')*1 :
        typeof i === 'number' ?
        i : 0;
    };

    getDataCalendar()

    
    appCalendarInit(dta_calendar)
    
    
    //CargarDatos(nMes,annio);
    //table_render_excel(dta_table_excel)
    

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
  
    var Selectors = {
        ADD_NUEVA_SOLCITUD: '#addNuevaSolicitud',        
        ADD_MULTI_ROW: '#addMultiRow',
        MODAL_COMMENT: '#IdmdlComment',
    };

    
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
                ${event.extendedProps.articulotxt.split(' ').slice(0, 30).join(' ')}
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
                    ${ moment(event.start).locale('es').format("dddd, MMMM D, YYYY, h:mm A") } 
                    </p>
                </div>
            </div>
            <div class="d-flex mt-3">
                ${getStackIcon('fas fa-calendar-check')}
                <div class="flex-1">
                    <h6>Fecha Finalización</h6>
                    <p class="mb-1">
                    ${ moment(event.end).locale('es').format("dddd, MMMM D, YYYY, h:mm A") } 
                    </p>
                </div>
            </div>
        </div>
        </div>
        <div class="modal-footer d-flex justify-content-end bg-light px-card border-top-0">
        
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
                var modal = new window.bootstrap.Modal(eventDetailsModal);
                modal.show();

            },
            dateClick: function dateClick(info) {
                var month_ = moment().month(calendar.currentData.viewTitle).format("M");
                var year_ = moment().month(calendar.currentData.viewTitle).format("YYYY")

                getData(month_,year_)

                
                var modal = new window.bootstrap.Modal(addEventModal);
                modal.show();

                var flatpickr = document.querySelector(Selectors.EVENT_START_DATE)._flatpickr;

                flatpickr.setDate([info.dateStr]);
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
                articulo = _e$target.pArticulo,
                /*label = _e$target.label,
                
                allDay = _e$target.allDay;*/
                console.log(articulo.value)
                
                if(articulo.value != 0){
                    calendar.addEvent({
                        title: title.value,
                        start: startDate.value,
                        end: endDate.value,
                        className: 'bg-soft-success',
                        description: description.value,
                        articulotxt: articulo.value
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
    };
    
    $("#id_send_info").click(function(){

        var dtaId       = $("#id_row").text()
        var dtaFecha    = $("#eventStartDate").val()
        var txtCantidad = $("#id_txt_proyeccion_mensual").val()



        if(dtaFecha===''){
            Swal.fire(
                'Fecha',
                'No tiene Fecha seleccionada',
                'error'
            )
        }else if (txtCantidad===''){
            Swal.fire(
                'Proyeccion Mensual',
                'Ingrese el dato',
                'error'
            )
        }else{
            $.ajax({
                url: "guardar_solicitud",
                data: {
                    rowID       : dtaId,
                    fecha       : dtaFecha,
                    cantidad    : txtCantidad,
                    _token: "{{ csrf_token() }}" 
                },
                type: 'post',
                async: true,
                success: function(response) {
                    Swal.fire("Exito!", "Guardado exitosamente", "success");
                },
                error: function(response) {
                    Swal.fire("Oops", "No se ha podido guardar!", "error");
                }
            }).done(function(data) {
                CargarDatos(nMes,annio);
            });
        }


    });
     
    function getData(month_,year_){
        
        $.ajax({
            type: "GET",
            url: 'getDataPromocion/'+ month_ + "/" + year_, 
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

        //var mes   = $("#IdSelectMes option:selected").val();           
        //var annio  = $("#IdSelectAnnio option:selected").val()

        var mes = moment().format('M');
        var annio = moment().format('YYYY');

        dta_calendar = []

        //var SumCantidad = 0;

        $.ajax({
            type: "GET",
            url: 'getDataPromocion/' + mes + "/"+annio, 
            async: false,
            dataType: "json",
            success: function(data){
                $.each(data,function(key, registro) {
                    
                    /*$color = "";
                    $d = Date.now;
                    if(registro.end_at < $d){
                        $color = 'bg-soft-danger'
                    }else{
                        $color = 'bg-soft-success'
                    }*/

                    dta_calendar.push(
                        {
                            'Id_evnt'       : registro.id,
                            'title'         : registro.titulo,
                            'start'         : registro.created_at,
                            'end'           : registro.end_at,
                            'description'   : registro.descripcion,
                            'articulotxt'    : registro.articulotxt,
                            'className'     : 'bg-soft-success'
                        }
                    )
                    //SumCantidad += parseFloat(registro.Cantidad)
                }); 	 
                
                //$("#id_sum_mes").html(numeral(SumCantidad).format('0,00.00'))
            },
            error: function(data) {
                //alert('error');
            }
        });
        return dta_calendar;
        
    }

    $("#save_promocion").click(function(){
        
        
        var var_articulo   = $("#pArticulo option:selected").val();     
        var var_articulo_txt   = $("#pArticulo option:selected").text();     
        var txtTitulo = $("#pTitulo").val()
        var dtaFechaIni    = $("#pStartDate").val()
        var dtaFechaFin    = $("#pEndDate").val()
        var pDescripcion       = $("#pDescription").val()
        var nameImg = $('#pImage').text()


        if (txtTitulo==='' || var_articulo==='0'){
            Swal.fire(
                'Valor de Ingreso',
                'Rellene todos los campos',
                'error'
            )
        }else{
            $.ajax({
                url: "insert_promocion",
                type: 'get',
                data: {
                    Articulo       : var_articulo,                    
                    Articulo_Txt       : var_articulo_txt,
                    Titulo    : txtTitulo,
                    fecha       : dtaFechaIni,
                    dtaFechaFin : dtaFechaFin,
                    Descripcion    : pDescripcion
                },
                async: true,
                success: function(response) {
                    Swal.fire("Exito!", "Guardado exitosamente", "success");
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
