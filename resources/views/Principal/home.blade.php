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

            <div class="row g-0">
            <div class="col-lg-6 col-xl-6 pe-lg-2 mb-3">
              <div class="card h-lg-100 overflow-hidden">
                <div class="card-body p-0">
                  <div class="table-responsive scrollbar">
                    <table class="table table-dashboard mb-0 table-borderless fs--1 border-200" id="tbl_productos">
                      <thead class="bg-light">
                        <tr class="text-900">
                          <th ><div class="input-group" >
                                <input class="form-control form-control-sm shadow-none search" type="search" placeholder="Buscar..." aria-label="search" id="id_txt_buscar" />
                                <div class="input-group-text bg-transparent">
                                    <span class="fa fa-search fs--1 text-600"></span>
                                </div>
                            </div>
                        </th>
                          
                        </tr>
                      </thead>
                      <tbody>
                      @foreach ($Productos as $producto)  
                        <tr class="border-bottom border-200">
                          <td>
                            <div class="d-flex align-items-center position-relative">
                                
                            <div class="avatar avatar-2xl status-online">
                                      <img class="rounded-circle" src="{{ asset('images/item.png') }}" alt="" />
                                    </div>
                              <div class="flex-1 ms-3">
                                <h6 class="mb-1 fw-semi-bold">{{ strtoupper($producto->DESCRIPCION) }}</h6>
                                <p class="fw-semi-bold mb-0 text-500">
                                <p class="mb-0 fs--1">{{ strtoupper($producto->ARTICULO) }} &bull; {{ number_format($producto->total,2) }} &bull; {{ strtoupper($producto->UNIDAD) }} &bull; <span class="fas fa-boxes"></span>
                                        <span class="badge rounded-pill ms-3 badge-soft-success ">C$  {{ number_format($producto->PRECIO_FARMACIA,2) }}</span>  
                                        <span class="badge rounded-pill ms-3 badge-soft-info"><span class="fas fa-check"></span>  {{ $producto->REGLAS }}</span>                                          
                                    </p>
                                </p>
                              </div>
                            </div>
                          </td>
                          
                        </tr>
                        @endforeach
                     
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="card-footer bg-light py-2">
                  <div class="row flex-between-center">
                    <div class="col-auto">
                      <br>
                    </div>
                    
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-xl-6 ps-lg-2 mb-3">
            <div class="card h-lg-100 overflow-hidden">
                <div class="card-body p-0">
                  <div class="table-responsive scrollbar">
                    <table class="table table-dashboard mb-0 table-borderless fs--1 border-200" id="tbl_clientes">
                    <thead class="bg-light">
                        <tr class="text-900">
                          <th ><div class="input-group" >
                                <input class="form-control form-control-sm shadow-none search" type="search" placeholder="Buscar..." aria-label="search" id="id_txt_buscar_cliente" />
                                <div class="input-group-text bg-transparent">
                                    <span class="fa fa-search fs--1 text-600"></span>
                                </div>
                            </div>
                        </th>
                        <th>
                          <select>
                        </th>
                          
                        </tr>
                      </thead>
                      <tbody>
                      @foreach ($Clientes as $cliente)  
                        <tr class="border-bottom border-200 {{ ($cliente->MOROSO=='S')? '': 'bg-soft-danger' }} ">
                          <td>
                            <div class="d-flex align-items-center position-relative">
                                
                            <div class="avatar avatar-2xl status-online">
                                <img class="rounded-circle" src="{{ asset('images/item.png') }}" alt="" />
                            </div>
                              <div class="flex-1 ms-3">
                                <h6 class="mb-1 fw-semi-bold">{{ strtoupper($cliente->NOMBRE) }} {{ ($cliente->MOROSO=='S')? '': ' [MOROSO]' }}</h6>
                                <p class="fw-semi-bold mb-0 text-500 ">
                                    <p class="mb-1 fs--1 ">
                                    {{ $cliente->CLIENTE }} &bull; {{ $cliente->VENDEDOR }} &bull; 
                                        <span class="fas fa-boxes"></span>
                                        <span class="badge rounded-pill ms-3 badge-soft-success ">C$  {{ number_format($cliente->LIMITE_CREDITO,2) }}</span>                                          
                                        <span class="badge rounded-pill ms-3 badge-soft-danger"><span class="fas fa-check"></span> C$ {{number_format($cliente->SALDO,2) }} </span>                                          
                                        <span class="badge rounded-pill ms-3 badge-soft-info"><span class="fas fa-check"></span> C$ {{number_format($cliente->CREDITODISP,2) }} </span>                                          
                                    </p>
                                    <p class="mb-0 fs--1" >
                                    {{ $cliente->DIRECCION }}
                                    </p>
                                    <p class="mb-0 fs--1" >
                                    {{ $cliente->TELEFONO1 }} &bull; {{ $cliente->TELEFONO2 }}
                                    </p>
                                    
                                    
                                </p>
                                
                              </div>
                            </div>
                          </td>
                          <td class="align-middle pe-card">
                            <div class="d-flex align-items-center">
                              {{ date('D, M d, Y', strtotime($cliente->fecha)) }}</br> Ult. Fact.
                            </div>
                          </td>
                        </tr>
                        @endforeach
                     
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="card-footer bg-light py-2">
                  <div class="row flex-between-center">
                    <div class="col-auto">
                    <br>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>


        
        <!--OPEN MODALS -->
        <div class="modal fade" id="modal_new_product" tabindex="-1">
          <div class="modal-dialog modal-xl">
            <div class="modal-content border-0">
            <div class="modal-header px-5 position-relative modal-shape-header bg-shape">
                <div class="position-relative z-index-1 light">
                    <h4 class="mb-0 text-white" id="authentication-modal-label">Producto</h4>
                    <p class="fs--1 mb-0 text-white invisible"> --- </p>
                    <p class="fs--1 mb-0 text-white invisible" id="id_modal_state"> - </p>
                </div>
                <button class="btn-close btn-close-white position-absolute top-0 end-0 mt-2 me-2" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
                <div class="modal-body p-card">
                    <div class="mb-3">
                        <label class="fs-0" for="id_articulo">Codigo de Sistema</label>
                        <select class="form-select" id="id_articulo" name="label">
                            <option value="" selected="selected">None</option>
                           
                        </select>
                    </div>
                  <div class="mb-3">
                    <label class="fs-0" for="id_nombre_corto">Nombre Corto</label>
                    <input class="form-control" id="id_nombre_corto" type="text" name="title" required="required" />
                  </div>
                  <div class="mb-3">
                    <label class="fs-0" for="id_nombre_largo">Nombre Largo</label>
                    <textarea class="form-control" rows="3" name="description" id="id_nombre_largo" required="required"></textarea>
                  </div>

                  <div class="row g-2">
                    <div class="col-md-3 col-sm-12 col-xxl-3">
                        <div class="mb-3">
                            <label class="fs-0" for="id_tipo">Unidad Almacen</label>
                            <select class="form-select" id="id_unidad_almacen" name="label" required="required">
                                <option value="" selected="selected">None</option>
                              
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-12 col-xxl-3">
                        <div class="mb-3">
                            <label class="fs-0" for="id_tipo">Laboratorio</label>
                            <select class="form-select" id="id_laboratorio" name="label" required="required">
                                <option value="" selected="selected">None</option>
                              
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-12 col-xxl-3">
                        <div class="mb-3">
                            <label class="fs-0" for="id_tipo">Proveedor</label>
                            <select class="form-select" id="id_proveedor" name="label" required="required">
                                <option value="" selected="selected">None</option>
                               
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-12 col-xxl-3">
                        <div class="mb-3">
                            <label class="fs-0" for="id_tipo">Tipo Producto</label>
                            <select class="form-select" id="id_tipo" name="label" required="required">
                                <option value="" selected="selected">None</option>
                               
                            </select>
                        </div>
                    </div>
                  </div>

                  
                </div>
                <div class="card-footer d-flex justify-content-end align-items-center bg-light">
                  <button class="btn btn-primary px-4" id="id_send_frm_produc" type="submit">Guardar</button>
                </div>
            </div>
          </div>
        </div>
        <!--CLOSE MODALS -->
    </div>
</main>
@endsection('content')