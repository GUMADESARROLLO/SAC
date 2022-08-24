@extends('layouts.lyt_gumadesk')
@section('metodosjs')
@include('jsViews.js_home');
@endsection
@section('content')

<!-- ===============================================-->
<!--    Main Content-->
<!-- ===============================================-->
<main class="main" id="top">
    <div class="container-fluid" data-layout="container">
        
        <div class="content">            
            @include('layouts.nav_importacion')
              <div class="card">
                <div class="card-header d-flex flex-between-center ps-0 py-0 border-bottom">
                  <ul class="nav nav-tabs border-0 flex-nowrap tab-active-caret" id="crm-revenue-chart-tab" role="tablist" data-tab-has-echarts="data-tab-has-echarts">
                    <li class="nav-item" role="presentation"><a class="nav-link py-3 mb-0 active" id="crm-revenue-tab" data-bs-toggle="tab" href="#crm-revenue" role="tab" aria-controls="crm-revenue" aria-selected="true">Inventario</a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link py-3 mb-0" id="crm-profit-tab" data-bs-toggle="tab" href="#crm-profit" role="tab" aria-controls="crm-profit" aria-selected="false">Clientes</a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link py-3 mb-0" id="crm-users-tab" data-bs-toggle="tab" href="#crm-users" role="tab" aria-controls="crm-users" aria-selected="false">Liquidacion 12 Meses</a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link py-3 mb-0" id="crm-deals-tab" data-bs-toggle="tab" href="#crm-deals" role="tab" aria-controls="crm-deals" aria-selected="false">Liquidacion 6 Meses</a></li>                    
                  </ul>
                  <div class="dropdown font-sans-serif btn-reveal-trigger">
                    <button class="btn btn-link text-600 btn-sm dropdown-toggle dropdown-caret-none btn-reveal" type="button" id="dropdown-session-by-country" data-bs-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--2"></span></button>
                    <div class="dropdown-menu dropdown-menu-end border py-2" aria-labelledby="dropdown-session-by-country"><a class="dropdown-item" href="#!">View</a><a class="dropdown-item" href="#!">Export</a>
                      <div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#!">Remove</a>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <div class="row g-1">                   
                    <div class="col-xxl-12">
                      <div class="tab-content">
                        <div class="tab-pane active" id="crm-revenue" role="tabpanel" aria-labelledby="crm-revenue-tab">
                          <table id="tbl_inventario" class="display" style="width:100%"></table>
                        </div>
                        <div class="tab-pane" id="crm-users" role="tabpanel" aria-labelledby="crm-users-tab">
                            <table id="tbl_inventario_liq_12" class="display" style="width:100%"></table>
                        </div>
                        <div class="tab-pane" id="crm-deals" role="tabpanel" aria-labelledby="crm-deals-tab">
                          <table id="tbl_inventario_liq_6" class="display" style="width:100%"></table>
                        </div>
                        <div class="tab-pane" id="crm-profit" role="tabpanel" aria-labelledby="crm-profit-tab">
                          <table id="tbl_mst_clientes" class="display" style="width:100%"></table>
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
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
                <div class="card-header bg-light">          
                    <div class="row justify-content-between">
                      <div class="col">
                        <div class="d-flex">
                          <div class="avatar avatar-2xl status-online">
                            <img class="rounded-circle" src="{{ asset('images/user/avatar-4.jpg') }}" alt="" />

                          </div>
                          <div class="flex-1 align-self-center ms-2">
                            <p class="mb-1 lh-1">10118531<a class="fw-semi-bold" href="#!" > <span id=""> abiraterona acetato 250 mg Tabletas 60/Frasco 1/Caja (Naprod) </span> </a> </p>
                            <p class="mb-0 fs--1"> <div id="id_reglas"></div></p>
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
                        
                      <div class="col-md-12 col-xxl-8">
              <div class="card h-100">
                <div class="card-header d-flex flex-between-center border-bottom border-200 py-2">
                  <h6 class="mb-0">Bodegas</h6>
                  
                </div>
                <div class="card-body">
                <table id="tbl_bodegas" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>BODEGA</th>
                                <th>NOMBRE</th>
                                <th>CANTIDAD DISPONIBLE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>61</td>
                                <td>2011-04-25</td>
                                <td>$320,800</td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>
              </div>
            </div>
            <div class="col-xxl-4">
              <div class="card h-100">
                <div class="card-header d-flex flex-between-center border-bottom py-2">
                  <h6 class="mb-0">Nivel de precios</h6></a>
                </div>
                <div class="card-body">                  
                  <table id="tbl_lista_precios" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>NIVEL PRECIO</th>
                                <th>PRECIO</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>61</td>
                                <td>2011-04-25</td>
                            </tr>
                            
                        </tbody>
                    </table>
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
                          <table id="tbl_historico_factura" class="display" style="width:100%"></table>
                        </div>
                        <div class="tab-pane" id="Ultm-3meses" role="tabpanel" aria-labelledby="Ultm-3meses-tab">
                          <table id="tbl_ultm_3m" class="display" style="width:100%"></table>

                        </div>
                        <div class="tab-pane" id="art-no-fact" role="tabpanel" aria-labelledby="art-no-fact-tab">
                          <table id="tbl_no_facturado" class="display" style="width:100%"></table>
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
                    </a><a class="fs--2 text-600 mb-0" href="#!">C$  <span id="no_fact"></span> </a></div>
                  <div class="d-flex flex-between-center rounded-3 bg-light p-3 mb-2"><a href="#!">
                      <h6 class="mb-0"><span class="fas fa-circle fs--1 me-3 text-warning"></span>30 Dias</h6>
                    </a><a class="fs--2 text-600 mb-0" href="#!">C$ <span id="30_dias"></a></div>
                  <div class="d-flex flex-between-center rounded-3 bg-light p-3 mb-2"><a href="#!">
                      <h6 class="mb-0"><span class="fas fa-circle fs--1 me-3 text-secondary"></span>60 Dias</h6>
                    </a><a class="fs--2 text-600 mb-0" href="#!">C$ <span id="60_dias"></a></div>
                  <div class="d-flex flex-between-center rounded-3 bg-light p-3"><a href="#!">
                      <h6 class="mb-0"><span class="fas fa-circle fs--1 me-3 text-info"></span>90 Dias</h6>
                    </a><a class="fs--2 text-600 mb-0" href="#!">C$ <span id="90_dias"></a></div>
                    <div class="d-flex flex-between-center rounded-3 bg-light p-3 mb-2"><a href="#!">
                      <h6 class="mb-0"><span class="fas fa-circle fs--1 me-3 text-primary"></span>120 Dias</h6>
                    </a><a class="fs--2 text-600 mb-0" href="#!">C$ <span id="120_dias"></a></div>
                  <div class="d-flex flex-between-center rounded-3 bg-light p-3 mb-2"><a href="#!">
                      <h6 class="mb-0"><span class="fas fa-circle fs--1 me-3 text-warning"></span>Mas 120 Dias</h6>
                    </a><a class="fs--2 text-600 mb-0" href="#!">C$ <span id="mas_120_dias"></a></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        
        <!--CLOSE MODALS -->
    </div>
</main>
@endsection('content')