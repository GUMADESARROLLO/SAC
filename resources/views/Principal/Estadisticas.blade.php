@extends('layouts.lyt_gumadesk')
@section('metodosjs')
@include('jsViews.js_estadisticas');
@endsection
@section('content')

<!-- ===============================================-->
<!--    Main Content-->
<!-- ===============================================-->
<main class="main" id="top">
    <div class="container-fluid" data-layout="container">        
        <div class="content">            

            @include('layouts.nav_importacion')

            <div class="col-md-12 col-xxl-12 mb-3">
              <div class="card h-100">
                
                <div class="card-header">
                  <div class="d-flex position-relative align-items-center">
                    <div class="flex-1">
                      <div class="d-flex flex-between-center">
                      <h6 class="mb-0">Regresar</h6>
                    <div class="row flex-between-center">
                            <div class="col-4 col-sm-auto d-flex align-items-center">
                                                      
                            </div>
                            <div class="col-8 col-sm-auto text-end ">
                              <div class="row g-3 needs-validation" >
                              
                                <div class="col-md-auto">
                                  <div class="input-group" >
                                  <form class="row align-items-center g-3">
                                    <div class="col-auto"><h6 class="text-700 mb-0"> </h6></div>
                                    <div class="col-md-auto position-relative">                                      
                                      <input id="id_range_select" class="form-control form-control-sm datetimepicker" type="text" data-options='{"mode":"range","dateFormat":"Y-m-d","disableMobile":true}'/>
                                    </div>
                                  </form>  
                                      <div class="input-group-text bg-transparent">
                                          <span class="fa fa-calendar fs--1 text-600"></span>
                                      </div>
                                      <div class="input-group-text bg-transparent" id="id_btn_new">
                                          <span class="fas fa-history fs--1 text-600"></span>
                                      </div>
                                      
                                    </div>
                                  </div>
                                <div class="col-md-auto">
                                  <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="id_select_status">
                                    <option value="-1">Todas...</option>
                                    <option value="0">F00</option>
                                    <option value="1">F00</option>
                                    <option value="2">F00</option>
                                  </select>
                                </div>
                               
                              </div>
                            </div>
                          </div>
                  </div>
                    </div>
                  </div>
                 
                  
                </div>                
                <div class="card-body p-0">
                  <div class="scrollbar-overlay pt-0 px-card ask-analytics">
                    <div class="row g-3 mb-3">

                      <div class="col-md-4 col-xxl-4">
                        <div class="border border-1 border-300 rounded-2 p-3 ask-analytics-item position-relative mb-3">
                    
                            <div class="col-auto">
                              <div class="row g-sm-4">
                                
                                <div class="col-12 col-sm-auto">
                                  <div class=" pe-4 border-sm-end border-200">
                                    <h6 class="fs--2 text-600 mb-1">Meta del mes.</h6>
                                    <div class="d-flex align-items-center">
                                      <h5 class="fs-0 text-900 mb-0 me-2" id="">C$ 500,000.00</h5>
                                    </div>
                                  </div>
                                </div>

                                <div class="col-12 col-sm-auto">
                                  <div class="pe-4 border-sm-end border-200">
                                    <h6 class="fs--2 text-600 mb-1">Venta del mes</h6>
                                    <div class="d-flex align-items-center">
                                      <h5 class="fs-0 text-900 mb-0 me-2" id="">C$ 500,000.00</h5>
                                    </div>
                                  </div>
                                </div>

                            
                                <div class="col-12 col-sm-auto">
                                  <div class="pe-0">
                                    <h6 class="fs--2 text-600 mb-1">% CUMPL</h6>
                                    <div class="d-flex align-items-center">
                                      <h5 class="fs-0 text-900 mb-0 me-2" id="">% 100</h5>
                                    </div>
                                  </div>
                                </div>

                              </div>
                            </div>
                        </div>
                      </div>
                     
                      <div class="col-md-5 col-xxl-5">
                        <div class="border border-1 border-300 rounded-2 p-3 ask-analytics-item position-relative mb-3">
                      
                            <div class="col-auto">
                              <div class="row g-sm-4">
                                
                                <div class="col-12 col-sm-auto">
                                  <div class=" pe-4 border-sm-end border-200">
                                    <h6 class="fs--2 text-600 mb-1">Ventas del Dia.</h6>
                                    <div class="d-flex align-items-center">
                                      <h5 class="fs-0 text-900 mb-0 me-2" id="">C$ 206,000.00</h5>
                                    </div>
                                  </div>
                                </div>

                                <div class="col-12 col-sm-auto">
                                  <div class="pe-4 border-sm-end border-200">
                                    <h6 class="fs--2 text-600 mb-1">Ventas del 3 al 9 de octubre: </h6>
                                    <div class="d-flex align-items-center">
                                      <h5 class="fs-0 text-900 mb-0 me-2" id="">C$ 354,000.00</h5>
                                    </div>
                                  </div>
                                </div>

                            
                                <div class="col-12 col-sm-auto">
                                  <div class="pe-4 border-sm-end border-200">
                                    <h6 class="fs--2 text-600 mb-1">Dias Hábiles Fact.</h6>
                                    
                                    <div class="d-flex align-items-center">
                                      <h5 class="fs-0 text-900 mb-0 me-2" id="">7 <span class="badge badge-soft-info rounded-pill ms-2">0.0%</span></h5>
                                    </div>
                                  </div>
                                </div>

                                <div class="col-12 col-sm-auto">
                                  <div class="pe-0">
                                    <h6 class="fs--2 text-600 mb-1">Dias Hábiles</h6>
                                    <div class="d-flex align-items-center">
                                      <h5 class="fs-0 text-900 mb-0 me-2" id="">20</h5>
                                    </div>
                                  </div>
                                </div>

                              </div>
                            </div>
                        </div>
                      </div>
                      <div class="col-md-3 col-xxl-3">
                        <div class="border border-1 border-300 rounded-2 p-3 ask-analytics-item position-relative mb-3">
                     
                            <div class="col-auto">
                              <div class="row g-sm-4">
                                
                                <div class="col-12 col-sm-auto">
                                  <div class=" pe-4 border-sm-end border-200">
                                    <h6 class="fs--2 text-600 mb-1">Meta de Clientes.</h6>
                                    <div class="d-flex align-items-center">
                                      <h5 class="fs-0 text-900 mb-0 me-2" id="">206</h5>
                                    </div>
                                  </div>
                                </div>

                                <div class="col-12 col-sm-auto">
                                  <div class="pe-4 border-sm-end border-200">
                                    <h6 class="fs--2 text-600 mb-1">Clientes Fact.</h6>
                                    <div class="d-flex align-items-center">
                                      <h5 class="fs-0 text-900 mb-0 me-2" id="">354</h5>
                                    </div>
                                  </div>
                                </div>

                            
                                <div class="col-12 col-sm-auto">
                                  <div class="pe-0">
                                    <h6 class="fs--2 text-600 mb-1">% Cumpl.</h6>
                                    <div class="d-flex align-items-center">
                                      <h5 class="fs-0 text-900 mb-0 me-2" id="">% 58</h5>
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
            

          

            <div class="row g-3 mb-3">
           
            <div class="col-md-6 col-xxl-8">

            


            <div class="card">
                <div class="card-header d-flex flex-between-center ps-0 py-0 border-bottom">
                  <ul class="nav nav-tabs border-0 flex-nowrap tab-active-caret" id="crm-revenue-chart-tab" role="tablist" data-tab-has-echarts="data-tab-has-echarts">
                    <li class="nav-item" role="presentation"><a class="nav-link py-3 mb-0 active" id="crm-revenue-tab" data-bs-toggle="tab" href="#crm-revenue" role="tab" aria-controls="crm-revenue" aria-selected="true">Venta del periodo</a></li>                    
                    <li class="nav-item" role="presentation"><a class="nav-link py-3 mb-0" id="crm-profit-tab" data-bs-toggle="tab" href="#crm-profit" role="tab" aria-controls="crm-profit" aria-selected="false">Clientes</a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link py-3 mb-0" id="crm-deals-tab" data-bs-toggle="tab" href="#crm-deals" role="tab" aria-controls="crm-deals" aria-selected="false">Facturas</a></li>
                    
                    <li class="nav-item" role="presentation"><a class="nav-link py-3 mb-0" id="crm-users-tab" data-bs-toggle="tab" href="#crm-users" role="tab" aria-controls="crm-users" aria-selected="false">Articulos</a></li>
                  </ul>                 
                </div>
                <div class="card-body">
                  <div class="row g-1">
                  <div class="col-xxl-3">
                      <div class="row g-0 my-2">
                        <div class="col-md-6 col-xxl-12">
                          <div class="border-xxl-bottom border-xxl-200 mb-2">
                            <h2 class="text-primary">$37,950</h2>
                            <p class="fs--2 text-500 fw-semi-bold mb-0"><span class="fas fa-circle text-primary me-2"></span>Monto de Cierre</p>
                            <p class="fs--2 text-500 fw-semi-bold"><span class="fas fa-circle text-warning me-2"></span>Meta</p>
                          </div>
                          <div class="form-check form-check-inline me-2">
                            <input class="form-check-input" id="crmInbound" type="radio" name="bound" value="inbound" Checked="Checked" />
                            <label class="form-check-label" for="crmInbound">Inbound</label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" id="outbound" type="radio" name="bound" value="outbound" />
                            <label class="form-check-label" for="outbound">Outbound</label>
                          </div>
                        </div>
                        <div class="col-md-6 col-xxl-12 py-2">
                          <div class="row mx-0">
                            <div class="col-6 border-end border-bottom py-3">
                              <h5 class="fw-normal text-600">150</h5>
                              <h6 class="text-500 mb-0">Facturas</h6>
                            </div>
                            <div class="col-6 border-bottom py-3">
                              <h5 class="fw-normal text-600">250</h5>
                              <h6 class="text-500 mb-0">Clientes</h6>
                            </div>
                            <div class="col-6 border-end py-3">
                              <h5 class="fw-normal text-600">150</h5>
                              <h6 class="text-500 mb-0">Articulos</h6>
                            </div>
                            <div class="col-6 py-3">
                              <h5 class="fw-normal text-600"> 0.00</h5>
                              <h6 class="text-500 mb-0">Other</h6>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-xxl-9">
                      <div class="tab-content">
                        <div class="tab-pane active" id="crm-revenue" role="tabpanel" aria-labelledby="crm-revenue-tab">
                          <div class="echart-crm-revenue" data-echart-responsive="true" data-echart-tab="data-echart-tab" style="height:320px;"></div>
                        </div>
                        <div class="tab-pane" id="crm-users" role="tabpanel" aria-labelledby="crm-users-tab">
                          <div class="echart-crm-users" data-echart-responsive="true" data-echart-tab="data-echart-tab" style="height:320px;"></div>
                        </div>
                        <div class="tab-pane" id="crm-deals" role="tabpanel" aria-labelledby="crm-deals-tab">
                          <div class="echart-crm-deals" data-echart-responsive="true" data-echart-tab="data-echart-tab" style="height:320px;"></div>
                        </div>
                        <div class="tab-pane" id="crm-profit" role="tabpanel" aria-labelledby="crm-profit-tab">
                          <div class="echart-crm-profit" data-echart-responsive="true" data-echart-tab="data-echart-tab" style="height:320px;"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6 col-xxl-4">
              <div class="card echart-session-by-browser-card h-100">
                <div class="card-header d-flex flex-between-center bg-light py-2">
                  <h6 class="mb-0">Ranking de vendedores</h6>                  
                </div>
                <div class="card-body d-flex flex-column justify-content-between py-0">
                  <div class="position-relative mb-3">
                    <div class="echart-session-by-browser h-100" data-echart-responsive="true"></div>
                    <div class="position-absolute top-50 start-50 translate-middle text-center">
                      <p class="fs--1 mb-0 text-400 font-sans-serif fw-medium">Total</p>
                      <p class="fs-3 mb-0 font-sans-serif fw-medium mt-n2">15.6k</p>
                    </div>
                  </div>
                  <div class="border-top">
                    <table class="table table-sm mb-0">
                      <tbody>
                        <tr>
                          <td class="py-3">
                            <div class="d-flex align-items-center position-relative">
                              <div class="avatar avatar-2xl status-online">
                                <img class="rounded-circle" src="{{ asset('images/user/avatar-4.jpg') }}" alt="" />
                              </div>
                              <div class="flex-1 ms-3">
                                <h6 class="mb-0 fw-semi-bold"><a class="stretched-link text-900" href="../pages/user/profile.html">1. Nombre del Vendedor</a></h6>
                                <p class="text-500 fs--2 mb-0">F00</p>
                              </div>
                            </div>
                          </td>
                          <td class="py-3">
                            <div class="d-flex align-items-center"><span class="fas fa-circle fs--2 me-2 text-primary"></span>
                              <h6 class="fw-normal text-700 mb-0">50.3%</h6>
                            </div>
                          </td>
                          <td class="py-3">
                            <div class="d-flex align-items-center justify-content-end"><span class="fas fa-caret-down text-danger"></span>
                              <h6 class="fs--2 mb-0 ms-2 text-700">2.9%</h6>
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td class="py-3">
                          <div class="d-flex align-items-center position-relative">
                              <div class="avatar avatar-2xl status-online">
                                <img class="rounded-circle" src="{{ asset('images/user/avatar-4.jpg') }}" alt="" />
                              </div>
                              <div class="flex-1 ms-3">
                                <h6 class="mb-0 fw-semi-bold"><a class="stretched-link text-900" href="../pages/user/profile.html">2. Nombre del Vendedor</a></h6>
                                <p class="text-500 fs--2 mb-0">F00</p>
                              </div>
                            </div>
                          </td>
                          <td class="py-3">
                            <div class="d-flex align-items-center"><span class="fas fa-circle fs--2 me-2 text-success"></span>
                              <h6 class="fw-normal text-700 mb-0">30.1%</h6>
                            </div>
                          </td>
                          <td class="py-3">
                            <div class="d-flex align-items-center justify-content-end"><span class="fas fa-caret-up text-success"></span>
                              <h6 class="fs--2 mb-0 ms-2 text-700">29.4%</h6>
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td class="py-3">
                          <div class="d-flex align-items-center position-relative">
                              <div class="avatar avatar-2xl status-online">
                                <img class="rounded-circle" src="{{ asset('images/user/avatar-4.jpg') }}" alt="" />
                              </div>
                              <div class="flex-1 ms-3">
                                <h6 class="mb-0 fw-semi-bold"><a class="stretched-link text-900" href="../pages/user/profile.html">3. Nombre del Vendedor</a></h6>
                                <p class="text-500 fs--2 mb-0">F00</p>
                              </div>
                            </div>
                          </td>
                          <td class="py-3">
                            <div class="d-flex align-items-center"><span class="fas fa-circle fs--2 me-2 text-info"></span>
                              <h6 class="fw-normal text-700 mb-0">20.6%</h6>
                            </div>
                          </td>
                          <td class="py-3">
                            <div class="d-flex align-items-center justify-content-end"><span class="fas fa-caret-up text-success"></span>
                              <h6 class="fs--2 mb-0 ms-2 text-700">220.7%</h6>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
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