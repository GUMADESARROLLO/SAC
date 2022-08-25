@extends('layouts.lyt_gumadesk')
@section('metodosjs')
@include('jsViews.js_usuario')
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
            <div class="card-header bg-light">
                <div class="row flex-between-center">
                    <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
                    <h5 class="fs-0 mb-0 text-nowrap py-2 py-xl-0">Listas de Usuarios ( {{count($Usuarios)}} )</h5>
                    </div>
                    <div class="col-8 col-sm-auto text-end ps-2">
                        <div id="table-customers-replace-element">
                            <div class="input-group" >
                                <input class="form-control form-control-sm shadow-none search" type="search" placeholder="Buscar..." aria-label="search" id="id_txt_buscar" />
                                <div class="input-group-text bg-transparent">
                                    <span class="fa fa-search fs--1 text-600"></span>
                                </div>
                                <div class="input-group-text bg-transparent" id="id_btn_new">
                                    <span class="fa fa-plus fs--1 text-600"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
          
            <div class="card-body bg-light px-1 py-0">
                <div class="row g-0 text-center fs--1">
                    @foreach ($Usuarios as $usuario) 
                        <div class="col-6 col-md-4 col-lg-3 col-xxl-2 mb-1">
                            <div class="bg-white dark__bg-1100 p-3 h-100">
                                <a href="#!">
                                    <img class="img-thumbnail img-fluid rounded-circle mb-3 shadow-sm" src="images/user/avatar-4.jpg" alt="" width="100" />
                                </a>
                                
                            <h6 class="mb-1"><a href="#!" onclick="OpenModal({{$usuario}})" >{{ strtoupper($usuario->nombre) }}</a>
                            </h6>
                            <p class="fs--2 mb-1"><a class="text-700" href="#!" onclick="AsginarRuta({{$usuario}})">{{ strtoupper($usuario->Usuario) }}</a></p>
                            <div class="col">
                                @foreach ($usuario->Detalles as $Rutas)
                                <a class="d-inline-flex align-items-center border rounded-pill px-3 py-1 me-2 mt-2 inbox-link" href="#!" onclick="Remover({{$Rutas}})">
                                    <span class="fas fa-user-tie text-danger" data-fa-transform="grow-4"></span><span class="ms-2"> {{$Rutas->RUTA}}</span>
                                </a>
                                @endforeach   
                            </div>
                            </div>
                        </div>
                    @endforeach   
                </div>
            </div>
            </div>
           
        </div>
    </div>
</main>
<div class="modal fade" id="modal_new_product" tabindex="-1">
          <div class="modal-dialog modal-xl">
            <div class="modal-content border-0">
            <div class="modal-header px-5 position-relative modal-shape-header bg-shape">
                <div class="position-relative z-index-1 light">
                    <h4 class="mb-0 text-white" id="authentication-modal-label">Informacion del Usuario</h4>                    
                    <p class="fs--1 mb-0 text-white" id="id_modal_state"> New </p>
                </div>
                <button class="btn-close btn-close-white position-absolute top-0 end-0 mt-2 me-2" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
                <div class="modal-body p-card">
                  <div class="mb-3">
                    <label class="fs-0" for="id_nombre_usuario">Usuario</label>
                    <input class="form-control" id="id_nombre_usuario" type="text" name="title" required="required" />
                  </div>
                  <div class="mb-3">
                    <label class="fs-0" for="id_nombre_completo">Nombre Completo</label>
                    <input class="form-control" id="id_nombre_completo" type="text" name="title" required="required" />
                  </div>
                  <div class="mb-3">
                    <label class="fs-0" for="id_password">Contraseña</label>
                    <input class="form-control" id="id_password" type="password" name="title" required="required" />
                  </div>
                 

                  
                </div>
                <div class="card-footer d-flex justify-content-end align-items-center bg-light">
                  <button class="btn btn-danger px-4" id="id_remover" type="submit">Eliminar</button>
                  <button class="btn btn-primary px-4 ms-3" id="id_send_frm_produc" type="submit">Guardar</button>
                </div>
            </div>
          </div>
        </div>
@endsection('content')