@extends('layouts.lyt_gumadesk')
@section('sac_title', 'Equipo SAC')

@section('metodosjs')
<script>
  window.SAC_EQ_SACS = @json($Sacs);
</script>
@include('jsViews.js_equipo_sac')
@endsection

@section('content')
<main class="main" id="top">
  <div class="container-fluid" data-layout="container">
    <div class="content">
      @include('layouts.nav_importacion')

      <div class="sac-wrap sac-eq">
        <div class="sac-head">
          <div>
            <h1 class="sac-h1">Equipo SAC</h1>
            <p class="sac-sub">Estados, tiempo por estado y pedidos procesados por el equipo</p>
          </div>
          <div class="sac-eq-headr">
            <div class="sac-eq-live" data-eq-solo="vivo">
              <i class="sac-eq-pulse" aria-hidden="true"></i>
              <span id="sacEqLiveTxt">Hoy</span>
            </div>
          </div>
        </div>

        <section class="card" style="overflow: visible" aria-label="Equipo SAC">
          <div class="sac-toolbar sac-eq-bar">
            <div class="nav-tabs m-0" role="tablist" aria-label="Reportes del equipo">
              <button type="button" class="nav-link active" role="tab" data-eq-tab="vivo" aria-selected="true">En vivo</button>
              <button type="button" class="nav-link" role="tab" data-eq-tab="bitacora" aria-selected="false">Bitácora</button>
              <button type="button" class="nav-link" role="tab" data-eq-tab="resumen" aria-selected="false">Resumen</button>
              <button type="button" class="nav-link" role="tab" data-eq-tab="pedidos" aria-selected="false">Pedidos</button>
              <button type="button" class="nav-link" role="tab" data-eq-tab="aplicaciones" aria-selected="false">Aplicaciones</button>
            </div>
            <button type="button" class="btn btn-falcon-default ms-auto" id="sacEqExport">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><path d="m7 10 5 5 5-5M12 15V3"></path></svg>
              Exportar
            </button>
          </div>
          <div class="sac-toolbar sac-eq-filtros" data-eq-oculto="vivo">
            <div class="sac-eq-presets" data-eq-oculto="vivo resumen" role="group" aria-label="Periodo rápido">
              <button type="button" class="sac-chip on" data-eq-preset="hoy">Hoy</button>
              <button type="button" class="sac-chip" data-eq-preset="ayer">Ayer</button>
              <button type="button" class="sac-chip" data-eq-preset="7">7 días</button>
              <button type="button" class="sac-chip" data-eq-preset="mes">Este mes</button>
            </div>
            <div class="sac-range2" data-eq-oculto="vivo resumen" role="group" aria-label="Rango de fechas">
              <div class="sac-range">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18"></path></svg>
                <label class="visually-hidden" for="sacEqDesde">Desde</label>
                <input type="text" class="form-control" id="sacEqDesde" placeholder="Desde" readonly>
              </div>
              <span class="sac-range2-sep" aria-hidden="true">–</span>
              <div class="sac-range">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18"></path></svg>
                <label class="visually-hidden" for="sacEqHasta">Hasta</label>
                <input type="text" class="form-control" id="sacEqHasta" placeholder="Hasta" readonly>
              </div>
            </div>
            <div class="sac-eq-semana" data-eq-solo="resumen" role="group" aria-label="Rango del resumen">
              <div class="sac-range2">
                <div class="sac-range">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18"></path></svg>
                  <label class="visually-hidden" for="sacEqResDesde">Desde</label>
                  <input type="text" class="form-control" id="sacEqResDesde" placeholder="Desde" readonly>
                </div>
                <span class="sac-range2-sep" aria-hidden="true">–</span>
                <div class="sac-range">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18"></path></svg>
                  <label class="visually-hidden" for="sacEqResHasta">Hasta</label>
                  <input type="text" class="form-control" id="sacEqResHasta" placeholder="Hasta" readonly>
                </div>
              </div>
              <button type="button" class="sac-chip on" id="sacEqResSemana">Esta semana</button>
            </div>
            <label class="visually-hidden" for="sacEqSac">SAC</label>
            <select class="form-select sac-eq-sel" id="sacEqSac">
              <option value="">Todo el equipo</option>
            </select>
          </div>

          {{-- ===== En vivo ===== --}}
          <div class="sac-eq-pane" data-eq-pane="vivo" role="tabpanel">
            <div class="sac-eq-legend" aria-hidden="true">
              <span class="is-on"><i class="sac-est-dot"></i>En línea</span>
              <span class="is-afk"><i class="sac-est-dot"></i>AFK</span>
              <span class="is-lunch"><i class="sac-est-dot"></i>Descanso</span>
              <span class="is-off"><i class="sac-est-dot"></i>Desconectado</span>
            </div>
            <div class="sac-eq-grid" id="sacEqGrid"></div>
          </div>

          {{-- ===== Bitácora de cambios de estado ===== --}}
          <div class="sac-eq-pane" data-eq-pane="bitacora" role="tabpanel" hidden>
            <div class="sac-tblbox">
              <table class="table mb-0 sac-eq-tbl" id="sacEqTblBit" style="width: 100%">
                <thead><tr><th>Fecha</th><th>Hora</th><th>SAC</th><th>Cambio de estado</th><th class="text-end">Duración</th><th>Origen</th></tr></thead>
              </table>
            </div>
          </div>

          {{-- ===== Resumen semanal: entrada y salida por día ===== --}}
          <div class="sac-eq-pane" data-eq-pane="resumen" role="tabpanel" hidden>
            <div class="sac-tblbox sac-eq-sembox">
              <table class="sac-eq-sem" aria-describedby="sacEqSemLeyenda">
                <thead id="sacEqSemHead"></thead>
                <tbody id="sacEqResBody"></tbody>
              </table>
            </div>
            <div class="sac-eq-sem-ley" id="sacEqSemLeyenda">
              <p>Cada día muestra cuánto tiempo estuvo el SAC <strong>En línea</strong>, en <strong>AFK</strong> y en <strong>Descanso</strong> (HH:MM:SS). <strong>Efectivo</strong> = En línea ÷ (En línea + AFK + Descanso).</p>
              <p>El tiempo Desconectado no se cuenta · Hoy se suma hasta este momento (<em>en curso</em>) · Pasa el cursor sobre un día para ver los tiempos exactos.</p>
            </div>
          </div>

          {{-- ===== Pedidos procesados ===== --}}
          <div class="sac-eq-pane" data-eq-pane="pedidos" role="tabpanel" hidden>
            <div class="sac-kpis" id="sacEqPedKpis"></div>
            <div class="sac-tblbox">
              <table class="table mb-0 sac-eq-tbl" id="sacEqTblPed" style="width: 100%">
                <thead><tr><th>Pedido</th><th>Fecha</th><th>Cliente</th><th>Procesado por</th><th>Recibido</th><th>Procesado</th><th class="text-end">Respuesta</th><th class="text-end">Monto</th><th>Estado</th></tr></thead>
              </table>
            </div>
          </div>

          {{-- ===== Aplicaciones: en qué programas se va el tiempo (lo reporta la app de escritorio) ===== --}}
          <div class="sac-eq-pane" data-eq-pane="aplicaciones" role="tabpanel" hidden>
            <div class="sac-kpis" id="sacEqAppKpis"></div>
            <div class="sac-app-cols sac-app-cols-mitad">
              <section class="sac-app-box" aria-labelledby="sacEqAppT0">
                <h2 id="sacEqAppT0">Carga de trabajo</h2>
                <p class="sac-muted">Personas por día, según su tiempo activo</p>
                <div class="sac-carga" id="sacEqCarga"></div>
                <div class="sac-app-ley" id="sacEqCargaLey"></div>
              </section>
              <section class="sac-app-box sac-app-box-wide" aria-labelledby="sacEqAppT1">
                <h2 id="sacEqAppT1">Aplicaciones más usadas</h2>
                <p class="sac-muted" id="sacEqAppSub"></p>
                <div class="sac-apt">
                  <div class="sac-apt-lista" id="sacEqTopLista"></div>
                  <div class="sac-apt-dona" id="sacEqTopDona"></div>
                </div>
              </section>
            </div>
            <div class="sac-app-cols sac-app-cols-2">
              <section class="sac-app-box" aria-labelledby="sacEqAppT2">
                <h2 id="sacEqAppT2">Por persona</h2>
                <p class="sac-muted">Reparto del tiempo medido</p>
                <div class="sac-app-ley" aria-hidden="true">
                  <span><i class="is-act"></i>Activo</span>
                  <span><i class="is-ina"></i>Sin actividad</span>
                </div>
                <div class="sac-app-lista" id="sacEqAppPersonas"></div>
              </section>
              <section class="sac-app-box sac-app-box-wide sac-app-det" aria-labelledby="sacEqAppT3">
                <h2 id="sacEqAppT3">Detalle por persona y aplicación</h2>
                <p class="sac-muted">Tiempo activo en cada aplicación</p>
                <div class="sac-app-scroll">
                  <table class="table mb-0 sac-app-tbl" id="sacEqAppDet"></table>
                </div>
              </section>
            </div>
            <details class="sac-app-clasif" open>
              <summary>Clasificar aplicaciones</summary>
              <p class="sac-muted">Marca cada aplicación como productiva, neutral o no productiva. Se hace una sola vez y vale para todo el equipo.</p>
              <div class="sac-clasif" id="sacEqAppLista"></div>
            </details>
            <p class="sac-muted sac-app-nota">Qué se registra: el nombre del programa en primer plano y el tiempo, solo mientras la persona está En línea o AFK. No se guardan títulos de ventana, páginas visitadas, teclas ni capturas de pantalla. <strong>Sin actividad</strong> = más de 5 minutos sin teclado ni mouse. La app lo indica en su menú.</p>
          </div>
        </section>
      </div>
    </div>
  </div>
</main>
<div class="sac-eq-tip" id="sacEqTip" role="tooltip" hidden></div>
@endsection
