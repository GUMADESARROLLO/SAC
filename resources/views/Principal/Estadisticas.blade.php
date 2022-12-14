@extends('layouts.lyt_gumadesk')
@section('metodosjs')
@include('jsViews.js_estadisticas')
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
                      <a class="btn btn-link btn-sm px-0 shadow-none" href="Home"><span class="fas fa-arrow-left ms-1 fs--2"></span> Regresar</a>
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
                                        <p class="mb-0 fs--2 text-500" id="id_Venta_Week_Label"><span id="id_Dias_Habiles">0</span> de <span id="id_Dias_Facturados"> 0 </span> Dias H??biles.</p>
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


            

           
           
       
            <div class="row g-3">
           
              <div class="col-md-12 col-xxl-12">

              


                <div class="card">
                  <div class="card-header d-flex flex-between-center ps-0 py-0 border-bottom">
                    <ul class="nav nav-tabs border-0 flex-nowrap tab-active-caret" id="crm-revenue-chart-tab" role="tablist" data-tab-has-echarts="data-tab-has-echarts">
                      <li class="nav-item" role="presentation"><a class="nav-link py-3 mb-0 active" id="crm-revenue-tab" data-bs-toggle="tab" href="#crm-revenue" role="tab" aria-controls="crm-revenue" aria-selected="true">Ventas por Dia</a></li>                    
                      <li class="nav-item" role="presentation"><a class="nav-link py-3 mb-0" id="sale-path-tab" data-bs-toggle="tab" href="#sale-path" role="tab" aria-controls="sale-path" aria-selected="true">Ventas por Ruta</a></li>
                      <li class="nav-item" role="presentation"><a class="nav-link py-3 mb-0" id="crm-profit-tab" data-bs-toggle="tab" href="#crm-profit" role="tab" aria-controls="crm-profit" aria-selected="false">Mis Rutas</a></li>
                    </ul>                 
                  </div>
                  <div class="card-body">
                    <div class="row g-1">                    
                      <div class="col-xxl-12">
                        <div class="tab-content">

                          <div class="tab-pane active" id="crm-revenue" role="tabpanel" aria-labelledby="crm-revenue-tab">
                            <div class="echart-sale-VentasDelMes" data-echart-responsive="true" data-echart-tab="data-echart-tab" style="height:320px;"></div>
                          </div>

                          <div class="tab-pane" id="sale-path" role="tabpanel" aria-labelledby="sale-path-tab">
                            <div class="echart-sale-VentasMesRutas" data-echart-responsive="true" data-echart-tab="data-echart-tab" style="height:320px;"></div>
                          </div>

                          <div class="tab-pane" id="crm-profit" role="tabpanel" aria-labelledby="crm-profit-tab">
                            <div class="row fs--1 px-1 py-0" id="id_rutas"></div>
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
</main>
@endsection('content')