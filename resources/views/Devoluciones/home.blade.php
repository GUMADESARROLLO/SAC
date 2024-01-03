@extends('layouts.lyt_gumadesk')
@section('metodosjs')
@include('jsViews.js_Devoluciones')
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
                        <li class="nav-item" role="presentation"><a class="nav-link py-3 mb-0 active" id="crm-revenue-tab" data-bs-toggle="tab" href="#crm-revenue" role="tab" aria-controls="crm-revenue" aria-selected="true"></a></li>                  
                    </ul>
                    <div class="dropdown font-sans-serif btn-reveal-trigger">
                        <div id="id_loading" class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="input-group" >
                        <input class="form-control form-control-sm shadow-none search" type="search" placeholder="Digite el numero de Factura" aria-label="search" id="tbl_buscar_tbl_facturas" />
                        <div class="input-group-text bg-grey" id="btn_search">
                            <span class="fa fa-search fs--1 text-600" ></span>
                        </div>
                    </div>
                    <div class="table-responsive scrollbar mt-3">
                    <table id="tbl_facturas" class="table table-striped overflow-hidden" style="width:100%"></table>
                    </div>
                </div>
              </div>
    </div>
</main>
@endsection('content')