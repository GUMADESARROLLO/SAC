@extends('layouts.lyt_gumadesk')
@section('metodosjs')
@include('jsViews.js_comiciones')
@endsection
@section('content')

<!-- ===============================================-->
<!--    Main Content-->
<!-- ===============================================-->
<main class="main" id="top">
    <div class="container-fluid" data-layout="container">        
        <div class="content">            

            @include('layouts.nav_importacion')

            

            <div class="col-12">
              
              <div class="card h-100">
                
                <div class="card-header">
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
                        <div class="col-md-auto">
                          <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="id_select_sac">
                              <option value="0" >Todas</option>                                      
                            
                          </select>
                        </div> 
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
                </div> 
                <div class="card-body p-0 mb-2" >                  
                  <div class="scrollbar-overlay p-0 px-car">
                    <div class="row flex-between-center border border-1 border-300 rounded-2">
                      <table id="table_comisiones" class="table fs--1 mb-0 overflow-hidden" >
                        <thead>
                          <tr>
                            <th>VENDEDOR</th>
                            <th colspan="3">RECUPERACIÓN DE CRÉDITO</th>
                            <th colspan="3">RECUPERACIÓN DE CONTADO</th>
                            <th colspan="3">TOTAL BONOS Y COMISIONES</th>
                            <th>TOTAL</th>
                          </tr>
                          <tr >
                            <th></th>
                            <th> </th>
                            <th></th>                            
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>
                        @foreach ($Comision as $cms)
                          <tr>
                            <td>
                              <div class="d-flex align-items-center position-relative mt-2">
                                <div class="avatar avatar-xl ">
                                  <img class="rounded-circle" src="{{ asset('images/user/avatar-4.jpg') }}"   />
                                </div>
                                  <div class="flex-1 ms-3">
                                    <h6 class="mb-0 fw-semi-bold"><div class="stretched-link text-900">{{ strtoupper($cms['NOMBRE']) }}</div></h6>
                                    <p class="text-500 fs--2 mb-0">{{ strtoupper($cms['VENDEDOR']) }} | EST-NS-MD </p>
                                  </div>
                              </div>
                            </td>
                            <td>
                              <div class="mb-3 pe-4 border-sm-end border-200">
                                <h6 class="fs--2 text-600 mb-1">Ventas Val.</h6>
                                <div class="d-flex align-items-center">
                                  <h5 class="fs-0 text-900 mb-0 me-2">C$ {{ strtoupper($cms['DATARESULT']['Comision_de_venta']['Total'][1]) }} </h5>
                                </div>
                              </div> 
                            </td>
                            <td>
                              <div class="mb-3 pe-4 border-sm-end border-200">
                                <h6 class="fs--2 text-600 mb-1">Comisión</h6>
                                <div class="d-flex align-items-center">
                                  <h5 class="fs-0 text-900 mb-0 me-2">C$ {{ strtoupper($cms['DATARESULT']['Comision_de_venta']['Total'][3]) }}</h5>
                                  <span class="badge rounded-pill badge-soft-primary"><span class="fas fa-caret-up"></span> {{ strtoupper($cms['DATARESULT']['Comision_de_venta']['Total'][2]) }}%</span>
                                </div>
                              </div>
                            </td>
                            <td>
                              <div class="mb-3 pe-4 border-sm-end border-200">
                                <h6 class="fs--2 text-600 mb-1">Valor.</h6>
                                <div class="d-flex align-items-center">
                                  <h5 class="fs-0 text-900 mb-0 me-2">C$ {{ strtoupper($cms['DATARESULT']['recuperacion_de_credito'][0]) }} </h5>                                  
                                </div>
                              </div>
                            </td>
                            <td>
                              <div class="mb-3 pe-4 border-sm-end border-200">
                                <h6 class="fs--2 text-600 mb-1">Comisión</h6>
                                <div class="d-flex align-items-center">
                                  <h5 class="fs-0 text-900 mb-0 me-2">C$ {{ strtoupper($cms['DATARESULT']['recuperacion_de_credito'][2]) }} </h5>
                                  <span class="badge rounded-pill badge-soft-primary"><span class="fas fa-caret-up"></span> {{ strtoupper($cms['DATARESULT']['recuperacion_de_credito'][1]) }}%</span>
                                </div>
                              </div>
                            </td>
                            <td>
                              <div class="mb-3 pe-4 border-sm-end border-200">
                                <h6 class="fs--2 text-600 mb-1">Valor</h6>
                                <div class="d-flex align-items-center">
                                  <h5 class="fs-0 text-900 mb-0 me-2">C$ {{ strtoupper($cms['DATARESULT']['recuperacion_de_contado'][0]) }} </h5>
                                  
                                </div>
                              </div>
                            </td>
                            <td>
                              <div class="mb-3 pe-4 border-sm-end border-200">
                                <h6 class="fs--2 text-600 mb-1">Comisión</h6>
                                <div class="d-flex align-items-center">
                                  <h5 class="fs-0 text-900 mb-0 me-2">C$ {{ strtoupper($cms['DATARESULT']['recuperacion_de_contado'][2]) }} </h5>
                                  <span class="badge rounded-pill badge-soft-primary"><span class="fas fa-caret-up"></span> {{ strtoupper($cms['DATARESULT']['recuperacion_de_contado'][1]) }}%</span>
                                </div>
                              </div>
                            </td>
                            <td>
                              <div class="mb-3 pe-4 border-sm-end border-200">
                                <h6 class="fs--2 text-600 mb-1">Bono.Cobertura</h6>
                                <div class="d-flex align-items-center">
                                  <h5 class="fs-0 text-900 mb-0 me-2">C$ {{ strtoupper($cms['DATARESULT']['Totales_finales'][0]) }}</h5>
                                </div>
                              </div>  
                            </td>
                            <td>
                              <div class="mb-3 pe-4 border-sm-end border-200">
                                <h6 class="fs--2 text-600 mb-1">Total. Comisiones</h6>
                                <div class="d-flex align-items-center">
                                  <h5 class="fs-0 text-900 mb-0 me-2">C$ {{ strtoupper($cms['DATARESULT']['Totales_finales'][1]) }}</h5>
                                </div>
                              </div>
                            </td>
                            <td>
                              <div class="mb-3 pe-4 border-sm-end border-200">
                                <h6 class="fs--2 text-600 mb-1">Comisión + Bono</h6>
                                <div class="d-flex align-items-center">
                                  <h5 class="fs-0 text-900 mb-0 me-2">C$ {{ strtoupper($cms['DATARESULT']['Totales_finales'][2]) }}</h5>
                                </div>
                              </div>
                            </td>
                            <td>
                              <div class="mb-3 pe-0">
                                <h6 class="fs--2 text-600 mb-1">Total Compensación</h6>
                                <div class="d-flex align-items-center">
                                  <h5 class="fs-0 text-900 mb-0 me-2">C$ {{ strtoupper($cms['DATARESULT']['Total_Compensacion']) }}</h5>                                  
                                </div>
                              </div>
                            </td>
                          </tr>
                        @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                
                @foreach ($Comision as $cms)
                <table class="table" style="display:none">
                                      <thead class="bg-200 text-900">
                                        <tr>
                                          <th class="border-0">Sku Clasif</th>
                                          <th class="border-0 text-center">Sku Fact</th>
                                          <th class="border-0 text-end">Ventas Val</th>
                                          <th class="border-0 text-end">Factor Com</th>
                                          <th class="border-0 text-end">Comisión </th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <tr class="border-200">
                                          <td class="align-middle">
                                            <h6 class="mb-0 text-nowrap">80% </h6>
                                          </td>
                                          <td class="align-middle text-center">{{ strtoupper($cms['DATARESULT']['Comision_de_venta']['Lista80'][0]) }}</td>
                                          <td class="align-middle text-end">C$ {{ strtoupper($cms['DATARESULT']['Comision_de_venta']['Lista80'][1]) }} </td>
                                          <td class="align-middle text-end"> {{ strtoupper($cms['DATARESULT']['Comision_de_venta']['Lista80'][2]) }} </td>                                          
                                          <td class="align-middle text-end">C$ {{ strtoupper($cms['DATARESULT']['Comision_de_venta']['Lista80'][3]) }} </td>
                                        </tr>
                                        <tr class="border-200">
                                          <td class="align-middle">
                                            <h6 class="mb-0 text-nowrap">20% </h6>
                                          </td>
                                          <td class="align-middle text-center">{{ strtoupper($cms['DATARESULT']['Comision_de_venta']['Lista20'][0]) }}</td>
                                          <td class="align-middle text-end">C$ {{ strtoupper($cms['DATARESULT']['Comision_de_venta']['Lista20'][1]) }} </td>
                                          <td class="align-middle text-end"> {{ strtoupper($cms['DATARESULT']['Comision_de_venta']['Lista20'][2]) }} </td>                                          
                                          <td class="align-middle text-end">C$ {{ strtoupper($cms['DATARESULT']['Comision_de_venta']['Lista20'][3]) }} </td>
                                        </tr>
                                        <tr class="border-200">
                                          <td class="align-middle">
                                            <h6 class="mb-0 text-nowrap">Total </h6>
                                          </td>
                                          <td class="align-middle text-center">{{ strtoupper($cms['DATARESULT']['Comision_de_venta']['Total'][0]) }}</td>
                                          <td class="align-middle text-end">C$ {{ strtoupper($cms['DATARESULT']['Comision_de_venta']['Total'][1]) }} </td>
                                          <td class="align-middle text-end"> {{ strtoupper($cms['DATARESULT']['Comision_de_venta']['Total'][2]) }} </td>                                          
                                          <td class="align-middle text-end">C$  {{ strtoupper($cms['DATARESULT']['Comision_de_venta']['Total'][3]) }} </td>
                                        </tr>
                                      </tbody>
                                    </table>
                @endforeach
                
              </div>
            </div>

            <div class="row g-0 mt-3">
            <div class="col-lg-6 pe-lg-2 mb-3">
              <div class="card h-lg-100 overflow-hidden">
                <div class="card-header bg-light">
                  <div class="row align-items-center">
                    <div class="col">
                      <h6 class="mb-0">Escala Numerica</h6>
                    </div>
                    <div class="col-auto text-center pe-card">
                      <select class="form-select form-select-sm">
                        <option>Working Time</option>
                        <option>Estimated Time</option>
                        <option>Billable Time</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="card-body p-0">
                  <div class="row g-0 align-items-center py-2 position-relative border-bottom border-200">
                    <div class="col ps-card py-1 position-static">
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-xl me-3">
                          <div class="avatar-name rounded-circle bg-soft-primary text-dark"><span class="fs-0 text-primary">F</span></div>
                        </div>
                        <div class="flex-1">
                          <h6 class="mb-0 d-flex align-items-center"><a class="text-800 stretched-link" href="#!">Falcon</a><span class="badge rounded-pill ms-2 bg-200 text-primary">38%</span></h6>
                        </div>
                      </div>
                    </div>
                    <div class="col py-1">
                      <div class="row flex-end-center g-0">
                        <div class="col-auto pe-2">
                          <div class="fs--1 fw-semi-bold">12:50:00</div>
                        </div>
                        <div class="col-5 pe-card ps-2">
                          <div class="progress bg-200 me-2" style="height: 5px;">
                            <div class="progress-bar rounded-pill" role="progressbar" style="width: 38%" aria-valuenow="38" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row g-0 align-items-center py-2 position-relative border-bottom border-200">
                    <div class="col ps-card py-1 position-static">
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-xl me-3">
                          <div class="avatar-name rounded-circle bg-soft-success text-dark"><span class="fs-0 text-success">R</span></div>
                        </div>
                        <div class="flex-1">
                          <h6 class="mb-0 d-flex align-items-center"><a class="text-800 stretched-link" href="#!">Reign</a><span class="badge rounded-pill ms-2 bg-200 text-primary">79%</span></h6>
                        </div>
                      </div>
                    </div>
                    <div class="col py-1">
                      <div class="row flex-end-center g-0">
                        <div class="col-auto pe-2">
                          <div class="fs--1 fw-semi-bold">25:20:00</div>
                        </div>
                        <div class="col-5 pe-card ps-2">
                          <div class="progress bg-200 me-2" style="height: 5px;">
                            <div class="progress-bar rounded-pill" role="progressbar" style="width: 79%" aria-valuenow="79" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row g-0 align-items-center py-2 position-relative border-bottom border-200">
                    <div class="col ps-card py-1 position-static">
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-xl me-3">
                          <div class="avatar-name rounded-circle bg-soft-info text-dark"><span class="fs-0 text-info">B</span></div>
                        </div>
                        <div class="flex-1">
                          <h6 class="mb-0 d-flex align-items-center"><a class="text-800 stretched-link" href="#!">Boots4</a><span class="badge rounded-pill ms-2 bg-200 text-primary">90%</span></h6>
                        </div>
                      </div>
                    </div>
                    <div class="col py-1">
                      <div class="row flex-end-center g-0">
                        <div class="col-auto pe-2">
                          <div class="fs--1 fw-semi-bold">58:20:00</div>
                        </div>
                        <div class="col-5 pe-card ps-2">
                          <div class="progress bg-200 me-2" style="height: 5px;">
                            <div class="progress-bar rounded-pill" role="progressbar" style="width: 90%" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row g-0 align-items-center py-2 position-relative border-bottom border-200">
                    <div class="col ps-card py-1 position-static">
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-xl me-3">
                          <div class="avatar-name rounded-circle bg-soft-warning text-dark"><span class="fs-0 text-warning">R</span></div>
                        </div>
                        <div class="flex-1">
                          <h6 class="mb-0 d-flex align-items-center"><a class="text-800 stretched-link" href="#!">Raven</a><span class="badge rounded-pill ms-2 bg-200 text-primary">40%</span></h6>
                        </div>
                      </div>
                    </div>
                    <div class="col py-1">
                      <div class="row flex-end-center g-0">
                        <div class="col-auto pe-2">
                          <div class="fs--1 fw-semi-bold">21:20:00</div>
                        </div>
                        <div class="col-5 pe-card ps-2">
                          <div class="progress bg-200 me-2" style="height: 5px;">
                            <div class="progress-bar rounded-pill" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row g-0 align-items-center py-2 position-relative">
                    <div class="col ps-card py-1 position-static">
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-xl me-3">
                          <div class="avatar-name rounded-circle bg-soft-danger text-dark"><span class="fs-0 text-danger">S</span></div>
                        </div>
                        <div class="flex-1">
                          <h6 class="mb-0 d-flex align-items-center"><a class="text-800 stretched-link" href="#!">Slick</a><span class="badge rounded-pill ms-2 bg-200 text-primary">70%</span></h6>
                        </div>
                      </div>
                    </div>
                    <div class="col py-1">
                      <div class="row flex-end-center g-0">
                        <div class="col-auto pe-2">
                          <div class="fs--1 fw-semi-bold">31:20:00</div>
                        </div>
                        <div class="col-5 pe-card ps-2">
                          <div class="progress bg-200 me-2" style="height: 5px;">
                            <div class="progress-bar rounded-pill" role="progressbar" style="width: 70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-footer bg-light p-0"><a class="btn btn-sm btn-link d-block w-100 py-2" href="#!">Show all projects<span class="fas fa-chevron-right ms-1 fs--2"></span></a></div>
              </div>
            </div>
            <div class="col-lg-6 pe-lg-2 mb-3">
              <div class="card h-lg-100 overflow-hidden">
                <div class="card-header bg-light">
                  <div class="row align-items-center">
                    <div class="col">
                      <h6 class="mb-0">Cobertura de Clientes</h6>
                    </div>
                    <div class="col-auto text-center pe-card">
                      <select class="form-select form-select-sm">
                        <option>Working Time</option>
                        <option>Estimated Time</option>
                        <option>Billable Time</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="card-body p-0">
                  <div class="row g-0 align-items-center py-2 position-relative border-bottom border-200">
                    <div class="col ps-card py-1 position-static">
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-xl me-3">
                          <div class="avatar-name rounded-circle bg-soft-primary text-dark"><span class="fs-0 text-primary">F</span></div>
                        </div>
                        <div class="flex-1">
                          <h6 class="mb-0 d-flex align-items-center"><a class="text-800 stretched-link" href="#!">Falcon</a><span class="badge rounded-pill ms-2 bg-200 text-primary">38%</span></h6>
                        </div>
                      </div>
                    </div>
                    <div class="col py-1">
                      <div class="row flex-end-center g-0">
                        <div class="col-auto pe-2">
                          <div class="fs--1 fw-semi-bold">12:50:00</div>
                        </div>
                        <div class="col-5 pe-card ps-2">
                          <div class="progress bg-200 me-2" style="height: 5px;">
                            <div class="progress-bar rounded-pill" role="progressbar" style="width: 38%" aria-valuenow="38" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row g-0 align-items-center py-2 position-relative border-bottom border-200">
                    <div class="col ps-card py-1 position-static">
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-xl me-3">
                          <div class="avatar-name rounded-circle bg-soft-success text-dark"><span class="fs-0 text-success">R</span></div>
                        </div>
                        <div class="flex-1">
                          <h6 class="mb-0 d-flex align-items-center"><a class="text-800 stretched-link" href="#!">Reign</a><span class="badge rounded-pill ms-2 bg-200 text-primary">79%</span></h6>
                        </div>
                      </div>
                    </div>
                    <div class="col py-1">
                      <div class="row flex-end-center g-0">
                        <div class="col-auto pe-2">
                          <div class="fs--1 fw-semi-bold">25:20:00</div>
                        </div>
                        <div class="col-5 pe-card ps-2">
                          <div class="progress bg-200 me-2" style="height: 5px;">
                            <div class="progress-bar rounded-pill" role="progressbar" style="width: 79%" aria-valuenow="79" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row g-0 align-items-center py-2 position-relative border-bottom border-200">
                    <div class="col ps-card py-1 position-static">
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-xl me-3">
                          <div class="avatar-name rounded-circle bg-soft-info text-dark"><span class="fs-0 text-info">B</span></div>
                        </div>
                        <div class="flex-1">
                          <h6 class="mb-0 d-flex align-items-center"><a class="text-800 stretched-link" href="#!">Boots4</a><span class="badge rounded-pill ms-2 bg-200 text-primary">90%</span></h6>
                        </div>
                      </div>
                    </div>
                    <div class="col py-1">
                      <div class="row flex-end-center g-0">
                        <div class="col-auto pe-2">
                          <div class="fs--1 fw-semi-bold">58:20:00</div>
                        </div>
                        <div class="col-5 pe-card ps-2">
                          <div class="progress bg-200 me-2" style="height: 5px;">
                            <div class="progress-bar rounded-pill" role="progressbar" style="width: 90%" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row g-0 align-items-center py-2 position-relative border-bottom border-200">
                    <div class="col ps-card py-1 position-static">
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-xl me-3">
                          <div class="avatar-name rounded-circle bg-soft-warning text-dark"><span class="fs-0 text-warning">R</span></div>
                        </div>
                        <div class="flex-1">
                          <h6 class="mb-0 d-flex align-items-center"><a class="text-800 stretched-link" href="#!">Raven</a><span class="badge rounded-pill ms-2 bg-200 text-primary">40%</span></h6>
                        </div>
                      </div>
                    </div>
                    <div class="col py-1">
                      <div class="row flex-end-center g-0">
                        <div class="col-auto pe-2">
                          <div class="fs--1 fw-semi-bold">21:20:00</div>
                        </div>
                        <div class="col-5 pe-card ps-2">
                          <div class="progress bg-200 me-2" style="height: 5px;">
                            <div class="progress-bar rounded-pill" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row g-0 align-items-center py-2 position-relative">
                    <div class="col ps-card py-1 position-static">
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-xl me-3">
                          <div class="avatar-name rounded-circle bg-soft-danger text-dark"><span class="fs-0 text-danger">S</span></div>
                        </div>
                        <div class="flex-1">
                          <h6 class="mb-0 d-flex align-items-center"><a class="text-800 stretched-link" href="#!">Slick</a><span class="badge rounded-pill ms-2 bg-200 text-primary">70%</span></h6>
                        </div>
                      </div>
                    </div>
                    <div class="col py-1">
                      <div class="row flex-end-center g-0">
                        <div class="col-auto pe-2">
                          <div class="fs--1 fw-semi-bold">31:20:00</div>
                        </div>
                        <div class="col-5 pe-card ps-2">
                          <div class="progress bg-200 me-2" style="height: 5px;">
                            <div class="progress-bar rounded-pill" role="progressbar" style="width: 70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-footer bg-light p-0"><a class="btn btn-sm btn-link d-block w-100 py-2" href="#!">Show all projects<span class="fas fa-chevron-right ms-1 fs--2"></span></a></div>
              </div>
            </div>
          </div>

            
          </div>
      
    </div>
</main>
@endsection('content')