@extends('layouts.lyt_gumadesk')
@section('metodosjs')
@include('jsViews.js_home')
@endsection
@section('content')

<!-- ===============================================-->
<!--    Main Content-->
<!-- ===============================================-->
<main class="main" id="top">
    <div class="container-fluid" data-layout="container">        
        <div class="content">            

            @include('layouts.nav_importacion')
            @if (Session::get('rol') != '10')  
            <div class="col-md-12 col-xxl-12 mb-3">
              
              <div class="card h-100">
                
                <div class="card-header">
                  <div class="d-flex position-relative align-items-center">
                    <div class="flex-1">
                      <div class="d-flex flex-between-center">
                      <h6 class="mb-0">Mi progreso</h6>
                      <a class="btn btn-link btn-sm px-0 shadow-none" href="estadisticas">Ver más<span class="fas fa-arrow-right ms-1 fs--2"></span></a>
                  </div>
                    </div>
                  </div>
                 
                  
                </div> 
                     
                <div class="card-body p-0">
                  <div class="scrollbar-overlay pt-0 px-card ask-analytics">
                    <div class="row g-3 mb-3">

                      <div class="col-md-4 col-xxl-4">
                        <div class="border border-1 border-300 rounded-2 p-3 ask-analytics-item position-relative">
                    
                            <div class="col-auto">
                              <div class="row g-sm-4">
                                
                                <div class="col-12 col-sm-auto">
                                  <div class="d-flex align-items-center pe-4 border-sm-end border-200">
                                    <div class="icon-item icon-item-sm border rounded-3 shadow-none me-2"><span class="fab fa-font-awesome-flag text-primary"></span></div>
                                      <div class="flex-1">
                                        <h6 class="text-800 mb-0"id="id_Venta_Meta" >...</h6>
                                        <p class="mb-0 fs--2 text-500">Meta del mes.</p>
                                    </div>
                                  </div>
                                </div>

                                <div class="col-12 col-sm-auto">
                                  <div class="d-flex align-items-center pe-4 border-sm-end border-200">
                                    <div class="icon-item icon-item-sm border rounded-3 shadow-none me-2"><span class="fas fa-dollar-sign text-success"></span></div>
                                      <div class="flex-1">
                                        <h6 class="text-800 mb-0"id="id_Venta_Real" >...</h6>
                                        <p class="mb-0 fs--2 text-500">Venta del mes.</p>
                                    </div>
                                  </div>
                                </div>

                                <div class="col-12 col-sm-auto">
                                  <div class="d-flex align-items-center pe-0">
                                    <div class="icon-item icon-item-sm border rounded-3 shadow-none me-2"><span class="fas fa-chart-bar text-info"></span></div>
                                      <div class="flex-1">
                                        <h6 class="text-800 mb-0"id="id_Venta_Porc" >...</h6>
                                        <p class="mb-0 fs--2 text-500">% CUMPL.</p>
                                      </div>
                                  </div>
                                </div>
                        
                              </div>
                            </div>
                        </div>
                      </div>
                     
                      <div class="col-md-4 col-xxl-4">
                        <div class="border border-1 border-300 rounded-2 p-3 ask-analytics-item position-relative">
                      
                            <div class="col-auto">
                              <div class="row g-sm-4">

                                <div class="col-12 col-sm-auto">
                                  <div class="d-flex align-items-center pe-4 border-sm-end border-200">
                                    <div class="icon-item icon-item-sm border rounded-3 shadow-none me-2"><span class="fas fa-dollar-sign text-primary"></span></div>
                                      <div class="flex-1">
                                        <h6 class="text-800 mb-0"id="id_Venta_Actu" >...</h6>
                                        <p class="mb-0 fs--2 text-500">Ventas del Dia.</p>
                                    </div>
                                  </div>
                                </div>

                                <div class="col-12 col-sm-auto">
                                  <div class="d-flex align-items-center pe-4 border-sm-end border-200">
                                    <div class="icon-item icon-item-sm border rounded-3 shadow-none me-2"><span class="fas fa-dollar-sign text-success"></span></div>
                                      <div class="flex-1">
                                        <h6 class="text-800 mb-0" id="id_Venta_Week">...</h6>
                                        <p class="mb-0 fs--2 text-500" id="id_Venta_Week_Label">Ventas del 0 al 0 de --- </p>
                                    </div>
                                  </div>
                                </div>

                                <div class="col-12 col-sm-auto">
                                  <div class="d-flex align-items-center pe-0">
                                    <div class="icon-item icon-item-sm border rounded-3 shadow-none me-2"><span class="fas fa-chart-bar text-info"></span></div>
                                      <div class="flex-1">
                                        <h6 class="text-800 mb-0" id="id_Dias_porcent">...</h6>
                                        <p class="mb-0 fs--2 text-500" id="id_Venta_Week_Label"><span id="id_Dias_Habiles">0</span> de <span id="id_Dias_Facturados"> 0 </span> Dias Hábiles.</p>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                        </div>
                      </div>
                      <div class="col-md-4 col-xxl-4">
                        <div class="border border-1 border-300 rounded-2 p-3 ask-analytics-item position-relative">
                     
                            <div class="col-auto">
                              <div class="row g-sm-4">

                              
                                <div class="col-12 col-sm-auto">
                                  <div class="d-flex align-items-center pe-4 border-sm-end border-200">
                                    <div class="icon-item icon-item-sm border rounded-3 shadow-none me-2"><span class="fas fa-user-friends text-primary"></span></div>
                                      <div class="flex-1">
                                        <h6 class="text-800 mb-0"id="id_Cliente_Meta" >...</h6>
                                        <p class="mb-0 fs--2 text-500">Meta de Clientes.</p>
                                    </div>
                                  </div>
                                </div>

                                <div class="col-12 col-sm-auto">
                                  <div class="d-flex align-items-center pe-4 border-sm-end border-200">
                                    <div class="icon-item icon-item-sm border rounded-3 shadow-none me-2"><span class="fas fa-user-friends text-success"></span></div>
                                      <div class="flex-1">
                                        <h6 class="text-800 mb-0"id="id_Cliente_Real" >...</h6>
                                        <p class="mb-0 fs--2 text-500">Clientes Fact.</p>
                                    </div>
                                  </div>
                                </div>

                                <div class="col-12 col-sm-auto">
                                  <div class="d-flex align-items-center pe-0">
                                    <div class="icon-item icon-item-sm border rounded-3 shadow-none me-2"><span class="fas fa-chart-bar text-info"></span></div>
                                      <div class="flex-1">
                                        <h6 class="text-800 mb-0"id="id_Cliente_Porc" >...</h6>
                                        <p class="mb-0 fs--2 text-500">% Cumpl.</p>
                                    </div>
                                  </div>
                                </div>

                              </div>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
               
              </div>
             
            </div>            
            @endif
          

              <div class="card">
                <div class="card-header d-flex flex-between-center ps-0 py-0 border-bottom">
                  <ul class="nav nav-tabs border-0 flex-nowrap tab-active-caret" id="crm-revenue-chart-tab" role="tablist" data-tab-has-echarts="data-tab-has-echarts">
                  

                    @if (Session::get('rol') == '1' || Session::get('rol') == '2' || Session::get('rol') == '9' )
                      <li class="nav-item" role="presentation"><a class="nav-link py-3 mb-0 {{$Normal}}" id="crm-promocion-tab" data-bs-toggle="tab" href="#crm-promocion" role="tab" aria-controls="crm-promocion" aria-selected="false">Promoción</a></li>  
                      <li class="nav-item" role="presentation"><a class="nav-link py-3 mb-0" id="crm-pedido-tab" data-bs-toggle="tab" href="#crm-pedido" role="tab" aria-controls="crm-pedido" aria-selected="false">Pedidos</a></li>  
                      <li class="nav-item" role="presentation"><a class="nav-link py-3 mb-0" id="crm-revenue-tab" data-bs-toggle="tab" href="#crm-revenue" role="tab" aria-controls="crm-revenue" aria-selected="true">Inventario</a></li>
                      <li class="nav-item" role="presentation"><a class="nav-link py-3 mb-0" id="crm-profit-tab" data-bs-toggle="tab" href="#crm-profit" role="tab" aria-controls="crm-profit" aria-selected="false">Clientes</a></li>                    
                      <li class="nav-item" role="presentation"><a class="nav-link py-3 mb-0" id="crm-users-tab" data-bs-toggle="tab" href="#crm-users" role="tab" aria-controls="crm-users" aria-selected="false">Liquidacion 12 Meses</a></li>
                      <li class="nav-item" role="presentation"><a class="nav-link py-3 mb-0" id="crm-deals-tab" data-bs-toggle="tab" href="#crm-deals" role="tab" aria-controls="crm-deals" aria-selected="false">Liquidacion 6 Meses</a></li> 
                    @elseif (Session::get('rol') == '10')                    
                    <li class="nav-item" role="presentation"><a class="nav-link py-3 mb-0 {{$Normal}}" id="crm-revenue-tab" data-bs-toggle="tab" href="#crm-revenue" role="tab" aria-controls="crm-revenue" aria-selected="true">Inventario</a></li>
                    @endif
                    

                  </ul>
                  <div class="dropdown font-sans-serif btn-reveal-trigger">
                    <div id="id_loading" class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
                  </div>
                </div>
                
                <div class="card-body">
                  <div class="row g-1">                   
                    <div class="col-xxl-12">
                      <div class="tab-content">
                        
                        <div class="tab-pane {{$Normal}}" id="crm-revenue" role="tabpanel" aria-labelledby="crm-revenue-tab">
                          <div class="row flex-between-center mb-3 ">
                            <div class="col-4 col-sm-auto d-flex align-items-center">
                              <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="tbl_select_inventario">                                          
                                <option selected="" value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="-1">*</option>
                              </select>                      
                            </div>
                            <div class="col-8 col-sm-auto text-end ">
                              <div class="row g-3 needs-validation" >                              
                                <div class="col-md-auto">
                                  <div class="input-group" >
                                    <input class="form-control form-control-sm shadow-none search" type="search" placeholder="Buscar..." aria-label="search" id="tbl_buscar_inventario" />
                                    <div class="input-group-text bg-transparent">
                                        <span class="fa fa-search fs--1 text-600"></span>
                                    </div>
                                    <a href="{{ route('generarPDF')}}" class="btn btn-danger btn-sm" style="margin-left: 10px;"><i class="fa fa-file-pdf"></i></a>
                                    <a href="{{ route('generarExcel')}}" class="btn btn-success btn-sm" style="margin-left: 10px;"><i class="fa fa-file-excel"></i></a>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <table id="tbl_inventario" class="table table-striped overflow-hidden" style="width:100%"></table>
                        </div>

                        <div class="tab-pane" id="crm-profit" role="tabpanel" aria-labelledby="crm-profit-tab">
                          <div class="row flex-between-center mb-3 ">
                            <div class="col-4 col-sm-auto d-flex align-items-center">
                              <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="tbl_select_cliente">                                          
                                <option selected="" value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="-1">*</option>
                              </select>                      
                            </div>
                            <div class="col-8 col-sm-auto text-end ">
                              <div class="row g-3 needs-validation" >                              
                                <div class="col-md-auto">
                                  <div class="input-group" >
                                    <input class="form-control form-control-sm shadow-none search" type="search" placeholder="Buscar..." aria-label="search" id="tbl_buscar_cliente" />
                                    <div class="input-group-text bg-transparent">
                                        <span class="fa fa-search fs--1 text-600"></span>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <table id="tbl_mst_clientes" class="table table-striped overflow-hidden" style="width:100%"></table>
                        </div>

                        <div class="tab-pane {{$SAC}}" id="crm-promocion" role="tabpanel" aria-labelledby="crm-promocion-tab">
                          <div class="container-fluid" >
                          <div class="row">

                              @foreach($promocion as $promo)
                                  <div class="col-sm-4 col-xs-4" style=""><div class="card text-white bg-dark mb-5" style="width: 100%;">
                                    <div class="">
                                      <img class="card-img-top" src="{{$promo->image_url}}" alt="" height="220px" />
                                    </div>
                                    <div class="card-body" style="height:150px">
                                        <div class="col">
                                          <div class="d-flex">
                                            <div class="calendar me-2"><span class="calendar-month">{{ \Carbon\Carbon::parse($promo->fechaFinal)->format('M') }}</span><span class="calendar-day">{{ \Carbon\Carbon::parse($promo->fechaFinal)->format('d') }}</span></div>
                                              <div class="flex-1 ">
                                                <h5 class="text-primary"><strong>{{$promo->titulo}}</strong></h5>
                                                <h6 class="text-secondary "><strong>{{$promo->nombre}}</strong></h6><p></p>
                                                @if(@strlen($promo->descripcion) > 70)
                                                  <p>{{@substr($promo->descripcion,0,70)}} <a href="#!" onclick="promocionLeerMas('{{$promo->id}}')">... Leer Más</a></p>
                                                @else
                                                <p>{{$promo->descripcion}}</p>
                                                @endif
                                            </div>
                                          </div>
                                        </div>
                                      
                                    </div>
                                    <div class="card-footer">
                                    <div class="col mt-2">
                                          <div class="d-flex">
                                            <div class="calendar me-2"></div>
                                            <span class="fs-0 text-warning fw-semi-bold">{{ \Carbon\Carbon::parse($promo->fechaInicio)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($promo->fechaFinal)->format('d/m/Y') }}</span>
                                          </div> 
                                          </div>
                                    </div>

                                  </div>
                                  
                                  </div>
                              @endforeach
                          </div>
                          </div>
                        </div>

                        <div class="tab-pane " id="crm-pedido" role="tabpanel" aria-labelledby="crm-pedido-tab">
                          <div class="row flex-between-center">
                            <div class="col-4 col-sm-auto d-flex align-items-center">
                            <form class="row align-items-center g-3">
                              <div class="col-auto"><h6 class="text-700 mb-0"> </h6></div>
                              <div class="col-md-auto position-relative">
                                <span class="fas fa-calendar-alt text-primary position-absolute translate-middle-y ms-2 mt-3"> </span>
                                <input id="id_range_select" class="form-control form-control-sm datetimepicker ps-4" type="text" data-options='{"mode":"range","dateFormat":"Y-m-d","disableMobile":true}'/>
                              </div>
                            </form>                            
                            </div>
                            <div class="col-8 col-sm-auto text-end ">
                              <div class="row g-3 needs-validation" >
                              
                                <div class="col-md-auto">
                                  <div class="input-group" >
                                      <input class="form-control form-control-sm shadow-none search" type="search" placeholder="Buscar..." aria-label="search" id="id_txt_buscar" />
                                      <div class="input-group-text bg-transparent">
                                          <span class="fa fa-search fs--1 text-600"></span>
                                      </div>
                                      <div class="input-group-text bg-transparent" id="id_btn_new">
                                          <span class="fas fa-history fs--1 text-600"></span>
                                      </div>
                                    </div>
                                  </div>
                                  @if( Session::get('rol') == '1' || Session::get('rol') == '2')
                                <div class="col-md-auto">
                                  <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="id_select_sac">
                                      <option value="0" >Todas</option>                                      
                                      @foreach ($Lista_SAC as $vnd)
                                        <option value="{{$vnd->username}}">{{$vnd->nombre}}</option>
                                      @endforeach
                                  </select>
                                </div> 
                                @endif
                                <div class="col-md-auto">
                                  <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="id_select_status">
                                    <option value="-1">Todos</option>
                                    <option value="0">Pendientes</option>
                                    <option value="1">Procesado</option>
                                    <option value="2">Cancelado</option>
                                  </select>
                                </div>
                               
                                <div class="col-md-auto">
                                  <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="frm_lab_row">                                          
                                    <option selected="" value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="-1">*</option>
                                  </select>
                                </div> 
                              </div>
                            </div>
                          </div>
                          <table id="tbl_mst_pedido" class="table table-striped overflow-hidden" style="width:100%"></table>
                        </div>
                        
                        <div class="tab-pane" id="crm-users" role="tabpanel" aria-labelledby="crm-users-tab">
                            <div class="row flex-between-center mb-3 ">
                              <div class="col-4 col-sm-auto d-flex align-items-center">
                                <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="tbl_select_liq12">                                          
                                  <option selected="" value="5">5</option>
                                  <option value="10">10</option>
                                  <option value="20">20</option>
                                  <option value="-1">*</option>
                                </select>                      
                              </div>
                              <div class="col-8 col-sm-auto text-end ">
                                <div class="row g-3 needs-validation" >                              
                                  <div class="col-md-auto">
                                    <div class="input-group" >
                                      <input class="form-control form-control-sm shadow-none search" type="search" placeholder="Buscar..." aria-label="search" id="tbl_buscar_liq12" />
                                      <div class="input-group-text bg-transparent">
                                          <span class="fa fa-search fs--1 text-600"></span>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>                          
                            <table id="tbl_inventario_liq_12" class="table table-striped overflow-hidden" style="width:100%"></table>
                        </div>
                        
                        <div class="tab-pane" id="crm-deals" role="tabpanel" aria-labelledby="crm-deals-tab">
                          <div class="row flex-between-center mb-3 ">
                            <div class="col-4 col-sm-auto d-flex align-items-center">
                              <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="tbl_select_liq6">                                          
                                <option selected="" value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="-1">*</option>
                              </select>                      
                            </div>
                            <div class="col-8 col-sm-auto text-end ">
                              <div class="row g-3 needs-validation" >                              
                                <div class="col-md-auto">
                                  <div class="input-group" >
                                    <input class="form-control form-control-sm shadow-none search" type="search" placeholder="Buscar..." aria-label="search" id="tbl_buscar_liq6" />
                                    <div class="input-group-text bg-transparent">
                                        <span class="fa fa-search fs--1 text-600"></span>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <table id="tbl_inventario_liq_6" class="table table-striped overflow-hidden" style="width:100%"></table>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>
              </div>
        <!--OPEN MODALS -->
    
       
        <div class="modal fade" id="modal_info_inventario" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content border-0">
              <div class="position-absolute top-0 end-0 mt-3 me-3 z-index-1">
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal" aria-label="Close" ></button>
              </div>
              <div class="position-absolute top-0 end-0 mt-3 me-7 z-index-1">
                <div class="spinner-border" role="status" id="id_load_articulo"></div>
              </div>
              
                <div class="card-header bg-light">          
                    <div class="row justify-content-between">
                      <div class="col">
                        <div class="d-flex">
                          <div class="avatar avatar-2xl status-online">
                            <img class="rounded-circle" src="{{ asset('images/user/avatar-4.jpg') }}" alt="" />
                          </div>
                          <div class="flex-1 align-self-center ms-2">
                            <p class="mb-1 lh-1"> <a class="fw-semi-bold" href="#!" > <span id="id_nombre_articulo"> <span id=""></span> </span> </a> </p>
                            <p class="mb-0 fs--1"> <span id="id_codigo_articulo"></span>  &bull;  <span id="lbl_unidad"></span>  &bull; <span class="fas fa-globe-americas"></span></p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-body p-0">

                  
                  <div class="row g-3">
                <div class="col-12">
                  <div class="card h-100">
                    <div class="card-header">
                      <div class="row flex-between-center">
                        <div class="col-auto">
                          <h6 class="mb-2" id="id_reglas">Cargando ... </h6>
                        </div>
                        <div class="col-auto">
                          <div class="row g-sm-4">
                            <div class="col-12 col-sm-auto">
                              <div class="mb-3 pe-4 border-sm-end border-200">
                                <h6 class="fs--2 text-600 mb-1">FARMACIA</h6>
                                <div class="d-flex align-items-center">
                                  <h5 class="fs-0 text-900 mb-0 me-2" id="id_precio_farmacia">C$ 0.00</h5>
                                </div>
                              </div>
                            </div>
                            <div class="col-12 col-sm-auto">
                              <div class="mb-3 pe-4 border-sm-end border-200">
                                <h6 class="fs--2 text-600 mb-1">MAYORISTA</h6>
                                <div class="d-flex align-items-center">
                                  <h5 class="fs-0 text-900 mb-0 me-2" id="id_precio_mayorista">C$ 0.00</h5>
                                </div>
                              </div>
                            </div>

                            
                            @if (Session::get('rol') == '1' || Session::get('rol') == '2' || Session::get('rol') == '9' )
                            <div class="col-12 col-sm-auto">
                              <div class="mb-3 pe-4 border-sm-end border-200">
                                <h6 class="fs--2 text-600 mb-1">INS. PUB</h6>
                                <div class="d-flex align-items-center">
                                  <h5 class="fs-0 text-900 mb-0 me-2" id="id_precio_inst_pub">C$ 0.00</h5>
                                </div>
                              </div>
                            </div>
                            @endif
                            <div class="col-12 col-sm-auto">
                              <div class="mb-3 pe-0">
                                <h6 class="fs--2 text-600 mb-1">PUBLICO</h6>
                                <div class="d-flex align-items-center">
                                  <h5 class="fs-0 text-900 mb-0 me-2" id="id_precio_mayortista">C$ 0.00</h5>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-header">                    
                      <div class="row flex-between-center">                        
                        <div class="col-md-12 col-xxl-12">           
                          <div class="card h-100">                           
                            <div class="card-body">                              
                              <table id="tbl_bodegas" class="table table-striped overflow-hidden" style="width:100%"></table>
                            </div>
                          </div>
                        </div>
                        
                      </div>
                    </div>
                   
                  </div>
                </div>
              
              </div>
                </div>
              </div>
            </div>
          </div>
        </div>
          


        <div class="modal fade" id="modal_info_cliente" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content border-0">
              <div class="position-absolute top-0 end-0 mt-3 me-3 z-index-1">
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="position-absolute top-0 end-0 mt-3 me-7 z-index-1">
                <div class="spinner-border" role="status" id="id_load_cliente"></div>
              </div>
              <div class="card-header bg-light">          
                  <div class="row justify-content-between">
                    <div class="col">
                      <div class="d-flex">
                        <div class="avatar avatar-2xl status-online">
                          <img class="rounded-circle" src="{{ asset('images/user/avatar-4.jpg') }}" alt="" />

                        </div>
                        <div class="flex-1 align-self-center ms-2">
                          <p class="mb-1 lh-1"><a class="fw-semi-bold" href="#!" > <span id="lbl_nombre_cliente"> Cargando... </span> </a> Rutas: <a href="#!"> <span id="lbl_rutas"></span> </a> </p>
                          <p class="mb-0 fs--1"> <span id="lbl_codigo"></span>  &bull;  <span id="lbl_last_sale"></span>  &bull; Ultim Factura &bull; <span class="fas fa-globe-americas"></span></p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <div class="modal-body p-0">
                <div class="p-4">
                  <div class="row">
                    <div class="col-lg-9">
                      <div class="d-flex">
                        <div class="flex-1">
                          <div class="row mb-2">
                            <div class="col-4 border-end border-200">
                              <h4 class="mb-0"> <span id="lbl_limite"></span> </h4>
                              <p class="fs--1 text-600 mb-0">Limite de Credito</p>
                            </div>
                            <div class="col-4 border-end text-center border-200">
                              <h4 class="mb-0"> <span id="lbl_saldo"></span></h4>
                              <p class="fs--1 text-600 mb-0">Saldo</p>
                            </div>
                            <div class="col-4 text-center">
                              <h4 class="mb-0"> <span id="lbl_disponible"></span> </h4>
                              <p class="fs--1 text-600 mb-0">Disponible</p>
                            </div>
                          </div>
                          <hr class="my-4" />
                        </div>
                      </div>
                      <div class="card">
                <div class="card-header d-flex flex-between-center ps-0 py-0 border-bottom">
                  <ul class="nav nav-tabs border-0 flex-nowrap tab-active-caret" id="crm-revenue-chart-tab" role="tablist" data-tab-has-echarts="data-tab-has-echarts">
                    <li class="nav-item" role="presentation"><a class="nav-link py-3 mb-0 active" id="historico-factura-tab" data-bs-toggle="tab" href="#historico-factura" role="tab" aria-controls="historico-factura" aria-selected="true">Historico de Factura</a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link py-3 mb-0" id="Ultm-3meses-tab" data-bs-toggle="tab" href="#Ultm-3meses" role="tab" aria-controls="Ultm-3meses" aria-selected="false">Ultm. 3 Meses</a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link py-3 mb-0" id="art-no-fact-tab" data-bs-toggle="tab" href="#art-no-fact" role="tab" aria-controls="art-no-fact" aria-selected="false">Art. No Facturados</a></li>
                    @if (Session::get('rol') == '1'  )
                      <li class="nav-item" role="presentation"><a class="nav-link py-3 mb-0" id="plan-creci-tab" data-bs-toggle="tab" href="#plan-creci" role="tab" aria-controls="plan-creci" aria-selected="false">Plan de Crecimiento</a></li>       
                    @endif
                  </ul>
                 
                </div>
                <div class="card-body">
                  <div class="row g-1">                    
                    <div class="col-xxl-12">
                      <div class="tab-content">
                        <div class="tab-pane active" id="historico-factura" role="tabpanel" aria-labelledby="historico-factura-tab">
                        <div class="row flex-between-center mb-3 ">
                              <div class="col-4 col-sm-auto d-flex align-items-center">
                                <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="tbl_select_history_factura">                                          
                                  <option selected="" value="5">5</option>
                                  <option value="10">10</option>
                                  <option value="20">20</option>
                                  <option value="-1">*</option>
                                </select>                      
                              </div>
                              <div class="col-8 col-sm-auto text-end ">
                                <div class="row g-3 needs-validation" >                              
                                  <div class="col-md-auto">
                                    <div class="input-group" >
                                      <input class="form-control form-control-sm shadow-none search" type="search" placeholder="Buscar..." aria-label="search" id="tbl_buscar_history_factura" />
                                      <div class="input-group-text bg-transparent">
                                          <span class="fa fa-search fs--1 text-600"></span>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          <table id="tbl_historico_factura" class="table table-striped overflow-hidden" style="width:100%"></table>
                        </div>
                        <div class="tab-pane" id="Ultm-3meses" role="tabpanel" aria-labelledby="Ultm-3meses-tab">
                        <div class="row flex-between-center mb-3 ">
                              <div class="col-4 col-sm-auto d-flex align-items-center">
                                <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="tbl_select_last3m">                                          
                                  <option selected="" value="5">5</option>
                                  <option value="10">10</option>
                                  <option value="20">20</option>
                                  <option value="-1">*</option>
                                </select>                      
                              </div>
                              <div class="col-8 col-sm-auto text-end ">
                                <div class="row g-3 needs-validation" >                              
                                  <div class="col-md-auto">
                                    <div class="input-group" >
                                      <input class="form-control form-control-sm shadow-none search" type="search" placeholder="Buscar..." aria-label="search" id="tbl_buscar_last3m" />
                                      <div class="input-group-text bg-transparent">
                                          <span class="fa fa-search fs--1 text-600"></span>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          <table id="tbl_ultm_3m" class="table table-striped overflow-hidden" style="width:100%"></table>

                        </div>
                        <div class="tab-pane" id="art-no-fact" role="tabpanel" aria-labelledby="art-no-fact-tab">
                        <div class="row flex-between-center mb-3 ">
                              <div class="col-4 col-sm-auto d-flex align-items-center">
                                <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="tbl_select_nofacturado">                                          
                                  <option selected="" value="5">5</option>
                                  <option value="10">10</option>
                                  <option value="20">20</option>
                                  <option value="-1">*</option>
                                </select>                      
                              </div>
                              <div class="col-8 col-sm-auto text-end ">
                                <div class="row g-3 needs-validation" >                              
                                  <div class="col-md-auto">
                                    <div class="input-group" >
                                      <input class="form-control form-control-sm shadow-none search" type="search" placeholder="Buscar..." aria-label="search" id="tbl_search_nofacturado" />
                                      <div class="input-group-text bg-transparent">
                                          <span class="fa fa-search fs--1 text-600"></span>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          <table id="tbl_no_facturado" class="table table-striped overflow-hidden" style="width:100%"></table>
                        </div>
                        
                        <div class="tab-pane" id="plan-creci" role="tabpanel" aria-labelledby="art-no-fact-tab">
                          
                        <div class="row g-3 mb-3">
                            <div class="col-md-12 col-xxl-12">
                            <div class="d-flex flex-between-center ">
                              <div class="mb-0"><div class="form-check form-switch">
                                <input class="form-check-input" id="flexSwitchCheckDefault" type="checkbox"  oninput="AddPlan()" />
                                <label class="form-check-label" for="flexSwitchCheckDefault">Plan de Crecimiento</label>
                              </div></div>
                              <h4 class="py-1 fs--1 text-800 mb-0" id="id_ttmes">C$ 21,349.29 </h4>
                            </div>
                              

                              
                              
                              <div class="border border-1 border-300 rounded-2 p-3 ask-analytics-item position-relative">

                                  <div class="col-auto">
                                    <div class="row g-sm-4">
                                      
                                      <div class="col-12 col-sm-auto">
                                        <div class="d-flex align-items-center pe-4 border-sm-end border-200">
                                          <div class="icon-item icon-item-sm border rounded-3 shadow-none me-2"><span class="fas fa-dollar-sign text-primary"></span></div>
                                            <div class="flex-1">
                                              <h6 class="text-800 mb-0"id="id_monto_base" >C$ 0.00</h6>
                                              <p class="mb-0 fs--2 text-500">Monto Base</p>
                                          </div>
                                        </div>
                                      </div>

                                      <div class="col-12 col-sm-auto">
                                        <div class="d-flex align-items-center pe-4 border-sm-end border-200">
                                          <div class="icon-item icon-item-sm border rounded-3 shadow-none me-2"><span class="fas fa-dollar-sign text-success"></span></div>
                                            <div class="flex-1">
                                              <h6 class="text-800 mb-0"id="id_crecimiento_esperado" >C$ 0.00</h6>
                                              <p class="mb-0 fs--2 text-500">Crecimiento (% 25)</p>
                                          </div>
                                        </div>
                                      </div>

                                      <div class="col-12 col-sm-auto">
                                        <div class="d-flex align-items-center pe-4 border-sm-end border-200">
                                          <div class="icon-item icon-item-sm border rounded-3 shadow-none me-2"><span class="fas fa-dollar-sign text-success"></span></div>
                                            <div class="flex-1">
                                              <h6 class="text-800 mb-0"id="id_crecimiento_minimo" >C$ 0.00</h6>
                                              <p class="mb-0 fs--2 text-500">Compra Minima Mes.</p>
                                          </div>
                                        </div>
                                      </div>

                                      <div class="col-12 col-sm-auto">
                                        <div class="d-flex align-items-center pe-0">
                                          <div class="icon-item icon-item-sm border rounded-3 shadow-none me-2"><span class="fas fa-chart-bar text-info"></span></div>
                                            <div class="flex-1">
                                              <h6 class="text-800 mb-0"id="id_porcent_crecimientio" > -20 %</h6>
                                              <p class="mb-0 fs--2 text-500">% Crecimiento.</p>
                                            </div>
                                        </div>
                                      </div>
                              
                                    </div>
                                  </div>
                              </div>

                              <div class="echart-sale-VentasDelMes" data-echart-responsive="true" data-echart-tab="data-echart-tab" style="height:320px;"></div>
                            </div>                          
                          </div>
                        </div>

                        
                        
                      </div>
                    </div>
                  </div>
                </div>
              </div>
                    </div>
                    <div class="col-lg-3">
                      <h6 class="mt-5 mt-lg-0">Saldos en Mora</h6>
                      <div class="d-flex flex-between-center rounded-3 bg-light p-3 mb-2"><a href="#!">
                      <h6 class="mb-0"><span class="fas fa-circle fs--1 me-3 text-primary"></span>No Vencido</h6>
                    </a><a class="fs--2 text-600 mb-0" href="#!">C$  <span id="no_fact">0.00</span> </a></div>
                  <div class="d-flex flex-between-center rounded-3 bg-light p-3 mb-2"><a href="#!">
                      <h6 class="mb-0"><span class="fas fa-circle fs--1 me-3 text-warning"></span>30 Dias</h6>
                    </a><a class="fs--2 text-600 mb-0" href="#!">C$ <span id="30_dias">0.00</a></div>
                  <div class="d-flex flex-between-center rounded-3 bg-light p-3 mb-2"><a href="#!">
                      <h6 class="mb-0"><span class="fas fa-circle fs--1 me-3 text-secondary"></span>60 Dias</h6>
                    </a><a class="fs--2 text-600 mb-0" href="#!">C$ <span id="60_dias">0.00</a></div>
                  <div class="d-flex flex-between-center rounded-3 bg-light p-3"><a href="#!">
                      <h6 class="mb-0"><span class="fas fa-circle fs--1 me-3 text-info"></span>90 Dias</h6>
                    </a><a class="fs--2 text-600 mb-0" href="#!">C$ <span id="90_dias">0.00</a></div>
                    <div class="d-flex flex-between-center rounded-3 bg-light p-3 mb-2"><a href="#!">
                      <h6 class="mb-0"><span class="fas fa-circle fs--1 me-3 text-primary"></span>120 Dias</h6>
                    </a><a class="fs--2 text-600 mb-0" href="#!">C$ <span id="120_dias">0.00</a></div>
                  <div class="d-flex flex-between-center rounded-3 bg-light p-3 mb-2"><a href="#!">
                      <h6 class="mb-0"><span class="fas fa-circle fs--1 me-3 text-warning"></span>Mas 120 Dias</h6>
                    </a><a class="fs--2 text-600 mb-0" href="#!">C$ <span id="mas_120_dias">0.00</a></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="IdmdlComment" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">

            <div class="position-absolute top-0 end-0 mt-3 me-3 z-index-1">
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="card-header bg-light">
                <div class="row justify-content-between">
                  <div class="col">
                    <div class="d-flex">
                      <div class="avatar avatar-2xl">
                        <img class="rounded-circle" src="{{ asset('images/item.png') }}" alt="" />

                      </div>
                      <div class="flex-1 align-self-center ms-2">
                        <p class="mb-1 lh-1"><a class="fw-semi-bold" href="!#" id="id_modal_name_item" >Nombre Item</a></p>
                        <p class="mb-0 fs--1"><span id="id_modal_articulo"></span> </p>
                        <p class="mb-0 fs--1">#<span id="id_modal_nSoli"></span> &bull; <span id="id_modal_Fecha"></span> <span class="fas fa-calendar"></span></p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="modal-body">
                <div class="mb-3">
                    <h6 class="fs-0 mb-0"> <span id="id_comentario_pedido"></span></h6>
                </div>
                <div class="d-flex align-items-center border-top border-200 pt-3">

                
                  <div class="avatar avatar-xl">
                    <img class="rounded-circle" src="{{ asset('images/user/avatar-4.jpg') }}" alt="" />
                  </div>
                  <input class="form-control rounded-pill ms-2 fs--1" type="text" placeholder="Escribe un Comentario..." id="id_textarea_comment" />
                </div>
        

                <div class="d-flex mt-3">
                 
                
                <div id="id_comment_item"></div>
                
              </div>

            </div>
            
          </div>
        </div>
        
        <!--CLOSE MODALS -->
    </div>
</main>
@endsection('content')