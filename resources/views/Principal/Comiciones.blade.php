@extends('layouts.lyt_gumadesk')
@section('metodosjs')
@include('jsViews.js_Comiciones')
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
                  <div class="d-flex position-relative align-items-center">
                    <div class="flex-1">
                      <div class="d-flex flex-between-center">
                        <div class="d-flex align-items-center position-relative">

                          <div class="flex-1">
                            <h6 class="mb-0 fw-semi-bold"><div class="stretched-link text-900">LIQUIDACIÓN DE COMISIONES DE EJECUTIVOS DE VENTAS</div></h6>
                          </div>
                          
                        </div>
                      <a class="btn btn-link btn-sm px-0 shadow-none" href="estadisticas">Ver más<span class="fas fa-arrow-right ms-1 fs--2"></span></a>
                      
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
                            <th colspan="2">COMISIÓN DE VENTA </th>                            
                            <th colspan="2">RECUPERACIÓN DE CRÉDITO</th>
                            <th colspan="2">RECUPERACIÓN DE CONTADO</th>
                            <th>Bono.Cobertura</th>
                            <th>Total. Comisiones</th>
                            <th>Comisión + Bono</th>
                            <th>Total Compensación</th>
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
                                  <h5 class="fs-0 text-900 mb-0 me-2">C$ {{ strtoupper($cms['DATARESULT']['Comision_de_venta']['Total'][3]) }} </h5>
                                </div>
                              </div> 
                            </td>
                            <td>
                              <div class="mb-3 pe-4 border-sm-end border-200">
                                <h6 class="fs--2 text-600 mb-1">Comisión</h6>
                                <div class="d-flex align-items-center">
                                  <h5 class="fs-0 text-900 mb-0 me-2">C$ {{ strtoupper($cms['DATARESULT']['Comision_de_venta']['Total'][1]) }}</h5>
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
                                <h6 class="fs--2 text-600 mb-1">Forecast Hours</h6>
                                <div class="d-flex align-items-center">
                                  <h5 class="fs-0 text-900 mb-0 me-2">C$ {{ strtoupper($cms['DATARESULT']['recuperacion_de_contado'][2]) }} </h5>
                                  <span class="badge rounded-pill badge-soft-primary"><span class="fas fa-caret-up"></span> {{ strtoupper($cms['DATARESULT']['recuperacion_de_contado'][1]) }}%</span>
                                </div>
                              </div>
                            </td>
                            <td>                              
                              <h6 class="fs--2 text-600 mb-1"></h6>
                              <div class="d-flex align-items-center">
                                <h5 class="fs-0 text-900 mb-0 me-2">C$ {{ strtoupper($cms['DATARESULT']['Totales_finales'][0]) }}</h5>
                              </div>
                            </td>
                            <td>
                              <div class="d-flex align-items-center">
                                <h5 class="fs-0 text-900 mb-0 me-2">C$ {{ strtoupper($cms['DATARESULT']['Totales_finales'][1]) }}</h5>
                              </div>
                            </td>
                            <td>
                              <div class="d-flex align-items-center">
                                <h5 class="fs-0 text-900 mb-0 me-2">C$ {{ strtoupper($cms['DATARESULT']['Totales_finales'][2]) }}</h5>
                              </div>
                            </td>
                            <td>
                              <div class="d-flex align-items-center">
                                <h5 class="fs-0 text-900 mb-0 me-2">C$ {{ strtoupper($cms['DATARESULT']['Total_Compensacion']) }}</h5>
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
          </div>
      
    </div>
</main>
@endsection('content')