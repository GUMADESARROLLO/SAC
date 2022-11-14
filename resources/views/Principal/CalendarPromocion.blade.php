@extends('layouts.lyt_gumadesk')
@section('metodosjs')
@include('jsViews.js_Promociones');

@endsection
@section('content')
<!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">
      <div class="container" data-layout="container">
        <div class="content">
        @include('layouts.nav_importacion')
          <div class="card mb-3 overflow-hidden">
            <div class="card-header">
              <div class="row gx-0 align-items-center">
                <div class="col-auto d-flex justify-content-end order-md-1">
                  <button class="btn icon-item icon-item-sm shadow-none p-0 me-1 ms-md-2" type="button" data-event="prev" data-bs-toggle="tooltip" title="Previous"><span class="fas fa-arrow-left"></span></button>
                  <button class="btn icon-item icon-item-sm shadow-none p-0 me-1 me-lg-2" type="button" data-event="next" data-bs-toggle="tooltip" title="Next"><span class="fas fa-arrow-right"></span></button>
                </div>
                <div class="col-auto col-md-auto order-md-2">
                  <h4 class="mb-0 fs-0 fs-sm-1 fs-lg-2 calendar-title"></h4>
                </div>
                <div class="col col-md-auto d-flex justify-content-end order-md-3">
                  <button class="btn btn-falcon-primary btn-sm" type="button" data-event="today">Hoy</button>
                </div>
                <div class="col-md-auto d-md-none">
                  <hr />
                </div>
                <div class="col-auto d-flex order-md-0">
                  <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#addEventModal"> <span class="fas fa-plus me-2"></span>Agregar Promoción</button>
                </div>
                <div class="col d-flex justify-content-end order-md-2">
                  <div class="dropdown font-sans-serif me-md-2">
                    <button class="btn btn-falcon-default text-600 btn-sm dropdown-toggle dropdown-caret-none" type="button" id="email-filter" data-bs-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false"><span data-view-title="data-view-title">Mes</span><span class="fas fa-sort ms-2 fs--1"></span></button>
                    <div class="dropdown-menu dropdown-menu-end border py-2" aria-labelledby="email-filter"><a class="active dropdown-item d-flex justify-content-between" href="#!" data-fc-view="dayGridMonth">Mes<span class="icon-check"><span class="fas fa-check" data-fa-transform="down-4 shrink-4"></span></span></a><a class="dropdown-item d-flex justify-content-between" href="#!" data-fc-view="timeGridWeek">Semana<span class="icon-check"><span class="fas fa-check" data-fa-transform="down-4 shrink-4"></span></span></a><a class="dropdown-item d-flex justify-content-between" href="#!" data-fc-view="timeGridDay">Día<span class="icon-check"><span class="fas fa-check" data-fa-transform="down-4 shrink-4"></span></span></a><a class="dropdown-item d-flex justify-content-between" href="#!" data-fc-view="listWeek">Lista<span class="icon-check"><span class="fas fa-check" data-fa-transform="down-4 shrink-4"></span></span></a><a class="dropdown-item d-flex justify-content-between" href="#!" data-fc-view="listYear">Año<span class="icon-check"><span class="fas fa-check" data-fa-transform="down-4 shrink-4"></span></span></a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="calendar-outline" id="appCalendar"></div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="addEventModal" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content border">
              <form id="addEventForm" autocomplete="off">
                <div class="modal-header px-card bg-light border-bottom-0">
                  <h5 class="modal-title">Crear Promoción</h5>
                  <button class="btn-close me-n1" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-card">
                  <div class="mb-3">
                    <label class="fs-0" for="eventTitle">Titulo</label>
                    <input class="form-control" id="pTitulo" type="text" name="title" required="required" />
                  </div>
                  <div class="mb-3">
                    <label class="fs-0" for="eventStartDate">Fecha de Inicio</label>
                    <input class="form-control datetimepicker" id="pStartDate" type="text" required="required" name="startDate" placeholder="yyyy/mm/dd hh:mm" data-options='{"static":"true","enableTime":"true","dateFormat":"Y-m-d H:i"}' />
                  </div>
                  <div class="mb-3">
                    <label class="fs-0" for="eventEndDate">Fecha de Finalización</label>
                    <input class="form-control datetimepicker" id="pEndDate" type="text" name="endDate" placeholder="yyyy/mm/dd hh:mm" data-options='{"static":"true","enableTime":"true","dateFormat":"Y-m-d H:i"}' />
                  </div>
                  <div class="mb-3">
                    <label class="fs-0" for="eventDescription">Descripción</label>
                    <textarea class="form-control" rows="3" name="description" id="pDescription"></textarea>
                  </div>
                  <div class="mb-3">
                    <label class="fs-0" for="pArticulos">Articulo</label>
                    <select class="form-select" id="pArticulo" name="label">
                      <option value="" selected="selected">Seleccione</option>
                      @foreach ($articulos as $art)
                        <option value="{{$art->ARTICULO}}">{{strtoupper($art->DESCRIPCION)}}</option>
                      @endforeach
                      
                    </select>
                  </div>
                  <div class="form-group mb-3">
              
                    <div class="panel">SUBIR IMAGEN</div>

                    <input type="file" class="nuevaImagen" id="pImage" name="nuevaImagen">

                    <p class="help-block">Peso máximo de la imagen 2MB</p>

                    <!--<img src="vistas/img/productos/default/anonymous.png" class="img-thumbnail previsualizar" width="100px">-->

                  </div>
                </div>
                <div class="card-footer d-flex justify-content-end align-items-center bg-light">
                  <button class="btn btn-primary px-4" type="submit" id="save_promocion">Guardar</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </main>
    <!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->

@endsection('content')
